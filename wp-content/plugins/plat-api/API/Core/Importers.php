<?php

namespace Platform\API\API\Core;

use Platform\API\API\Base;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use function Platform\API\request_wporg;

class Importers extends Base {

	public function __construct() {
		register_rest_route( 'core/importers', '1.1', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'importers' ),
		) );
	}

	public function importers( WP_REST_Request $request ): WP_REST_Response {
		$data = request_wporg( add_query_arg( $_GET, '/core/importers/1.1/' ) );
		if ( is_wp_error( $data ) ) {
			$args = array(
				'message' => $data->get_error_message(),
			);
			$this->error( $args );
		}

		return new WP_REST_Response( json_decode( $data ) );
	}
}
