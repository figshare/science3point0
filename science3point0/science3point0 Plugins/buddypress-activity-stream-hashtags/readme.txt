=== Plugin Name ===
Contributors: nuprn1
Donate link: http://buddypress.org/community/groups/buddypress-activity-stream-hashtags/donate/
Tags: buddypress, activity stream, activity, hashtag, hashtags
Requires at least: PHP 5.2, WordPress 2.9.2, BuddyPress 1.2.4.1
Tested up to: PHP 5.2.x, WordPress 3.0, BuddyPress 1.2.5.2
Stable tag: 0.3.1

This plugin will convert #hashtags references to a link (activity search page) posted within the activity stream

== Description ==

This plugin will convert #hashtags references to a link (activity search page) posted within the activity stream

Works on the same filters as the @atusername mention filter (see Extra Configuration if you want to enable this on blog/comments activity) - this will convert anything with a leading #

Warning: This plugin converts #hashtags prior to database insert/update. Uninstalling this plugin will not remove #hashtags links from the activity content.

Please note: accepted pattern is: `[#]([_0-9a-zA-Z-]+)` - all linked hashtags will have a css a.hashtag - currently does not support unicode.


= Also works with =
* BuddyPress Edit Activity Stream plugin 0.3.0 or greater
* BuddyPress Activity Stream Ajax Notifier plugin


= Related Links: = 

* <a href="http://blog.etiviti.com/2010/06/buddypress-activity-stream-hashtags-plugin/" title="BuddyPress Activity Stream Hashtags - Blog About Page">About Page</a>
* <a href="http://etivite.com" title="Plugin Demo Site">Author's BuddyPress Demo Site</a>


== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page

== Frequently Asked Questions ==

= What pattern is matched? =

The regex looks for /[#]([_0-9a-zA-Z-]+)/ within the content and will proceed to replace anything matching /(^|\s|\b)#myhashtag/

= Can this be enabled with other content? =

Possible - try applying the filter `bp_activity_hashtags_filter`

See extra configuration

= Why convert #hashtags into links before the database save? =

The trick with activity search_terms (which is used for @atmentions) is the ending </a> since BuddyPress's sql for searching is %%term%% so #child would match #children

= What url is used? =

`$bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/myhashtag`

Where you may define the slug in your wp-config.php:

`define( 'BP_ACTIVITY_HASHTAGS_SLUG', 'tag' )`

= My question isn't answered here =

Please contact me on

* <a href="http://blog.etiviti.com/2010/06/buddypress-activity-stream-hashtags-plugin/" title="BuddyPress Activity Stream Hashtags - Blog About Page">About Page</a>
* <a href="http://twitter.com/etiviti" title="Twitter">Twitter</a>


== Changelog ==

= 0.3.1 =

* Bug: Added display_comments=true to activity loop to display all instances of a hashtag search (thanks r-a-y!)

= 0.3.0 =

* Feature: RSS feed for a hashtag (adds head rel and replaces activity rss link)
* Feature: Added filter for hashtag activity title

= 0.2.0 =

* Bug: Filtering hashtags (thanks r-a-y!)

= 0.1.0 =

* First [BETA] version


== Upgrade Notice ==



== Extra Configuration ==

= Add hashtags to activity stream excerpts for blog posts and comments =

Add the following filters to your theme functions.php or bp-custom.php file

`
add_filter( 'bp_blogs_activity_new_post_content', 'bp_activity_hashtags_filter' );
add_filter( 'bp_blogs_activity_new_comment_content', 'bp_activity_hashtags_filter' );
`
