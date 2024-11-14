<?php

namespace Platform\Translate\WPOrgTranslateImport\Service;

use GP;
use GP_Route;
use Platform\Chinese_Format\Chinese_Format;
use Platform\Logger\Logger;
use Translation_Entry;
use WordPressdotorg\GlotPress\TranslationSuggestions\Translation_Memory_Client;
use WP_CLI;
use WP_Error;
use WPorg_GP_Project_Stats;
use function Platform\Translate\WPOrgTranslateImport\create_project;
use function Platform\Translate\WPOrgTranslateImport\get_web_page_contents;
use const Platform\Translate\WPOrgTranslateImport\PLUGIN;
use const Platform\Translate\WPOrgTranslateImport\THEME;

class Project {
	private $stats;

	public function __construct() {
		$this->stats = new WPorg_GP_Project_Stats;

		add_action( 'gp_import_from_wporg', [ $this, 'import' ], 10, 2 );
		add_action( 'gp_delete_project', [ $this, 'delete' ], 10, 2 );
		add_filter( 'gp_translations_footer_links', [ $this, 'gp_translations_footer_links' ], 10, 4 );

		GP::$router->add( "/gp-wp-import/(.+?)/(.+?)", [ $this, 'import' ], 'get' );
		GP::$router->add( "/gp-wp-import/(.+?)/(.+?)", [ $this, 'import' ], 'post' );
	}

