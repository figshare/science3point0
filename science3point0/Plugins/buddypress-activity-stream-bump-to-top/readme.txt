=== Plugin Name ===
Contributors: nuprn1
Donate link: http://buddypress.org/community/groups/buddypress-activity-stream-bump-to-top/donate/
Tags: buddypress, activity stream, activity
Requires at least: PHP 5.2, WordPress 2.9.2, BuddyPress 1.2.4.1
Tested up to: PHP 5.2.x, WordPress 3.0, BuddyPress 1.2.5
Stable tag: 0.3.1

This plugin will "bump" an activity record to the top of the stream when activity comment reply is made.

== Description ==

This plugin will "bump" an activity record to the top of the stream when activity comment reply is made.

* BuddyPress 1.2.4.1 and higher ONLY!!! * (due to bug in earlier versions of BuddyPress)

The original date_recorded is appended to the time_since filter with the span class time-created. Both timestamps are displayed on the activity stream meta

= Related Links: = 

* <a href="http://blog.etiviti.com/2010/05/buddypress-activity-stream-bump-to-top-plugin/" title="BuddyPress Member Profile Stats - Blog About Page">About Page</a>
* <a href="http://etivite.com" title="Plugin Demo Site">Author's BuddyPress Demo Site</a>


== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page
3. Adjust settings via the Activity Bump admin page

== Frequently Asked Questions ==

= How do I exclude a certain activity type from being bumped? =

The wp-admin screen for this plugin allows you to exclude certain activity types from being bumped.

= How does it work? =

When a new comment is posted to an activity record - this plugin will copy the original date_recorded to the activity_meta table. The main activity date_recorded is then overwritten with the last activity comment reply date.

= I really do not like it and want my old dates back =

Have no fear - you can revert the dates back to the original date_recorded via the plugin's admin page. Perform this action before uninstall.

= My question isn't answered here =

Please contact me on

* <a href="http://blog.etiviti.com/2010/05/buddypress-activity-stream-bump-to-top-plugin/" title="BuddyPress Activity Stream Bump to Top - Blog About Page">About Page</a>
* <a href="http://twitter.com/etiviti" title="Twitter">Twitter</a>


== Changelog ==

= 0.3.1 =

* Added filter bp_activity_bump_time_since to time-since output
* Added 'updated' string next to bump timestamp

= 0.3.0 =

* Plugin released

= 0.1.0 =

* First bp hack version


== Upgrade Notice ==



== Extra Configuration ==

add a filter to bp_activity_bump_time_since (date_recorded, $bumpdate, $content) 