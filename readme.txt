=== Back-End Instructions ===
Contributors: doodlebee
Donate link: http://brassblogs.com/donate
Tags: developers, clients, instructions
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.6

Plugin for developers of WordPress to provide easy back-end instructions to their clients - using text, video, audio - whatever.

== Description ==

WordPress developers: ever written awesome custom functions and script work for client's site? Then you provide said client with instructions - either via printed manual, video tutorials, or even emails - but no matter how many ways you tell them how to use the site you just made for them, they keep asking you for instructions?

I've found this is typically because most clients want instructions that appear "in their face".  They like immediate answers to their questions - they don't want to have to find that file, or search their email for the answers.  It's much faster for them to just call you and ask what to do.

This plugin solves the issue.  Now there can be no more excuses for not finding the answers you've already supplied for them *ad nauseum*.

If you'd like to see examples of this plugin, please feel free to visit the [plugin page on my site](http://brassblogs.com/blog/back-end-instructions-official-release) for information beyond what's described in this readme file.

= Features =
Using WordPress' default posting capabilities, mixed in with some custom post types and meta fields (and a little sprinkle of magic fairy dust and a lot of prayers), you can easily create and manage instructions for each page of the back-end of your client's site. Simply create a post, associate the post with a page in the back-end, add in your content, and *voil&agrave;*: a small button appears at the top of the page that expands with jQuery to show a list of "instructables" (post title links) associated with that page.  

Features include:

1. The ability to provide a small excerpt to describe what each "instructable" contains within.
2. Click the link title, and the "instructable" expands to display the content.
    - Content can be a written list of instructions, or some other form of media.
    - Content can be anything you like, and you can use it just like you would any other post - the only difference is, this stuff shows up in the back-end.
3. Only Administrators can gain access to edit/create the "instructables" - lower levels will only see them. 
4. You can set what end user level can view which instructions. For example, you don't want an Author to see the same instructions for "Edit Posts" that an Administrator would see - it would confuse them. So you can also create content and serve up specific information based on the user level, if you so desire. 

Note that upper levels will also see lower-level videos, so keep that in mind while preparing your instructions. I find it useful to make videos specific to the lowest level first, and then build onto that base as you go up the user-level chain of command.


== Installation ==

Installation is simple, and adds nothing to your database (save the posts and content you create). 

1. Unzip the package.
2. The contents of the package should look like so:
	- BackEnd-Instructions (folder)
	  - instructions.php (file)
	  - bei_add_header.php (file)
	  - bei_post_type.php (file)
	  - bei_functions.php (file)
	  - readme.txt (file)
	  - css (folder)
	  - style.css (file - located within the CSS folder)
	  - embed_loader (folder)
	  - expressinstall.swf (file - located within the embed_loader folder)
	  - jingloader.swf (file - located within the embed_loader folder)
	  - swfobject.js (file - located within the embed_loader folder)	
3. Upload the BackEndInstructions folder (and all of its contents) to the "plugins" directory of your WordPress installation.
4. Activate the plugin through the "Plugins" menu in WordPress.
6. All done!

= How to Use =

All of your work will be done in the new Custom Post Type that will be set up for you, so look in the left sidebar for the "Instructions" panel (found just below "Comments", provided no other plugin also has additions here!). As with writing posts, you'll have the option of editing a previously-created instruction (which you shouldn't have any), or adding a new one. 

So let's start with "Add New."

1. **Give it a meaningful, but short, title.**  You want to give the title something with meaning, but keep it short and sweet.  3 or 4 words at the most should do it.

2. **Add in your content.** Place your actual instructions here.  You can write text-based instructions, or add in audio or video files via normal posting methods. YouTube, Vimeo, and self-hosted videos can be added in the sidebar by just entering the full URL to the video in question (for Vimeo and YouTube, just copy the URL in the address bar. For self-hosted, copy the File URL for the media file in your Media Library; or if it's uploaded to some directory on your server, put in the correct path to the file.)

3. **Add in an excerpt, if desired.**  The excerpt would be a good spot to give a small description of what's going on in this post.  The plugin is set to show an excerpt, but not content - so if you leave this empty, the end user will just rely on the title.

4. **Instruction Summary**  If you enter in anything here, it'll be displayed after the instruction shows up.  HTML is allowed, but you'll have to either write it in the content area and use the formatting buttons, copy and paste into this area, or just hand-code the hTML yourself. I would recommend using this ONLY if you are solely putting up video or audio content.  It's really not necessary for text-based content (in fact, it won't even show up with tex-based content).  I'd recommend writing a small list of what is covered in the video/audio files so it can be quickly scanned. Personally, I put in the time (min:sec) and a short 3-4 word description of what is covered in the video, so if someone wants to scan to a particular point in the video, they know exactly where it is. But you can use it - or not - for whatever you like.

