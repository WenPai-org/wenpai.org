<?php

namespace Platform\API\API\Translations;

use Platform\API\API\Base;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use function Platform\API\request_wporg;

class Index extends Base {

	public function __construct() {
		register_rest_route( 'translations', '(?P<path>.+)', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'patterns' ),
		) );
	}

	public function patterns( WP_REST_Request $request ): WP_REST_Response {
		$path = $request->get_param( 'path' );
		$data = request_wporg( add_query_arg( $_GET, '/translations/' . $path ) );
		if ( is_wp_error( $data ) ) {
			$args = array(
				'message' => $data->get_error_message(),
			);
			$this->error( $args );
		}

		return new WP_REST_Response( json_decode( $data ) );
	}
}
