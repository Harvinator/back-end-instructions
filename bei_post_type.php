<?php 
/*-----------------------------------------------------------------------------
	This part just registers the new post type, and creates the custom meta
	sections for use.
-----------------------------------------------------------------------------*/

//version check
if(!function_exists('get_site_url')) $install = get_bloginfo('wpurl');
else $install = get_site_url();

if(!function_exists('register_post_type') || get_bloginfo('version') < 3.0) { 
	die('<p style="font: 0.8em Tahoma, Helvetica, sans-serif;">This plugin will not work in versions earlier than 3.0. However, it\'s highly recommended that you upgrade to the <a href="' . $install . '/wp-admin/update-core.php" target="_parent">most current and secure version</a>, even though you can use this plugin in version 3.0. </p>');
} else { // if passes version muster, register the post type
  register_post_type('instructions', array(
	'label' => __('Instructions'),
	'singular_label' => __('Admin Instructions'),
	'public' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'_builtin' => false,
	'hierarchical' => false,
	'rewrite' => false,
	'query_var' => 'bei',
	'supports' => array('title', 'editor', 'excerpt')
  ));
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
  global $bei_key;

  if( function_exists( 'add_meta_box' ) ) {
	add_meta_box( 'bei-meta-boxes', __('Instruction Page Information'), 'bei_display_meta_box', 'instructions', 'side', 'low' );
  }
}

function bei_display_meta_box() {
global $post, $bei_meta_boxes, $bei_key;


echo '<div class="form-wrap">' . "\n";


wp_nonce_field( plugin_basename( __FILE__ ), $bei_key . '_wpnonce', false, true );

$output = '';

  foreach($bei_meta_boxes as $meta_box) { 
    $data = get_post_meta($post->ID, $bei_key, true);     
	
	$output .= '<p style="font-size:1.1em; font-style:normal; "><label for="' . $meta_box['name'] . '" style="display:inline-block; width:65px; text-align:right; font-size:0.9em;">' . $meta_box['description'] . '</label>' . "\n";
	$output .= '<input type="text" name="' . $meta_box['name'] . '" value="' . $data[$meta_box['name']] . '" style="width:170px;" />';     	
	$output .= "</p>\n\n";
  }
  
  echo '<div>' . "\n" . $output . "\n" . '</div></div>' . "\n\n";
}

function bei_save_meta_box( $post_id ) {
  global $post, $bei_meta_boxes, $bei_key;

if($bei_meta_boxes) {
  foreach( $bei_meta_boxes as $meta_box ) {
	$data[ $meta_box[ 'name' ] ] = $_POST[ $meta_box[ 'name' ] ];
  }
}

  if ( !wp_verify_nonce( $_POST[ $bei_key . '_wpnonce' ], plugin_basename(__FILE__) ) )
	return $post_id;

  if ( !current_user_can( 'edit_post', $post_id ))
	return $post_id;

  update_post_meta( $post_id, $bei_key, $data );
}

add_action( 'admin_menu', 'bei_create_meta_box' );
add_action( 'save_post', 'bei_save_meta_box' );


// secondary set of boxes for additional meta fields
$bei_secondary_key = "extras";
$bei_secondary_meta_boxes = array(
	"summary" => array(
	  "name" => "summary",  
	  "description" => __('Enter a short description (suggested: a bullet list) of points this instructable will cover.')
	)
);

function bei_create_secondary_meta_box() {
  global $bei_secondary_key;

  if( function_exists( 'add_meta_box' ) ) {
	add_meta_box( 'bei-secondary-meta-boxes', __('Instruction Summary'), 'bei_display_secondary_meta_box', 'instructions', 'normal', 'low' );
  }
}

function bei_display_secondary_meta_box() {
global $post, $bei_secondary_meta_boxes, $bei_secondary_key;


echo '<div class="form-wrap">' . "\n";


wp_nonce_field( plugin_basename( __FILE__ ), $bei_secondary_key . '_wpnonce', false, true );

$output = '';

  foreach($bei_secondary_meta_boxes as $meta_box) { 
    $data = get_post_meta($post->ID, $bei_secondary_key, true);     
	
	$output .= '<p style="font-size:1.1em; font-style:normal; ">' . $meta_box['description'] . "\n";
	$output .= '<textarea name="' . $meta_box['name'] . '" style="width:100%;" rows="7">' . $data[$meta_box['name']] . '</textarea>';     	
	$output .= "</p>\n\n";
  }
  
  echo '<div>' . "\n" . $output . "\n" . '</div></div>' . "\n\n";
}

function bei_save_secondary_meta_box( $post_id ) {
  global $post, $bei_secondary_meta_boxes, $bei_secondary_key;

if($bei_secondary_meta_boxes) {
  foreach( $bei_secondary_meta_boxes as $meta_box ) {
	$data[ $meta_box[ 'name' ] ] = $_POST[ $meta_box[ 'name' ] ];
  }
}

  if ( !wp_verify_nonce( $_POST[ $bei_secondary_key . '_wpnonce' ], plugin_basename(__FILE__) ) )
	return $post_id;

  if ( !current_user_can( 'edit_post', $post_id ))
	return $post_id;

  update_post_meta( $post_id, $bei_secondary_key, $data );
}

add_action( 'admin_menu', 'bei_create_secondary_meta_box' );
add_action( 'save_post', 'bei_save_secondary_meta_box' );  
