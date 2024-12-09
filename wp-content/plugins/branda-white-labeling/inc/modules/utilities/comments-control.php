<?php
/**
 * Branda Comments Control class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Branda_Comments_Control' ) ) {

	class Branda_Comments_Control extends Branda_Helper {

		protected $option_name = 'ub_comments_control';
		/**
		 * module status, false will be not working
		 */
		private $status            = false;
		private $disallow_comments = false;
		private $post_types        = array();

		public function __construct() {
			parent::__construct();
			$this->module = 'comments-control';
			add_filter( 'comment_flood_filter', array( $this, 'limit_comments_flood_filter' ), 10, 3 );
			add_filter( 'ultimatebranding_settings_comments_control', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_comments_control_process', array( $this, 'update' ) );
			add_action( 'init', array( $this, 'set' ), 11 );
			add_action( 'init', array( $this, 'upgrade_options' ) );
			//add_action( 'shutdown', array( $this, 'save_post_types' ) );
			add_filter( 'comments_open', array( $this, 'comments_open_check' ), 10, 2 );
			add_filter( 'ultimatebranding_settings_comments_control_preserve', array( $this, 'add_preserve_fields' ) );
		}

		/**
		 * Comments Open filter
		 * // TODO: at some point we should refactor this class to have a single method for checking comment status.
		 *
		 * @since 2.2.0
		 */
		public function comments_open_check( $status, $post_id ) {
			$value = $this->get_value( 'settings', 'status', 'off' );
			if (
				is_admin() // Don't care about the admin area
				|| 'off' === $value // No restrictions on comments
				|| ! $status // Already disabled so we don't need to do anything
			) {
				return $status;
			}

			$by_post_type           = $this->get_value( 'settings', 'by_post_type', 'off' );
			$disabled_post_types    = $this->get_value( 'settings', 'post_types', array() );
			$post_type              = get_post_type( $post_id );
			$disabled_for_post_type = $by_post_type === 'off'
				? true // Disabled for all
				: branda_get_array_value( $disabled_post_types, $post_type ) === 'on'; // Disabled for current type

			$by_blacklist      = $this->get_value( 'settings', 'by_blacklist', 'off' );
			$disabled_for_user = $by_blacklist === 'on'
				? true // Disabled for everyone
				: $this->is_blacklisted_ip();

			$disabled = $disabled_for_post_type && $disabled_for_user;

			return ! $disabled;
		}

		/**
		 * Helper to check IP
		 *
		 * @since 3.0.0
		 */
		private function is_blacklisted_ip() {
			$blacklist = $this->get_value( 'settings', 'blacklist', '' );
			if ( ! is_array( $blacklist ) ) {
				$blacklist = preg_split( '/[, \r\n]/', $blacklist );
			}
			$_remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
			if (
				is_array( $blacklist )
				&& in_array( $_remote_addr, $blacklist )
			) {
				return true;
			}
			return false;
		}

		/**
		 * apply rules
		 */
		public function set() {
			$post_types = $this->get_post_types();
			// This set() method might remove post type support for comments
			// Save unmodified post types for later
			$this->post_types = $post_types;

			$value = $this->get_value( 'settings', 'status', 'off' );
			if ( 'off' === $value ) {
				return;
			}
			$by_post_type = $this->get_value( 'settings', 'by_post_type', 'off' );
			$by_blacklist = $this->get_value( 'settings', 'by_blacklist', 'on' ); // "on" means `IP Blacklist` option is disabled ¯\_(ツ)_/¯
			if ( 'off' === $by_post_type ) { // disable for all post types
				if ( 'on' === $by_blacklist ) { // disable for all IP
					$this->disallow_comments = true;
				} else {
					$is_blacklisted_ip = $this->is_blacklisted_ip();
					if ( $is_blacklisted_ip ) {
						$this->disallow_comments = true;
					}
				}
			}
			/**
			 * off - disable everywhere
			 */
			if ( $this->disallow_comments ) {
				add_action( 'widgets_init', array( $this, 'unregister_widgets' ) );
				add_filter( 'wp_headers', array( $this, 'filter_wp_headers' ) );
				add_action( 'template_redirect', array( $this, 'filter_query' ), 9 );
				// Admin bar filtering has to happen here since WP 3.6
				add_action( 'template_redirect', array( $this, 'filter_admin_bar' ) );
				add_action( 'admin_init', array( $this, 'filter_admin_bar' ) );
				add_action( 'admin_menu', array( $this, 'filter_admin_menu' ), PHP_INT_MAX );
				add_action( 'wp_dashboard_setup', array( $this, 'filter_dashboard' ) );
				add_filter( 'pre_option_default_pingback_flag', '__return_zero' );
				add_filter( 'manage_posts_columns', array( $this, 'remove_column_comments' ) );
				add_filter( 'manage_pages_columns', array( $this, 'remove_column_comments' ) );
				add_filter( 'manage_media_columns', array( $this, 'remove_column_comments' ) );
				foreach ( $post_types as $type => $label ) {
					if ( post_type_supports( $type, 'comments' ) ) {
						remove_post_type_support( $type, 'comments' );
						remove_post_type_support( $type, 'trackbacks' );
					}
				}
			}
		}

		public function upgrade_options() {
			$value = branda_get_option( $this->option_name );
			if ( empty( $value ) ) {
				/**
				 * migrate data from plugin Comments Control
				 * https://wpmudev.com/project/comments-control/
				 */
				$value                          = array();
				$value['rules']['whitelist']    = branda_get_option( 'limit_comments_allowed_ips' );
				$value['settings']['blacklist'] = branda_get_option( 'limit_comments_denied_ips' );
				branda_update_option( $this->option_name, $value );
				branda_delete_option( 'limit_comments_allowed_ips' );
				branda_delete_option( 'limit_comments_denied_ips' );
			}
			$option_name_cpt = 'ub_comments_control_cpt';
			$value           = branda_get_option( $option_name_cpt );
			if ( ! empty( $value ) ) {
				$this->set_value( 'settings', 'available_post_types', $value );
				branda_delete_option( $option_name_cpt );
			}
		}

		public function limit_comments_flood_filter( $flood_die, $time_lastcomment, $time_newcomment ) {
			global $user_id;
			if ( intval( $user_id ) > 0 ) {
				return false;
			}
			/**
			 * get settings
			 */
			$whitelist = $this->get_value( 'rules', 'whitelist', '' );
			$blacklist = $this->get_value( 'settings', 'blacklist', '' );
			if ( trim( $whitelist ) != '' || trim( $blacklist ) != '' ) {
				$_remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
				$_remote_addr = preg_replace( '/\./', '\.', $_remote_addr );
				if ( preg_match( '/' . $_remote_addr . '/i', $whitelist ) > 0 ) {
					return false;
				}
				if ( preg_match( '/' . $_remote_addr . '/i', $blacklist ) > 0 ) {
					return true;
				}
			}
			return $flood_die;
		}

		/**
		 * Set options Configuration
		 *
		 * @since 1.8.6
		 */
		protected function set_options() {
			$description = __( 'You can choose to disable comments globally on your website.', 'ub' );
			if ( $this->is_network ) {
				$description = __( 'You can choose to disable comments globally on your network.', 'ub' );
			}
			$options = array(
				'settings' => array(
					'title'       => __( 'Comments', 'ub' ),
					'description' => $description,
					'fields'      => array(
						'post_types'   => array(
							'type'         => 'checkboxes',
							'master'       => 'enabled-posts',
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
							'options'      => $this->post_types,
						),
						'by_post_type' => array(
							'type'         => 'sui-tab',
							'label'        => __( 'Post Type', 'ub' ),
							'description'  => __( 'Choose post type to disable comments on.', 'ub' ),
							'options'      => array(
								'off' => __( 'All', 'ub' ),
								'on'  => __( 'Certain Post Types', 'ub' ),
							),
							'default'      => 'off',
							'master'       => 'enabled',
							'slave-class'  => 'enabled-posts',
							'display'      => 'sui-tab-content',
							'master-value' => 'on',
						),
						'blacklist'    => array(
							'type'         => 'textarea',
							'description'  => __( 'Type one IP address per line. Both IPv4 and IPv6 are supported. IP ranges are also accepted in format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx.', 'ub' ),
							'classes'      => array( 'large-text' ),
							'master'       => 'enabled-blacklist',
							'placeholder'  => esc_html__( 'Enter your IP blacklist here...', 'ub' ),
							'display'      => 'sui-tab-content',
							'master-value' => 'off',
						),
						'by_blacklist' => array(
							'type'         => 'sui-tab',
							'label'        => __( 'Disable comments for', 'ub' ),
							'description'  => __( 'You can choose to disable comments for everyone or disable comments only for IPs mentioned in an IP Blacklist.', 'ub' ),
							'options'      => array(
								'on'  => __( 'Everyone', 'ub' ),
								'off' => __( 'IP Blacklist', 'ub' ),
							),
							'default'      => 'on',
							'master'       => 'enabled',
							'slave-class'  => 'enabled-blacklist',
							'display'      => 'sui-tab-content',
							'master-value' => 'on',
						),
						'status'       => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'off' => __( 'Enable', 'ub' ),
								'on'  => __( 'Disable', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => 'enabled',
						),
					),
				),
				'rules'    => array(
					'show-as' => 'boxes',
					'fields'  => array(
						'whitelist' => array(
							'type'              => 'textarea',
							'label'             => __( 'IP Whitelist', 'ub' ),
							'description'       => __( 'Type one IP address per line. Both IPv4 and IPv6 are supported. IP ranges are also accepted in format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx.', 'ub' ),
							'description-extra' => array(
								__( 'IPs for which comments will not be throttled. One IP per line or comma separated.', 'ub' ),
							),
							'classes'           => array( 'large-text' ),
							'placeholder'       => esc_html__( 'Enter your IP whitelist here...', 'ub' ),
						),
					),
					'master'  => array(
						'section' => 'settings',
						'field'   => 'status',
						'value'   => 'on',
					),
				),
			);

			$this->options = $options;
		}

		/**
		 * get post types
		 *
		 * @since 2.2.0
		 */
		private function get_post_types() {
			$types = array();
			$args  = array(
				'public' => true,
			);
			$t     = get_post_types( $args, 'objects' );
			foreach ( $t as $key => $one ) {
				/**
				 * Do not list post type which does not supports comments.
				 *
				 * @since x.x.x
				 */
				$post_type_support_comments = post_type_supports( $key, 'comments' );
				if ( false === $post_type_support_comments ) {
					continue;
				}
				if ( isset( $one->labels->singular_name ) ) {
					$types[ $key ] = $one->labels->singular_name;
				} elseif ( isset( $one->label ) ) {
					$types[ $key ] = $one->label;
				} else {
					$types[ $key ] = $key;
				}
			}
			/**
			 * get CPT registered by sites
			 */
			$value = $this->get_value( 'settings', 'available_post_types', array() );
			if ( is_array( $value ) && ! empty( $value ) ) {
				$types += $value;
			}
			asort( $types );
			return $types;
		}

		public function unregister_widgets() {
			unregister_widget( 'WP_Widget_Recent_Comments' );
		}

		/*
		 * Remove the X-Pingback HTTP header
		 */
		public function filter_wp_headers( $headers ) {
			unset( $headers['X-Pingback'] );
			return $headers;
		}

		/*
		 * Issue a 403 for all comment feed requests.
		 */
		public function filter_query() {
			if ( is_comment_feed() ) {
				wp_die( __( 'Comments are closed.', 'ub' ), '', array( 'response' => 403 ) );
			}
		}

		/*
		 * Remove comment links from the admin bar.
		 */
		public function filter_admin_bar() {
			if ( is_admin_bar_showing() ) {
				// Remove comments links from admin bar
				remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
				if ( is_multisite() ) {
					add_action( 'admin_bar_menu', array( $this, 'remove_network_comment_links' ), 500 );
				}
			}
		}

		/*
		 * Remove comment links from the admin bar in a multisite network.
		 */
		public function remove_network_comment_links( $wp_admin_bar ) {
			if ( is_user_logged_in() ) {
				foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
					$wp_admin_bar->remove_menu( 'blog-' . $blog->userblog_id . '-c' );
				}
			} else {
				// We have no way to know whether the plugin is active on other sites, so only remove this one
				$wp_admin_bar->remove_menu( 'blog-' . get_current_blog_id() . '-c' );
			}
		}

		public function filter_admin_menu() {
			global $pagenow;
			if (
				'comment.php' === $pagenow
				|| 'edit-comments.php' === $pagenow
				|| 'options-discussion.php' === $pagenow
			) {
				wp_die( __( 'Comments are closed.', 'ub' ), '', array( 'response' => 403 ) );
			}
			remove_menu_page( 'edit-comments.php' );
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
			/**
			 * remove meta box
			 */
			$post_types = $this->get_post_types();
			$contexts   = array( 'normal', 'advanced', 'side' );
			foreach ( $post_types as $post_type => $label ) {
				foreach ( $contexts as $context ) {
					remove_meta_box( 'commentstatusdiv', $post_type, $context );
					remove_meta_box( 'commentsdiv', $post_type, $context );
				}
			}
		}

		public function filter_dashboard() {
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		}

		public function remove_column_comments( $columns ) {
			if ( isset( $columns['comments'] ) ) {
				unset( $columns['comments'] );
			}
			return $columns;
		}

		public function save_post_types() {
			$post_types = get_post_types( array( '_builtin' => false ), 'objects' );
			if ( empty( $post_types ) ) {
				return;
			}
			$types = $this->get_value( 'settings', 'available_post_types', array() );
			foreach ( $post_types as $key => $one ) {
				$support = post_type_supports( $key, 'comments' );
				if ( ! $support ) {
					continue;
				}
				if ( isset( $one->labels->singular_name ) ) {
					$types[ $key ] = $one->labels->singular_name;
				} elseif ( isset( $one->label ) ) {
					$types[ $key ] = $one->label;
				} else {
					$types[ $key ] = $key;
				}
			}
			$this->set_value( 'settings', 'available_post_types', $types );
		}

		private function filter_by_post( $post ) {
			if ( true === $this->disallow_comments ) {
				return true;
			}
			if ( ! is_a( $post, 'WP_Post' ) ) {
				$post = get_post( $post );
			}
			if ( ! is_a( $post, 'WP_Post' ) ) {
				return false;
			}
			if ( array_key_exists( $post->post_type, $this->post_types ) ) {
				return true;
			}
			return false;
		}

		public function filter_existing_comments( $comments, $post_id ) {
			$filter = $this->filter_by_post( $post_id );
			if ( $filter ) {
				return array();
			}
			return $comments;
		}

		public function filter_comment_status( $open, $post_id ) {
			$filter = $this->filter_by_post( $post_id );
			if ( $filter ) {
				return false;
			}
			return $open;
		}

		public function setup_notice() {
			$screen = get_current_screen();
			if ( ! preg_match( '/page_branding/', $screen->id ) ) {
				return;
			}
			$url     = $this->get_base_url();
			$url     = add_query_arg(
				array(
					'page'   => 'branding_group_utilities',
					'module' => $this->module,
				),
				$url
			);
			$message = sprintf( __( 'The <em>Comments Control</em> module is active, but is not properly configured. Visit the <a href="%s">configuration page</a> to choose which post types to disable comments on.', 'ub' ), esc_url( $url ) );
			echo Branda_Helper::sui_notice( $message, 'info' );
		}

		/**
		 * Add settings sections to prevent delete on save.
		 *
		 * Add settings sections (virtual options not included in
		 * "set_options()" function to avoid delete during update.
		 *
		 * @since 3.0.0
		 */
		public function add_preserve_fields() {
			return array(
				'settings' => array(
					'available_post_types',
				),
			);
		}
	}
}
new Branda_Comments_Control();
