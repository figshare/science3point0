=== Group Documents ===
Contributors: Peter Anselmo
Tags: wpmu, buddypress, group, document, plugin, file, media, storage, upload, widget
Requires at least: WPMU 2.8, BuddyPress 1.1
Tested up to: 3.0-beta2/1.2.3
Stable tag: 0.3.5
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U5B9GFM2LGRXS

This allows members of BuddyPress groups to upload and store files and documents that are relevant to the group.

== Description ==

Group Documents creates a page within each BuddyPress group to upload and any type of file or document.  Documents can be edited and deleted either by the document owner or by the group administrator. Categories can be used to organize documents. Activity is logged in the main activity stream, and is also tied to the user and group activity streams.  The site administrator can set filters on file extensions, set display options, and upload files via FTP. Group members and moderators can receive email notifications at their option.  There are also "Recent Uploads" and "Popular Downloads" widgets than can be used to show activity at a glance.

PLEASE: If you have any issues or it doesn't work for you, ask a question on the BuddyPress forums or email me: peter@studio66design.com. Reporting bugs is the only way for them to be fixed.  It doesn't help anyone to mark "broken" without asking around.  Thanks!  

== Installation ==

Make sure Wordpress and BuddyPress are installed and active.

Copy the plugin folder buddypress-group-documents/ into /wp-content/plugins/

Browse to the plugin administration screen and activate the plugin.

There will now be a "Group Documents" menu item under the "BuddyPress" menu.  Here you will find a list of all file extensions allowed for uploaded files along with other settings.

Please don't hesitate to contact me, especially if you run into trouble.  I will respond promptly.  peter@studio66design.com

== Screenshots ==

1. Main view from the website
2. The Site Admin can filter uploads by extension
3. Ties into the site activity stream
4. Allows options for email notifications
5. Includes Recent Uploads Widget
6. Admin view of the Widget

==Changelog==

Aplogies for the frequent updates, this plugin is under active development!

= 0.3.5 =
- Added WP3.0-beta2 Compatibility

= 0.3.4 =
- Improved compatibility with several themes
- Added AJAX to category add/delete for group administrators

= 0.3.3 =
- Added categories! (turned off by deafult)
- Added option to restrict uploads to group moderators
- Added bp-custom option to designate and filter for "Featured documents"

= 0.3.2 =
- Added Customizable titles for widgets
- Added bp-custom option to filter widget display by group
- Added a silent workaround for the WP-Single relative file path bug.

= 0.3.1 =
- Added download count tracking!
- Added "Most Popular" widget
- Added sorting!
- Styled and AJAXed admin uploads
- Added German translation (Courtesy of Michael Berra)
- Code refactoring (filters)
- Fixed the documnet link url in Recent Uploads widget
- Fixed a Paging bug where you had to click twice

= 0.3.0 =
- Moved document storage out of plugin folder into uploads folder
- Updated activity stream for 1.2 to fix bugs and display the description
- Added Italian translation (Courtesy of Luca Camellini)
- Added icons! (Icons by Mark James: http://www.famfamfam.com/lab/icons/ )
- Added admin bulk upload folder (also good for large files)
- Added a few jQuery touches
- Fixed admin.js 'file not found' error
- Fixed a bug validating file names with multiple periods

= 0.2.5 =
- Removed email notifications error with 1.2
- Fixed a bug where icon was not showing on 1.1.X
- Fixed a bug with site admin menu permissions
- Added document folder location override ability
- Minor HTML validation tweeks

= 0.2.4 =
* Fixed a bug where errors were thrown on group deletion

= 0.2.3 =
* Added BuddyPress 1.2 Compatibility
* Added additional callbacks for extensibility
* Fixed bug where newlines were dropping from description

= 0.2.2 =
* Fixed bug where documents in private groups were visible to everyone
* Additional strings added for i18n
* Added French Translation (Courtesy of Daniel Halstenbach)

= 0.2.1 =
* Cleaned up some loose ends with i18n & translation

= 0.2 =
* Added paging for long lists of documents
* Added option for email notifications
* Added option for file size display
* Fixed a bug where unneccesary slashes were added to file name
* Significant refactoring of code

= 0.1.3 =
* Fixed a bug with the site admin menu in WPMU 2.9
* Reorganized several files & folders to reduce redundancy

= 0.1.2 =
* Fixed a bug where the period was dropping from the file extension

= 0.1.1 =
* Fixed a folder naming discrepancy betweeen 'bp-group-documents' and 'buddypress-group-documents'

= 0.1 =
* Initial Realease

== Notes ==

Roadmap.txt - contains ideas proposed and the (approximate) order of implementation

History.txt - contains all the changes since version .1

License.txt - contains the licensing details for this component.

== Feedback ==

Please email me with any bugs, improvements, or comments. I will respond promptly :-)

peter@studio66design.com.

== Donate ==

If this plugin saved you time, please help contribute to it's ongoing development and improvement.

Paypal Link:
https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U5B9GFM2LGRXS

You can also say "Thank You" via my Amazon wish list
https://www.amazon.com/wishlist/1787JDHX33AGW

