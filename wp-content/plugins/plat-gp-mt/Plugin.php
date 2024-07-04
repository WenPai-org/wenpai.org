<?php

namespace Platform\Translate\MachineTranslate;

use GP;
use GP_Route;
use Platform\Translate\MachineTranslate\Service\Translate;

defined( 'ABSPATH' ) || exit;

class Plugin extends GP_Route {
	/**
	 * 创建一个插件实例
	 */
	public function __construct() {
		parent::__construct();
		// 加载插件
		if ( class_exists( 'WP_CLI' ) ) {
			new Command\Translate();
		}
		$t = new Translate();

		GP::$router->add( '/cloud-translate', array( $t, 'api' ), 'post' );
		GP::$router->add( "/gp-mt/(.+?)", array( $this, 'add_web_translate_job' ), 'get' );
		GP::$router->add( "/gp-mt/(.+?)", array( $this, 'add_web_translate_job' ), 'post' );
		GP::$router->add( "/gp-mt-bulk/(.+?)", array( $this, 'bulk_add_web_translate_job' ), 'get' );

		add_action( 'plat_schedule_gp_mt', array( $t, 'web' ), 999, 2 );

		// 为 GlotPress 的翻译组件添加 `AI 翻译` 按钮
		// 2024.3.10 已经被 AI 建议替代，所以不再需要
		/*add_filter( 'gp_entry_actions', function ( $actions ) {
			$actions[0] = str_replace( '<div class="button-group entry-actions">', '', $actions[0] );
			$actions[0] = '<div class="button-group entry-actions"> <button class="auto-translate" title="AI 翻译"><i class="fad fa-robot"></i> AI 翻译</button> ' . $actions[0];

			return $actions;
		} );*/

		// 载入静态文件
		// 2024.3.10 已经被 AI 建议替代，所以不再需要
		/*add_action( 'wp_enqueue_scripts', function () {
			wp_enqueue_script( 'plat-mt', plugin_dir_url( PLUGIN_FILE ) . 'assets/js/mt.js', array( 'jquery' ), '1.0.0', true );
		} );*/
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
	 * 创建网页端翻译任务
	 */
	public function add_web_translate_job( $project_id ) {
		if ( ! is_user_logged_in() ) {
			$route            = new GP_Route();
			$route->notices[] = '请先登录';
			$route->redirect( $_SERVER['HTTP_REFERER'] );

			return;
		}

		// 检查是否有权限
		if ( ! current_user_can( 'manage_options' ) ) {
			$route            = new GP_Route();
			$route->notices[] = '你没有权限执行该操作';
			$route->redirect( $_SERVER['HTTP_REFERER'] );

			return;
		}

		// 检查任务锁
		$check = get_transient( 'plat_gp_mt_' . $project_id );

		if ( $check ) {
			$route            = new GP_Route();
			$route->notices[] = '同一项目一小时内只能执行一次 AI 翻译任务！';
			$route->redirect( $_SERVER['HTTP_REFERER'] ?? '/' );

			return;
		}

		// 设置任务锁
		set_transient( 'plat_gp_mt_' . $project_id, true, 60 * 60 );

		$project = $this->add_translate_job( $project_id );
		$referer = gp_url_project( $project['path'] );
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referer = $_SERVER['HTTP_REFERER'];
		}

		$route            = new GP_Route();
		$route->notices[] = '该请求已加入队列，请稍后刷新页面';
		$route->redirect( $referer );
	}

	/**
	 * 批量创建网页端翻译任务
	 * $project_id 项目 ID（xxx-xxx格式）
	 */
	public function bulk_add_web_translate_job( $project_id ) {
		if ( ! is_user_logged_in() ) {
			$route            = new GP_Route();
			$route->notices[] = '请先登录';
			$route->redirect( $_SERVER['HTTP_REFERER'] );

			return;
		}

		// 检查是否有权限
		if ( ! current_user_can( 'manage_options' ) ) {
			$route            = new GP_Route();
			$route->notices[] = '你没有权限执行该操作';
			$route->redirect( $_SERVER['HTTP_REFERER'] );

			return;
		}

		// 切割项目 ID
		$project_ids = explode( '-', $project_id );
		for ( $id = $project_ids[0]; $id <= $project_ids[1]; $id ++ ) {
			// 检查任务锁
			$check = get_transient( 'plat_gp_mt_' . $id );

			if ( $check ) {
				continue;
			}

			// 设置任务锁
			set_transient( 'plat_gp_mt_' . $id, true, 60 * 60 );
			$this->add_translate_job( $id );
		}

		$route            = new GP_Route();
		$route->notices[] = '该请求已加入队列，请稍后刷新页面';
		$route->redirect( '/' );
	}

	public function add_translate_job( $project_id ) {
		$project = GP::$project->find_one( array( 'id' => $project_id ) )->fields();
		// 获取待翻译原文
		$site_id = SITE_ID_TRANSLATE;
		$sql     = <<<SQL
select *
from wp_{$site_id}_gp_originals
where project_id = {$project_id}
  and id not in (
    select original_id
    from wp_{$site_id}_gp_translations
    where translation_set_id = (
        select id
        from wp_{$site_id}_gp_translation_sets
        where project_id = {$project_id}
    )
)
  and status = '+active';
SQL;

		$originals = GP::$original->many( $sql );

		for ( $i = 0; true; $i += 300 ) {
			$item = array_slice( $originals, $i, 500, true );
			if ( empty( $item ) ) {
				break;
			}

			wp_schedule_single_event( time() + 1, 'plat_schedule_gp_mt', [
				'project_id' => $project_id,
				'originals'  => $item,
			] );
		}

		return $project;
	}
}

