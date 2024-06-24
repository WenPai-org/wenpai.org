<?php
/**
 * Plugin Name: Helper
 * Description: 一组全局帮助函数
 * Version: 1.0
 * Author: 如来
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Helper;

use WP_Error;
use WP_Http;

/**
 * 通过解析一组category_ids来分析当前文章的类别
 *
 * 类型包括：插件、主题、块模板
 *
 * @param array $category_ids
 *
 * @return string
 */
function get_product_type_by_category_ids( array $category_ids ): string {
	$category = get_term_by( 'id', $category_ids[0] ?? '', 'category' );

	if ( is_wp_error( $category ) ) {
		return '';
	}

	return $category->slug;
}

/**
 * 通过解析一组categories来分析当前产品的类别
 *
 * 类型包括：插件、主题、小程序、块模板
 *
 * @param array $categories
 *
 * @return string
 */
function get_product_type_by_categories( array $categories ): string {
	$category_ids = array();

	foreach ( $categories as $category ) {
		if ( is_array( $category ) ) {
			$category_ids[] = $category['term_id'];
		} else {
			$category_ids[] = $category->term_id;
		}
	}

	return get_product_type_by_category_ids( $category_ids );
}

/**
 * 检查是否存在某个GlotPress项目
 *
 * @param string $slug 项目Slug
 * @param string $type 项目类型：plugins 或 themes
 *
 * @return bool 存在返回true，否则返回false
 */
function exist_gp_project( string $slug, string $type ): bool {
	global $wpdb;

	$path = $type . '/' . $slug;
	$sql  = $wpdb->prepare( 'SELECT id FROM wp_' . SITE_ID_TRANSLATE . '_gp_projects WHERE slug = %s AND path = %s;', $slug, $path );

	return ! empty( $wpdb->get_row( $sql ) );
}

/**
 * 从ES中批量检索一组产品
 *
 * @param array $slugs 产品 Slug 数组
 * @param array $fields 要输出的字段
 */
function get_products_from_es( array $slugs, array $fields = [] ): WP_Error|array {
	$body = [
		'track_total_hits' => true,
		'query'            => array(
			'bool' => array(
				'minimum_should_match' => 1,
				'should'               => array(
					array(
						'terms' => array(
							'post_name.raw' => $slugs
						)
					)
				),
			)
		),
		'size'             => 500,
	];

	return fetch_result_from_store_es( $body, $fields );
}

/**
 * 从ES中模糊搜索一组文章
 *
 * @param string $keyword 关键词
 * @param string $search_type 搜索类型 关键词 | 作者 | 标签
 * @param array $fields 要输出的字段
 * @param int $page 页码
 * @param int $per_page 每页数量
 */
function search_products_from_es( string $keyword, string $search_type, array $fields = array(), int $page = 1, int $per_page = 10 ): WP_Error|array {
	switch ( $search_type ) {
		case 'search':
			$query = array(
				'bool' => array(
					'minimum_should_match' => 1,
					'should'               => array(
						array(
							'multi_match' => array(
								'query' => $keyword,
							)
						)
					)
				)
			);
			break;
		case 'author':
			$query = array(
				'bool' => array(
					'minimum_should_match' => 1,
					'should'               => array(
						array(
							'multi_match' => array(
								'query'  => $keyword,
								'fields' => array(
									'post_author.display_name',
								),
							)
						)
					)
				)
			);
			break;
		case 'tag':
			$query = array(
				'bool' => array(
					'minimum_should_match' => 1,
					'should'               => array(
						array(
							'match' => array(
								'post_title'               => array(
									'query' => $keyword,
									'boost' => 2,
								),
								'post_content'             => array(
									'query' => $keyword,
									'boost' => 1.5,
								),
								'terms.post_tag.name'      => array(
									'query' => $keyword,
									'boost' => 1,
								),
								'post_author.display_name' => array(
									'query' => $keyword,
									'boost' => 1,
								),
							),
						)
					)
				)
			);
			break;
		default:
			return new WP_Error( '你在搜个啥？' );
	}

	$body = array(
		'track_total_hits' => true,
		'query'            => $query,
		'from'             => ( $page - 1 ) * $per_page,
		'size'             => $per_page,
	);

	return fetch_result_from_store_es( $body, $fields );
}

/**
 * 从ES中返回一组按指定排序的产品
 *
 * @param string $browse 排序类型 featured 特色 popular 热门 recommended 推荐
 * @param array $fields 要输出的字段
 * @param int $page 页码
 * @param int $per_page 每页数量
 */
function get_products_from_es_by_browsed( string $browse, array $fields = array(), int $page = 1, int $per_page = 10 ): WP_Error|array {
	switch ( $browse ) {
		case 'popular':
		case 'featured':
		case 'recommended':
			$sort = array(
				array(
					'meta.views.long' => array(
						'order' => 'desc',
					)
				),
			);
			break;
		case 'new':
			$sort = array(
				array(
					'post_modified_gmt' => array(
						'order' => 'desc',
					)
				),
			);
			break;
		default:
			return new WP_Error( '你在搜个啥？' );
	}

	$body = array(
		'track_total_hits' => true,
		'sort'             => $sort,
		'from'             => ( $page - 1 ) * $per_page,
		'size'             => $per_page,
	);

	return fetch_result_from_store_es( $body, $fields );
}

/**
 * 获取 ES 的市场搜索结果
 *
 * @param array $body
 * @param array $fields
 *
 * @return array|WP_Error
 */
function fetch_result_from_store_es( array $body, array $fields ): array|WP_Error {
	$body = wp_json_encode( $body );

	$index_name = match ( get_current_blog_id() ) {
		SITE_ID_THEMES => 'wenpaiorgthemes-post-4',
		SITE_ID_PLUGINS => 'wenpaiorgplugins-post-3',
		default => '',
	};
	if ( empty( $index_name ) ) {
		return new WP_Error( '站点ID参数无匹配索引名' );
	}

	$request = wp_remote_post(
		'http://127.0.0.1:9200/' . $index_name . '/_search' . ( empty( $fields ) ? '' : ( '?_source_includes=' . join( ',', $fields ) ) ),
		[
			'timeout' => 10,
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body'    => $body,
		]
	);

	if ( is_wp_error( $request ) ) {
		return $request;
	}

	if ( WP_Http::OK !== wp_remote_retrieve_response_code( $request ) ) {
		return new WP_Error( 'ES 未响应 200 状态码: ' . wp_remote_retrieve_body( $request ) );
	}

	$body = wp_remote_retrieve_body( $request );

	return json_decode( $body, true );
}

/**
 * 判断字符串是否是或包含中文
 */
function is_chinese( string $str ): bool {
	if ( preg_match( '/[\x{4e00}-\x{9fa5}]/u', $str ) > 0 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * 执行一个 Shell 命令
 *
 * @param string $command
 * @param bool $get_return
 *
 * @return WP_Error|string|bool
 */
function execute_command( string $command, bool $get_return = false ): WP_Error|string|bool {
	exec( $command, $output, $return_var );

	if ( $return_var ) {
		return new WP_Error( $return_var, '执行命令时出错。', $output );
	}

	return $get_return ? join( "\n", $output ) : true;
}
