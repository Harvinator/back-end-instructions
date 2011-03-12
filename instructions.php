<?php
/*
Plugin Name: Back-End Instructions
Plugin URI: http://wordpress.org/extend/plugins/back-end-instructions/
Description: Plugin to provide nice little instructions for back-end WordPress users
Author: Shelly Cole
Version: 0.6
Author URI: http://brassblogs.com
License: GPL2

    Copyright 2010  Michelle Cole  (email : shelly@brassblogs.com)

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

/* 
   pluggable.php is loaded AFTER all plugins have been loaded, therefore using certain
   functions fail.  For this plugin get_currentuserdata is needed, but because of
   this issue with pluggable.php, we have to force it to load sooner so we can use 
   that function.  
   
   A secondary issue is that some other plugins also do the same thing: force pluggable.php 
   to load early.  This can cause conflicts of interest.
   
   So what we're doing here is checking the options table to see what plugins are installed
   and activated, and if there are any that are known to force pluggable.php to load early
   we want to avoid any potential conflicts, so we will NOT force it to load early, and 
   instead, will "ride" on the other plugin that does.
   
   The variable "$findme" contains the array of plugins that are known to conflict with this
   one because of this issue.  If you find one that is not listed here, feel free to add
   it to the array.  You can find what to add to the array by logging into phpMyAdminIf you do that, I ask you kindly to inform me of the plugin so I can 
   add it to any future updates.
*/ 

/* the following commented-out section is old - I'm just leaving it here for "just in case" purposes.
$check = get_option('active_plugins');	// get list of all active plugins
$findme = array('1' => 'role-scoper/role-scoper.php');

foreach($findme as $active_plugin) {	
	// if one of the above-named plugins is installed and active, set a flag
	if(in_array($active_plugin, $check)) $found_conflict = 'true';
}

// if the conflicting plugin is found, do not load pluggable.php early.
if($found_conflict != 'true') { */

if( !function_exists('wp_set_current_user')	) {
  require(ABSPATH . WPINC . '/pluggable.php');
}

require('bei_post_type.php');
require('bei_functions.php');
require('bei_add_header.php');

/*-----------------------------------------------------------------------------
	Automagically insert that first post
-----------------------------------------------------------------------------*/

//run the check, and create the post if it doesn't exist
check_bei_posts();

function check_bei_posts($return='') { // checks to see if the post already exists
	global $wpdb;
	$bei_query = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'instructions' AND $wpdb->posts.post_name = 'bei_how_to'";
	$bei_results = $wpdb->get_results($bei_query, OBJECT);
	
	if(empty($bei_results)) : 
		add_action('admin_init', 'bei_create_first_post'); 
		add_action('admin_init', 'bei_add_post_meta');
	else : 
		//do nothing
	endif;
	
	if($return == 'return') return $bei_results;
}

function bei_create_first_post() { // creates the first post
  $bei_contact = antispambot('brass.blogs@gmail.com');
  $bei_first_post = array(
	'post_title' => __('How to Use Back End Instructions'),
	'post_status' => 'publish',
	'post_type' => 'instructions',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_name' => 'bei_how_to',
	'post_excerpt' => __('Watch a quick video on how to use this plugin. If you have any issues, please <a href="mailto:' . $bei_contact . '">let me know!</a>')
  );
  
  $bei_first_id = wp_insert_post( $bei_first_post );
  return $bei_first_id;
}

function bei_add_post_meta() {
	global $wpdb;
	$pluginloc = plugins_url();
  // post should be created - now let's update the postmeta
  $bei_values = check_bei_posts('return');
  $bei_first_id = $bei_values[0]->ID;
  $bei_array = array('page_id' => 'edit.php?post_type=instructions', 'video_url' => 'http://www.youtube.com/watch?v=5drBD_UD6rI', 'user_level' => 'Administrator');
  add_post_meta($bei_first_id, 'instructions', $bei_array);
}