<?php
if ( ! class_exists( 'Branda_Helper' ) ) {

	class Branda_Helper {
		protected $_options;
		protected $data        = null;
		protected $option_name = 'unknown';
		protected $url;
		protected $build;
		protected $deprecated_version = false;
		protected $file               = __FILE__;
		protected $uba;
		protected $old_option_name;
		/**
		 * Debug mode?
		 *
		 * @since 2.3.0
		 */
		private static $debug;

		/**
		 * Module name
		 *
		 * @since 1.9.4
		 */
		protected $module = 'Branda_Helper';

		/**
		 * Messages
		 *
		 * @since 3.0.0
		 */
		protected $messages = array();

		/**
		 * Is Network
		 *
		 * @since 3.0.0
		 */
		protected $is_network = false;

		/**
		 * Is Network Admin
		 *
		 * @since 3.2.0
		 */
		protected $is_network_admin = false;
		/**
		 * Is Main Site
		 *
		 * @since 3.2.0
		 */
		protected $is_main_site = true;

		/**
		 * User roles.
		 *
		 * @since 3.1.0
		 *
		 * @var array
		 */
		protected $roles = array();

		/**
		 * Positions
		 *
		 * @since 3.1.2
		 */
		protected $positions = array( 'top', 'right', 'bottom', 'left' );

		/**
		 * An Escape callback function to be called in `esc_deep()` method.
		 *
		 * @since 3.4.9.1
		 */
		protected $esc_callback = null;

		/**
		 * Option for module to skip generic escape when escaped in module class.
		 *
		 * @since 3.4.9.1
		 */
		protected $skip_escape = false;

		public function __construct() {
			global $branda_network;
			$this->is_network = $branda_network;
			if ( $this->is_network ) {
				$this->is_network_admin = is_network_admin();
				$this->is_main_site     = is_main_site();
			}
			/**
			 * Set Branda version
			 */
			if ( empty( $this->build ) ) {
				global $ub_version;
				$this->build = $ub_version;
			}
			/**
			 * Check is deprecated?
			 */
			if (
				! empty( $this->deprecated_version )
				&& false === $this->deprecated_version
				/**
				 * avoid to compare with development version
				 */
				&& ! preg_match( '/^PLUGIN_VER/', $this->build )
			) {
				$compare = version_compare( $this->deprecated_version, $this->build );
				if ( 1 > $compare ) {
					return;
				}
			}
			/**
			 * admin
			 */
			if ( is_admin() ) {
				$uba    = branda_get_uba_object();
				$params = array(
					'page' => 'branding',
				);
				if ( is_a( $uba, 'Branda_Admin' ) ) {
					$this->uba = $uba;
				} else {
					$this->uba = new Branda_Admin();
				}
				$this->url = add_query_arg(
					$params,
					is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
				);
			}
			add_filter( 'ultimate_branding_options_names', array( $this, 'add_option_name' ) );
			add_filter( 'ultimate_branding_get_option_name', array( $this, 'get_module_option_name' ), 10, 2 );
			/**
			 * Add data copy
			 */
			add_filter( 'ultimate_branding_options_footer', array( $this, 'add_copy_options' ), 10, 3 );
			/**
			 * Upgrade plugin settings.
			 *
			 * @since 2.3.0
			 */
			add_action( 'init', array( $this, 'upgrade_plugin_settings' ), 5 );
			/**
			 * Rename option name.
			 *
			 * @since 2.3.0
			 */
			add_action( 'init', array( $this, 'rename_option_name' ), 9 );
			/**
			 * Enqueue module group assets.
			 */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

			/**
			 * Check has module configuration to allow reset module button.
			 *
			 * @since 3.1.0
			 */
			add_filter( 'branda_options_show_reset_module_button', array( $this, 'show_reset_module_button' ), 10, 2 );
		}

		public function __isset( $property ) {
			if ( 'options' === $property ) {
				$options = $this->__get( $property );
				return ! empty( $options );
			}
			return isset( $this->$property );
		}

		public function __set( $property, $value ) {
			if ( 'options' === $property ) {
				$this->_options = $value;
			} else {
				$this->$property = $value;
			}
		}

		public function __get( $property ) {
			if ( 'options' === $property ) {
				if ( is_null( $this->_options ) ) {
					if ( method_exists( $this, 'set_options' ) ) {
						$this->set_options();
					} else {
						$this->_options = array();
					}
				}
				return $this->_options;
			}
		}

		public function add_option_name( $options ) {
			if ( ! in_array( $this->option_name, $options ) ) {
				$options[] = $this->option_name;
			}
			return $options;
		}

		/**
		 * Is include current user roles to $allowed_roles or not
		 *
		 * @param array $allowed_roles Role list
		 * @return bool
		 */
		public static function is_allowed_role( $allowed_roles ) {
			$user       = wp_get_current_user();
			$user_roles = (array) $user->roles;

			$allow = array_intersect( $allowed_roles, $user_roles )
				|| empty( $user_roles ) && in_array( 'guest', $allowed_roles, true )
				|| is_multisite() && in_array( 'super', $allowed_roles, true ) && is_super_admin();

			return $allow;
		}


		/**
		 * @since 1.9.1 added parameter $default
		 *
		 * @param mixed $default default value return if we do not have any.
		 */
		protected function get_value( $section = null, $name = null, $default = null, $escape = true ) {
			$this->set_data();
			$data = $value = $this->data;
			if ( ! empty( $section ) ) {
				if ( empty( $name ) && isset( $value[ $section ] ) ) {
					$data = $value[ $section ];
				} else {

					if ( ! is_array( $value ) ) {
						$value = array();
					}

					/**
					 * If default is empty, then try to return default defined by
					 * configuration.
					 *
					 * @since 1.9.5
					 */
					if (
						null === $default
						&& isset( $this->options )
						&& isset( $this->options[ $section ] )
						&& isset( $this->options[ $section ]['fields'] )
						&& isset( $this->options[ $section ]['fields'][ $name ] )
						&& isset( $this->options[ $section ]['fields'][ $name ]['default'] )
					) {
						$data = $this->options[ $section ]['fields'][ $name ]['default'];
					} else {
						$data = $default;
					}
					if ( isset( $value[ $section ] ) ) {
						if ( empty( $name ) ) {
							$data = $value[ $section ];
						} elseif ( isset( $value[ $section ][ $name ] ) ) {
							if ( is_string( $value[ $section ][ $name ] ) ) {
								$data = stripslashes( $value[ $section ][ $name ] );
							} else {
								$data = $value[ $section ][ $name ];
							}
						}
					} elseif ( empty( $name ) ) {
						$value[ $section ] = array();
						/**
						 * Add defaults
						 */
						if (
							isset( $this->options )
							&& isset( $this->options[ $section ] )
							&& isset( $this->options[ $section ]['fields'] )
						) {
							foreach ( $this->options[ $section ]['fields'] as $field_name => $field ) {
								if ( isset( $field['default'] ) ) {
									$data[ $field_name ] = $field['default'];
								}
							}
						}
					}
				}
			}

			if ( $escape ) {
				$data = apply_filters( 'ub_escaped_value',
					$this->esc_deep( $data, $this->esc_callback, $section ),
					$data, $this->module, $section, $name );
			}
			
			return apply_filters( 'ub_get_value', $data, $this->module, $section, $name );
		}

		/**
		 * Escapes module output
		 * 
		 * @since 3.4.9.1
		 * 
		 * @param mixed $value Value to be escaped
		 * @param mixed $callback Optional callback to be used for excaping value.
		 */
		public function esc_deep( $value = null, $callback = null, $index = null ) {
			if ( empty( $value ) || $this->skip_escape ) {
				return $value;
			}

			$callback = ! empty( $callback ) ? $callback : $this->esc_callback;

			//return empty( $callback ) || ! is_callable( $callback ) ? wp_kses_post_deep( $value ) : map_deep( $value, $callback );
			return empty( $callback ) || ! is_callable( $callback ) ? wp_kses_post_deep( $value ) : $this->map_deep( $value, $callback, $index );
		}

		/**
		 * Undocumented function
		 *
		 * @param [type] $value
		 * @param [type] $callback
		 * @param [type] $index
		 * @return string|int|bool
		 */
		public function map_deep( $value = null, $callback = null, $index = null ) {
			if ( empty( $value ) ) {
				return $value;
			}

			$callback = ! empty( $callback ) ? $callback : $this->esc_callback;

			if ( is_array( $value ) ) {
				foreach ( $value as $index => $item ) {
					$value[ $index ] = $this->map_deep( $item, $callback, $index );
				}
			} elseif ( is_object( $value ) ) {
				$object_vars = get_object_vars( $value );

				foreach ( $object_vars as $property_name => $property_value ) {
					$value->$property_name = $this->map_deep( $property_value, $callback, $property_name );
				}
			} else {
				$callback_params = ! empty( $index ) ?
					array(
						'key'   => $index,
						'value' => $value,
					) :
					$value;
				$value           = call_user_func( $callback, $callback_params );
			}

			return $value;
		}

		/**
		 * set value
		 *
		 * @since 2.1.0
		 *
		 * @param string $key key
		 * @param string $subkey subkey
		 * @param mixed  $value Value to store.
		 */
		protected function set_value( $key, $subkey, $value = null ) {
			$data = $this->get_value();
			if ( null === $value ) {
				if ( isset( $data[ $key ] ) && isset( $data[ $key ][ $subkey ] ) ) {
					unset( $data[ $key ][ $subkey ] );
				}
			} else {
				if ( ! is_array( $data ) ) {
					$data = array();
				}

				if ( ! isset( $data[ $key ] ) ) {
					$data[ $key ] = array();
				}
				$data[ $key ][ $subkey ] = $value;
			}

			return $this->update_value( $data );
		}

		/**
		 * delete value
		 *
		 * @since 3.0.0
		 *
		 * @param string $key key
		 * @param string $subkey subkey
		 */
		protected function delete_value( $key, $subkey = null ) {
			$data = $this->get_value();
			if ( isset( $data[ $key ] ) ) {
				if ( null === $subkey ) {
					unset( $data[ $key ] );
				} elseif ( isset( $data[ $key ][ $subkey ] ) ) {
					unset( $data[ $key ][ $subkey ] );
				} else {
					return false;
				}
				return $this->update_value( $data );
			}
			return false;
		}

		public function admin_options_page( $content ) {
			if ( method_exists( $this, 'set_options' ) ) {
				$this->set_data();
				$simple_options = new Simple_Options();
				do_action( 'branda_admin_options_page_before_options', $this->option_name );
				return $simple_options->build_options( $this->options, $this->data, $this->module );
			}
			return self::sui_notice( esc_html__( 'Something went wrong!', 'ub' ) );
		}

		protected function set_data() {
			if ( null === $this->data ) {
				$value = branda_get_option( $this->option_name );
				if ( 'empty' !== $value ) {
					$this->data = $value;
				}
			}
		}

		/**
		 * Update settings
		 *
		 * @since 1.8.6
		 */
		public function update( $status ) {
			$value = isset( $_POST['simple_options'] ) ? $_POST['simple_options'] : array();
			if ( '' === $value ) {
				$value = 'empty';
			}

			/**
			 * check empty options
			 */
			$this->options;
			if ( empty( $this->options ) ) {
				if ( self::is_debug() ) {
					$msg = sprintf( 'Branda Admin: empty options array for %s variable. Please contact with plugin developers.', $this->option_name );
					error_log( $msg, 0 );
				}
				return;
			}
			/**
			 * try to preserve some values (get current value)
			 */
			$current_value = $this->get_value();

			/**
			 * produce
			 */
			foreach ( $this->options as $section_key => $section_data ) {
				if ( ! isset( $section_data['fields'] ) ) {
					continue;
				}
				if ( isset( $section_data['sortable'] ) && isset( $value[ $section_key ] ) ) {
					$value[ '_' . $section_key . '_sortable' ] = array_keys( $value[ $section_key ] );
				}
				foreach ( $section_data['fields'] as $key => $data ) {
					if ( ! isset( $data['type'] ) ) {
						$data['type'] = 'text';
					}

					if ( is_array( $value ) && ! isset( $value[ $section_key ] ) ) {
						$value[ $section_key ] = array();
					}

					$value = $this->sanitise_input_by_type( $data['type'], $value, $section_key, $key, $current_value, $data );
				}
			}
			/**
			 * Add settings sections to prevent delete on save.
			 *
			 * Add settings sections (virtual options not included in
			 * "set_options()" function to avoid delete during update.
			 *
			 * @since 3.0.0
			 */
			$filter   = sprintf(
				'ultimatebranding_settings_%s_preserve',
				self::hyphen_to_underscore( $this->module )
			);
			$preserve = apply_filters( $filter, array() );
			if ( ! empty( $preserve ) && is_array( $preserve ) ) {
				foreach ( $preserve as $section_key => $data ) {
					if ( ! isset( $current_value[ $section_key ] ) ) {
						continue;
					}
					if ( null === $data ) {
						$value[ $section_key ] = $current_value[ $section_key ];
						continue;
					}
					foreach ( $data as $key ) {
						if ( ! isset( $current_value[ $section_key ][ $key ] ) ) {
							continue;
						}
						if ( ! isset( $value[ $section_key ] ) ) {
							$value[ $section_key ] = array();
						}
						$value[ $section_key ][ $key ] = $current_value[ $section_key ][ $key ];
					}
				}
			}
			/**
			 * do action
			 */
			do_action( 'branda_helper_update', $this->module, $value, $current_value );
			
			/**
			 * save & return
			 */
			return $this->update_value( $value );
		}

		/**
		 * Sanitizes input by inpu type.
		 *
		 * @param [type] $type
		 * @param [type] $value
		 * @param [type] $section_key
		 * @param [type] $key
		 * @return void
		 */
		public function sanitise_input_by_type( $type = null, $value = null, $section_key = null, $key = null, $current_value = null, $data = array() ) {
			if ( empty( $type ) || empty( $value ) || empty( $section_key ) || empty( $key ) ) {
				return $value;
			}

			if ( is_object( $value ) ) {
				$value = (array) $value;
			}

			switch ( $type ) {
				case 'media':
					if ( isset( $value[ $section_key ][ $key ] ) && is_array( $value[ $section_key ][ $key ] ) ) {
						$value[ $section_key ][ $key ] = array_shift( $value[ $section_key ][ $key ] );
						$image                         = wp_get_attachment_image_src( $value[ $section_key ][ $key ], 'full' );
						if ( false !== $image ) {
							$value[ $section_key ][ $key . '_meta' ] = $image;
						}
					}
					break;
				case 'gallery':
					if ( isset( $value[ $section_key ][ $key ] ) && is_array( $value[ $section_key ][ $key ] ) ) {
						$gallery = array();
						foreach ( $value[ $section_key ][ $key ] as $id ) {
							if ( empty( $id ) ) {
								continue;
							}
							$one = array(
								'value' => $id,
								'meta'  => array( $id ),
							);
							if ( preg_match( '/^\d+$/', $id ) ) {
								$image = wp_get_attachment_image_src( $id, 'full' );
								if ( false !== $image ) {
									$one['meta'] = $image;
								}
							}
							$gallery[] = $one;
						}
						$value[ $section_key ][ $key ] = $gallery;
					}
					break;
				case 'checkbox':
					if (
						isset( $value[ $section_key ] )
						&& isset( $value[ $section_key ][ $key ] )
					) {
						$value[ $section_key ][ $key ] = 'on';
					} else {
						$value[ $section_key ][ $key ] = 'off';
					}
					break;
				case 'checkboxes':
					if (
						isset( $value[ $section_key ] )
						&& isset( $value[ $section_key ][ $key ] )
						&& is_array( $value[ $section_key ][ $key ] )
					) {
						foreach ( $value[ $section_key ][ $key ] as $c_key => $c_value ) {
							$value[ $section_key ][ $key ][ $c_key ] = 'off';
							if ( $c_value ) {
								$value[ $section_key ][ $key ][ $c_key ] = 'on';
							}
						}
					}
					break;
				/**
				 * save extra data if field is a wp_editor
				 */
				case 'wp_editor':
					$v                                       = do_shortcode( stripslashes( self::kses_body_markup( $value[ $section_key ][ $key ] ) ) );
					$value[ $section_key ][ $key . '_meta' ] = wpautop( $v );
					$value[ $section_key ][ $key ] = $v;
					break;
				case 'html_editor':
					// Do not allow to users without unfiltered_html permissions to change html content.
					// Keep previous content instead.
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						$value[ $section_key ][ $key ] = $current_value[ $section_key ][ $key ];
					} else {
						$value[ $section_key ][ $key ] = self::kses_markup( $value[ $section_key ][ $key ] );
					}

					break;
				case 'css_editor':
					// Using `strip_tags` as done in `wp_custom_css_cb` (https://developer.wordpress.org/reference/functions/wp_custom_css_cb/).
					$value[ $section_key ][ $key ] = strip_tags( $value[ $section_key ][ $key ] );
					break;
				case 'number':
					$value[ $section_key ][ $key ] = intval( $value[ $section_key ][ $key ] );
					/**
					 * Check minimum
					 *
					 * @since 3.1.2
					 */
					if ( isset( $data['min'] ) ) {
						$value[ $section_key ][ $key ] = max( $data['min'], $value[ $section_key ][ $key ] );
					}
					/**
					 * Check maximum
					 *
					 * @since 3.1.2
					 */
					if ( isset( $data['max'] ) ) {
						$value[ $section_key ][ $key ] = min( $data['max'], $value[ $section_key ][ $key ] );
					}
					break;
				case 'text':
					if ( isset( $value[ $section_key ][ $key ] ) ) {
						$value[ $section_key ][ $key ] = sanitize_text_field( stripslashes( $value[ $section_key ][ $key ] ) );
					}
					break;
				case 'email':
					if ( isset( $value[ $section_key ][ $key ] ) ) {
						$value[ $section_key ][ $key ] = sanitize_email( stripslashes( $value[ $section_key ][ $key ] ) );
					}
					break;
				default:
					break;
			}

			return apply_filters( 'branda_sanitize_input_by_type', $value, $type, $section_key, $key, $current_value, $data, $this->module );
		}

		/**
		 * Sanitizes html markup separatelly for head and body.
		 *
		 * @param string $data
		 * @return string
		 */
		public static function kses_markup( $data ) {
			if ( self::is_html( $data ) ) {
				preg_match( "/<head[^>]*>(.*?)<\/head>/is", $data, $head_matches );

				if ( ! empty( $head_matches ) ) {
					$head_content      = $head_matches[1];
					$allowed_head_tags = apply_filters( 'branda_esc_html_editor_head_tags_allowed', '<title><meta><base><style>' );
					$data              = str_replace( $head_content, strip_tags( $head_content, $allowed_head_tags ), $data );
				}

				preg_match( "/<body[^>]*>(.*?)<\/body>/is", $data, $body_matches );
				add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'kses_allow_style_tag' ) );

				if ( ! empty( $body_matches ) ) {
					$body_content = $body_matches[1];
					$data         = str_replace( $body_content, wp_kses_post( $body_content ), $data );
				}

				if ( empty( $head_matches ) && empty( $body_matches ) ) {
					$data = wp_kses_post( $data );
				}

				remove_filter( 'wp_kses_allowed_html', array( __CLASS__, 'kses_allow_style_tag' ) );
			} else {
				$data = esc_html( $data );
			}

			return $data;
		}

		/**
		 * Sanitizes html markup. Expected to be markup in body. It does not separate head and body.
		 *
		 * @param string $data
		 * @return string
		 */
		public static function kses_body_markup( $data ) {
			if ( self::is_html( $data ) ) {
				add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'kses_allow_style_tag' ) );

				$data = wp_kses_post( $data );

				remove_filter( 'wp_kses_allowed_html', array( __CLASS__, 'kses_allow_style_tag' ) );
			} else {
				$data = esc_html( $data );
			}

			return $data;
		}

		/**
		 * Checks if content has html markup.
		 *
		 * @param string $content
		 * @return boolean
		 */
		public static function is_html( $content ) {
			return $content !== wp_strip_all_tags( $content );
		}

		public static function kses_allow_style_tag( $allowed_tags ) {
			$allowed_tags['style'] = array();
			return $allowed_tags;
		}

		/**
		 * Update whole value
		 *
		 * @since 1.9.5
		 */
		protected function update_value( $value ) {
			$value['plugin_version'] = $this->build;

			branda_update_option( $this->option_name, $value );
			$this->data = $value;
			return true;
		}

		/**
		 * Change bottom save button params.
		 *
		 * @param string $args   Current args.
		 * @param array  $module Current module.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function change_bottom_save_button( $args, $module ) {
			if ( $this->module === $module['module'] ) {
				unset( $args['icon'] );

				$args['text'] = __( 'Save Settings', 'ub' );
			}
			return $args;
		}

		/**
		 * get base url
		 *
		 * @since 1.8.9
		 */
		protected function get_base_url() {
			$url = '';
			if ( ! is_admin() ) {
				return $url;
			}
			$screen = get_current_screen();
			if ( ! is_object( $screen ) ) {
				return $url;
			}
			$args = array(
				'page' => $screen->parent_base,
			);
			if ( isset( $_REQUEST['tab'] ) ) {
				$args['tab'] = $_REQUEST['tab'];
			}
			if ( is_network_admin() ) {
				$url = add_query_arg( $args, network_admin_url( 'admin.php' ) );
			} else {
				$url = add_query_arg( $args, admin_url( 'admin.php' ) );
			}
			return $url;
		}

		/**
		 * Notice wraper for Static SUI notice.
		 * https://wpmudev.github.io/shared-ui/notifications/
		 *
		 * @since 3.4
		 *
		 * @param string $message Message to display(should be escaped).
		 * @param string $type Optional. SUI Notice type class.
		 */
		public static function sui_notice( $message, $type = 'error' ) {
			$id = uniqid( 'branda-' );

			switch ( $type ) {
				case 'success':
					$icon = 'check-tick';
					break;
				case 'warning':
				case 'error':
					$icon = 'warning-alert';
					break;
				case 'loading':
					$icon = 'loader';
					break;
				default:
					$icon = 'info';
					break;
			}

			$text  = '<div class="sui-notice-content"><div class="sui-notice-message">';
			$text .= '<span class="sui-notice-icon sui-icon-' . $icon . ' sui-md" aria-hidden="true"></span>';
			$text .= '<p>' . $message . '</p>';
			$text .= '</div></div>';

			$content  = '<div id="' . esc_attr( $id ) . '" class="sui-notice sui-notice-' . esc_attr( $type ) . '">';
			$content .= $text;
			$content .= '</div>';

			return $content;
		}

		/**
		 * Notice wraper for Inline SUI notice.
		 * https://wpmudev.github.io/shared-ui/notifications/
		 *
		 * @since 3.4
		 *
		 * @param string $id HTML ID.
		 * @param string $type Optional. SUI Notice type class.
		 */
		public static function sui_inline_notice( $id, $type = 'error' ) {
			$content  = '<div id="' . esc_attr( $id ) . '" class="sui-notice sui-notice-' . esc_attr( $type ) . '" role="alert" aria-live="assertive">';
			$content .= '</div>';

			return $content;
		}

		/**
		 * Handle filter for option name, it should be overwrite by module
		 * method.
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( $module === $this->module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		/**
		 * Remove "Save Changes" button from page.
		 *
		 * @since 1.9.2
		 */
		public function disable_save() {
			add_filter( 'ultimatebranding_settings_panel_show_submit', '__return_false' );
		}

		/**
		 * get nonce action
		 *
		 * @since 1.9.4
		 *
		 * @param string  $name nonce name
		 * @param integer $user_id User ID.
		 * @return nonce action name
		 */
		protected function get_nonce_action_name( $name = 'default', $user_id = 0 ) {
			if ( 0 === $user_id ) {
				$user_id = get_current_user_id();
			}
			$nonce_action = sprintf(
				'%s_%s_%d',
				__CLASS__,
				$name,
				$user_id
			);
			return $nonce_action;
		}

		/**
		 * Load SocialLogos style.
		 * https://wpcalypso.wordpress.com/devdocs/design/social-logos
		 *
		 * @since 1.9.7
		 * @since 3.0.0 Added `module_key` argument.
		 *
		 * @param string $module_key Module key to load module related css.
		 */
		protected function load_social_logos_css( $module_key = '' ) {
			$deps = array();
			if ( ! empty( $module_key ) ) {
				$module = $this->get_module_by_module( $module_key );
				if ( ! empty( $module ) ) {
					$file  = sprintf(
						'modules/%s/assets/css/%s.css',
						$module['group'],
						$module_key
					);
					$check = branda_files_dir( $file );
					if ( is_file( $check ) ) {
						$file = branda_files_url( $file );
						wp_register_style( $module_key, $file, array(), $this->build, 'screen' );
						$deps[] = $module_key;
					}
				}
			}
			$url = $this->get_social_logos_css_url();
			wp_enqueue_style( 'SocialLogos', $url, $deps, '2.0.0', 'screen' );
		}

		/**
		 * Get SocialLogos style URL.
		 * https://wpcalypso.wordpress.com/devdocs/design/social-logos
		 *
		 * @since 1.9.7
		 */
		protected function get_social_logos_css_url() {
			$url = branda_url( 'external/icon-font/social-logos.css' );
			return $url;
		}

		/**
		 * SocialLogos social icons.
		 * https://wpcalypso.wordpress.com/devdocs/design/social-logos
		 *
		 * @since 1.9.7
		 */
		private function get_social_media_array() {
			$social = array(
				'amazon'      => array( 'label' => __( 'Amazon', 'ub' ) ),
				'blogger'     => array( 'label' => __( 'Blogger', 'ub' ) ),
				'codepen'     => array( 'label' => __( 'CodePen', 'ub' ) ),
				'dribbble'    => array( 'label' => __( 'Dribbble', 'ub' ) ),
				'dropbox'     => array( 'label' => __( 'Dropbox', 'ub' ) ),
				'eventbrite'  => array( 'label' => __( 'Eventbrite', 'ub' ) ),
				'facebook'    => array( 'label' => __( 'Facebook', 'ub' ) ),
				'flickr'      => array( 'label' => __( 'Flickr', 'ub' ) ),
				'foursquare'  => array( 'label' => __( 'Foursquare', 'ub' ) ),
				'ghost'       => array( 'label' => __( 'Ghost', 'ub' ) ),
				'github'      => array( 'label' => __( 'Github', 'ub' ) ),
				'instagram'   => array( 'label' => __( 'Instagram', 'ub' ) ),
				'linkedin'    => array( 'label' => __( 'LinkedIn', 'ub' ) ),
				'mail'        => array( 'label' => __( 'Mail', 'ub' ) ),
				'pinterest'   => array( 'label' => __( 'Pinterest', 'ub' ) ),
				'pocket'      => array( 'label' => __( 'Pocket', 'ub' ) ),
				'polldaddy'   => array( 'label' => __( 'Polldaddy', 'ub' ) ),
				'reddit'      => array( 'label' => __( 'Reddit', 'ub' ) ),
				'skype'       => array( 'label' => __( 'Skype', 'ub' ) ),
				'spotify'     => array( 'label' => __( 'Spotify', 'ub' ) ),
				'squarespace' => array( 'label' => __( 'Squarespace', 'ub' ) ),
				'stumbleupon' => array( 'label' => __( 'Stumbleupon', 'ub' ) ),
				'telegram'    => array( 'label' => __( 'Telegram', 'ub' ) ),
				'tumblr'      => array( 'label' => __( 'Tumblr', 'ub' ) ),
				'twitter'     => array( 'label' => __( 'Twitter', 'ub' ) ),
				'vimeo'       => array( 'label' => __( 'Vimeo', 'ub' ) ),
				'whatsapp'    => array( 'label' => __( 'Whatsapp', 'ub' ) ),
				'wordpress'   => array( 'label' => __( 'WordPress', 'ub' ) ),
				'xanga'       => array( 'label' => __( 'Xanga', 'ub' ) ),
				'youtube'     => array( 'label' => __( 'Youtube', 'ub' ) ),
			);
			/**
			 * Sort in case when translation made a mess
			 */
			uasort( $social, array( $this, 'get_social_media_array_sort_helper' ) );
			return $social;
		}

		/**
		 * Private sort helper for get_social_media_array() array.
		 */
		private function get_social_media_array_sort_helper( $a, $b ) {
			return strcmp( strtolower( $a['label'] ), strtolower( $b['label'] ) );
		}

		/**
		 * Replace URL with protocol with related URL.
		 *
		 * @since 1.9.7
		 *
		 * @param string $url URL
		 * @return string $url
		 */
		protected function make_relative_url( $url ) {
			if ( empty( $url ) ) {
				return;
			}
			if ( ! is_string( $url ) ) {
				return;
			}
			$re = sprintf( '@^(%s|%s)@', set_url_scheme( home_url(), 'http' ), set_url_scheme( home_url(), 'https' ) );
			$to = set_url_scheme( home_url(), 'relative' );
			return preg_replace( $re, $to, $url );
		}

		/**
		 * CSS border style
		 *
		 * @since 1.9.7
		 */
		protected function css_border_options() {
			$options = array(
				'dotted' => __( 'Dotted', 'ub' ),
				'dashed' => __( 'Dashed', 'ub' ),
				'solid'  => __( 'Solid', 'ub' ),
				'double' => __( 'Double', 'ub' ),
				'groove' => __( '3D grooved', 'ub' ),
				'ridge'  => __( '3D ridged', 'ub' ),
				'inset'  => __( '3D inset', 'ub' ),
				'outset' => __( '3D outset', 'ub' ),
			);
			return $options;
		}

		protected function css_background_color( $color ) {
			if ( empty( $color ) ) {
				$color = 'transparent';
			}
			$css = sprintf( 'background-color: %s;', $color );
			return $css;
		}

		protected function css_color( $color ) {
			if ( empty( $color ) ) {
				$color = 'inherit';
			}
			$css = sprintf( 'color: %s;', $color );
			return $css;
		}

		protected function css_width( $width, $units = 'px' ) {
			if ( empty( $width ) ) {
				return '';
			}
			$css = sprintf( 'width: %s%s;', $width, $units );
			return $css;
		}

		protected function css_height( $height, $units = 'px' ) {
			if ( empty( $height ) ) {
				return '';
			}
			return sprintf( 'height: %s%s;', $height, $units );
		}

		/**
		 * CSS Radius
		 *
		 * @since 2.2.0
		 */
		protected function css_radius( $radius, $units = 'px' ) {
			if ( 0 === $radius ) {
				$units = '';
			}
			$keys = array( '-webkit-border-radius', '-moz-border-radius', 'border-radius' );
			$css  = '';
			foreach ( $keys as $key ) {
				$css .= sprintf( '%s: %s%s;', $key, $radius, esc_attr( $units ) );
			}
			return $css;
		}

		/**
		 * CSS color.
		 *
		 * @since 1.9.6
		 *
		 * @param array   $section Configuration section.
		 * @param string  $key Configuration key.
		 * @param string  $selector CSS selector.
		 * @param boolean $echo Print or return data.
		 */
		protected function css_color_from_data( $section, $key, $selector, $echo = true ) {
			$css   = '';
			$value = $this->get_value( $section, $key );
			if ( ! empty( $value ) ) {
				$css .= sprintf( '%s{color:%s}', $selector, $value );
				$css .= PHP_EOL;
			}
			if ( $echo ) {
				echo $css;
				return;
			}
			return $css;
		}

		/**
		 * CSS background color.
		 *
		 * @since 1.9.6
		 *
		 * @param array   $section Configuration section.
		 * @param string  $key Configuration key.
		 * @param string  $selector CSS selector.
		 * @param boolean $echo Print or return data.
		 */
		protected function css_background_color_from_data( $section, $key, $selector, $echo = true ) {
			return $this->css_background_transparency( $section, $key, 100, $selector, $echo );
		}

		/**
		 * CSS background color with transparency.
		 *
		 * @since 1.9.6
		 *
		 * @param array   $section Configuration section.
		 * @param string  $key Configuration key.
		 * @param number  $transparency CSS transparency.
		 * @param string  $selector CSS selector.
		 * @param boolean $echo Print or return data.
		 */
		protected function css_background_transparency( $section, $key, $transparency, $selector, $echo = true ) {
			$css             = '';
			$bg_color        = $this->get_value( $section, $key );
			$bg_transparency = $this->get_value( $section, $transparency );
			if ( $bg_color ) {
				$css .= $selector;
				$css .= '{';
				if ( 0 < $bg_transparency && 100 !== $bg_transparency ) {
					$bg_color = $this->convert_hex_to_rbg( $bg_color );
					$css     .= sprintf( 'background-color:rgba(%s,%0.2f)', implode( ',', $bg_color ), $bg_transparency / 100 );
				} else {
					$css .= sprintf( 'background-color:%s', $bg_color );
				}
				$css .= '}';
				$css .= PHP_EOL;
			}
			if ( $echo ) {
				echo $css;
				return;
			}
			return $css;
		}

		/**
		 *
		 * @since 3.2.0 added $echo param
		 *
		 * @param boolean $echo Print or not to print.
		 */
		protected function css_opacity( $data, $key, $selector, $echo = true ) {
			$content = '';
			if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
				$content  = sprintf( '%s{opacity:%0.2f}', $selector, $data[ $key ] / 100 );
				$content .= PHP_EOL;
				if ( $echo ) {
					echo $content;
					return;
				}
			}
			return $content;
		}

		protected function css_hide( $data, $key, $selector ) {
			if ( isset( $data[ $key ] ) && 'off' == $data[ $key ] ) {
				printf( '%s{display:none}', $selector );
				echo PHP_EOL;
			}
		}

		/**
		 * CSS Box Shadow
		 *
		 * @since 3.1.0
		 *
		 * @param integer $horizontal Horizontal Length
		 * @param integer $vertical Vertical Length
		 * @param integer $blur Blur Radius
		 * @param integer $spread Spread Radius
		 * @param string  $color Shadow Color
		 * @param boolean $echo Print or return data, default true
		 */
		protected function css_box_shadow( $horizontal, $vertical, $blur, $spread, $color, $echo = true ) {
			$css = sprintf(
				'-webkit-box-shadow: %1$dpx %2$dpx %3$dpx %4$dpx %5$s;-moz-box-shadow: %1$dpx %2$dpx %3$dpx %4$dpx %5$s;box-shadow: %1$dpx %2$dpx %3$dpx %4$dpx %5$s;',
				$horizontal,
				$vertical,
				$blur,
				$spread,
				$color
			);
			if ( $echo ) {
				echo $css;
				echo PHP_EOL;
				return;
			}
			return $css;
		}

		/**
		 * Produce common CSS for document settings.
		 *
		 * @input string $section Section of data.
		 *
		 * @since 2.3.0
		 */
		protected function common_css_document( $selector = '.page' ) {
			$css = sprintf( '%s{', $selector );
			/**
			 * width
			 */
			$value = $this->get_value( 'design', 'content_width', 0 );
			$units = $this->get_value( 'design', 'content_width_units', 'px' );
			if ( 0 < $value ) {
				$css .= $this->css_width( $value, $units );
			}
			/**
			 * Radius
			 */
			$value = $this->get_value( 'design', 'content_radius', 0 );
			if ( 0 < $value ) {
				$css .= $this->css_radius( $value );
			}
			/**
			 * Colors: background
			 */
			$value = $this->get_value( 'colors', 'content_background', false );
			$css  .= $this->css_background_color( $value );
			/**
			 * Colors: color
			 */
			$value = $this->get_value( 'colors', 'content_color', false );
			$css  .= $this->css_color( $value );
			$css  .= '}';
			$css  .= PHP_EOL;
			return $css;
		}

		/**
		 * Produce common CSS for body settings.
		 *
		 * @input string $section Section of data.
		 *
		 * @since 2.3.0
		 */
		protected function common_css_body( $selector = 'body' ) {
			$css = sprintf( '%s{', $selector );
			/**
			 * Colors: background
			 */
			$value = $this->get_value( 'colors', 'document_background', false );
			if ( $value ) {
				$css .= $this->css_background_color( $value );
			}
			/**
			 * Colors: color
			 */
			$value = $this->get_value( 'colors', 'document_color', false );
			if ( $value ) {
				$css .= $this->css_color( $value );
			}
			$css .= '}';
			$css .= PHP_EOL;
			return $css;
		}

		/**
		 * HTML for background slider
		 *
		 * @since 3.1.0
		 *
		 * @param boolean $echo Print or return.
		 *
		 * Based on:
		 * https://tympanus.net/codrops/2012/01/02/fullscreen-background-image-slideshow-with-css3/
		 */
		protected function html_background_common( $echo = true ) {
			$value = $this->get_value( 'content', 'content_background' );
			if ( ! is_array( $value ) ) {
				return;
			}
			if ( 2 > count( $value ) ) {
				return;
			}
			// Return nothing if it's not a slideshow
			$mode = $this->get_value( 'design', 'background_mode' );
			if ( 'slideshow' !== $mode ) {
				return;
			}
			$content = '<ul class="cb-slideshow">';
			foreach ( $value as $v ) {
				$content .= '<li><span></span></li>';
			}
			$content .= '</ul>';
			if ( $echo ) {
				echo $content;
				return;
			}
			return $content;
		}

		/**
		 * Prepare COMMON css background!
		 *
		 * @since 2.3.0
		 * @since 3.2.0 added $echo param
		 *
		 * @param string  $selector HTML element selector.
		 * @param boolean $echo Print or not to print.
		 */
		protected function css_background_common( $selector = 'html', $echo = true, $wrap = true ) {
			$data      = $this->get_value( 'content' );
			$args      = array(
				'id'                    => $this->get_name(),
				'selector'              => $selector,
				'selector2'             => sprintf( '%s, .branda-background-mask', $selector ),
				'background_color'      => $this->get_value( 'colors', 'background_color' ),
				'background_position_x' => 'center',
				'background_position_y' => 'center',
				'background_size'       => array( 'auto', 'auto' ),
			);
			$css       = '';
			$selector2 = sprintf( '%s, .branda-background-mask', $selector );
			/**
			 * background-color
			 */
			$css .= $this->css_background_color_from_data( 'colors', 'background_color', $selector2, false );
			/**
			 * background-image
			 */
			$show = true;
			$v    = $this->get_value( 'content', 'content_background' );
			if ( $show && is_array( $v ) && 0 < count( $v ) ) {
				if ( 0 < count( $v ) && isset( $v[0]['meta'] ) ) {
					$css .= sprintf( '%s {', $selector2 );
					/**
					 * Background Size
					 */
					$mode = $this->get_value( 'design', 'background_size' );
					if ( 'manual' === $mode ) {
						$width  = intval( $this->get_value( 'design', 'background_size_width', 0 ) );
						$height = intval( $this->get_value( 'design', 'background_size_height', 0 ) );
						if ( 0 === $width ) {
							$width = 'auto';
						} else {
							$width .= '%';
						}
						if ( 0 === $height ) {
							$height = 'auto';
						} else {
							$height .= '%';
						}
					}
					switch ( $mode ) {
						case 'cover':
						case 'contain':
							$body_styles             = ' body {margin: 0;}';
							$args['background_size'] = $mode;
							$css                    .= sprintf( 'height: 100vh; -webkit-background-size: %1$s; -moz-background-size: %1$s; -o-background-size: %1$s; background-size: %1$s;', $mode );
							break;
						case 'manual':
							$args['background_size'] = array( $width, $height );
							$css                    .= sprintf( 'background-size: %s %s;', $width, $height );
							break;
						default:
							break;
					}
					/**
					 * Background: X
					 */
					$value = $this->get_value( 'design', 'background_position_x', 'center' );
					switch ( $value ) {
						case 'left':
						case 'right':
						case 'center':
							$args['background_position_x'] = $value;
							$css                          .= sprintf( 'background-position-x: %s;', $value );
							break;
						case 'custom':
							$unit                          = $this->get_value( 'design', 'background_position_x_units', '%' );
							$value                         = $this->get_value( 'design', 'background_position_x_custom', 0 );
							$css                          .= sprintf( 'background-position-x: %d%s;', $value, $unit );
							$args['background_position_x'] = $value . $unit;
							break;
						default:
							break;
					}
					/**
					 * Background: Y
					 */
					$value = $this->get_value( 'design', 'background_position_y', 'center' );
					switch ( $value ) {
						case 'top':
						case 'bottom':
						case 'center':
							$css                          .= sprintf( 'background-position-y: %s;', $value );
							$args['background_position_y'] = $value;
							break;
						case 'custom':
							$unit                          = $this->get_value( 'design', 'background_position_y_units', '%' );
							$value                         = $this->get_value( 'design', 'background_position_y_custom', 0 );
							$css                          .= sprintf( 'background-position-y: %d%s;', $value, $unit );
							$args['background_position_y'] = $value . $unit;
							break;
						default:
							break;
					}
					/**
					 * Background Mode
					 */
					$mode = $this->get_value( 'design', 'background_mode' );
					$id   = 0;
					if ( 'slideshow' !== $mode ) {
						do {
							$id = rand( 0, count( $v ) - 1 );
						} while ( ! isset( $v[ $id ]['meta'] ) );
					}
					/**
					 * Crop image
					 */
					$url = $v[ $id ]['meta'][0];
					
					if ( empty( $v[ $id ]['cropped'] ) && isset( $v[ $id ]['value'] ) ) {
						$url = $this->maybe_crop( $v[ $id ], $id );
						$crop_type = $this->get_value( 'design', 'background_crop', 'auto' );

						if ( empty( $crop_type ) ) {
							$crop_type = 'background_crop';
						}

						$v[ $id ]['cropped'] = $crop_type;

						$this->set_value( 'content', 'content_background', $v );
					}
					$css  .= sprintf( 'background-image: url(%s);', $this->make_relative_url( esc_url( $url ) ) );
					$css  .= 'background-repeat: no-repeat;';
					$value = $this->get_value( 'design', 'background_attachment', 'unset' );
					if ( preg_match( '/^(scroll|fixed)$/', $value ) ) {
						$css .= sprintf( 'background-attachment: %s;', esc_attr( $value ) );
					}
					$css .= '}';
					$css .= PHP_EOL;
					if ( 'slideshow' === $mode && 1 < count( $v ) ) {
						$images = array();
						foreach ( $v as $id => $one ) {
							if ( isset( $one['meta'] ) ) {
								$url = $one['meta'][0];

								if ( empty( $v[ $id ]['cropped'] ) ) {
									$original_url = wp_get_attachment_url( intval( $v[ $id ]['value'] ) );
									$url          = $this->maybe_crop( $v[ $id ], $id );

									if ( filter_var( $url, FILTER_VALIDATE_URL ) && $original_url !== $url ) {
										$new_attachment_id = attachment_url_to_postid( $url );

										if ( ! empty( $new_attachment_id ) ) {
											$v[ $id ]['value'] = $new_attachment_id;
											//$this->set_value( 'content', 'content_background', $v );
										}

										$crop_type = $this->get_value( 'design', 'background_crop', 'auto' );

										if ( empty( $crop_type ) ) {
											$crop_type = 'background_crop';
										}

										$v[ $id ]['cropped'] = $crop_type;

										$this->set_value( 'content', 'content_background', $v );
									}
								}
								
								$images[] = $url = $this->make_relative_url( $url );
							}
						}
						if ( 1 < count( $images ) ) {
							$args['id']       = $this->get_name( 'slideshow' );
							$args['duration'] = intval( $this->get_value( 'design', 'background_duration' ) );
							$args['images']   = $images;
							$template         = 'front-end/common/css/slideshow';
							$content          = $this->render( $template, $args, true );
							if ( $wrap ) {
								$content = $this->wrap_css_background( $content );
							}
							if ( $echo ) {
								echo $content;
								return;
							}
							return $content;
						}
					}
				}
			}
			$content = '';
			if ( ! empty( $css ) ) {
				$content .= $css;
				if ( isset( $body_styles ) ) {
					$content .= $body_styles;
				}
				if ( $wrap ) {
					$content = $this->wrap_css_background( $content );
				}
			}
			if ( $echo ) {
				echo $content;
				return;
			}
			return $content;
		}

		/**
		 * Wrap content by <style> HTML-tag
		 *
		 * @param string $content
		 * @return string
		 */
		private function wrap_css_background( $content ) {
			$before = sprintf(
				'<style id="%s" type="text/css">',
				$this->get_name( 'background' )
			);
			$after  = '</style>';

			return $before . $content . $after;
		}

		/**
		 * Prepare COMMON css logo!
		 *
		 * @since 2.3.0
		 * @since 3.1.0 added $echo param
		 * @since 3.1.1 added $force_a param
		 *
		 * @param string  $selector HTML element selector.
		 * @param boolean $echo Print or not to print.
		 * @param boolean $echo Force A tag.
		 */
		protected function css_logo_common( $selector, $echo = true, $force_a = false ) {
			$content = '';
			$show    = $this->get_value( 'content', 'logo_show', 'on' );
			if ( 'off' === $show ) {
				$content = sprintf(
					'%s{display: none;}%s',
					$selector,
					PHP_EOL
				);
				if ( $echo ) {
					echo $content;
					return;
				}
				return $content;
			}
			/**
			 * rounded_form
			 */
			$rounded = intval( $this->get_value( 'design', 'logo_rounded' ) );
			if ( 0 < $rounded ) {
				$content .= $selector . ', ' . $selector . ' a {';
				$content .= $this->css_radius( $rounded );
				$content .= '}';
			}
			/**
			 * logo
			 */
			$src        = $this->get_value( 'content', 'logo_image', false );
			$width      = $height = 'auto';
			$data       = $this->get_value( 'content', 'logo_image_meta', false );
			$logo_width = intval( $this->get_value( 'design', 'logo_width', false ) );
			if ( is_array( $data ) ) {
				$src    = $data[0];
				$width  = max( 1, intval( $data[1] ) );
				$height = max( 1, intval( $data[2] ) );
				if ( 0 < $logo_width ) {
					$scale  = $logo_width / $width;
					$width  = $logo_width;
					$height = intval( $height * $scale );
				} elseif ( $width > 320 ) {
					$scale  = 320 / $width;
					$height = intval( $height * $scale );
					$width  = intval( $width * $scale );
				}
			} elseif ( 0 < $logo_width && false === $data ) {
				$width = $height = $logo_width;
			}
			/**
			 * margin
			 */
			$margins = array(
				'top'    => 0,
				'right'  => 'auto',
				'bottom' => 0,
				'left'   => 'auto',
			);
			$value   = $this->get_value( 'design', 'logo_position', 'center' );
			switch ( $value ) {
				case 'left':
					$margins['left'] = 0;
					break;
				case 'right':
					$margins['right'] = 0;
					break;
				default:
			}
			$logo_margin_units = $this->get_value( 'design', 'logo_margin_units', 'px' );
			foreach ( $this->positions as $position ) {
				$value = $this->get_value( 'design', 'logo_margin_' . $position, false );
				if ( false === $value ) {
					continue;
				}
				if ( 'auto' === $margins[ $position ] ) {
					continue;
				}
				$margins[ $position ] = sprintf( '%d%s', $value, esc_attr( $logo_margin_units ) );
			}
			$url = $this->get_value( 'content', 'logo_url' );
			if ( ! empty( $src ) ) {
				$content .= $selector . ', ' . $selector . ' a {';
				$content .= 'display: block;';
				$content .= '}';
				$content .= PHP_EOL;
				if ( $force_a || ! empty( $url ) ) {
					$content .= $selector . ' a {';
				} else {
					$content .= $selector . ' {';
				}
				$content .= sprintf( 'background: transparent url(%s) no-repeat 50%% 5%%;', $this->make_relative_url( $src ) );
				$content .= 'background-size: contain;';
				$content .= sprintf( 'margin: %s;', implode( ' ', array_values( $margins ) ) );
				$content .= 'overflow: hidden;';
				$content .= 'text-indent: -9999px;';
				if ( 'auto' !== $height ) {
					$content .= sprintf( 'height: %dpx;', $height );
				}
				if ( 'auto' !== $width ) {
					$content .= sprintf( 'width: %dpx;', $width );
				}
				$content .= PHP_EOL;
				$content .= '}';
				$content .= PHP_EOL;
			} elseif ( is_array( $data ) && 0 < $data['width'] ) {
				if ( $force_a || ! empty( $url ) ) {
					$content .= $selector . ' a {';
				} else {
					$content .= $selector . ' {';
				}
				$content .= 'background-size: 100%;';
				$content .= 'background-repeat: no-repeat%;';
				$content .= sprintf( 'margin: %s;', implode( ' ', array_values( $margins ) ) );
				if ( 'auto' !== $data['width'] ) {
					$content .= sprintf( 'height: %dpx;', $data['width'] );
					$content .= sprintf( 'width: %dpx;', $data['width'] );
				}
				$content .= PHP_EOL;
				$content .= '}';
				$content .= PHP_EOL;
			}
			/**
			 * logo_transparency
			 */
			$design   = $this->get_value( 'design' );
			$content .= $this->css_opacity( $design, 'logo_opacity', $selector, $echo );
			/**
			 * margin
			 */
			$keys   = array( 'top', 'right', 'bottom', 'left' );
			$margin = array();
			foreach ( $keys as $key ) {
				$name  = sprintf( 'logo_margin_%s', $key );
				$value = isset( $design[ $name ] ) ? intval( $design[ $name ] ) : 0;
				if ( 0 < $value ) {
					$value .= $this->get_value( 'design', 'logo_margin_units', 'px' );
				}
				if ( 0 === $value && preg_match( '/^(left|right)$/', $key ) ) {
					$value = 'auto';
				}
				$margin[ $key ] = $value;
			}
			/**
			 * position
			 */
			if ( isset( $design['logo_position'] ) ) {
				switch ( $design['logo_position'] ) {
					case 'left':
						$margin['left']  = 0;
						$margin['right'] = 'auto';
						break;
					case 'right':
						$margin['left']  = 'auto';
						$margin['right'] = '0';
						break;
					case 'center':
						$margin['left']  = 'auto';
						$margin['right'] = 'auto';
						break;
					default:
						break;
				}
			}
			if ( ! empty( $margin ) ) {
				$content .= sprintf(
					'%s%s{margin:%s;}%s',
					$selector,
					empty( $url ) ? '' : ' a',
					implode( ' ', $margin ),
					PHP_EOL
				);
			}
			if ( $echo ) {
				echo $content;
				return;
			}
			return $content;
		}

		/**
		 * Convert color from RGB to HEX.
		 *
		 * @since 1.9.6
		 */
		protected function convert_hex_to_rbg( $hex ) {
			if ( preg_match( '/^#.{6}$/', $hex ) ) {
				return sscanf( $hex, '#%02x%02x%02x' );
			}
			return $hex;
		}

		/**
		 * Helper to enqueue scripts/styles
		 *
		 * @since 1.9.9
		 *
		 * @param $handle
		 * @param $src
		 * @param bool   $version
		 */
		protected function enqueue( $handle, $src = false, $version = false, $dependencies = array() ) {
			$is_script = preg_match( '/js$/', $src );
			$src       = plugins_url( 'assets/' . $src, $this->file );
			$version   = $version ? $version : $this->build;

			if ( $is_script ) {
				wp_enqueue_script( $handle, $src, $dependencies, $version, false );
			} else {
				wp_enqueue_style( $handle, $src, $dependencies, $version, 'all' );
			}
		}

		/**
		 * get the template
		 *
		 * @since 2.0.0
		 */
		protected function get_template( $file = 'index' ) {
			$file = sprintf(
				'%s/assets/templates/%s.html',
				dirname( $this->file ),
				sanitize_title( $file )
			);
			if ( is_file( $file ) && is_readable( $file ) ) {
				$file = file_get_contents( $file );
				return $file;
			}
			return __( 'Something went wrong!', 'ub' );
		}

		/**
		 * backgroun options
		 *
		 * @since 2.3.0
		 */
		protected function get_options_background( $defaults = array() ) {
			$data = array(
				'title'  => __( 'Background', 'ub' ),
				'fields' => array(
					'color'    => array(
						'type'    => 'color',
						'label'   => __( 'Background Color', 'ub' ),
						'default' => isset( $defaults['color'] ) ? $defaults['color'] : '#210101',
					),
					'show'     => array(
						'type'        => 'checkbox',
						'label'       => __( 'Display Background Images', 'ub' ),
						'description' => __( 'Would you like to use background images?', 'ub' ),
						'options'     => array(
							'off' => __( 'Hide', 'ub' ),
							'on'  => __( 'Show', 'ub' ),
						),
						'default'     => isset( $defaults['show'] ) ? $defaults['show'] : 'on',
						'classes'     => array( 'switch-button' ),
						'slave-class' => 'ub-default-background-show-related',
					),
					'mode'     => array(
						'type'    => 'radio',
						'label'   => __( 'Multiple Images Mode', 'ub' ),
						'options' => array(
							'slideshow' => __( 'Slideshow', 'ub' ),
							'random'    => __( 'Random', 'ub' ),
						),
						'default' => 'slideshow',
						'master'  => 'ub-default-background-show-related',
					),
					'image'    => array(
						'type'        => 'gallery',
						'label'       => __( 'Background image', 'ub' ),
						'description' => __( 'You can upload a background image here. The image will stretch to fit the page, and will automatically resize as the window size changes. You\'ll have the best results by using images with a minimum width of 1024px.', 'ub' ),
						'master'      => 'ub-default-background-show-related',
					),
					'duration' => array(
						'type'        => 'number',
						'label'       => __( 'Slideshow Duration', 'ub' ),
						'description' => __( 'Duration in minutes, we strongly recommended do not use less than 5 minutes.', 'ub' ),
						'default'     => 10,
						'min'         => 1,
						'max'         => 60,
						'after'       => __( 'Minutes', 'ub' ),
						'classes'     => array( 'ui-slider' ),
						'master'      => array(
							'section' => 'background',
							'field'   => 'mode',
							'value'   => 'slideshow',
							'master'  => 'ub-default-background-show-related',
						),
					),
				),
			);
			/**
			 * Allow to change background options.
			 *
			 * @since 2.3.0
			 *
			 * @param array $data Background options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'ub_get_options_background', $data, $defaults, $this->module );
		}

		protected function get_options_fields( $group, $sections, $defaults = array() ) {
			$data = array();
			foreach ( $sections as $section ) {
				$function = array(
					$this,
					sprintf( 'get_options_fields_%s_%s', $group, $section ),
				);
				if ( is_callable( $function ) ) {
					$section_defaults = isset( $defaults[ $section ] ) ? $defaults[ $section ] : array();
					$data            += call_user_func( $function, $section_defaults );
				} elseif ( self::is_debug() ) {
					error_log( sprintf( 'Missing callback: %s', $function[1] ) );
				}
			}
			return $data;
		}

		/**
		 * Common Options: Reset
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_reset( $id, $defaults = array() ) {
			$data = array(
				'reset' => array(
					'type'              => 'button',
					'value'             => __( 'Reset', 'ub' ),
					'data'              => array(
						'nonce'   => $this->get_nonce_value( $id, 'reset' ),
						'section' => $id,
						'module'  => $this->module,
					),
					'sui'               => array(
						'ghost',
					),
					'before'            => '<div class="sui-row">',
					'after'             => '</div>',
					'container-classes' => array(
						'branda-section-reset',
					),
					'classes'           => array(
						'branda-reset-section',
					),
				),
			);
			return $data;
		}

		/**
		 * Common Options: Content -> Logo
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_logo( $defaults = array() ) {
			$data = array(
				'logo_image'         => array(
					'type'         => 'media',
					'master'       => $this->get_name( 'logo' ),
					'master-value' => 'on',
					'label'        => __( 'Logo', 'ub' ),
					'description'  => array(
						'content'  => __( 'Preferred width of logo is 320px for best results.', 'ub' ),
						'position' => 'bottom',
					),
					'display'      => 'sui-tab-content',
					'group'        => array(
						'begin' => true,
					),
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Logo &amp; Background', 'ub' ),
					),
				),
				'logo_url'           => array(
					'type'         => 'text',
					'label'        => __( 'URL', 'ub' ),
					'description'  => array(
						'content'  => __( 'Users will get redirected to this link when they click on logo.', 'ub' ),
						'position' => 'bottom',
					),
					'default'      => esc_url( isset( $defaults['url'] ) ? $defaults['url'] : '' ),
					'master'       => $this->get_name( 'logo' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'logo_alt'           => array(
					'type'         => 'text',
					'label'        => __( 'Alt Text', 'ub' ),
					'default'      => esc_html( isset( $defaults['alt'] ) ? $defaults['alt'] : '' ),
					'master'       => $this->get_name( 'logo' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'logo_show'          => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Logo visibility', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => isset( $defaults['show'] ) ? $defaults['show'] : 'on',
					'slave-class' => $this->get_name( 'logo' ),
					'group'       => array(
						'end' => true,
					),
				),
				'content_background' => array(
					'type'        => 'gallery',
					'label'       => __( 'Background image', 'ub' ),
					'description' => array(
						'content'  => __( 'For best results, image should have a minimum width of 1024px. You can upload multiple images and create a slide-show in the design section.', 'ub' ),
						'position' => 'bottom',
					),
					'accordion'   => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Content -> Error Message
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_error_message( $defaults = array() ) {
			$data = array(
				'content_title'   => array(
					'label'     => __( 'Title (optional)', 'ub' ),
					'default'   => isset( $defaults['content_title'] ) ? $defaults['content_title'] : '',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Error Message', 'ub' ),
					),
				),
				'content_content' => array(
					'type'      => 'wp_editor',
					'label'     => __( 'Description (optional)', 'ub' ),
					'default'   => isset( $defaults['content_content'] ) ? $defaults['content_content'] : '',
					'accordion' => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Content Social Media options fields
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_social( $defaults = array() ) {
			$social_media     = $this->get_options_social_media( $defaults );
			$value            = $this->get_value( 'content' );
			$social_media_new = array();
			$data             = array();
			/**
			 * tmplate
			 */
			$args     = array(
				'buttom_open_label' => __( 'Open item', 'ub' ),
				'field_label'       => __( 'URL', 'ub' ),
			);
			$template = '/admin/common/options/social-media-row';
			if ( ! empty( $value ) && is_array( $value ) ) {
				foreach ( $value as $name => $val ) {
					if ( empty( $val ) ) {
						continue;
					}
					if ( ! preg_match( '/^social_media_(.+)$/', $name, $matches ) ) {
						continue;
					}
					$key = $matches[1];
					if ( ! isset( $social_media[ $key ] ) ) {
						continue;
					}
					$args['id']                     = $key;
					$args['label']                  = $social_media[ $key ]['label'];
						$args['value']              = $val;
					$one                            = array(
						'type'    => 'raw',
						'content' => $this->render( $template, $args, true ),
					);
					$data[ $name ]                  = $one;
					$social_media[ $key ]['hidden'] = true;

				}
			}
			$data['add-new-button'] = array(
				'type'      => 'button',
				'value'     => __( 'Add Accounts', 'ub' ),
				'icon'      => 'plus',
				'sui'       => 'ghost',
				'after'     => '',
				'accordion' => array(
					'end' => true,
					'box' => false,
				),
				'classes'   => array(
					'branda-social-logo-add-dialog-button',
				),
				'data'      => array(
					'modal-open' => $this->get_nonce_action( 'social', 'media', 'add' ),
					'modal-mask' => 'true',
				),
			);
			/**
			 * Add open accordion
			 */
			$keys                               = array_keys( $data );
			$key                                = $keys[0];
			$data[ $key ]['accordion']['begin'] = true;
			$data[ $key ]['accordion']['box']   = false;
			$data[ $key ]['accordion']['title'] = __( 'Social Accounts', 'ub' );
			if ( 1 > count( $data ) ) {
				$key = 'add-new-button';
			}
			$data['add-new-button']['before_field']  = '';
			$data[ $key ]['before_field']            = '<div class="sui-accordion branda-sui-accordion-sortable social-logo-color branda-social-logos-main-container">';
			$data['add-new-button']['before_field'] .= '</div>';
			/**
			 * dialog
			 */
			$content  = sprintf(
				'<p class="sui-description">%s</p>',
				esc_html__( 'Choose the platforms to insert into your social sharing module.', 'ub' )
			);
			$content .= '<div class="sui-box-selectors">';
			$content .= '<ul>';
			foreach ( $social_media as $k => $value ) {
				$id       = $this->get_nonce_action( 'social-media', $k );
				$content .= sprintf(
					'<li class="branda-social-logo-li-%s%s" data-id="%s"><label for="%s" class="sui-box-selector">',
					esc_attr( $k ),
					isset( $value['hidden'] ) && $value['hidden'] ? ' hidden' : '',
					esc_attr( $k ),
					esc_attr( $id )
				);
				$content .= sprintf(
					'<input type="checkbox" name="%s" id="%s" value="%s" data-label="%s" />',
					esc_attr( $id ),
					esc_attr( $id ),
					esc_attr( $k ),
					esc_attr( $value['label'] )
				);
				$content .= '<span class="branda-social-logo-container">';
				$content .= sprintf(
					'<span class="social-logo social-logo-%s"></span><span class="social-media-title">%s</span>',
					esc_attr( $k ),
					esc_html( $value['label'] )
				);
				$content .= '</span>';
				$content .= '</label></li>';
			}
			$content .= '</ul>';
			$content .= '</div>';
			/**
			 * Footer
			 */
			$footer                           = '';
			$args                             = array(
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
				'data' => array(
					'modal-close' => '',
				),
			);
			$footer                          .= $this->button( $args );
			$args                             = array(
				'text'  => __( 'Add Accounts', 'ub' ),
				'sui'   => '',
				'class' => 'branda-social-logo-add-accounts ' . $this->get_name( 'add' ),
				'data'  => array(
					'nonce'    => wp_create_nonce( $this->get_nonce_action( 'social', 'media', 'add' ) ),
					'dialog'   => $this->get_nonce_action( 'social', 'media', 'add' ),
					'template' => $this->get_name( 'social-media-item' ),
				),
			);
			$footer                          .= $this->button( $args );
			$args                             = array(
				'id'           => $this->get_nonce_action( 'social', 'media', 'add' ),
				'content'      => $content,
				'footer'       => array(
					'content' => $footer,
					'classes' => array(
						'sui-space-between',
					),
				),
				'title'        => __( 'Add Social Account', 'ub' ),
				'confirm_type' => false,
				'classes'      => array( 'branda-social-logo-add-dialog', 'social-logo-color', 'sui-modal-lg' ),
			);
			$data['add-new-button']['after'] .= $this->sui_dialog( $args );
			/**
			 * template
			 */
			if ( is_a( $this->uba, 'Branda_Admin' ) ) {
				$args                             = array(
					'id'                => '{{{data.id}}}',
					'label'             => '{{{data.label}}}',
					'buttom_open_label' => __( 'Open item', 'ub' ),
					'field_label'       => __( 'URL', 'ub' ),
					'value'             => '',
				);
				$template                         = '/admin/common/options/social-media-row';
				$content                          = '<div class="sui-form-field simple-option simple-option-raw">';
				$content                         .= $this->uba->render( $template, $args, true );
				$content                         .= '</div>';
				$data['add-new-button']['after'] .= sprintf(
					'<script type="text/html" id="tmpl-%s">%s</script>',
					esc_attr( $this->get_name( 'social-media-item' ) ),
					$content
				);
			}
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Content -> Reset
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_reset( $defaults = array() ) {
			return $this->get_options_fields_reset( 'content', $defaults );
		}

		/**
		 * Common Options: Design -> Logo
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_logo( $defaults = array() ) {
			$data = array(
				'logo_width'         => array(
					'type'         => 'number',
					'label'        => __( 'Width', 'ub' ),
					'default'      => intval( isset( $defaults['logo_width'] ) ? $defaults['logo_width'] : 84 ),
					'min'          => 0,
					'max'          => 320,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Logo', 'ub' ),
					),
					'description'  => array(
						'content'  => __( 'It should be smaller than the form canvas (320px).', 'ub' ),
						'position' => 'bottom',
					),
				),
				'logo_opacity'       => array(
					'type'         => 'number',
					'label'        => __( 'Opacity', 'ub' ),
					'min'          => 0,
					'max'          => 100,
					'default'      => intval( isset( $defaults['logo_opacity'] ) ? $defaults['logo_opacity'] : 100 ),
					'after_label'  => '%',
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'logo_position'      => array(
					'type'    => 'sui-tab',
					'label'   => __( 'Position', 'ub' ),
					'options' => array(
						'left'   => __( 'Left', 'ub' ),
						'center' => __( 'Center', 'ub' ),
						'right'  => __( 'Right', 'ub' ),
					),
					'default' => isset( $defaults['position'] ) ? $defaults['position'] : 'center',
				),
				'logo_margin_top'    => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'default'      => intval( isset( $defaults['logo_margin_top'] ) ? $defaults['logo_margin_top'] : 0 ),
					'min'          => 0,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Margin', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'logo_margin_units',
					),
				),
				'logo_margin_right'  => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'default'      => intval( isset( $defaults['logo_margin_right'] ) ? $defaults['logo_margin_right'] : 0 ),
					'min'          => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'logo_margin_bottom' => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'default'      => intval( isset( $defaults['logo_margin_bottom'] ) ? $defaults['logo_margin_bottom'] : 25 ),
					'min'          => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'logo_margin_left'   => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'default'      => intval( isset( $defaults['logo_margin_left'] ) ? $defaults['logo_margin_left'] : 0 ),
					'min'          => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end' => true,
					),
				),
				'logo_rounded'       => array(
					'type'        => 'number',
					'label'       => __( 'Corner radius', 'ub' ),
					'min'         => 0,
					'default'     => intval( isset( $defaults['logo_rounded'] ) ? $defaults['logo_rounded'] : 0 ),
					'after_label' => __( 'px', 'ub' ),
					'accordion'   => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Design -> Background
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_background( $defaults = array() ) {
			$data = array(
				'background_duration'      => array(
					'type'         => 'number',
					'label'        => __( 'Duration', 'ub' ),
					'after_label'  => __( 'seconds', 'ub' ),
					'description'  => array(
						'content'  => __( 'We strongly recommend duration of at least 5 seconds.', 'ub' ),
						'position' => 'bottom',
					),
					'default'      => 10,
					'min'          => 1,
					'master'       => $this->get_name( 'design-background-mode' ),
					'master-value' => 'slideshow',
					'display'      => 'sui-tab-content',
				),
				'background_mode'          => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Multiple images mode', 'ub' ),
					'options'     => array(
						'slideshow' => __( 'Slideshow', 'ub' ),
						'random'    => __( 'Random', 'ub' ),
					),
					'default'     => 'slideshow',
					'slave-class' => $this->get_name( 'design-background-mode' ),
					'accordion'   => array(
						'begin' => true,
						'title' => __( 'Background', 'ub' ),
					),
				),
				'background_crop_width_p'  => array(
					'type'         => 'number',
					'label'        => __( 'Width', 'ub' ),
					'after_label'  => __( '%', 'ub' ),
					'master'       => $this->get_name( 'design-background-crop' ),
					'master-value' => 'width',
					'display'      => 'sui-tab-content',
					'default'      => 50,
					'max'          => 100,
				),
				'background_crop_height_p' => array(
					'type'         => 'number',
					'label'        => __( 'Height', 'ub' ),
					'after_label'  => __( '%', 'ub' ),
					'master'       => $this->get_name( 'design-background-crop' ),
					'master-value' => 'height',
					'display'      => 'sui-tab-content',
					'default'      => 50,
					'max'          => 100,
				),
				'background_crop_width'    => array(
					'type'         => 'number',
					'label'        => __( 'Width', 'ub' ),
					'after_label'  => __( 'px', 'ub' ),
					'master'       => $this->get_name( 'design-background-crop' ),
					'master-value' => 'fixed',
					'display'      => 'sui-tab-content',
					'default'      => 1920,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'background_crop_height'   => array(
					'type'         => 'number',
					'label'        => __( 'Height', 'ub' ),
					'after_label'  => __( 'px', 'ub' ),
					'master'       => $this->get_name( 'design-background-crop' ),
					'master-value' => 'fixed',
					'display'      => 'sui-tab-content',
					'default'      => 1200,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'background_crop'          => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Background crop', 'ub' ),
					'description' => array(
						'content' => __( 'This setting work only with images added to WordPress Media Library.', 'ub' ),
					),
					'options'     => array(
						'auto'   => array(
							'label'   => __( 'Auto', 'ub' ),
							'tooltip' => __( 'Do not change crop.', 'ub' ),
						),
						'height' => array(
							'label'   => __( 'Height', 'ub' ),
							'tooltip' => __( 'Crop proportional, change height.', 'ub' ),
						),
						'width'  => array(
							'label'   => __( 'Width', 'ub' ),
							'tooltip' => __( 'Crop proportional, change width.', 'ub' ),
						),
						'fixed'  => array(
							'label'   => __( 'Fixed', 'ub' ),
							'tooltip' => __( 'Crop to fixed size', 'ub' ),
						),
					),
					'slave-class' => $this->get_name( 'design-background-crop' ),
					'default'     => 'auto',
				),
				'background_attachment'    => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Background attachment', 'ub' ),
					'description' => array(
						'content' => __( 'This CSS property sets whether a background image position is fixed within the viewport, or scrolls with its containing block.', 'ub' ),
					),
					'options'     => array(
						'unset'  => array(
							'label'   => __( 'Unset', 'ub' ),
							'tooltip' => __( 'Do not change.', 'ub' ),
						),
						'fixed'  => array(
							'label'   => __( 'Fixed', 'ub' ),
							'tooltip' => __( 'The background is fixed relative to the viewport. Even if an element has a scrolling mechanism, the background doesn\'t move with the element.', 'ub' ),
						),
						'scroll' => array(
							'label'   => __( 'Scroll', 'ub' ),
							'tooltip' => __( 'The background is fixed relative to the element itself and does not scroll with its contents.', 'ub' ),
						),
					),
					'default'     => 'unset',
				),
				'background_size_width'    => array(
					'type'         => 'number',
					'description'  => array(
						'content'  => __( 'Leave "0" to "auto" value.', 'ub' ),
						'position' => 'bottom',
					),
					'label'        => __( 'Width', 'ub' ),
					'after_label'  => __( '%', 'ub' ),
					'master'       => $this->get_name( 'design-background-size' ),
					'master-value' => 'manual',
					'display'      => 'sui-tab-content',
					'default'      => 0,
					'max'          => 100,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'background_size_height'   => array(
					'type'         => 'number',
					'description'  => array(
						'content'  => __( 'Leave "0" to "auto" value.', 'ub' ),
						'position' => 'bottom',
					),
					'label'        => __( 'Height', 'ub' ),
					'after_label'  => __( '%', 'ub' ),
					'master'       => $this->get_name( 'design-background-size' ),
					'master-value' => 'manual',
					'display'      => 'sui-tab-content',
					'default'      => 0,
					'max'          => 100,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'background_size'          => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Background size', 'ub' ),
					'options'     => array(
						'cover'   => array(
							'label'   => __( 'Cover', 'ub' ),
							'tooltip' => __( 'Resize the background image to cover the entire container, even if it has to stretch the image or cut a little bit off one of the edges.', 'ub' ),
						),
						'contain' => array(
							'label'   => __( 'Contain', 'ub' ),
							'tooltip' => __( 'Resize the background image to make sure the image is fully visible.', 'ub' ),
						),
						'none'    => array(
							'label'   => __( 'Auto', 'ub' ),
							'tooltip' => __( 'The background image is displayed in its original size.', 'ub' ),
						),
						'manual'  => array(
							'label'   => __( 'Length', 'ub' ),
							'tooltip' => __( 'Sets the width and height of the background image.', 'ub' ),
						),
					),
					'slave-class' => $this->get_name( 'design-background-size' ),
					'default'     => 'cover',
				),
				'background_position_x'    => array(
					'type'    => 'sui-tab-icon',
					'label'   => __( 'Horizontal background position', 'ub' ),
					'options' => array(
						'left'   => array(
							'label' => 'align-x-left',
						),
						'center' => array(
							'label' => 'align-x-center',
						),
						'right'  => array(
							'label' => 'align-x-right',
						),
						'custom' => array(
							'label' => __( 'Custom', 'ub' ),
							'type'  => 'text',
							'after' => $this->get_design_background_position( 'x' ),
						),
					),
					'default' => 'center',
					'units'   => array(
						'position' => 'field',
						'name'     => 'background_position_x_units',
						'default'  => '%',
					),
				),
				'background_position_y'    => array(
					'type'      => 'sui-tab-icon',
					'label'     => __( 'Vertical background position', 'ub' ),
					'options'   => array(
						'top'    => array(
							'label' => 'align-y-top',
						),
						'center' => array(
							'label' => 'align-y-center',
						),
						'bottom' => array(
							'label' => 'align-y-bottom',
						),
						'custom' => array(
							'label' => __( 'Custom', 'ub' ),
							'type'  => 'text',
							'after' => $this->get_design_background_position( 'y' ),
						),
					),
					'default'   => 'center',
					'accordion' => array(
						'end' => true,
					),
					'units'     => array(
						'position' => 'field',
						'name'     => 'background_position_y_units',
						'default'  => '%',
					),
				),
			);
			/**
			 * Allow to change content fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Design -> Background, helper for position
		 *
		 * @since 3.0.0
		 */
		private function get_design_background_position( $type ) {
			$content = '';
			$key     = sprintf( 'background_position_%s_custom', $type );
			$value   = $this->get_value( 'design', $key, '' );

			$content .= sprintf(
				'<input type="number" name="simple_options[design][%s]" value="%d" class="sui-form-control sui-input-sm" />',
				esc_attr( $key ),
				esc_attr( $value )
			);
			return $content;
		}

		/**
		 * Common Options: Design -> Document
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_document( $defaults = array() ) {
			$data = array(
				'content_width'  => array(
					'type'         => 'number',
					'label'        => __( 'Width', 'ub' ),
					'description'  => array(
						'content'  => __( 'It should preferably be less than 1024 for best results.', 'ub' ),
						'position' => 'bottom',
					),
					'default'      => intval( isset( $defaults['width'] ) ? $defaults['width'] : 700 ),
					'min'          => 100,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Content Wrapper', 'ub' ),
					),
					'units'        => array(
						'position' => 'field',
						'name'     => 'content_width_units',
					),
				),
				'content_radius' => array(
					'type'         => 'number',
					'label'        => __( 'Corner radius', 'ub' ),
					'min'          => 0,
					'default'      => intval( isset( $defaults['radius'] ) ? $defaults['radius'] : 0 ),
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'accordion'    => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data logo options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Design -> Social Media
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_social( $defaults = array() ) {
			$data = array(
				'social_media_show'   => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Show', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => esc_attr( isset( $defaults['social_media_show'] ) ? $defaults['social_media_show'] : 'off' ),
					'accordion'   => array(
						'begin' => true,
						'title' => __( 'Social Accounts', 'ub' ),
					),
					'classes'     => array(
						'branda-social-media-show',
					),
					'description' => array(
						'content'  => self::sui_notice( __( 'Options below are not affected.', 'ub' ), 'warning' ),
						'position' => 'bottom',
					),
				),
				'social_media_target' => array(
					'type'    => 'sui-tab',
					'label'   => __( 'Open link', 'ub' ),
					'options' => array(
						'_blank' => __( 'New', 'ub' ),
						'_self'  => __( 'The same', 'ub' ),
					),
					'default' => esc_attr( isset( $defaults['target'] ) ? $defaults['target'] : '_self' ),
				),
				'social_media_colors' => array(
					'type'              => 'sui-tab',
					'label'             => __( 'Icon style', 'ub' ),
					'options'           => array(
						'color'      => __( 'Colors', 'ub' ),
						'monochrome' => __( 'Monochrome', 'ub' ),
					),
					'default'           => esc_attr( isset( $defaults['colors'] ) ? $defaults['colors'] : 'monochrome' ),
					'accordion'         => array(
						'end' => true,
					),
					'container-classes' => array(
						'branda-icons-style',
					),
					'after'             => '<ul class="branda-social-logo-preview"><li><span class="social-logo social-logo-facebook"></span></li><li><span class="social-logo social-logo-twitter"></span></li><li><span class="social-logo social-logo-ghost"></span></li><li><span class="social-logo social-logo-flickr"></span></li><li><span class="social-logo social-logo-spotify"></span></li></ul>',
				),
			);
			/**
			 * Hide wrning
			 */
			$show = $this->get_value( 'design', 'social_media_show', 'off' );
			if ( 'on' === $show ) {
				$data['social_media_show']['description']['classes'][] = 'hidden';
			}
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data logo options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Design -> Reset
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_reset( $defaults = array() ) {
			return $this->get_options_fields_reset( 'design', $defaults );
		}

		/**
		 * Common Options: Colors -> Logo & Background
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_logo( $defaults = array() ) {
			$data = array(
				'document_color'      => array(
					'type'      => 'color',
					'label'     => __( 'Color', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Logo & Background', 'ub' ),
					),
					'default'   => esc_attr( isset( $defaults['document_color'] ) ? $defaults['document_color'] : '#000000' ),
				),
				'document_background' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'accordion' => array(
						'end' => true,
					),
					'default'   => esc_attr( isset( $defaults['document_background'] ) ? $defaults['document_background'] : '#f1f1f1' ),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Background
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_background( $defaults = array() ) {
			$data = array(
				'background_color' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Background', 'ub' ),
						'end'   => true,
					),
					'default'   => esc_attr( isset( $defaults['background_color'] ) ? $defaults['background_color'] : '#f1f1f1' ),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Error Message
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_error_messages( $defaults = array() ) {
			$data = array(
				'error_messages_background'  => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Error Messages', 'ub' ),
						'item'  => array(
							'classes' => array(
								$this->get_name( 'error-message' ),
							),
						),
					),
					'default'   => esc_attr( isset( $defaults['error_messages_background'] ) ? $defaults['error_messages_background'] : '#fff' ),
					'panes'     => array(
						'begin'      => true,
						'title'      => __( 'Default', 'ub' ),
						'begin_pane' => true,
					),
				),
				'error_messages_border'      => array(
					'type'    => 'color',
					'label'   => __( 'Border', 'ub' ),
					'default' => esc_attr( isset( $defaults['error_messages_border'] ) ? $defaults['error_messages_border'] : '#dc3232' ),
				),
				'error_messages_text'        => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => esc_attr( isset( $defaults['error_messages_text'] ) ? $defaults['error_messages_text'] : '#444' ),
				),
				'error_messages_link'        => array(
					'type'    => 'color',
					'label'   => __( 'Link', 'ub' ),
					'default' => esc_attr( isset( $defaults['error_messages_link'] ) ? $defaults['error_messages_link'] : '#0073aa' ),
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * active
				 */
				'error_messages_link_active' => array(
					'type'    => 'color',
					'label'   => __( 'Link', 'ub' ),
					'default' => esc_attr( isset( $defaults['error_messages_link_active'] ) ? $defaults['error_messages_link_active'] : '#0073aa' ),
					'panes'   => array(
						'title'      => __( 'Active', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * Focus
				 */
				'error_messages_link_focus'  => array(
					'type'    => 'color',
					'label'   => __( 'Link', 'ub' ),
					'default' => esc_attr( isset( $defaults['error_messages_link_focus'] ) ? $defaults['error_messages_link_focus'] : '#124964' ),
					'panes'   => array(
						'title'      => __( 'Focus', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * hover
				 */
				'error_messages_link_hover'  => array(
					'type'      => 'color',
					'label'     => __( 'Link', 'ub' ),
					'default'   => esc_attr( isset( $defaults['error_messages_link_hover'] ) ? $defaults['error_messages_link_hover'] : '#00a0d2' ),
					'panes'     => array(
						'title'      => __( 'hover', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
						'end'        => true,
					),
					'accordion' => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Document
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_document( $defaults = array() ) {
			$data = array(
				'content_background' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Content Wrapper', 'ub' ),
						'end'   => true,
					),
					'default'   => esc_attr( isset( $defaults['document_color'] ) ? $defaults['document_color'] : '#ddd' ),
					'data'      => array(
						'alpha' => 'true',
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: colors -> Reset
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_reset( $defaults = array() ) {
			return $this->get_options_fields_reset( 'colors', $defaults );
		}

		/**
		 * Social Media Settings
		 *
		 * @since 2.3.0
		 */
		protected function get_options_social_media_settings( $defaults = array() ) {
			$data = array(
				'title'  => __( 'Social Media Settings', 'ub' ),
				'fields' => array(
					'show'                         => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show', 'ub' ),
						'description' => __( 'Would you like to show social media?', 'ub' ),
						'options'     => array(
							'off' => __( 'Hide', 'ub' ),
							'on'  => __( 'Show', 'ub' ),
						),
						'default'     => esc_attr( isset( $defaults['show'] ) ? $defaults['show'] : 'on' ),
						'classes'     => array( 'switch-button' ),
						'slave-class' => 'social-media',
					),
					'colors'                       => array(
						'type'        => 'checkbox',
						'label'       => __( 'Colors', 'ub' ),
						'description' => __( 'Would you like show colored icons?', 'ub' ),
						'options'     => array(
							'on'  => __( 'Colors', 'ub' ),
							'off' => __( 'Monochrome', 'ub' ),
						),
						'default'     => esc_attr( isset( $defaults['colors'] ) ? $defaults['colors'] : 'off' ),
						'classes'     => array( 'switch-button' ),
						'master'      => 'social-media',
					),
					'social_media_link_in_new_tab' => array(
						'type'        => 'checkbox',
						'label'       => __( 'Open Links', 'ub' ),
						'description' => __( 'Would you like open link in new or the same window/tab?', 'ub' ),
						'options'     => array(
							'on'  => __( 'New', 'ub' ),
							'off' => __( 'The same', 'ub' ),
						),
						'default'     => esc_attr( isset( $defaults['social_media_link_in_new_tab'] ) ? $defaults['social_media_link_in_new_tab'] : 'off' ),
						'classes'     => array( 'switch-button' ),
						'master'      => 'social-media',
					),
				),
			);
			/**
			 * Allow to change Social Media Settings options.
			 *
			 * @since 2.3.0
			 *
			 * @param array $data logo options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'ub_get_options_social_media_settings', $data, $defaults, $this->module );
		}

		/**
		 * Social Media
		 *
		 * @since 2.3.0
		 */
		protected function get_options_social_media( $defaults = array() ) {
			$data = $this->get_social_media_array();
			/**
			 * Allow to change Social Media Settings options.
			 *
			 * @since 2.3.0
			 *
			 * @param array $data logo options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'ub_get_options_social_media', $data, $defaults, $this->module );
		}

		/**
		 * Common options Social Media
		 *
		 * @since 2.3.0
		 */
		protected function common_options_social_media( $context = '' ) {
			$data  = array(
				'social_media' => '',
				'body_classes' => array(),
				'stylesheet'   => '',
			);
			$value = $this->get_value( 'design', 'social_media_show', 'unset' );
			if ( 'off' === $value ) {
				return $data;
			}
			$config             = $this->get_options_social_media();
			$content_data       = $this->get_value( 'content' );
			$social_media_links = array();
			foreach ( $config as $key => $value ) {
				$name = $this->get_social_media_name( $key );
				if ( isset( $content_data[ $name ] ) && ! empty( $content_data[ $name ] ) ) {
					$social_media_links[ $name ] = array(
						'key' => $key,
						'url' => $content_data[ $name ],
					);
				}
			}
			if ( empty( $social_media_links ) ) {
				return $data;
			}
			$social_media = $stylesheet = '';
			$body_classes = array();
			$color        = $this->get_value( 'design', 'social_media_colors', 'monochrome' );
			if ( 'color' === $color ) {
				$body_classes[] = 'use-color';
			}
			$target = $this->get_value( 'design', 'social_media_target', '_self' );
			if ( '_blank' === $target ) {
				$target = ' target="_blank"';
			} else {
				$target = '';
			}
			foreach ( $content_data as $name => $value ) {
				$key = $url = false;
				if ( isset( $social_media_links[ $name ] ) ) {
					$url = $social_media_links[ $name ]['url'];
					$key = $social_media_links[ $name ]['key'];
				}
				if ( empty( $url ) ) {
					continue;
				}
				$social_media .= sprintf(
					'<li><a href="%s"%s><span class="social-logo social-logo-%s"></span></a></li>%s',
					esc_url( $url ),
					$target,
					esc_attr( $key ),
					self::is_debug() ? PHP_EOL : ''
				);
			}
			if ( ! empty( $social_media ) ) {
				$body_classes[] = 'has-social';
				$social_media   = sprintf( '<div id="social"><ul>%s</ul></div>', $social_media );
				$stylesheet     = $context
					? sprintf( 'ub-social-logos-%s-styling', $context )
					: 'ub-social-logos-styling';
				wp_enqueue_style( $stylesheet, $this->get_social_logos_css_url(), array(), $this->build, 'all' );
			}
			return array(
				'social_media' => $social_media,
				'body_classes' => $body_classes,
				'stylesheet'   => $stylesheet,
			);
		}

		/**
		 * Get related modules by section
		 *
		 * @since 2.3.0
		 *
		 * @param string $section Section.
		 */
		private function get_related_modules( $section ) {
			if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
				return array();
			}
			$related = $this->uba->get_related();
			if ( ! isset( $related[ $section ] ) ) {
				return array();
			}
			if ( 2 > count( $related[ $section ] ) ) {
				return array();
			}
			return $related[ $section ];
		}

		/**
		 * Add copy options.
		 *
		 * Allow top copy selected settings between modules.
		 *
		 * @since 2.3.0
		 *
		 * @param string $content Current footer.
		 * @param string $module Current module.
		 * @param string $section Current section.
		 */
		public function add_copy_options( $content, $module, $section ) {
			$modules = array();
			/**
			 * check current module
			 */
			if ( $module !== $this->module ) {
				return $content;
			}
			/**
			 * get modules
			 */
			$modules = $this->get_related_modules( $section );
			if ( empty( $modules ) ) {
				return $content;
			}
			/**
			 * add
			 */
			/**
			 * Turn off copy
			 */
			if ( 0 ) {
				$option_name = $this->get_module_option_name( '', $module );
				$content    .= sprintf(
					'<div class="ub-copy-section-settings" data-module="%s" data-section="%s" data-nonce="%s"><label>',
					esc_attr( $option_name ),
					esc_attr( $section ),
					wp_create_nonce( 'ub-copy-section-settings-' . $option_name )
				);
				$content    .= '<select>';
				$content    .= sprintf( '<option value="-1">%s</option>', esc_html__( '-- Copy from --', 'ub' ) );
				foreach ( $modules as $key => $data ) {
					if ( $key === $module ) {
						continue;
					}
					$content .= sprintf(
						'<option value="%s">%s</a>',
						esc_attr( $data['option'] ),
						esc_html( $data['title'] )
					);
				}
				$content .= '</select>';
				$content .= sprintf( '<a href="#" class="button">%s</a>', esc_html__( 'Copy', 'ub' ) );
				$content .= '</label></div>';
			}
			return $content;
		}

		private function add_related( $related, $section ) {
			if ( ! isset( $related[ $section ] ) ) {
				$related[ $section ] = array();
			}
			$uba           = branda_get_uba_object();
			$dir           = branda_files_dir( 'modules/' );
			$dir           = wp_normalize_path( $dir );
			$re            = sprintf( '@%s@', $dir );
			$module        = preg_replace( $re, '', $this->file );
			$configuration = $uba->get_configuration();
			$title         = __( 'Unknown', 'ub' );
			if (
				isset( $configuration[ $module ] )
				&& isset( $configuration[ $module ]['name'] )
			) {
				$title = $configuration[ $module ]['name'];
			}
			$related[ $section ][ $this->module ] = array(
				'title'  => $title,
				'module' => $module,
				'option' => $this->get_module_option_name( '', $this->module ),
			);
			return $related;
		}

		public function add_related_logo( $related ) {
			return $this->add_related( $related, 'logo' );
		}

		public function add_related_background( $related ) {
			return $this->add_related( $related, 'background' );
		}

		public function add_related_social_media_settings( $related ) {
			return $this->add_related( $related, 'social_media_settings' );
		}

		public function add_related_social_media( $related ) {
			return $this->add_related( $related, 'social_media' );
		}

		/**
		 * Common options Social Media
		 *
		 * @since 2.3.0
		 */
		protected function common_options_document( $defaults = array(), $remove = array() ) {
			$args = array(
				'title'  => __( 'Document', 'ub' ),
				'fields' => array(
					'title_show'              => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show Title', 'ub' ),
						'description' => __( 'Would you like to show title?', 'ub' ),
						'options'     => array(
							'off' => __( 'Off', 'ub' ),
							'on'  => __( 'On', 'ub' ),
						),
						'default'     => 'off',
						'classes'     => array( 'switch-button' ),
						'slave-class' => 'title',
					),
					'title'                   => array(
						'label'   => __( 'Title', 'ub' ),
						'default' => '',
						'master'  => 'title',
					),
					'content_show'            => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show Content', 'ub' ),
						'description' => __( 'Would you like to show content?', 'ub' ),
						'options'     => array(
							'off' => __( 'Off', 'ub' ),
							'on'  => __( 'On', 'ub' ),
						),
						'default'     => 'off',
						'classes'     => array( 'switch-button' ),
						'slave-class' => 'content',
					),
					'content'                 => array(
						'type'    => 'wp_editor',
						'label'   => __( 'Content', 'ub' ),
						'master'  => 'content',
						'default' => '',
					),
					'color'                   => array(
						'type'    => 'color',
						'label'   => __( 'Color', 'ub' ),
						'default' => '#000000',
					),
					'background'              => array(
						'type'    => 'color',
						'label'   => __( 'Background Color', 'ub' ),
						'default' => '#f1f1f1',
					),
					'background_transparency' => array(
						'type'    => 'number',
						'label'   => __( 'Background Transparency', 'ub' ),
						'min'     => 0,
						'max'     => 100,
						'default' => 0,
						'classes' => array( 'ui-slider' ),
						'after'   => '%',
					),
					'width'                   => array(
						'type'    => 'number',
						'label'   => __( 'Width', 'ub' ),
						'default' => 600,
						'min'     => 0,
						'max'     => 2000,
						'classes' => array( 'ui-slider' ),
					),
				),
			);
			$args = array_replace_recursive( $args, $defaults );
			$args = $this->remove_from_array( $args, $remove );
			return $args;
		}

		private function remove_from_array( $args, $remove ) {
			foreach ( $remove as $section => $one ) {
				if ( is_string( $one ) && isset( $args[ $one ] ) ) {
					unset( $args[ $one ] );
				} elseif ( is_array( $one ) && isset( $args[ $section ] ) ) {
					$args[ $section ] = $this->remove_from_array( $args[ $section ], $one );
				}
			}
			return $args;
		}

		/**
		 * rename option name
		 *
		 * @since 2.3.0
		 */
		public function rename_option_name() {
			if ( ! empty( $this->old_option_name ) ) {
				$value = branda_get_option( $this->old_option_name );
				if ( ! empty( $value ) ) {
					$this->update_value( $value );
					branda_delete_option( $this->old_option_name );
				}
			}
		}

		/**
		 * upgrade plugin settinfs
		 *
		 * @since 2.3.0
		 */
		public function upgrade_plugin_settings() {
			/**
			 * turn on custom-login-screen instead custom-login-css
			 */
			$is_active = branda_is_active_module( 'custom-login-css.php' );
			if ( $is_active ) {
				if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
					$this->uba = new Branda_Admin();
				}
				$this->uba->activate_module( 'custom-login-screen.php' );
			}
		}

		protected function debug( $arg, $module, $start = true ) {
			if ( ! self::is_debug() ) {
				return;
			}
			$value = is_array( $arg ) ? serialize( $arg ) : $arg;
			printf(
				'%s<!-- %s: %s [%s] %s -->%s',
				PHP_EOL,
				$start ? 'begin' : 'end',
				'Branda',
				$module,
				$value,
				PHP_EOL
			);
		}

		protected function bold( $a ) {
			return sprintf( '<b>%s</b>', $a );
		}

		/**
		 * default options for open link in new tab.
		 *
		 * @since 2.3.0
		 *
		 * @param array $defaults Default paramters.
		 *
		 * @return array $args Array of params.
		 */
		public function get_options_link_in_new_tab( $defaults = array() ) {
			$args = array(
				'type'    => 'sui-tab',
				'label'   => __( 'Open link', 'ub' ),
				'options' => array(
					'off' => __( 'Same Tab', 'ub' ),
					'on'  => __( 'New Tab', 'ub' ),
				),
				'default' => 'off',
			);
			$args = wp_parse_args( $defaults, $args );
			return $args;
		}

		/**
		 * add SUI button
		 */
		public function button( $args ) {
			$uba = branda_get_uba_object();
			return $uba->button( $args );
		}

		/**
		 * Reset module
		 *
		 * @since 3.0.0
		 */
		public function reset_module( $status ) {
			branda_delete_option( $this->option_name );
			return true;
		}

		protected function sui_dialog( $args ) {
			$id             = isset( $args['id'] ) ? $args['id'] : $this->generate_id( $args );
			$confirm_type   = ! isset( $args['confirm_type'] ) || false !== $args['confirm_type'];
			$common_classes = $confirm_type ? ' sui-content-center  sui-flatten' : '';
			$content        = sprintf(
				'<div class="sui-modal %s">',
				isset( $args['classes'] ) ? esc_attr( implode( ' ', $args['classes'] ) ) : '' // sui-modal-sm
			);

			$content .= sprintf(
				'<div id="%s" class="sui-modal-content" aria-modal="true" aria-labelledby="%s" role="dialog">',
				esc_attr( $id ),
				esc_attr( $id ) . '-title'
			);
			$content .= '<div class="sui-box" role="document">';
			/**
			 * Header
			 */
			$content .= '<div class="sui-box-header' . $common_classes . '">';
			$content .= '<button class="sui-button-icon sui-button-float--right" data-modal-close>'
					. '<i class="sui-icon-close sui-md" aria-hidden="true"></i>'
					. '<span class="sui-screen-reader-text">'
					. esc_html__( 'Close this modal', 'ub' )
					. '</span></button>';
			/**
			 * Title
			 */
			if ( isset( $args['title'] ) ) {
				$content .= sprintf(
					'<h3 class="sui-box-title%s" id="%s">%s</h3>',
					$confirm_type ? ' sui-lg' : '',
					esc_attr( $id ) . '-title',
					esc_html( $args['title'] )
				);
			}

			$content .= '</div>'; // sui-box-header.
			/**
			 * Content
			 */
			if ( isset( $args['content'] ) ) {
				$content .= '<div class="sui-box-body' . $common_classes . '">';
				$content .= $args['content'];
				$content .= '</div>';
			}
			/**
			 * footer
			 */
			if ( isset( $args['footer'] ) ) {
				$classes = array( 'sui-box-footer' );
				$footer  = $args['footer'];
				if ( is_array( $args['footer'] ) ) {
					if ( isset( $args['footer']['content'] ) ) {
						$footer = $args['footer']['content'];
					}
					if (
						isset( $args['footer']['classes'] )
						&& is_array( $args['footer']['classes'] )
					) {
						$classes = array_merge( $classes, $args['footer']['classes'] );
					}
					if ( ! empty( $common_classes ) && in_array( 'sui-space-between', $classes, true ) ) {
						$common_classes = str_replace( ' sui-content-center', '', $common_classes );
					}
				}
				if ( is_string( $footer ) ) {
					$content .= sprintf(
						'<div class="%s' . $common_classes . '">%s</div>',
						esc_attr( implode( ' ', $classes ) ),
						$footer
					);
				}
			}
			$content .= '</div>';// sui-box
			$content .= '</div>';// sui-modal-content
			$content .= '</div>';// sui-modal
			return $content;
		}

		public function sui_tabs( $config, $widget_id = 0, $flushed = false ) {
			$content = '<div class="sui-tabs">';
			if ( $flushed ) {
				$content = '<div class="sui-tabs sui-tabs-flushed">';
			}
			/**
			 * tabs: menu
			 */
			$tabs = '';
			$add  = true;
			foreach ( $config as $one ) {
				if ( ! isset( $one['tab'] ) ) {
					continue;
				}
				$tabs .= sprintf(
					'<div%s>%s</div>',
					$add ? ' class="active"' : '',
					esc_html( $one['tab'] )
				);
				$add   = false;
			}
			if ( ! empty( $tabs ) ) {
				$content .= sprintf( '<div data-tabs="">%s</div>', $tabs );
			}
			/**
			 * tabs: panes
			 */
			if ( ! empty( $tabs ) ) {
				$content .= '<div data-panes="">';
			}
			$add = true;
			foreach ( $config as $one ) {
				if ( ! empty( $tabs ) ) {
					$content .= sprintf(
						'<div%s>',
						$add ? ' class="active"' : ''
					);
					$add      = false;
				}
				$tab = '';
				if ( isset( $one['tab_name'] ) ) {
					$tab = $one['tab_name'];
				} elseif ( isset( $one['id'] ) ) {
					$tab = $one['id'];
				} else {
					$tab = $this->generate_id( $data );
				}
				$content .= $this->proceed_sui_config( $one['fields'], $widget_id, $tab );
				if ( ! empty( $tabs ) ) {
					$content .= '</div>';
				}
			}
			if ( ! empty( $tabs ) ) {
				$content .= '</div>';
			}
			/**
			 * close
			 */
			$content .= '</div>';
			return $content;
		}

		protected function proceed_sui_config( $config, $widget_id = 0, $tab = '' ) {
			$row     = false;
			$content = '';
			foreach ( $config as $name => $data ) {
				/**
				 * group
				 */
				if (
					isset( $data['group'] )
					&& isset( $data['group']['begin'] )
					&& true === $data['group']['begin']
				) {
					$group_classes = array();
					// Add group classes.
					if ( ! empty( $data['group']['classes'] ) ) {
						$group_classes = array_merge( $group_classes, (array) $data['group']['classes'] );
					}
					$content .= sprintf(
						'<div class="%s">',
						esc_attr( implode( ' ', $group_classes ) )
					);
				}
				if ( isset( $data['sui-row'] ) && 'begin' === $data['sui-row'] ) {
					$row      = true;
					$content .= '<div class="sui-row">';
				}
				if ( $row ) {
					$content .= '<div class="sui-col">';
				}
				$id    = sanitize_title(
					sprintf(
						'branda-%s-%s-%s',
						$tab,
						$name,
						$widget_id
					)
				);
				$value = isset( $data['value'] ) ? $data['value'] : '';
				if (
					isset( $data['divider'] )
					&& isset( $data['divider']['position'] )
					&& 'before' === $data['divider']['position']
				) {
					$content .= '<div class="branda-divider"></div>';
				}
				$classes = array(
					'sui-form-field',
					sprintf(
						'branda%s-%s',
						empty( $tab ) ? '' : '-' . esc_attr( preg_replace( '/[\[\]]\+/', '-', sanitize_title( $tab ) ) ),
						esc_attr( $name )
					),
				);
				if (
					isset( $data['classes'] )
					&& is_array( $data['classes'] )
				) {
					$classes = array_merge( $classes, $data['classes'] );
				}
				$show = isset( $data['hide-th'] ) && true === $data['hide-th'] ? false : true;
				if ( $show ) {
					$content .= sprintf(
						'<div class="%s">',
						esc_attr( implode( ' ', $classes ) )
					);
				}
				if ( isset( $data['label'] ) ) {
					$content .= sprintf(
						'<label for="%s" class="sui-label">%s</label>',
						esc_attr( $id ),
						esc_html( $data['label'] )
					);
				}
				/**
				 * description
				 */
				$content .= $this->get_description( $data, 'top' );
				$extra    = array();
				/**
				 * style
				 */
				if ( isset( $data['style'] ) && is_string( $data['style'] ) ) {
					$extra[] = sprintf( 'style="%s"', esc_attr( $data['style'] ) );
				}
				/**
				 * placeholder
				 */
				if ( isset( $data['placeholder'] ) && is_string( $data['placeholder'] ) ) {
					$extra[] = sprintf( 'placeholder="%s"', esc_attr( $data['placeholder'] ) );
				}
				$type = isset( $data['type'] ) ? $data['type'] : 'text';
				if ( 'number' === $type ) {
					$extra[] = 'min="1"';
				}
				/**
				 * data
				 */
				if ( isset( $data['data'] ) ) {
					foreach ( $data['data'] as $data_key => $data_value ) {
						$extra[] = sprintf(
							'data-%s="%s"',
							sanitize_title( $data_key ),
							esc_attr( $data_value )
						);
					}
				}
				/**
				 * Before field
				 *
				 * @since 2.0.7
				 */
				if (
					isset( $data['field'] )
					&& isset( $data['field']['before'] )
				) {
					$content .= $data['field']['before'];
				}
				switch ( $type ) {
					case 'text':
					case 'url':
					case 'number':
						$extra[]  = 'aria-describedby="input-description"';
						$extra[]  = 'class="sui-form-control"';
						$content .= sprintf(
							'<input id="%s" type="%s" name="branda[%s]" value="%s" data-default="%s" data-required="%s" %s />',
							esc_attr( $id ),
							esc_attr( $type ),
							esc_attr( $name ),
							esc_attr( $value ),
							isset( $data['default'] ) ? esc_attr( $data['default'] ) : '',
							isset( $data['required'] ) && $data['required'] ? 'required' : 'no',
							implode( ' ', $extra )
						);
						break;
					case 'checkboxes':
						$columns = isset( $data['columns'] ) ? $data['columns'] : 1;
						$counter = 0;
						foreach ( $data['options'] as $checkbox_value => $checkbox_label ) {
							if ( 1 < $columns ) {
								if ( 0 === $counter % $columns ) {
									$content .= '<div class="sui-row">';
								}
								$content .= sprintf(
									'<div class="sui-col-md-%d">',
									12 / $columns
								);
							}
							$content .= '<label class="sui-checkbox">';
							$content .= sprintf(
								'<input type="checkbox" name="branda[%s][%s]" value="%s" %s %s />',
								esc_attr( $name ),
								esc_attr( $checkbox_value ),
								esc_attr( $checkbox_value ),
								checked( 1, array_key_exists( $checkbox_value, $value ), false ),
								implode( ' ', $extra )
							);
							$content .= '<span></span>';
							$content .= sprintf(
								'<span>%s</span>',
								esc_html( $checkbox_label )
							);
							$content .= '</label>';
							if ( 1 < $columns ) {
								$content .= '</div>';
								$counter++;
								if ( 0 === $counter % $columns ) {
									$content .= '</div>';
								}
							}
						}
						if ( 1 < $columns && 0 !== $counter % $columns ) {
							$content .= '</div>';
						}
						break;
					case 'wp_editor':
						$args = array(
							'textarea_name' => sprintf( 'branda[%s]', $name ),
							'textarea_rows' => 9,
							'teeny'         => true,
						);
						ob_start();
						wp_editor( stripslashes( $value ), $id, $args );
						$content .= ob_get_contents();
						ob_end_clean();
						break;
					case 'html_editor':
						if ( ! is_string( $value ) ) {
							$value = '';
						}
						$data['classes'][] = 'ub_' . $data['type'];
						$content          .= sprintf(
							'<textarea id="%s" name="branda[%s]" class="%s">%s</textarea>',
							esc_attr( $id ),
							esc_attr( $name ),
							isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
							esc_attr( stripslashes( $value ) )
						);
						break;
					/**
					 * SUI tab
					 */
					case 'sui-tab':
						$content     .= '<div class="sui-side-tabs sui-tabs">';
						$content     .= '<div class="sui-tabs-menu">';
						$tabs_content = '';
						foreach ( $data['options'] as $radio_value => $radio_label ) {
							$tab_id   = sanitize_title( $this->get_nonce_action( $widget_id, $name, $radio_value ) );
							$content .= sprintf(
								'<label class="sui-tab-item%s">',
								$value === $radio_value ? ' active' : ''
							);
							$content .= sprintf(
								'<input type="radio" name="branda[%s]" value="%s" %s data-name="%s" data-tab-menu="%s" data-default="%s" />',
								esc_attr( $name ),
								esc_attr( $radio_value ),
								checked( $value, $radio_value, false ),
								sanitize_title( esc_attr( $name ) ),
								esc_attr( $tab_id ),
								esc_attr( $value )
							);
							$content .= esc_html( $radio_label );
							$content .= '</label>';
							if (
							isset( $data['content'] )
							&& isset( $data['content'][ $radio_value ] )
							) {
								$tabs_content .= sprintf(
									'<div class="sui-tab-boxed branda-tab-%s%s" data-tab-content="%s">',
									esc_attr( $tab_id ),
									$value === $radio_value ? ' active' : '',
									esc_attr( $tab_id )
								);
								$tabs_content .= $data['content'][ $radio_value ];
								$tabs_content .= '</div>';
							}
						}
						$content .= '</div>';
						if ( ! empty( $tabs_content ) ) {
							$content .= '<div class="sui-tabs-content">';
							$content .= $tabs_content;
							$content .= '</div>';
						}
						$content .= '</div>';
						break;
					case 'button':
						$content .= $this->button( $data );
						break;
					case 'callback':
						if ( isset( $data['callback'] ) && is_callable( $data['callback'] ) ) {
							$content .= call_user_func( $data['callback'], $widget_id, $value );
						} else {
							$content .= __( 'Something went wrong!', 'ub' );
						}
						break;
					default:
						break;
				}
				/**
				 * After field
				 *
				 * @since 2.0.7
				 */
				if (
					isset( $data['field'] )
					&& isset( $data['field']['after'] )
				) {
					$content .= $data['field']['after'];
				}
				if ( isset( $data['required'] ) && $data['required'] ) {
					$content .= sprintf(
						'<span class="hidden">%s</span>',
						__( 'This field can not be empty!', 'ub' )
					);
				}
				/**
				 * description
				 */
				$content .= $this->get_description( $data, 'bottom' );
				if ( $show ) {
					$content .= '</div>';
				}
				if ( $row ) {
					$content .= '</div>';
				}
				if ( isset( $data['sui-row'] ) && 'end' === $data['sui-row'] ) {
					$row      = false;
					$content .= '</div>';
				}
				/**
				 * group
				 */
				if (
					isset( $data['group'] )
					&& isset( $data['group']['end'] )
					&& true === $data['group']['end']
				) {
					$content .= '</div>';
				}
			}
			return $content;
		}

		/**
		 * check input data
		 */
		protected function check_input_data( $nonce_action, $fields = array(), $nonce_name = '_wpnonce' ) {
			$fields[] = $nonce_name;
			foreach ( $fields as $field ) {
				if ( ! isset( $_REQUEST[ $field ] ) ) {
					$this->json_error( 'missing' );
				}
			}
			if ( ! wp_verify_nonce( $_REQUEST[ $nonce_name ], $nonce_action ) ) {
				$this->json_error( 'security' );
			}
		}

		/**
		 * helper for wp_send_json_error.
		 *
		 * @since 3.0.0
		 *
		 * @param string $message Message to send.
		 */
		protected function json_error( $message = 'wrong' ) {
			if ( empty( $this->messages ) ) {
				$uba            = branda_get_uba_object();
				$this->messages = $uba->messages;
			}
			if ( isset( $this->messages[ $message ] ) ) {
				$message = $this->messages[ $message ];
			}
			wp_send_json_error( array( 'message' => $message ) );
		}

		/**
		 * helper to get name for actions, dialogs, etc.
		 *
		 * @since 3.0.0
		 */
		public function get_name( $sufix = null, $module = null ) {
			$module = empty( $module ) ? $this->module : $module;
			$name   = sprintf( 'branda-%s', $module );
			if ( empty( $sufix ) || ( ! is_string( $sufix ) && ! is_numeric( $sufix ) ) ) {
				return $name;
			}
			return sprintf( '%s-%s', $name, $sufix );
		}

		/**
		 * helper to get template name.
		 *
		 * @since 3.0.0
		 */
		public function get_template_name( $template, $area = 'admin/modules' ) {
			return sprintf( '%s/%s/%s', $area, $this->module, $template );
		}

		/**
		 * Helper to get nonce action
		 *
		 * @since 3.0.0
		 */
		public function get_nonce_action( $id = 0, $action = 'save', $third = 0 ) {
			$sufix = sprintf( '%s-%s', $action, $id );
			if ( null === $id ) {
				$sufix = $action;
			}
			if ( ! empty( $third ) ) {
				$sufix = sprintf( '%s-%s', $sufix, $third );
			}
			return $this->get_name( $sufix );
		}

		/**
		 * Helper to create nonce and get nonce value
		 *
		 * @since 3.0.0
		 */
		public function get_nonce_value( $id = 0, $action = 'save', $third = 0 ) {
			$name  = $this->get_nonce_action( $id, $action, $third );
			$nonce = wp_create_nonce( $name );
			return $nonce;
		}

		/**
		 * SUI: get dialog delete
		 *
		 * @since 3.0.0
		 */
		public function get_dialog_delete( $id, $args = array() ) {
			$defaults = array(
				'description' => __( 'Do you really want to delete selected item?', 'ub' ),
				'title'       => __( 'Are you sure?', 'ub' ),
			);
			$attr     = wp_parse_args( $args, $defaults );
			/**
			 * Allow to change attributes
			 *
			 * @since 3.0.0
			 */
			$attr    = apply_filters( 'branda_dialog_delete_attr', $attr, $this->module, $id );
			$content = sprintf(
				'<span class="sui-description">%s</span>',
				esc_html( $attr['description'] )
			);
			/**
			 * Footer
			 */
			$footer  = '';
			$args    = array(
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
				'data' => array(
					'modal-close' => '',
				),
			);
			$footer .= $this->button( $args );
			$args    = array(
				'text'  => __( 'Delete', 'ub' ),
				'sui'   => array( 'red', 'ghost' ),
				'icon'  => 'trash',
				'class' => $this->get_name( 'delete' ),
				'data'  => array(
					'nonce' => $this->get_nonce_value( $id, 'delete' ),
					'id'    => $id,
				),
			);
			$footer .= $this->button( $args );
			/**
			 * SUI Dialog
			 */
			$args = array(
				'id'      => $this->get_nonce_action( $id, 'delete' ),
				'content' => $content,
				'footer'  => array(
					'content' => $footer,
					'classes' => array( 'sui-actions-center' ),
				),
				'classes' => array( 'sui-modal-sm' ),
				'title'   => $attr['title'],
			);
			return $this->sui_dialog( $args );
		}

		/**
		 * Returns valid schema
		 *
		 * @param $url
		 *
		 * @return mixed
		 */
		protected function get_url_valid_shema( $url ) {
			$image       = $url;
			$v_image_url = parse_url( $url );
			/**
			 * Allow http sites to load https favicons
			 */
			if ( isset( $v_image_url['scheme'] ) && 'https' === $v_image_url['scheme'] ) {
				return $image;
			}
			if ( is_ssl() ) {
				$image = str_replace( 'http', 'https', $image );
			}
			return $image;
		}

		/**
		 * Disable button "Save Changes".
		 *
		 * @since 3.0.0
		 */
		public function disable_save_changes( $status, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $status;
			}
			return false;
		}

		protected function sui_row_md( $args ) {
			$content = '<div class="sui-row">';
			foreach ( $args as $data ) {
				$content .= sprintf(
					'<div class="sui-col-md-%d">%s</div>',
					$data['columns'],
					$data['value']
				);
			}
			$content .= '</div>';
			return $content;
		}

		protected function sui_accordion_indicator() {
			$content  = '<div class="sui-accordion-col-auto">';
			$content .= sprintf(
				'<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="%s"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>',
				esc_attr__( 'Open item', 'ub' )
			);
			$content .= '</div>';
			return $content;
		}

		/**
		 * check to load logos & load it
		 *
		 * @since 3.0.0
		 */
		private function check_load_social_logos_css() {
			foreach ( $this->options as $section => $data ) {
				if ( ! isset( $data['fields'] ) ) {
					continue;
				}
				$keys = array_keys( $data['fields'] );
				foreach ( $keys as $key ) {
					if ( ! preg_match( '/^social_media_/', $key ) ) {
						continue;
					}
					$this->load_social_logos_css();
					return;
				}
			}
		}

		/**
		 * Enqueue current module group assets.
		 *
		 * On page load, enqueue styles and scripts required for the
		 * current group of modules.
		 *
		 * @since 3.0.0
		 */
		public function enqueue_admin_assets() {
			$module = $this->get_module_by_module( $this->module );
			if ( ! isset( $module['group'] ) ) {
				return;
			}
			$group = $this->uba->get_current_group();
			if ( $group !== $module['group'] ) {
				return;
			}
			$this->enqueue_module_admin_assets( $this->module );
		}

		/**
		 * Enqueue admin module assets.
		 *
		 * @param string|null $module Module key.
		 *
		 * @since 2.0.0
		 */
		private function enqueue_module_admin_assets( $module = null ) {
			// Load common social assets.
			$this->check_load_social_logos_css();
			// Get a handler name for the module.
			$handler = $this->get_name( null, $module );
			/**
			 * Automagically enqueue module admin js.
			 */
			$filename = $this->get_admin_module_assets( 'js', $module );
			$file     = branda_files_dir( $filename );
			// Check if file exists.
			if ( is_file( $file ) ) {
				$file = branda_files_url( $filename );
				// Register script.
				wp_register_script( $handler, $file, array( 'jquery' ), $this->build, true );
				// Localize the script.
				if ( ! empty( $this->messages ) ) {
					$localize = array(
						'messages' => $this->messages,
					);
					wp_localize_script( $handler, __CLASS__, $localize );
				}
				$localize = apply_filters( 'branda_localize_script', array(), $module );
				if ( ! empty( $localize ) ) {
					$name = $this->get_name();
					$name = self::hyphen_to_underscore( $name );
					wp_localize_script( $handler, $name, $localize );
				}
				// Now enqueue the script.
				wp_enqueue_script( $handler );
			}
			/**
			 * Automagically enqueue module admin css.
			 */
			$filename = $this->get_admin_module_assets( 'css', $module );
			$file     = branda_files_dir( $filename );
			// Check if it is valid file.
			if ( is_file( $file ) ) {
				$file = branda_files_url( $filename );
				wp_enqueue_style( $handler, $file, array(), $this->build );
			}
			/**
			 * Run action to enqueue modules relted screipts.
			 *
			 * @since 3.1.0
			 */
			do_action( 'branda_admin_enqueue_module_admin_assets', $module );
		}

		/**
		 * get Social Media name.
		 *
		 * @since 3.0.0
		 */
		protected function get_social_media_name( $key ) {
			$name = sprintf(
				'social_media_%s',
				$key
			);
			return sanitize_title( $name );
		}

		protected function common_document_css( $target ) {
			$css = '';
			/**
			 * Target Width
			 */
			$value = $this->get_value( 'design', 'content_width', false );
			$units = $this->get_value( 'design', 'content_width_units', 'px' );
			$css  .= $this->css_width( $value, $units );
			/**
			 * Target Radius
			 */
			$value = $this->get_value( 'design', 'content_radius', false );
			$css  .= $this->css_radius( $value );
			/**
			 * Color
			 */
			$value = $this->get_value( 'colors', 'content_color' );
			$css  .= $this->css_color( $value );
			/**
			 * Background Color
			 */
			$value = $this->get_value( 'colors', 'content_background' );
			$css  .= $this->css_background_color( $value );
			if ( empty( $css ) ) {
				return $css;
			}
			$css = sprintf(
				'%s{%s}%s',
				$target,
				$css,
				PHP_EOL
			);
			return $css;
		}

		/**
		 * Common BODY CSS
		 *
		 * @since 3.0.0
		 */
		protected function common_body_css() {
			$css = '';
			/**
			 * Background Image
			 */
			$value = $this->get_value( 'content', 'content_background_meta' );
			if ( is_array( $value ) ) {
				$css .= sprintf(
					'background-image:url("%s");',
					$this->make_relative_url( $value[0] )
				);
				$css .= 'background-size: cover;';
			}
			/**
			 * Background Color
			 */
			$value = $this->get_value( 'colors', 'document_background' );
			$css  .= $this->css_background_color( $value );
			/**
			 * Color
			 */
			$value = $this->get_value( 'colors', 'document_color' );
			$css  .= $this->css_color( $value );
			if ( empty( $css ) ) {
				return $css;
			}
			$css = sprintf(
				'body{%s}%s',
				$css,
				PHP_EOL
			);
			return $css;
		}

		/**
		 * CSS actions colors.
		 *
		 * @since 3.0.0
		 *
		 * @param array   $section Configuration section.
		 * @param string  $key Configuration key.
		 * @param array   $selectors CSS selectors.
		 * @param boolean $echo Print or return data.
		 */
		protected function css_actions_colors( $section, $key, $selectors, $echo = true ) {
			$data = $this->get_value( $section );
			if ( empty( $data ) ) {
				return;
			}
			$css     = '';
			$names   = array( 'color', 'border', 'background' );
			$subkeys = array( '', 'visited', 'hover', 'active', 'focus' );
			if ( is_string( $selectors ) ) {
				$selectors = array( $selectors );
			}
			foreach ( $selectors as $selector ) {
				foreach ( $subkeys as $subkey ) {
					foreach ( $names as $name ) {
						$css_name = $name;
						switch ( $name ) {
							case 'border':
								$css_name = 'border-color';
								break;
							case 'background':
								$css_name = 'background-color';
								break;
							default:
								break;
						}
						$k = sprintf( '%s_%s_%s', $key, $name, $subkey );
						if ( '' === $subkey ) {
							$k = sprintf( '%s_%s', $key, $name );
						}
						if ( isset( $data[ $k ] ) ) {
							$css .= sprintf(
								'%s%s{',
								$selector,
								empty( $subkey ) ? '' : ':' . $subkey
							);
							$css .= sprintf(
								'%s:%s;',
								$css_name,
								$data[ $k ]
							);
							$css .= '}';
							$css .= PHP_EOL;
						}
					}
				}
			}
			if ( $echo ) {
				echo $css;
				return;
			}
			return $css;
		}

		/**
		 * Get default options
		 *
		 * @since 3.0.0
		 */
		protected function get_default_options() {
			$options = array();
			foreach ( $this->options as $section_key => $section_data ) {
				$options[ $section_key ] = array();
				if ( ! isset( $section_data['fields'] ) ) {
					continue;
				}
				foreach ( $section_data['fields'] as $key => $data ) {
					if ( isset( $data['default'] ) ) {
						$options[ $section_key ][ $key ] = $data['default'];
					}
				}
			}
			return $options;
		}

		/**
		 * Get default js admin file.
		 *
		 * @param string $type Asset type (css/js).
		 * @param string $module_key Module name.
		 * @param string $site Site.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		private function get_admin_module_assets( $type, $module_key = null, $site = 'admin' ) {
			$filename   = '';
			$module_key = empty( $module_key ) ? $this->module : $module_key;
			$module     = $this->get_module_by_module( $module_key );
			if ( empty( $module ) ) {
				return $filename;
			}
			$dir = '';
			if ( preg_match( '/\//', $module['key'] ) ) {
				$dir = dirname( $module['key'] );
			}
			$filename = sprintf(
				'modules/%s/assets/%s/%s/%s.%s',
				$dir,
				$type,
				$site,
				$module_key,
				$type
			);
			return $filename;
		}

		/**
		 * Get current module data
		 *
		 * @since 3.0.0
		 */
		protected function get_module_by_module( $module ) {
			$configuration = array();
			if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
				$this->uba = new Branda_Admin();
			}
			$configuration = $this->uba->get_configuration();
			foreach ( $configuration as $key => $data ) {
				if ( $data['module'] === $module ) {
					return $data;
				}
			}
			return array();
		}

		/**
		 * Search helper
		 *
		 * @since 3.0.0
		 */
		public function ajax_search_subsite() {
			$user_id      = ! empty( $_GET['user_id'] ) ? sanitize_text_field( $_GET['user_id'] ) : '';
			$nonce_action = $this->get_nonce_action_name( 'search', $user_id );
			$this->check_input_data( $nonce_action, array( 'user_id', 'q' ) );
			if ( ! function_exists( 'get_sites' ) ) {
				$this->json_error();
			}

			$q = ! empty( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : null;
			if ( empty( $q ) ) {
				$this->json_error();
			}
			$exclude = array();
			if ( 'images' === $this->module ) {
				$exclude[] = branda_get_main_site_id();
			}
			if (
				isset( $_REQUEST['extra'] )
				&& is_array( $_REQUEST['extra'] )
			) {
				foreach ( $_REQUEST['extra'] as $id ) {
					$id = intval( $id );
					if ( 0 < $id ) {
						$exclude[] = $id;
					}
				}
			}
			$args  = array(
				'search'       => $q,
				'site__not_in' => $exclude,
			);
			$sites = get_sites( $args );
			if ( empty( $sites ) ) {
				$this->json_error( 'empty' );
			}
			$result = array();
			foreach ( $sites as $site ) {
				$details  = get_blog_details( $site->blog_id );
				$result[] = array(
					'id'       => $site->blog_id,
					'title'    => $details->blogname,
					'text'     => $details->blogname,
					'subtitle' => $details->siteurl,
				);
			}
			wp_send_json_success( $result );
		}

		/**
		 * Common Migration
		 *
		 * @since 3.0.0
		 *
		 * @param array $data New data options format.
		 * @param array $value Old data options format.
		 *
		 * @return array $data New data options format.
		 */
		protected function common_upgrade_options( $data, $value ) {
			/**
			 * document
			 */
			if ( isset( $value['document'] ) ) {
				$v = $value['document'];
				if ( isset( $v['color'] ) ) {
					$data['colors']['document_color'] = $v['color'];
				}
				if ( isset( $v['background'] ) ) {
					$data['colors']['document_background'] = $v['background'];
					if ( isset( $v['background_transparency'] ) ) {
						$data['colors']['document_background'] = sprintf(
							'rgba(%s,%.2f)',
							implode( ',', $this->convert_hex_to_rbg( $v['background'] ) ),
							( 100 - $v['background_transparency'] ) / 100
						);
					}
				}
				if ( isset( $v['width'] ) ) {
					$data['design']['content_width'] = $v['width'];
				}
			}
			/**
			 * logo
			 */
			if ( isset( $value['logo'] ) ) {
				$v = $value['logo'];
				if ( isset( $v['show'] ) ) {
					$data['content']['logo_show'] = $v['show'];
				}
				if ( isset( $v['image'] ) ) {
					$data['content']['logo_image'] = $v['image'];
				}
				if ( isset( $v['image_meta'] ) ) {
					$data['content']['logo_image_meta'] = $v['image_meta'];
				}
				if ( isset( $v['width'] ) ) {
					$data['design']['logo_width'] = $v['width'];
				}
				if ( isset( $v['position'] ) ) {
					$data['design']['logo_position'] = $v['position'];
				}
				if ( isset( $v['transparency'] ) ) {
					$data['design']['logo_opacity'] = $v['transparency'];
				}
				if ( isset( $v['rounded'] ) ) {
					$data['design']['logo_rounded'] = $v['rounded'];
				}
				if ( isset( $v['url'] ) ) {
					$data['content']['logo_url'] = $v['url'];
				}
				if ( isset( $v['alt'] ) ) {
					$data['content']['logo_alt'] = $v['alt'];
				}
				if ( isset( $v['margin_bottom'] ) ) {
					$data['design']['logo_margin_bottom'] = $v['margin_bottom'];
				}
			}
			/**
			 * background
			 */
			if ( isset( $value['background'] ) ) {
				$v = $value['background'];
				if ( isset( $v['color'] ) ) {
					$data['colors']['background_color'] = $v['color'];
				}
				if ( isset( $v['mode'] ) ) {
					$data['design']['background_mode'] = $v['mode'];
				}
				if ( isset( $v['image'] ) ) {
					$data['content']['content_background'] = $v['image'];
				}
				if ( isset( $v['duration'] ) ) {
					$data['design']['background_duration'] = $v['duration'];
				}
			}
			/**
			 * social_media_settings
			 */
			if ( isset( $value['social_media_settings'] ) ) {
				$v = $value['social_media_settings'];
				if ( isset( $v['colors'] ) ) {
					$data['design']['social_media_colors'] = 'on' === $v['colors'] ? 'color' : 'monochrome';
				}
				if ( isset( $v['social_media_link_in_new_tab'] ) ) {
					$data['design']['social_media_target'] = 'on' === $v['social_media_link_in_new_tab'] ? '_blank' : '_self';
				}
				if ( isset( $v['show'] ) ) {
					$data['design']['social_media_show'] = $v['show'];
				}
				if ( isset( $v['position'] ) ) {
					$data['design']['social_media_position'] = $v['position'];
				}
				if ( isset( $v['background_color'] ) ) {
					$data['colors']['social_background_color'] = $v['background_color'];
				}
			}
			/**
			 * social_media
			 */
			if ( isset( $value['social_media'] ) ) {
				$v = $value['social_media'];
				if ( is_array( $v ) ) {
					$order = array();
					if ( isset( $value['_social_media_sortable'] ) ) {
						$order = $value['_social_media_sortable'];
					}
					if ( is_array( $order ) ) {
						foreach ( $order as $social ) {
							if ( isset( $v[ $social ] ) ) {
								if ( ! empty( $v[ $social ] ) ) {
									$social_key                     = sprintf( 'social_media_%s', $social );
									$data['content'][ $social_key ] = $v[ $social ];
								}
								unset( $v[ $social ] );
							}
						}
					}
					foreach ( $v as $social => $social_media ) {
						if ( ! empty( $v[ $social ] ) ) {
							$social_key                     = sprintf( 'social_media_%s', $social );
							$data['content'][ $social_key ] = $social_media;
						}
					}
				}
			}
			/**
			 * Commons
			 */
			$data['colors']['form_container_background'] = 'transparent';
			/**
			 * Custom CSS
			 */
			if (
				isset( $value['css'] )
				&& isset( $value['css']['css'] )
			) {
				$data['css'] = array(
					'css' => $value['css']['css'],
				);
			}
			return $data;
		}

		/**
		 * Get site name
		 *
		 * @return string
		 */
		protected function get_site_name() {
			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( empty( $sitename ) ) {
				$sitename = 'website';
			}

			return $sitename;
		}

		/**
		 * Check if it's pro version and it's paid user
		 *
		 * @return bool
		 */
		public static function is_full_pro() {
			return self::is_pro() && self::is_member();
		}

		/**
		 * Check if it's pro or free version
		 *
		 * @return bool
		 */
		public static function is_pro() {
			$is_pro = defined( 'BRANDA_BUILD_TYPE' ) && 'full' === BRANDA_BUILD_TYPE;

			return $is_pro;
		}

		/**
		 * Check if user is a paid one in WPMU DEV
		 *
		 * @return bool
		 */
		public static function is_member() {
			if ( self::membership_includes_branda() ) {
				return true;
			}
			
			if ( function_exists( 'is_wpmudev_member' ) ) {
				return is_wpmudev_member();
			}
			return false;
		}

		private static function membership_includes_branda() {
			if ( class_exists( 'WPMUDEV_Dashboard' ) && method_exists( \WPMUDEV_Dashboard::$upgrader, 'user_can_install' ) ) {
				return \WPMUDEV_Dashboard::$upgrader->user_can_install( 9135, true );
			}

			// For a little while we can return deprecated method. After a period we can return false instead;
			return self::membership_includes_branda_deprecated();
		}

		private static function membership_includes_branda_deprecated() {
			if (
				! method_exists( 'WPMUDEV_Dashboard_Api', 'get_membership_projects' )
				|| ! method_exists( 'WPMUDEV_Dashboard_Api', 'get_membership_type' )
			) {
				return false;
			}

			$branda_project_id = 9135;
			$type = WPMUDEV_Dashboard::$api->get_membership_type();
			$projects = WPMUDEV_Dashboard::$api->get_membership_projects();

			return ( 'unit' === $type && in_array( $branda_project_id, $projects, true ) )
			       || ( 'single' === $type && $branda_project_id === $projects );
		}

		/**
		 * Check is users can register.
		 *
		 * @since 3.0.0
		 */
		protected function is_user_registration_open() {
			if ( $this->is_network ) {
				$status = get_site_option( 'registration' );
				if ( 'none' === $status || 'blog' === $status ) {
					return true;
				}
			} else {
				$status = get_option( 'users_can_register' );
				if ( empty( $status ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Get user registration settings URL
		 *
		 * @since 3.0.0
		 */
		protected function get_user_registration_settings_url() {
			$url = false;
			if ( $this->is_network ) {
				$status = get_site_option( 'registration' );
				if ( 'none' === $status || 'blog' === $status ) {
					$url = network_admin_url( 'settings.php' );
				}
			} else {
				$status = get_option( 'users_can_register' );
				if ( empty( $status ) ) {
					$url  = admin_url( 'options-general.php' );
					$url .= '#users_can_register';
				}
			}
			return $url;
		}

		/**
		 * Get section "Custom CSS" array.
		 *
		 * @since 3.0.0
		 *
		 * @param string $config Extra configuration array.
		 *
		 * @return array $options Option array for custom CSS field.
		 */
		protected function get_custom_css_array( $config = '' ) {
			$description = __( 'For more advanced customization options use custom CSS.', 'ub' );
			if (
				isset( $config['extra_description'] )
				&& ! empty( $config['extra_description'] )
			) {
				$description .= ' ';
				$description .= $config['extra_description'];
			}
			$options = array(
				'title'       => __( 'Custom CSS', 'ub' ),
				'description' => $description,
				'hide-th'     => true,
				'hide-reset'  => true,
				'fields'      => array(
					'css' => array(
						'type' => 'css_editor',
					),
				),
			);
			if ( isset( $config['ace_selectors'] ) ) {
				$options['fields']['css']['ace_selectors'] = $config['ace_selectors'];
			}
			return $options;
		}

		/**
		 * Helper for no items function, show nice notice.
		 *
		 * @since 3.0.0
		 * @since 3.7.0 Added param $echo
		 */
		public function no_items( $args, $echo = true ) {
			$template = 'admin/common/no-items';
			if ( $echo ) {
				$this->uba->render( $template, $args );
			} else {
				return $this->uba->render( $template, $args, true );
			}
		}

		/**
		 * Renders a view file
		 *
		 * @param $file
		 * @param array      $params
		 * @param bool|false $return
		 * @return string
		 */
		public function render( $file, $params = array(), $return = false ) {
			$content = '';
			if ( array_key_exists( 'this', $params ) ) {
				unset( $params['this'] );
			}
			extract( $params, EXTR_OVERWRITE ); // phpcs:ignorei
			$file          = trim( $file, '/' );
			$template_file = branda_dir( 'views/' . $file ) . '.php';
			if ( file_exists( $template_file ) ) {
				ob_start();
				include $template_file;
				$content = ob_get_clean();
			} elseif ( self::is_debug() ) {
				error_log( __( 'Template file does not exists!', 'ub' ) );
				error_log( $template_file );
				if ( current_user_can( 'manage_options' ) ) {
					$message  = sprintf(
						__( 'Template file %s does not exists!', 'ub' ),
						$this->bold( $template_file )
					);
					$content .= self::sui_notice( $message, 'warning' );
				}
			}
			if ( $return ) {
				return $content;
			}
			echo $content;
		}

		/**
		 * Get HTML for Pro tag if it's needed
		 *
		 * @return string
		 */
		public static function maybe_pro_tag() {
			$html = '';
			if ( ! self::is_full_pro() ) {
				$html = '<span class="sui-tag sui-tag-pro" style="font-size: 9px; top: 10px; right: 8px;">' .
					esc_html__( 'Pro', 'ub' ) .
				'</span>';
			}

			return $html;
		}

		/**
		 * Check has module any configuration.
		 *
		 * @since 3.0.0
		 *
		 * @return boolean
		 */
		public function has_configuration() {
			$data = $this->get_value();
			if ( ! is_array( $data ) ) {
				return false;
			}
			foreach ( $data as $key => $value ) {
				if ( preg_match( '/^(updated|plugin_version)$/', $key ) ) {
					continue;
				}
				if ( ! empty( $value ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * generate random ID
		 *
		 * @since 3.0.0
		 *
		 * @param mixed $args Value/array base to be id.
		 *
		 * @return string $id new id.
		 */
		protected function generate_id( $args ) {
			$value = '';
			if ( ! empty( $args ) ) {
				$value = serialize( $args );
			}
			$value .= time();
			$value .= rand();
			/**
			 * get algorithm
			 */
			$algoritm = apply_filters( 'branda_get_id_algoritm', 'crc32' );
			$allowed  = hash_algos();
			if ( ! in_array( $allowed, $allowed ) ) {
				$allowed = 'crc32';
			}
			$id = sprintf(
				'branda_%s_%s',
				preg_replace( '/[^\d]/', '', $this->build ),
				hash( $algoritm, $value )
			);
			return $id;
		}

		/**
		 * get description helper
		 *
		 * @since 3.0.1
		 *
		 * @param array  $data Data.
		 * @param string $position Position for description.
		 *
		 * @return string $content Wraped description.
		 */
		private function get_description( $data, $position ) {
			$content = '';
			if ( ! isset( $data['description'] ) ) {
				return $content;
			}
			$description          = $data['description'];
			$description_position = 'top';
			if ( is_array( $data['description'] ) ) {
				if ( isset( $data['description']['content'] ) ) {
					$description = $data['description']['content'];
				} else {
					return $content;
				}
				if ( isset( $data['description']['position'] ) ) {
					$description_position = $data['description']['position'];
				}
			}
			if ( $position !== $description_position ) {
				return $content;
			}
			$content .= sprintf(
				'<span class="sui-description">%s</span>',
				$description
			);
			return $content;
		}

		/**
		 * Get User registration is not allowed notice
		 *
		 * @since 3.0.1
		 */
		protected function get_users_can_register_notice() {
			$url = admin_url( 'options-general.php' );
			if ( $this->is_network ) {
				$url  = network_admin_url( 'settings.php' );
				$url .= '#users_can_register';
			}
			$notice = array(
				'type'  => 'description',
				'value' => self::sui_notice(
					sprintf(
						__( 'User registration has been disabled. Click <a href="%s">here</a> to enable the user registration for your site.', 'ub' ),
						$url
					)
				),
			);
			return $notice;
		}

		/**
		 * Get module name.
		 *
		 * Public read-only access to $this->module variable.
		 *
		 * @since 3.1.0
		 *
		 * @return string $this->module Internal module name.
		 */
		public function get_module_name() {
			return $this->module;
		}

		/**
		 * Set Roles, to avoid double get.
		 *
		 * @since 3.1.0
		 */
		protected function set_roles( $sort = true, $add_super = true ) {
			if ( ! empty( $this->roles ) ) {
				return;
			}
			$roles = wp_roles()->get_names();
			if ( $add_super && $this->is_network ) {
				$roles = array_merge( array( 'super' => __( 'Network Administrator', 'ub' ) ), $roles );
			}
			if ( $sort ) {
				asort( $roles );
			}
			$this->roles = $roles;
		}

		/**
		 * Maybe crop an image and save it as new version
		 *
		 * @since 3.2.0
		 */
		protected function maybe_crop( $image, $id ) {
			$url = $image['meta'][0];
			/**
			 * no attachment ID
			 */
			if ( ! isset( $image['value'] ) || empty( $image['value'] ) ) {
				return $url;
			}
			$crop = $this->get_value( 'design', 'background_crop', 'auto' );
			/**
			 * No crop
			 */
			if ( empty( $crop ) || 'auto' === $crop ) {
				return $url;
			}
			/**
			 * no image
			 */
			$attachment_id = $image['value'];
			$attachment    = wp_get_attachment_metadata( $attachment_id );
			if ( false === $attachment || empty( $attachment ) ) {
				return $url;
			}

			switch ( $crop ) {
				case 'width':
					$value = $this->get_value( 'design', 'background_crop_width_p' );
					$height = $attachment['height'];
					$width = intval( $value * $height / 100 );

					if (
						( $image['meta'][1] === $width && $image['meta'][2] === $height ) ||
						$width > $attachment['width']
					) {
						return $url;
					}
					return $this->save_new_image( $url, $id, $attachment_id, $image, $width, $height );
					break;
				case 'height':
					$value = $this->get_value( 'design', 'background_crop_height_p' );
					$width = $attachment['width'];
					$height = intval( $value * $width / 100 );
					if (
						( $image['meta'][1] === $width && $image['meta'][2] === $height ) ||
						$height > $attachment['height']
					) {
						return $url;
					}
					return $this->save_new_image( $url, $id, $attachment_id, $image, $width, $height );
					break;
				case 'fixed':
					$width = intval( $this->get_value( 'design', 'background_crop_width' ) );
					$height = intval( $this->get_value( 'design', 'background_crop_height' ) );

					if (
						( intval( $image['meta'][1] ) === $width && intval( $image['meta'][2] ) === $height ) ||
						$width > $attachment['width'] || $height > $attachment['height']
					) {
						return $url;
					}
					return $this->save_new_image( $url, $id, $attachment_id, $image, $width, $height );
					break;
				default:
					return $image['meta'][0];
			}
			return $image['meta'][0];
		}

		private function save_new_image( $url, $id, $attachment_id, $image, $width, $height ) {
			$file   = get_attached_file( $attachment_id );
			$editor = wp_get_image_editor( $file );
			if ( is_wp_error( $editor ) ) {
				return $url;
			}
			$editor->resize( $width, $height, true );
			$file          = preg_replace( '/-' . $image['meta'][1] . 'x' . $image['meta'][2] . '/', '', $image['meta'][0] );
			$file          = preg_replace( '/(\.[jpegnif]+)$/', '-' . $width . 'x' . $height . '$1', $file );
			$wp_upload_dir = wp_upload_dir();
			$file          = $wp_upload_dir['path'] . basename( $file );
			$attachment    = $editor->save( $file );
			$value         = $this->get_value( 'content', 'content_background' );
			$attachment_id = $this->insert_attachment( $file, $attachment['mime-type'] );
			$value[ $id ]  = array(
				'value' => $attachment_id,
				'meta'  => array(
					wp_get_attachment_image_url( $attachment_id, array( $width, $height ) ),
					$width,
					$height,
					null,
				),
			);
			$this->set_value( 'content', 'content_background', $value );
			return $value[ $id ]['meta'][0];
		}

		/**
		 * Insert attachment into the WordPress Media Library.
		 *
		 * @param  string $file_path The path to the media file.
		 * @param  string $mime_type The MIME type of the media file.
		 *
		 * @since 3.2.0
		 */
		private function insert_attachment( $file_path, $mime_type ) {
			// Get the path to the uploads directory.
			$wp_upload_dir = wp_upload_dir();
			// Prepare an array of post data for the attachment.
			$attachment_data = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_path ),
				'post_mime_type' => $mime_type,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			$attachment_id   = wp_insert_attachment( $attachment_data, $file_path );
			// Gives us access to the wp_generate_attachment_metadata() function.
			require_once ABSPATH . 'wp-admin/includes/image.php';
			// Generate metadata for the attachment and update the database record.
			$attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
			wp_update_attachment_metadata( $attachment_id, $attach_data );
			return $attachment_id;
		}

		/**
		 * Replace hyphens to underscores in the string
		 *
		 * @param string $str String
		 * @return string
		 */
		public static function hyphen_to_underscore( $str ) {
			return str_replace( '-', '_', $str );
		}

		/**
		 * Check has module configuration to allow reset module button.
		 *
		 * @since 3.1.0
		 *
		 * @param boolean $show show or not, this is filter
		 * @param string  $module Module name.
		 */
		public function show_reset_module_button( $show, $module ) {
			if ( $module !== $this->module ) {
				return $show;
			}
			return $this->has_configuration();
		}

		protected function sanitize_request_payload( $payload, $sanitize_methods = array() ) {
			$sanitized_payload = array();
			foreach ( $payload as $key => $value ) {
				if ( isset( $sanitize_methods[ $key ] ) ) {

					if ( ! $sanitize_methods[ $key ] || ! is_callable( $sanitize_methods[ $key ] ) ) {
						continue;
					}

					$sanitized_payload[ $key ] = call_user_func( $sanitize_methods[ $key ], $value );

				} elseif ( is_array( $value ) ) {

					$sanitized_payload[ $key ] = $this->sanitize_request_payload(
						$value,
						$sanitize_methods
					);

				} else {

					$sanitized_payload[ $key ] = sanitize_text_field( $value );

				}
			}

			return $sanitized_payload;
		}

		/**
		 * Check if debug mode is enabled
		 *
		 * @return bool
		 */
		public static function is_debug() {
			if ( is_null( self::$debug ) ) {
				/**
				 * Debug only when WP_DEBUG && WPMUDEV_BETATEST
				 */
				$debug       = defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WPMUDEV_BETATEST' ) && WPMUDEV_BETATEST;
				self::$debug = apply_filters( 'ultimatebranding_debug', $debug );
			}

			return self::$debug;
		}

		/**
		 * Checks if filename has an svg extension.
		 * 
		 * @param mixed $filename
		 * @return bool
		 */
		public static function has_svg_ext( ?string $filename = "" ): bool {
			return pathinfo( $filename, PATHINFO_EXTENSION ) === 'svg';
			//return wp_check_filetype( $filename )['ext'] === 'svg';
		}

		/**
		 * Checks if the given file is a valid SVG file. It doesn't check if it is a secure file. Use 3rd party plugins to allow secure SVG uploads.
		 * 
		 * @param mixed $filepath
		 * @return bool
		 */
		public static function is_valid_svg( ?string $filepath = null ): bool {
			if ( empty( $filepath ) ) {
				return false;
			}

			$filename  = basename( $filepath );
			$file_info = wp_check_filetype_and_ext( $filepath, $filename );

			return ! empty( $file_info['type'] ) &&
				( 'image/svg+xml' === $file_info['type'] || 'image/svg' === $file_info['type'] ) &&
				in_array( 'svg', array_keys( get_allowed_mime_types() ), true );
		}
	}
}
