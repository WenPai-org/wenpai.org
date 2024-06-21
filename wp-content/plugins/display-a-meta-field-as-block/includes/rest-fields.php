<?php
/**
 * The RestFields
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( RestFields::class ) ) :
	/**
	 * The RestFields class.
	 */
	class RestFields extends CoreComponent {
		/**
		 * Array of public object types
		 */
		protected $object_types = [];

		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Load public object types.
			add_action( 'init', [ $this, 'load_public_object_types' ] );

			// Expose all custom rest fields for public object types.
			add_action( 'rest_api_init', [ $this, 'expose_custom_rest_fields' ], PHP_INT_MAX );
		}

		/**
		 * Load public object types.
		 *
		 * @return void
		 */
		public function load_public_object_types() {
			if ( empty( $this->object_types ) ) {
				$this->object_types = $this->get_public_object_types();
			}
		}

		/**
		 * Load all available rest fields for public object types
		 *
		 * @return void
		 */
		public function expose_custom_rest_fields() {
			global $wp_rest_additional_fields;

			if ( count( $this->object_types ) > 0 ) {
				$object_types = array_reduce(
					$this->object_types,
					function ( $previous, $object_type ) use ( $wp_rest_additional_fields ) {
						if ( isset( $wp_rest_additional_fields[ $object_type ] ) ) {
							$field_names = array_filter(
								array_keys( $wp_rest_additional_fields[ $object_type ] ),
								function ( $key ) {
									return 'acf' !== $key; // Ignore acf.
								}
							);

							if ( count( $field_names ) > 0 ) {
								$previous[ $object_type ] = array_values( $field_names );
							}
						}

						return $previous;
					},
					[]
				);

				if ( count( $object_types ) > 0 ) {
					foreach ( $object_types as $object_type => $fields ) {
						register_rest_field(
							$object_type,
							'mfb_rest_fields',
							array(
								'get_callback' => function () use ( $fields ) {
									return $fields;
								},
								'schema'       => array(
									'type' => 'array',
								),
							)
						);
					}
				}
			}
		}

		/**
		 * Get all public object types
		 *
		 * @return array
		 */
		private function get_public_object_types() {
			$object_types = [];

			// Get public post types.
			$post_types = get_post_types(
				[
					'public'       => true,
					'show_in_rest' => true,
				]
			);

			if ( ! empty( $post_types ) ) {
				$object_types = array_keys( $post_types );
			}

			$other_types = apply_filters( 'meta_field_block_get_additional_public_types_for_rest', [] );

			if ( ! empty( $other_types ) ) {
				$object_types = array_merge( $object_types, $other_types );
			}

			return $object_types;
		}
	}
endif;
