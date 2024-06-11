=== Wbcom Designs - BuddyPress Member Export Import ===
Contributors: vapvarun,wbcomdesigns
Donate link: www.wbcomdesigns.com
Tags: export, import, users, members, buddypress, x-profile, user-data, avatar.
Requires at least: 3.0.1
Tested up to: 6.0.1
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Buddypress Member Export Import bring you feature to export Buddypress members and x-profile fields data into CSV file and import buddypress members from CSV file.

== Description ==
Buddypress Member Export Import bring you feature to export Buddypress members and x-profile fields data with their avatar and buddyress group's name ( in which member's join and created ) into CSV file. You can export members with their xprofile fields data or only member's default data and import buddypress members from CSV file.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `bp-xprofile-export-import.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to admin menu and click on Member Data tab. You can see there is two sub pages Members Export and Members Import.

== Frequently Asked Questions ==

= Where do I get support?
We request you to use [Wbcom Designs](https://wbcomdesigns.com/contact/) for all the support questions. We love helping. Using Wbcom Designs allows us to assist you better and quicker.

= How can we export member's data into CSV file? =
Go to Admin menu and click  "/Members Data->Members Export" page. Select members from "Select Members" drop down box and then select x-profile fields group's name for member's x-profile fields data. And then select x-profile fields and click export button.

= How can we import member's data from CSV file? =
Go to Admin menu and click  "/Members Data->Members Import" page. Upload CSV file from ( Upload CSV File ) button. After this you have to map the CSV column to x-profile fields and click import button.

== Screenshots ==

1. Plugin activation.
2. Admin menu page.
3. Members export page.
4. Notice when exporting.
5. Members import page.
6. Mapping x-profile fields with CSV column.
7. Notice when importing.

== Changelog ==
= 1.5.0 =
* Fixed: updated backend UI

= 1.4.0 =
* Fixed: (#14) Fixed fatal error displaying when extended profile bp component
* Fixed: phpcs issues
* Fixed: plugin installation issue
* Fixed: export csv issue

= 1.2.0 =
* Fixed: (#8) Fixed Exporting file issue

= 1.1.0 =
* Fixed: Added class check intsted of check loader
* Fixed: Update dependent plugin notice

= 1.0.3 =
* Fixed: Escaping function.

= 1.0.2 =
* Compatibilty with WordPress version 5.5.1 and BuddyPress version 6.3.0.
* Fixed: Escaping function.

= 1.0.1 =
* Enhancement - Added support for user meta fields mapping.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.
