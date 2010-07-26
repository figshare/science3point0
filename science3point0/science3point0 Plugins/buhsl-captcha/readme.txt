=== Buhsl Captcha ===
Contributors: Gennadiy Bukhmatov
Author URI: http://buhsl.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NVVSDREWKGVZG
Tags: captcha, comment, comments, login, anti-spam, spam, security, buddypress, wpmu, wordpressmu
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: trunk

Adds CAPTCHA anti-spam methods to WordPress on the comment form, registration form. WPMU and BuddyPress compatible.

== Description ==

Buhsl Captcha is a very simple, but powerful plug-in that helps you to prevent spam. 
Plug-in don't use COOKIES or SESSION. So there is no annoying messages like 
"You should enable cookies" for visitors. Plug-in use hashed captcha code value that 
will be comparing with entered captcha value and check if correspondent image file exists on server. 
Image of captcha created during request and stored at server.  
When user entered right captcha value image file will be removed. 
If not, garbage collector will remove it after time life is passed.

[Plugin URI]: (http://buhsl.com/wp-plugins/buhsl-captcha/)

Features:
--------
 * Configure from Admin panel
 * JavaScript is not required
 * Valid HTML
 * Allows Trackbacks and Pingbacks
 * Hide the CAPTCHA from logged in users and or admins

Requirements/Restrictions:
-------------------------
 * Works with Wordpress 2.9.2+, WPMU, and BuddyPress
 * PHP 4.0.6 or above with GD2 library support.
 * Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your comments.php form.

== Installation ==

1. Upload the `buhsl-captcha` folder to the `/wp-content/plugins/` directory, or download through the `Plugins` menu in WordPress
2. Activate the plugin through the `Plugins` menu in WordPress


1. This is how to install SI Captcha globally on WPMU or BuddyPress:
2. Step 1: upload the content of 'buhsl-captcha' folder and all it's contents to `/mu-plugins/`

== Configuration ==

After the plugin is activated, you can configure it by selecting the `Buhsl Captcha` tab on the `Admin Plugins` page.


== Usage ==

Once activated, a captcha image and captcha code entry is added to the comment and register forms. 
The Login form captcha is not enabled.

== Frequently Asked Questions == 
No data
== Changelog == 
No data
== Upgrade Notice == 
No data
== Screenshots ==
No data