<?php
/**
 * Branda Subsites class.
 *
 * Class that handle permissions settings functionality.
 *
 * @since      3.2.0
 *
 * @package Branda
 * @subpackage Settings
 */
if ( ! class_exists( 'Branda_Permissions' ) ) {

	/**
	 * Class Branda_Permissions.
	 */
	class Branda_Permissions extends Branda_Helper {

		/**
		 * Permissions instance
		 *
		 * @since 3.2
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Module option name.
		 *
		 * @since 3.2.0
		 *
		 * @var string
		 */
		protected $option_name       = 'ub_permissions';
		protected $option_name_users = 'ub_permissions_users';

		/**
		 * Return the Branda_Permissions instance
		 *
		 * @since 3.2
		 * @return Branda_Permissions
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Branda_Permissions constructor.
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			// Set module name.
			$this->module = 'permissions';
			parent::__construct();
			/**
			 * User roles
			 */
			$this->set_roles();
			// Handle module settings.
			add_filter( 'ultimatebranding_settings_permissions', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_permissions_process', array( $this, 'update' ) );
			// Add custom content title.
			add_filter( 'branda_before_module_form', array( $this, 'add_title_before_form' ), 10, 2 );
			// Change bottom save button params.
			add_filter( 'branda_after_form_save_button_args', array( $this, 'change_bottom_save_button' ), 10, 2 );
			add_filter( 'branda_module_check_for_subsite', array( $this, 'check_module' ), 10, 3 );
			add_filter( 'branda_group_check_for_subsite', array( $this, 'check_group' ), 10, 3 );
			add_filter( 'branda_force_local_option', array( $this, 'check_option' ), 10, 2 );
			/**
			 * branda subsites options name
			 */
			add_filter( 'branda_subsites_options_names', array( $this, 'get_options_names' ) );
			/**
			 * branda subsites allowed modules
			 */
			add_filter( 'branda_subsites_allowed_modules', array( $this, 'get_allowed_modules' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.2
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_branda_usersearch', array( $this, 'usersearch' ) );
			add_action( 'wp_ajax_branda_permissions_add_user', array( $this, 'ajax_save_user' ) );
			add_action( 'wp_ajax_branda_permissions_delete_user', array( $this, 'ajax_delete_user' ) );
			add_action( 'wp_ajax_branda_notice_permissions_notice_save', array( $this, 'ajax_save_subsite_messsage_status' ) );
			add_filter( 'branda_subsites_dashboard_message', array( $this, 'add_dashboard_message_data' ) );
		}

		/**
		 * Build form with options.
		 *
		 * Set settings form fields for the module.
		 *
		 * @since 3.2.0
		 */
		protected function set_options() {
			global $branda_network;

			$options = array(
				'permissions' => array(
					'title'       => __( 'User Permissions', 'ub' ),
					'description' => __( 'Configure which users and user roles can access and configure Branda\'s settings.', 'ub' ),
					'fields'      => array(
						'user_roles' => array(
							'label'           => __( 'User Roles', 'ub' ),
							'description'     => __( 'Choose which user roles can have access and configure Branda.', 'ub' ),
							'type'            => 'checkboxes',
							'columns'         => 2,
							'disabled'        => $branda_network ? array( 'super' ) : array( 'administrator' ),
							'always_selected' => $branda_network ? array( 'super' ) : array( 'administrator' ),
							'options'         => $this->roles,
						),
						'users'      => array(
							'type'        => 'callback',
							'callback'    => array( $this, 'get_users' ),
							'label'       => __( 'Custom Users', 'ub' ),
							'description' => __( 'In addition to the roles above, select specific users who are allowed to access and configure Branda.', 'ub' ),
						),
					),
				),
			);

			if ( $branda_network ) {
				$options['subsites'] = array(
					'title'       => __( 'Subsite Controls', 'ub' ),
					'description' => __( 'By default, subsites will inherit your network settings. Choose which modules you want to allow subsite admins to override.', 'ub' ),
					'fields'      => array(
						'items'  => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list' ),
							'master'       => $this->get_name( 'status' ),
							'master-value' => 'modules',
							'display'      => 'sui-tab-content',
						),
						'none'   => array(
							'type'         => 'description',
							'value'        => Branda_Helper::sui_notice( esc_html__( 'Subsite admins can\'t override any module settings and will always inherit your network settings.', 'ub' ), 'info' ),
							'master'       => $this->get_name( 'status' ),
							'master-value' => 'disabled',
							'display'      => 'sui-tab-content',
							'wrap'         => false,
						),
						'all'    => array(
							'type'         => 'description',
							'value'        => Branda_Helper::sui_notice( esc_html__( 'Subsite admins can override any default module settings set in your network settings.', 'ub' ), 'info' ),
							'master'       => $this->get_name( 'status' ),
							'master-value' => 'enabled',
							'display'      => 'sui-tab-content',
							'wrap'         => false,
						),
						'status' => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'disabled' => __( 'None', 'ub' ),
								'enabled'  => __( 'All', 'ub' ),
								'modules'  => __( 'Custom', 'ub' ),
							),
							'default'     => 'disabled',
							'slave-class' => $this->get_name( 'status' ),
							'description' => array(
								'content'  => '',
								'position' => 'bottom',
							),
						),
					),
				);
			}

			$this->options = $options;
		}

		/**
		 * List of users.
		 *
		 * @since 3.2
		 */
		public function get_users() {
			$template = $this->get_template_name( 'list' );
			$nonce    = $this->get_nonce_value( 'new' );
			$items    = $this->get_allowed_users();
			if ( is_array( $items ) ) {
				foreach ( $items as $key => $data ) {
					if ( ! is_array( $data ) ) {
						continue;
					}
					if ( ! isset( $data['id'] ) ) {
						continue;
					}
				}
			}
			$args = array(
				'button'      => $this->button(
					array(
						'data' => array(
							'modal-open' => $this->get_name( 'add-user' ),
							'nonce'      => $nonce,
						),
						'icon' => 'plus',
						'text' => __( 'Add User', 'ub' ),
						'sui'  => 'magenta',
					)
				),
				'order'       => $this->get_value( 'order' ),
				'template'    => $this->get_template_name( 'row' ),
				'items'       => $items,
				'button_plus' => $this->button(
					array(
						'data' => array(
							'modal-open' => $this->get_name( 'add-user' ),
							'nonce'      => $nonce,
						),
						'icon' => 'plus',
						'text' => __( 'Add User', 'ub' ),
						'sui'  => 'ghost',
					)
				),
			);
			return $this->render( $template, $args, true );
		}

		/**
		 * Add title before form.
		 *
		 * @param string $content Current content.
		 * @param array  $module  Current module.
		 *
		 * @since 3.2.0
		 *
		 * @return string
		 */
		public function add_title_before_form( $content, $module ) {
			if ( $this->module === $module['module'] ) {
				$template = $this->get_template_name( 'header' );
				$content .= $this->render( $template, array(), true );
			}
			return $content;
		}

		/**
		 * AJAX: add user
		 *
		 * @since 3.2
		 */
		public function ajax_save_user() {
			$id         = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
			$user_login = ! empty( $_POST['login'] ) ? trim( filter_input( INPUT_POST, 'login' ) ) : '';
			$item       = array();
			if ( ! is_null( $id ) ) {
				$this->check_input_data( 'add_user', array( 'id' ) );
			} elseif ( ! is_null( $user_login ) ) {
				$this->check_input_data( 'add_user', array( 'login' ) );
				$user = get_user_by( 'login', $user_login );
				// if it doesn't find user by login - try to find it by email
				if ( ! $user && is_email( $user_login ) ) {
					$user = get_user_by( 'email', $user_login );
				}
				if ( $user ) {
					$id                   = $user->ID;
					$item['email']        = $user->user_email;
					$item['display_name'] = $user->display_name;
					$item['avatar']       = get_avatar_url( $user->ID );
				} else {
					$this->json_error( 'wrong_userlogin' );
				}
			} else {
				$this->json_error();
			}
			$items = $this->get_allowed_user_ids();
			// Add new user ID
			$items[] = $id;
			$items   = array_unique( $items );
			$this->update_allowed_users( $items );
			$item['id']      = $id;
			$item['nonce']   = $this->get_nonce_value( $id, 'remove' );
			$item['message'] = __( 'User Permissions updated successfully.', 'ub' );
			/**
			 * Send it back
			 */
			wp_send_json_success( $item );
		}

		/**
		 * AJAX: delete user
		 *
		 * @since 3.2
		 */
		public function ajax_delete_user() {
			$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
			if ( get_current_user_id() === $id ) {
				wp_send_json_error( array( 'message' => __( 'You can\'t remove your access yourself!', 'ub' ) ) );
			}
			$nonce_action = $this->get_nonce_action( $id, 'remove' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$users = $this->get_allowed_user_ids();
			$key   = array_search( $id, $users, true );
			if ( false !== $key ) {
				unset( $users[ $key ] );
				$this->update_allowed_users( $users );
				/**
				 * Send Message
				 */
				wp_send_json_success(
					array(
						'id'      => $id,
						'message' => __( 'User Permissions updated successfully.', 'ub' ),
					)
				);
			}
			wp_send_json_error( array( 'message' => __( 'Selected user doesn\'t exist!', 'ub' ) ) );
		}

		/**
		 * AJAX: Get users list based on 'q' filter.
		 *
		 * @since  3.2
		 */
		public function usersearch() {
			if ( is_multisite() && wp_is_large_network( 'users' ) ) {
				wp_die( -1 );
			}

			$this->check_input_data( 'usersearch', array(), 'hash' );

			$q = filter_input( INPUT_POST, 'q' );

			$users = $this->get_potential_users( $q );
			$items = array();
			foreach ( $users as $user ) {
				$items[] = array(
					'id'          => $user->id,
					'email'       => $user->email,
					'displayName' => $user->name,
					'thumb'       => $user->avatar,
					'label'       => sprintf(
						'<span class="name title">%1$s</span> <span class="email">(%2$s)</span>',
						$user->name,
						$user->email
					),
					'display'     => $user->name . ' (' . $user->email . ')',
				);
			}

			wp_send_json_success( $items );
		}

		/**
		 * Update allowed users option
		 *
		 * @param array $users
		 * @return array
		 */
		private function update_allowed_users( $users = null ) {
			if ( is_null( $users ) ) {
				$user_id = get_current_user_id();
				if ( is_super_admin( $user_id ) ) {
					$users = array( $user_id );
				} else {
					$users = array();
				}
			}
			branda_update_option( $this->option_name_users, $users );

			return $users;
		}

		public function current_user_has_access() {
			$user_id = get_current_user_id();

			// if it's too early
			if ( ! $user_id ) {
				return false;
			}

			$user_ids = $this->get_allowed_user_ids();

			if ( in_array( $user_id, $user_ids, true ) ) {
				return true;
			}

			$allowed_roles = $this->get_allowed_roles();
			$allow         = Branda_Helper::is_allowed_role( $allowed_roles );

			return $allow;
		}

		/**
		 * Get allowed users option
		 *
		 * @return array
		 */
		public function get_allowed_user_ids() {
			$permission_settings = branda_get_option_filtered( $this->option_name_users );
			$ids                 = ! empty( $permission_settings ) ? $permission_settings : $this->update_allowed_users();

			return $ids;
		}

		/**
		 * Get allowed roles option
		 *
		 * @return array
		 */
		public function get_allowed_roles() {
			$roles   = array_keys( $this->get_value( 'permissions', 'user_roles', array() ) );
			$roles[] = $this->is_network ? 'super' : 'administrator';

			/**
			 * Allowed roles who have access to Branda settings
			 *
			 * @since 3.3
			 * @param array $roles WP roles
			 */
			return apply_filters( 'branda_permissions_allowed_roles', $roles );
		}

		/**
		 * Get allowed users with additional details
		 *
		 * @return array
		 */
		private function get_allowed_users() {
			$ids = $this->get_allowed_user_ids();

			$allowed_users = array();
			foreach ( $ids as $id ) {
				$user = get_userdata( $id );
				if ( ! $user ) {
					continue;
				}

				$allowed_users[] = array(
					'id'     => $user->ID,
					'title'  => $user->display_name,
					'email'  => $user->user_email,
					'avatar' => get_avatar_url( $user->ID ),
					'nonce'  => $this->get_nonce_value( $user->ID, 'remove' ),
				);
			}

			return $allowed_users;
		}

		/**
		 * Returns a list of users.
		 *
		 * The currently logged in user is excluded from the return value, since
		 * this user is not a potentialy but an actualy allowed user.
		 *
		 * @since  3.2
		 *
		 * @param  string $filter Filter by user name.
		 *
		 * @return array List of user-details
		 */
		protected function get_potential_users( $filter ) {
			global $wpdb;
			$items = array();

			if ( is_multisite() && wp_is_large_network( 'users' ) ) {
				return $items;
			}

			$allowed_users = $this->get_allowed_user_ids();
			/*
			 * We build a custom SQL here so we can also get users that are not
			 * assigned to a specific blog but only have access to the network
			 * admin (on multisites).
			 */
			$sql    = "
			SELECT
				u.ID as id,
				u.display_name,
				m_fn.meta_value as first_name,
				m_ln.meta_value as last_name
			FROM {$wpdb->users} u
				LEFT JOIN {$wpdb->usermeta} m_fn ON m_fn.user_id=u.ID AND m_fn.meta_key='first_name'
				LEFT JOIN {$wpdb->usermeta} m_ln ON m_ln.user_id=u.ID AND m_ln.meta_key='last_name'
			WHERE
				u.ID NOT IN ('" . implode( "','", $allowed_users ) . "')
				AND (u.display_name LIKE %s OR m_fn.meta_value LIKE %s OR m_ln.meta_value LIKE %s OR u.user_email LIKE %s)
			";
			$filter = '%' . $filter . '%';
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$sql = $wpdb->prepare(
				$sql,
				$filter,
				$filter,
				$filter,
				$filter
			);

			// Now we have a list of all users, no matter which blog they belong to.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$res = $wpdb->get_results( $sql );

			// Filter users by capabilty.
			foreach ( $res as $item ) {
				$user = get_userdata( $item->id );
				if ( ! $user ) {
					continue;
				}

				$items[] = (object) array(
					'id'         => $user->ID,
					'name'       => $user->display_name,
					'first_name' => $user->user_firstname,
					'last_name'  => $user->user_lastname,
					'email'      => $user->user_email,
					'avatar'     => get_avatar_url( $user->ID ),
				);
			}

			return $items;
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.2
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
			$dialog_id = $this->get_name( 'add-user' );
			/**
			 * Custom Item Row
			 */
			$template = $this->get_template_name( 'tmpl/row' );
			$args     = array(
				'template'  => $this->get_template_name( 'row' ),
				'dialog_id' => $dialog_id,
			);
			$content .= $this->render( $template, $args, true );
			/**
			 * Dialog settings
			 */
			$template = $this->get_template_name( 'dialogs/add-user' );
			$content .= $this->render( $template, array(), true );

			return $content;
		}

		/**
		 * Get list of allowed modules.
		 *
		 * @since 3.2.0
		 */
		public function get_list() {
			$template = $this->get_template_name( 'susbsites' );
			$args     = array(
				'groups' => $this->get_modules( 'group' ),
			);
			return $this->render( $template, $args, true );
		}

		/**
		 * Get modules
		 *
		 * @since 3.2.0
		 */
		private function get_modules( $mode = 'flat' ) {
			$uba           = branda_get_uba_object();
			$configuration = $uba->get_configuration();
			$groups        = branda_get_groups_list();
			$modules       = $uba->get_modules_stats();
			$items         = array();
			$value         = $this->get_value( 'subsites', 'items' );
			foreach ( $modules as $group_key => $group_data ) {
				foreach ( $group_data['modules'] as $module_key => $module_data ) {
					if ( ! isset( $configuration[ $module_key ] ) ) {
						continue;
					}
					$module = $configuration[ $module_key ];
					if (
						! isset( $module['allow-override'] )
						|| 'no' === $module['allow-override']
					) {
						continue;
					}
					$module['id']         = $this->get_name( $module['module'] );
					$module['group_data'] = $groups[ $group_key ];
					$module['title']      = isset( $module['name_alt'] ) ? $module['name_alt'] : $module['name'];
					$module['checked']    = is_array( $value ) && in_array( $module_key, $value );
					$items[ $module_key ] = $module;
				}
			}
			uasort( $items, array( $this, 'sort_items' ) );
			if ( 'flat' === $mode ) {
				return $items;
			}
			$groups = array();
			foreach ( $items as $item ) {
				if ( ! isset( $groups[ $item['group'] ] ) ) {
					$groups[ $item['group'] ] = array();
				}
				$groups[ $item['group'] ][ $item['module'] ] = $item;
			}
			return $groups;
		}

		/**
		 * Check module to show
		 *
		 * @since 3.2.0
		 */
		public function check_module( $status, $key, $module ) {
			if ( empty( $module ) ) {
				$uba    = branda_get_uba_object();
				$module = $uba->get_module_by_module( $key );
				$key    = $module['key'];
			}
			$is_active = branda_is_active_module( $key );
			if ( ! $is_active || ! isset( $module['allow-override'] )
					|| 'no' === $module['allow-override'] ) {
				return $status;
			}
			$value = $this->get_value( 'subsites', 'status' );
			switch ( $value ) {
				case 'enabled':
					return true;
				case 'modules':
					$value = $this->get_value( 'subsites', 'items' );
					if ( is_array( $value ) ) {
						return in_array( $key, $value );
					}
					break;
				default:
					return $status;
			}
			return $status;
		}

		/**
		 * Check group visibility
		 *
		 * @since 3.2.0
		 */
		public function check_group( $status, $key, $group ) {
			foreach ( $group as $module_key => $module ) {
				$status = $this->check_module( $status, $module_key, $module );
				if ( $status ) {
					return $status;
				}
			}
			return false;
		}

		/**
		 * Check option usage
		 *
		 * @since 3.2.0
		 */
		public function check_option( $value, $option_name ) {
			if ( is_network_admin() || $this->is_network_admin_ajax_request() ) {
				return false;
			}
			$uba    = branda_get_uba_object();
			$module = $uba->get_module_by_option( $option_name );
			if ( empty( $module ) || is_wp_error( $module ) ) {
				return $value;
			}
			$check = $this->check_module( $value, $module['key'], $module );
			return $check;
		}

		private function is_network_admin_ajax_request() {
			return defined( 'DOING_AJAX' ) && DOING_AJAX
				   && strpos( wp_get_referer(), network_admin_url() ) === 0;
		}

		/**
		 * Sort items
		 *
		 * @since 3.2.0
		 */
		private function sort_items( $a, $b ) {
			if ( $a['group_data']['title'] === $b['group_data']['title'] ) {
				return strnatcmp( $a['name'], $b['name'] );
			}
			return strnatcmp( $a['group_data']['title'], $b['group_data']['title'] );
		}

		/**
		 * Get all active option names.
		 *
		 * @since 3.2.0
		 */
		public function get_options_names() {
			$options_names = array();
			$value         = $this->get_value( 'subsites', 'status', 'disabled' );
			switch ( $value ) {
				case 'enabled':
					$items = $this->get_modules();
					foreach ( $items as $key => $data ) {
						if ( isset( $data['options'] ) ) {
							$options_names = array_merge( $options_names, $data['options'] );
						}
					}
					break;
				case 'modules':
					$items = $this->get_modules();
					$value = $this->get_value( 'subsites', 'items' );
					foreach ( $items as $key => $data ) {
						if ( ! in_array( $key, $value ) ) {
							continue;
						}
						if ( isset( $data['options'] ) ) {
							$options_names = array_merge( $options_names, $data['options'] );
						}
					}
					break;
				default:
			}
			return $options_names;
		}

		/**
		 * Get allowed modules. It is helper for export.
		 *
		 * @since 3.2.0
		 *
		 * @param array $modules List of active modules.
		 */
		public function get_allowed_modules( $modules ) {
			$value = $this->get_value( 'subsites', 'status', 'disabled' );
			switch ( $value ) {
				case 'disabled':
					return array();
				case 'modules':
					$value   = $this->get_value( 'subsites', 'items', array() );
					$modules = array();
					foreach ( $value as $key ) {
						$modules[ $key ] = 'yes';
					}
					break;
				default:
			}
			return $modules;
		}

		/**
		 * save user status of message
		 */
		public function ajax_save_subsite_messsage_status() {
			$id           = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			update_user_option( $id, $this->option_name, 'hide' );
			wp_send_json_success();
		}

		/**
		 * Add message, based on user status and saved info
		 *
		 * @since 3.2.0
		 */
		public function add_dashboard_message_data( $message ) {
			if ( ! is_super_admin() ) {
				return $message;
			}
			$user_info = wp_get_current_user();
			$hide      = get_user_option( $this->option_name, $user_info->ID );
			if ( 'hide' === $hide ) {
				$message['show'] = false;
				return $message;
			}
			$username = $user_info->user_firstname;
			if ( empty( $username ) ) {
				$username = $user_info->display_name;
			}
			$message['nonce']    = $this->get_nonce_value( $user_info->ID );
			$message['username'] = $username;
			$message['user_id']  = $user_info->ID;
			return $message;
		}
	}
}
Branda_Permissions::get_instance();
