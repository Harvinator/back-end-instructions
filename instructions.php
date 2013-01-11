<?php
/*
Plugin Name: Back-End Instructions
Plugin URI: http://wordpress.org/extend/plugins/back-end-instructions/
Description: Plugin to provide nice little instructions for back-end WordPress users
Author: Shelly Cole
Version: 2.4
Author URI: http://brassblogs.com
License: GPLv2

    Copyright 2010  Michelle Cole  (email : brass.blogs@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/


/*-----------------------------------------------------------------------------
				Startup stuff - let's prepare!
-----------------------------------------------------------------------------*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 	// prevent loading of this page from outside WordPress 
	die('You are not allowed to call this page directly.');

global $current_user, $post;											// globalize
$pluginloc = dirname( plugin_basename( __FILE__ ) );
$address = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]; // current page's path
$addy_parts = explode('/', $address);									// get url parts
$endofurl = end($addy_parts);											// get the last part of the current url
$class='';																// activate (so you can see it?) or not?

if( !function_exists('wp_set_current_user')	) { 						// check to see if pluggable is pulled in elsewhere to avoided conflicts
  require(ABSPATH . WPINC . '/pluggable.php');
}

add_action('admin_init', 'bei_add_instructions_options');				// add the options array if it's not there
function bei_add_instructions_options() {
	$options = get_option('bei_options');

	if(!$options) {
		$array = array('admin' => 'activate_plugins',					// array for all the options
					   'public' => 'no',
					   'registered' => 'yes',
					   'view' => 'delete_posts');
  		add_option('bei_options', $array, 'yes');						// add the new option array	
	}
}

function bei_query_vars($query) {										// be *absolutely sure* these aren't in search results
    if($query->is_search) { 
      $types = get_post_types(); 										// get the array of all post types
      foreach($types as $key => $value) {
		if ($value == 'instructions') unset($types[$key]); 				// if "instructions" post type is found, remove it
	  }
      $query->set('post_type', $types); 								// set post types listed above (all of them, sans "instructions")
    }

    return $query; 														// return the query and perform the search
}
add_filter('pre_get_posts', 'bei_query_vars'); 							// Wonder Twin powers, activate!


/*-----------------------------------------------------------------------------
					Translate!
-----------------------------------------------------------------------------*/

add_action( 'plugins_loaded', 'bei_languages_for_translation' );
function bei_languages_for_translation() {
	load_plugin_textdomain( 'bei_languages', false, $pluginloc . '/bei_languages' );
}

/*-----------------------------------------------------------------------------
	This part just registers the new post type, and creates the custom meta
	sections for use.
-----------------------------------------------------------------------------*/
  
add_action('init', 'bei_create_instructions_management');
function bei_create_instructions_management() {
	global $current_user;

	$options = get_option('bei_options');

	$level = $options['admin'];
	$front = $options['public'];
	
	// version check
	if(!function_exists('get_site_url')) $install = get_bloginfo('wpurl');
	else $install = get_site_url();
	
	$warning = sprintf(__('This plugin will not work in versions earlier than 3.1. However, it\'s highly recommended that you upgrade to the <a href="%1$s/wp-admin/update-core.php" target="_parent">most current and secure version</a>, even though you can use this plugin in version 3.1.', 'bei_languages'), $install);

	if(!function_exists('register_post_type') || get_bloginfo('version') < 3.1) { 
		die('<p style="font: 0.8em Tahoma, Helvetica, sans-serif;">' . $warning. '</p>');
	} else { // if passes version muster, register the post type 
	  if(current_user_can($level)) { // show or hide menu?
	  	$show = true;
	  } else {
	  	$show = false;
	  }

	  if($front == 'yes') {
	  	$front = true;
	  	$rewrite = array( 'slug' => 'instructions', 'with_front' => true );
	  } else {
	  	$front = false;
	  	$rewrite = false;
	  }
	 
	  register_post_type('instructions', array(
										'labels' => array(
														  'name' => __('Instructions', 'bei_languages'),
														  'singular_name' => __('Instruction', 'bei_languages'),
														  'add_new' => __('Add New Instruction', 'bei_languages'),
														  'add_new_item' => __('Add New Instruction', 'bei_languages'),
														  'edit' => __('Edit', 'bei_languages'),
														  'edit_item' => __('Edit Instruction', 'bei_languages'),
														  'new_item' => __('New Instruction', 'bei_languages'),
														  'view' => __('View Instruction', 'bei_languages'),
														  'view_item' => __('View Instruction', 'bei_languages'),
														  'search_items' => __('Search Instructions', 'bei_languages'),
														  'not_found' => __('No instructions found.', 'bei_languages'),
														  'not_found_in_trash' => __('No instructions found in trash.', 'bei_languages'),
														  'parent' => __('Parent Instruction', 'bei_languages')
														 ),
										'description' => __('Section to add and manage instructions.', 'bei_languages'),
										'show_ui' => $show,
										'menu_position' => 5,
										'publicly_queryable' => $front, 
										'public' => $front,
										'exclude_from_search' => true,
										'heirarchical' => false,
										'query_var' => 'instructions',
										'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
										'rewrite' => $rewrite,
										'has_archive' => $front,
										'can_export' => true,
										'show_tagcloud' => false,
										'show_in_menu' => $show,
										'register_meta_box_cb' => 'bei_create_meta_box'
									  )
					  );

	}
}


