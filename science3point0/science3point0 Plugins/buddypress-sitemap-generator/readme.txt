=== Plugin Name ===
Contributors: nuprn1
Donate link: http://firevortex.net/donatebeer/
Tags: buddypress, sitemap, sitemaps, google sitemaps
Requires at least: PHP 5.2, WordPress 2.9.2, BuddyPress 1.2.x
Tested up to: PHP 5.2.x, WordPress 2.9.2, BuddyPress 1.2.1
Stable tag: 0.0.4

This plugin will generate a sitemapindex and various component sitemap xml files for search engines and indexing of BuddyPress

== Description ==

This plugin will generate a sitemapindex and various component sitemap xml files for search engines: Google, Bing, Yahoo and Ask.com to better index BuddyPress. 

With such a sitemap, it's much easier for the crawlers to see the complete structure of your site and retrieve it more efficiently.

* This release is compatible with BuddyPress 1.2.1 and WordPress 2.9.2.


= Related Links: =

* <a href="http://blog.etiviti.com/2010/02/buddypress-sitemap-generator/" title="BuddyPress Sitemap Generator - Blog About Page">About Page</a>
* <a href="http://etivite.com" title="Plugin Demo Site">Author's BuddyPress Demo Site</a>


= Based on: =

* <a href="http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/" title="Google XML Sitemaps Plugin for WordPress">Google XML Sitemaps Plugin for WordPress</a>

= This is a early BETA release: =

This plug-in does not invoke BP components but rather queries MySQL directly. If you test this plug-in, please use the debug rebuild option which will output logs.

A daily cron to generate the sitemap is set upon activation - you may change this - see FAQ


= What to expect: =

* no fancy priority
* no options yet besides ping/sitemap location
* limited to 50k urls for each component

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Use your favorite FTP program to a file in your WordPress/Base directory (that's where the wp-config.php is) named bp-sitemap.xml

**Depending which BuddyPress components are activited - YOu may need to create the following as well:**
* bp-sitemap-activity.xml & bp-sitemap-activity.xml.gz (all activity)
* bp-sitemap-members.xml & bp-sitemap-members.xml.gz (all members)
* bp-sitemap-members-f.xml & bp-sitemap-members-f.xml.gz (profile friends)
* bp-sitemap-members-g.xml & bp-sitemap-members-g.xml (profile groups)
* bp-sitemap-members-a.xml & bp-sitemap-members-a.xml (profile activity)
* bp-sitemap-members-x.xml & bp-sitemap-members-x.xml(xprofile)
* bp-sitemap-groups.xml & bp-sitemap-groups.xml(only public and private groups)
* bp-sitemap-groups-f.xml & bp-sitemap-groups-f.xml (forum topics for public groups)

 and make them writable via CHMOD 666. More information about CHMOD and how to make files writable is available at the [WordPress Codex](http://codex.wordpress.org/Changing_File_Permissions) and on [stadtaus.com](http://www.stadtaus.com/en/tutorials/chmod-ftp-file-permissions.php). Making your whole blog directory writable is NOT recommended anymore due to security reasons.

4. Activate the plugin at the plugin administration page
5. Open the plugin configuration page, which is located under Settings -> BuddyPress Sitemap and build the sitemap the first time. If you get a permission error, check the file permissions of the newly created files.
6. Currently - You will need to generate this sitemap manually for updated activity, members, groups, forum posts, etc.

== Frequently Asked Questions ==

= When does the sitemap update? =

Currently upon activation of this plugin - a daily cron will be set. In your wp-config.php file you may add `define( 'BP_SITEMAP_CRON_INTERVAL', 'daily' );` to one of the accepted wp-cron values (hourly, twicedaily, daily)

If you need to change the time of execution - simply deactivate and reactivate the sitemap at the time you wish to the cron build to occur (this will change in future release to a selectable time)

= I can not set the priority for certain components and urls! =

This is a **BETA** and currently priority is hardcoded into bp-sitemap-core.php file

= Does this plugin work with WordPressMU? =

I'm not sure - please test and let me know.

= I get an fopen and / or permission denied error or my sitemap files could not be written =

If you get permission errors, make sure that the script has the right to overwrite the sitemap.xml and sitemap.xml.gz files. Try to create the sitemap.xml resp. sitemap.xml.gz at manually and upload them with a ftp program and set the rights with CHMOD to 666 (or 777 if 666 still doesn't work). Then restart sitemap generation on the administration page. A good tutorial for changing file permissions can be found on the [WordPress Codex](http://codex.wordpress.org/Changing_File_Permissions) and at [stadtaus.com](http://www.stadtaus.com/en/tutorials/chmod-ftp-file-permissions.php).

= Why does this plugin generate multiple sitemap files? =

Per the sitemap.org protocol - urls are limited to 50k per file. Given the community nature of BuddyPress, extracting content into a sitemapindex will allow larger sites to publish all urls.

= My question isn't answered here =

Please contact me on
* <a href="http://blog.etiviti.com/2010/02/buddypress-sitemap-generator/" title="BuddyPress Sitemap Generator - Blog About Page">About Page</a>
* <a href="http://twitter.com/etiviti" title="Twitter">Twitter</a>


== Changelog ==

= 0.0.4 =

* wp-cron hook

= 0.0.3 =

* If write xml file was disabled - sitemapindex was not generated
* Clean up the old UI a little bit

= 0.0.2 =

* Added member/url options if they exists
* Fixed up ping logging

= 0.0.1 =
* First [BETA] version to try out


== Upgrade Notice ==


== Extra Configuration ==

You can set all standard urls or if data exists for member subnav urls (member/group member/friends member/profile)
