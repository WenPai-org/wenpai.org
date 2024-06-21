<?php

namespace Platform\API\API\Themes;

use Platform\API\API\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function Platform\API\request_wporg;
use function Platform\Helper\get_products_from_es_by_browsed;
use function Platform\Helper\search_products_from_es;

class Info extends Base {

	public function __construct() {
		register_rest_route( 'themes/info', '1.1', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'info' ),
		) );
		register_rest_route( 'themes/info', '1.2', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'info' ),
		) );
	}

	public function info( WP_REST_Request $request ): WP_REST_Response {
		$action = $this->get_action( $request->get_params() );
		$params = $this->prepare_params( $action, $request->get_param( 'request' ) );
		switch ( $action ) {
			case 'search':
				$keyword     = '';
				$search_type = '';
				if ( isset( $params['search'] ) ) {
					$keyword     = $params['search'];
					$search_type = 'search';
				} elseif ( isset( $params['tag'] ) ) {
					$keyword     = is_array( $params['tag'] ) ? implode( ' ', $params['tag'] ) : $params['tag'];
					$search_type = 'tag';
				}
				switch_to_blog( SITE_ID_THEMES );
				$products = search_products_from_es( $keyword, $search_type, [], 1, (int) $params['per_page'] );
				restore_current_blog();
				if ( is_wp_error( $products ) ) {
					$data = $this->get_wporg_themes( $request->get_params() );
				} else {
					$data = $this->prepare_search_data( $products, $params );
				}
				break;
			case 'browse':
				switch_to_blog( SITE_ID_THEMES );
				$products = get_products_from_es_by_browsed( $params['browse'], [], 1, (int) $params['per_page'] );
				restore_current_blog();
				if ( is_wp_error( $products ) ) {
					$data = $this->get_wporg_themes( $request->get_params() );
				} else {
					$data = $this->prepare_search_data( $products, $params );
				}
				break;
			default:
				$data = $this->get_wporg_themes( $request->get_params() );
		}

		return new WP_REST_Response( $data );
	}

	private function get_wporg_themes( array $params ): array {
		$data = request_wporg( add_query_arg( $params, '/themes/info/1.2/' ) );
		if ( is_wp_error( $data ) ) {
			return [];
		}

		$data = str_replace( 'downloads.wordpress.org', 'downloads.wenpai.net', $data );
		$data = str_replace( 'profiles.wordpress.org', 'profiles.wenpai.org', $data );
		$data = str_replace( 'ts.w.org', 'ts.wenpai.net', $data );
		$data = str_replace( 's.w.org', 's.wenpai.net', $data );

		$data = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return [];
		}

		return $data;
	}

	/**
	 * 通过参数判断这个请求在干什么
	 * WordPress 的主题 API 格式也非常奇葩，目前只考虑最常用的两种情况。
	 *
	 * 1. 搜索主题
	 *    https://api.wordpress.org/themes/info/1.2/?action=query_themes&request[per_page]=100&request[fields][reviews_url]=1&request[locale]=zh_CN&request[search]=xxx&request[wp_version]=6.3
	 *    https://api.wordpress.org/themes/info/1.2/?action=query_themes&request[per_page]=100&request[fields][reviews_url]=1&request[tag]=full-site-editing&request[locale]=zh_CN&request[wp_version]=6.3
	 *    2 种搜索方式，通过 search 字段搜索关键词，通过 tag 字段搜索标签
	 * 2. 默认筛选
	 *    https://api.wordpress.org/themes/info/1.2/?action=query_themes&request[per_page]=100&request[fields][reviews_url]=1&request[browse]=popular&request[locale]=zh_CN&request[wp_version]=6.3
	 *    通过 browse 字段筛选主题，支持 popular 热门 new 最新
	 *    但是 WordPress 后台还有一个 favorites 收藏，它是通过 user 字段去获取的，这个不考虑。
	 *    WordPress 后台那个区块主题实际上是通过 tag=full-site-editing 来获取的，这个也不考虑。
	 *
	 * @param array $params
	 *
	 * @return string 操作，可选值：search, browse, unknown
	 */
	private function get_action( array $params ): string {
		if ( empty( $params['action'] ) || empty( $params['request'] ) || $params['action'] !== 'query_themes' || isset( $params['request']['user'] ) ) {
			return 'unknown';
		}

		if ( isset( $params['request']['search'] ) || isset( $params['request']['tag'] ) ) {
			return 'search';
		}

		if ( isset( $params['request']['browse'] ) ) {
			return 'browse';
		}

		return 'unknown';
	}

	/**
	 * 根据前面获取到的操作来准备参数
	 *
	 * @param string $action 操作 可选值：search, browse
	 * @param array $params 需要处理的参数，这个参数是请求的 request 数组
	 *
	 * @return array
	 */
	private function prepare_params( string $action, array $params ): array {
		switch ( $action ) {
			case 'search':
				$allowed = array(
					'per_page',
					'locale',
					'search',
					'author',
					'tag',
				);
				break;
			case 'browse':
				$allowed = array(
					'per_page',
					'locale',
					'browse',
				);
				break;
			default:
				return array();
		}

		// per_page 禁止超过 100
		if ( isset( $params['per_page'] ) && $params['per_page'] > 100 ) {
			$params['per_page'] = 100;
		}

		return array_filter( $params, function ( string $param ) use ( $allowed ) {
			return in_array( $param, $allowed );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * 过滤从 ES 中查询到的主题数据
	 *
	 * @param array $products
	 * @param array $params
	 *
	 * @return array
	 */
	private function prepare_search_data( array $products, array $params ): array {
		$total = $products['hits']['total']['value'] ?? 0;
		$pages = ceil( $total / $params['per_page'] );

		switch_to_blog( SITE_ID_THEMES );
		$themes     = [];
		$raw_themes = $products['hits']['hits'] ?? [];
		foreach ( $raw_themes as $raw_theme ) {
			$raw                     = $raw_theme['_source'];
			$theme                   = [];
			$theme['name']           = $raw['post_title'];
			$theme['slug']           = $raw['post_name'];
			$theme['version']        = $raw['meta']['version'][0]['value'] ?? '';
			$theme['preview_url']    = 'https://wpthemes.cn/' . $theme['slug'] . '/';
			$theme['author']         = [
				'user_nicename' => $raw['meta']['author_username'][0]['value'] ?? '',
				'profile'       => "https://profiles.wenpai.org/" . $raw['meta']['author_username'][0]['value'] ?? '',
				'avatar'        => 'https://weavatar.com/avatar/no_photo.webp?s=128&d=letter&letter=' . $raw['meta']['author'][0]['value'] ?? '',
				'display_name'  => $raw['meta']['author'][0]['value'] ?? '',
				'author'        => $raw['meta']['author'][0]['value'] ?? '',
				'author_url'    => "https://profiles.wenpai.org/" . $raw['meta']['author_username'][0]['value'] ?? '',
			];
			$theme['screenshot_url'] = $raw['meta']['banner'][0]['value'] ?? '';
			$theme['rating']         = $raw['meta']['rating'][0]['value'] ?? 100;
			$theme['num_ratings']    = $raw['meta']['num_ratings'][0]['value'] ?? 1;
			$theme['reviews_url']    = 'https://wenpai.org/support/theme/' . $theme['slug'] . '/reviews/';
			/*$theme['downloaded']        = $raw['meta']['total_sales'][0]['value'] ?? 0;
			$theme['last_updated']      = date( "Y-m-d", strtotime( $raw['post_modified_gmt'] ) ) ?? date( "Y-m-d" );
			$theme['last_updated_time'] = date( "Y-m-d H:i:s", strtotime( $raw['post_modified_gmt'] ) ) ?? date( "Y-m-d H:i:s" );
			$theme['creation_time']     = date( "Y-m-d H:i:s", strtotime( $raw['post_date_gmt'] ) ) ?? date( "Y-m-d H:i:s" );*/
			$theme['homepage']    = site_url( $raw['post_name'] . '/' );
			$theme['description'] = $raw['post_content'] ?? '';
			//$theme['download_link']     = get_woo_download_url( $raw['ID'] );

			/*$tags = [];
			foreach ( $raw['terms']['product_tag'] ?? [] as $tag ) {
				$tags[ $tag['slug'] ] = $tag['name'];
			}
			$theme['tags'] = $tags;*/

			$theme['requires']                = $raw['meta']['requires_wordpress_version'][0]['value'] ?? '';
			$theme['requires_php']            = $raw['meta']['requires_php_version'][0]['value'] ?? '';
			$theme['is_commercial']           = false;
			$theme['external_support_url']    = false;
			$theme['is_community']            = false;
			$theme['external_repository_url'] = "";

			$themes[] = $theme;
		}
		restore_current_blog();

		return [
			'info'   => [
				'page'    => 1,
				'pages'   => (int) $pages,
				'results' => (int) $total,
			],
			'themes' => $themes,
		];
	}
}
