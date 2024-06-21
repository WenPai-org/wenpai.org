<?php

namespace Platform\Translate\WPOrgTranslateImport;

use GP;
use Platform\Translate\WPOrgTranslateImport\Command\Release;
use Platform\Translate\WPOrgTranslateImport\Command\Worker;
use Platform\Translate\WPOrgTranslateImport\Service\Project;
use Platform\Translate\WPOrgTranslateImport\Web\Import;

defined( 'ABSPATH' ) || exit;

class Plugin {
	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		// 加载插件
		if ( class_exists( 'WP_CLI' ) ) {
			new Worker();
			new Release();
		}

		new Project();
		new Import();
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
