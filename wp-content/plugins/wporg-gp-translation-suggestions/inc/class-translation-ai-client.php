<?php

namespace WordPressdotorg\GlotPress\TranslationSuggestions;

use Platform\Chinese_Format\Chinese_Format;
use WP_Error;

class Translation_AI_Client {

	const API_ENDPOINT = 'https://api.deeplx.org/translate';
	const API_KEY = '';

	/**
	 * Queries translation memory for a string.
	 *
	 * @param string $text Text to search translations for.
	 * @param string $target_locale Locale to search in.
	 *
	 * @return array|WP_Error      List of suggestions on success, WP_Error on failure.
	 */
	public static function query( string $text, string $target_locale = 'zh_CN' ): WP_Error|array {
		/*$messages[]      = array(
			'role'    => 'system',
			'content' => '你是一位翻译者，擅长 WordPress 插件和主题的中文翻译，请以 JSON 数组格式(例如: [ "翻译1", "翻译2", "翻译3" ])直接回答用户提交的翻译需求，不要输出任何提示及无关信息。',
		);
		$messages[]      = array(
			'role'    => 'user',
			'content' => '文本: ' . $text . ' 的中文翻译是什么？以 JSON 数组格式回答数个不同的翻译，不要输出任何提示及无关信息。',
		);
		$openai_response = wp_remote_post(
			self::API_ENDPOINT . '/v1/chat/completions',
			array(
				'timeout' => 20,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . self::API_KEY,
				),
				'body'    => wp_json_encode(
					array(
						'model'      => 'gpt-3.5-turbo',
						'max_tokens' => 4096,
						'n'          => 1,
						'messages'   => $messages,
					)
				),
			)
		);

		$response_status = wp_remote_retrieve_response_code( $openai_response );
		$output          = json_decode( wp_remote_retrieve_body( $openai_response ), true );

		if ( 200 !== $response_status || is_wp_error( $openai_response ) ) {
			return new WP_Error( 'AI 未响应正确的状态码' );
		}

		$message = $output['choices'][0]['message']['content'];
		$result  = json_decode( $message, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error( '解析 AI 返回失败' );
		}*/

		$request         = [
			'text'        => $text,
			'source_lang' => 'auto',
			'target_lang' => 'ZH',
		];
		$response        = wp_remote_post(
			self::API_ENDPOINT,
			array(
				'timeout' => 30,
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $request ),
			)
		);
		$response_status = wp_remote_retrieve_response_code( $response );
		$output          = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_status || is_wp_error( $response ) ) {
			return new WP_Error( 'AI 未响应正确的状态码' );
		}
		if ( ! isset( $output['code'] ) || 200 !== $output['code'] ) {
			return new WP_Error( 'AI 返回错误' );
		}

		$result[] = $output['data'];
		if ( isset( $output['alternatives'] ) && is_array( $output['alternatives'] ) ) {
			foreach ( $output['alternatives'] as $suggestion ) {
				$result[] = $suggestion;
			}
		}

		$suggestions = [];
		$format      = Chinese_Format::get_instance();
		foreach ( $result as $translation ) {
			$translation   = $format->convert( $translation );
			$suggestions[] = [
				'translation' => $translation,
			];
		}

		return $suggestions;
	}
}
