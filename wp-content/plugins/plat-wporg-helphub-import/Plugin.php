<?php

namespace Platform\Translate\WPOrgHelpHubImport;

use Platform\Translate\WPOrgHelpHubImport\Command\Worker;
use Platform\Translate\WPOrgHelpHubImport\Service\Article;
use \Platform\Translate\WPOrgHelpHubImport\Service\Translate;

defined( 'ABSPATH' ) || exit;

class Plugin {
	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		// 加载插件
		if ( class_exists( 'WP_CLI' ) ) {
			new Worker();
		}

		new Article();
		new Translate();
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
}
