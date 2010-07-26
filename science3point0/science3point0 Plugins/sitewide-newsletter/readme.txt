=== Sitewide Newsletters ===
Contributors: mrwiblog
Donate link: http://www.stillbreathing.co.uk/donate/
Tags: wordpress mu, buddypress, personal, welcome, email, message, greet
Requires at least: 2.7
Tested up to: 3
Stable tag: 0.3.2

Sitewide Newsletters is a Wordpress MU plugin that allows site administrators to send an email message to all users.

== Description ==

This plugin for Wordpress MU is really simple. It allows site administrators to send a (plain text) to all users. You can type in the subject and text for the email, and it will try to send it to all user email addresses in the database.

That's pretty much it.

There are two small additional features with this plugin. Firstly if any emails fail to be sent the failing email addresses will be shown in a list, so you can decide what to do. Secondly there is a "Test" checkbox, which when checked will only send the newsletter to the site admin email address.

If you have a lot of users this plugin may take a long while to run, as it sends a separate email for each user. There is some reasoning behind that: in the future I may write in some special codes that you can use in your emails that will insert the users email address (so they can unsubscribe, for instance), a link to their wp-admin folder, and more user-specific data. That stuff is pretty much impossible to do if you use BCC to send one email to multiple people.

== Installation ==

The plugin should be placed in your /wp-content/mu-plugins/ directory (*not* /wp-content/plugins/) and requires no activation.

== Frequently Asked Questions ==

= Why did you write this plugin? =

To scratch my own itch when developing [BeatsBase.com](http://beatsbase.com "Free mix hosting for DJs") and [Wibsite.com](http://wibsite.com "The worlds most popular Wibsite"). Hopefully this plugin helps other developers too.

== Screenshots ==

1. The newsletter form

== Changelog ==

0.3.2 Compatibility with WP 3.0, several small bugfixes
0.3.1 Updated plugin URI
0.3 Added support link and donate button
0.2 Added from name and from email form fields
0.1 Initial version