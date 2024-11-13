=== bbp style pack ===
Contributors: robin-w
Tags: forum, bbpress, bbp, style
Donate link: http://www.rewweb.co.uk/donate
Tested up to: 6.6
Stable tag: 6.1.2
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

For bbPress - Lets you style bbPress, and add display features


== Description  ==
This Plugin lets you style bbPress, and add display features

You can change the forum styling for elements, letting you match (or contrast!) bbPress to your theme

Many features are available at the click of a button, such as creating vertical lists, adding create new topic links, hiding counts and much more.

<ul>
<li>Style font sizes colors etc. in forums and topics</li>
<li>Change forum display layouts</li>
<li>Add or take away forum elements, such as adding descriptions or removing 'this forum contains..'</li>
<li>Change the forum order</li>
<li>Change the freshness display to date and time, or combination date and freshness</li>
<li>Change the breadcrumbs to alter or remove elements, or remove breadcrumbs completely</li>
<li>Add Create new Topic, Subscribe and Profile buttons, making navigation easier</li>
<li>Add login Register and profile to menus</li>
<li>Change forum role names or add role images</li>
<li>Amend subscription email headings and text</li>
<li>Amend the topic list order</li>
<li>Add topic previews to make topic navigation easier</li>
<li>Change how the topic and reply forms display - adding, removing or changing elements</li>
<li>Amend how profiles display and configure who sees them</li>
<li>Amend the search styling</li>
<li>Use additional shortcodes to improve how you display your forums and topics</li>
<li>Add an unread posts section so that users can easily see new topics and replies</li>
<li>Add a quote button to topics and replies</li>
<li>Add moderation tools to allow to to control </li>
<li>Add an unread posts section so that users can easily see new topics and replies</li>
<li>Use additional widgets to better display latest activity, or forum and topic information</li>
<li>Find a list of other useful bbPress related plugins</li>
<li>Let bbpress work with FSE themes
</ul>


== Installation ==
To install this plugin :

1. Go to Dashboard>plugins>add new
2. Search for 'bbp style pack'
3. Click install
4. and then activate
6. go into settings and set up as required.

<strong>Settings</strong>


== Screenshots ==
1. a sample settings page


== Changelog ==

= 6.1.2 =
* The second of several technical releases which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.1.1 =
* The first of several technical releases which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.0.9/6.1.0 =
* Fixed a bug if trim revisions was not numeric in bsp_trim_revision_log
* Fixed a css bug for topic/reply display font size

= 6.0.8 =
* I've added the ability to change the 'from name' on subscription emails. See Subscription Emails tab item 1.

= 6.0.7 =
* I've added the ability to customise the 'create new reply' button with forum or topic name. See 'Topic/Reply Display' tab item 25.

= 6.0.6 =
* Following release of bbPress 2.6.11 a modification is needed for those using the [bsp-moderation-pending] shortcode.

= 6.0.5 =
* I've fixed a small bug in the 6.0.4 realted to the new topic fields functionality.

= 6.0.4 =
* I've added the ability to control what additional fields are shown/required for anonymous posting on the topic/reply forms -  See the new 'Topic/Reply form' tab.
* I've fixed a small bug in the 6.0.3 release

= 6.0.3 =
* I've added a new tab, which lets you add additional fields to the topic form, for instance you can ask for 'Make of car' on a car restoration forum -  See the new 'Topic Additional Fields' tab.
* If you post a new topic using the form at the bottom of the topics list and do not complete the required fields, you can be sent to the top of the form without your topic being posted, and with no visible reason. I've added a bug fix which displays these errors at the top of the topics list.  You can exclude this fix in settings>bug fixes if you wish, but if you find issues with it, I'd like to know, so please also post a support thread.


= 6.0.2 =
* I've added a new tab, which lets you decide which columns to show on the forum and topics index pages, and do this differently for mobile if you wish.  See the new 'Column Display' tab.


= 6.0.1 =
* I've added the abilty to bulk move topics between forums in dashboard>topics.  Use the bulk edit feature and you can set the forum against multiple topics 


= 5.9.9 =
* Some FSE theme users are seeing css header issues - so I've added a different bbpress template version which can be selected.  See Theme Support tab Page display options. 

= 5.9.8 =
* In the topic/reply order tab, I've added a further option in the order of replies to list the topic at the end.

= 5.9.7 =
* On the 'subscriptions emails' tab, I've added the ability to have the forum name in the title of the emails.

