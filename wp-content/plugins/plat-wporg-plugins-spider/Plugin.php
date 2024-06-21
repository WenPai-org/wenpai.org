<?php

namespace Platform\Plugins\WPOrgSpider;

use Platform\Plugins\WPOrgSpider\Command\Worker;

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

		add_action( 'plat_job_wporg_plugins_update_task', [
			$this,
			'job_wporg_plugins_update_task'
		] );

		// 插件成功更新后执行的操作
		add_action( 'platform_wporg_plugins_updated', [ $this, 'plugins_updated' ] );

		// 插件删除后执行的操作
		add_action( 'platform_wporg_plugins_deleted', [ $this, 'plugins_deleted' ] );

		self::create_slug_update_check_task();
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

	/**
	 * 创建 Slug 更新检查任务
	 * 该函数由 Cron 每 30 分钟触发一次。
	 */
	public function create_slug_update_check_task(): void {
		if ( ! wp_next_scheduled( 'plat_job_wporg_plugins_update_task' ) ) {
			wp_schedule_event( strtotime( date( 'Y-m-d H:00:00' ) . ' +1 hour' ), 'hourly', 'plat_job_wporg_plugins_update_task' );
		}
	}

	/**
	 * 插件更新检查任务
	 */
	public function job_wporg_plugins_update_task(): void {
		$worker = new Worker();
		$worker->run();
	}

	/**
	 * 插件更新后执行的操作
	 */
	public function plugins_updated( $slug ): void {
		/**
		 * 安排翻译更新队列任务
		 */
		switch_to_blog( SITE_ID_TRANSLATE );

		$args = [
			'slug' => $slug,
			'type' => "plugins",
		];
		wp_schedule_single_event( time() + 1, 'gp_import_from_wporg', $args );

		restore_current_blog();
	}

	/**
	 * 插件删除后执行的操作
	 */
	public function plugins_deleted( $slug ): void {
		/**
		 * 删除翻译项目
		 */
		/*switch_to_blog( SITE_ID_TRANSLATE );

		$args = [
			'slug' => $slug,
			'type' => "plugins",
		];
		wp_schedule_single_event( time() + 1, 'gp_delete_project', $args );

		restore_current_blog();*/
	}
}
