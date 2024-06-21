<?php
/**
 * Plugin Name: Init
 * Description: 全局初始化
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

use Carbon\Carbon;
use JWTAuth\Auth;
use JWTAuth\Plugin_Updates;
use JWTAuth\Setup;
use Platform\Chinese_Format\Chinese_Format;

// Cavalcade
require __DIR__ . '/cavalcade/plugin.php';

// 关闭无必要的 PHP 错误报告
error_reporting( error_reporting() & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE );

/**
 * 载入日志服务
 */
require __DIR__ . '/logger/logger.php';

/**
 * 载入中文格式化服务
 */
require __DIR__ . '/chinese-format/chinese-format.php';

/**
 * 载入平台自定义的工具类
 */
require __DIR__ . '/tools/loader.php';

/**
 * 载入角色管理
 */
if ( get_current_blog_id() == SITE_ID_TRANSLATE ) {
	load_textdomain( 'rosetta', WP_CONTENT_DIR . '/languages/rosetta-zh_CN.mo' );
	require __DIR__ . '/rosetta-roles/rosetta-roles.php';
}

/**
 * 自动同步用户角色到所有站点
 */
add_action( 'init', 'schedule_multisite_user_sync' );
function schedule_multisite_user_sync() {
	if ( get_main_network_id() !== get_current_blog_id() ) {
		return;
	}
	if ( ! wp_next_scheduled( 'multisite_user_role_sync' ) ) {
		wp_schedule_event( strtotime( date( 'Y-m-d H:00:00' ) . ' +1 hour' ), 'hourly', 'multisite_user_role_sync' );
	}
}

add_action( 'multisite_user_role_sync', 'assign_role_to_all_users_in_all_sites' );
function assign_role_to_all_users_in_all_sites() {
	if ( ! is_multisite() ) {
		return;
	}

	$sites = get_sites();
	switch_to_blog( get_main_network_id() );
	$users = get_users( array( 'fields' => 'ids' ) );
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		foreach ( $users as $user_id ) {
			if ( ! is_user_member_of_blog( $user_id, $site->blog_id ) ) {
				add_user_to_blog( $site->blog_id, $user_id, 'subscriber' );
			}
		}

		restore_current_blog();
	}
}

/**
 * 如果用户在 URL 上拼接了 login_token 查询参数，则尝试解析 token 并使用其对应的用户来登录（如果已经登录其他用户则会切换为 token 对应的用户）
 */
add_action( 'wp_loaded', function () {
	if ( empty( $_GET['login_token'] ) ) {
		return;
	}

	$login_token                   = sanitize_text_field( $_GET['login_token'] );
	$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $login_token;

	// 解析 token
	$jwt   = new Auth();
	$token = $jwt->validate_token( false );

	$user_id = (int) $token?->data?->user?->id;
	if ( empty( $user_id ) ) {
		return;
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
} );

/**
 * 统一使用平台日志服务记录PHP错误
 */
$types = [
	1     => 'ERROR',
	2     => 'WARNING',
	4     => 'PARSE',
	8     => 'NOTICE',
	16    => 'CORE_ERROR',
	32    => 'CORE_WARNING',
	64    => 'COMPILE_ERROR',
	128   => 'COMPILE_WARNING',
	256   => 'USER_ERROR',
	512   => 'USER_WARNING',
	1024  => 'USER_NOTICE',
	2048  => 'STRICT',
	4096  => 'RECOVERABLE_ERROR',
	8192  => 'DEPRECATED',
	16384 => 'USER_DEPRECATED',
];
// 捕获全部异常
// TODO 暂时移除了异常捕获，因为太多了看不过来
/*$error_handler = set_error_handler( function ( $code, $message, $file, $line ): bool {
	global $types;

	Logger::error( Logger::GLOBAL, $message, array(
		'type'   => $types[ $code ] ?? $code,
		'file'   => $file,
		'line'   => $line,
		'server' => $_SERVER ?? [],
	) );

	return true;
} );

set_exception_handler( function ( $exception ) {
	global $types;

	Logger::error( Logger::GLOBAL, $exception->getMessage(), array(
		'type'   => $types[ $exception->getCode() ] ?? $exception->getCode(),
		'file'   => $exception->getFile(),
		'line'   => $exception->getLine(),
		'trace'  => $exception->getTraceAsString(),
		'server' => $_SERVER ?? [],
	) );
} );
register_shutdown_function(
	function () use ( $error_handler ) {
		global $types;

		$error = error_get_last();
		if ( ! $error ) {
			return;
		}
		Logger::warning( Logger::GLOBAL, $error['message'], array(
			'type'   => $types[ $error['type'] ] ?? $error['type'],
			'file'   => $error['file'],
			'line'   => $error['line'],
			'server' => $_SERVER ?? [],
		) );

		if ( $error_handler ) {
			restore_error_handler();
		}
	}
);*/

// 对 EP 同步的产品进行翻译以及调整
add_action( 'plugins_loaded', function () {
	add_filter( 'ep_prepare_meta_allowed_keys', 'plat_ep_prepare_meta_allowed_keys', 9999 );
} );

function plat_ep_prepare_meta_allowed_keys( $allowed ) {
	$allowed[] = '_thumbnail_id';
	$allowed[] = 'author';
	$allowed[] = 'author_username';
	$allowed[] = 'views';
	$allowed[] = 'version';
	$allowed[] = 'instruction';
	$allowed[] = 'faq';
	$allowed[] = 'changelog';
	$allowed[] = 'requires_wordpress_version';
	$allowed[] = 'tested_wordpress_version';
	$allowed[] = 'requires_php_version';
	$allowed[] = 'banner';
	$allowed[] = 'download_url';
	$allowed[] = 'rating';
	$allowed[] = 'num_ratings';
	$allowed[] = 'screenshots';

	return $allowed;
}

// 给 JWT 插件添加 EP 的白名单
add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$your_endpoints = array(
		'/wp-json/elasticpress/*',
	);

	return array_unique( array_merge( $endpoints, $your_endpoints ) );
} );


