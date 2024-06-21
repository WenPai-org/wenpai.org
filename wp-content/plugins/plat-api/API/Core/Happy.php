<?php

namespace Platform\API\API\Core;

use Platform\API\API\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Happy extends Base {

	public function __construct() {
		register_rest_route( 'core/browse-happy', '1.1', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'browse_happy' ),
		) );
		register_rest_route( 'core/serve-happy', '1.0', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'serve_happy' ),
		) );
	}

	public function browse_happy( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( [
			'platform'        => '',
			'mobile'          => false,
			'name'            => '',
			'version'         => '',
			'current_version' => '',
			'upgrade'         => false,
			'insecure'        => false,
			'update_url'      => '',
			'img_src'         => '',
			'img_src_ssl'     => '',
		] );
	}

	public function serve_happy( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( [
			'recommended_version' => '7.4',
			'minimum_version'     => '7.0',
			'is_supported'        => true,
			'is_secure'           => true,
			'is_acceptable'       => true,
		] );
	}
}
