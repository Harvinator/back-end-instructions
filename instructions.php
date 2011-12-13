<?php
/*
Plugin Name: Back-End Instructions
Plugin URI: http://wordpress.org/extend/plugins/back-end-instructions/
Description: Plugin to provide nice little instructions for back-end WordPress users
Author: Shelly Cole
Version: 1.0
Author URI: http://brassblogs.com
License: GPL2

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

// prevent loading of this page from outside WordPress
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))  
	die('You are not allowed to call this page directly.');

global $wpdb;

if( !function_exists('wp_set_current_user')	) { 
  // checking to see if pluggable is pulled in by any other plugin so conflicts are avoided
  require(ABSPATH . WPINC . '/pluggable.php');
}

// we want to be *absolutely sure* the "instructions" don't show up in any search queries on the front end.
function bei_query_vars($query) {
    if($query->is_search) { // only need it on the search results
      $types = get_post_types(); // get the array of all post types
      foreach($types as $key => $value) {
		if ($value == 'instructions') unset($types[$key]); // if "instructions" post type is found, remove it
	  }
      $query->set('post_type', $types); // set post types listed above (all of them, sans "instrucitons)
    }

    return $query; // return the query and perform the search
}
add_filter('pre_get_posts', 'bei_query_vars'); // Wonder Twin powers, activate!


/*-----------------------------------------------------------------------------
			On initial installation, create a post
-----------------------------------------------------------------------------*/

//run the check, and create the post if it doesn't exist
check_bei_posts();
function check_bei_posts() {
	// check to see if the option is there
	$optioncheck = get_option('_back_end_instructions');
	if(!$optioncheck) {
		// if not, run the function to create the first post and add the option in there. 
		add_action('admin_init', 'bei_create_first_post'); 
	} 
}

function bei_create_first_post() { 
  // email address - anti-spam. I'm paranoid. Sue me.
  $bei_contact = antispambot('brass.blogs@gmail.com');
  // just so it'll be easy to change if I ever need to - won't need to go looking for it.
  $bei_twitter = 'brassblogs';
  // the first post content
  $bei_first_post = array(
	'post_title' => __('How to Use Back End Instructions', 'bei_languages'),
	'post_status' => 'publish',
	'post_type' => 'instructions',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_name' => 'bei_how_to',
	'post_excerpt' => __('Watch a quick video on how to use this plugin. If you have any questions or issues with this plugin, please let me know through <a href="mailto:' . $bei_contact . '">email</a>, or just ask me on <a href="http://twitter.com/' . $bei_twitter . '">Twitter</a>!', 'bei_languages')
  );
  
  // grabs the ID of the newly-created post at the same time it inserts it
  $bei_first_id = wp_insert_post( $bei_first_post ); 
  // adds the post meta to show the instruction on a particular page
  update_post_meta($bei_first_id, 'instructions', array('page_id'=>'edit.php?post_type=instructions', 'video_url'=>'http://www.youtube.com/watch?v=5drBD_UD6rI', 'user_level'=>'Editor'));  
  // add the option so if the admin deletes it, it won't come back.
  add_option('_back_end_instructions', 'true', '', 'no'); 
}


/*-----------------------------------------------------------------------------
	This part just registers the new post type, and creates the custom meta
	sections for use.
-----------------------------------------------------------------------------*/

/* Hide from everyone but Admins */
function bei_hide_instructions_admin() { 		
  global $wpdb, $current_user;
  
  get_currentuserinfo();						// set up current user information
  
  if(current_user_can( 'edit_others_posts' ))		
      /* The above capability is for editors and administrators to seeand interact with the back end instructions area.
         If you'd like to change this level, just replace 'edit_others_posts' with...
         	Super Admins only: 'manage_network'
         	Administrators and above: 'activate_plugins'
         	Editors and above: 'edit_others_posts'
         	Authors and above: 'delete_published_posts'
         	Contributors and above: 'delete_posts'
         	Any logged-in User: 'read'
         PLEASE NOTE that it's HIGHLY recommended that the lowest level you use is the one currently set. */         
      $show = true;
  else
  	  $show = false;
  	  return $show;
}

