<?php

namespace Platform\API;

use Platform\API\API\Base;

defined( 'ABSPATH' ) || exit;

class Plugin {
	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * 插件激活时执行
	 */
	public static function activate() {
	}

	/**
	 * 插件删除时执行
	 */
	public static function uninstall() {
	}

	public function plugins_loaded(): void {
		// 将 REST API 根路径改到 /
		if ( ! is_admin() ) {
			add_action( 'init', function () {
				//flush_rewrite_rules( true );
				add_rewrite_rule( '^(.*)?', 'index.php?rest_route=/$matches[1]', 'top' );
			} );
		}

		add_action( 'rest_api_init', array( Base::class, 'init' ) );
	}
}

