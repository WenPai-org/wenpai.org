<?php
/**
 * Plugin Name: 文派客服自动回复
 * Plugin URI: https://wpchat.cn/autoreply
 * Description: 文派客服自动回复
 * Author: WenPai.org
 * Author URI: https://wenpai.org/
 * Text Domain: wpchat-autoreply
 * Domain Path: /languages
 * Version: 1.3
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'wpchat_autoreply_activate' );

function wpchat_autoreply_activate() {
	// Noting to do
}

// Load Plugin
add_action( 'init', 'wpchat_autoreply_init' );
function wpchat_autoreply_init() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/function.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/setting.php' );
	load_plugin_textdomain( 'wpchat-autoreply', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