add_action('init', 'bei_create_instructions_management');
function bei_create_instructions_management() {
	// version check
	if(!function_exists('get_site_url')) $install = get_bloginfo('wpurl');
	else $install = get_site_url();

	if(!function_exists('register_post_type') || get_bloginfo('version') < 3.0) { 
		die('<p style="font: 0.8em Tahoma, Helvetica, sans-serif;">' . __('This plugin will not work in versions earlier than 3.0. However, it\'s highly recommended that you upgrade to the <a href="' . $install . '/wp-admin/update-core.php" target="_parent">most current and secure version</a>, even though you can use this plugin in version 3.0.', 'bei_languages') . '</p>');
	} else { // if passes version muster, register the post type 
	  $show = bei_hide_instructions_admin();
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
										'publicly_queryable' => false, 
										'public' => true,
										'exclude_from_search' => true,
										'heirarchical' => false,
										'query_var' => 'bei',
										'supports' => array('title', 'editor', 'excerpt'),
										'rewrite' => false,
										'can_export' => true,
										'show_tagcloud' => false,
										'show_in_menu' => $show
									  )
					  );
	}
}

// meta information for the instructions posts (custom fields)
$bei_key = "instructions";
$bei_meta_boxes = array(
	"page" => array(
	  "name" => "page_id",  
	  "description" => __('Page Name: ', 'bei_languages')
	), 
	"video" => array(
	  "name" => "video_url",  
	  "description" => __('Video URL: ', 'bei_languages')
	),
	"level" => array(
	  "name" => "user_level",  
	  "description" => __('User Level: ', 'bei_languages')
	)
);

function bei_create_meta_box() {
  if( function_exists( 'add_meta_box' ) ) {
	add_meta_box( 'bei-meta-boxes', __('Instruction Page Information', 'bei_languages'), 'bei_display_meta_box', 'instructions', 'side', 'low' );
  }
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
    
    if(!empty($data[$name])) $value = $data[$name];
    else $value = '';
	
	$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $name . '" style="display:inline-block; width:70px; text-align:right; font-size:0.9em;">' . $desc . '</label>' . "\n";
	$output .= '<input type="text" name="' . $name . '" value="' . $value . '" style="width:170px;" />';     	
	$output .= "</p>\n\n";
  }
  
  echo '<div>' . "\n" . $output . "\n" . '</div></div>' . "\n\n";
}

function bei_save_meta_box( $post_id ) {
  global $post, $data, $bei_meta_boxes, $bei_key;

  $nonce = $bei_key . '_wpnonce';
  if(!$nonce) $nonce = '';
  
if($bei_meta_boxes) {
  foreach( $bei_meta_boxes as $meta_box ) {
  	$name = $meta_box['name'];  	
    $desc = $meta_box['description'];
  	if($_POST) $data[$name] = $_POST[$name];
  }
}

  if($_POST) {
   	if(!wp_verify_nonce($_POST[$nonce], plugin_basename(__FILE__))) 
	  return $post_id;  
  } 
  
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }

  if(!current_user_can('edit_post', $post_id))
	return $post_id;

  update_post_meta( $post_id, $bei_key, $data );
}

add_action( 'admin_menu', 'bei_create_meta_box' );
add_action( 'save_post', 'bei_save_meta_box' );


/*-----------------------------------------------------------------------------
			Stuff to add to the admin header for jQuery show/hide fun
-----------------------------------------------------------------------------*/

// adding the stuff to the page headers
function bei_enqueue_back_end_header() { 
  global $pluginloc; 
  wp_enqueue_script('jquery');
  $count = bei_back_end_help_section('count'); // how many items
  $video = bei_back_end_help_section('video'); // code for video output
  $activate = bei_back_end_help_section('activate'); // set activation flag
  $css_file = WP_CONTENT_DIR . '/themes/' . get_template() . '/bei_style.css'; // if your styles are in a subdirectory, add it here
  $css = get_bloginfo('template_directory') . '/bei_style.css';
  
  // scripts and styles to be added to the header - 'activate' will ensure this stuff is 
  // only added to pages it needs to be added to
  $header  = "\n" . '<!-- back end instructions -->' . "\n";
   
  $header .= '<script type="text/javascript">' . "\n";
  //$header .= 'var $back_end_instructions = jQuery.noConflict();' . "\n";
  $header .= 'jQuery(document).ready(function($) {' . "\n\n";  
  $header .= "\t\t" . '$("#screen-meta-links > div > a").click(function() { ' . "\n";
  $header .= "\t\t\t" . '$("#bei-link-wrap").toggleClass("screen-meta-hide");' . "\n";
  $header .= "\t\t" . '});' . "\n";
  $header .= "\t" . '$("#bei-screen-meta").hide();' . "\n"; /* hides the content */
  $header .= "\t" . '$("#bei-link").click(function() {' . "\n";
  $header .= "\t\t" . '$("#bei-screen-meta").slideToggle(100);' . "\n";
  $header .= "\t\t" . '$("#bei-link-wrap").toggleClass("screen-meta-active");' . "\n";
  $header .= "\t\t" . '$("#screen-meta-links").toggleClass("screen-meta-hide");' . "\n";
  $header .= "\t\t" . 'return false;' . "\n";
  $header .= "\t" . '});' . "\n\n"; 

   for($i=0;$i<$count;$i++) {   
     $header .= "\t" . '$(".bei_help_video' . $i . '").hide();' . "\n";
     $header .= "\t" . '$(".bei_showlink' . $i . '").click(function() {' . "\n";
     $header .= "\t\t" . '$(".bei_help_video' . $i . '").toggle("fast");' . "\n";
     $header .= "\t\t" . 'return false;' . "\n";
     $header .= "\t" . '});' . "\n\n";
   } 

  $header .= '});' . "\n";
  $header .= '</script>' . "\n";   
  $header .= '<link rel="stylesheet" href="' . $pluginloc . 'style.css" />' . "\n";
  if(file_exists($css_file)) $header .= '<link rel="stylesheet" href="' . $css . '" />' . "\n";
  $header .= '<!--/end back end instructions -->' . "\n\n";
  
  if($activate == 'activate') echo $header; // don't insert the header junk if it's not needed
    
}


