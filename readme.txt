=== dashboard-last-news ===
Contributors: andre renaut
Donate link: don't forget to rate this plugin !
Tags: Feed, Aggregator, SimplePie, Widget, Dashboard, Wordpress, Plugin, Management, Last, News, Last News
Requires at least: 2.5
Tested up to: 2.5
Stable tag: 2.5.0.1

Allows you to display the last items of several feeds on your dashboard 

== Description ==

Allows you to display the last items of several feeds in your dashboard 

	Look and Feel totally compliant with Wordpress 2.5 
	For each widget, aggegates as much feeds as you like (1 to 10) and display the last items (3 to 20).
	Up to 5 different Last News widgets on your Dashboard !!!
	Supported languages : English, French	(.pot provided)


Tested on Firefox2, Internet Explorer 7, Safari 3.1 (Windows XP)

== Installation ==

###First ! & Important Stuff !###

	a) **create** the folder cache under wp-content, **make sure it is writeable**.
		This should result in the following file structure:

		`- wp-content
		    - cache`

	b) **plugin** : (unless you are already using SimplePie class) [SimplePie core](http://simplepie.org/wiki/plugins/wordpress/simplepie_core)


###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`.

This should result in the following file structure:

`- wp-content
    - plugins
     	  - dashboard-last-news
            | dashboard-last-news.php
            | readme.txt
            | screenshot-1.jpg
            | screenshot-2.jpg
            | screenshot-3.jpg
            | screenshot-4.jpg
            | screenshot-5.jpg
            | screenshot-6.jpg
            
            - css
                 | lastnewsslider.css
            
            - img
                 | bout.png
                 | fond.png
             
            - js
                 | lastnewsslider.js
             
 	           - ui
                      | jquery.dimensions.js
                      | ui.mouse.js
                      | ui.slider.js

            - lang
                 | dashboard-last-news.pot
                 | dashboard-last-news-fr_FR.po
                 | dashboard-last-news-fr_FR.mo

            - php
                 | ajax.php
                 | settings.php
                 | setform.php`

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

Then just visit your admin area and activate the plugin. **Make sure your simplepie core plugin is also activated**.

* Edit the widget with the widget control panel (Edit link)
	- set the title, 
	- display images if available (y/n), 
	- set the number of url feeds (need to save them and re edit the widget if you need more space),
	- set the number of lines 
	- fill your url feeds in the widget control panel.

* Save your settings ...

* Enjoy !

== Frequently Asked Questions ==



= Can i have more than one Last News widget ? ... =

 YES, check the Last News settings panel ! You can have up to 5 different widgets !!!

= Editing the widget, i do not see the save button ... =

 Scroll down !

= My dashboard widgets are displayed in the wrong order ! =

 Sorry but Wordpress 2.5 does not provide any dashboard widget managing page as for your sidebars (Design > Widgets). 
  I am sure this point will be solved in a next future.
  I personnaly use this plugin : [Dashboard Widget Manager](http://wordpress.org/extend/plugins/dashboard-widget-manager).

= How long the feeds are cached ? =

	SimplePie default is 1 hour (3600 seconds).
	NOT RECOMMENDED : but if you want to change this, 
		* edit the plugin file, 
		* find this string : `//SimplePie default cache duration is 3600 sec (1 hour)`
		* and you might see what you have to do ...
	[More about SimplePie caching system](http://simplepie.org/wiki/faq/how_does_simplepie_s_caching_http_conditional_get_system_work)

== Screenshots ==

1. Displaying the widget
2. Editing the widget
3. Scroll down !
4. Widget settings
5. Your future dashboard !
5. my Wordpress News

== Log ==

 *2.5.0.1*  	2008/04/12 minor bugs
				-> no translation with ajax
				-> trust SimplePie for thumbnails
				-> display every widget if Dashboard Widget Manager plugin is not active.

 *2.5*     		2008/04/12 first release

== Next features ==

**Any new idea** or **code improvement** can be posted at : contact@nogent94.com


on Windows/Safari 3.1 items cannot be clicked (it is just a silly &lt;a&gt;&lt;/a&gt; !!!) ??? and i don't know if Mac/Safari will have the same behaviour !!!
bug as been reported to Apple.