= 5.9.6 =
* I've fixed a small error relating to auto login


= 5.9.5 =
* We've improved auto login in the subscriptions email tab to redirect to the correct reply on login.
* We've improved how Style Pack and bbpress language translations work with other plugins such as loco-translate


= 5.9.4 =
* I've corrected the reply links pagination to work correctly when reverse reply order is selected in the topic order tab.


= 5.9.3 =
* I've improved the way the subscription management auto login works for sites who may work in multiple languages and have additional paths in the url.


= 5.9.2 =
* I've added the abilty to display dashicons instead of the topics/voices/posts text on forum and topic page titles - see the forums index and topics index tabs for details.


= 5.9.1 =
* If you are using moderation, when moderators receive an email and click the link, if they are not logged in they cannot immediately moderate.  I've now added the ability to set an automatic login - see the forums moderation settings for details in dashboard>settings>forums.


= 5.8.9/5.9.0 =
*  I've added a bug fix for bbpress if you are using akismet.  When Akismet detects spam, it does not correctly amend the latest activity.  This fixes that.  If you are using akismet, then go to Dashboard>settings>bbp style pack>bug fixes and select the option
*  Fixed an error in some php versions which threw a 'Undefined array key' error in subscriptions management
*  A technical change - fixed a deprectaed function reset() in functions.php
*  The [bbp-stats] shortcode was not showing the correct forum count if there were private forums, this now has a fix in 'bbPress bugs' tab.

= 5.8.8 =
* A bug fix for those using blocks - I've fixed an error seen if you also have WordPress script debug set.


= 5.8.7 =
* A technical change - I've added some further code to make some links in the backend work correctly if you are using a sub-directory by ensuring that the correct site_url function is used.
* A further technical change to remove a deprecated function (FILTER_SANITIZE_STRING) and replace it with FILTER_UNSAFE_RAW
 

= 5.8.6 =
* A technical chnage - I've added a filter to allow the tinymce editor to have attributes.

= 5.8.3/5.8.4/5.8.5 =
* I've added a widget 'list forums' that lists the forums and number of posts, similar to the one on the bbpress support site.


= 5.8.2 =
* I've added the ability for the topic title in a forum page to take you to the latest reply, and if 'unread posts' is active, the latest unread post.  See 'Topics Index Styling' tab item 21.


= 5.8.1 =
* I've made further improvements on how subscriptions management works, allowing subscriptions to be switched off for roles.

= 5.7.9/5.8.0 =
* An improvement for those using the subscription emails and WPML - these have to modified to allow WPML to translate as required.
* A fix for those using the more/less content function as well as the open in new window, as this caused an error.

= 5.7.8 =
* If you go into dashboard>topics and edit a topic, and then click update - only one subscription is saved. I've added a fix to ensure that all are saved. This is by default included, but you can exclude this fix in the 'bug fixes' tab.
			

= 5.7.7 =
* A bit of a technical change - I've added the ability to amend the dependancy on which the bspstyle.css loads - by default it waits until bbp-default loaded.  This can now be filtered using the filter 'bsp_enqueue_css_dependancy'.


= 5.7.6 =
* If you are using buddypress version 12, then you should add the 'BP Classic' plugin which gives compatibility between bbpress and the new Buddypress version.  I've added a recommendation for this into the Buddypress tab that Buddypress users see in the style pack settings. 
* I added a fix whereby if you are still using bbpress 2.6.6 a file I added in a recent update does not cause an error.

= 5.7.5 =
* I've added a bbpress bug fix for those that have converted from other forums to bbpress, but then may get an error 'Uncaught TypeError: register_shutdown_function' - see settings>bug fixes to exclude

= 5.7.3/5.7.4 =
* I've added theme support for the 'Hello Elementor' theme.
* I've added the ability for Buddyboss users to use the 'Quotes' function.


= 5.7.2 =
* I've added the ability to have the full editor on topic/reply forms - see topic/reply form tab item 9.
* I've fixed an issue with user profiles if also using buddypress and viweing multiple paged user topics or replies.

= 5.7.1 =
* If you have forums or categories with ONLY PRIVATE sub forums, then sub forums will not display on the forums list.  This is fixed in this version, with an option in settings>bug fixes to exclude.
* Fixed a deprecated function (FILTER_SANITIZE_STRING) in subscriptions management
* Fixed a strpos null error in subscriptions management
* Fixed a deprecated notice for PHP 8.2 for Dynamic Properties that might display in the backend
* further improvements to the bbpress template used in FSE themes