// meta information for the instructions posts (custom fields)
$bei_key = "instructions";
$bei_meta_boxes = array(
	"page" => array(
	  "name" => "page_id",  
	  "description" => __('Page Name: ', 'bei_languages'),
	  "type" => "",
	  "choices" => ""
	), 
	"multi" => array(
	  "name" => "multi",  
	  "description" => __('+ ', 'bei_languages'),
	  "type" => "dynamic",
	  "choices" => ""
	),
	"video" => array(
	  "name" => "video_url",  
	  "description" => __('Video URL: ', 'bei_languages'),
	  "type" => "",
	  "choices" => ""
	),
	"level" => array(
	  "name" => "user_level",  
	  "description" => __('User Level: ', 'bei_languages'),
	  "type" => "dropdown",
	  "choices" => array('manage_network' => __('Super Administrator', 'bei_languages'),
 						 'activate_plugins' => __('Administrator', 'bei_languages'),
 						 'edit_others_posts' => __('Editor', 'bei_languages'),
 						 'delete_published_posts' => __('Author', 'bei_languages'),
 						 'delete_posts' => __('Contributor', 'bei_languages'),
 						 'read' => __('Subscriber', 'bei_languages')
				   )
	)
);

function bei_create_meta_box() {
  global $bei_key;
  add_meta_box( 'bei-meta-boxes', __('Instruction Page Information', 'bei_languages'), 'bei_display_meta_box', 'instructions', 'side', 'low' );
}

