<?php

abstract class Branda_SMTP_Importer {
	protected $option;
	protected $translate;
	protected $module;

	abstract public function import( $module );

	/**
	 * Procedded configuration
	 *
	 * @since 3.1.0
	 */
	protected function proceed() {
		$value = branda_get_option( $this->option );
		$data  = $this->module->smtp_get_value();
		foreach ( $this->translate as $group_key => $group_data ) {
			foreach ( $group_data as $field_key => $imported_key ) {
				$filter = $v = null;
				if ( ! isset( $data[ $group_key ] ) ) {
					$data[ $group_key ] = array();
				}
				if (
					is_array( $imported_key )
					&& isset( $value[ $imported_key[0] ] )
					&& isset( $value[ $imported_key[0] ][ $imported_key[1] ] )
				) {
					$filter = sprintf( 'branda_smtp_import_%s_%s_%s', $this->option, $imported_key[0], $imported_key[1] );
					$v      = $value[ $imported_key[0] ][ $imported_key[1] ];
				}
				if (
					is_string( $imported_key )
					&& isset( $value[ $imported_key ] )
				) {
					$filter = sprintf( 'branda_smtp_import_%s_%s', $this->option, $imported_key );
					$v      = $value[ $imported_key ];
				}
				if (
					empty( $v )
					|| empty( $filter )
				) {
					continue;
				}
					$data[ $group_key ][ $field_key ] = apply_filters( $filter, $v );
			}
		}
		$this->module->smtp_update_value( $data );
	}

	/**
	 * Decode from base64
	 *
	 * @since 3.1.0
	 */
	public function base64_decode( $value ) {
		return base64_decode( $value );
	}

	/**
	 * Sanitize for on/off
	 *
	 * @since 3.1.0
	 */
	public function sanitize_on( $value ) {
		$value = strtolower( $value );
		switch ( $value ) {
			case 'on':
			case 'off':
				return $value;
			case 'override':
			case 'yes':
			case '1':
			case 1:
				return 'on';
			default:
				return 'off';
		}
		return 'off';
	}
}

