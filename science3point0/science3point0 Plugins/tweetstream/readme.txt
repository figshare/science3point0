=== Tweetstream ===
Contributors: Blackphantom
Tags: Buddypress, Twitter, Tweet,Tweetstream
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TKBY4JM6WDSD2
Requires at least: WP 2.9.1, BuddyPress 1.2.3
Tested up to: WP 3.0, BuddyPress 1.2.5
Stable tag: 1.4

== Released under the GPL license ==
http://www.opensource.org/licenses/gpl-license.php

== Description ==

Tweetstream a Buddypress twitter intergration plugin.

With this plugin you can synchronize your activity stream and your tweets.
Also you can send a tweet upon forum topic creation and topic reply.

Everything is designed for easy-intergration, easy-setup and easy-usability.
With a lots of admin and per user settings.

This plugins uses the cron functionality of wordpress, in wp 2.9.1 and wpmu 2.9.1 the cron was broken.
Please upgrade to 2.9.2 for this plugin to work.

!!! Please deactivate en re-activate the plugin after every upgrading to newest release!!!

Some options:
- Synchronise twitter and buddypress activity's
- Post new topic links to twitter.
- Post topic reply's to twitter.
- Extra filter on activity stream to show tweets.
- A lots of user and admin options
- Multilanguage!
  
The plugin got full localisation support.
Check out the screenshots for more info.

== SPECIAL THANKS TO ==
All the poeple who donated and translated the plugin!

== Installation ==
1. Upload this plugin to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Setup the tweetstream plugin in the admin.
4. Done!


!!! Please deactivate en re-activate the plugin after every upgrading to newest release!!!


Requirements.
- PHP5
- CURL
- JSON

== Screenshots ==
1. Tweetstream
2. Tweetstream
3. Tweetstream
4. Tweetstream
5. Tweetstream
6. Tweetstream
7. Tweetstream
8. Tweetstream
9. Tweetstream

== ChangeLog ==

= 1.4 = 
* Fixed exeption errors when twitter is having problems.
* Fixed max imports per user (per day).
* Added new tweet icon
* Better check if twitter is down
* Added new icons for admin and front-end
* Added list of tweetstream users in admin with reset function
* Added stats of tweetstream in the admin
* Added a help/future updates page in the admin
* Seperated settings in admin


= 1.3.3 = 
* Fixed filters

= 1.3.2 = 
* jQuery prevented sending updates to twitter, now fixed

= 1.3.1 = 
* Fixed import (imports again)

= 1.3 = 
* Improved memory usage
* Update twitter classes
* Cleaner code
* Perfomace update for import
* Better filtering functions
* Fixed custom members slug
* Fixed broken tweet in topic creation/reply.
* Able to adjust import interval in admin 

= 1.2.3 = 
* Fixed import
* Build in extra validation checks

= 1.2.2.1 =
* Forgot to turn on cron,sorry

= 1.2.2 = 
* Removed wordpress cron, and replaces with own cron code
* Performance fixed

= 1.2.1.2 = 
* Fixed jquery calls change (due upgrade bp 1.2.3)
* Fixed import

= 1.2.1.1 =
* Fixed settings page

= 1.2.1 =
* Fixed cron problem
* Fixed profile url and normal url blending
* Fixed conflict betweet facestream and tweetstream
* Fixed problem: when upgrading buddypress tweetstream gave errors.
* Fixed problem: tweetstream breaks edit function in BP Forums.
* Fixed topic to twitter problem

= 1.2.0 =
* Better cron, now get tweets every 5 minutes, no more one profile visits.
* Ajusted filtering now if hashtag wanted add # to word.
* Short profile url fix.
* Now adds the #twitter tag on submit.

= 1.1.9 = 
* Fixed cron updates added messages as admin
* Added Italian translation.
* Added Turkish translation.
* Fixed typo in language file
* Added admin option to turn off syncing to bp for all users
* Added admin option to set max import of tweets per user.

= 1.1.8 =
* Updated German language file
* Update more then 140 characters wherent cut-off corectly
* Fix on get_results error

= 1.1.7 = 
* Added more places to import tweets (faster updated).
* Added cron for tweets import.
* Fixed deny access on twitter bug.
* Small bug with syncing fixed.
* Added russian translation.
* Shortend url when user chooses for profile link added to tweet.
* cyrillic texts fix.


= 1.1.6 = 
* Added some localisation fixes
* Removed @ when posting to twitter (mention conversion will be there in a later version).
* Added personsal filters option (per user filter).
* Added admin explicit words filter.
* Fixed bug with importing tweets.
* Fixed admin page visible to users bug.

= 1.1.5 =
* Fixed double tweet import (now checking with tweet_id)
* Added localisation
* Languale files added:
    - english
    - dutch

= 1.1.4 =
* Added admin option to remove filter tags in tweets or not.
* Fixed special characters to twitter.
* Fixed some time display and saving issues.
* Removed tinyurl API and added own url shorten function.
* Javascript include url fix.

= 1.1.3 =
* Speeded up the import, almost live updates now!
* When creating a topic on the forums possible to twitter it, wil shown as topic title + backlink (tinyurl) to topic
* Security fix, user could change the url to get to other users settings.

= 1.1.2 =
* Removed some hard-coded stuff (oops).
* Added option to turn syncing from twitter to activity off.
* Added option to remove profile link behind tweet.
* Added admin option to remove "Want to twitter..." message.
* Fixed user profile link on tweet messages.
* Added seperated js and css file.
* Improvement off looks.

= 1.1.1 =
* Quotes to twitter fix
* WMPU support fix
* Filter bug fix


= 1.1 =
* Options page in admin to setup twitter API
* Filters, to filter specific tweets

= 1.0 =
* First release