function bei_display_meta_box() {
global $post, $bei_meta_boxes, $bei_key;
$post_id = $post->ID;

echo '<div class="form-wrap">' . "\n";

wp_nonce_field( plugin_basename( __FILE__ ), $bei_key . '_wpnonce', false, true );

$output = '';

  foreach($bei_meta_boxes as $meta_box) { 
    $data = get_post_meta($post->ID, $bei_key, true);
    $name = $meta_box['name'];
    $desc = $meta_box['description'];
    $type = $meta_box['type'];
    $choices = $meta_box['choices'];
    
    if(!empty($data[$name])) $value = $data[$name];
    else $value = '';
	
	if($type == 'dropdown') {
		// set up dropdown names to make 'em pretty on return
			if($data[$name] == 'manage_networks') $display = __('Super Administrator', 'bei_languages'); 
			if($data[$name] == 'activate_plugins' || $data[$name] == 'administrator' || $data[$name] == 'Administrator') $display = __('Administrator', 'bei_languages'); 
			if($data[$name] == 'edit_others_posts' || $data[$name] == 'editor'|| $data[$name] == 'Editor') $display = __('Editor', 'bei_languages'); 
			if($data[$name] == 'delete_published_posts' || $data[$name] == 'author' || $data[$name] == 'Author') $display = __('Author', 'bei_languages'); 
			if($data[$name] == 'delete_posts' || $data[$name] == 'contributor' || $data[$name] == 'Contributor') $display = __('Contributor', 'bei_languages'); 
			if($data[$name] == 'read' || $data[$name] == 'subscriber' || $data[$name] == 'Subscriber') $display = __('Subscriber', 'bei_languages'); 
					
	  	$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $name . '" style="display:inline-block; width:70px; margin-right:3px; text-align:right; font-size:0.9em; cursor:text">' . $desc . '</label><select name="' . $name . '">' . "\n";
		if (isset($data[$name])) {
			$output .= '<option value="' . $data[$name] . '" selected>'. $display .'</option>' . "\n";
		} else {
			$output .= '<option value="read" selected>Subscriber</option>' . "\n";
		}
		$output .= '<option value="">-------------------</option>' . "\n";
		foreach($choices as $dropdown_key => $dropdown_value) {
			
			$output .= '<option value="' . $dropdown_key . '">' . $dropdown_value . '</option>' . "\n";
		}
		$output .= '</select>' . "\n";
		
	} elseif($type == 'textbox') {
		$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $name . '" style="vertical-align:top; display:block; width:70px; text-align:right; font-size:0.9em; cursor:text;">' . $desc . '</label>' . "\n";
		$output .= '<textarea rows="5" cols="10" name="' . $name . '" style="width:250px;" />' . $value . '</textarea>';     	
		$output .= "</p>\n\n";
	
	} elseif($type == 'dynamic') {
		$output .= '<div class="more_fields">' . "\n";
    	if($value) {
    		foreach($value as $value) {
    			$output .= '<p><strong style="display:inline-block; width:26px; text-align:right; margin-right:7px;"><a href="#" id="' . $name . '" class="add_field" style="text-decoration:none; color:#666; font-style:normal;">' . $desc . '</a></strong></label>' . "\n";
    			$output .= '<input type="text" name="' . $name . '[]" value="' . $value . '" style="width:170px;" /></p>'; 
    		}
    	} else {
    		$output .= '<p><strong style="display:inline-block; width:26px; text-align:right; margin-right:7px;"><a href="#" id="' . $name . '" class="add_field" style="text-decoration:none; color:#666; font-style:normal;">' . $desc . '</a></strong></label>' . "\n";
    		$output .= '<input type="text" name="' . $name . '[]" value="' . $value . '" style="width:170px;" /></p>'; 
    	}
    	$output .= '</div>' ."\n\n";
    	
    } else {
		$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $name . '" style="display:inline-block; width:70px; text-align:right; font-size:0.9em; cursor:text">' . $desc . '</label>' . "\n";
	$output .= '<input type="text" name="' . $name . '" value="' . $value . '" style="width:170px;" />';     	
	$output .= "</p>\n\n";
	}
  }
  
  echo '<div>' . "\n" . $output . "\n" . '</div></div>' . "\n\n";
}


function bei_save_meta_box( $post_id ) {
  global $post, $bei_meta_boxes, $bei_key;

  foreach( $bei_meta_boxes as $meta_box ) {
	$data[ $meta_box[ 'name' ] ] = $_POST[ $meta_box[ 'name' ] ];
  }

  if ( !wp_verify_nonce( $_POST[ $bei_key . '_wpnonce' ], plugin_basename(__FILE__) ) )
	return $post_id;

  if ( !current_user_can( 'edit_post', $post_id ))
	return $post_id;

  update_post_meta( $post_id, $bei_key, $data );
}

add_action( 'save_post', 'bei_save_meta_box' );



/*-----------------------------------------------------------------------------
					Check for old versions, add new stuff
-----------------------------------------------------------------------------*/

