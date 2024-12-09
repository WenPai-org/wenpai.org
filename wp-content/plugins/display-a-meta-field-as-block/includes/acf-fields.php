<?php
/**
 * The ACFFields
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( ACFFields::class ) ) :
	/**
	 * The ACFFields class.
	 */
	class ACFFields extends CoreComponent {
		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Don't format fields for rest.
			add_filter( 'acf/settings/rest_api_format', [ $this, 'api_format' ] );

			// Format special fields for rest.
			add_filter( 'acf/rest/format_value_for_rest', [ $this, 'format_value_for_rest' ], 10, 4 );

			// Flush the server cache for ACF fields.
			add_action( 'save_post', [ $this, 'flush_acf_cache' ], 10, 2 );
		}

		/**
		 * Don't format fields for rest by default
		 *
		 * @return string
		 */
		public function api_format() {
			return 'light';
		}

		/**
		 * Filter the formatted value for a given field.
		 *
		 * @param mixed      $value_formatted The formatted value.
		 * @param string|int $post_id The post ID of the current object.
		 * @param array      $field The field array.
		 * @param mixed      $raw_value The raw/unformatted value.
		 * @param string     $format The format applied to the field value.
		 *
		 * @return mixed
		 */
		public function format_value_for_rest( $value_formatted, $post_id, $field, $raw_value ) {
			$simple_value_formatted = $this->render_field( $value_formatted, $post_id, $field, $raw_value );

			$rest_formatted_value = [
				'simple_value_formatted' => $simple_value_formatted,
				'value_formatted'        => $value_formatted,
				'value'                  => $raw_value,
				'field'                  => $field,
			];

			return apply_filters(
				'meta_field_block_acf_field_format_value_for_rest',
				$rest_formatted_value,
				$post_id
			);
		}

		/**
		 * Get field value for front end by object id and field name.
		 *
		 * @param string     $field_name
		 * @param int/string $object_id
		 * @param string     $object_type
		 *
		 * @return mixed
		 */
		public function get_field_value( $field_name, $object_id, $object_type = '' ) {
			// Get the id with object type.
			$object_id_with_type = $this->get_object_id_with_type( $object_id, $object_type, $field_name );

			$field_object = get_field_object( $field_name, $object_id_with_type, false, true );

			if ( ! $field_object ) {
				// Field key cache.
				$cache_key = 'field_key';

				// Get from the cache.
				$cache_data = wp_cache_get( $cache_key, 'mfb' );

				if ( false === $cache_data ) {
					$cache_data = [];
				}

				if ( isset( $cache_data[ $field_name ] ) ) {
					$field_key = $cache_data[ $field_name ];
				} else {
					$field_key = $this->get_field_key( $field_name, $object_id_with_type );

					// Update cache.
					if ( ! empty( $field_key ) ) {
						$cache_data[ $field_name ] = $field_key;
						wp_cache_set( $cache_key, $cache_data, 'mfb' );
					}
				}

				if ( $field_key ) {
					$field_object = get_field_object( $field_key, $object_id_with_type, false, true );
				}

				if ( ! $field_object ) {
					// Get a dummy field.
					$field_object = acf_get_valid_field(
						array(
							'name' => $field_name,
							'key'  => '',
							'type' => '',
						)
					);

					// Get value for field.
					$field_object['value'] = acf_get_value( $object_id_with_type, $field_object );
				}
			}

			// Field with raw value.
			$field = $field_object;

			// Get raw value first.
			$raw_value = $field_object['value'] ?? '';

			// Format it.
			$field_object['value'] = acf_format_value( $raw_value, $object_id_with_type, $field_object );

			return [
				'value' => $this->render_field( $field_object['value'] ?? '', $object_id, $field_object, $raw_value, $object_type ),
				'field' => $field,
			];
		}

		/**
		 * Get the object id with type
		 *
		 * @param int    $object_id
		 * @param string $object_type
		 * @param string $field_name
		 * @return mixed
		 */
		public function get_object_id_with_type( $object_id, $object_type, $field_name ) {
			if ( ! in_array( $object_type, [ 'post', 'term', 'user' ], true ) ) {
				$object_id_with_type = $object_type;
			} else {
				$object_id_with_type = in_array( $object_type, [ 'term', 'user' ], true ) ? $object_type . '_' . $object_id : $object_id;
			}

			return $object_id_with_type;
		}

		/**
		 * Get field key by name.
		 *
		 * @param string     $field_name
		 * @param int/string $object_id
		 *
		 * @return boolean|string
		 */
		private function get_field_key( $field_name, $object_id ) {
			$fields = $this->get_all_fields();

			$filtered_fields = array_filter(
				$fields,
				function ( $field_value ) use ( $field_name ) {
					return $field_name === $field_value['name'] ?? '';
				}
			);

			switch ( count( $filtered_fields ) ) {
				case 0:
					return false;
				case 1:
					return current( $filtered_fields )['key'] ?? '';
			}

			// More than 1 items.
			$field_groups_ids = array();
			$field_groups     = acf_get_field_groups(
				array(
					'post_id' => $object_id,
				)
			);

			foreach ( $field_groups as $field_group ) {
				$field_groups_ids[] = $field_group['ID'];
			}

			// Check if field is part of one of the field groups, return the first one.
			foreach ( $filtered_fields as $field ) {
				if ( in_array( $field['parent'] ?? 0, $field_groups_ids, true ) ) {
					return $field['key'] ?? '';
				}
			}

			return false;
		}

		/**
		 * Get all ACF fields
		 *
		 * @return array
		 */
		private function get_all_fields() {
			// Try cache.
			$cache_key = 'get_all_acf_fields';
			$fields    = wp_cache_get( $cache_key, 'mfb' );
			if ( $fields === false ) {
				// Query posts.
				$posts = get_posts(
					array(
						'posts_per_page'         => -1,
						'post_type'              => 'acf-field',
						'orderby'                => 'menu_order',
						'order'                  => 'ASC',
						'suppress_filters'       => true, // DO NOT allow third-party to modify the query.
						'cache_results'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'post_status'            => array( 'publish' ),
					)
				);

				// Loop over posts and populate array of fields.
				$fields = array();
				foreach ( $posts as $post ) {
					// Unserialize post_content.
					$field = (array) maybe_unserialize( $post->post_content );

					// Update attributes.
					$field['ID']         = $post->ID;
					$field['key']        = $post->post_name;
					$field['label']      = $post->post_title;
					$field['name']       = $post->post_excerpt;
					$field['menu_order'] = $post->menu_order;
					$field['parent']     = $post->post_parent;

					$fields[] = $field;
				}

				// Update cache.
				if ( ! empty( $fields ) ) {
					wp_cache_set( $cache_key, $fields, 'mfb' );
				}
			}

			// Return fields.
			return $fields;
		}

		/**
		 * Render the field
		 *
		 * @param object $value
		 * @param mixed  $object_id
		 * @param array  $field
		 * @param mixed  $raw_value
		 * @param string $object_type
		 * @return void
		 */
		public function render_field( $value, $object_id, $field, $raw_value, $object_type = '' ) {
			// Get the value for rendering.
			$field_value = $this->render_acf_field( $value, $object_id, $field, $raw_value );

			return apply_filters( 'meta_field_block_get_acf_field', $field_value, $object_id, $field, $raw_value, $object_type );
		}

		/**
		 * Display value for ACF fields
		 *
		 * @param mixed $value
		 * @param int   $post_id
		 * @param array $field
		 * @param array $raw_value
		 * @return string
		 */
		public function render_acf_field( $value, $post_id, $field, $raw_value ) {
			$field_value = $value;

			$field_type = $field['type'] ?? '';
			if ( $field_type ) { // Not flexible item.
				$format_func = 'format_field_' . $field_type;
				if ( is_callable( [ $this, $format_func ] ) ) {
					$field_value = $this->{$format_func}( $value, $field, $post_id, $raw_value );
				} else {
					if ( in_array( $field_type, [ 'date_picker', 'time_picker', 'date_time_picker' ], true ) ) {
						$field_value = $this->format_field_datetime( $value, $field, $post_id, $raw_value );
					}
				}

				$field_value = is_array( $field_value ) || is_object( $field_value ) ? '<code><em>' . __( 'This data type is not supported! Please contact the author for help.', 'display-a-meta-field-as-block' ) . '</em></code>' : $field_value;
			}

			return $field_value;
		}

		/**
		 * Format image field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_image( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			if ( $value ) {
				if ( is_numeric( $value ) || is_array( $value ) ) {
					$image_id = is_numeric( $value ) ? $value : ( is_array( $value ) ? ( $value['ID'] ?? 0 ) : 0 );
				} else {
					$image_id = $raw_value;
				}

				$image_size = $field['preview_size'] ?? 'full';
				if ( $image_id ) {
					$field_value = wp_get_attachment_image( $image_id, $image_size );
				}
			}

			return $field_value;
		}

		/**
		 * Format link field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_link( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			if ( $value ) {
				if ( ! is_array( $value ) ) {
					$value = $raw_value;
				}

				if ( is_array( $value ) ) {
					$value = wp_parse_args(
						$value,
						[
							'title'  => '',
							'url'    => '',
							'target' => '',
						]
					);

					if ( empty( $value['url'] ) ) {
						$field_value = '';
					} else {
						$value['title'] = ! empty( $value['title'] ) ? $value['title'] : $value['url'];
						$rel            = '_blank' === $value['target'] ? ' rel="noreferrer noopener"' : '';
						$field_value    = sprintf( '<a href="%1$s" target="%3$s"%4$s>%2$s</a>', $value['url'], $value['title'], $value['target'], $rel );
					}
				}
			}

			return $field_value;
		}

		/**
		 * Format page_link field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_page_link( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$value        = ! is_array( $raw_value ) ? [ $raw_value ] : $raw_value;
			$value_markup = array_filter(
				array_map(
					function ( $item ) {
						if ( is_numeric( $item ) ) {
							return $this->get_post_link( $item );
						} elseif ( $item ) {
							return sprintf( '<a class="post-link" href="%1$s">%1$s</a>', $item );
						}

						return '';
					},
					$value
				)
			);

			if ( count( $value_markup ) === 0 ) {
				$field_value = '';
			} else {
				if ( count( $value_markup ) > 1 ) {
					$field_value = '<ul><li>' . implode( '</li><li>', $value_markup ) . '</li></ul>';
				} else {
					$field_value = $value_markup[0];
				}
			}

			return $field_value;
		}

		/**
		 * Format post_object field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_post_object( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$post_array = ! is_array( $value ) ? [ $value ] : $value;

			$post_array_markup = array_filter(
				array_map(
					function ( $post ) {
							return $this->get_post_link( $post );
					},
					$post_array
				)
			);

			if ( count( $post_array_markup ) === 0 ) {
				$field_value = '';
			} else {
				if ( count( $post_array_markup ) > 1 ) {
					$field_value = '<ul><li>' . implode( '</li><li>', $post_array_markup ) . '</li></ul>';
				} else {
					$field_value = $post_array_markup[0];
				}
			}

			return $field_value;
		}

		/**
		 * Format relationship field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_relationship( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$post_array = ! is_array( $value ) ? [ $value ] : $value;

			$post_array_markup = array_filter(
				array_map(
					function ( $post ) {
							return $this->get_post_link( $post );
					},
					$post_array
				)
			);

			$field_value = count( $post_array_markup ) > 0 ? '<ul><li>' . implode( '</li><li>', $post_array_markup ) . '</li></ul>' : '';

			return $field_value;
		}

		/**
		 * Format taxonomy field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_taxonomy( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$term_array = ! is_array( $value ) ? [ $value ] : $value;

			$term_array_markup = array_filter(
				array_map(
					function ( $term ) {
						if ( $term ) {
							$term_object = get_term( $term );
							if ( $term_object && $term_object instanceof \WP_Term ) {
								return sprintf( '<a class="term-link" href="%1$s">%2$s</a>', get_term_link( $term ), $term_object->name );
							}
						} else {
							return '';
						}
					},
					$term_array
				)
			);

			if ( count( $term_array_markup ) === 0 ) {
				$field_value = '';
			} else {
				if ( count( $term_array_markup ) > 1 ) {
					$field_value = '<ul><li>' . implode( '</li><li>', $term_array_markup ) . '</li></ul>';
				} else {
					$field_value = $term_array_markup[0];
				}
			}

			return $field_value;
		}

		/**
		 * Format user field type
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_user( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$user_array = [];
			if ( is_array( $value ) ) {
				if ( isset( $value['display_name'] ) ) {
					// Return format as array and only 1 item.
					$user_array = [ $value ];
				} else {
					$user_array = $value;
				}
			} else {
				$user_array = [ $value ];
			}

			$user_array_markup = array_filter(
				array_map(
					function ( $user ) {
						$user_link         = '';
						$user_id           = 0;
						$user_display_name = '';

						if ( is_object( $user ) ) {
							$user_id           = $user->ID;
							$user_display_name = $user->display_name ?? '';
						} elseif ( is_numeric( $user ) ) {
							$user_object = get_userdata( $user );
							if ( $user_object ) {
								$user_id           = $user_object->ID;
								$user_display_name = $user_object->display_name ?? '';
							}
						} elseif ( is_array( $user ) ) {
							$user_id           = $user['ID'] ?? 0;
							$user_display_name = $user['display_name'] ?? '';
						}

						if ( $user_id && $user_display_name ) {
							return sprintf( '<a class="user-link" href="%1$s">%2$s</a>', get_author_posts_url( $user_id ), $user_display_name );
						}

						return '';
					},
					is_array( $user_array ) ? $user_array : []
				)
			);

			if ( count( $user_array_markup ) === 0 ) {
				$field_value = '';
			} else {
				if ( count( $user_array_markup ) > 1 ) {
					$field_value = '<ul><li>' . implode( '</li><li>', $user_array_markup ) . '</li></ul>';
				} else {
					$field_value = $user_array_markup[0];
				}
			}

			return $field_value;
		}

		/**
		 * Render a post as link
		 *
		 * @param int|WP_Post $post
		 * @return string
		 */
		private function get_post_link( $post ) {
			if ( $post ) {
				$url = esc_url( get_permalink( $post ) );
				if ( $url ) {
					return sprintf(
						'<a class="post-link" href="%1$s" rel="bookmark">%2$s</a>',
						$url,
						esc_html( get_the_title( $post ) )
					);
				}
			}

			return '';
		}

		/**
		 * Format datetime fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return mixed
		 */
		public function format_field_datetime( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			if ( $value ) {
				$field_type    = $field['type'] ?? '';
				$rest_format   = '';
				$return_format = '';
				if ( 'date_picker' === $field_type ) {
					$rest_format   = 'Ymd';
					$return_format = $field['return_format'] ?? '';
				} elseif ( 'time_picker' === $field_type ) {
					$rest_format   = 'H:i:s';
					$return_format = $field['return_format'] ?? '';
				} elseif ( 'date_time_picker' === $field_type ) {
					$rest_format   = 'Y-m-d H:i:s';
					$return_format = $field['return_format'] ?? '';
				}

				if ( $rest_format && $return_format ) {
					$date = \DateTime::createFromFormat( $rest_format, $value );

					if ( $date ) {
						$field_value = $date->format( $return_format );
					}
				}
			}

			return $field_value;
		}

		/**
		 * Format true_false fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_true_false( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			$on_text  = $field['ui_on_text'] ?? '';
			$off_text = $field['ui_off_text'] ?? '';

			if ( empty( $on_text ) && empty( $off_text ) ) {
				$on_text  = _x( 'Yes', 'The display text for the "true" value of the true_false ACF field type', 'display-a-meta-field-as-block' );
				$off_text = _x( 'No', 'The display text for the "false" value of the true_false ACF field type', 'display-a-meta-field-as-block' );
			}

			if ( $value ) {
				$field_value = apply_filters( 'meta_field_block_acf_field_true_false_on_text', $on_text, $field, $value, $post_id );
			} else {
				$field_value = apply_filters( 'meta_field_block_acf_field_true_false_off_text', $off_text, $field, $value, $post_id );
			}

			return $field_value;
		}

		/**
		 * Format checkbox fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_checkbox( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			// Allow customizing the separator.
			$separator = apply_filters( 'meta_field_block_acf_field_choice_item_separator', ', ', $value, $field, $post_id );

			if ( $value ) {
				if ( is_array( $value ) ) {
					$refine_value = array_filter(
						array_map(
							function ( $item ) {
								$return_value = '';
								if ( $item ) {
									// Return format as both
									if ( is_array( $item ) ) {
										$return_value = $item['value'] ?? '' ? $item['value'] : '';
									} else {
										$return_value = $item;
									}
								}

								return $return_value;
							},
							$value
						)
					);

					if ( $refine_value ) {
						$field_value = '<span class="value-item">' . implode( '</span>' . $separator . '<span class="value-item">', $refine_value ) . '</span>';
					}
				}
			} else {
				$field_value = '';
			}

			return $field_value;
		}

		/**
		 * Format radio fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_radio( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			if ( $value ) {
				if ( is_array( $value ) ) {
					$field_value = $value['value'] ?? '';
				}
			} else {
				$field_value = '';
			}

			return $field_value;
		}

		/**
		 * Format button group fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_button_group( $value, $field, $post_id, $raw_value ) {
			return $this->format_field_radio( $value, $field, $post_id, $raw_value );
		}

		/**
		 * Format select fields
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_select( $value, $field, $post_id, $raw_value ) {
			$field_value = $value;

			// Allow customizing the separator.
			$separator = apply_filters( 'meta_field_block_acf_field_choice_item_separator', ', ', $value, $field, $post_id );

			$value_array = [];
			if ( is_array( $value ) ) {
				// Single value but the return format is both.
				if ( isset( $value['value'] ) ) {
					// Return format as array and only 1 item.
					$value_array = [ $value ];
				} else {
					$value_array = $value;
				}
			} else {
				$value_array = [ $value ];
			}

			if ( $value_array ) {
				$refine_value = array_filter(
					array_map(
						function ( $item ) {
							$return_value = '';
							if ( $item ) {
								// Return format as both
								if ( is_array( $item ) ) {
									$return_value = $item['value'] ?? '' ? $item['value'] : '';
								} else {
									$return_value = $item;
								}
							}

							return $return_value;
						},
						$value_array
					)
				);

				if ( $refine_value ) {
					$field_value = '<span class="value-item">' . implode( '</span>' . $separator . '<span class="value-item">', $refine_value ) . '</span>';
				}
			} else {
				$field_value = '';
			}

			return $field_value;
		}

		/**
		 * Format textarea
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_textarea( $value, $field, $post_id, $raw_value ) {
			if ( $value ) {
				if ( 'wpautop' === ( $field['new_lines'] ?? '' ) ) {
					$field_value = wpautop( $value );
				} elseif ( 'br' === ( $field['new_lines'] ?? '' ) ) {
					$field_value = nl2br( $value );
				} else {
					$field_value = $value;
				}
			} else {
				$field_value = '';
			}

			return $field_value;
		}

		/**
		 * Format wysiwyg
		 *
		 * @param mixed $value
		 * @param array $field
		 * @param int   $post_id
		 * @param mixed $raw_value
		 * @return string
		 */
		public function format_field_wysiwyg( $value, $field, $post_id, $raw_value ) {
			if ( $value ) {
				$field_value = apply_filters( 'acf_the_content', $value );

				// Follow the_content function in /wp-includes/post-template.php.
				$field_value = str_replace( ']]>', ']]&gt;', $field_value );
			} else {
				$field_value = '';
			}

			return $field_value;
		}

		/**
		 * Flush the server cache for ACF fields
		 *
		 * @param int     $post_id
		 * @param WP_Post $post
		 * @return void
		 */
		public function flush_acf_cache( $post_id, $post ) {
			if ( 'acf-field-group' === $post->post_type ) {
				wp_cache_delete( 'field_key', 'mfb' );
				wp_cache_delete( 'get_all_acf_fields', 'mfb' );
			}
		}
	}
endif;

