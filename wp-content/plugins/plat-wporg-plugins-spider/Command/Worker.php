<?php

namespace Platform\Plugins\WPOrgSpider\Command;

use Carbon\Carbon;
use DiDom\Document;
use DiDom\Query;
use Exception;
use EXMAGE_WP_IMAGE_LINKS;
use Platform\Logger\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use WP_CLI_Command;
use WP_CLI;

class Worker extends WP_CLI_Command {

	public function __construct() {
		parent::__construct();
		try {
			WP_CLI::add_command( 'platform wporg_plugins_update', __NAMESPACE__ . '\Worker' );
		} catch ( Exception $e ) {
			Logger::error( Logger::Plugins, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * 全量更新
	 * @return void
	 */
	public function force_run(): void {
		while ( ob_get_level() ) {
			ob_end_flush();
		}

		$slugs = [];

		WP_CLI::line( '开始获取远程全部slug' );

		$body = wp_remote_get( "https://plugins.svn.wordpress.org/", [
			'timeout' => 60,
			"headers" => [
				"User-Agent" => "WordPress"
			]
		] );
		if ( is_wp_error( $body ) ) {
			Logger::error( Logger::Plugins, '获取远程全部slug失败' );
		}

		$document = new Document( wp_remote_retrieve_body( $body ) );

		try {
			$slugs = $document->find( '/html/body/ul/li/a/@href', Query::TYPE_XPATH );
		} catch ( Exception $e ) {
			Logger::error( Logger::Plugins, '从 Svn 页面提取 slug 失败', [
				'error' => $e->getMessage()
			] );
		}

		// 去除 Slug 结尾的斜杠
		$slugs = array_map( function ( $value ) {
			return str_replace( '/', '', $value );
		}, $slugs );

		WP_CLI::line( '获取远程全部slug完成' );

		global $wpdb;
		define( 'WP_IMPORTING', true );
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		remove_action( 'do_pings', 'do_all_pings', 10, 1 );
		add_filter( 'pre_wp_unique_post_slug',
			function ( $override_slug, $slug, $post_id, $post_status, $post_type, $post_parent ) {
				return $slug;
			}, 10, 6
		);
		$wpdb->query( 'SET autocommit = 0;' );

		$chunks = array_chunk( $slugs, 40 );// 并发数

		foreach ( $chunks as $chunk_index => $chunk ) {
			$infos = $this->fetch_remote_plugins( $chunk );

			foreach ( $infos as $index => $info ) {
				if ( empty( $info ) ) {
					continue;
				}

				// 动手前先判断一下安装数和更新日期，安装数小于1000且更新时间2年以前的直接放弃。
				if ( $info['download_count'] < 1000 && Carbon::parse( $info['updated_at'] )->diffInYears( Carbon::now() ) > 2 ) {
					Logger::info( Logger::Plugins, "跳过 {$info['slug']}" );
					continue;
				}

				try {
					$this->update_post( $chunk[ $index ], $info );
				} catch ( Exception $e ) {
					Logger::error( Logger::Plugins, '爬虫运行出错', array(
						'slug'  => $chunk[ $index ],
						'info'  => $info,
						'error' => $e->getMessage(),
					) );
				}
			}

			$wpdb->query( 'COMMIT;' );

			WP_CLI::line( '当前位于: ' . $chunk_index . ' | 总共: ' . count( $chunks ) );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		WP_CLI::line( '全量更新完成' );
	}

	/**
	 * 增量更新
	 * @return void
	 */
	public function run(): void {
		$lastRun = get_option( 'wporg_spider_last_run' );

		if ( empty( $lastRun ) ) {
			$lastRun = Carbon::now()->subDays( 2 )->toAtomString();
		}

		$startTime = Carbon::parse( $lastRun )->toAtomString();
		$endTime   = Carbon::now()->toAtomString();

		$slugs = [];

		if ( class_exists( 'WP_CLI' ) ) {
			WP_CLI::line( '增量更新开始[' . $startTime . ' - ' . $endTime . ']' );
		}

		$output    = shell_exec( "svn log -v https://plugins.svn.wordpress.org/ -r \{$startTime\}:\{$endTime\}" );
		$outputRaw = explode( "\n", $output );

		$i = 0;

		foreach ( $outputRaw as $item ) {
			preg_match( '|\s[A-Z]\s/([^/]+)|', $item, $matches );

			if ( ! empty( $matches[1] ) ) {
				// 为了防止重复项，这里使用键来存储对应的 Slug
				$slugs[ $matches[1] ] = $i;
				$i ++;
			}
		}

		$slugs  = array_flip( $slugs );
		$chunks = array_chunk( $slugs, 10 );// 并发数

		foreach ( $chunks as $chunk_index => $chunk ) {
			$infos = $this->fetch_remote_plugins( $chunk );

			foreach ( $infos as $index => $info ) {
				if ( $info === false ) {
					// 如果返回 false，说明这个插件已经被下架了，直接删除对应的文章
					global $wpdb;
					$r = $wpdb->get_var( $wpdb->prepare( "select ID from {$wpdb->prefix}posts where post_name=%s limit 1;", $chunk[ $index ] ) );
					if ( ! empty( $r ) ) {
						wp_delete_post( $r, true );
					}

					// 触发插件删除后的钩子
					do_action( 'platform_wporg_plugins_deleted', $chunk[ $index ] );

					continue;
				}
				if ( empty( $info ) ) {
					Logger::error( Logger::Plugins, '爬虫运行返回空数据', array(
						'slug' => $chunk[ $index ],
					) );
					continue;
				}

				try {
					$this->update_post( $chunk[ $index ], $info );
				} catch ( Exception $e ) {
					Logger::error( Logger::Plugins, '爬虫运行出错', array(
						'slug'  => $chunk[ $index ],
						'info'  => $info,
						'error' => $e->getMessage(),
					) );
				}
			}

			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::line( '当前位于: ' . $chunk_index . ' | 总共: ' . count( $chunks ) );
			}
		}

		update_option( 'wporg_spider_last_run', $endTime );

		if ( class_exists( 'WP_CLI' ) ) {
			WP_CLI::line( '增量更新完成[' . $endTime . ']' );
		}
	}

	public function run_single( $args, $assoc_args ) {
		if ( empty( $assoc_args['slug'] ) ) {
			WP_CLI::line( '你需要给出要导入的项目 Slug' );
			exit;
		}

		$slugs = [ $assoc_args['slug'] ];
		$infos = $this->fetch_remote_plugins( $slugs );

		foreach ( $infos as $info ) {
			if ( $info === false ) {
				WP_CLI::line( '已下架：' . $assoc_args['slug'] );
				continue;
			}
			if ( empty( $info ) ) {
				WP_CLI::line( '空数据：' . $assoc_args['slug'] );
				continue;
			}

			try {
				$this->update_post( $assoc_args['slug'], $info );
			} catch ( Exception $e ) {
				WP_CLI::line( '出错：' . $e->getMessage() );
			}
		}

		WP_CLI::line( '搞定！' );
	}

	/**
	 * 更新文章
	 *
	 * @param string $slug
	 * @param array $info
	 *
	 * @return void
	 * @throws Exception
	 */
	private function update_post( string $slug, array $info ): void {
		global $wpdb;

		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( empty( $post ) ) {
			$post_id = wp_insert_post( [
				'post_title'   => $info['name'],
				'post_name'    => $slug,
				'post_content' => $info['description'] ?? '',
				'post_excerpt' => $info['short_description'] ?? '',
				'post_status'  => 'publish',
				'post_author'  => BOT_USER_ID,
			] );
			if ( is_wp_error( $post_id ) ) {
				throw new Exception( $post_id->get_error_message() );
			} else {
				$post = get_post( $post_id );
			}
		}

		set_post_thumbnail( $post->ID, $this->update_image( $info['icon'], 'Logo' ) );

		wp_update_post( [
			'ID'           => $post->ID,
			'post_title'   => $info['name'],
			'post_content' => $info['description'] ?? '',
			'post_excerpt' => $info['short_description'] ?? '',
			'post_status'  => 'publish',
			'post_author'  => BOT_USER_ID,
		], true );

		$tags = array();
		foreach ( $info['tags'] as $tag ) {
			$term = get_term_by( 'slug', sanitize_title( $tag ), 'post_tag', ARRAY_A );
			if ( ! $term || is_wp_error( $term ) ) {
				$term = wp_insert_term( $tag, 'post_tag' );
				if ( is_wp_error( $term ) ) {
					Logger::error( Logger::Plugins, '创建标签失败', [ 'error' => $term->get_error_message() ] );
					continue;
				}
				$term = get_term_by( 'id', $term['term_id'], 'post_tag', ARRAY_A );
			}

			if ( is_wp_error( $term ) ) {
				throw new Exception( $term->get_error_message() );
			}

			if ( ! $term ) {
				throw new Exception( "创建标签失败，失败的标签: {$tag}" );
			}

			$tags[] = $term['term_id'];
		}
		wp_set_post_terms( $post->ID, $tags, 'post_tag' );

		update_post_meta( $post->ID, 'author', $info['author'] );
		update_post_meta( $post->ID, 'author_username', $info['author_username'] );
		update_post_meta( $post->ID, 'views', $info['download_count'] );
		update_post_meta( $post->ID, 'version', $info['version'] );
		update_post_meta( $post->ID, 'instruction', $info['instruction'] );
		update_post_meta( $post->ID, 'faq', $info['faq'] );
		update_post_meta( $post->ID, 'changelog', $info['changelog'] );
		update_post_meta( $post->ID, 'requires_wordpress_version', $info['requires_wordpress_version'] );
		update_post_meta( $post->ID, 'tested_wordpress_version', $info['tested_wordpress_version'] );
		update_post_meta( $post->ID, 'requires_php_version', $info['requires_php_version'] );
		update_post_meta( $post->ID, 'banner', $info['banner'] );
		update_post_meta( $post->ID, 'download_url', $info['download_url'] );
		update_post_meta( $post->ID, 'rating', $info['rating'] );
		update_post_meta( $post->ID, 'num_ratings', $info['num_ratings'] );

		$image_ids = [];
		foreach ( $info['screenshots'] as $screenshot ) {
			$image_ids[] = $this->update_image( $screenshot['src'], $screenshot['caption'] );
		}
		update_post_meta( $post->ID, 'screenshots', $image_ids );

		$wpdb->update( $wpdb->posts,
			[
				'post_date'         => $info['created_at']->addHours( 8 )->toDateTimeString(),
				'post_date_gmt'     => $info['created_at']->toDateTimeString(),
				'post_modified'     => $info['updated_at']->addHours( 8 )->toDateTimeString(),
				'post_modified_gmt' => $info['updated_at']->toDateTimeString()
			],
			[ 'ID' => $post->ID ] );

		// 创建论坛
		//$this->create_bbpress_forum_for_post( $post->ID );

		// 手工触发 save_post 钩子，以使诸如 EP 等插件监控到插件的更新
		do_action( 'save_post', $post->ID, $post, true );
		do_action( 'wp_insert_post', $post->ID, $post, true );

		// 触发一个自定义钩子，方便在插件更新后执行一些平台特有的操作，比如刷新 CDN、更新翻译
		do_action( 'platform_wporg_plugins_updated', $info['slug'] );

		unset( $post );
	}

	/**
	 * 更新图片到媒体库
	 *
	 * @param string $url
	 * @param string $alt
	 *
	 * @return int
	 */
	private function update_image( string $url, string $alt = '' ): int {
		add_filter( 'exmage_get_supported_image_sizes', function () {
			return [];
		} );
		$image = EXMAGE_WP_IMAGE_LINKS::add_image( $url, $image_id, $alt );

		return (int) $image['id'];
	}


	/**
	 * 获取远程插件信息
	 *
	 * @param array $slugMap
	 *
	 * @return bool|array
	 */
	private function fetch_remote_plugins( array $slugMap ): bool|array {
		$url    = "https://api.wordpress.org/plugins/info/1.2/";
		$query  = "action=plugin_information&request[fields][short_description]=1&request[fields][downloaded]=1&request[fields][icons]=1&request[fields][contributors]=0&request[fields][versions]=0&request[slug]=";
		$client = new Client( [ 'base_uri' => $url ] );

		foreach ( $slugMap as $index => $slug ) {
			$slugMap[ $index ] = $client->getAsync( '', [ 'query' => $query . $slug, 'timeout' => 10 ] );
		}

		$responses = Promise\Utils::settle( $slugMap )->wait();
		$data      = [];

		foreach ( $slugMap as $index => $request ) {
			if ( $responses[ $index ]['state'] === 'fulfilled' ) {
				$body   = $responses[ $index ]['value']->getBody()->getContents();
				$status = $responses[ $index ]['value']->getStatusCode();

				if ( $status !== 200 ) {
					$data[ $index ] = false;
				} else {
					if ( empty( $body ) ) {
						Logger::error( Logger::Plugins, '获取远程插件信息失败', [ 'url' => $url ] );
						$data[ $index ] = [];
					} else {
						$data[ $index ] = $this->parse_plugin_info( $body );
					}
				}
			} else {
				$data[ $index ] = false;
			}
		}

		return $data;
	}

	/**
	 * 解析插件信息
	 *
	 * @param string $html
	 *
	 * @return bool|array
	 */
	private function parse_plugin_info( string $html ): bool|array {
		$plugin = json_decode( $html, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		$name                       = html_entity_decode( $plugin['name'] );
		$slug                       = $plugin['slug'];
		$version                    = $plugin['version'];
		$author                     = preg_match( '/<a href=".*?">(.*?)<\/a>/', $plugin['author'],
			$matches ) ? $matches[1] : $plugin['author'];
		$author_username            = str_replace( 'https://profiles.wordpress.org/', '', $plugin['author_profile'] );
		$description                = html_entity_decode( $plugin['sections']['description'] ?? '' );
		$short_description          = html_entity_decode( $plugin['short_description'] );
		$changelog                  = html_entity_decode( $plugin['sections']['changelog'] ?? '' );
		$instruction                = html_entity_decode( $plugin['sections']['installation'] ?? '' );
		$icon                       = str_replace( 'ps.w.org', 'ps.wenpai.net',
			strtok( $plugin['icons']['2x'] ?? $plugin['icons']['1x'] ?? '', '?' ) );
		$icon                       = empty( $icon ) ? "https://weavatar.com/avatar/no_photo.webp?s=256&d=letter&letter=" . $plugin['name'] : $icon;
		$banner                     = str_replace( 'ps.w.org', 'ps.wenpai.net',
			strtok( $plugin['banners']['high'] ?? $plugin['banners']['low'] ?? '', '?' ) );
		$banner                     = empty( $banner ) ? null : $banner;
		$requires_wordpress_version = $plugin['requires'] == 0 ? null : $plugin['requires'];
		$tested_wordpress_version   = $plugin['tested'] == 0 ? null : $plugin['tested'];
		$requires_php_version       = $plugin['requires_php'] == 0 ? null : $plugin['requires_php'];
		$download_count             = $plugin['downloaded'];
		$download_url               = str_replace( 'downloads.wordpress.org', 'downloads.wenpai.net', $plugin['download_link'] );

		foreach ( $plugin['screenshots'] as $key => $screenshot ) {
			$plugin['screenshots'][ $key ] = [
				'src'     => str_replace( 'ps.w.org', 'ps.wenpai.net', strtok( $screenshot['src'], '?' ) ),
				'caption' => $screenshot['caption'] ?? '',
			];
		}

		$tags        = $plugin['tags'];
		$rating      = $plugin['rating'];
		$num_ratings = $plugin['num_ratings'];
		$screenshots = $plugin['screenshots'];
		$created_at  = Carbon::parse( $plugin['added'] );
		$updated_at  = Carbon::parse( $plugin['last_updated'] );

		// 由于 Woo 后台不怎么好实现无极添加，所以这里直接返回问题和答案
		$faq = $plugin['sections']['faq'] ?? '';

		/*$faq = [];
		preg_match_all('/<dt id=\'(.*?)\'>\n(.*?)\n<\/h4>\n<p>\n<p>(.*?)<\/p>\n<\/p>/s',
			$plugin['sections']['faq'] ?? '',
			$matches);
		$questions = $matches[2];
		$answers = $matches[3];
		for ($i = 0; $i < count($questions); $i++) {
			$faq[] = [
				'question' => html_entity_decode($questions[$i]),
				'answer' => html_entity_decode($answers[$i]),
			];
		}*/

		return [
			'name'                       => $name,
			'slug'                       => $slug,
			'version'                    => $version,
			'author'                     => $author,
			'author_username'            => $author_username,
			'description'                => $description,
			'short_description'          => $short_description,
			"rating"                     => $rating,
			'num_ratings'                => $num_ratings,
			"tags"                       => $tags,
			'faq'                        => $faq,
			'changelog'                  => $changelog,
			'instruction'                => $instruction,
			'icon'                       => $icon,
			'banner'                     => $banner,
			'requires_wordpress_version' => $requires_wordpress_version,
			'tested_wordpress_version'   => $tested_wordpress_version,
			'requires_php_version'       => $requires_php_version,
			'download_count'             => $download_count,
			'download_url'               => $download_url,
			'screenshots'                => $screenshots,
			'created_at'                 => $created_at,
			'updated_at'                 => $updated_at,
		];
	}

	public function create_bbpress_forum_for_post( $post_ID ): void {
		$post = get_post( $post_ID );
		if ( ! $post ) {
			Logger::error( Logger::Plugins, '获取文章失败', [ 'post_id' => $post_ID ] );
		}

		switch_to_blog( SITE_ID_FORUM );
		require_once WP_PLUGIN_DIR . '/bbpress/bbpress.php';
		// 需要假装已经注册了 forum 文章类型
		if ( ! post_type_exists( bbp_get_forum_post_type() ) ) {
			bbp_register_post_types();
		}

		$existing_forum = get_posts( array(
			'post_type'      => bbp_get_forum_post_type(),
			'name'           => $post->post_name,
			'posts_per_page' => 1,
		) );

		$forum_data = array(
			'post_title'        => $post->post_title,
			'post_content'      => $post->post_excerpt,
			'post_parent'       => 24789,
			'post_name'         => $post->post_name,
			'post_author'       => BOT_USER_ID,
			'post_date'         => $post->post_date,
			'post_date_gmt'     => $post->post_date_gmt,
			'post_modified'     => $post->post_modified,
			'post_modified_gmt' => $post->post_modified_gmt,
		);

		if ( ! empty( $existing_forum ) ) {
			$forum_data['ID'] = $existing_forum[0]->ID;
			wp_update_post( $forum_data );
			$forum_id = $existing_forum[0]->ID;
		} else {
			$forum_id = bbp_insert_forum( $forum_data );
		}

		unset( $forum_data, $existing_forum, $post, $forum_id );

		restore_current_blog();
	}
}
