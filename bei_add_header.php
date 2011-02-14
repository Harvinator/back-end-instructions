<?php
// adding the stuff to the page headers
function enqueue_back_end_header() { 
  global $pluginloc; // this, and the next few lines are defined in bei_functions.php
  wp_enqueue_script('jquery');
  $count = back_end_help_section('count'); // how many items
  $video = back_end_help_section('video'); // code for video output
  $activate = back_end_help_section('activate'); // set activation flag
  
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
  $header .= '<!--/end back end instructions -->' . "\n\n";
  
  if($activate == 'activate') echo $header; // don't insert the header junk if it's not needed
    
}