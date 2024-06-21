<?php

namespace Platform\Translate\GeneratePack\Service;

use GP;
use GP_Locales;
use Platform\Logger\Logger;
use stdClass;
use Translation_Entry;
use WP_Error;
use ZipArchive;
use function Platform\Helper\execute_command;

class Pack {
	// 打包保存目录
	const BASE_PACK_DIR = WP_CONTENT_DIR . '/translation';

	// 允许的项目类型
	const ALLOWED_TYPE = array(
		'themes',
		'plugins',
		'core',
		//'hosting',
	);

	const PACKAGE_THRESHOLD = 80;

	private string $slug = '';

	private string $type = '';

	/**
	 * -------------------------------------------------------------
	 * 标记项目原始类型
	 * -------------------------------------------------------------
	 * 对于第三方托管项目，其 type 为 hosting，此时难以通过 API 为用户推送翻译，
	 * 因为不知道该项目到底是插件还是主题。由此为新项目引入名为 type_raw 的
	 * meta 字段，来标识项目的“原始类型”，其值只可能是 plugins 或 themes 。
	 *
	 * 对于数据库中的 type_raw 字段，如果项目自身不存在 type_raw 值的话，则会
	 * 调用他的 type 值，由此其值将也可能是 core、hosting
	 *
	 * @var string
	 */
	private string $type_raw = '';

	private string $version = '';

	private array $branches = array();

	public function generate_all_pack(): void {
		foreach ( self::ALLOWED_TYPE as $type ) {
			// 刷一遍缓存，防止获取到的项目信息不是最新的
			wp_cache_flush();

			$parent = GP::$project->find_one( array( 'path' => $type ) );
			if ( ! $parent ) {
				Logger::error( Logger::TRANSLATEPACK, '翻译打包失败，无效的父类型: ' . $type );

				return;
			}

			$products = GP::$project->find_many( array( 'parent_project_id' => $parent->id ) );

			foreach ( $products as $product ) {
				$version  = gp_get_meta( 'project', $product->id, 'version' ) ?: '';
				$type_raw = gp_get_meta( 'project', $product->id, 'type_raw' ) ?: '';

				self::job(
					$product->slug,
					$type,
					$version,
					'',
					$type_raw
				);
			}
		}
	}

	public function job( string $slug, string $type, string $version, string $branch = '', string $type_raw = '' ): void {
		$this->slug     = $slug;
		$this->type     = $type;
		$this->version  = $version;
		$this->type_raw = $type_raw;

		switch ( $type ) {
			case 'plugins':
			case 'themes':
			case 'hosting':
				$this->branches = array(
					'body' => $slug,
				);

				$this->generate();
				break;
			case 'core':
				$this->branches = array(
					'body'    => '',
					'cc'      => 'continents-cities',
					'admin'   => 'admin',
					'network' => 'admin-network',
				);

				$this->generate();
				break;
			default:
				Logger::error( Logger::TRANSLATEPACK, '翻译打包失败，无效的应用类型: ' . $type );
		}
	}

