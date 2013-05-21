=== Back End Instructions ===
Contributors: doodlebee
Donate link: http://brassblogs.com/donate
Tags: developers, clients, instructions, client sites, docs, documentation, help
Requires at least: 3.1
Tested up to: 3.6-beta3
Stable tag: 3.1
License: GPLv2 or later

Plugin for WordPress developers to provide easy "how to use" instructions to their clients.


== Description ==

WordPress developers: ever written awesome custom functions and script work for client's site? Then you provide said client with instructions - either via printed manual, video tutorials, or even emails - but no matter how many ways you tell them how to use the site you just made for them, they keep asking you for instructions?

I've found this is typically because most clients want instructions that appear "in your face".  They like immediate answers to their questions - they don't want to have to find that file, or search their email for the answers.  It's much faster for them to just call you and ask what to do.

This plugin solves the issue.  Now there can be no more excuses for not finding the answers you've already supplied for them *ad nauseum*.


= Features =

1. Version 3.0 now has the option of using a custom tab, or the ability to be integrated in the WordPress "Help" tab.
2. Due to popular demand, I've added in a new feature where you can add a single instruction to multiple pages. 
3. Choose the tab (named by your post title), and the "instructable" displays the content.
    - Content can be a written list of instructions, or some other form of media.
    - Content can be anything you like, and you can use it just like you would any other post - the only difference is, this stuff shows up in the back-end.
4. The options page will allow you to set what user level can add/edit/delete "instructables", as well as allow you to choose whether or not to make the instructions viewable from the front end. You can also choose to make front-end "instructables" viewable to the public, or only to logged-in users.  You can also give non-logged-in users "capabilities" to restrict what they see.
5. You can set what end user level can view which instructions. For example, you don't want an Author to see the same instructions for "Edit Posts" that an Administrator would see - it would confuse them. So you can also create content and serve up specific information based on the user level, if you so desire. 

Note that upper levels will also see lower-level videos, so keep that in mind while preparing your instructions. I recommend making videos/content specific to the lowest level first, and then build onto that base as you go up the user-level chain of command. 
6. Multi-Site capable.


== Installation ==

Installation is simple, and adds nothing more to your database than an options and the content you create. 

1. Unzip the package.
2. The contents of the package should look like so:
	- back-end-instructions (folder)
	  - bei_languages (folder)
	  - &nbsp; &nbsp; back-end-instructions.pot (file)
	  - css (folder)
	  - &nbsp; &nbsp; bei_colors-classic.css (file)
	  - &nbsp; &nbsp; bei_colors-fresh.css (file)	  
	  - instructions.php (file)
	  - mytheme (folder)
	  - &nbsp; &nbsp; archive-instructions.php (file)	
	  - &nbsp; &nbsp; single-instructions.php (file) 
	  - player.swf (file)
	  - readme.txt (file)	
3. Upload the back-end-instructions folder (and all of its contents) to the "plugins" directory of your WordPress installation.
4. Activate the plugin through the "Plugins" menu in WordPress.
6. All done! 


= How to Use =

All of your work will be done in the new Custom Post Type that will be set up for you, so look in the left sidebar for the "Instructions" panel (found just below "Posts"). As with writing posts, you'll have the option of editing a previously-created instruction (which you shouldn't have any, save the initial example created), or adding a new one. 

So let's start with "Add New."

1. **Give it a meaningful, but short, title.**  You want to give the title something with meaning, but keep it short and sweet.  3 or 4 words at the most should do it. Keep in mind that this title will be the "link" for the tab that contains the actual instruction content, so don't go crazy.

2. **Add in your content.** Place your actual instructions here.  You can write text-based instructions, or add in images, audio, or video files via normal posting methods.  If you'd like easier methods of adding video, you can simply place the full URL path to the video into the "Video" in the sidebar meta box - it will auto-format video for you, and display it above your post content. (This only works for MP4 videos,  Vimeo, and YouTube.) Note, this new addition is by request: many people didn't like that the "Video" sidebar metabox *replaced* the content.  So now it's added to, and displayed above, the content.

3. **Add in an excerpt, if desired.**  The excerpt would be a good spot to give a small description of what's going on in this post. I find it very useful to place timestamps (and a small description of what you find at each timestamp) in the excerpt section.  Whatever you place in the excerpt area will show up below the content.