check_bei_posts();
function check_bei_posts() {													// function to check that plugin has never 
																				// been installed before
	$options = get_option('bei_options');

	$old = get_option('_back_end_instructions');								// old versions
				   
	if($old) {																	// if the plugin is already installed, and it's an older version
		delete_option('_back_end_instructions');								// remove the old option
	} 
	
	if(!$options) {																// if the new option is not set...
		add_action('admin_init', 'bei_create_first_post'); 						// create the default instructions
	}
}


/*-----------------------------------------------------------------------------
				Set up an options page for defaults
-----------------------------------------------------------------------------*/

add_action('admin_menu', 'instructions_admin_add_options');									// start 'er up!
function instructions_admin_add_options() {
	add_options_page('Back End Instructions', 'Back End Instructions', 'manage_options', 'bei', 'bei_options_page');
}


function bei_options_page() { 																// the actual page contents ?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div><h2>Back End Instructions</h2>
	<p>There aren't too many default settings for the Back End Instructions, but it makes life easier to have them here.</p>
	<form action="options.php" method="post">
		<?php settings_fields('bei_options'); ?>
		<?php do_settings_sections('bei'); ?>
		<p><input name="submit" type="submit" id="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'bei_languages'); ?>" /></p>
	</form>
</div>
<?php } 


add_action('admin_init', 'instructions_admin_init');
function instructions_admin_init(){															// the options settings
	register_setting( 'bei_options', 'bei_options', 'bei_options_validate' );
	add_settings_section('bei_main', '', 'bei_section_text', 'bei'); 
	add_settings_field('bei_admin', 'Default Admin Level', 'bei_setting_string', 'bei', 'bei_main'); 
	add_settings_field('bei_public', 'Show in front?', 'bei_setting_string_public', 'bei', 'bei_main'); 
	add_settings_field('bei_registered', 'Logged-in users only?', 'bei_setting_string_private', 'bei', 'bei_main'); 
	add_settings_field('bei_view', 'Default viewing level', 'bei_setting_string_view', 'bei', 'bei_main');
}

function bei_section_text() {																//nuthin' really.  Might use later.
}

function bei_setting_string() {
	
	$options = get_option('bei_options');
	
	echo __('<span class="description" style="display:block;">Choose the lowest level logged-in user to create/edit/delete Instructions.</span>', 'bei_languages');
	
	if(is_multisite()) {																	// test that this is a multi-site install	
	  echo '<input id="bei_admin" name="bei_options[admin]" size="40" type="radio" value="manage_network" ' . (isset($options["admin"]) && $options["admin"] == "manage_network" ? 'checked="checked" ' : '') . '/> Super Administrator (for multi-site only)<br />';
	}
	
	echo '<input id="bei_admin" name="bei_options[admin]" size="40" type="radio" value="activate_plugins" ' . (isset($options["admin"]) && $options["admin"] == "activate_plugins" ? 'checked="checked" ' : '') . '/> Administrator';
	echo '<br /><input id="bei_admin" name="bei_options[admin]" size="40" type="radio" value="edit_others_posts" ' . (isset($options["admin"]) && $options["admin"] == "edit_others_posts" ? 'checked="checked" ' : '') . '/> Editor';
	echo '<br /><input id="bei_admin" name="bei_options[admin]" size="40" type="radio" value="delete_published_posts" ' . (isset($options["admin"]) && $options["admin"] == "delete_published_posts" ? 'checked="checked" ' : '') . '/> Author';
}

function bei_setting_string_public() {
	
	$options = get_option('bei_options');

	$permalink = get_option("home") . '/wp-admin/options-permalink.php';
	
	echo sprintf(__('<span class="description" style="display:block;">Check "yes" if you\'d like to make your instructions viewable on the front end of the site. <br /><strong>PLEASE NOTE</strong>: The first time you change this option, you WILL have to <a href="%1$s">re-save your permalink settings</a> for this to take effect.  You may not ever have to do it again, but if you find you have issues after swapping back and forth, then try resetting them again to see if it helps.</span>', 'bei_languages'), $permalink) . "\n\n";
	
	if(!isset($options['public'])) $options['public'] = 'no';
	echo '<input id="bei_public" name="bei_options[public]" size="40" type="radio" value="yes" ' . (isset($options["public"]) && $options["public"] == "yes" ? 'checked="checked" ' : '') . '/> Yes' . "\n";
	echo ' &nbsp; &nbsp; <input id="bei_public" name="bei_options[public]" size="40" type="radio" value="no" ' . (isset($options["public"]) && $options["public"] == "no" ? 'checked="checked" ' : '') . '/> No' . "\n\n";
}

