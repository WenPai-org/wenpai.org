<?php
/**
 * Plugin Name: JWT Token 生成器
 * Description: 为平台开发的一个生成 JWT Token 的插件，需要配合 JWT Auth 插件使用
 * Author: 树新蜂
 * Version: 1.0.0
 * License: GPLv3 or later
 * Network: True
 * Requires at least: 4.9
 * Tested up to: 9.9.9
 * Requires PHP: 5.6.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Token;

defined( 'ABSPATH' ) || exit;


const VERSION     = '1.0.0';
const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

// 注册插件激活钩子
register_activation_hook( PLUGIN_FILE, [ Plugin::class, 'activate' ] );
// 注册插件删除钩子
register_uninstall_hook( PLUGIN_FILE, [ Plugin::class, 'uninstall' ] );

// 给 JWT 插件添加白名单
add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$your_endpoints = array(
		'/wp-json/token/*',
	);

	return array_unique( array_merge( $endpoints, $your_endpoints ) );
} );

/**
 * 为生成 Token 接口自动添加 nonce
 * TODO 这不是最优解决方案，待改进
 */
if ( str_contains( $_SERVER['REQUEST_URI'], '/wp-json/token/generate' ) ) {
	add_action( 'plugins_loaded', function () {
		$nonce                      = wp_create_nonce( 'wp_rest' );
		$_SERVER['HTTP_X_WP_NONCE'] = $nonce;
	} );
}

new Plugin();
