<?php 
/*-----------------------------------------------------------------------------
	This part just registers the new post type, and creates the custom meta
	sections for use.
-----------------------------------------------------------------------------*/

/* Hide from everyone but Admins */
function bei_hide_instructions_admin() { 		
  global $wpdb, $current_user;
  
  get_currentuserinfo();						// set up current user information
  
  if(current_user_can('edit_others_posts'))		// editors and admins
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
		die('<p style="font: 0.8em Tahoma, Helvetica, sans-serif;">This plugin will not work in versions earlier than 3.0. However, it\'s highly recommended that you upgrade to the <a href="' . $install . '/wp-admin/update-core.php" target="_parent">most current and secure version</a>, even though you can use this plugin in version 3.0. </p>');
	} else { // if passes version muster, register the post type 
	  $show = bei_hide_instructions_admin();
	  register_post_type('instructions', array(
										'labels' => array(
														  'name' => 'Instructions',
														  'singular_name' => 'Instruction',
														  'add_new' => 'Add New Instruction',
														  'add_new_item' => 'Add New Instruction',
														  'edit' => 'Edit',
														  'edit_item' => 'Edit Instruction',
														  'new_item' => 'New Instruction',
														  'view' => 'View Instruction',
														  'view_item' => 'View Instruction',
														  'search_items' => 'Search Instructions',
														  'not_found' => 'No instructions found.',
														  'not_found_in_trash' => 'No instructions found in trash.',
														  'parent' => 'Parent Instruction'
														 ),
										'description' => 'Section to add and manage instructions.',
										'show_ui' => $show,
										'publicly_queryable' => true, 
										'public' => true,
										'heirarchical' => false,
										'query_var' => 'bei',
										'supports' => array('title', 'editor', 'excerpt'),
										'rewrite' => false,
										'can_export' => true,
										'_builtin' => false,
										'show_tagcloud' => false,
										'show_in_menu' => $show // for 3.1.x
									  )
					  );
	}
}

// meta information for the instructions posts (custom fields)
$bei_key = "instructions";
$bei_meta_boxes = array(
	"page" => array(
	  "name" => "page_id",  
	  "description" => __('Page Name: ')
	), 
	"video" => array(
	  "name" => "video_url",  
	  "description" => __('Video URL: ')
	),
	"level" => array(
	  "name" => "user_level",  
	  "description" => __('User Level: ')
	)
);

function bei_create_meta_box() {
  if( function_exists( 'add_meta_box' ) ) {
	add_meta_box( 'bei-meta-boxes', __('Instruction Page Information'), 'bei_display_meta_box', 'instructions', 'side', 'low' );
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
	
	$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $name . '" style="display:inline-block; width:65px; text-align:right; font-size:0.9em;">' . $desc . '</label>' . "\n";
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