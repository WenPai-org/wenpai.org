<?php

namespace Platform\Token\Service;

use JWTAuth\Auth;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class Token {
	public function __construct() {
		register_rest_route( 'token', 'generate', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'generate' ],
			'permission_callback' => '__return_true',
		] );
		register_rest_route( 'token', 'renew', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'renew' ],
			'permission_callback' => '__return_true',
		] );
	}

	public function generate(): void {
		/**
		 * Token 生成流程
		 * 1. 获取 GET 参数 callback ，用于回调返回的 Token，未设置将退出
		 * 2. 获取用户信息签发 Token
		 * 3. 带上 Token 重定向至 callback 地址
		 */

		if ( empty( $_GET['callback'] ) ) {
			wp_die( "缺少 callback 参数" );
		}
		if ( empty( $_GET['_remote_nonce'] ) ) {
			wp_die( "缺少 _remote_nonce 参数" );
		}
		if ( ! filter_var( $_GET['callback'], FILTER_VALIDATE_URL ) ) {
			wp_die( "callback 参数不合法" );
		}

		if ( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url() );
			exit;
		}

		$jwt     = new Auth();
		$user_id = get_current_user_id();
		$user    = get_user_by( 'id', $user_id );
		$token   = $jwt->generate_token( $user );

		$params = [
			'token'    => $token,
			'_wpnonce' => $_GET['_remote_nonce'],
		];
		$params = http_build_query( $params );

		wp_redirect( sanitize_text_field( $_GET['callback'] ) . '?' . $params );
	}

	public function renew(): WP_REST_Response {
		$jwt = new Auth();

		$token = $jwt->validate_token( false );
		// 判断是否为 WP_REST_Response 类型
		if ( $token instanceof WP_REST_Response ) {
			return $token;
		}
		$user = get_user_by( 'id', $token?->data?->user?->id );

		if ( empty( $user ) ) {
			return new WP_REST_Response( [
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'jwt_auth_user_not_found',
				'message'    => __( "User doesn't exist", 'jwt-auth' ),
				'data'       => array(),
			], 401 );
		}


		$new_token = $jwt->generate_token( $user, false );

		return new WP_REST_Response( $new_token, 200 );
	}
}
