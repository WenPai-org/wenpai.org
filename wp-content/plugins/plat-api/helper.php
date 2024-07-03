<?php

namespace Platform\API;

use WP_Error;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

function request_wporg( string $path ): WP_Error|string {
	// 生成缓存键，基于请求路径和请求参数
	$cache_key = 'wporg_request_' . md5( $path . serialize( $_POST ) . $_SERVER['REQUEST_METHOD'] );
	$cached    = wp_cache_get( $cache_key, 'platform' );

	if ( false !== $cached ) {
		return $cached;
	}

	$client  = new Client();
	$options = [
		'headers'     => [
			'User-Agent' => $_SERVER['HTTP_USER_AGENT']
		],
		'form_params' => $_POST,
		'timeout'     => 40,
	];

	// 发起请求
	try {
		$response = $client->request( $_SERVER['REQUEST_METHOD'], 'https://api.wpmirror.com' . $path, $options );
		$body     = $response->getBody()->getContents();
		wp_cache_set( $cache_key, $body, 'platform', 1800 );

		return $body;
	} catch ( GuzzleException|RequestException $e ) {
		return new WP_Error( 'request_failed', $e->getMessage() );
	}
}
