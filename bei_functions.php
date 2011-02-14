<?php
add_action('admin_head', 'enqueue_back_end_header');
add_action('admin_notices', 'back_end_help_section');

// set up reusable defaults to globalize later
$pluginloc = plugins_url() . '/BackEndInstructions/';
$address = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
$addy_parts = explode('/', $address);
$endofurl = end($addy_parts);


// internationalization - just a starting point - feel free to uncomment and use
// load_plugin_textdomain('bei_languages', $pluginloc.'languages/');

/*-----------------------------------------------------------------------------
	Let's deal with the output - i.e. show those instructions!
-----------------------------------------------------------------------------*/

function back_end_help_section($type='') { 		// the output to actually show the instructions
  global $pluginloc, $current_user, $wpdb, $post, $pagenow, $endofurl;
  get_currentuserinfo();						// set up current user information

  // start the query to pull the correct post into the instructions area
  $querystr = "SELECT wposts.* FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wposts.post_status = 'publish' AND wposts.post_type = 'instructions' AND wpostmeta.meta_key = 'instructions' ORDER BY wposts.post_date ASC";

    $pageposts = $wpdb->get_results($querystr, OBJECT);    
	
    if($pageposts): global $instruction;
    
      $output  = '<div id="bei_help_text">' . "\n";

	  // test for dashboard	 
	  if($pagenow == 'index.php') $dashboard = 'true'; 

      $count = '0';
      foreach($pageposts as $instruction): setup_postdata($instruction);

      // get values for each insctructable and set up reusable variables
        $postid = $instruction->ID;								// instruction post ID
        $url = $instruction->guid;								// instruction post URL
        $title = $instruction->post_title;						// instruction post title
        $content = $instruction->post_content;					// instruction post content
        $excerpt = $instruction->post_excerpt;					// instruction post excerpt
        $meta = get_post_meta($postid, 'instructions'); 		// instruction post meta: top of the array       
        $summarymeta = get_post_meta($postid, 'extras');		
        $summary = $summarymeta[0]['summary'];					// instruction post meta: secondary excerpt
        $whatpage = $meta[0]['page_id'];						// instruction post meta value: the page to show on
        $video = $meta[0]['video_url'];							// instruction post meta value: the video URL
        $level = $meta[0]['user_level'];						// instruction post meta value: the user level allowed
        if($dashboard == 'true') $endofurl = 'index.php';		// show dashboard instructions on login
             
        // set up the user levels to comapre to the current user level
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
          
          if(($whatpage == $pagenow) || (strpos($whatpage, $pagenow) !== false)) {  // test that this content is for this page
                    
            // ensure that role-specific instructions only appear to those roles
            if(current_user_can('activate_plugins') && $admin == 'true' ||
               current_user_can('edit_others_posts') && $editor == 'true' ||
               current_user_can('publish_posts') && $author == 'true' ||
               current_user_can('edit_posts') && $contributor == 'true' ||
               current_user_can('read') && $level == '') {
               	
               if($endofurl != $whatpage) continue; // make sure the proper stuff shows up for post types and plugins 	
          	
        	  $output .= '<p><a class="bei_showlink' . $count . '" href="' . $url . '">' . $title . '</a> &nbsp; <em>' . $excerpt . '</em></p>' . "\n";
        	  if(!empty($video)) {
        	  	// if the video URL is there, show it
        	  	$output .= '<div class="instructions bei_help_video' . $count . '" id="video' . $count . '">' . "\n";

        	  	if($isyoutube == 'yes') { 
        	      $output .= '<object type="application/x-shockwave-flash" style="width:640px; height:485px;" data="' . $fixvideo . '"><param name="movie" value="' . $fixvideo . '" /></object>' . "\n";
        	  	} else if($isvimeo == 'yes') { 
        	      $output .= '<iframe src="http://player.vimeo.com/video/' . $vidid . '" width="640" height="366" frameborder="0"></iframe>' . "\n";
        	  	} else if($isvimeo == 'no' && $isyoutube == 'no') {   	    
        	      $output .= '<object id="media' . $count . '" width="640" height="485">' . "\n";
        	      $output .= '<param name="movie" value="' . $pluginloc . 'embed_loader/jingloader.swf"></param>' . "\n";
        	      $output .= '<param name="allowfullscreen" value="true"></param>' . "\n";
        	      $output .= '<param name="flashvars" value="content=' . $video . '&amp;containerwidth=640&amp;containerheight=485&amp;&amp;autostart=false&amp;showbranding=false"></param>';
        	      $output .= '<embed src="' . $pluginloc . 'embed_loader/jingloader.swf" type="application/x-shockwave-flash" allowfullscreen="true" width="640" height="485" flashvars="content=' . $video . '&amp;containerwidth=640&amp;containerheight=360"></embed>' . "\n";
        	      $output .= '</object>' . "\n";
        	  	}
        	  	
        	    $output .= apply_filters('the_content', $summary);        	    
      			$output .= '</div>' . "\n";
        	  } else { // show the content of the post for text instructions
        	    $output .= '<div class="bei_help_video' . $count . '">' . "\n";
        	  	$output .= apply_filters('the_content',$content);
        	  	$output .= '</div>' . "\n";
        	  }
           
          $count++; 
          } 
             
          $class = 'activate';
        }

    endforeach;
    
    $output .= '</div>' . "\n";
    // our button to click so we can expand the div
    $output .= '<div id="bei_expand_link">' . "\n";
    $output .= '<a href="" class="show-settings" id="bei_help_link">' . __('View instructions for this page.') . '</a>' . "\n";
    $output .= '</div>' . "\n";
    endif;
  
    if($class == 'activate') {    	
    	if($type == '') echo $output;
    	else if($type == 'count') return $count;    
        else if($type == 'activate') return $class;    
    }   
}    