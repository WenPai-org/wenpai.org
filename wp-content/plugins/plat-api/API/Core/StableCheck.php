<?php

namespace Platform\API\API\Core;

use Platform\API\API\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function Platform\API\request_wporg;

class StableCheck extends Base {

	public function __construct() {
		register_rest_route( 'core/stable-check', '1.0', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'stable_check' ),
		) );
	}

	public function stable_check( WP_REST_Request $request ): WP_REST_Response {
		$data = request_wporg( add_query_arg( $_GET, '/core/stable-check/1.0/' ) );
		if ( is_wp_error( $data ) ) {
			$args = array(
				'message' => $data->get_error_message(),
			);
			$this->error( $args );
		}

		return new WP_REST_Response( json_decode( $data ) );
	}
}
