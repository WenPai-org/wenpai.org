<?php
/*
Plugin Name: Mistape
Description: Mistape allows visitors to effortlessly notify site staff about found spelling errors.
Version: 1.4.0
Author URI: https://wenpai.org
Author: WenPai.org
License: MIT License
License URI: http://opensource.org/licenses/MIT
Text Domain: mistape
Domain Path: /languages
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MISTAPE__VERSION', '1.4.0' );
define( 'MISTAPE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MISTAPE__PLUGIN_FILE', __FILE__ );
define( 'MISTAPE__PLUGIN_FOLDER', basename( MISTAPE__PLUGIN_DIR ) );
define( 'MISTAPE__PLUGIN_URL', WP_PLUGIN_URL . '/' . MISTAPE__PLUGIN_FOLDER );

require_once( MISTAPE__PLUGIN_DIR . 'src/class-deco-mistape-abstract.php' );
require_once( MISTAPE__PLUGIN_DIR . 'src/class-deco-mistape-admin.php' );
require_once( MISTAPE__PLUGIN_DIR . 'src/class-deco-mistape-ajax.php' );
require_once( MISTAPE__PLUGIN_DIR . 'src/class-deco-mistape-table.php' );

register_activation_hook( __FILE__, 'Deco_Mistape_Admin::activation' );
register_deactivation_hook( __FILE__, 'Deco_Mistape_Admin::deactivate_addons' );

add_action( 'plugins_loaded', 'deco_mistape_init' );
add_action( 'mistape_init_addons', 'Deco_Mistape_Table_Addon::init', 10 );

function deco_mistape_init() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		// load ajax-related class
		Deco_Mistape_Ajax::maybe_instantiate();
	} elseif ( is_admin() ) {
		// conditionally load admin-related class
		Deco_Mistape_Admin::get_instance();
	} else {
		// or frontend class
		require_once( MISTAPE__PLUGIN_DIR . 'src/class-deco-mistape-front.php' );
		Deco_Mistape::get_instance();
	}
}


