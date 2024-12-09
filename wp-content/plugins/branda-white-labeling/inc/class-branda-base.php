<?php
if ( ! class_exists( 'Branda_Base' ) ) {
	include_once dirname( __FILE__ ) . '/class-branda-loader.php';
	class Branda_Base extends Branda_Loader {
		/**
		 * base URL
		 *
		 * @since 2.1.0
		 */
		protected $base_url = null;

		protected $debug         = false;
		protected $build         = 0;
		protected $modules       = array();
		protected $configuration = array();

		/**
		 * related modules
		 *
		 * @since 2.3.0
		 */
		protected $related = array();

		/**
		 * group
		 *
		 * @since 3.0.0
		 */
		protected $group = 'dashboard';

		/**
		 * Hide Branding
		 *
		 * @since 3.0.6
		 */
		protected $hide_branding = false;

		public function __construct() {
			parent::__construct();
			branda_set_ub_version();
			global $ub_version;
			$this->build = $ub_version;
			/**
			 * Always add this toolbar item, also on front-end.
			 *
			 * @since 1.9.1
			 */
			add_action( 'admin_bar_menu', array( $this, 'setup_toolbar' ), 999 );
			/**
			 * version
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_version', array( $this, 'version' ) );
		}

		/**
		 * return plugin version
		 *
		 * @since 3.0.0
		 */
		public function version( $version ) {
			return $this->build;
		}

		/**
		 * Add link to Branding to the WP toolbar; only for multisite
		 * networks
		 *
		 * @since 1.9.1
		 * @param  WP_Admin_Bar $wp_admin_bar The toolbar handler object.
		 */
		public function setup_toolbar( $wp_admin_bar ) {
			if ( $this->is_network ) {
				$args = array(
					'id'     => 'network-admin-branding',
					'title'  => __( 'Branda Pro', 'ub' ),
					'href'   => add_query_arg( 'page', 'branding', network_admin_url( 'admin.php' ) ),
					'parent' => 'network-admin',
				);
				$wp_admin_bar->add_node( $args );
			}
		}

		/**
		 * get configuration array
		 *
		 * @since 2.3.0
		 */
		public function get_configuration() {
			return $this->configuration;
		}

		/**
		 * get related array
		 *
		 * @since 2.3.0
		 */
		public function get_related() {
			return $this->related;
		}

		public function get_current_group_title() {
			if ( isset( $this->submenu[ $this->group ] ) ) {
				return $this->submenu[ $this->group ]['title'];
			}
			return __( 'Unknown', 'ub' );
		}

		/**
		 * Get relevant chapter to Branda Docs
		 *
		 * @since 3.2.0
		 * @return string
		 */
		public function get_current_group_documentation_chapter() {
			if ( isset( $this->submenu[ $this->group ] ) ) {
				return $this->submenu[ $this->group ]['documentation_chapter'];
			}
			return 'dashboard';
		}

		protected function get_current_page() {
			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

			if ( empty( $page ) ) {
				$page = isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : null;
			}
			if ( empty( $page ) ) {
				$page = 'branding';
			}
			return $page;
		}

		/**
		 * get module by 'module' field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value Module "module" attribute.
		 * @return array|WP_Error Module data or WP_Error.
		 */
		public function get_module_by_module( $value ) {
			foreach ( $this->configuration as $module ) {
				if ( $value === $module['module'] ) {
					return $module;
				}
			}
			$err = new WP_Error(
				'error',
				__( 'Module does not exists.', 'ub' )
			);
			return $err;
		}

		/**
		 * get module by 'options' field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value Module "module" attribute.
		 * @return array|WP_Error Module data or WP_Error.
		 */
		public function get_module_by_option( $value ) {
			foreach ( $this->configuration as $module ) {
				if ( ! isset( $module['options'] ) ) {
					continue;
				}
				if ( in_array( $value, $module['options'] ) ) {
					return $module;
				}
			}
			$err = new WP_Error(
				'error',
				__( 'Module does not exists.', 'ub' )
			);
			return $err;
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
			$template_file = self::get_template_file_name( $file );
			if ( $template_file ) {
				extract( $params, EXTR_OVERWRITE ); // phpcs:ignore
				ob_start();
				include $template_file;
				$content = ob_get_clean();
			}
			if ( $return ) {
				return $content;
			}
			echo $content;
		}

		/**
		 * Get full template path and check if it exists
		 *
		 * @param string $key Path part to template.
		 * @return boolean|string
		 */
		protected static function get_template_file_name( $key ) {
			$template_file = branda_dir( 'views/' . $key ) . '.php';
			if ( ! file_exists( $template_file ) ) {
				return false;
			}

			return $template_file;
		}

		/**
		 * Can module be loaded?
		 *
		 * @since 3.1.0
		 *
		 * @param array $module Module data.
		 */
		protected function can_load_module( $module ) {
			if (
				! $this->is_network
				&& isset( $module['network-activated-only'] )
				&& true === $module['network-activated-only']
			) {
				return apply_filters( 'branda_can_load_module', false, $module );
			}
			if (
				is_multisite()
				&& isset( $module['main-blog-only'] )
				&& true === $module['main-blog-only']
				&& ! is_main_site()
			) {
				/**
				 * Filter allow to change module availability.
				 *
				 * @since 3.1.0
				 *
				 * @param boolean
				 * @param array $module Module data.
				 */
				return apply_filters( 'branda_can_load_module', false, $module );
			}
			if (
				! $this->is_network
				&& isset( $module['network-only'] )
				&& true === $module['network-only']
			) {
				return apply_filters( 'branda_can_load_module', false, $module );
			}
			return apply_filters( 'branda_can_load_module', true, $module );
		}
	}
}
