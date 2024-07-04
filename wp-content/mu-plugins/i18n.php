<?php
/**
 * Plugin Name: i18n支持插件
 * Description: 为 WenPai.Org 提供以 GlotPress 为后端而不是以 mo 文件为后端的 i18n 支持
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform;

use DiDom\Document;
use WP_Error;

class i18n {

	const int|float CACHE_EXPIRE = 3 * DAY_IN_SECONDS;

	const string CACHE_GROUP = 'platform-i18n';

	private int $site = SITE_ID_TRANSLATE;

	/**
	 * @var i18n|null
	 */
	private static ?i18n $instance = null;

	/**
	 * @return i18n
	 */
	public static function get_instance(): i18n {
		if ( ! ( self::$instance instanceof i18n ) ) {
			self::$instance = new i18n();
		}

		return self::$instance;
	}

	/**
	 * 翻译给定的原文
	 *
	 * 如果其中包含HTML标签，则只会将标签内容与数据库中的翻译条目对比。比如说<a>hello<b/>word</a>，这条句子会分别匹配hello和world
	 * 特别地，对于包含 HTML 标签的原文，会在 HTML 标签中插入该原文在 GlotPress 项目中的ID
	 *
	 * @param string $cache_key 该原文的缓存键，如果为空则不缓存
	 * @param string $content 要翻译的原文内容
	 * @param string $gp_project_path GlotPress 中管理的项目路径
	 * @param bool $no_wpautop 是否格式化
	 *
	 * @return string|WP_Error
	 */
	public function translate( string $cache_key, string $content, string $gp_project_path, bool $no_wpautop = false ): string|WP_Error {
		if ( empty( $content ) ) {
			return $content;
		}

		$cache_data = empty( $cache_key ) ? '' : wp_cache_get( $cache_key, self::CACHE_GROUP );
		if ( ! empty( $cache_data ) ) {
			return $cache_data;
		}

		$project = $this->get_gp_project( $gp_project_path );
		if ( empty( $project ) ) {
			return $content;
		}

		$originals = $this->get_gp_originals( $project->id );
		if ( empty( $originals ) ) {
			return $content;
		}

		$translation_set_id = $this->get_gp_translation_set_id( $project->id );
		if ( empty( $translation_set_id ) ) {
			return $content;
		}

		uasort( $originals, function ( $a, $b ) {
			$a_len = strlen( $a );
			$b_len = strlen( $b );

			return $a_len == $b_len ? 0 : ( $a_len > $b_len ? - 1 : 1 );
		} );

		//$content = $this->prepare_text( $content );

		$translations = $this->get_gp_translations( $originals, $translation_set_id );
		if ( empty( $translations ) ) {
			return $content;
		}

		$content = $this->do_translate( $content, $originals, $translations );

		if ( ! $no_wpautop ) {
			$content = wpautop( str_replace( '\\', '', $content ), false );
		}

		wp_cache_set( $cache_key, $content, self::CACHE_GROUP, self::CACHE_EXPIRE );

		return $content;
	}

	private function get_gp_project( string $path ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM wp_{$this->site}_gp_projects WHERE path = %s;", $path );

		return $wpdb->get_row( $sql );
	}

	private function get_gp_originals( $project_id ): array {
		global $wpdb;

		if ( empty( $project_id ) ) {
			return array();
		}

		$originals = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, singular FROM wp_{$this->site}_gp_originals WHERE project_id = %d AND status = %s ORDER BY CHAR_LENGTH(singular) DESC",
			$project_id, '+active'
		), ARRAY_A );

		return array_column( $originals, 'singular', 'id' );
	}

	private function get_gp_translation_set_id( int $project_id ): int {
		global $wpdb;

		if ( empty( $project_id ) ) {
			return 0;
		}

		$sql = $wpdb->prepare( "SELECT * FROM wp_{$this->site}_gp_translation_sets WHERE project_id = %s AND locale=%s;", $project_id, 'zh-cn' );

		return $wpdb->get_row( $sql )->id ?? 0;
	}

	/**
	 * 从wordpress.org上抓取的文本经过了一层转移，这个函数旨在将所有转移或未转义的字符都格式化为统一的格式
	 */
	private function prepare_text( string $string ): array|string {
		if ( empty( $string ) ) {
			return $string;
		}

		$string_p = &$string;
		$string_p = preg_replace( "/ rel=\"[\w|\s]*\"/m", '', $string_p );
		$string_p = str_replace( '-', '-', $string_p );
		$string_p = str_replace( '–', '-', $string_p );
		$string_p = str_replace( '&#8211;', '-', $string_p );
		$string_p = str_replace( '’', "'", $string_p );
		$string_p = str_replace( '”', '"', $string_p );
		$string_p = str_replace( '“', '"', $string_p );
		$string_p = str_replace( '&amp;', '&', $string_p );

		return $string_p;
	}

	/**
	 * 执行翻译，并为 HTML 标签插入原文 ID
	 */
	private function do_translate( string $content, array $originals, array $translations ): string {
		$dom = new Document();
		$dom->loadHtml( $content, LIBXML_HTML_NOIMPLIED | LIBXML_BIGLINES | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE | LIBXML_SCHEMA_CREATE );
		foreach ( $originals as $original_id => $original ) {
			$translation = $translations[ $original_id ] ?? $original;

			foreach ( $dom->toElement()->children() as $node ) {
				// 有一些用作列表的 HTML 标签，对于它们，需要取子元素
				$list_tag = array(
					'ol',
					'ul',
				);
				if ( ! empty( $node->getNode()->tagName ) && in_array( $node->getNode()->tagName, $list_tag ) ) {
					foreach ( $node->children() as $child_node ) {
						if ( ! empty( $child_node->innerHtml() ) ) {
							if ( str_contains( $child_node->innerHtml(), $original ) ) {
								$child_node->setAttribute( 'original_id', $original_id );
								$child_node->setInnerHtml( $translation );
							}
						}
					}
				} else {
					if ( ! empty( $node->innerHtml() ) ) {
						if ( str_contains( $node->innerHtml(), $original ) ) {
							$node->setAttribute( 'original_id', $original_id );
							$node->setInnerHtml( $translation );
						}
					}
				}
			}
		}

		return $dom->toElement()->html();
	}

	private function get_gp_translations( $originals, $translation_set_id ): array {
		global $wpdb;

		$translations = [];

		$raw_translations = $wpdb->get_results( $wpdb->prepare(
			"SELECT original_id, translation_0 FROM wp_{$this->site}_gp_translations WHERE original_id IN (" . implode( ', ', array_keys( $originals ) ) . ') AND translation_set_id = %d AND status = %s',
			$translation_set_id, 'current'
		) );

		foreach ( $raw_translations as $translation ) {
			$translations[ $translation->original_id ] = $translation->translation_0;
		}

		return $translations;
	}

}
