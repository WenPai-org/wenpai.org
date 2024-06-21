<?php
/**
 * Handle dynamic fields
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2024, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( DynamicField::class ) ) :
	/**
	 * The DynamicField class.
	 */
	class DynamicField extends CoreComponent {
		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Handle value for dynamic fields.
			add_action( 'rest_api_init', [ $this, 'register_endpoint_for_dynamic_fields' ] );

			// Run shortcode.
			add_filter( '_meta_field_block_get_field_value', [ $this, 'run_shortcode' ], 10, 6 );
		}

		/**
		 * Handle value for dynamic fields.
		 *
		 * @return void
		 */
		public function register_endpoint_for_dynamic_fields() {
			register_rest_route(
				'mfb/v1',
				'/getDynamicField/',
				array(
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_dynamic_field' ],
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}

		/**
		 * Get value for dynamic field
		 *
		 * @param WP_Request $request
		 * @return array
		 */
		public function get_dynamic_field( $request ) {
			global $post;

			$post_id = $request->get_param( 'postId' );

			if ( $post_id > 0 ) {
				$post = get_post( $post_id );

				// Set up postdata since this will be needed if post_id was set.
				setup_postdata( $post );
			}

			$attributes = $request->get_param( 'attributes' );

			$attributes['fetchRawValue'] = true;

			// Create an array representation simulating the output of parse_blocks.
			$block = array(
				'blockName'    => 'mfb/meta-field-block',
				'attrs'        => $attributes,
				'innerHTML'    => '',
				'innerContent' => array(),
			);

			return render_block( $block );
		}

		/**
		 * Get value for a shortcode
		 *
		 * @param string   $content
		 * @param string   $field_name
		 * @param int      $object_id
		 * @param string   $object_type
		 * @param array    $attributes
		 * @param WP_Block $block
		 * @return mixed
		 */
		public function run_shortcode( $content, $field_name, $object_id, $object_type, $attributes, $block ) {
			if ( 'dynamic' === ( $attributes['fieldType'] ?? '' ) ) {
				if ( $field_name && '[' === substr( $field_name, 0, 1 ) && ']' === substr( $field_name, -1 ) ) {
					$shortcode_content = do_shortcode( $field_name );
					if ( $shortcode_content !== $field_name ) {
						$content = $shortcode_content;
					}
				}
			}

			return $content;
		}
	}
endif;
