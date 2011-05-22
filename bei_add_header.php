<?php
// adding the stuff to the page headers
function bei_enqueue_back_end_header() { 
  global $pluginloc; // this, and the next few lines are defined in bei_functions.php
  wp_enqueue_script('jquery');
  $count = bei_back_end_help_section('count'); // how many items
  $video = bei_back_end_help_section('video'); // code for video output
  $activate = bei_back_end_help_section('activate'); // set activation flag
  $css_file = WP_CONTENT_DIR . '/themes/' . get_template() . '/bei_style.css'; // if your styles are in a subdirectory, add it here
  $css = get_bloginfo('template_directory') . '/bei_style.css';
  
  // scripts and styles to be added to the header - 'activate' will ensure this stuff is 
  // only added to pages it needs to be added to
  $header  = "\n" . '<!-- back end instructions -->' . "\n";
  
  $header .= '<script type="text/javascript" src="' . $pluginloc . 'embed_loader/swfobject.js"></script>' . "\n";
  for($i=0;$i<$count;$i++) { 
     $header .= '<script type="text/javascript">swfobject.registerObject("media' . $i . '", "9.0.115", "expressInstall.swf")</script>' . "\n";
   } 
   
  $header .= '<script type="text/javascript">' . "\n";
  $header .= 'var $back_end_instructions = jQuery.noConflict();' . "\n";
  $header .= '$back_end_instructions(document).ready(function() {' . "\n\n";
  $header .= "\t" . '$back_end_instructions("#bei_help_text").hide();' . "\n";
  $header .= "\t" . '$back_end_instructions("#bei_help_link").click(function() {' . "\n";
  $header .= "\t\t" . '$back_end_instructions("#bei_help_text").toggle("fast");' . "\n";
  $header .= "\t\t" . 'return false;' . "\n";
  $header .= "\t" . '});' . "\n\n"; 

   for($i=0;$i<$count;$i++) {   
     $header .= "\t" . '$back_end_instructions(".bei_help_video' . $i . '").hide();' . "\n";
     $header .= "\t" . '$back_end_instructions(".bei_showlink' . $i . '").click(function() {' . "\n";
     $header .= "\t\t" . '$back_end_instructions(".bei_help_video' . $i . '").toggle("fast");' . "\n";
     $header .= "\t\t" . 'return false;' . "\n";
     $header .= "\t" . '});' . "\n\n";
   } 

  $header .= '});' . "\n";
  $header .= '</script>' . "\n";   
  $header .= '<link rel="stylesheet" href="' . $pluginloc . 'css/style.css" />' . "\n";
  if(file_exists($css_file)) $header .= '<link rel="stylesheet" href="' . $css . '" />' . "\n";
  $header .= '<!--/end back end instructions -->' . "\n\n";
  
  if($activate == 'activate') echo $header; // don't insert the header junk if it's not needed
    
}