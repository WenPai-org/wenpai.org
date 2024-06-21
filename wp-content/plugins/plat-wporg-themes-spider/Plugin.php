<?php

namespace Platform\Themes\WPOrgSpider;

use Platform\Themes\WPOrgSpider\Command\Worker;

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

		// 目录 meta 区块过滤
		add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id, $object_type ) {
			$field_name = $attributes['fieldName'] ?? '';

			if ( 'updated_at' === $field_name ) {
				global $post;
				$time          = get_the_modified_date( 'Y-m-d H:i:s', $post );
				$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
			}
			if ( 'created_at' === $field_name ) {
				global $post;
				$time          = get_the_date( 'Y-m-d H:i:s', $post );
				$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
			}
			if ( 'screenshots' === $field_name ) {
				global $post;
				$image_ids     = get_post_meta( $post->ID, 'screenshots', true );
				$block_content = '';
				if ( ! empty( $image_ids ) ) {
					$block_content = '<div class="screenshots">';
					foreach ( $image_ids as $image_id ) {
						$block_content .= wp_get_attachment_image( $image_id, 'full' );
					}
					$block_content .= '</div>';
				}
			}

			return $block_content;
		}, 10, 5 );

		add_action( 'plat_job_wporg_themes_update_task', [
			$this,
			'job_wporg_themes_update_task'
		] );

		// 主题成功更新后执行的操作
		add_action( 'platform_wporg_themes_updated', [ $this, 'themes_updated' ] );

		// 主题删除后执行的操作
		add_action( 'platform_wporg_themes_deleted', [ $this, 'themes_deleted' ] );

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
		if ( ! wp_next_scheduled( 'plat_job_wporg_themes_update_task' ) ) {
			wp_schedule_event( strtotime( date( 'Y-m-d H:00:00' ) . ' +1 hour' ), 'hourly', 'plat_job_wporg_themes_update_task' );
		}
	}

	/**
	 * 主题更新检查任务
	 */
	public function job_wporg_themes_update_task(): void {
		$worker = new Worker();
		$worker->run();
	}

	/**
	 * 主题更新后执行的操作
	 */
	public function themes_updated( $slug ): void {
		/**
		 * 安排翻译更新队列任务
		 */
		switch_to_blog( SITE_ID_TRANSLATE );

		$args = [
			'slug' => $slug,
			'type' => "themes",
		];
		wp_schedule_single_event( time() + 1, 'gp_import_from_wporg', $args );

		restore_current_blog();
	}

	/**
	 * 主题删除后执行的操作
	 */
	public function themes_deleted( $slug ): void {
		/**
		 * 删除翻译项目
		 */
		/*switch_to_blog( SITE_ID_TRANSLATE );

		$args = [
			'slug' => $slug,
			'type' => "themes",
		];
		wp_schedule_single_event( time() + 1, 'gp_delete_project', $args );

		restore_current_blog();*/
	}
}