5. **Instruction Page Information** In the right sidebar, at the bottom, will be this section.

	**Page Name** enter in what page you want this to appear on. You can find the page name at the end of the URL in your address bar, after the last trailing slash, but before any ? in the URL. So, for example, if you want the instruction to show up on the Dashboard, put in "index.php".  If you'd like it on the Users page, enter in "users.php".
   
   This will also work for various plugins and post types.  In the case of making the instructions show up on a particular page related to a custom post type or a plugin, simply copy everything that shows up after the last "/" and paste it into the field.   
   
   *(There is, of course, no guarantee that this will work on ALL plugins out there. But generally, it should.)*
   
	**Video URL** Here is where the video magic happens. If you'd like to post a YouTube or Vimeo video as your instructable, then grab the URL of the YouTube or Vimeo page for that video and pop it in here.  The plugin is set to rewrite the URL so that the video will play correctly. (You may also just pop YouTube or Vimeo embed code in the regular text area if you like - but that *can* be buggy for various reasons.  If you choose that option, your best bet is to ensure  the "Visual" tab is not selected when you pop the code in, and don't switch back until after you've saved the post.  Using the sidebar option is just a cleaner method of pulling it off, with very little hassle. But you are not limited to only using this field if you don't want to.)
   
   If you're using a self-hosted SWF file, then you need to enter in the full path to the video you want to use. So if you've uploaded an SWF file to your Media Library, and want to use that, then select the File URL and copy it, then paste it into the field. If there are .swf files out there on the internet, and you have the URL to grab the file directly, it should work to place that in the field as well.  
   
   If you're not using .swf files, YouTube or Vimeo, then simply use the embed code provided from the location you want (most video places have embed code they let you use) and pop that into the main content area (not this sidebar field) and it'll work just fine.  Again, just make sure you're using the HTML editor, not the Visual editor, when you do this. Also note that if you have content in the main "write post" area, and you pop a URL into the "Video URL" field, the "Video URL" field is set to display in place of any written content.  Meaning, if you want your main content area stuff to show, then leave the "Video URL" field blank.
   
   **User Level** If you'd like particular instructions to show up certain user levels, you can choose an option here.   For example, Admins usually have more menu options to choose from than Authors.  You can add "extra info" for admins so they understand the items related to their role, but lower levels won't see those instructions. If you leave the option blank, any logged in user at any level will see the instructable.
   
 
== Frequently Asked Questions ==  

= No questions at this time. =
But if you have any, by all means, feel free to ask away.  I'd also love input on features you'd like added or things you'd like to see to improve this plugin.  See the "Credit" information on how to contact me.

= Notes = 

1. I would HIGHLY recommend obtaining a copy of Adminimize or Role Scoper to hide the "Instructions" custom post type from the administration panel in the left sidebar.  Although it's set to only allow Administrator-level access actually edit and create posts for this plugin, lower levels may still be able to view it.  I'd definitely recommend obtaining one of these to hide the menu from sight altogether.  But that's your choice - I'll not force it on you!

2. This plugin uses custom post types to create the content for it.  If you decide you no longer wish to use this plugin, you need to decide what you'd like to do with the content.  If you want it completely removed from the database, then before you uninstall it, you need to go to "Instructions" and check the box to "Move to Trash", and then "Apply." Then you need to go the the trash and empty it to be sure the posts are completely removed from your database - THEN uninstall the plugin. (A note: the instal "How to" post will still be in the database.)  If you want to keep the content as regular posts, then you will either manually have to edit your MySQL backup to change them from "instructions" to "post" (just open the database backup in a plain-text editor do a find/replace, save, and re-import), or use a plugin like [Post Type Switcher](http://wordpress.org/extend/plugins/post-type-switcher/) to handle that for you before you uninstall. Otherwise, it'll all just stay there and hang out, with no way to see it until you re-install the plugin again, or code your theme to pull the content out of the database to display it on the front end.

3. If you're in the mood to translate, you can "turn on" the capability within the bei_functions.php file, at the top.  I've tried to make it as easy as possible for this to happen, but if I've missed something, please let me know.

= Known Issues = 

1. There are a few other plugins out there that cause jQuery conflicts. In one case, a Gallery plugin (called "Shiba Media Gallery") would conflict with this plugin and hide all of the created galleries. The plugin has since been upgraded, and the conflict is no longer there - so if you're running that gallery plugin, I'd recommend upgrading it, because it now works fine side-by-side with this one.  If you run across one, please let me know which plugin it is so I can figure out how to fix this.

2. If using Jing (Pro or otherwise) for your self-hosted video content, you will still need to use the SWF format for your videos.  MP4's will not play with the provided video player.


== Screenshots ==

*Showing an example of the custom post type interface for the backend instructions. You can see the tab in the upper left-hand side to show/hide the instruction for this page.*
[Screenshot1](http://brassblogs.com/plugins/bei/bei1.png "Back End Instructions")

*When you click the table, it expands to show the list of instructions associated with this page.*
[Screenshot2](http://brassblogs.com/plugins/bei/bei3.png "Back End Instructions")

*You can expand the instruction further to show the video content.*
[Screenshot3](http://brassblogs.com/plugins/bei/bei2.png "Back End Instructions")

== Changelog ==

= 0.6 = 
* fixed issue where instructions were not showing up on individual posts (post.php)
* repaired path to css and script files

= 0.5 = 
* added video and inserted a default post to show an example of use
* double-checks and cleaning/fixing errors (mostly spelling and clarification) - finalizing for official release

= 0.4 = 
* fixed issue with other plugins that call pluggable.php and cause a conflict
* added capability for targeting custom post types
* added capability to use vimeo

= 0.3 = 
* tested for WordPress 3.1
* cleaned up/streamlined code

= 0.2 = 
* Fixed issue where instructions for the dashboard wouldn't show up upon initial login.

= 0.1 = 
* First release.


== Upgrade Notice ==

none at this time.


== Credits, Thank-Yous, Support Information, etc. ==

If you have any questions, comments or suggestions for improvement, feel free to contact Shelly at [Brass Blogs Web Design](http://brassblogs.com "Web Design in Hartford, Farmington Valley, Granby, Avon, Simsbury, Weatogue CT").

Given that this is free, I offer limited support. Pretty much if you have issues with the plugin *working* I will do whatever I can to help you fix the issue, but when it comes to customizations, I'm in limited supply.  I'll do what I can, but no guarantees.  Pretty much your standard "AS IS" application.  In all honesty, ask customization questions in the forums - if I can't help, perhaps someone else can.  (If you want to hire me to customize it, that's another story - feel free to contact me to do so!)