<?php

namespace Platform\Translate\GeneratePack;

use Platform\Translate\GeneratePack\Service\Pack;
use Platform\Translate\GeneratePack\Command\Worker;

defined( 'ABSPATH' ) || exit;

class Plugin {
	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		if ( class_exists( 'WP_CLI' ) ) {
			new Worker();
		}

		add_action( 'plat_generate_all_language_pack', array( $this, 'generate_all_language_pack' ) );

		if ( ! wp_next_scheduled( 'plat_generate_all_language_pack' ) ) {
			wp_schedule_event( strtotime(date('Y-m-d H:00:00') . ' +1 hour'), 'hourly', 'plat_generate_all_language_pack' );
		}
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

	public function generate_all_language_pack(): void {
		$generate_pack = new Pack();
		$generate_pack->generate_all_pack();
	}
}

