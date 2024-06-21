<?php

namespace Platform\API\Service;

use WP_Error;
use function Platform\API\request_wporg;

class Core {

	public function update_check( array $params ): array|WP_Error {
		$update_exists = array();

		$data = request_wporg( add_query_arg( $_GET, '/core/version-check/1.7/' ) );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$data = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'json_decode_failed', json_last_error_msg() );
		}
		$updates = $data['offers'] ?? [];

		foreach ( $updates as $update ) {
			if ( ! empty( $update['download'] ) ) {
				$update['download'] = $this->replace( $update['download'] );
			}
			if ( ! empty( $update['packages']['full'] ) ) {
				$update['packages']['full'] = $this->replace( $update['packages']['full'] );
			}
			if ( ! empty( $update['packages']['no_content'] ) ) {
				$update['packages']['no_content'] = $this->replace( $update['packages']['no_content'] );
			}
			if ( ! empty( $update['packages']['new_bundled'] ) ) {
				$update['packages']['new_bundled'] = $this->replace( $update['packages']['new_bundled'] );
			}
			if ( ! empty( $update['packages']['partial'] ) ) {
				$update['packages']['partial'] = $this->replace( $update['packages']['partial'] );
			}
			$update_exists[] = $update;
		}

		return $update_exists;
	}

	private function replace( string $url ): string {
		$url = str_replace( 'https://downloads.wordpress.org', 'https://downloads.wenpai.net', $url );
		$url = str_replace( 'https://wordpress.org', 'https://wpmirror.com', $url );

		return $url;
	}
}