function bei_setting_string_private() {
	
	$options = get_option('bei_options');
	
	echo __('<span class="description" style="display:block;">Check "yes" if you\'d like to make front-end instructions visible only to logged-in users.<br /><strong>PLEASE NOTE</strong>: if you check "yes" ANYONE can see ALL of these instructions.  See the next option to help with that a bit.</span>', 'bei_languages') . "\n\n";
	
	echo '<input id="bei_registered" name="bei_options[registered]" size="40" type="radio" value="yes" ' . (isset($options["registered"]) && $options["registered"] == "yes" ? 'checked="checked" ' : '') . '/> Yes' . "\n";
	echo ' &nbsp; &nbsp; <input id="bei_registered" name="bei_options[registered]" size="40" type="radio" value="no" ' . (isset($options["registered"]) && $options["registered"] == "no" ? 'checked="checked" ' : '') . '/> No' . "\n\n";
}	

function bei_setting_string_view() {
	
	$options = get_option('bei_options');
	
	echo __('<span class="description" style="display:block;">You only need to choose an option from this dropdown if you set "Show in front?" to "yes" AND "Logged-in users only?" to "no".  If this option were not here, then ANY visitor to the site could see ALL instructions just by visiting the page.  If the user is logged in, they would see only instructions that were available to their level, but if they aren\'t, they would see them for ALL levels.  This option will allow you to treat a non-logged-in user as if they have a user level.  The default is "Contributor."</span>', 'bei_languages') . "\n\n";
	
	// setup array
	$choices = array();
	
	if(is_multisite()) {																	// test that this is a multi-site install	
	  $choices['Super Administrator'] = 'manage_networks';
	}
	
	$choices['Administrator'] = 'activate_plugins';
	$choices['Editor'] = 'edit_others_posts';
	$choices['Author'] = 'delete_published_posts';
	$choices['Contributor'] = 'delete_posts';
	$choices['Subscriber'] = 'read';
				
	echo '<p><select id="bei_view" name="bei_options[view]"></p>' . "\n";

	if (isset($options["view"])) {
		foreach($choices as $key => $value) {
			if($options["view"] == $value) 
			  echo '<option value="' . $value . '" selected>'. $key .'</option>' . "\n";
		}
	} else {
			  echo '<option value="delete_posts" selected>Contributor</option>' . "\n";
	}
		
	echo '<option value="">-------------------</option>' . "\n";
		
	foreach($choices as $key => $value) {
		echo '<option value="' . $value . '">' . $key .'</option>' . "\n";
	}	
	
	echo '</select>' . "\n";	
}

function bei_options_validate($input) {
	isset($input['admin']) ? $newinput['admin'] = trim($input['admin']) : $newinput['admin'] = '';
	isset($input['public']) ? $newinput['public'] = trim($input['public']) : $newinput['public'] = '';
	isset($input['registered']) ? $newinput['registered'] = trim($input['registered']) : $newinput['registered'] = '';
	isset($input['view']) ? $newinput['view'] = trim($input['view']) : $newinput['view'] = '';
	return $newinput;
}


/*-----------------------------------------------------------------------------
			On initial installation, create a post
-----------------------------------------------------------------------------*/