= 5.7.0 =
* Improvement to the bbpress template used in FSE themes to put the head section in the correct place
* Subscriptions Management: Fix for Sub forums that were not always displaying correctly in user subscriptions.

= 5.6.9 =
* "Doing It Wrong" Fix: The Plugin Information page had a single line of code displaying a WordPress "Doing it wrong" message. It was related to multisite settings for getting member count. The code has been revised to follow WordPress best practices.
* Bug Fix & Enhancement: The CSS Location tab settings were not being applied to some CSS/JS files properly, resulting in 404 not found errors in the admin panel. This bug has been fixed and new options added for setting a custom JS file location as well. The tab has also been renamed to CSS/JS Location to reflect the new changes. Previously, only specific files used the location specific in the CSS Location tab. Now, all Style Pack CSS/JS files honor the values set in the CSS/JS Location tab.
* WordPress Compatibility: Style Pack has been tested against WordPress 6.4 to ensure compatibility.

= 5.6.8 = 
* Security fix for display-top-users shortcode potential XSS vulnerability as reported by "NGÔ THIÊN AN"

= 5.6.7 = 
* Technical Bug Fix: fix deprecation notice for unread tab with php 8.2.
* Improve the unread icon link in topics to take you to the last read reply, rather than the end of the thread.

= 5.6.6 = 
* Technical Bug Fix: With certain settings the 'bsp_topic_subscribe_filter' had an error with php 8.1.

= 5.6.5 = 
* Bug Fix: PHP empty array error when no settings saved for Subscription Emails settings.

= 5.6.4 =
* Bug Fix: Version 5.6.3 made changes to how BuddyPress profile views were handled. In some cases, it could lead to "out of memory" errors. BuddyPress profile checks have been re-worked to be as efficient as possible. Additionally, all BuddyPress-specific functions have been setup to only run on BuddyPress pages to speedup page loads across the entire site.
* New Features/Tab: BuddyPress settings tab added for additional visibility and redirection control over BuddyPress-specific sections (global groups, global activity, global members). If you have BuddyPress activated, you'll automatically see the BuddyPress settings tab in Style Pack.
* New Feature: Subscription Emails now have an option for selecting which roles should receive subscription emails. By default bbPress sends them to all roles. There is now a new option #3 on the "Subscription Emails" settings tab. The new default for this setting is to send emails to Keymaster, Senior Moderator, Moderator, and Participant roles only. Roles are dynamically obtained, so any custom bbPress roles you have registered can also be configured for subscription emails.
* Minor Improvement: "Plugin Information" tab now includes member count, forum count, topic count and reply count as part of the reported data to improve future troubleshooting.

= 5.6.3 =
* Updated compatibility with WordPress 6.3.
* Minor Improvement: Better handling of BuddyPress pages when setting up profile visibility in the "Profiles" settings tab. Profile visibility settings now only affect actual profile visibility, not BuddyPress core pages such as groups.
* Minor Improvement: Shortcodes overahul. This includes a full revamp of the "Shortcodes" settings tab, and better handling of optional values to prevent some issues users were experiencing.
* Arabic machine translation added.

= 5.6.2 =
* Bug Fix: Plugin conflict with miniOrange plugins has been patched. "Moderation" and "Subscription Management" tabs now save settings properly when any plugin from miniOrange is active.
* Bug Fix: Theme Support "forum width" is now properly applied to Full-Site Editor block themes. Other recent Theme Support changes have been applied to the "Reset Settings" and "Plugin Information" tabs and will be displayed accordingly based on if your active theme has specific support options or not.
* Bug Fix: "form5" and "form6" was showing on the frontend in some template files. They have been removed from frontend templates.
* Minor Improvement: The "Reset Settings" tab now has a Select/Unselect All option to save you from having to manually click 30+ checkboxes in the event that you wanted to do a full plugin reset.
* Minor Improvement: Additional help info added for using a custom class with topic favorite/subscribe links in the "Topic/Reply Display" tab. It offers suggestions for targeted selectors to make sure your custom CSS code is applied properly to the favorite/subscribe links without being overwritten by default bbPress styling. 