4. **Regarding Shortcodes ** Several people have asked me, in the past, to make it possible to write shortcodes without having them parsed, so people can give examples of how to write and use the shortcodes.  The good thing about moving the shortcodes into the help menu is that the help menu is meant for static text only, and shortcodes are completely stripped.  I've provided a method for putting shortcode content in the instructional post without having it stripped, and without parsing the information.  Simply replace the "[" and "]" with "{{" and "}}".  Your shortcodes will display (not be parsed) so you can teach people how to use them.  

	*NOTE* On the front end of the site, the shortcodes WILL be parsed. For example: {{gallery}} will actually work exactly like [gallery] when viewing on the front end, and display your gallery of images; but {{gallery}} will display [gallery] on the back end.

5. **Instruction Page Information** In the right sidebar, at the bottom, will be this section.

	**Page Name.** Enter in what page you want this to appear on. You can find the page name at the end of the URL in your address bar. For example, let's say you want to write an instruction on how to write a new post. The URL for adding a new post is typically something like *"http://yoursitename/wp-admin/post-new.php?post_type=photos"*.  If you want this instruction to appear ONLY on the "Add New" page in the custom post type ("photos" in this case), then you just copy everything in the URL after the last "/" (which, in this example, would be *"post-new.php?post_type=photos"*) and put it in the "Page Name" field.  However, if you'd like this same instruction to appear on the "Add Post" page on *any* post type (even under the "Posts > Add New" section for default WordPress posts), the copy everything after the last "/" but before the "?" (in this example: *"post-new.php"*) and paste it into this field.  The only exception to this is if you want stuff to how up on the Dashboard.  In that case, all you need to write is "dashboard".  There is also an option to set additional pages, if you like.  Just click the "+" button to add multiple fields - each one is where you'll enter the page name - same method as above.
   
   	*There is, of course, no guarantee that this will work on ALL non-default pages out there - i.e. anything added by custom functions or other plugins, but generally, it should.*
   
	**Videos** Here is where the video magic happens. If you'd like to post an MP4, YouTube, or Vimeo video as your instructable, then grab the URL of the YouTube or Vimeo page (or the full path to the MP4 file) for that video and pop it in here. This video will display above the content you write.  Further information below:

   	**Vimeo or YouTube**  If you're on a page at youtube.com or vimeo.com, and you want to use the video on that page within an instruction, just copy the URL of the page and paste it in the "Video URL" field.  Alternatively, you can simply copy the video embed code that these sites provide for you, and paste it within the post content area. (Just be sure you're using the HTML tab when you do that - the Visual editor messes with things a bit.) This, of course, works for any system that provides embed code to paste video on your site - you're not restricted to Vimeo or YouTube.
   
   	**MP4/OGV files** The only note here is, if you do use MP4 files, I HIGHLY recommend creating an .ogv version of the same file, because some browsers cannot play MP4 files ::coughFirefoxcough:: so the .ogv file is kinda necessary.  So if you use MP4, I'd say you also MUST create an OGV version and upload that to your server alongside the MP4.  Don't worry though, it's easy.  I'll even give you a link to a [free video converter](http://www.mirovideoconverter.com/) so you can do that.  Be sure the OGV and MP4 files have the exact same name, down to the case.
   
   	**SWF Files** If you're using a self-hosted SWF file, or you have the URL to an SWF file, then you need to enter in the full path to the SWF file into the "Video URL" field.  
   
   	**User Level** If you'd like particular instructions to show up certain user levels, you can choose an option here.   For example, Admins usually have more menu options to choose from than Authors.  You can add "extra info" for admins so they understand the items related to their role, but lower levels won't see those instructions. If you leave the option blank, any logged in user at any level will see the instructable.
   	
6. **Settings** In the right sidebar, under "Settings > Back End Instructions", you'll find the settings page for the instructions defaults.

	**Use a custom help tab?** This was somethign that was originally put into the plugin, and then removed later (when it was really easy - and made sense - to pop this stuff in the help tab).  But it's been requested that the option be put back in, because apparently some of you all like to get rid of the help tabs :)  Check "Yes" for this option, and the custom Instructions tab will hold the instructions, instead of the WordPress Help Tab.

	**Default Admin Level** This is the lowest user level you'd like to have access to create and edit Instructions.  The lowest level you can choose is "Author" ("Contributor" and "Subscriber" are just asking for trouble, so they are not options. "Author" is pushing it, but I leave it there anyway, because I can see instances where that would be helpful.)  This setting does not give permissions that the user level doesn't already have.  For example, if you set the level to "Author", then the authors of your site can write, edit and publish instructions, but it doesn't give them any extra capabilities like activating plugins and such.
	
	**Show in Front?** If you'd like to make your instructions visible on the front end, set this to "yes".  If it's the first time you're doing this, you WILL need to redo your permalinks. (All you have to do is go to your permalinks settings and hit the "save changes" button.)  If you swap this option back and forth, it may throw the system for a loop, so just try re-saving your permalinks again to see if that clears things up.
	
	**Logged-in users only?**  This goes with the above option.  By default, if the instructions are visible on the front end of the site, then end users are required to be logged in to see them (otherwise, they get a prompt to log in before viewing).  But you can set it so the general public can see them if you want.  The next option will help make things a bit more "private" for random visitors if you opt to make them visible to 100% of the population.
	
	**Default Viewing Level** This goes with the previous two options.  If you have "Show in Front?" set to "Yes", and "Logged-in users only?" set to "no" (thus making them publicly viewable) you can set this option to give the general public capability status.  (Don't worry, it's faked, and only for this plugin.  They won't *actually* have any capabilities!) Basically, this setting will treat random visitors as if they do have a logged-in status - this dropdown lets you choose what level they are at.  By default, it's "contributor", so anyone swinging by will be able to read posts marked for Subscribers and Contributors; but posts marked for Authors, Editors, Admins, and Super-Admins will be hidden.
	
7. **Other Front End Viewing Options** 

	**Theme Files** There are two theme files available for you to use - archive-instructions.php and single-instructions.php - if you enable front-end viewing.  Simply copy these files and paste them into the theme folder youre using.  The files are basic Twenty Eleven copies, with a bit of necessary code stuck in there to take your settings into account.  You can edit them to match your theme as you please.  Just be sure you move these files to your current theme, or any upgrade you make will overwrite whatever changes you've made.


== Frequently Asked Questions ==

= The instructions aren't showing when I use the custom instructions tab.
This actually has to do with jQuery and the character output.  The plugin uses jQuery to add the custom tab, as well as pull in the content from the instruction post.  If you use double quotation marks, brackets, and a few other types of characters, it will break the jQuery and cause the tab to not show.  If you run into these issues, try using an [HTML Character Entity](http://www.addedbytes.com/download/html-character-entities-cheat-sheet/png/) to replace the questionable characters in your content.  I currently do not have a running list of what characters cause the jQuery in this plugin to break - but I do know that "" and [] will do it if it's in your content.  Replace them with the appropriate HTML Entities (in this example, &amp;#34; for the double quotes and  &amp;#91; and &amp;#93; for the brackets), and your problem is solved (and the display will be fine).

= How do you pull these posts into the front end of the site? =
As of version 2.0 - due to the numerous requests I've gotten, you can now show them on the front-end of the site if you so desire.  Under "Settings > Back End Instructions", you may now choose to display the instructions on the front end of the site, as well as choose to keep them private (i.e. open them up to the public, or make a user log in before viewing).  See the "How to Use" section for further instructions.

**PLEASE NOTE**

This plugin was created to show these custom posts in the *back end* of the site, in the administrative area (thus the name of the plugin). The reason I did it this way is because when your client is logged in to the back end of the site, s/he can manage the site content and s/he will be in one single spot and can easily read their instructions without having to flip back and forth between an instructions page and what they are trying to do. The content pages of this plugin were not originally meant to be displayed on the front-end of the site. IMO, it negates the entire purpose of the plugin.  If you are looking for a plugin that will create custom post types for you to use on your site, then you're looking for a different plugin.

But due to numerous requests, I have enabled this capability.  I would imagine it's because you want to be able to see your instruction to see how it's formatted, etc., for editing purposes. (Or perhaps you just want your clients to stay out of the back end, for whatever reason.)  But know that the instructions you view on the front end of your site WILL NOT look like the instructions seen on the back-end, because the ones seen on the front end will use your theme's styling, which the back-end uses the admin styling.  

Again, I state: if you are simply looking for a plugin to set up a custom post type that you use on the front end of the site, this is NOT the plugin you want.  It was not designed for that purpose, and frankly, it never will be.

= Do you have any video content that is already created for the basic WordPress stuff? =

I do not.

Believe me, I've thought about it. But there's two reasons that I haven't done it yet... 1) it takes up a lot of time that I don't have at the moment, and I'd really have to soleley dedicate a few days to creating all of those videos. I deliver this plugin for free (because I love you all), but I do have to put food on the table, so client work takes precedence. 2) WordPress is upgraded quite often, and many times, the administrative area changes, so the videos are quickly outdated.  So along with reason #1, I'd have to take off time every time WordPress is upgraded. Which I wouldn't mind doing if I could! 


= Any other questions? =

By all means, feel free to ask away.  I'd also love input on features you'd like added or things you'd like to see to improve this plugin.  See the "Credit" information on how to contact me.


= Notes = 

1. This plugin uses custom post types to create the content for it.  If you decide you no longer wish to use this plugin, you need to decide what you'd like to do with the content.  If you want it completely removed from the database, then before you uninstall it, you need to go to "Instructions" and check the box to "Move to Trash", and then "Apply." Then you need to go the the trash and empty it to be sure the posts are completely removed from your database - THEN uninstall the plugin. If you want to keep the content as regular posts, then you will either manually have to edit your MySQL backup to change them from "instructions" to "post" (just open the database backup in a plain-text editor do a find/replace, save, and re-import), or use a plugin like [Post Type Switcher](http://wordpress.org/extend/plugins/post-type-switcher/) to handle that for you before you uninstall. Otherwise, it'll all just stay there and hang out, with no way to see it until you re-install the plugin again, or code your theme to pull the content out of the database to display it on the front end.

2. If you're in the mood to translate, please feel free to do so.  I've created a base .pot file in the "back-end-instructions/bei-languages" folder.  You can use [POEdit](http://www.poedit.net/) to create the new file - just be sure to keep the proper Wordpress language file naming conventions in mind.  (in other words, if you create a Spanish translation, the file name should be "back-end-instructions-es-ES.mo"


= Known Issues = 

* Bug with swapping instructions that contain playing videos. So far, I can only get YouTube videos to stop on tab switch. .swf, Vimeo and other player versions are still buggy in this regard.  If you change tabs, the video of the previously-opened tab will continue to play in the background.  So you have to be sure to stop the video before you change tabs. If you're a guru at jQuery, and feel like you might know the solution, have at it! In the meantime, I'll still be plugging away, trying to sort this out.


== Screenshots ==

1. Example of the Back End Instructions page.
2. A view of the edit.php page (where an instruction will show) with the instruction expanded under the "Help" tab.
3. Close-up view of the ability to add an instruction to multiple pages on the back end.


== Changelog ==

= 3.1 =
* Bugfix - renamed a variable that was causing a conflict with other plugins.
* Added German and Spanish translations. I don't speak German or Spanish, so if I'm off, please feel free to edit :)

= 3.0 =
* made a few security fixes
* updated the options table
* removed the addition of the "first post" since it's really unnecessary
* re-added the "Instructions" tab - but now it's optional
* heavy rewrite of code to clean up and streamline
* added css admin files (for custom "Instructions" tab to match user settings admin colors)
* fixed the replacement brackets for shortcodes ("{{ }}") so the front-end instructions no longer parse them

= 2.5.2 =
* Bugfix: swapped a custom loop for a custom query to fix a reported conflict with a WooThemes theme.

= 2.5.1 =
* Bugfix: missing variable.

= 2.5 =
* pluggable.php won't load until after ALL plugins have loaded, and only if needed.
* removed the bei_query_vars function, as it was causing issues with filtering posts in the admin. (It was a function put in a long time ago to make sure the instructions ddn't show up in search results - but the function is no longer necessary as it's taken care of in the options page)
* Fixed a jQuery conflict that was causing the "+" fields to not work.
* Fixed issue with "xxxx characters of unexpected output" upon activation.
* Updated "How To" video to show the new features, etc. (the old one was causing confusion - pardon my laziness!)
* Fixed it so the initial "How To Use Back End Instructions" post isn't indexed in the search engines, because no one wants that.

= 2.4 =
* Finally fixed the issue of the Instructions post type not showing up when Wordpress SEO is installed and activated.
* Fixed issue where radio buttons don't function in setting page when the Ultimate TinyMCE (by Josh Lobe - tested version 4.0.3, which was the only version I have access to) plugin is used.

= 2.3 =
* Discovered an issue where instructions that were created before version 2.2 would not show up anywhere after the upgrade.  My bad.  Variable misnaming.  It's now fixed.

= 2.2 =
* tested in WordPress 3.5-alpha, minor bug fixes and code cleanup for the upcoming WordPress release.
* tested in a Multi-Site environment, seems to work fine.  (Please contact me if you find out otherwise, because my tests are only in a localhost environment.)
* removed stylesheets because they actually aren't necessary anymore

= 2.1 =
* some commits didn't take - partial upgrade. Trying again to get all the right files in there.

= 2.0 =
* Further streamlined and updated code, removed unnecessary stuff. This makes this version of the plugin unusable for Wordpress versions below 3.1.
* Created options page for defaults
* Can now provide shortcode instructions without parsing the shortcode.
* Set up an option to allow you to display instructions on front end.
* Set up an secondary option to make instructions that are shown on the front-end visible to everyone (public) or only to logged-in users (private).
* Fixed the translation stuff by creating .pot file for easier translation, removed default .mo and .po files.
* added capability for popping in an instruction to a list of specified pages.
* edited the output in regards to the video: you can now show video AND text, instead of having the video replace the text.
* updated/cleaned up readme.txt file.
* created a banner for the plugins page (just for fun)

= 1.1 =
This is an "idiot's release" - I committed changes before I was supposed to - and committing uncovered bugs I didn't forsee.  Totally my bad!

= 1.0 =
* updated code to streamline and make it more efficient.
* added an option to the database to check if the plugin is installed so the initial "How to Use" post isn't created over and over again, even after deletion.
* updated/cleaned up the readme file.
* removed the CSS and embed_loader files and folders.
* updated the video embedding to use HTML5 Video.
* because I'm an idiot and forgot to add the mo-po files for translation, I've added the mo-po files for translation.
* it was pointed out to me that these items can be included in front-end search results.  This issue is now fixed for both future and past installations.
* cleaned up CSS to make it better match the default styling (classic admin theme)
* fixed it so you don't see the flash of expanded content on page load (before the stylesheet kicks in and hides it)

= 0.8 =
* further fixed "Notice" warnings - especially when adding a new one. *NOTE: there is some kind of bug with WordPress, custom fields, and auto-save. If your error-reporting is on, the AJAX responder in WP will show an error in a pink box. Don't worry about the error - it's just an annoyance for now, and only shows up when you add a brand-spankin' new instruction, after you give it a title. Everything still works fine.
* Changed custom post type to reflect where you are (i.e. change "Add New Post" to "Add New Instruction", etc.)
* removed the need for an additional plugin to hide the instructions menu option from lower-level users. Now anyone with "Editor" or "Administrator" level access will see the menu option, but anyone below that level will not.
* removed the extra "Summary" section, since it's redundant. The Excerpt area works as the descriptions, and instead of having the post content replaced by video content, it'll just add to the video content, if so desired.
* fixed it so you can specify a particular back-end page, or just a general area for an instruction to appear. (i.e. if you want soemthing to appear on "post-new.php" just enter that in the "Page Name" area, but if you want the instruciton to only appear on the "Add New Instruction" pagem, and not the regular "Add Post" pages, you put in "post-new.php?post_type=instructions".
* added easier styling capability.

= 0.7 = 
* fixed issue where people were receiving "Notice" information when they had error_reporting turned on. These warnings were due to empty variables. (Once information was put in, they would go away, but on initial acitvation they would show because no info had been put in yet.) A minor annoyance that didn't affect the functionality of the plugin - now taken care of.

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
* cleaned up/streamlined terminal code

= 0.2 = 
* Fixed issue where instructions for the dashboard wouldn't show up upon initial login (dashboard).

= 0.1 = 
* First release.

== Upgrade Notice ==

This update provides a few bugfixes, and contains a few search and minor security fixes.  I have also added a Spanish and German translation.  If you have any issues, please ask in the <a href="http://wordpress.org/support/plugin/back-end-instructions">support forums</a> for the plugin.  I\'m pretty good about helping out!

== Other Notes ==

If you have any questions, comments or suggestions for improvement, feel free to contact Shelly at [Brass Blogs Web Design](http://brassblogs.com/contact "Web Design in Hartford, Farmington Valley, Granby, Avon, Simsbury, Weatogue CT"). If you prefer Twitter, I'm [@brassblogs](http://twitter.com/brassblogs).

Given that this is free, I offer limited support. Pretty much if you have issues with the plugin *working* I will do whatever I can to help you fix the issue, but when it comes to customizations, I'm in limited supply.  I'll do what I can, but no guarantees.  Pretty much your standard "as-is" application.  In all honesty, ask customization questions in the forums - if I can't help, perhaps someone else can.  (If you want to hire me to customize it, that's another story - feel free to contact me to do so!)