function bei_create_first_post() { 															// create the initial instructions
  $bei_contact = antispambot('brass.blogs@gmail.com');										// email address - anti-spam. I'm paranoid. Sue me.
  $bei_twitter = 'brassblogs';																// just so it'll be easy to change if I ever need to
  $bei_content = sprintf(__('Watch a quick video on how to use this plugin. If you have any questions or issues with this plugin, please let me know through <a href="mailto:%1$s">email</a>, or just ask me on <a href="http://twitter.com/%2$s">Twitter</a>!', 'bei_languages'), $bei_contact, $bei_twitter);
    
  $bei_first_post = array(																	// the first post content
	'post_title' => __('How to Use Back End Instructions', 'bei_languages'),
	'post_status' => 'publish',
	'post_type' => 'instructions',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_name' => 'bei-how-to',
	'post_excerpt' => $bei_content,
	);
  
  $bei_first_id = wp_insert_post( $bei_first_post, true ); 									// grabs the ID of the newly-created post at 
  																							// the same time it inserts it
  update_post_meta($bei_first_id, 'instructions', array('page_id'=>'edit.php?post_type=instructions', 'video_url'=>'http://www.youtube.com/watch?v=5drBD_UD6rI', 'user_level'=>'activate_plugins'));  										// adds the post meta to show the instruction 
  																							// on a particular page	
}


/*-----------------------------------------------------------------------------
	 Now that we're all done with that, let's make 'em show up!
-----------------------------------------------------------------------------*/
function array_find($needle, $haystack) {													// testing function
	if(strstr($needle, '?') !== false) { 													// check to see if the current page we're on has a "?"
		$test = explode('?', $needle);
		$test = $test[0]; 				 													// if it does, get the part before the "?"
	} else {
		$test = $needle;				 													// if it doesn't, just use the current page
	}

	foreach ($haystack as $key=>$item) {
		if(($item == $test) || ($item == $needle)) return 'found'; 
	}

	return false; 
}

