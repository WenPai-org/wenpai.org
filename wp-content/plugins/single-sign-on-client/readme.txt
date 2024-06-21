=== Simple Single Sign On ===
Contributors: justingreerbbi
Donate link: http://wp-oauth.com
Tags: OAuth 2, SSO, Single Sign On, OAuth 2.0, WordPress SSO, Single Sign On, Client, OAuth client
Requires at least: 5.4
Requires PHP: 5.6
Tested up to: 5.8
Stable tag: 4.1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable SSO (single sign on) on your WordPress website with OAuth 2.0.

== Description ==

This plugin is designed and developed for use with WP OAuth Server (https://wordpress.org/plugins/oauth2-provider/).
Once Single Sign On Client is installed, it will add Single Sign On abilities to used another WordPress sites users.

[youtube https://youtu.be/2JBr9uxO2Yc]

= Use Case =

Site A is your main WordPress site but you need to launch another WordPress website or service (Site B).
Instead of having all your users create a new account on the new website, you can simply use Sign Sign on from Site A.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/single-sign-on-client` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Single Sign On screen to configure the client

== Frequently Asked Questions ==

= Does this plugin work with other OAuth 2.0 Servers? =

Although it has been designed solely for <a href="https://wordpress.org/plugins/oauth2-provider/">WP OAuth Server</a>, you may still be able to use this plugin for other OAuth 2.0 servers.

= I get a Single Sign On Error =

In certain cases the user will be presented with a message "Single Sign On Error". This is because the use either has a
username already and the emails do not match or visa versa. The solution is to completely remove the associated user from
the client site.

== Changelog ==

= 4.1.0 =
* Modified for use with the latest WordPress and Beta Plugin
* Codebase update for PHP 8
* Fixed warnings about unused tags

= 4.0.0 =
* ADDED: Login Only Restriction. Prevents registration of new users during SSO.
* FIXED: Dashboard redirect issue
* Refactored codebase for future development
* Tested with the latest WordPress and Beta Plugin

= 2.0.0 =
* Refactoring of core plugin functions

= 1.4.6 =
* Added new logic to capture possible mismatch username by using email as a fallback user lookup.

= 1.4.5 =
* Added custom redirect_uri to trigger callback to allow for custom redirect.
* Added First Name and Last Name user Mapping
* Removed default user from allowable SSO. This user should already have access.

= 1.4.2 =

* Added auto SSO functionality for all pages.

= 1.4.1 =

* Added Error Handling to auth code and token request.

= 1.4.0 =

* Updated Admin UI Options
* Updated for WordPress 5.1

= 1.3.2 =

* Added wpoc_user_created action. Triggered when a new user is created via Single Sign On.
* Added wpoc_user_login action. Triggered when a user is logged into the system via Single Sign On.

= 1.3.1 =

* FIX: Missing function call to use new filter added in version 1.3.0

= 1.3.0 =

* Added "wpssoc_user_redirect_url" filter to allow for custom user redirects after successful login.

= 1.0.0 =

* Init repo push