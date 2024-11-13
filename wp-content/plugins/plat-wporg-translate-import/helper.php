<?php

namespace Platform\Translate\WPOrgTranslateImport;

use WP_Error;

const PLUGIN = 'plugins';

const THEME = 'themes';

function get_web_page_contents( $url ): WP_Error|bool|string {
	/*$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'WordPress/6.4.3; https://translate.wordpress.org/' );

	$response = curl_exec( $ch );

	if ( curl_errno( $ch ) ) {
		return new WP_Error( 'http_request_error', '请求URL失败，CURL错误: ' . curl_error( $ch ) );
	}

	$status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );
	if ( 200 !== $status_code ) {
		return new WP_Error( 'http_request_error_404', '请求URL失败，返回状态码: ' . $status_code );
	}

	return $response;*/
	$response = wp_remote_get( $url, array(
		'timeout'    => 60,
		'sslverify'  => false,
	) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status_code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status_code ) {
		return new WP_Error( 'http_request_error_404', '请求URL失败，返回状态码: ' . $status_code );
	}

	return wp_remote_retrieve_body( $response );
}

function create_project( string $name, string $slug, string $type, int $parent_project_id, string $parent_project_slug = '' ): int {
	global $wpdb;

	$parent_project_slug = empty( $parent_project_slug ) ? '' : $parent_project_slug . '/';

	$res = $wpdb->insert( 'wp_' . SITE_ID_TRANSLATE . '_gp_projects', array(
		'name'                => $name,
		'slug'                => $slug,
		'path'                => sprintf( '%s/%s%s', $type, $parent_project_slug, $slug ),
		'description'         => '',
		'source_url_template' => '',
		'parent_project_id'   => $parent_project_id,
		'active'              => 1
	) );

	$project_id = $wpdb->insert_id;

	// 不准给根项目创建默认翻译集
	if ( 0 !== (int) $res && ! empty( $parent_project_slug ) ) {
		$wpdb->insert( 'wp_' . SITE_ID_TRANSLATE . '_gp_translation_sets', array(
			'name'       => '简体中文',
			'slug'       => 'default',
			'project_id' => $project_id,
			'locale'     => 'zh-cn'
		) );
	}

	return $project_id;
}