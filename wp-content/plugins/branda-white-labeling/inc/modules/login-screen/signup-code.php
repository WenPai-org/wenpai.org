<?php
/**
 * Branda Signup Codes class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Signup_Codes' ) ) {

	class Branda_Signup_Codes extends Branda_Helper {

		protected $option_name = 'ub_signup_codes';

		/**
		 * Single item defaults
		 *
		 * @since 3.1.0
		 */
		private $item_defaults = array(
			'id'   => 'new',
			'case' => 'sensitive',
			'code' => '',
			'role' => 'wp-default',
		);

		/**
		 * set registered new user role
		 *
		 * @since 3.1.0
		 */
		private $role = '';

		public function __construct() {
			parent::__construct();
			$this->module = 'signup-code';
			add_filter( 'ultimatebranding_settings_signup_code', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_signup_code_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_signup_code_process', array( $this, 'update_items' ), 1 );
			add_filter( 'ultimatebranding_settings_signup_code_preserve', array( $this, 'add_preserve_fields' ) );
			/**
			 * User Create Code
			 */
			add_action( 'register_form', array( $this, 'add_user_code' ) );
			add_action( 'signup_extra_fields', array( $this, 'add_user_code' ) );
			add_filter( 'registration_errors', array( $this, 'validate_user_signup_single' ) );
			add_filter( 'wpmu_validate_user_signup', array( $this, 'validate_user_signup' ) );
			add_action( 'register_new_user', array( $this, 'set_registered_new_user_role' ) );
			add_filter( 'signup_user_meta', array( $this, 'add_registered_new_user_role' ), 10, 4 );
			add_action( 'wpmu_activate_user', array( $this, 'set_activated_user_role' ), 10, 3 );
			add_action( 'wpmu_activate_blog', array( $this, 'set_activated_blog_role' ), 10, 5 );
			/**
			 * Blog Create Code
			 */
			add_action( 'signup_blogform', array( $this, 'add_blog_code' ) );
			add_filter( 'wpmu_validate_blog_signup', array( $this, 'validate_blog_signup' ) );
			/**
			 * BuddyPress integration
			 */
			add_action( 'bp_account_details_fields', array( $this, 'add_user_code' ) );
			add_action( 'bp_blog_details_fields', array( $this, 'add_blog_code' ) );
			add_action( 'bp_signup_validate', array( $this, 'add_wrong_code_error' ) );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.1.0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
		}

		/**
		 * Add settings sections to prevent delete on save.
		 *
		 * Add settings sections (virtual options not included in
		 * "set_options()" function to avoid delete during update.
		 *
		 * @since 3.1.0
		 *
		 * @return array
		 */
		public function add_preserve_fields() {
			return array(
				'user' => array(
					'items',
				),
				'blog' => array(
					'items',
				),
			);
		}

		/**
		 * Set options
		 *
		 * @since 2.3.0
		 */
		protected function set_options() {
			$options = array(
				'user' => array(
					'title'       => __( 'User Registration', 'ub' ),
					'description' => __( 'Choose whether anyone can register to your site or you want to restrict registration with a signup code only.', 'ub' ),
					'fields'      => array(
						'items'    => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list_users' ),
							'master'       => $this->get_name( 'user' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'help'     => array(
							'type'         => 'text',
							'label'        => __( 'Field Description', 'ub' ),
							'description'  => array(
								'content'  => __( 'This will appear under the input field on the signup form.', 'ub' ),
								'position' => 'bottom',
							),
							'default'      => __( 'You need to enter the code to create a user.', 'ub' ),
							'master'       => $this->get_name( 'user' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'error'    => array(
							'type'         => 'text',
							'label'        => __( 'Error Message', 'ub' ),
							'description'  => array(
								'content'  => __( 'This will appear under the input field on the signup form.', 'ub' ),
								'position' => 'bottom',
							),
							'default'      => __( '<strong>ERROR</strong>: User create code is invalid.', 'ub' ),
							'master'       => $this->get_name( 'user' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'settings' => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'off' => __( 'Anyone', 'ub' ),
								'on'  => __( 'With Signup Code', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'user' ),
						),
					),
				),
				'blog' => array(
					'title'        => __( 'Blog Registration', 'ub' ),
					'description'  => __( 'Choose if anyone can register a blog to your site or you want to restrict registration with a signup code only.', 'ub' ),
					'network-only' => true,
					'fields'       => array(
						'items'    => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list_blogs' ),
							'master'       => $this->get_name( 'blog' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'branding' => array(
							'type'         => 'text',
							'label'        => __( 'Field Label', 'ub' ),
							'description'  => array(
								'content'  => __( 'This label will appear on the signup form with the signup code field.', 'ub' ),
								'position' => 'bottom',
							),
							'default'      => __( 'Blog Create Code', 'ub' ),
							'master'       => $this->get_name( 'blog' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'help'     => array(
							'type'         => 'text',
							'label'        => __( 'Field Description', 'ub' ),
							'description'  => array(
								'content'  => __( 'This will appear under the input field on the signup form.', 'ub' ),
								'position' => 'bottom',
							),
							'default'      => __( 'You need to enter the code to create a blog.', 'ub' ),
							'master'       => $this->get_name( 'blog' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'error'    => array(
							'type'         => 'text',
							'label'        => __( 'Error Message', 'ub' ),
							'description'  => array(
								'content'  => __( 'This will appear under the input field on the signup form.', 'ub' ),
								'position' => 'bottom',
							),
							'default'      => __( 'Blog create code is invalid.', 'ub' ),
							'master'       => $this->get_name( 'blog' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'settings' => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'off' => __( 'Anyone', 'ub' ),
								'on'  => __( 'With Signup Code', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'blog' ),
						),
					),
				),
			);
			/**
			 * change settings for single site
			 */
			if ( $this->is_network ) {
				/**
				 * handle settings
				 */
				$status = get_site_option( 'registration' );
				if ( 'none' === $status || 'user' === $status ) {
					$url                       = network_admin_url( 'settings.php' );
					$notice                    = array(
						'type'  => 'description',
						'value' => Branda_Helper::sui_notice(
							sprintf(
								__( 'Blog registration has been disabled. Click <a href="%s">here</a> to enable the site registration for your network.', 'ub' ),
								$url
							)
						),
					);
					$options['blog']['fields'] = array( 'notice' => $notice );
				}
			}
			/**
			 * set users registration
			 */
			$options       = $this->set_users_can_register( $options );
			$this->options = $options;
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$data = $this->get_value();
			if ( isset( $data['settings'] ) ) {
				if ( isset( $data['settings']['user'] ) ) {
					$data['user']['settings'] = $data['settings']['user'];
					$update                   = true;
				}
				if ( isset( $data['settings']['blog'] ) ) {
					$data['blog']['settings'] = $data['settings']['blog'];
					$update                   = true;
				}
				unset( $data['settings'] );
				$this->update_value( $data );
			}
		}

		/**
		 * Set user registration is not allowed message
		 *
		 * @since 3.0.0
		 */
		private function set_users_can_register( $data ) {
			$is_open = $this->is_user_registration_open();
			if ( false === $is_open ) {
				return $data;
			}
			$notice                 = $this->get_users_can_register_notice();
			$data['user']['fields'] = array( 'notice' => $notice );
			return $data;
		}

		/**
		 * Print code field.
		 *
		 * @since 2.3.0
		 *
		 * @param string   $id ID of field.
		 * @param array    $value Configuration of field.
		 * @param WP_Error $errors WP_Error object.
		 */
		private function print_field( $id, $value, $errors ) {
			echo '<p class="ultimate-branding-password">';
			$html_id = 'ultimate_branding_' . $id;
			$name    = $this->get_name( $id );
			if ( isset( $value['branding'] ) && ! empty( $value['branding'] ) ) {
				printf(
					'<label for="%s">%s</label>',
					esc_attr( $html_id ),
					esc_html( $value['branding'] )
				);
			}
			/**
			 * error message
			 */
			if ( is_a( $errors, 'WP_Error' ) && $errmsg = $errors->get_error_message( $name ) ) {
				printf( '<p class="error">%s</p>', $errmsg );
			} elseif ( is_array( $errors ) && ! empty( $errors['buddypress'] ) ) {
				echo $errors['buddypress'];
			}
			printf(
				'<input type="text" name="%s" class="input" id="%s" autocomplete="off" />',
				esc_attr( $name ),
				esc_attr( $html_id )
			);
			if ( isset( $value['help'] ) && ! empty( $value['help'] ) ) {
				echo '<span>';
				echo esc_html( wp_strip_all_tags( $value['help'] ) );
				echo '</span>';
			}
			echo '</p>';
		}

		/**
		 * Check is User Create Code in use?
		 *
		 * @since 2.3.0
		 */
		private function check_user_code() {
			if ( is_admin() ) {
				return false;
			}
			$value = $this->get_value( 'user', 'settings', 'off' );
			if ( 'on' !== $value ) {
				return;
			}
			if ( $this->is_network ) {
				$status = get_site_option( 'registration' );
				if ( 'blog' === $status ) {
					return false;
				}
				$show = $this->get_value( 'user', 'settings', 'off' );
				if ( 'on' === $show ) {
					$codes = $this->get_value( 'user', 'items' );
					if ( ! empty( $codes ) ) {
						return true;
					}
					$code = $this->get_value( 'user', 'code' );
					if ( empty( $code ) ) {
						return false;
					}
					return true;
				}
			} else {
				$codes = $this->get_value( 'user', 'items' );
				if ( ! empty( $codes ) ) {
					return true;
				}
				$code = $this->get_value( 'user', 'code' );
				if ( ! empty( $code ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Add User Create Code to login form.
		 *
		 * @since 2.3.0
		 *
		 * @param WP_Error $errors WP_Error object.
		 */
		public function add_user_code( $errors ) {
			// compatibility with buddypress: show error
			global $branda_bp_errors;
			if ( ! empty( $branda_bp_errors['branda-signup-code-user_code'] ) && function_exists( 'nouveau_error_template' ) ) {
				$errors           = array( 'buddypress' => nouveau_error_template( wp_strip_all_tags( $branda_bp_errors['branda-signup-code-user_code'] ), 'bp-feedback error' ) );
				$branda_bp_errors = null;
			}

			$show = $this->check_user_code();
			if ( ! $show ) {
				return;
			}
			/**
			 * get configuration
			 */
			$value = $this->get_value( 'user' );
			if ( ! isset( $value['branding'] ) || empty( $value['branding'] ) ) {
				$value['branding'] = __( 'User Create Code', 'ub' );
			}
			/**
			 * print
			 */
			$this->print_field( 'user_code', $value, $errors );
		}

		/**
		 * Buddypress: Don't create user if signup code is invalid
		 */
		public function add_wrong_code_error() {
			global $branda_bp_errors;
			if ( ! empty( $branda_bp_errors['branda-signup-code-user_code'] ) && function_exists( 'buddypress' ) ) {
				$bp = buddypress();
				$bp->signup->errors['branda-signup-code-user_code'] = $branda_bp_errors['branda-signup-code-user_code'];
			}
		}

		/**
		 * Validate User Create Code
		 *
		 * @since 2.3.0
		 *
		 * @param array $results Result of create accound form.
		 */
		public function validate_user_signup( $results ) {
			/**
			 * validate user signup
			 * check user code
			 */
			$show = $this->check_user_code();
			if ( $show ) {
				$name         = $this->get_name( 'user_code' );
				$code_entered = ! empty( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';
				/**
				 * Code Saved
				 */
				$codes = $this->get_value( 'user', 'items' );

				$code_saved = $this->get_value( 'user', 'code', array() );
				if ( ! empty( $code_saved ) ) {
					$codes['old'] = array(
						'code' => $code_saved,
						'case' => $this->get_value( 'user', 'case', 'sensitive' ),
						'role' => '',
					);
				}
				$match = false;
				foreach ( $codes as $data ) {
					if ( $match ) {
						continue;
					}
					if ( 'insensitive' === $data['case'] ) {
						$code_saved    = strtolower( $data['code'] );
						$code_to_check = strtolower( $code_entered );
						$match         = $code_saved === $code_to_check;
					} else {
						$match = $data['code'] === $code_entered;
					}
					if ( $match ) {
						$this->role = $data['role'];
					}
				}
				if ( ! $match ) {
					if ( function_exists( 'buddypress' ) ) {
						global $branda_bp_errors;
						$branda_bp_errors[ $name ] = $this->get_value( 'user', 'error' );
					}
					$results['errors']->add( $name, $this->get_value( 'user', 'error' ) );
				}
			}
			/**
			 * return
			 */
			return $results;
		}

		/**
		 * Validate User Create Code on single site
		 *
		 * @since 2.3.0
		 *
		 * @param WP_Error $errors WP_Error object.
		 */
		public function validate_user_signup_single( $errors ) {
			$results = array(
				'errors' => $errors,
			);
			$results = $this->validate_user_signup( $results );
			return $results['errors'];
		}

		/**
		 * Validate Blog Code
		 *
		 * @since 2.3.0
		 *
		 * @param array $results Result of create accound form.
		 */
		public function validate_blog_signup( $results ) {
			$results = $this->validate_user_signup( $results );
			/**
			 * check blog create code
			 */
			$show = $this->check_blog_code();
			if ( $show ) {
				$name         = $this->get_name( 'blog_code' );
				$saved_codes  = $this->get_value( 'blog', 'items' );
				$code_entered = ! empty( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';

				foreach ( $saved_codes as $saved_data ) {
					$saved_code = isset( $saved_data['code'] ) ? $saved_data['code'] : null;
					if ( is_null( $saved_code ) ) {
						continue;
					}
					/**
					 * Case sensitive/insensitive
					 */
					if ( ! empty( $saved_data['case'] ) && 'insensitive' === $saved_data['case'] ) {
						$equal = strtolower( $saved_code ) === strtolower( $code_entered );
					} else {
						$equal = $saved_code === $code_entered;
					}
					if ( $equal ) {
						break;
					}
				}
				/**
				 * Compare!
				 */
				if ( empty( $equal ) ) {
					$results['errors']->add( $name, $this->get_value( 'blog', 'error' ) );
				}
			}
			/**
			 * return
			 */
			return $results;
		}

		/**
		 * Check is Clog Create Code in use?
		 *
		 * @since 2.3.0
		 */
		private function check_blog_code() {
			if ( is_admin() ) {
				return false;
			}
			$show = $this->get_value( 'blog', 'settings', 'off' );
			if ( 'on' === $show ) {
				$code = $this->get_value( 'blog', 'items' );
				if ( empty( $code ) ) {
					return false;
				}
				return true;
			}
			return false;
		}

		/**
		 * Adds an additional field for Blog description,
		 * on signup form for WordPress or Buddypress
		 *
		 * @param type $errors
		 */
		public function add_blog_code( $errors ) {
			$show = $this->check_user_code();
			if ( $show ) {
				$name = $this->get_name( 'user_code' );
				if ( isset( $_POST[ $name ] ) ) {
					printf(
						'<input type="hidden" name="%s" value="%s" />',
						esc_attr( $name ),
						esc_attr( $_POST[ $name ] )
					);
				} else {
					$action = ! empty( $_GET[ 'action' ] ) ? sanitize_text_field( $_GET[ 'action' ] ) : '';
					if (
						! class_exists( 'ProSites_View_Front_Registration' )
						|| 'new_blog' !== $action
					) {
						return $errors;
					}
				}
			}
			$show = $this->check_blog_code();
			if ( ! $show ) {
				return;
			}
			/**
			 * get configuration
			 */
			$value = $this->get_value( 'blog' );
			if ( ! isset( $value['branding'] ) || empty( $value['branding'] ) ) {
				$value['branding'] = __( 'Blog Create Code', 'ub' );
			}
			/**
			 * print
			 */
			$this->print_field( 'blog_code', $value, $errors );
		}

		/**
		 * Get Signup Codes for users
		 *
		 * @since 3.1.0
		 */
		public function get_list_users() {
			return $this->get_list( 'user' );
		}

		/**
		 * Get Signup Codes for Sites
		 *
		 * @since 3.1.0
		 */
		public function get_list_blogs() {
			return $this->get_list( 'blog' );
		}

		/**
		 * Get list of Signup Codes
		 *
		 * @since 3.1.0
		 *
		 * @param string $type Type of items, allowed 'user' or 'blog'.
		 */
		public function get_list( $type ) {
			$this->set_roles();
			$roles  = array(
				'-' => __( 'Choose a user role', 'ub' ),
			);
			$roles += $this->roles;
			$data   = $this->get_value( $type, 'items' );
			if ( ! is_array( $data ) || empty( $data ) ) {
				$data = array(
					'new' => array(
						'type' => $type,
						'code' => $this->get_value( $type, 'code' ),
						'role' => '-',
						'case' => $this->get_value( $type, 'case' ),
					),
				);
			}
			foreach ( $data as $key => $one ) {
				$data[ $key ]['id'] = $key;
				if ( ! isset( $one['case'] ) ) {
					$data[ $key ]['case'] = 'insensitive';
				}
			}
			$args     = array(
				'type'            => $type,
				'row'             => $this->get_template_name( 'row-' . $type ),
				'items'           => $data,
				'container_class' => $this->get_name( $type . '-container' ),
				'roles'           => $roles,
			);
			$template = $this->get_template_name( 'items' );
			$content  = $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * SUI: button add
		 *
		 * @since 3.0.8
		 *
		 * @return string Button HTML.
		 */
		public function button_add() {
			$args = array(
				'data' => array(
					'modal-open' => $this->get_name( 'edit' ),
					'nonce'      => $this->get_nonce_value( 'new' ),
				),
				'icon' => 'plus',
				'text' => _x( 'Add Signup Code', 'button', 'ub' ),
				'sui'  => 'blue',
			);
			return $this->button( $args );
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.1.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			/**
			 * Dialog ID
			 */
			$dialog_id = $this->get_name( 'edit' );
			/**
			 * Custom Item Row
			 */
			$roles    = array(
				'-' => __( 'Choose a user role', 'ub' ),
			);
			$roles   += $this->roles;
			$template = $this->get_template_name( 'tmpl/row-user' );
			$args     = array(
				'name'     => $this->get_name( 'row' ),
				'template' => $this->get_template_name( 'row-user' ),
				'roles'    => $roles,
			);
			$content .= $this->render( $template, $args, true );
			if ( $this->is_network ) {
				$template = $this->get_template_name( 'tmpl/row-blog' );
				$args     = array(
					'name'     => $this->get_name( 'row' ),
					'template' => $this->get_template_name( 'row-blog' ),
					'roles'    => $roles,
				);
				$content .= $this->render( $template, $args, true );
			}
			return $content;
		}

		/**
		 * Update Items
		 *
		 * @since 3.1.0
		 */
		public function update_items( $status ) {
			if ( ! isset( $_POST['simple_options'] ) ) {
				return $status;
			}
			$this->set_roles();
			$types = array( 'user', 'blog' );
			foreach ( $types as $type ) {
				$items = array();
				if (
					isset( $_POST['simple_options'][ $type ] )
					&& is_array( $_POST['simple_options'][ $type ] )
				) {
					foreach ( $_POST['simple_options'][ $type ] as $key => $data ) {
						if ( preg_match( '/^(new|branda)/', $key ) ) {
							unset( $_POST['simple_options'][ $type ][ $key ] );
							if ( preg_match( '/^new/', $key ) ) {
								$key = $this->generate_id( $data );
							}
						} else {
							continue;
						}
						if (
							! isset( $data['code'] )
							|| empty( $data['code'] )
						) {
							continue;
						}
						/**
						 * Sanitize role
						 */
						$role = '-';
						if (
							isset( $data['role'] )
							&& array_key_exists( $data['role'], $this->roles )
						) {
							$role = $data['role'];
						}
						/**
						 * Sanitize case match
						 */
						$case = 'insensitive';
						if (
							isset( $data['case'] )
							&& 'on' === $data['case']
						) {
							$case = 'sensitive';
						}
						$items[ $key ] = array(
							'code' => sanitize_text_field( $data['code'] ),
							'role' => $role,
							'case' => $case,
						);
					}
				}
				$this->set_value( $type, 'items', $items );
			}
			return $status;
		}

		/**
		 * Set newly register user proper role
		 */
		public function set_registered_new_user_role( $user_id ) {
			$this->set_roles();
			if ( array_key_exists( $this->role, $this->roles ) ) {
				$user = new WP_User( $user_id );
				$user->set_role( $this->role );
			}
		}

		/**
		 * Save role infor on Signup meta
		 *
		 * @since 3.1.0
		 */
		public function add_registered_new_user_role( $meta, $user, $user_email, $key ) {
			$this->set_roles();
			if ( array_key_exists( $this->role, $this->roles ) ) {
				$meta[ $this->option_name ] = $this->role;
			}
			return $meta;
		}

		/**
		 * Add user role to new registered user inside MU
		 *
		 * @since 3.1.0
		 */
		public function set_activated_user_role( $user_id, $password, $meta ) {
			$blog_id = get_current_blog_id();
			$this->add_user_to_blog( $blog_id, $user_id, $meta );
		}

		/**
		 * Add user role to new registered blog
		 *
		 * @since 3.1.0
		 */
		public function set_activated_blog_role( $blog_id, $user_id, $password, $title, $meta ) {
			$this->add_user_to_blog( $blog_id, $user_id, $meta );
		}

		/**
		 * Add user role to user
		 *
		 * @since 3.1.0
		 */
		private function add_user_to_blog( $blog_id, $user_id, $meta ) {
			if ( ! isset( $meta[ $this->option_name ] ) ) {
				return;
			}
			$this->set_roles();
			$role = $meta[ $this->option_name ];
			if ( ! array_key_exists( $role, $this->roles ) ) {
				return;
			}
			add_user_to_blog( $blog_id, $user_id, $role );
		}
	}
}
new Branda_Signup_Codes();
