=== Plugin Name ===
Contributors: nuprn1
Donate link: hhttp://buddypress.org/community/groups/buddypress-edit-activity-stream/donate/
Tags: buddypress, activity stream, activity, status
Requires at least: PHP 5.2, WordPress 2.9.2, BuddyPress 1.2.4.1
Tested up to: PHP 5.2.x, WordPress 3.0, BuddyPress 1.2.5
Stable tag: 0.3.0

This plugin allows an user to edit their activity stream status update within a specified time period.

== Description ==

This plugin allows an user to edit their activity stream status update within a specified time period.

Allows site admin to edit any activity update (except forum topics and replies)

= Related Links: = 

* <a href="http://blog.etiviti.com/2010/06/buddypress-edit-activity-stream-plugin/" title="BuddyPress Edit Activity Stream - Blog About Page">About Page</a>
* <a href="http://etivite.com" title="Plugin Demo Site">Author's BuddyPress Demo Site</a>


== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page
3. Adjust settings via the Activity Edit admin page

== Frequently Asked Questions ==

= What is the time interval for locking out the edit? =

Please set the time length that conforms to http://www.php.net/manual/en/datetime.formats.relative.php

= How do I change the theme for the edit page? =

Copy the file buddypress-edit-activity-stream/templates/activity/activity-edit.php to your child bp-themes/activity/ directory

= Why can't I edit my activity reply comment? =

Currently the bp-core file does not include a filter on the admin links (reply delete) within the activity comments.

= My question isn't answered here =

Please contact me on

* <a href="http://blog.etiviti.com/2010/06/buddypress-edit-activity-stream-plugin/" title="BuddyPress Edit Activity Stream - Blog About Page">About Page</a>
* <a href="http://twitter.com/etiviti" title="Twitter">Twitter</a>


== Changelog ==

= 0.3.0 =

* Feature: support for activity stream hashtag plugin

= 0.2.0 =

* Bug: update _usermeta bp_last_update if editing activity->ids match up

= 0.1.0 =

* First [BETA] version


== Upgrade Notice ==



== Extra Configuration ==