	private function generate(): void {
		$pack_file_paths   = array();
		$build_directory   = self::BASE_PACK_DIR . "/{$this->type}/{$this->slug}/{$this->version}";
		$working_directory = "/tmp/platform/translate-pack-tmp/{$this->slug}";
		$export_directory  = "{$working_directory}/{$this->version}/zh_CN";

		foreach ( $this->branches as $branch => $textdomain ) {
			$gp_project = GP::$project->by_path( "$this->type/$this->slug" );
			if ( ! $gp_project ) {
				Logger::error( Logger::TRANSLATEPACK, '无效的 path: ' . "{$this->type}/$this->slug" );

				return;
			}

			$gp_project = GP::$project->by_path( "{$this->type}/$this->slug/$branch" );
			if ( ! $gp_project ) {
				Logger::error( Logger::TRANSLATEPACK, '项目信息获取失败，path: ' . "{$this->type}/$this->slug/$branch" );

				return;
			}

			$translation_sets = GP::$translation_set->by_project_id( $gp_project->id );
			if ( ! $translation_sets ) {
				Logger::error( Logger::TRANSLATEPACK, '翻译集获取失败，项目id: ' . $gp_project->id );

				return;
			}

			if ( ! $this->version ) {
				Logger::error( Logger::TRANSLATEPACK, '版本号为空，项目id: ' . $gp_project->id );

				return;
			}

			$data                    = new stdClass();
			$data->type              = $this->type;
			$data->type_raw          = $this->type_raw;
			$data->domain            = $textdomain;
			$data->slug              = $this->slug;
			$data->version           = $this->version;
			$data->translation_sets  = $translation_sets;
			$data->gp_project        = $gp_project;
			$data->working_directory = $working_directory;
			$data->export_directory  = $export_directory;
			$r                       = $this->build_language_packs( $data );
			if ( ! $r ) {
				return;
			}

			$pack_file_paths[] = $r;
		}

		$zip_file       = "{$export_directory}/{$this->slug}-zh_CN.zip";
		$build_zip_file = "{$build_directory}/zh_CN.zip";

		// 创建 ZIP 压缩包
		$pack_files = array();
		foreach ( $pack_file_paths as $pack_file_path ) {
			$pack_files[] = $pack_file_path['po_file'];
			$pack_files[] = $pack_file_path['mo_file'];
			foreach ( (array) $pack_file_path['additional_files'] as $item ) {
				$pack_files[] = $item;
			}
		}
		$zip = new ZipArchive();
		$zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE );
		foreach ( $pack_files as $pack_file ) {
			$zip->addFile( $pack_file, basename( $pack_file ) );
		}
		$zip->close();

