<?php
/**
 * Plugin Name: OAuth 2.0 Single Sign On Client
 * Plugin URI: http://dash10.digital
 * Version: 4.1.0
 * Description: Creates the ability to login using Single Sign On from WP OAuth Server.
 * Author: Dash10 Digital
 * Author URI: https://dash10.digital
 * License: GPL2
 *
 * This program is GLP but; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of.
 */
/**
 * @todo Ensure only 1 load form login can happen. No page reload or refresh should be able to reauthenticate
 * @todo Add check for user already active on the current site. This will ensure there is no cross accounts. Maybe add an option to enable or disable this.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'WPSSO_FILE' ) ) {
	define( 'WPSSO_FILE', plugin_dir_path( __FILE__ ) );
}

// Require the main plugin class
require_once( WPSSO_FILE . 'class.single-sign-on-client.php' );

add_action( "wp_loaded", 'onelogin_register_files' );
function onelogin_register_files() {
	wp_register_style( 'wpsso_admin', plugins_url( '/assets/css/admin.css', __FILE__ ) );
	wp_register_script( 'wpsso_admin', plugins_url( '/assets/js/admin.js', __FILE__ ) );
}

add_action( 'admin_menu', array( new WPSSO_Client(), 'plugin_init' ) );
register_activation_hook( __FILE__, array( new WPSSO_Client, 'setup' ) );
register_activation_hook( __FILE__, array( new WPSSO_Client, 'upgrade' ) );