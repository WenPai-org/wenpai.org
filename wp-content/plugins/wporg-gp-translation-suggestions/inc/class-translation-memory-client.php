<?php

namespace WordPressdotorg\GlotPress\TranslationSuggestions;

use GP;
use Platform\Chinese_Format\Chinese_Format;
use Platform\Logger\Logger;
use Text_Diff;
use WP_Error;
use WP_Http;
use WP_Text_Diff_Renderer_inline;
use function Platform\Helper\is_chinese;

require_once ABSPATH . '/wp-includes/wp-diff.php';

class Translation_Memory_Client {

	const API_ENDPOINT = 'http://127.0.0.1:9200/translate_memory/_search';
	const API_BULK_ENDPOINT = 'http://127.0.0.1:9200/translate_memory/_bulk';

	/**
	 * 更新翻译记忆库内容
	 *
	 * 这里将记忆库后端由WordPress.com更改为本地服务器上的ES
	 *
	 * @param array $translations List of translation IDs, keyed by original ID.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function update( array $translations ): bool|WP_Error {
		$requests = [];

		foreach ( $translations as $original_id => $translation_id ) {
			$translation = GP::$translation->get( $translation_id );

			// Check again in case the translation was changed.
			if ( 'current' !== $translation->status ) {
				continue;
			}

			$original = GP::$original->get( $original_id );
			$source   = $original->fields()['singular'] ?? '';

			if ( empty( $source ) || empty( $translation->translation_0 ) || ! is_chinese( $translation->translation_0 ) ) {
				continue;
			}

			$source = strtolower( trim( $source ) );
			$format = Chinese_Format::get_instance();
			$target = trim( $translation->translation_0 );
			$target = $format->convert( $target );

			$id = md5(
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
		}

		if ( empty( $requests ) ) {
			return new WP_Error( '尝试导入空翻译' );
		}

		$body = join( PHP_EOL, $requests );
		$body .= PHP_EOL;

		$request = wp_remote_post(
			self::API_BULK_ENDPOINT,
			[
				'timeout' => 10,
				'headers' => array(
					'Content-Type' => 'application/x-ndjson',
				),
				'body'    => $body,
			]
		);

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		if ( WP_Http::OK !== wp_remote_retrieve_response_code( $request ) ) {
			Logger::error( Logger::TRANSLATE, '导入记忆库未返回正确的状态码', [ 'body' => wp_remote_retrieve_body( $request ) ] );

			return new WP_Error( '导入记忆库未返回正确的状态码' );
		}

		$body   = wp_remote_retrieve_body( $request );
		$result = json_decode( $body, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			Logger::error( Logger::TRANSLATE, '导入解析记忆库返回失败', [ 'body' => wp_remote_retrieve_body( $request ) ] );

			return new WP_Error( '导入解析记忆库返回失败' );
		}

		if ( isset( $result['errors'] ) && $result['errors'] ) {
			Logger::error( Logger::TRANSLATE, '导入记忆库失败', [ 'body' => wp_remote_retrieve_body( $request ) ] );

			return new WP_Error( '导入记忆库失败' );
		}

		return true;
	}

	/**
	 * Queries translation memory for a string.
	 *
	 * @param string $text Text to search translations for.
	 * @param string $target_locale Locale to search in.
	 *
	 * @return array|WP_Error      List of suggestions on success, WP_Error on failure.
	 */
	public static function query( string $text, string $target_locale = 'zh_CN' ): WP_Error|array {
		$body = array(
			'query' => array(
				'bool' => array(
					'should' => array(
						array(
							'match' => array(
								'source' => array(
									'query' => $text,
									'boost' => 2,
								),
							),
						),
					),
				),
			),
			'sort'  => array(
				array(
					'_score' => 'desc',
				),
			),
			'size'  => 10,
		);
		$body = wp_json_encode( $body );

		$request = wp_remote_post(
			self::API_ENDPOINT,
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
			Logger::error( Logger::TRANSLATE, '记忆库未返回正确的状态码', [ 'body' => wp_remote_retrieve_body( $request ) ] );

			return new WP_Error( '记忆库未返回正确的状态码' );
		}

		$body   = wp_remote_retrieve_body( $request );
		$result = json_decode( $body, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			Logger::error( Logger::TRANSLATE, '解析记忆库返回失败', [ 'body' => wp_remote_retrieve_body( $request ) ] );

			return new WP_Error( '解析记忆库返回失败' );
		}

		if ( empty( $result ) ) {
			return [];
		}

		$result = $result['hits']['hits'] ?? array();

		$suggestions = [];
		foreach ( $result as $match ) {
			$source      = $match['_source']['source'] ?? '';
			$translation = $match['_source']['target'] ?? '';

			$text   = strtolower( trim( $text ) );
			$source = strtolower( trim( $source ) );

			similar_text( $source, $text, $similarity_score );

			if ( $similarity_score < 70 ) {
				continue;
			}

			$suggestions[] = [
				'similarity_score' => $similarity_score,
				'source'           => $source,
				'translation'      => $translation,
				'diff'             => ( 100 == $similarity_score ) ? null : self::diff( $text, $source ),
			];
		}

		return $suggestions;
	}

	/**
	 * Generates the differences between two sequences of strings.
	 *
	 * @param string $previous_text Previous text.
	 * @param string $text New text.
	 *
	 * @return string HTML markup for the differences between the two texts.
	 */
	protected static function diff( string $previous_text, string $text ): string {
		$diff     = new  Text_Diff( 'auto', [ [ $previous_text ], [ $text ] ] );
		$renderer = new WP_Text_Diff_Renderer_inline();

		return $renderer->render( $diff );
	}
}
