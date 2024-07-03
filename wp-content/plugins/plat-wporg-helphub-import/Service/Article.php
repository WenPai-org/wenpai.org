<?php

namespace Platform\Translate\WPOrgHelpHubImport\Service;

use DateTime;
use DateTimeZone;
use DiDom\Document;
use Exception;
use Platform\Logger\Logger;
use WP_CLI;
use WP_Error;
use WP_Http;
use function Platform\Translate\WPOrgHelpHubImport\compress_html;
use function Platform\Translate\WPOrgHelpHubImport\prepare_w_org_string;

defined( 'ABSPATH' ) || exit;

class Article {
	private array $cat_map = array();

	public function __construct() {
		//add_action( 'init', array( $this, 'job' ) );
	}

	public function job() {
		/**
		 * 初始化 w.org 上分类 ID 与本地的分类 ID 的对照关系
		 */
		$this->cat_map = $this->update_category();

		$total_pages = 100;
		for ( $page = 1; $page <= $total_pages; $page ++ ) {
			$url  = "https://wpmirror.com/documentation/wp-json/wp/v2/articles?per_page=100&page=$page";
			$data = $this->remote_get( $url );
			if ( is_wp_error( $data ) ) {
				Logger::error( Logger::DOCUMENT, $data->get_error_message(), array(
					'url' => $url,
				) );

				return;
			}

			foreach ( $data as $item ) {
				$cat_ids = array_map( function ( $item ) {
					return $this->cat_map[ $item ];
				}, $item->category );

				$r = $this->insert( $item->id, $item->modified_gmt, $item->slug, $item->title?->rendered, $item->menu_order, $item->content?->rendered, $item->excerpt?->rendered, 0, $cat_ids );
				if ( is_wp_error( $r ) ) {
					Logger::error( Logger::DOCUMENT, '创建文章失败：' . $r->get_error_message() );
				}

				// 导入后顺便把内容复制一份到 meta，方便之后替换翻译用
				//update_post_meta( $r, 'original_content', $item->content?->rendered );

				// 文档导入后为翻译平台安排一个计划任务来将文档同步过去
				switch_to_blog( SITE_ID_TRANSLATE );
				wp_schedule_single_event( time() + 1, 'plat_gp_helphub_import', array(
					'name'    => $item->title?->rendered,
					'slug'    => $item->slug,
					'content' => $item->content?->rendered,
				) );
				restore_current_blog();
				WP_CLI::line( '导入文章：' . $item->title?->rendered );
			}
		}

	}

	private function update_category(): array {
		$url  = "https://wpmirror.com/documentation/wp-json/wp/v2/category?per_page=100";
		$data = $this->remote_get( $url );
		if ( is_wp_error( $data ) ) {
			Logger::error( Logger::DOCUMENT, $data->get_error_message(), array(
				'url' => $url,
			) );

			return array();
		}

		$cat_map = array();
		foreach ( $data as $item ) {
			if ( ! function_exists( 'wp_insert_category' ) ) {
				require ABSPATH . '/wp-admin/includes/taxonomy.php';
			}

			// 替换关键字
			$item->name = prepare_w_org_string( $item->name );
			$item->slug = str_replace( 'wordpress', 'wenpai', $item->slug );

			if ( ! get_category_by_slug( $item->slug ) ) {
				$args = array(
					'taxonomy'          => 'category',
					'cat_name'          => $item->name,
					'category_nicename' => $item->slug,
				);
				wp_insert_category( $args );
			}

			$r = get_category_by_slug( $item->slug );

			$cat_map[ $item->id ] = $r?->term_id;
		}

		return $cat_map;
	}

	private function remote_get( string $url ): array|WP_Error {
		$args = array(
			'timeout' => 600,
		);

		$r = wp_remote_get( $url, $args );

		if ( is_wp_error( $r ) ) {
			return new WP_Error( 'docs_import_error', '从 w.org 抓取文档失败：' . $r->get_error_message() );
		}

		$status_code = wp_remote_retrieve_response_code( $r );
		if ( WP_Http::OK !== $status_code ) {
			return new WP_Error( 'docs_import_error', '从 w.org 抓取文档失败，接口返回状态码：' . $status_code );
		}

		$body = wp_remote_retrieve_body( $r );

		return json_decode( $body );
	}

	private function insert( int $id, string $modified_gmt, string $post_name, string $title, int $order, string $content = '', string $excerpt = '', int $post_parent = 0, array $cat_ids = array() ): int|WP_Error {
		$is_exist = get_post( $id );
		if ( ! $is_exist ) {
			$this->insert_empty_post( $id );
		}

		// 判断时间是否相等，相等就不用更新了
		/*if ( $is_exist && $is_exist->post_date_gmt === date( 'Y-m-d H:i:s', strtotime( $modified_gmt ) ) ) {
			return $is_exist->ID;
		}*/

		// 使用平台的全局 DOM 处理库先预处理一遍，因为这个鸟库会转义一些 HTML 实体，如果不预处理的话，将来经过这个库处理的字符串就和原字符串匹配不上了
		$dom = new Document( $content );

		$body = $dom->find( 'body' );

		$content = $body[0]->html();

		// 去除 DOM 处理库自动添加的 Body 标签
		if ( preg_match( '|^<body>([\s\S]*?)</body>$|', $content, $matches ) ) {
			if ( ! empty( $matches[1] ) ) {
				$content = $matches[1];
			}
		}

		$content = compress_html( $content );
		$content = prepare_w_org_string( $content );

		$title     = prepare_w_org_string( $title );
		$post_name = prepare_w_org_string( $post_name );

		// 时间处理成中国
		try {
			$modified = new DateTime( $modified_gmt, new DateTimeZone( 'UTC' ) );
			$modified->setTimezone( new DateTimeZone( 'Asia/Shanghai' ) );
		} catch ( Exception $e ) {
			return new WP_Error( 'docs_import_error', '时间格式化失败：' . $e->getMessage() );
		}

		$args = array(
			'ID'            => $id,
			'post_author'   => BOT_USER_ID,
			'post_content'  => $content,
			'post_title'    => $title,
			'post_name'     => $post_name,
			'post_excerpt'  => $excerpt,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_parent'   => $post_parent,
			'menu_order'    => $order,
			'post_category' => $cat_ids,
			'post_date'     => $modified->format( 'Y-m-d H:i:s' ),
			'post_date_gmt' => date( 'Y-m-d H:i:s', strtotime( $modified_gmt ) ),
		);

		return wp_insert_post( $args );
	}

	/**
	 * 创建 ID 为给定值的空文章
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	private function insert_empty_post( int $id ): bool {
		global $wpdb;

		$wpdb->insert( $wpdb->posts, array(
				'ID' => $id,
			)
		);

		return true;
	}
}