	/**
	 * 通过 slug 和 type 导入项目
	 *
	 * @param string $slug
	 * @param string $type 是插件 Or 主题？
	 *
	 * @return bool
	 */
	public function import( string $slug, string $type = PLUGIN ): bool {
		if ( 'cli' !== PHP_SAPI ) {
			set_time_limit( 3600 );
			ignore_user_abort( true );
		}

		$route     = new GP_Route;
		$wporg_url = sprintf( 'https://translate.wordpress.org/locale/zh-cn/default/wp-%s/%s/', $type, $slug );
		$data      = get_web_page_contents( $wporg_url );
		if ( is_wp_error( $data ) ) {
			if ( 'cli' !== PHP_SAPI ) {
				$route->redirect_with_error( $data->get_error_message() );
			} else {
				WP_CLI::line( $data->get_error_message() );
			}

			return false;
		}

		if ( PLUGIN === $type ) {
			$sub_projects = self::get_plugin_sub_project( $slug );
		} else {
			$sub_projects = self::get_theme_sub_project( $slug );
		}

		if ( is_wp_error( $sub_projects ) ) {
			Logger::error( Logger::TRANSLATE, '获取子项目详情失败', array(
				'slug'  => $slug,
				'type'  => $type,
				'error' => $sub_projects->get_error_message(),
			) );

			if ( 'cli' !== PHP_SAPI ) {
				$route->redirect_with_error( '获取子项目详情失败：' . $sub_projects->get_error_message() );
			} else {
				WP_CLI::line( '获取子项目详情失败：' . $sub_projects->get_error_message() );
			}

			return false;
		}

		foreach ( $sub_projects as $sub_project ) {
			// 因为每个项目都只有一个中文翻译集，所以这里直接按项目ID搜索
			$translation_set = GP::$translation_set->find_one( array( 'project_id' => $sub_project->id ) );

			if ( PLUGIN === $type ) {
				if ( 'body' === $sub_project->slug ) {
					$wporg_project_slug = 'stable';
				} elseif ( 'readme' === $sub_project->slug ) {
					$wporg_project_slug = 'stable-readme';
				} else {
					Logger::error( Logger::TRANSLATE, '插件子项目 slug 错误(非 body 亦非 readme)' );

					continue;
				}

				$wporg_url = sprintf( 'https://translate.wordpress.org/projects/wp-%s/%s/%s/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $type, $slug, $wporg_project_slug );

				$data = get_web_page_contents( $wporg_url );
				if ( is_wp_error( $data ) ) {
					// 插件第一次请求失败时尝试抓取trunk翻译
					$wporg_project_slug = str_replace( 'stable', 'dev', $wporg_project_slug );
					$wporg_url          = sprintf( 'https://translate.wordpress.org/projects/wp-%s/%s/%s/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $type, $slug, $wporg_project_slug );

					$data = get_web_page_contents( $wporg_url );
				}
			} else {
				$wporg_url = sprintf( 'https://translate.wordpress.org/projects/wp-%s/%s/zh-cn/default/export-translations/?filters[term]&filters[term_scope]=scope_any&filters[status]=current_or_waiting_or_fuzzy_or_untranslated&filters[user_login]&format=po', $type, $slug );

				$data = get_web_page_contents( $wporg_url );
			}
			if ( is_wp_error( $data ) ) {
				Logger::error( Logger::TRANSLATE, '请求翻译文件失败', array(
					'id'    => $sub_project->id,
					'url'   => $wporg_url,
					'error' => $data->get_error_message(),
				) );

				if ( 'cli' == PHP_SAPI ) {
					WP_CLI::line( '请求翻译文件失败：' . $data->get_error_message() );
				}

				continue;
			}

			if ( ! empty( $data ) ) {
				$temp_file = tempnam( sys_get_temp_dir(), 'GPI' );

				if ( false !== file_put_contents( $temp_file, $data ) ) {
					$format = gp_get_import_file_format( 'po', '' );

					$originals = $format->read_originals_from_file( $temp_file, $sub_project );
					if ( ! $originals ) {
						unlink( $temp_file );
						Logger::error( Logger::TRANSLATE, '无法从文件加载原文', array(
							'id' => $sub_project->id,
						) );

						continue;
					}
					GP::$original->import_for_project( $sub_project, $originals );

					$translations = $format->read_translations_from_file( $temp_file, $sub_project );
					if ( ! $translations ) {
						unlink( $temp_file );
						Logger::error( Logger::TRANSLATE, '无法从文件加载翻译', array(
							'id' => $sub_project->id,
						) );

						continue;
					}

					wp_set_current_user( BOT_USER_ID );

					/**
					 * 禁止导入翻译字符串为空或仅仅包含一个换行符的条目，这些问题的翻译文件是由 GlotPress 的 BUG 在翻译导出时生成的
					 *
					 * @var $entry Translation_Entry
					 */
					foreach ( $translations->entries as $entry ) {
						if ( empty( $entry->translations ) ) {
							continue;
						}

						if ( $entry->translations[0] === "\n" ) {
							$entry->translations = array();
						}

						// 预格式化翻译
						$f = Chinese_Format::get_instance();
						foreach ( $entry->translations as $key => $value ) {
							$entry->translations[ $key ] = $f->convert( $value );
						}
					}
					unset( $entry );

					$translation_set->import( $translations );
					unlink( $temp_file );

					// 同步翻译到翻译记忆库中
					$originals_and_translations = [];
					$raw                        = GP::$translation->find(
						array(
							'translation_set_id' => $translation_set->id,
							'status'             => 'current',
						),
						'original_id ASC'
					);
					foreach ( $raw as $item ) {
						$originals_and_translations[ $item->original_id ] = $item->id;
						// 添加到统计列表
						$this->stats->translation_edited( $item );
					}
					Translation_Memory_Client::update( $originals_and_translations );

				}
			} else {
				Logger::error( Logger::TRANSLATE, '翻译下载出错', array(
					'id' => $sub_project->id,
				) );

				return false;
			}
		}

		// 执行统计操作
		$this->stats->shutdown();

		if ( 'cli' !== PHP_SAPI ) {
			$referer = gp_url_project( "{$type}/$slug" );
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$referer = $_SERVER['HTTP_REFERER'];
			}

			$route->notices[] = '已成功导入';
			$route->redirect( $referer );
		}

		return true;
	}

	/**
	 * 通过 slug 和 type 删除项目
	 *
	 * @param string $slug
	 * @param string $type 是插件 Or 主题？
	 *
	 * @return bool
	 */
	public static function delete( string $slug, string $type = PLUGIN ): bool {
		$root_project = GP::$project->find_one( array(
			'path' => $type,
		) );

		$project = GP::$project->find_one( array(
			'slug'              => $slug,
			'parent_project_id' => $root_project->id,
		) );
		if ( empty( $project ) ) {
			return false;
		}

		// 处理子项目
		foreach ( $project->sub_projects() as $sub_project ) {
			self::delete_by_id( $sub_project );
		}

		self::delete_by_id( $project );

		// 删除各种关联数据
		global $wpdb;
		$wpdb->delete( $wpdb->dotorg_translation_warnings, array( 'project_path' => $project->path ) );
		$wpdb->delete( $wpdb->project_translation_status, array( 'project_id' => $project->id ) );
		$wpdb->delete( 'translate_translation_editors', array( 'project_id' => $project->id ) );

		return true;
	}

	private static function delete_by_id( $project ): void {
		$translation_set = GP::$translation_set->find_one( array(
			'project_id' => $project->id,
		) );
		if ( ! empty( $translation_set ) ) {
			$translation_set->delete();
		}

		$originals = GP::$original->find( array(
			'project_id' => $project->id,
		) );
		if ( ! empty( $originals ) ) {
			$originals[0]->delete_all(
				array(
					'project_id' => $project->id,
				)
			);
			$translations = GP::$translation->find( array(
				'original_id' => $originals,
			) );
			if ( ! empty( $translations ) ) {
				$translations[0]->delete_all(
					array(
						'original_id' => $originals,
					)
				);
			}
		}

		// 删除 meta
		gp_delete_meta( $project->id, 'directory_post_id', '', 'project' );
		gp_delete_meta( $project->id, 'version', '', 'project' );
		gp_delete_meta( $project->id, 'views', '', 'project' );

		$project->delete();
	}

	public static function get_plugin_sub_project( $slug ): array|WP_Error {
		$type            = PLUGIN;
		$plugins_project = GP::$project->find_one( array(
			'path' => 'plugins',
		) );

		$project = GP::$project->find_one( array(
			'slug'              => $slug,
			'parent_project_id' => $plugins_project->id,
		) );

		$project_info = self::get_project_info_by_post( $slug, $type );
		if ( empty( $project_info['name'] ) ) {
			return new WP_Error( 'error', '从插件目录获取项目详情失败' );
		}

		/**
		 * 如果项目不存在则创建之
		 */
		if ( empty( $project ) ) {
			$master_project = create_project(
				$project_info['name'],
				$slug,
				$type,
				$plugins_project->id,
			);
			if ( empty( $master_project ) ) {
				return new WP_Error( 'error', '创建主项目失败' );
			}

			$body_id = create_project(
				'插件主体',
				'body',
				$type,
				$master_project,
				$slug,
			);
			if ( empty( $body_id ) ) {
				return new WP_Error( 'error', '创建body项目失败' );
			}

			$readme_id = create_project(
				'自述文件',
				'readme',
				$type,
				$master_project,
				$slug,
			);
			if ( empty( $readme_id ) ) {
				return new WP_Error( 'error', '创建readme项目失败' );
			}

			$project = GP::$project->find_one( array(
				'slug'              => $slug,
				'parent_project_id' => $plugins_project->id,
			) );
			if ( empty( $project ) ) {
				return new WP_Error( 'error', '执行完项目创建流程后依然无法获取项目详情' );
			}
		}

		/**
		 * 对于项目详情和版本号，这俩基本每次需要获取的时候都会变更，所以在这里直接更新
		 */
		$r = self::update_project( $project->id, $project_info['name'], $project_info['description'], $project_info['ID'], $project_info['version'], $project_info['views'] );
		if ( false === $r ) {
			return new WP_Error( 'error', '无法更新项目版本号' );
		}

		$body = GP::$project->find_one( array(
			'slug'              => 'body',
			'parent_project_id' => $project->id,
		) );
		if ( empty( $body ) ) {
			return new WP_Error( 'error', '无法获取body项目详情' );
		}

		/**
		 * 为程序主体更新代码模板URL
		 */
		self::update_source_url_template( (int) $body->id, (string) $body->path, $type, (string) $project_info['version'] );

		$readme = GP::$project->find_one( array(
			'slug'              => 'readme',
			'parent_project_id' => $project->id,
		) );
		if ( empty( $readme ) ) {
			return new WP_Error( 'error', '无法获取readme项目详情' );
		}

		return array(
			'body'   => $body,
			'readme' => $readme,
		);
	}

	private static function get_project_info_by_post( string $slug, string $type ): array {
		if ( $type == PLUGIN ) {
			switch_to_blog( SITE_ID_PLUGINS );
		} else {
			switch_to_blog( SITE_ID_THEMES );
		}

		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( empty( $post ) ) {
			restore_current_blog();

			return [
				'ID'          => '',
				'name'        => '',
				'version'     => '',
				'description' => '',
				'views'       => 0,
			];
		}
		$ID          = $post->ID;
		$name        = $post->post_title;
		$version     = get_post_meta( $post->ID, 'version', true );
		$description = $post->post_content;
		$views       = get_post_meta( $post->ID, 'views', true );

		restore_current_blog();

		return [
			'ID'          => $ID,
			'name'        => $name,
			'version'     => $version,
			'description' => $description,
			'views'       => $views,
		];
	}

	private static function update_project( $project_id, $name, $description, $post_id, $version, $views ): bool {
		GP::$project->update( array(
			'name'        => $name,
			'description' => $description,
		), array(
			'id' => $project_id,
		) );

		gp_update_meta( $project_id, 'directory_post_id', $post_id, 'project' );
		gp_update_meta( $project_id, 'version', $version, 'project' );
		gp_update_meta( $project_id, 'views', $views, 'project' );

		return true;
	}

	/**
	 * 为项目更新上source_url_template字段
	 *
	 * 这个函数通常给只为承载程序主体翻译的项目调用
	 *
	 * @param int $project_id
	 * @param string $path
	 * @param string $type
	 * @param string $version
	 *
	 * @return void
	 */
	private static function update_source_url_template( int $project_id, string $path, string $type, string $version = '' ): void {
		if ( PLUGIN === $type ) {
			$source_url_template = str_replace( '/body', '/trunk/%file%#L%line%', $path );
		} elseif ( THEME === $type ) {
			$items = explode( '/', $path );
			$slug  = $items[ count( $items ) - 1 ];

			$source_url_template = str_replace( "/$slug/$slug", "/$slug/$version/%file%#L%line%", $path );
		} else {
			$source_url_template = '';
		}

		GP::$project->update( array(
			'source_url_template' => '/svn/' . $source_url_template,
		), array(
			'id' => $project_id,
		) );

	}

	public static function get_theme_sub_project( $slug ): array|WP_Error {
		$type           = THEME;
		$themes_project = GP::$project->find_one( array(
			'path' => 'themes',
		) );

		$project = GP::$project->find_one( array(
			'slug'              => $slug,
			'parent_project_id' => $themes_project->id,
		) );

		$project_info = self::get_project_info_by_post( $slug, $type );
		if ( empty( $project_info['name'] ) ) {
			return new WP_Error( 'error', '从主题目录获取项目详情失败' );
		}

		/**
		 * 如果项目不存在则创建之
		 */
		if ( empty( $project ) ) {
			$master_project = create_project(
				$project_info['name'],
				$slug,
				$type,
				$themes_project->id,
			);
			if ( empty( $master_project ) ) {
				return new WP_Error( 'error', '创建主项目失败' );
			}

			$body_id = create_project(
				'主题主体',
				'body',
				$type,
				$master_project,
				$slug,
			);
			if ( empty( $body_id ) ) {
				return new WP_Error( 'error', '创建主题子项目失败' );
			}

			$project = GP::$project->find_one( array(
				'slug'              => $slug,
				'parent_project_id' => $themes_project->id,
			) );
			if ( empty( $project ) ) {
				return new WP_Error( 'error', '执行完项目创建流程后依然无法获取项目详情' );
			}
		}

		/**
		 * 对于项目详情和版本号，这俩基本每次需要获取的时候都会变更，所以在这里直接更新
		 */
		$r = self::update_project( $project->id, $project_info['name'], $project_info['description'], $project_info['ID'], $project_info['version'], $project_info['views'] );
		if ( false === $r ) {
			return new WP_Error( 'error', '无法更新项目版本号' );
		}

		$body = GP::$project->find_one( array(
			'slug'              => 'body',
			'parent_project_id' => $project->id,
		) );
		if ( empty( $body ) ) {
			return new WP_Error( 'error', '无法获取主题body项目详情' );
		}

		/**
		 * 为程序主体更新代码模板URL
		 */
		self::update_source_url_template( (int) $body->id, (string) $body->path, $type, (string) $project_info['version'] );

		return array(
			$slug => $body
		);
	}

	/**
	 * 翻译强制导入功能，只有管理员可以使用
	 *
	 * @param $footer_links
	 * @param $project
	 * @param $locale
	 * @param $translation_set
	 *
	 * @return mixed
	 */
	public function gp_translations_footer_links( $footer_links, $project, $locale, $translation_set ) {
		$type = str_starts_with( $project->path, 'plugins/' ) ? 'plugins' : false;
		$type = str_starts_with( $project->path, 'themes/' ) && false === $type ? 'themes' : $type;
		preg_match( "/{$type}\/(.+)\//", $project->path, $match );

		if ( is_user_logged_in() && current_user_can( 'manage_options' ) && $type && isset( $match[1] ) && ! empty( $match[1] ) ) {
			$footer_links[] = gp_link_get( gp_url( "/gp-wp-import/{$match[1]}/$type" ), '从 WordPress.Org 强制导入' );
		}

		return $footer_links;
	}

	public function before_request() {
	}

	public function after_request() {
	}
}