= 5.6.1 =
* Major Improvement: Better cache handling to ensure regenerated CSS/JS files and settings changes are applied on the frontend of the site consistently. We now automatically clear caches for most plugins and hosting providers including: AutOptimize, WP Super Cache, W3 Total Cache, WP Fastest Cache, WP Rocket, WP Optimize, LiteSpeed Cache, Hyper Cache, Cachify, Comet Cache, SG Optimizer, Pantheon, Zen Cache, Cache Enabler, Breeze, Swift Performance, GoDaddy, WP Engine, Kinsta, Pagely, Pressidum, and Savvii.
* Minor Improvement: Small changes to the plugin activation/upgrade functions to prevent possible PHP warning messages for empty arrays and to apply the new cache handling improvements.
* Minor Improvement: Small adjustment to applying custom classes to Topic Buttons to account for in-page AJAX updates.

= 5.6.0 =
* Bug Fix: Topic tags were being stripped when "Limit Topic Tags to a list" enabled. Pre-approved topic tags are now added to topics properly.
* New Requested Feature: Ability to style topic "Favorite" & "Subscribe" links as default bbPress links, stylized buttons, or with a custom CSS class. This has been added to the "Topic/Reply Display" tab as #26.
* CSS Improvement: When topic "Favorite" & "Subscribe" links are styled with the "Forum Button" style, the top few pixels were cut off from the buttons. We added a CSS code fix for this that is automatically applied when topic "Favorite" & "Subscribe" links are selected to be styled as buttons.
* Setting Location Change: The setting for the topic "Favorite" & "Subscribe" link separator has been moved from the "Forum Buttons" tab to the "Topic/Reply Display" tab as #27. It made sense to do this for consistency reasons and to keep related settings near each other. If you previously had any values set for this, don't worry, they'll automatically be migrated over to the new settings fields.

= 5.5.9 =
* A small correction to re-enable the reset keymaster role if all keymasters are deleted.

= 5.5.8 =
* We've added a new option to disable nested quotes (quotes within quotes). Find it as #11 in the "Quotes" tab.
* We've added some theme support for the Kadence theme, which allows this plugin to correctly apply styling settings.  If you are using Kadence you'll see a 'Theme Support' tab with details.

= 5.5.7 =
* Quote link visibility changed to follow anonymous/guest posting site settings for logged-out users. Now hidden for guests unless guest posting allowed within bbPress settings.

= 5.5.6 =
* Security fix for Subscription Management potential XSS vulnerability as reported by "thiennv"
* Additional sanitization added to all relevant input fields/values for increased security
* Italian and Vietnamese machine translations added

= 5.5.5 =
* Minor fix for array offset PHP warning message regarding order of replies
* .pot file regenerated, French/Russian/Japanese .po/.mo files re-sync'd, and empty strings auto-translated 

= 5.5.4 =
* We've added the ability to reverse the order of replies, so that a topic displays newest replies first.

= 5.5.3 =
* A fix for forum/topic "freshness" to use default date/time formats when "custom formatting" is selected but no custom formatting value is specified
* Improved prevention of accidental data leakage for all plugin admin files

= 5.5.2 =
* A fix for those using the Astra theme - certain bbpress screens loop endlessly.  If you are using Astra 4.x, then a theme support tab will appear in Dashboard>settings>bbp Style Pack to allow you to fix this issue.

= 5.5.0 / 5.5.1 =
* We have added Block Widget versions of bbPress and bbp Style Pack widgets. These can be used instead of the legacy widgets in themes.  In FSE themes,  legacy widgets cannot be used so these versions will allow FSE theme users to have widgets.  There is a new tab called "Block Widgets" which explains how to set these.  Whilst they follow the WordPress rules for how to set up blocks, in testing we found it not very intuitive, hence the need for some explanation in the new tab !
* We have re-written the way that bbPress displays with FSE themes.  If you are using a FSE theme, then in dashboard>settings>bbp style pack you will see a "Theme Support" tab with settings and detailed instructions on how to use the new bbpress templates.
* For those using the DIVI theme, we have added some support instructions as this theme does not display profiles and search well.  Divi have ackowldeged this as an issue, but have not as yet done a correction.  You therefore need to change some settings in the DIVI theme to get these to display properly.  If you are using the DIVI theme, then a "Theme Support" tab will show,  which explains what to do.
* We've added a new Setting: Ability to show/hide user profile links within quotes for everyone/logged-in/no one. The setting has been added to "Quotes" tab > "10. Quoted User Profile Links"
* We also added another new Setting: Ability to change/remove the " | " prefix from the Topic Subscribe button. The setting has been added to "Forum Buttons" tab > "2. Activate Subscribe Button" > "Topic Subscribe Prefix"