// 取消 EP 最大索引 10000 的限制
add_filter( 'ep_formatted_args', function ( $formatted_args ) {
	$formatted_args['track_total_hits'] = true;

	return $formatted_args;
} );

// 取消 EP 通知
add_filter( 'pre_option_ep_hide_different_server_type_notice', '__return_true' );
add_filter( 'pre_option_ep_hide_es_above_compat_notice', '__return_true' );

// 显示头像的简码
function show_user_wpavatar( $atts ) {
	global $current_user;
	wp_get_current_user();

	// Set default avatar size to 64 pixels
	$size = $atts['size'] ?? 64;

	// Set default CSS class to 'wpavatar'
	$class = isset( $atts['class'] ) ? 'wpavatar ' . $atts['class'] : 'wpavatar';

	// Set user ID as additional CSS class if provided
	if ( isset( $atts['user_id'] ) ) {
		$class   .= ' wpavatar-' . $atts['user_id'];
		$user_id = $atts['user_id'];
	} else {
		$user_id = $current_user->ID;
	}

	// Generate avatar HTML with size and class attributes
	return get_avatar( $user_id, $size, '', '', array( 'class' => $class ) );
}

add_shortcode( 'wpavatar', 'show_user_wpavatar' );

// 显示用户名的简码
function show_user_wpusername() {
	global $current_user;
	wp_get_current_user();

	return $current_user->display_name;
}

add_shortcode( 'wpavatar_username', 'show_user_wpusername' );

/**
 * 替换菜单栏上的各种变量为动态数据
 */
add_filter( 'wp_nav_menu_objects', function ( $menu_items ) {
	$data = array(
		'{wpavatar}'          => do_shortcode( '[wpavatar_username]' ),
		'{wpavatar_username}' => do_shortcode( '[wpavatar]' )
	);

	foreach ( $menu_items as $menu_item ) {
		foreach ( $data as $k => $v ) {
			if ( str_contains( $menu_item->title, $k ) ) {
				$menu_item->title = str_replace( $k, $v, $menu_item->title );
			}
		}
	}

	return $menu_items;
} );

// JWT 插件添加 Redis 的白名单
add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$your_endpoints = array(
		'/wp-json/objectcache/*',
	);

	return array_unique( array_merge( $endpoints, $your_endpoints ) );
} );

// JWT 插件添加区块可见性插件白名单
add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$your_endpoints = array(
		'/wp-json/block-visibility/*',
	);

	return array_unique( array_merge( $endpoints, $your_endpoints ) );
} );

// 评论使用富文本编辑器
add_filter( 'comment_form_defaults', 'comment_form_with_tinymice' );
function comment_form_with_tinymice( $args ) {
	ob_start();
	wp_editor( '', 'comment', array(
		'media_buttons' => true,
		'textarea_rows' => 10,
		'dfw'           => false,
		'tinymce'       => array(
			'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,bullist,numlist,code,blockquote,link,unlink,outdent,indent,|,undo,redo,fullscreen',
		)
	) );
	$args['comment_field'] = ob_get_clean();

	return $args;
}

// 禁用 ActionScheduler 异步运行，这傻逼玩意会导致 cavalcade 卡住
add_filter( 'action_scheduler_allow_async_request_runner', '__return_false' );
/*function asdds_disable_default_runner() {
	if ( class_exists( 'ActionScheduler' ) ) {
		remove_action( 'action_scheduler_run_queue', array( ActionScheduler::runner(), 'run' ) );
	}
}

// ActionScheduler_QueueRunner::init() is attached to 'init' with priority 1, so we need to run after that
add_action( 'init', 'asdds_disable_default_runner', 10 );*/


// 移除插件更新提示
/*function filter_plugin_updates( $value ) {
	if ( isset( $value ) && is_object( $value ) ) {
		unset( $value->response['gutenberg/gutenberg.php'] );
	}

	return $value;
}

add_filter( 'site_transient_update_plugins', 'filter_plugin_updates' );*/
