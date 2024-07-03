<?php

namespace Platform\Translate\WPOrgTranslateImport\Command;

use Exception;
use GP;
use GP_Locales;
use Platform\Chinese_Format\Chinese_Format;
use Platform\Logger\Logger;
use WP_CLI;
use WP_CLI_Command;
use function Platform\Translate\WPOrgTranslateImport\get_web_page_contents;

class Release extends WP_CLI_Command {
	public function __construct() {
		parent::__construct();

		try {
			WP_CLI::add_command( 'platform translate_import_release', __NAMESPACE__ . '\Release' );
		} catch ( Exception $e ) {
			Logger::error( Logger::STORE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	public function release( $args, $assoc_args ): void {
		if ( ! isset( $assoc_args['version'] ) ) {
			WP_CLI::line( '你需要给出要抓取的版本号' );
			exit;
		}
		$version = $assoc_args['version'];
		// 对版本号进行一下处理。用户传入的是诸如 5.8.3 需要处理成 5.8.x
		if ( $version !== 'dev' ) {
			$tmp                      = explode( '.', $version );
			$tmp[ count( $tmp ) - 1 ] = 'x';
			$version                  = join( '.', $tmp );
		}

		$old_version     = $assoc_args['old_version'] ?? '';
		$display_version = $assoc_args['display_version'] ?? $version;

		// 检查当前版本是否已存在
		$sub_projects = array(
			// 程序主体
			'body'    =>
				array(
					'name' => '主体',
					'url'  => sprintf( 'https://translate.wpmirror.com/projects/wp/%s/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $version ),
				),
			// 大洲与城市
			'cc'      =>
				array(
					'name' => '大洲与城市',
					'url'  => sprintf( 'https://translate.wpmirror.com/projects/wp/%s/cc/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $version ),
				),
			// 管理面板
			'admin'   =>
				array(
					'name' => '管理',
					'url'  => sprintf( 'https://translate.wpmirror.com/projects/wp/%s/admin/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $version ),
				),
			// 多站点网络管理面板
			'network' =>
				array(
					'name' => '网络管理',
					'url'  => sprintf( 'https://translate.wpmirror.com/projects/wp/%s/admin/network/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $version ),
				),
		);

		foreach ( $sub_projects as $slug => $project ) {
			$this->worker( $version, $old_version, $slug, $project['name'], $project['url'], $display_version );
		}

		WP_CLI::line( '恭喜你，执行成功' );
	}

	/**
	 * @param string $version 当前版本，如果不存在的话会自动创建
	 * @param string $old_version 旧版本，用于合并翻译
	 * @param string $wporg_translate_url 要抓取的的 WordPress.org 上的翻译源（通常是到处翻译的链接）
	 */
	private function worker( string $version, string $old_version, string $sub_project_slug, string $sub_project_name, string $wporg_translate_url, $display_version ): void {
		// 判断给定的父项目是否存在，不存在则新建
		$father_project = GP::$project->find_one( array(
			'path' => sprintf( 'core/%s', $display_version ),
		) );

		if ( ! $father_project ) {
			$root_project      = GP::$project->find_one( array(
				'path' => 'core',
			) );
			$father_project_id = $this->create_project( $display_version, $display_version, sprintf( 'WordPress %s 分支的翻译', $display_version ), $root_project->id );
		} else {
			$father_project_id = $father_project->id;
		}

		// 获取到父项目后需要把版本号更新上
		gp_update_meta( $father_project_id, 'version', $display_version, 'project' );

		// 判断给定的子项目是否存在，不存在则新建
		$sub_project = GP::$project->find_one( array(
			'path' => sprintf( 'core/%s/%s', $display_version, $sub_project_slug ),
		) );

		if ( ! $sub_project ) {
			$sub_project_id = $this->create_project( $sub_project_name, $sub_project_slug, '', $father_project_id, $display_version );

			$sub_project = GP::$project->find_one( array(
				'id' => $sub_project_id,
			) );
		}

		// 从 WordPress.org 获取翻译
		WP_CLI::line( 'URL: ' . $wporg_translate_url );
		$data = get_web_page_contents( $wporg_translate_url );
		if ( is_wp_error( $data ) ) {
			WP_CLI::line( '翻译文件下载失败，子项目：' . $sub_project_name );
			exit;
		}

		$temp_file = tempnam( sys_get_temp_dir(), 'Platform' );

		if ( ! file_put_contents( $temp_file, $data ) ) {
			unlink( $temp_file );

			WP_CLI::line( "翻译文件下载后保存失败，可能是权限问题，子项目：{$sub_project_name}，临时文件目录：{$temp_file}" );
			exit;
		}

		// 导入原文
		$format = gp_get_import_file_format( 'po', '' );

		$originals = $format->read_originals_from_file( $temp_file, $sub_project );
		if ( ! $originals ) {
			unlink( $temp_file );

			WP_CLI::line( "无法从翻译文件加载原文，子项目：{$sub_project_name}，翻译文件：{$temp_file}" );
			exit;
		}
		GP::$original->import_for_project( $sub_project, $originals );

		// 导入翻译
		// 正式导入前看看是否需要先导入旧版项目的翻译
		$translation_set = GP::$translation_set->find_one( array( 'project_id' => $sub_project->id ) );

		if ( ! empty( $old_version ) ) {
			$old_sub_project = GP::$project->find_one( array(
				'path' => sprintf( 'core/%s/%s', $old_version, $sub_project_slug ),
			) );

			if ( ! $old_sub_project ) {
				WP_CLI::line( "你所指定的旧版翻译的项目不存在，错误项目：core/$old_version/$sub_project_slug" );
				exit;
			}

			$old_translation_set = GP::$translation_set->find_one( array( 'project_id' => $old_sub_project->id ) );

			$old_po      = $format->print_exported_file( $old_sub_project, GP_Locales::by_slug( $old_translation_set->locale ), $old_translation_set, GP::$translation->for_export( $old_sub_project, $old_translation_set, array( 'status' => 'current' ) ) );
			$old_po_file = tempnam( sys_get_temp_dir(), 'Platform' );
			file_put_contents( $old_po_file, $old_po );

			$translations = $format->read_translations_from_file( $old_po_file, $sub_project );
			if ( ! $translations ) {
				unlink( $temp_file );
				unlink( $old_po_file );

				WP_CLI::line( "无法从翻译文件加载翻译，该翻译文件从 {core/$old_version/$sub_project_slug} 项目中导出，翻译文件位于： {$old_po_file}" );
				exit;
			}

			wp_set_current_user( BOT_USER_ID );

			$translation_set->import( $translations );

			unlink( $old_po_file );
		}

		// 然后导入新的翻译
		$translations = $format->read_translations_from_file( $temp_file, $sub_project );
		if ( ! $translations ) {
			unlink( $temp_file );

			WP_CLI::line( "无法从翻译文件加载翻译，翻译文件位于： {$temp_file}" );
			exit;
		}
		// 导入前先循环格式化一下，纠正错误的格式
		/*$format = Chinese_Format::get_instance();
		foreach ( $translations->entries as $translation ) {
			foreach ( $translation->translations as $key => $value ) {
				$translation->translations[ $key ] = $format->convert( $value );
			}
		}*/

		wp_set_current_user( BOT_USER_ID );
		add_filter( 'gp_translation_set_import_status', function ( $status, $entry, $translated ) {
			return 'current';
		}, 10, 3 );

		$translation_set->import( $translations, 'current' );

		unlink( $temp_file );
	}

	private function create_project( string $name, string $slug, string $description, int $parent_project_id = 0, string $parent_project_slug = '' ): int {
		global $wpdb;

		$parent_project_slug = empty( $parent_project_slug ) ? '' : $parent_project_slug . '/';

		$res = $wpdb->insert( 'wp_' . SITE_ID_TRANSLATE . '_gp_projects', array(
			'name'                => $name,
			'slug'                => $slug,
			'path'                => sprintf( 'core/%s%s', $parent_project_slug, $slug ),
			'description'         => $description,
			'source_url_template' => '',
			'parent_project_id'   => $parent_project_id,
			'active'              => 1
		) );

		$project_id = $wpdb->insert_id;

		$root_project = GP::$project->find_one( array(
			'path' => 'core',
		) );

		if ( 0 !== (int) $res && $parent_project_id != $root_project->id ) {
			$wpdb->insert( 'wp_' . SITE_ID_TRANSLATE . '_gp_translation_sets', array(
				'name'       => '简体中文',
				'slug'       => 'default',
				'project_id' => $project_id,
				'locale'     => 'zh-cn'
			) );
		}

		return $project_id;
	}
}