add_action('load-'.$pagenow, 'add_bei_instructions_button');
function add_bei_instructions_button() {
	global $current_user, $user_level, $post, $pagenow, $endofurl, $class, $address, $pluginloc, $options;
	
	$screen = get_current_screen();
	$this_screen = $screen->base; 
	$help_tab_content = array(); 															// set up the help tab content														

	$address = array_reverse(explode('/', $address));										// reverse the current URL
    $address = $address[0];																	// grab the last URL bit (the page we're on)
	
	$ids = array();																			// set up array of ID's that fit
	$instruction_query = new WP_Query(array('post_type' => 'instructions', 'post_status' => 'publish' ) );		// query
	
	if($instruction_query->have_posts()) : while($instruction_query->have_posts()) : $instruction_query->the_post();
		$post_id = get_the_ID();
		$instruction_info = get_post_meta($post_id, 'instructions');
		$page = $instruction_info[0]['page_id'];											// page this is supposed to be on
		
		$ids[] = $post_id;

	endwhile;
	endif;

	// now we have a list of ID's for instructions that this user is allowed to see.  Let's further narrow the field. 
	if($ids) {																				// if we actually have instructions for this user...
		foreach($ids as $post) :
			$instruction_info = get_post_meta($post, 'instructions');
			$page = $instruction_info[0]['page_id'];										// page for this instruction to be displayed on
			$multi = $instruction_info[0]['multi'];											// secondary pages, if any (this will be an array)
			$level = $instruction_info[0]['user_level'];									// level that can see this instruction
			$video = $instruction_info[0]['video_url'];										// video url
			$vid_id = 'player-' . $post;													// video IDs

			if($level == 'administrator' || $level == 'Administrator') $level = 'activate_plugins';	// replace the old values
			if($level == 'editor' || $level == 'Editor') $level = 'edit_others_posts';				// so they show up when they're
			if($level == 'author' || $level == 'Author') $level = 'delete_published_posts';			// supposed to
			if($level == 'contributor' || $level == 'Contributor') $level = 'delete_posts';
			if($level == 'subscriber' || $level == 'Subscriber') $level = 'read';
			
    		if($address == 'index.php' || $address == '') $address = 'dashboard';			// do a little fixin' for the dashboard
    		
    		$multi[] = $page;																// add pages to the array to search against

			$find = array_find($address, $multi);

			if($find != 'found') continue;													// if the current page isn't in the array, skip it	

			if(current_user_can($level)) :
				$post_info = get_post($post);												// get the post
				$id = 'bei-tab-' . $post;
				$title = $post_info->post_title;
				$content = apply_filters('the_content', $post_info->post_content);
				$content = preg_replace_callback( "/(\{\{)(.*)(\}\})/", create_function('$matches', 'return "[" . $matches[2] . "]";'), $content );
				$excerpt = '<p>'. $post_info->post_excerpt . '</p>';			

				$output = '';
				if(!empty($video)) {
					if(strpos($video, 'youtube.com') !== false) {							// check for youtube
	            		$fixvideo = str_replace('watch?v=', 'embed/', $video); 				// fix the youtube video so it'll play
	            		$output .= '<iframe id="' . $vid_id . '" name="' . $vid_id . '" style="display:block; margin: 15px auto;" width="480" height="360" src="' . $fixvideo . '?rel=0" 	frameborder="0" allowfullscreen></iframe><br />' . "\n";
         		
	          		} elseif(strpos($video, 'vimeo.com') !== false) { 						// check for vimeo
	            		$fixvideo = explode('/',$video);									// get video URL parts
	            		$vidid = end($fixvideo);											// get the video ID so it'll play
	            		$output .= '<iframe style="display:block; margin: 15px auto;" width="480" height="360" src="http://player.vimeo.com/video/' . $vidid . '" width="640" height="366" frameborder="0"></iframe>' . "\n";
	            	
	          		} elseif(strpos($video, '.swf') !== false) {							// check for .swf
	          			$output .= '<object data="' . $vdeo . '" width="480" height="360" style="display:block; margin:15px auto;">' . "\n";
    					$output .= '<embed src="' . $video . '" width="480" height="360">' . "\n";
    					$output .= '</embed>' . "\n";
  						$output .= '</object>' . "\n\n";
	          		} else {																// start HTML5
	          			$ogg = strstr($video, '.iphone.mp4');    							// check to be sure it's not an iphone.mp4 file
	        			if($ogg !== FALSE) $ogg = str_replace('.iphone.mp4', '.ogv', $video);
	        			else $ogg = str_replace('.mp4', '.ogv', $video);					     			
	        			
	        			
        			
        				$path = plugin_dir_url();												// get plugin path
	        			$output .= '<video class="html5-video" style="display:inline-block; margin: 15px auto;" width="480" height="360" controls>' . "\n";
						$output .= '<source src="' . $video . '"  type="video/mp4" />' . "\n";
						
						if($ogg) $output .= '<source src="' . $ogg . '"  type="video/ogg" />' . "\n";
						
						$output .= '<object type="application/x-shockwave-flash" data="' . $path . $pluginloc . '/player.swf">' . "\n";
						$output .= '<param name="movie" value="' . $path . $pluginloc . '/player.swf" />' . "\n";
						$output .= '<param name="flashvars" value="autostart=false&amp;controlbar=over&amp;file=' . $video . '" />' . "\n";
						$output .= '</object>' . "\n";
						$output .= '</video>' . "\n";
						$output .= '<p class="small">' . sprintf(__('If you have an issue viewing this video, please contact <a href="mailto:%1$s">%1$s</a>.', 'bei_languages'), antispambot(get_option("admin_email"))) . '</p>' . "\n";
	          		}
				}
			
			
				// finally! the instructions!		
				$screen->add_help_tab( array('id' => $id,            		 					//unique id for the tab
	   									 	 'title' => $title,      		 					//unique visible title for the tab
	   									 	 'content' => $output . $content . $excerpt,		//actual help text
								 	 ) );
			endif; // end current user
		endforeach;
	}
}


/*-----------------------------------------------------------------------------
				Functions for later use in theme files
-----------------------------------------------------------------------------*/