= 5.4.7 =
* "Plugin Information" tab overhaul: multisite-friendly, enhanced information, improved styling, and includes all current tabs/option groups
* ClipboardJS version updated and many additional copy options added to "Plugin Information" tab
* "Back-to-top" scroller button added to admin settings page

= 5.4.6 =
* Fixes missing files that were not included in the 5.4.5 trunk release 

= 5.4.5 =
* Corrections to include all current tabs & option groups within plugin settings import/export/reset operations

= 5.4.4 =
* Complete overhaul of the readme.txt changelog. Re-written using WP plugin directory standards
* "What's New?" tab is now auto-generated from the readme.txt changelog with improved styling

= 5.4.3 =
* Correction for background color not showing on topic author in forum roles

= 5.4.2 =
* Minor performance enhancement, which also fixes bug where topic rules overwrite reply rules on first save
* Changes to login settings tab to fix "undefined array key" PHP warning, handle cases where no primary menu exists, and correct alignment of selectable menus
* Enhanced admin plugin page links 

= 5.4.1 =
* Added theme support for Astra version 4.0.2, which has a bug whereby profiles and bbPress search do not work properly - a theme support tab will appear if you are using this theme

= 5.4.0 =
* Added the ability to style the topic/reply submit button to match those in the 'Forum Buttons' tab - see 'Topic/Reply Form' tab item 5

= 5.3.9 =
* Minor code improvements for latest activity widget and creation of style.css code

= 5.3.8 =
* A fix for the failed login process, which had stopped working following a WordPress upgrade

= 5.3.7 =
* Change for better detection of FSE themes

= 5.3.6 =
* A bug fix for menu profile items

= 5.3.5 =
* A bug fix for button classes when no value is set

= 5.3.4 =
* A bug fix for subscriptions button when BuddyPress also active

= 5.3.3 =
* A bug fix for new user error if subscriptions management active

= 5.3.2 =
* A minor bug fix for topic counts to prevent a repeated line 

= 5.3.1 =
* Bug fixes for moderation and single forum widgets

= 5.3.0 = 
* Refinement of handling file generations and cache management on plugin updates for both single site and multisite installs

= 5.2.9 =
* Further fix for file generations on plugin updates

= 5.2.8 =
* Bug fix for file generations on plugin updates 
* Bug fix for PHP empty array index warnings
* Better handling of custom CSS locations

= 5.2.7 =
* Bug fixes for widgets and unread icon alignment

= 5.2.6 =
* Multisite compatibility!
* Performance enhancements
* Topic Count tab/plugin enhancements
* Minor bug fixes
* Regenerated & re-sync'd language POT, PO & MO files 

= 5.2.5 =
* Regenerated POT to fix missing items 

= 5.2.4 =
* Technical correction to 5.2.3

= 5.2.3 =
* Improved actions for modtools shortcode 

= 5.2.2 =
* Added a moderations shortcode to display pending topics and replies
* Extended translation to include style pack and modtools 

= 5.2.1 =
* In modtools bbpress.php amended to allow filter for ban/flag and confirm
* report.php changed to fix issues reported by codejp3

= 5.2.0 =
* Renamed Sundry tab to Dashboard Admin and added sorts and links

= 5.1.9 =
* Added author filter to the forum
* Topic and reply admin screens

= 5.1.8 =
* Added topic/reply counts ability to the users admin and new sundry tab

= 5.1.7 =
* Added topic/reply count x.xk option

= 5.1.6 =
* Added topic/reply count

= 5.1.5 =
* Added reply button to Topic/Reply Display tab

= 5.1.4 =
* Added reply extracts to Topic/Reply Display tab

= 5.1.3 =
* Added subscription management

= 5.1.2 =
* Make jQuery a dependency in generate_css.php

= 5.1.1 =
* Revert to 5.0.9

= 5.0.9 =
* Revised support for FSE themes 

= 5.0.8 =
* Added fix for hidden forums showing in search 

= 5.0.7 =
* Updated POT file 

= 5.0.6 =
* Revised 2022 theme support for twenty twenty two child themes 

= 5.0.5 =
* Revised 2022 theme support 
* Fixed default display order when only 2 buttons 

= 5.0.4 =
* Added the ability to amend the order of elements in the quotes section 

= 5.0.3 =
* 2022 theme support added 

= 5.0.2 =
* Whitespace correction to settings>moderation 

= 5.0.1 =
* Whitespace correction to settings>moderation 

= 5.0.0 =
* Moderation tools added 