		if ( ! file_exists( $zip_file ) ) {
			Logger::warning( Logger::TRANSLATEPACK, "ZIP压缩失败" );

			// 清理工作目录
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $working_directory ) ) );

			return;
		}

		// 创建语言包存储目录
		$result = execute_command( sprintf(
			'mkdir -p %s 2>&1',
			escapeshellarg( $build_directory )
		) );

		if ( is_wp_error( $result ) ) {
			Logger::warning( Logger::TRANSLATEPACK, "创建语言包存储目录失败: ", $result->get_error_data() );

			// 清理工作目录
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $working_directory ) ) );

			return;
		}

		// 将翻译的 ZIP 压缩包移动到语言包存储目录中
		$result = rename( $zip_file, $build_zip_file );

		if ( ! $result ) {
			Logger::warning( Logger::TRANSLATEPACK, "移动ZIP包失败" );

			// 清理工作目录
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $working_directory ) ) );

			return;
		}

		/**
		 * 将语言包信息插入数据库。
		 */
		// 插入前先计算所有生成的文本域中的最大的一个“最后修改时间”
		$last_modified = '';
		foreach ( $pack_file_paths as $pack_file_path ) {
			if ( strtotime( $pack_file_path['last_modified'] ) > strtotime( $last_modified ) ) {
				$last_modified = $pack_file_path['last_modified'];
			}
		}
		$result = $this->insert_language_pack( $data->type, $data->type_raw, $data->slug, 'zh_CN', $data->version, $last_modified );

		if ( is_wp_error( $result ) ) {
			Logger::warning( Logger::TRANSLATEPACK, sprintf( "插入语言包信息失败: %s", $result->get_error_message() ) );

			// Clean up.
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $working_directory ) ) );

			return;
		}

		// 清理工作目录
		execute_command( sprintf( 'rm -rf %s', escapeshellarg( $working_directory ) ) );

		//Logger::info( Logger::TRANSLATEPACK, "为 {$this->slug} 的 zh_CN 成功生成了语言包" );
	}

	private function build_language_packs( $data ): false|array {
		$existing_packs = $this->get_active_language_packs( $data->type_raw, $data->slug, $data->version );

		$set = current( $data->translation_sets );
		// 获取 WP locale.
		$gp_locale = GP_Locales::by_slug( $set->locale );
		if ( ! isset( $gp_locale->wp_locale ) ) {
			return false;
		}

		// 设置 wp_locale 直到 GlotPress 为变体返回正确的 wp_locale。
		$wp_locale = $gp_locale->wp_locale;
		if ( 'default' !== $set->slug ) {
			$wp_locale = $wp_locale . '_' . $set->slug;
		}

		// 检查是否不存在任何”当前“翻译
		if ( 0 === $set->current_count() ) {
			//Logger::info( Logger::TRANSLATEPACK, "跳过 {$wp_locale} 因为没有翻译" );

			return false;
		}

		// 检查项目的翻译百分比是否高于阈值
		$has_existing_pack = $this->has_active_language_pack( $data->type_raw, $data->slug, $wp_locale );
		if ( ! $has_existing_pack ) {
			$percent_translated = $set->percent_translated();
			if ( $percent_translated < self::PACKAGE_THRESHOLD ) {
				//Logger::info( Logger::TRANSLATEPACK, "跳过 {$wp_locale}, 翻译率: ({$percent_translated}%)" );

				return false;
			}
		} else {
			//Logger::info( Logger::TRANSLATEPACK, "跳过翻译率检查 {$wp_locale}, 因为已经有存在的语言包" );
		}

		// 检查自从上次打包以来翻译是否被更新过
		if ( isset( $existing_packs[ $wp_locale ] ) ) {
			$pack_time      = strtotime( $existing_packs[ $wp_locale ]->updated );
			$glotpress_time = strtotime( $set->last_modified() );

			if ( $pack_time >= $glotpress_time ) {
				//Logger::info( Logger::TRANSLATEPACK, "跳过 {$wp_locale}, 因为没有更新的翻译" );

				return false;
			}
		}

		$entries = GP::$translation->for_export( $data->gp_project, $set, array( 'status' => 'current' ) );
		if ( ! $entries ) {
			//Logger::warning( Logger::TRANSLATEPACK, "{$wp_locale} 没有可用的翻译，退出执行" );

			return false;
		}

		if ( empty( $data->domain ) ) {
			$filename = "$wp_locale";
		} else {
			$filename = "{$data->domain}-{$wp_locale}";
		}
		$json_file_base = "{$data->export_directory}/{$filename}";
		$po_file        = "{$data->export_directory}/{$filename}.po";
		$mo_file        = "{$data->export_directory}/{$filename}.mo";
		$php_file       = "{$data->export_directory}/{$filename}.l10n.php";

		// 创建目录
		$this->create_directory( $data->export_directory );

		// 根据翻译条目出现的位置构建映射并分隔 po 条目。
		$mapping    = $this->build_mapping( $entries );
		$po_entries = array_key_exists( 'po', $mapping ) ? $mapping['po'] : [];

		unset( $mapping['po'] );

		// 为每个 JS 文件创建 JED json 文件。
		if ( $data->type == 'core' ) {
			$json_file_base   = "{$data->export_directory}/{$wp_locale}";
			$additional_files = $this->build_core_json_files( $data->gp_project, $gp_locale, $set, $mapping, $json_file_base );
		} else {
			$additional_files = $this->build_json_files( $data->gp_project, $gp_locale, $set, $mapping, $json_file_base );
		}

		// 创建 PHP 文件
		$php_file_written = $this->build_php_file( $data->gp_project, $gp_locale, $set, $po_entries, $php_file );
		if ( $php_file_written ) {
			$additional_files[] = $php_file;
		}

		// 创建 PO 文件
		$last_modified = $this->build_po_file( $data->gp_project, $gp_locale, $set, $po_entries, $po_file );
		if ( is_wp_error( $last_modified ) ) {
			Logger::warning( Logger::TRANSLATEPACK, sprintf( "为 {$wp_locale} 生成 PO 失败: %s", $last_modified->get_error_message() ) );

			// 清理工作目录
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $data->working_directory ) ) );

			return false;
		}

		// 创建 MO 文件
		$result = execute_command( sprintf(
			'msgfmt %s -o %s 2>&1',
			escapeshellarg( $po_file ),
			escapeshellarg( $mo_file )
		) );

		if ( is_wp_error( $result ) ) {
			Logger::warning( Logger::TRANSLATEPACK, "为 {$wp_locale} 生成 MO 失败: ", $result->get_error_data() );

			// 清理工作目录
			execute_command( sprintf( 'rm -rf %s', escapeshellarg( $data->working_directory ) ) );

			return false;
		}

		return array(
			'po_file'          => $po_file,
			'mo_file'          => $mo_file,
			'additional_files' => $additional_files,
			'last_modified'    => $last_modified,
		);
	}

	private function get_active_language_packs( $type_raw, $domain, $version ): array|object {
		global $wpdb;

		$active_language_packs = $wpdb->get_results( $wpdb->prepare(
			'SELECT language, updated FROM language_packs WHERE type_raw = %s AND domain = %s AND version = %s AND active = 1',
			$type_raw,
			$domain,
			$version
		), OBJECT_K );

		if ( ! $active_language_packs ) {
			return array();
		}

		return $active_language_packs;
	}

	private function has_active_language_pack( $type_raw, $domain, $locale ): bool {
		global $wpdb;

		return (bool) $wpdb->get_var( $wpdb->prepare(
			'SELECT updated FROM language_packs WHERE type_raw = %s AND domain = %s AND language = %s AND active = 1 LIMIT 1',
			$type_raw,
			$domain,
			$locale
		) );
	}

	/**
	 * 创建缺失的目录
	 *
	 * @param $directory
	 */
	private function create_directory( $directory ): void {
		execute_command( sprintf(
			'mkdir --parents %s 2>/dev/null',
			escapeshellarg( $directory )
		) );
	}

	private function build_mapping( $entries ): array {
		$mapping = [];

		foreach ( $entries as $entry ) {
			/** @var Translation_Entry $entry */

			// Find all unique sources this translation originates from.
			if ( ! empty( $entry->references ) ) {
				$sources = array_map(
					function ( $reference ) {
						$parts = explode( ':', $reference );
						$file  = $parts[0];

						if ( str_ends_with( $file, '.min.js' ) ) {
							return substr( $file, 0, - 7 ) . '.js';
						}

						if ( str_ends_with( $file, '.js' ) ) {
							return $file;
						}

						return 'po';
					},
					$entry->references
				);

				$sources = array_unique( $sources );
			} else {
				$sources = [ 'po' ];
			}

			foreach ( $sources as $source ) {
				$mapping[ $source ][] = $entry;
			}
		}

		return $mapping;
	}

	private function build_json_files( $gp_project, $gp_locale, $set, $mapping, $base_dest ): array {
		$files  = array();
		$format = gp_array_get( GP::$formats, 'jed1x' );

		foreach ( $mapping as $file => $entries ) {
			// 不要为源文件创建 JSON 文件。
			if ( str_starts_with( $file, 'src/' ) || str_contains( $file, '/src/' ) ) {
				continue;
			}

			// 获取 Jed 1.x 兼容 JSON 格式的翻译。
			$json_content = $format->print_exported_file( $gp_project, $gp_locale, $set, $entries );

			// 解码并添加带有文件引用的注释以进行调试。
			$json_content_decoded          = json_decode( $json_content );
			$json_content_decoded->comment = [ 'reference' => $file ];

			$json_content = wp_json_encode( $json_content_decoded );

			$hash = md5( $file );
			$dest = "{$base_dest}-{$hash}.json";

			file_put_contents( $dest, $json_content );

			$files[] = $dest;
		}

		return $files;
	}

	private function build_core_json_files( $gp_project, $gp_locale, $set, $mapping, $base_dest ): array {
		$files  = array();
		$format = gp_array_get( GP::$formats, 'jed1x' );

		foreach ( $mapping as $file => $entries ) {
			// Get the translations in Jed 1.x compatible JSON format.
			$json_content = $format->print_exported_file( $gp_project, $gp_locale, $set, $entries );

			// Decode and add comment with file reference for debugging.
			$json_content_decoded          = json_decode( $json_content );
			$json_content_decoded->comment = [ 'reference' => $file ];

			$hash = md5( $file );
			$dest = "{$base_dest}-{$hash}.json";

			/*
			 * 将翻译合并到现有 JSON 文件中。
			 *
			 * 某些字符串出现在多个源文件中，这些源文件可能在前端或管理中使用，或在两者中使用，因此它们可以是不同翻译项目的一部分（dev、admin、admin-network）。
			 * 与使用 gettext 的 PHP 不同，在 PHP 中，来自多个 MO 文件的翻译会自动合并，我们必须在为每个引用传送单个 JSON 文件之前合并翻译。
			 */
			if ( file_exists( $dest ) ) {
				$existing_json_content_decoded = json_decode( file_get_contents( $dest ) );
				if ( isset( $existing_json_content_decoded->locale_data->messages ) ) {
					foreach ( $existing_json_content_decoded->locale_data->messages as $key => $translations ) {
						if ( ! isset( $json_content_decoded->locale_data->messages->{$key} ) ) {
							$json_content_decoded->locale_data->messages->{$key} = $translations;
						}
					}
				}
			}

			file_put_contents( $dest, wp_json_encode( $json_content_decoded ) );

			$files[] = $dest;
		}

		return $files;
	}

	private function build_po_file( $gp_project, $gp_locale, $set, $entries, $dest ): string|WP_Error {
		$format     = gp_array_get( GP::$formats, 'po' );
		$po_content = $format->print_exported_file( $gp_project, $gp_locale, $set, $entries );

		// Get last updated.
		preg_match( '/^"PO-Revision-Date: (.*)\+\d+\\\n/m', $po_content, $match );
		if ( empty( $match[1] ) ) {
			return new WP_Error( 'invalid_format', '无法解析日期。' );
		}

		file_put_contents( $dest, $po_content );

		return $match[1];
	}

	/**
	 * Builds a PHP file for translations.
	 *
	 * @param GP_Project $gp_project The GlotPress project.
	 * @param GP_Locale $gp_locale The GlotPress locale.
	 * @param GP_Translation_Set $set The translation set.
	 * @param Translation_Entry[] $entries The translation entries.
	 * @param string $dest Destination file name.
	 *
	 * @return bool True on success, false on error.
	 */
	private function build_php_file( $gp_project, $gp_locale, $set, $entries, $dest ) {
		$format  = gp_array_get( GP::$formats, 'php' );
		$content = $format->print_exported_file( $gp_project, $gp_locale, $set, $entries );

		return false !== file_put_contents( $dest, $content );
	}

	private function insert_language_pack( $type, $type_raw, $domain, $language, $version, $updated ): WP_Error|bool {
		global $wpdb;

		$type_raw = $type_raw ?: $type;

		$existing = $wpdb->get_var( $wpdb->prepare(
			'SELECT id FROM language_packs WHERE type = %s AND type_raw = %s AND domain = %s AND language = %s AND version = %s AND updated = %s AND active = 1',
			$type,
			$type_raw,
			$domain,
			$language,
			$version,
			$updated
		) );

		if ( $existing ) {
			return true;
		}

		$now      = current_time( 'mysql', 1 );
		$inserted = $wpdb->insert( 'language_packs', [
			'type'          => $type,
			'type_raw'      => $type_raw,
			'domain'        => $domain,
			'language'      => $language,
			'version'       => $version,
			'updated'       => $updated,
			'active'        => 1,
			'date_added'    => $now,
			'date_modified' => $now,
		] );

		if ( ! $inserted ) {
			return new WP_Error( 'language_pack_not_inserted', '未插入语言包。' );
		}

		// 将相同版本的旧语言包标记为非活动。
		$wpdb->query( $wpdb->prepare(
			'UPDATE language_packs SET active = 0, date_modified = %s WHERE type = %s AND type_raw = %s AND domain = %s AND language = %s AND version = %s AND id <> %d',
			$now,
			$type,
			$type_raw,
			$domain,
			$language,
			$version,
			$wpdb->insert_id
		) );

		return true;
	}
}
