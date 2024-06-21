<?php
/**
 * Plugin name: GlotPress Rosetta Roles
 * Description: Ties roles on Rosetta sites directly into translate.wenpai.org.
 * Version:     2.0
 * Author:      树新蜂
 * License:     GPLv2 or later
 */

namespace WordPressdotorg\GlotPress\Rosetta_Roles;

use WordPressdotorg\Autoload;

// Store the root plugin file for usage with functions which use the plugin basename.
define( __NAMESPACE__ . '\PLUGIN_FILE', __FILE__ );

if ( ! class_exists( '\WordPressdotorg\Autoload\Autoloader', false ) ) {
	include __DIR__ . '/vendor/wordpressdotorg/autoload/class-autoloader.php';
}

// Register an Autoloader for all files.
Autoload\register_class_path( __NAMESPACE__, __DIR__ . '/inc' );

// Instantiate the Plugin.
Plugin::get_instance();
