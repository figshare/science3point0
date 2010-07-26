=== Seo for Buddypress ===
Contributors: svenl77,mahype
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NWEYBQUNE5PVY
Tags: seo,buddypress,wpmu,wp
Requires at least: 2.9.x & buddypress 1.2.x
Tested up to: 2.9.x & buddypress 1.2.x
Stable tag:  1.2.4 beta

This plugin adds an option page Seo for Buddypress under your Buddypress admin.

== Description ==

It gives you the possibility to enter title, description and keywords for the main blog, the user blogs and the buddypress pages.

Read what others are saying about Seo for Buddypress on wpmu.com<br>
http://wpmu.org/optimize-buddypress-for-search-engines-with-the-newly-updated-seo-plugin/
<br><br>
The 1.x version is a complete rewrite of the old plugin.<br>
It has a new logic, and much more functionality.<br>
If you update from an old version, please go to the settings page and click on "Update Database". <br>
<br>
For bug report and feature requests please go to:<br><br>
http://sven-lehnert.de/en/2009/04/29/buddypress-plugin-seo-for-buddypress/<br>
<br>
== Installation ==

1. Upload 'Seo for buddypress' to the '/wp-content/plugins/' directory<br>
2. Activate the plugin through the 'Plugins' menu in WordPress<br>
<br>
In the main blog theme header.php, must exist<br>
< title > < ?php bp_page_title() ? > < /title >
<br>
In the user blog theme header.php, must exist<br>
< title > < ?php wp_title(); ? > < /title >

<br>
These are the standard title tags. Added by buddypress and wordpress by default in the header.php.<br>
If you did not change your themes, or did any customisation, they will be there. <br>
<br><br>
That's it, have fun.<br><br>

Update:
If you update from an old version, please go to the settings page and click on "Update Database". <br><br>

==  Other Notes  ==  
1.2.3 beta<br>
I killed the menue with the last update... should be fixed now<br>
1.2.4 beta<br>
I killed the menue with rhe last update... should be fixed now<br>
Fixed a conflict reported by Terence (forum-attachments-for-buddypress use the same function named curPageURL<br>
Changed the Plugin menue behaviour, so if bp is instaled, the menue will be under Buddypress<br>
1.2.2 beta<br>
Fixed many bugs reported by the buddypress community. Thanks for all the feadback.<br>
1.2.1 beta<br>
Plugin is now ready for Wordpress and Wordpress MU without Buddypress | Added new Meta fields for post and pages | Compatible with wpSEO and AllInOne Seo Page Meta data<br>
1.1<br>
Stable version<br>
1.0.10 beta<br>
Fixed some Bugs reported by stwc. <br>
1.0.9 beta<br>
There was a problem with version 1.8...it prevents blog creation. <br>
1.0.8 beta<br>
Fixed a lot of bugs, reported in the forum<br>
1.0.7 beta<br>
Fixed a bbPress forums bug if forums component is disabled Fatal error: Class 'BP_Forums_Template_Forum' <br>
1.0.5 beta<br>
Fixed the %%forumtopictitle%% bug <br>
1.0.4 beta <br>
Add special tags for forum<br>
1.0.3 beta <br>
Missed to close a </strong> tag <br>
1.0.2 beta <br>
Fixed some Bugs <br>
1.0.1 beta <br>
add_action( 'bp_init', 'bp_seo_init' )<br>
1.0 beta <br>
This version is a complete rewrite of the old plugin.<br>
It has a new logic, and much more functionality.<br>
0.6.9<br>
Fixed some bugs, to make it work with bp 1.2<br>
0.6.8<br>
Fixed some bugs<br>
0.6.7<br>
Add Seo Options:<br>
PROFILE BLOGS RECENT POSTS<br>
PROFILE BLOGS RECENT COMMENTS<br>
PROFILE ACTIVITY FRIENDS<br>
GROUPS FORUM TOPIC<br>
YOUR OWN PROFILE PAGE & YOUR OWN DIRECTORY<br>
Changed some functions and file names like common in buddypress.<br>
That's why "Seo for Buddypress" is renamed in "bp-seo".<br>
Changed the meta function according to<br>
http://codex.buddypress.org/developer-docs/conditional-template-tags/
0.6.6<br>
Fixed the nav bug.<br>
0.6.5<br>
I have problems with the svn. I lost some js and css again.<br>
Tray again to fix nav bug...<br>
0.6.4<br>
Changed the way Seo for Buddyp0ress is added to the Buddypress admin panel.<br>
There has bean some conflicts out there.<br>
I hope it will work now for everyone.<br>
0.6.3<br>
update to work with wpmu 2.8.5.2<br>
Fixed some Bugs in the Main Blogs<br>
Fixed a Bugs in the Events Dirctory Blogs<br>
0.6.2.a<br>
I lost some js in 0.6.2. In this version added again<br>
0.6.2<br>
added bp-events plugin 1.1 support for all event pages<br>
added user blogs info help text<br>
0.6<br>
update to work with buddypress 1.1.2<br>
added main blog and user blogs support.<br>
0.5<br>
integrate the directories view groups, members, blogs and events<br>
0.4<br>
fix a bug reported by Mark Leonard.<br>
Next Version 0.5 I will integrate the directories view groups, members, blogs and events.<br>