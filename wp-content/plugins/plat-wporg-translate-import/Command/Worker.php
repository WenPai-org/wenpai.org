<?php

namespace Platform\Translate\WPOrgTranslateImport\Command;

use Exception;
use GP;
use Platform\Logger\Logger;
use Platform\Translate\WPOrgTranslateImport\Service\Project;
use WP_CLI;
use WP_CLI_Command;

class Worker extends WP_CLI_Command {
	public function __construct() {
		parent::__construct();

		try {
			WP_CLI::add_command( 'platform translate_import', __NAMESPACE__ . '\Worker' );
		} catch ( Exception $e ) {
			Logger::error( Logger::STORE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * 从应用市场同步所有商品元数据到翻译平台（不包括翻译和原文抓取）
	 * 注意这只会同步 GlotPress 中已存在的项目
	 *
	 * @return void
	 */
	public function sync_all_product(): void {
		$service = new Project();

		$plugins_project = GP::$project->find_one( array(
			'path' => 'plugins',
		) );
		$themes_project  = GP::$project->find_one( array(
			'path' => 'themes',
		) );

		$plugins = GP::$project->all( array(
			'parent_project_id' => $plugins_project->id,
		) );
		$themes  = GP::$project->all( array(
			'parent_project_id' => $themes_project->id,
		) );

		foreach ( $plugins as $plugin ) {
			Logger::info( Logger::TRANSLATE, '同步插件', [ 'slug' => $plugin->slug ] );
			$service->get_plugin_sub_project( $plugin->slug );
		}
		foreach ( $themes as $theme ) {
			Logger::info( Logger::TRANSLATE, '同步主题', [ 'slug' => $theme->slug ] );
			$service->get_theme_sub_project( $theme->slug );
		}

		WP_CLI::line( '搞定！' );
	}

	/**
	 * 导入单个项目的翻译
	 */
	public function import( $args, $assoc_args ): void {
		if ( empty( $assoc_args['slug'] ) || empty( $assoc_args['type'] ) ) {
			WP_CLI::line( '你需要给出要导入的项目 Slug 和类型' );
			exit;
		}
		$slug = $assoc_args['slug'];
		$type = $assoc_args['type'];

		$service = new Project();
		$service->import( $slug, $type );

		WP_CLI::line( '搞定！' );
	}
}
