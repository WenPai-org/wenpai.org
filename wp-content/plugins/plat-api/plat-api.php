<?php
/**
 * Plugin Name: 对外 API
 * Description: 该 API 旨在替代 WordPress.Org 并对其扩充
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\API;

const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

// 加载插件
require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

// 注册插件激活钩子
register_activation_hook( PLUGIN_FILE, [ Plugin::class, 'activate' ] );
// 注册插件删除钩子
register_uninstall_hook( PLUGIN_FILE, [ Plugin::class, 'uninstall' ] );

// 给 JWT 插件添加白名单
add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$your_endpoints = array(
		'/wp-json/themes/*',
		'/wp-json/plugins/*',
		'/wp-json/core/*',
		'/wp-json/patterns/*',
		'/wp-json/translations/*',
	);

	return array_unique( array_merge( $endpoints, $your_endpoints ) );
} );

new Plugin();