/*-----------------------------------------------------------------------------
				The functions that make it all work
-----------------------------------------------------------------------------*/
add_action('admin_head', 'bei_enqueue_back_end_header'); // adds the script to the header
add_action('admin_notices', 'bei_back_end_help_section'); // adds the actual content/instruction

// set up reusable defaults to globalize later
$pluginloc = plugins_url() . '/back-end-instructions/';
$address = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
$addy_parts = explode('/', $address);
$endofurl = end($addy_parts);
$class='';

// internationalization
add_action( 'init', 'bei_languages_for_translation' );
function bei_languages_for_translation() {
	load_plugin_textdomain( 'bei_languages', false, $pluginloc . 'bei_languages/' );
}

/*-----------------------------------------------------------------------------
	Let's deal with the output - i.e. show those instructions!
-----------------------------------------------------------------------------*/

function bei_back_end_help_section($type='') { 		// the output to actually show the instructions
  global $pluginloc, $current_user, $wpdb, $post, $pagenow, $endofurl, $class, $address;
  get_currentuserinfo();						// set up current user information

  // start the query to pull the correct post into the instructions area
  $querystr = "SELECT wposts.* FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wposts.post_status = 'publish' AND wposts.post_type = 'instructions' AND wpostmeta.meta_key = 'instructions' ORDER BY wposts.post_date ASC";

    $pageposts = $wpdb->get_results($querystr, OBJECT);    
	
    if($pageposts): global $instruction;
    
      $output  = '<div id="bei-screen-meta" class="metabox-prefs">' . "\n";
      $output .= '<div id="bei-screen-options-wrap">' . "\n";

	  // test for dashboard	 
	  if($pagenow == 'index.php') $dashboard = 'true'; 
	  else $dashboard = 'false';
	  
	  // set up variables
      $count = '0';
      $admin = '';
      $editor = '';
      $author = '';
      $contributor = '';
      $whatpage = '';
      $video = '';
      $level ='';
      foreach($pageposts as $instruction): setup_postdata($instruction);

      // get values for each insctructable and set up reusable variables
        $postid = $instruction->ID;								// instruction post ID
        $url = $instruction->guid;								// instruction post URL
        $title = $instruction->post_title;						// instruction post title
        $content = $instruction->post_content;					// instruction post content
        $excerpt = $instruction->post_excerpt;					// instruction post excerpt
        $meta = get_post_meta($postid, 'instructions'); 		// instruction post meta: top of the array
        if(!empty($meta[0])) {       
          $whatpage = $meta[0]['page_id'];						// instruction post meta value: the page to show on
          $video = $meta[0]['video_url'];							// instruction post meta value: the video URL
          $level = $meta[0]['user_level'];						// instruction post meta value: the user level allowed
        }
        if($dashboard == 'true') $endofurl = 'index.php';		// show dashboard instructions on login
             
        // set up the user levels to compare to the current user level
        if(strtolower($level) == 'administrator') $admin = 'true';
        if(strtolower($level) == 'editor') $editor = 'true';
        if(strtolower($level) == 'author') $author = 'true';
        if(strtolower($level) == 'contributor') $contributor = 'true';
       
          if(strpos($video, 'youtube.com') !== false) {			// check for youtube
            $fixvideo = str_replace('watch?v=', 'v/', $video); 	// fix the youtube video so it'll play
            $isyoutube = 'yes';
            $isvimeo='no';
          } else if(strpos($video, 'vimeo.com') !== false) { // check for vimeo
            $fixvideo = explode('/',$video);
            $vidid = end($fixvideo);
            $isyoutube = 'no';
            $isvimeo = 'yes';
          } else {
          	$isyoutube = 'no';
          	$isvimeo = 'no';
          }
          
          $address = array_reverse(explode('/', $address));
          $address = $address[0];
          if($whatpage == $pagenow || $whatpage == $address || ($whatpage == 'dashboard' && $pagenow == 'index.php')) {  // test that this content is for this page
           
          $class = 'activate';      
            // ensure that role-specific instructions only appear to those roles
            if(current_user_can('activate_plugins') && $admin == 'true' ||
               current_user_can('edit_others_posts') && $editor == 'true' ||
               current_user_can('publish_posts') && $author == 'true' ||
               current_user_can('edit_posts') && $contributor == 'true' ||
               current_user_can('read') && $level == '') {
               	
               if(strpos($whatpage, $endofurl)) continue; // make sure the proper stuff shows up for post types and plugins 	
          	
        	  $output .= '<p><a class="bei_showlink' . $count . '" href="' . $url . '">' . $title . '</a> &nbsp; <em>' . $excerpt . '</em></p>' . "\n";
        	  if(!empty($video)) {
        	  	// if the video URL is there, show it
        	  	$output .= '<div class="instructions bei_help_video' . $count . '" id="video' . $count . '">' . "\n";

        	  	if($isyoutube == 'yes') { 
        	      $output .= '<object type="application/x-shockwave-flash" style="width:640px; height:485px;" data="' . $fixvideo . '"><param name="movie" value="' . $fixvideo . '" /><param name="wmode" value="opaque" /></object>' . "\n";
        	  	} else if($isvimeo == 'yes') { 
        	      $output .= '<iframe src="http://player.vimeo.com/video/' . $vidid . '" width="640" height="366" frameborder="0"></iframe>' . "\n";
        	  	} else if($isvimeo == 'no' && $isyoutube == 'no') { // start HTML5  	
        	      $mp4 = strstr($video, '.mp4');
        	      if($mp4 !== FALSE) {
        	      	$extra = strstr($video, '.iphone.mp4');
        	      	// we also want to pull in the OGG video without having to physically put in the filename
        	      	 if($extra !== FALSE) $ogg = str_replace('.iphone.mp4', '.ogv', $video);
        	      	 else $ogg = str_replace('.mp4', '.ogv', $video);
        	      }
        	      
        	      $output .= '<video class="html5-video" controls>' . "\n";
				  $output .= '<source src="' . $video . '"  type="video/mp4" />' . "\n";
				  if($ogg) $output .= '<source src="' . $ogg . '"  type="video/ogg" />' . "\n";
				  $output .= '<object type="application/x-shockwave-flash" data="' . $pluginloc . 'player.swf">' . "\n";
				  $output .= '<param name="movie" value="' . $pluginloc . 'player.swf" />' . "\n";
				  $output .= '<param name="flashvars" value="autostart=false&amp;controlbar=over&amp;file=' . $video . '" />' . "\n";
				  $output .= '</object>' . "\n";
				  $output .= '</video>' . "\n";
				  $output .= '<p class="small">If you have an issue viewing this video, please contact (site owner\'s email addy).</p>' . "\n";
        	  	}
        	  	
        	    $output .= apply_filters('the_content', $post->post_content);        	    
      			$output .= '</div>' . "\n";
        	  } else { // show the content of the post for text instructions
        	    $output .= '<div class="bei_help_video' . $count . '">' . "\n";
        	  	$output .= apply_filters('the_content',$content);
        	  	$output .= '</div>' . "\n";
        	  }
           
          $count++; 
          } 
            
        }

    endforeach;
    
    $output .= '<!-- /end bei-screen-options-wrap -->' . "\n";
    $output .= '</div>' . "\n";
    $output .= '<!-- /end bei-screen-meta -->' . "\n";
    $output .= '</div>' . "\n";
    
    // our button to click so we can expand the div
    $output .= '<div id="bei-screen-meta-links">' . "\n";
    $output .= '<div id="bei-link-wrap" class="hide-if-no-js">' . "\n";
    $output .= '<a href="#bei-screen-meta" id="bei-link" class="show-settings">' . __('View instructions for this page', 'bei_languages') . '</a>' . "\n";
    $output .= '<!-- /bei-link-wrap -->' . "\n" . '</div>' . "\n";
    $output .= '<!-- /bei-screen-meta-links -->' . "\n" . '</div>' . "\n";
    endif;
  
    if($class == 'activate') {    	
    	if($type == '') echo $output;
    	else if($type == 'count') return $count;    
        else if($type == 'activate') return $class;    
    }   
} 