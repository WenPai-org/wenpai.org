<?php

namespace Platform\Token;

defined( 'ABSPATH' ) || exit;

use Platform\Token\Service\Base;

class Plugin {

	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		new Base();
	}

	/**
	 * 插件激活时执行
	 */
	public static function activate() {
	}

	/**
	 * 插件删除时执行
	 */
	public static function uninstall(): void {
	}
}
