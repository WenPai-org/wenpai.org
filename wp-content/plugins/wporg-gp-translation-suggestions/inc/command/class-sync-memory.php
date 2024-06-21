<?php

namespace WordPressdotorg\GlotPress\TranslationSuggestions\Command;

use Exception;
use Platform\Chinese_Format\Chinese_Format;
use Platform\Logger\Logger;
use WP_CLI;
use WP_CLI_Command;
use WP_Http;
use function Platform\Helper\is_chinese;

class Sync_Memory extends WP_CLI_Command {

	const API_ENDPOINT = 'http://127.0.0.1:9200/translate_memory';

	public function __construct() {
		parent::__construct();
		try {
			WP_CLI::add_command( 'platform translate_memory', __NAMESPACE__ . '\Sync_Memory' );
		} catch ( Exception $e ) {
			Logger::error( Logger::TRANSLATE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * 全量同步翻译记忆库
	 * @return void
	 */
	public function sync(): void {
		WP_CLI::line( '开始干活了' );

		global $wpdb;

		$site_id = SITE_ID_TRANSLATE;
		$sql     = <<<SQL
select o.id as original_id, t.id as translate_id, o.singular as source, t.translation_0 as target
from wp_{$site_id}_gp_translations as t
join wp_{$site_id}_gp_originals as o on t.original_id = o.id
where t.status = 'current'
SQL;

		$r = $wpdb->get_results( $sql );

		$requests = [];
		$format   = Chinese_Format::get_instance();
		foreach ( $r as $index => $item ) {
			$source      = $item->source;
			$translation = $item->target;

			if ( empty( $source ) || empty( $translation ) || ! is_chinese( $translation ) ) {
				continue;
			}

			$source = strtolower( trim( $source ) );
			$target = trim( $translation );
			$target = $format->convert( $target );
			$id     = md5(
				$source
				. '|'
				. $target
			);

			$requests[] = wp_json_encode( array(
				'index' => array(
					'_id' => $id,
				),
			) );
			$requests[] = wp_json_encode( array(
				'id'     => $id,
				'source' => $source,
				'target' => $target,
			) );

			if ( empty( $requests ) ) {
				WP_CLI::line( '尝试导入空翻译' );
				exit;
			}

			if ( count( $requests ) < 1000 ) {
				continue;
			}

			$body = join( PHP_EOL, $requests );
			$body .= PHP_EOL;

			$request = wp_remote_post(
				self::API_ENDPOINT . '/_bulk',
				[
					'timeout' => 10,
					'headers' => array(
						'Content-Type' => 'application/x-ndjson',
					),
					'body'    => $body,
				]
			);

			if ( is_wp_error( $request ) ) {
				WP_CLI::line( '导入记忆库失败: ' . $request->get_error_message() );
				exit;
			}

			if ( WP_Http::OK !== wp_remote_retrieve_response_code( $request ) ) {
				WP_CLI::line( '导入记忆库未返回正确的状态码: ' . wp_remote_retrieve_body( $request ) );
				exit;
			}

			$body   = wp_remote_retrieve_body( $request );
			$result = json_decode( $body, true );

			if ( JSON_ERROR_NONE !== json_last_error() ) {
				WP_CLI::line( '导入解析记忆库返回失败: ' . wp_remote_retrieve_body( $request ) );
				exit;
			}

			if ( isset( $result['errors'] ) && $result['errors'] ) {
				WP_CLI::line( '导入记忆库失败: ' . wp_remote_retrieve_body( $request ) );
				exit;
			}

			$requests = [];

			WP_CLI::success( '当前: ' . $index );
		}

		WP_CLI::success( '同步翻译记忆库成功' );
	}

	/**
	 * 清空记忆库
	 * @return void
	 */
	public function clear(): void {
		$request = wp_remote_request(
			self::API_ENDPOINT,
			[
				'method'  => 'DELETE',
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $request ) ) {
			WP_CLI::line( '清空记忆库失败: ' . $request->get_error_message() );
			exit;
		}

		if ( WP_Http::OK !== wp_remote_retrieve_response_code( $request ) ) {
			WP_CLI::line( '清空记忆库未返回正确的状态码: ' . wp_remote_retrieve_body( $request ) );
			exit;
		}

		WP_CLI::success( '清空翻译记忆库成功' );
	}
}
