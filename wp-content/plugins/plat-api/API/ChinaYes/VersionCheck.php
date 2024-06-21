<?php

namespace Platform\API\API\ChinaYes;

use Platform\API\API\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class VersionCheck extends Base {

	public function __construct() {
		register_rest_route( 'china-yes', 'version-check', array(
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'version_check' ),
		) );
		register_rest_route( 'china-yes', 'version-check', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'version_check' ),
		) );
	}

	public function version_check( WP_REST_Request $request ): WP_REST_Response {
		$installed_version = $request->get_param( 'installed_version' );
		$this->do_site_count( $request->get_header( 'User-Agent' ), $installed_version );

		$plugin_info = array(
			"name"            => "WP-China-Yes",
			"slug"            => "wp-china-yes",
			"version"         => "3.6.2",
			"download_url"    => "https://dl1.weixiaoduo.com/2024/03/wp-china-yes-3.6.2.zip",
			"homepage"        => "https://wp-china-yes.com",
			"requires"        => "4.9",
			"tested"          => "9.9.9",
			"requires_php"    => "5.6",
			"last_updated"    => "2024-03-09 20:00:00",
			"author"          => "文派开源",
			"author_homepage" => "https://wenpai.org",
			"sections"        => array(
				"description" => "文派叶子 🍃（WP-China-Yes）是中国 WordPress 生态基础设施软件，犹如落叶新芽，生生不息。"
			),
			"icons"           => array(
				"1x" => "https://weavatar.com/avatar/?s=128&d=letter&letter=Yes",
				"2x" => "https://weavatar.com/avatar/?s=256&d=letter&letter=Yes"
			),
			"rating"          => 100,
			"num_ratings"     => 10000,
			"downloaded"      => 100000,
			"active_installs" => 100000
		);

		return new WP_REST_Response( $plugin_info );
	}

	public function do_site_count( $user_agent, $installed_version ) {
		$user_agent_items = explode( ';', $user_agent );
		if ( count( $user_agent_items ) < 2 ) {
			return;
		}

		$wp_version   = trim( $user_agent_items[0] );
		$domain       = trim( $user_agent_items[1] );
		$current_time = current_time( 'mysql' );

		if ( empty( $domain ) || empty( $wp_version ) || empty( $installed_version ) ) {
			return;
		}

		global $wpdb;
		$sql = $wpdb->prepare(
			<<<SQL
			INSERT INTO china_yes_count (domain, wp_version, plugin_version, count, created_at, updated_at)
			VALUES (%s, %s, %s, 1, %s, %s)
			ON DUPLICATE KEY UPDATE
			    count = count + 1,
			    wp_version = %s,
			    plugin_version = %s,
			    updated_at = %s
			SQL,
			$domain, $wp_version, $installed_version, $current_time, $current_time, $wp_version, $installed_version, $current_time );

		$wpdb->query( $sql );
	}
}
