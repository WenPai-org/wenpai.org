<?php

namespace Platform\API\API\Plugins;

use Platform\API\API\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function Platform\API\request_wporg;
use function Platform\Helper\get_products_from_es_by_browsed;
use function Platform\Helper\search_products_from_es;

class Info extends Base {

	public function __construct() {
		register_rest_route( 'plugins/info', '1.1', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'info' ),
		) );
		register_rest_route( 'plugins/info', '1.2', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'info' ),
		) );

		register_rest_route( 'plugins/info', '1.0/(?P<slug>.+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'old_info' ),
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
				} elseif ( isset( $params['author'] ) ) {
					$keyword     = $params['author'];
					$search_type = 'author';
				} elseif ( isset( $params['tag'] ) ) {
					$keyword     = is_array( $params['tag'] ) ? implode( ' ', $params['tag'] ) : $params['tag'];
					$search_type = 'tag';
				}
				switch_to_blog( SITE_ID_PLUGINS );
				$products = search_products_from_es( $keyword, $search_type, [], (int) $params['page'], (int) $params['per_page'] );
				restore_current_blog();
				if ( is_wp_error( $products ) ) {
					$data = $this->get_wporg_plugins( $request->get_params() );
				} else {
					$data = $this->prepare_search_data( $products, $params );
				}
				break;
			case 'browse':
				switch_to_blog( SITE_ID_PLUGINS );
				$products = get_products_from_es_by_browsed( $params['browse'], [], (int) $params['page'], (int) $params['per_page'] );
				restore_current_blog();
				if ( is_wp_error( $products ) ) {
					$data = $this->get_wporg_plugins( $request->get_params() );
				} else {
					$data = $this->prepare_search_data( $products, $params );
				}
				break;
			case 'hot_tag' :
				$tags = wp_cache_get( 'api_hot_tags', 'platform' );
				if ( empty( $tags ) ) {
					switch_to_blog( SITE_ID_PLUGINS );

					$tags = get_terms( array(
						'taxonomy' => 'post_tag',
						'orderby'  => 'count',
						'order'    => 'DESC',
						'number'   => 100,
					) );

					restore_current_blog();
					wp_cache_set( 'api_hot_tags', $tags, 'platform', 7200 );
				}

				$data = [];
				foreach ( $tags as $tag ) {
					$data[ $tag->slug ] = [
						'name'  => $tag->name,
						'slug'  => $tag->slug,
						'count' => $tag->count,
					];
				}

				if ( empty( $data ) ) {
					$data = $this->get_wporg_plugins( $request->get_params() );
				}

				break;
			default:
				$data = $this->get_wporg_plugins( $request->get_params() );
		}

		return new WP_REST_Response( $data );
	}

	public function old_info( WP_REST_Request $request ): WP_REST_Response {
		$slug = $request->get_param( 'slug' );
		$data = request_wporg( add_query_arg( $_GET, '/plugins/info/1.0/' . $slug ) );
		if ( is_wp_error( $data ) ) {
			$args = array(
				'message' => $data->get_error_message(),
			);
			$this->error( $args );
		}

		$data = str_replace( 'downloads.wordpress.org', 'downloads.wenpai.net', $data );
		$data = str_replace( 'profiles.wordpress.org', 'profiles.wenpai.org', $data );
		$data = str_replace( 'ps.w.org', 'ps.wenpai.net', $data );
		$data = str_replace( 's.w.org', 's.wenpai.net', $data );

		return new WP_REST_Response( $data );
	}

	private function get_wporg_plugins( array $params ): array {
		$data = request_wporg( add_query_arg( $params, '/plugins/info/1.2/' ) );
		if ( is_wp_error( $data ) ) {
			return [];
		}

		$data = str_replace( 'downloads.wordpress.org', 'downloads.wenpai.net', $data );
		$data = str_replace( 'profiles.wordpress.org', 'profiles.wenpai.org', $data );
		$data = str_replace( 'ps.w.org', 'ps.wenpai.net', $data );
		$data = str_replace( 's.w.org', 's.wenpai.net', $data );

		$data = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return [];
		}

		return $data;
	}

	/**
	 * 通过参数判断这个请求在干什么
	 * WordPress 的插件 API 格式非常奇葩，目前只考虑最常用的两种情况。
	 *
	 * 1. 搜索插件
	 *    https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[page]=1&request[per_page]=36&request[locale]=zh_CN&request[search]=cdn&request[wp_version]=6.3
	 *    https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[page]=1&request[per_page]=36&request[locale]=zh_CN&request[author]=haozi&request[wp_version]=6.3
	 *    https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[page]=1&request[per_page]=36&request[locale]=zh_CN&request[tag]=cdn&request[wp_version]=6.3
	 *    3 种搜索方式，通过 search 字段搜索关键词，通过 author 字段搜索作者，通过 tag 字段搜索标签
	 * 2. 默认筛选
	 *    https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[page]=1&request[per_page]=36&request[locale]=zh_CN&request[browse]=popular&request[wp_version]=6.3
	 *    通过 browse 字段筛选插件，支持 featured 特色 popular 热门 recommended 推荐 beta 测试
	 *    但是 WordPress 后台还有一个 favorites 收藏，它是通过 user 字段去获取的，这个不考虑。
	 *
	 * @param array $params
	 *
	 * @return string 操作，可选值：search, browse, unknown, hot_tag
	 */
	private function get_action( array $params ): string {
		if ( isset( $params['action'] ) && $params['action'] === 'hot_tags' ) {
			return 'hot_tag';
		}
		if ( empty( $params['action'] ) || empty( $params['request'] ) || $params['action'] !== 'query_plugins' || isset( $params['request']['user'] ) ) {
			return 'unknown';
		}

		if ( isset( $params['request']['search'] ) || isset( $params['request']['author'] ) || isset( $params['request']['tag'] ) ) {
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
	 * @param mixed $params 需要处理的参数，这个参数是请求的 request 数组
	 *
	 * @return array
	 */
	private function prepare_params( string $action, mixed $params ): array {
		switch ( $action ) {
			case 'search':
				$allowed = array(
					'page',
					'per_page',
					'locale',
					'search',
					'author',
					'tag',
				);
				break;
			case 'browse':
				$allowed = array(
					'page',
					'per_page',
					'locale',
					'browse',
				);
				break;
			default:
				return array();
		}

		// per_page 禁止超过 40
		if ( isset( $params['per_page'] ) && $params['per_page'] > 40 ) {
			$params['per_page'] = 40;
		}

		return array_filter( $params, function ( string $param ) use ( $allowed ) {
			return in_array( $param, $allowed );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * 过滤从 ES 中查询到的插件数据
	 *
	 * @param array $products
	 * @param array $params
	 *
	 * @return array
	 */
	private function prepare_search_data( array $products, array $params ): array {
		$total = $products['hits']['total']['value'] ?? 0;
		$page  = $params['page'];
		$pages = ceil( $total / $params['per_page'] );

		switch_to_blog( SITE_ID_PLUGINS );
		$plugins     = [];
		$raw_plugins = $products['hits']['hits'] ?? [];
		foreach ( $raw_plugins as $raw_plugin ) {
			$raw                      = $raw_plugin['_source'];
			$plugin                   = [];
			$plugin['name']           = $raw['post_title'];
			$plugin['slug']           = $raw['post_name'];
			$plugin['version']        = $raw['meta']['version'][0]['value'] ?? '';
			$author                   = $raw['meta']['author'][0]['value'] ?? '';
			$author_username          = $raw['meta']['author_username'][0]['value'] ?? '';
			$plugin['author']         = '<a target="_blank" href="https://profiles.wenpai.org/' . $author_username . '">' . $author . '</a>';
			$plugin['author_profile'] = "https://profiles.wenpai.org/" . $author_username;
			$plugin['requires']       = $raw['meta']['requires_wordpress_version'][0]['value'] ?? '';
			$plugin['tested']         = $raw['meta']['tested_wordpress_version'][0]['value'] ?? '';
			$plugin['requires_php']   = $raw['meta']['requires_php_version'][0]['value'] ?? '';
			//$plugin['requires_plugins']         = [];
			$plugin['rating'] = $raw['meta']['rating'][0]['value'] ?? 100;
			//$plugin['ratings']                  = [];
			$plugin['num_ratings'] = $raw['meta']['num_ratings'][0]['value'] ?? 1;
			//$plugin['support_threads']          = 0;
			//$plugin['support_threads_resolved'] = 0;
			$plugin['active_installs']   = $raw['meta']['views'][0]['value'] ?? 0;
			$plugin['downloaded']        = $raw['meta']['views'][0]['value'] ?? 0;
			$plugin['last_updated']      = date( "Y-m-d H:i:s", strtotime( $raw['post_modified_gmt'] ) ) ?? date( "Y-m-d H:i:s" );
			$plugin['added']             = date( "Y-m-d H:i:s", strtotime( $raw['post_date_gmt'] ) ) ?? date( "Y-m-d H:i:s" );
			$plugin['homepage']          = site_url( $raw['post_name'] . '/' );
			$plugin['short_description'] = $raw['post_excerpt'] ?? '';
			$plugin['description']       = $raw['post_content'] ?? '';
			$plugin['download_link']     = $raw['meta']['download_url'][0]['value'] ?? "";

			$tags = [];
			foreach ( $raw['terms']['post_tag'] ?? [] as $tag ) {
				$tags[ $tag['slug'] ] = $tag['name'];
			}
			$plugin['tags'] = $tags;

			$plugin['donate_link'] = '';
			$plugin['icons']       = [
				'1x' => get_the_guid( $raw['thumbnail']['ID'] ) ?? 'https://weavatar.com/avatar/no_photo.webp?s=128&d=letter&letter=' . $raw['post_title'] ?? '',
				'2x' => get_the_guid( $raw['thumbnail']['ID'] ) ?? 'https://weavatar.com/avatar/no_photo.webp?s=256&d=letter&letter=' . $raw['post_title'] ?? '',
			];
			$plugins[]             = $plugin;
		}
		restore_current_blog();

		return [
			'info'    => [
				'page'    => (int) $page,
				'pages'   => (int) $pages,
				'results' => (int) $total,
			],
			'plugins' => $plugins,
		];
	}
}
