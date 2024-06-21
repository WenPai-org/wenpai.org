=== Simple Tabs Block ===
Contributors: cloudcatch, dkjensen
Tags: tabs, tab, block
Requires at least: 6.4
Tested up to: 6.5
Requires PHP: 7.0
Stable tag: 2.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create accessible tabbed layouts

== Description ==

Simple to use block to create tabs within the block editor. Each tab supports the capability to use any type of block within the content to create robust layouts.

This plugin introduces minimal styling and therefore likely will require custom styling in order to achieve the desired look and feel for your tabs.

Supports horizontal and vertical tabs.

== Features ==
* Tabs customizable using the native block editor style controls
* Fully accessible including keyboard controls
* Supports horizontal and vertical tabs
* Set default open tab

== Changelog ==

= 2.1.0 =
* Fix: Undefined property warning
* Feat: Add additional block supports
* WP 6.5 compatibility

= 2.0.1 =
* WP 6.4 compatibility

= 2.0.0 =

* **BREAKING CHANGE**: Refactor tabs block for updated block controls
* Feat: Add CSS classes to active tabs
* Feat: Allow block styling through the native block editor styles panel

= 1.3.0 =

* Feat: Update block styles
* Docs: Compatibility with 6.1

= 1.2.1 =

* Fix: Various block fixes

= 1.2.0 =

* Feat: Trigger event on window when tab is changed

= 1.1.1 =

* Fix: `label` undefined error

= 1.1.0 =

* Fix: Accessibility features
* Fix: Allow CSS classes on tabs
* Feat: Added tab description field

= 1.0.0 =

* Initial release

== Upgrade Notice ==

= 2.0.0 =
**BREAKING CHANGE**: Upgrading from version 1.x to 2.x could break any custom CSS applied to the tabs. It is highly recommended to test the upgrade on a staging environment before upgrading on a production site.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-tabs-block` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Screenshots ==

1. Block editor
2. Tab styles panel
3. Frontend view