function bei_caps() {
	// makes a fake list of capabilities, since people who aren't logged in won't have any
	$options = get_option('bei_options');
	$view = $options['view'];
	$caps = array();
	if($view == 'manage_networks') $caps[] = array('manage_networks', 'activate_plugins', 'edit_others_posts', 'delete_published_posts', 'delete_posts', 'read');
	if($view == 'activate_plugins') $caps[] = array('activate_plugins', 'edit_others_posts', 'delete_published_posts', 'delete_posts', 'read');
	if($view == 'edit_others_posts') $caps[] = array('edit_others_posts', 'delete_published_posts', 'delete_posts', 'read');
	if($view == 'delete_published_posts') $caps[] = array('delete_published_posts', 'delete_posts', 'read');
	if($view == 'delete_posts') $caps[] = array('delete_posts', 'read');
	if($view == 'read') $caps[] = array('read');
	
	return $caps[0];
}

function bei_test_front_end_info($type = '') {
	// test different parts so we can return flags for visibility's sake on the front end
	global $post, $options; 
	$public = $options['public'];											// show in front?
	$reg = $options['registered']; 											// allow only registered users to see on front end? 
	$login = get_option('home') . '/wp-login.php';							// login url 
	
	if($public == 'yes') {								// check to see if these should be visible on the front end
	
		if($reg == 'yes') {								// check to see if registration is required.
		
			if(!is_user_logged_in()) {					// if required, check to see that the user is logged in.
			
				$output  = '<div class="entry-content">';
				$output .= sprintf(__('I\'m sorry, but you must first be <a href="%1$s">logged in</a> to view this page.', 'bei_languages'), $login);
				$output .= '</div>';
				echo $output;								// if not, give them a message to log in.
				$showposts = false;							// don't show posts
			} else {
		  		$showposts = true;							// show 'em if logged in
		  	} 
		  				  
		  		if($showposts == false) $showposts = false;
		  		else $showposts = true;
		  				  
		} else {
			$showposts = true;
		}
			          
			if($showposts == false) $showposts = false;
		  	else $showposts = true;
	}
	
	if($type == '') return $showposts;
	elseif($type == 'message') return $output;
}

function bei_instructions_query_filter() {									// the query to get the post IDs of qualifying instructions
	global $wpdb, $options, $current_user;
	$view = $options['view']; 											    	// default user level for non-logged-in users 
	if(!is_user_logged_in()) $caps = bei_caps();								// get end user's capabilities for non-logged-in users
	else $caps = $current_user->allcaps;										// get the capabilities for the logged-in user
	$where = '';																// initialize
	
	$where = "SELECT p.* FROM $wpdb->posts AS p WHERE p.ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'instructions' AND meta_value LIKE '%user_level%' AND (";	

		$inner = array();														// set up the inner array
		foreach($caps as $key => $value) {
			$inner[] = $wpdb->prepare( "meta_value LIKE %s", '%' . $key . '%' );// make an array of checks from the $caps
		}
		
		$where .= implode(" OR ", $inner) . ") AND post_status = 'publish') ORDER BY post_date";	// end of query
		$results = $wpdb->get_results($where);									// get the results		
		
		$ids = array();															// set up the array for our IDs
		if($results) {
			foreach($results as $result) $ids[] = $result->ID;					// place all the post ID's in an array for later use
		}
		
		return $ids;															// return just the IDs if we want
}

function bei_next_prev_links($type='', $previous='', $next='') {
	global $post;
	$ids = bei_instructions_query_filter();										// run the above query
	$this_id = $post->ID;														// get the current instruction post ID
	$i = array_search($this_id, $ids);											// find it in the resulting query array

	if($previous == '') $previous = '&larr; Previous Instruction';				// default text for previous link
	if($next == '') $next = 'Next Instruction &rarr;';							// default text for next link
	
	if($type == 'previous') {
		$p = $i - 1;															// subtract 1 from the array key to get previous post ID
		$p = $ids[$p];															// grab the previous post ID
		if(in_array($p, $ids) !== FALSE) $link = '<a href="' . get_permalink($p) . '">' . $previous . '</a>';
	} elseif($type == 'next') {
		$n = $i + 1;															// add 1 to array keys to get next post ID
		$n = $ids[$n];															// grab the next post ID
		if(in_array($n, $ids) !== FALSE) $link = '<a href="' . get_permalink($n) . '"> ' . $next . '</a>';
	} 

	echo $link;
	
}
