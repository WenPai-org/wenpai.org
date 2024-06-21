<?php
/**
 * Plugin Name: Http Block
 * Description: 全局屏蔽一些没用的 HTTP 请求
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

add_filter( 'pre_http_request', function ( $preempt, $parsed_args, $url ) {
	// WPMUDEV
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'wpmudev.com' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// all-in-one-wp-migration
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'plugin-updates.wp-migration.com' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'redirect.wp-migration.com' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// ListSpeed 缓存
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'wpapi.quic.cloud' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// WP-Rocket
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'wp-rocket.me' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// Object Cache Pro
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'objectcache.pro' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// Duplicator
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'duplicator.com' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// image-upload-for-bbpress-pro
	if ( wp_parse_url( $url, PHP_URL_HOST ) === 'aspengrovestudios.com' ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// Woo 的 Feed
	if ( strpos( $url, 'public-api.wordpress.com/wpcom/v2/wcpay/incentives' ) ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// Woo 乱七八糟的东西
	if ( strpos( $url, 'woocommerce.com/wp-json' ) ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}
	// Youtube 嵌入视频
	if ( strpos( $url, 'www.youtube.com/embed' ) ) {
		return new WP_Error( 'http_request_not_executed', '无用 URL 已屏蔽访问' );
	}


	return $preempt;
}, 9999, 3 );
 