<?php
/**
 * Branda Admin Panel Tips class.
 *
 * Class that handles admin panel tips.
 *
 * @package Branda
 * @subpackage AdminArea
 */
if ( ! class_exists( 'Branda_Admin_Panel_Tips' ) ) {
	/**
	 * Class Branda_Admin_Panel_Tips
	 */
	class Branda_Admin_Panel_Tips extends Branda_Helper {
		/**
		 * Admin url.
		 *
		 * @var string
		 */
		private $admin_url = '';

		/**
		 * Custom post type name.
		 *
		 * @var string
		 */
		private $post_type = 'admin_panel_tip';

		/**
		 * Meta field name.
		 *
		 * @var string
		 */
		private $meta_field_name = '_ub_page';

		/**
		 * Meta field name till.
		 *
		 * @var string
		 */
		private $meta_field_name_till = '_ub_till';

		/**
		 * User meta "Show Tips" name.
		 * It can be changed on profile page.
		 *
		 * @since 3.0.6
		 *
		 * @var string
		 */
		private $profile_show_tips_name = 'show_tips';

		/**
		 * Branda_Admin_Panel_Tips constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'admin-panel-tips';
			// Register hooks for the module.
			add_action( 'save_post', array( $this, 'save_post' ) );
			add_action( 'save_post', array( $this, 'save_post_till' ) );
			add_action( 'admin_notices', array( $this, 'output' ) );
			add_action( 'profile_personal_options', array( $this, 'profile_option_output' ) );
			add_action( 'personal_options_update', array( $this, 'profile_option_update' ) );
			add_action( 'wp_ajax_branda_admin_panel_tips', array( $this, 'ajax_save_dissmissed' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'init', array( $this, 'custom_post_type' ), 100 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		}

		/**
		 * Set options for the module.
		 *
		 * We don't have any options for this module,
		 * so just show the text.
		 *
		 * @access protected
		 */
		protected function set_options() {
			$description   = Branda_Helper::sui_notice( __( 'This module has no global configuration. Please go to site admin and add some tips!', 'ub' ) );
			$this->options = array(
				'description' => array(
					'title'       => '', // No title needed.
					'description' => $description,
				),
			);
		}

		/**
		 * Register custom post type.
		 *
		 * Register custom post type for the admin
		 * panel tips module.
		 *
		 * @uses register_post_type()
		 */
		public function custom_post_type() {
			// We need this only in admin side.
			if ( ! is_admin() ) {
				return;
			}
			// Do not load on multisite network admin.
			if ( is_multisite() && is_network_admin() ) {
				return;
			}
			// CPT labels.
			$labels = array(
				'name'                  => _x( 'Tips', 'Tip General Name', 'ub' ),
				'singular_name'         => _x( 'Tip', 'Tip Singular Name', 'ub' ),
				'menu_name'             => __( 'Tips', 'ub' ),
				'name_admin_bar'        => __( 'Tip', 'ub' ),
				'archives'              => __( 'Tip Archives', 'ub' ),
				'attributes'            => __( 'Tip Attributes', 'ub' ),
				'parent_item_colon'     => __( 'Parent Tip:', 'ub' ),
				'all_items'             => __( 'Tips', 'ub' ),
				'add_new_item'          => __( 'Add New Tip', 'ub' ),
				'add_new'               => __( 'Add New', 'ub' ),
				'new_item'              => __( 'New Tip', 'ub' ),
				'edit_item'             => __( 'Edit Tip', 'ub' ),
				'update_item'           => __( 'Update Tip', 'ub' ),
				'view_item'             => __( 'View Tip', 'ub' ),
				'view_items'            => __( 'View Tips', 'ub' ),
				'search_items'          => __( 'Search Tip', 'ub' ),
				'not_found'             => __( 'Not found', 'ub' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'ub' ),
				'featured_image'        => __( 'Featured Image', 'ub' ),
				'set_featured_image'    => __( 'Set featured image', 'ub' ),
				'remove_featured_image' => __( 'Remove featured image', 'ub' ),
				'use_featured_image'    => __( 'Use as featured image', 'ub' ),
				'insert_into_item'      => __( 'Insert into item', 'ub' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'ub' ),
				'items_list'            => __( 'Tips list', 'ub' ),
				'items_list_navigation' => __( 'Tips list navigation', 'ub' ),
				'filter_items_list'     => __( 'Filter items list', 'ub' ),
			);
			// Do not show CPT in Admin area on subisite if permissions forbid it.
			$show_ui = true;
			if ( is_multisite() && ! is_main_site() ) {
				$allowed = apply_filters( 'branda_module_check_for_subsite', false, 'admin_menu', array() );
				$show_ui = ! ! $allowed;
			}
			// CPT arguments.
			$args = array(
				'label'               => __( 'Admin Panel Tips', 'ub' ),
				'description'         => __( 'Tip Description', 'ub' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => $show_ui,
				'show_in_admin_bar'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'menu_icon'           => $this->uba->get_u_logo(),
			);
			// Register cpt.
			register_post_type( $this->post_type, $args );
		}

		/**
		 * Ajax to hide tips for the user.
		 *
		 * Store a flag in user meta to hide admin
		 * panel tips for the user.
		 */
		public function ajax_save_dissmissed() {
			$keys = array( 'nonce', 'id', 'user_id' );
			foreach ( $keys as $key ) {
				if ( ! isset( $_POST[ $key ] ) ) {
					wp_send_json_error();
				}
			}
			/**
			 * Sanitize input
			 */
			$nonce   = ! empty( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			$user_id = ! empty( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : '';
			$post_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );

			/**
			 * Dismiss
			 */
			$nonce_action = $this->get_nonce_action( $post_id, 'dismiss' );
			if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
				$dismissed_tips   = (array) get_user_meta( $user_id, 'tips_dismissed', true );
				$dismissed_tips[] = $post_id;
				update_user_meta( $user_id, 'tips_dismissed', $dismissed_tips );
				wp_send_json_success();
			}
			/**
			 * Hide it all
			 */
			$nonce_action = $this->get_nonce_action( $post_id, 'hide' );
			if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
				update_user_meta( $user_id, $this->profile_show_tips_name, 'no' );
				wp_send_json_success();
			}
			// Show json error.
			wp_send_json_error();
		}

		/**
		 * Enqueue scripts and styles for the module.
		 *
		 * @uses wp_enqueue_style()
		 * @uses wp_enqueue_script()
		 */
		public function enqueue_scripts() {
			$handler = $this->get_name();
			wp_enqueue_style( $handler, plugins_url( 'assets/css/admin/admin-panel-tips.css', __FILE__ ), array(), $this->build );
			wp_enqueue_script( $handler, plugins_url( 'assets/js/admin/admin-panel-tips.js', __FILE__ ), array( 'jquery' ), $this->build, true );
			$data = array(
				'saving' => __( 'Saving...', 'ub' ),
			);
			wp_localize_script( $handler, 'branda_admin_panel_tips', $data );
		}

		/**
		 * Update user meta for tips.
		 */
		public function profile_option_update() {
			global $user_id;
			$show_tips = ! empty( $_POST[ $this->profile_show_tips_name ] ) ? sanitize_text_field( $_POST[ $this->profile_show_tips_name ] ) : '';
			$show_tips = 0 === $show_tips ? 'no' : 'yes';
			update_user_meta( $user_id, $this->profile_show_tips_name, $show_tips );
		}

		/**
		 * Admin notices for admin panel tips.
		 *
		 * Show admin notices for admin panel tips.
		 */
		public function output() {
			// Avoid activate/deactivate actions.
			if ( isset( $_GET['updated'] ) || isset( $_GET['activated'] ) ) {
				return;
			}
			// Do not show tips on Branda pages.
			$current_screen = get_current_screen();
			if ( 'branding' === $current_screen->parent_base ) {
				return;
			}
			global $current_user;
			// Hide if turned off.
			$show_tips = get_user_meta( $current_user->ID, $this->profile_show_tips_name, true );
			if ( 'no' === $show_tips ) {
				return;
			}
			$meta_query = array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'   => $this->meta_field_name,
						'value' => 'everywhere',
					),
					array(
						'key'   => $this->meta_field_name,
						'value' => $current_screen->parent_file,
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => $this->meta_field_name_till,
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => $this->meta_field_name_till,
						'value'   => time(),
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
			);
			$args       = array(
				'orderby'        => 'rand',
				'posts_per_page' => 1,
				'post_type'      => $this->post_type,
				'post_status'    => 'publish',
				'meta_query'     => $meta_query,
			);
			// Get closed tips list.
			$post__not_in = get_user_meta( get_current_user_id(), 'tips_dismissed', true );
			if ( ! empty( $post__not_in ) ) {
				if ( ! is_array( $post__not_in ) ) {
					$post__not_in = array( $post__not_in );
				}
				$args['post__not_in'] = $post__not_in;
			}
			// Get tips.
			$the_query = new WP_Query( $args );
			if ( $the_query->posts ) {
				$post = array_shift( $the_query->posts );
				if ( is_a( $post, 'WP_Post' ) ) {
					$content  = $post->post_content;
					$content  = do_shortcode( $content );
					$content  = wpautop( $content );
					$args     = array(
						'id'            => $post->ID,
						'nonce_dismiss' => $this->get_nonce_value( $post->ID, 'dismiss' ),
						'nonce_hide'    => $this->get_nonce_value( $post->ID, 'hide' ),
						'content'       => $content,
						'title'         => apply_filters( 'the_title', $post->post_title ),
						'user_id'       => get_current_user_id(),
					);
					$template = $this->get_template_name( 'tip' );
					$this->render( $template, $args );
				}
				wp_reset_postdata();
			}
		}

		/**
		 * Profile form field to show/hide tips.
		 *
		 * Let users hide or show tips from their profile
		 * edit page.
		 */
		public function profile_option_output() {
			if ( is_network_admin() ) {
				return;
			}
			$user_id   = get_current_user_id();
			$show_tips = get_user_meta( $user_id, $this->profile_show_tips_name, true );
			if ( null === $show_tips ) {
				$show_tips = true;
			} elseif ( preg_match( '/^(false|hidden|no)$/', $show_tips ) ) {
				$show_tips = false;
			} else {
				$show_tips = true;
			}
			$args     = array(
				$this->profile_show_tips_name => $show_tips,
			);
			$template = sprintf( '/admin/modules/%s/profile-option', $this->module );
			$this->render( $template, $args );
		}

		/**
		 * Save meta for tips post.
		 *
		 * While saving a admin tips post, save the meta
		 * to know where to load the admin notices for the
		 * admin panel tips.
		 *
		 * @param int $post_id Post ID.
		 *
		 * @since 1.8.6
		 */
		public function save_post( $post_id ) {
			// Continue only if post type is admin panel tips.
			if ( get_post_type( $post_id ) !== $this->post_type ) {
				return;
			}
			// Security check.
			if ( ! isset( $_POST['where_to_display_nonce'] ) || ! wp_verify_nonce( $_POST['where_to_display_nonce'], '_where_to_display_nonce' ) ) {
				return;
			}
			// Get meta values from the form.
			$values = array();
			if ( isset( $_POST[ $this->meta_field_name ] ) ) {
				$values = $_POST[ $this->meta_field_name ];
			}
			// Default value.
			if ( empty( $values ) ) {
				$values = array( 'everywhere' );
			}
			// Get current meta value.
			$current = get_post_meta( $post_id, $this->meta_field_name );
			// Remove unchecked items.
			foreach ( $current as $v ) {
				if ( in_array( $v, $values ) ) {
					continue;
				}
				delete_post_meta( $post_id, $this->meta_field_name, $v );
			}
			// Add new items.
			foreach ( $values as $v ) {
				if ( in_array( $v, $current ) ) {
					continue;
				}
				add_post_meta( $post_id, $this->meta_field_name, $v );
			}
		}

		/**
		 * Get where to display, from meta.
		 *
		 * @param string $key Meta key.
		 *
		 * @return bool|mixed|string
		 */
		public function where_to_display__get_meta( $key ) {
			global $post;
			$field = get_post_meta( $post->ID, $key, true );
			if ( ! empty( $field ) ) {
				return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
			}
			return false;
		}

		/**
		 * Add meta box to post form.
		 *
		 * Add new meta box to ask users, where
		 * to show admin panel tips.
		 *
		 * @uses add_meta_box()
		 *
		 * @since 1.8.8
		 */
		public function add_meta_boxes() {
			add_meta_box(
				'where_to_display',
				__( 'Where to display?', 'ub' ),
				array( $this, 'html' ),
				'admin_panel_tip',
				'side',
				'default'
			);
			add_meta_box(
				'till',
				__( 'Display till', 'ub' ),
				array( $this, 'add_till' ),
				'admin_panel_tip',
				'side',
				'default'
			);
		}

		/**
		 * HTML for meta box in post form.
		 *
		 * @param WP_Post $post Post object.
		 *
		 * @since 1.8.8
		 */
		public function html( $post ) {
			global $menu;
			// Nonce field.
			wp_nonce_field( '_where_to_display_nonce', 'where_to_display_nonce' );
			echo '<p>';
			esc_html_e( 'Allow to choose where this tip should be shown:', 'ub' );
			echo '</p>';
			$current = get_post_meta( $post->ID, $this->meta_field_name );
			$checked = in_array( 'everywhere', $current );
			echo '<ul>';
			printf(
				'<li><label><input type="checkbox" name="%s[]" value="everywhere" %s/> %s</label>',
				esc_attr( $this->meta_field_name ),
				checked( $checked, true, false ),
				esc_html__( 'Everywhere (except Branding)', 'ub' )
			);
			foreach ( $menu as $one ) {
				if ( empty( $one[0] ) ) {
					continue;
				}
				// Disalow on branding pages.
				if ( 'branding' === $one[2] ) {
					continue;
				}
				$checked = in_array( $one[2], $current );
				printf(
					'<li><label><input type="checkbox" name="%s[]" value="%s" %s/> %s</label>',
					esc_attr( $this->meta_field_name ),
					esc_attr( $one[2] ),
					checked( $checked, true, false ),
					esc_html( preg_replace( '/<.+/', '', $one[0] ) )
				);
			}
			echo '</ul>';
		}

		/**
		 * Add meta box to get the expiry date.
		 *
		 * @param WP_Post $post Post object.
		 *
		 * @since 2.3.0
		 */
		public function add_till( $post ) {
			// Security check.
			wp_nonce_field( '_till_date_nonce', 'till_date_nonce' );
			printf( '<p>%s</p>', esc_html__( 'Till date:', 'ub' ) );
			printf( '<p class="description">%s</p>', esc_html__( 'Leave empty to unlimited tip time.', 'ub' ) );
			$current = get_post_meta( $post->ID, $this->meta_field_name_till, true );
			if ( ! empty( $current ) ) {
				$current = date_i18n( get_option( 'date_format' ), $current );
			}
			$alt = sprintf( '%s_%s', $this->meta_field_name_till, $this->generate_id( $current ) );
			printf(
				'<input type="text" class="datepicker" name="%s[human]" value="%s" data-alt="%s" data-min="%s" />',
				esc_attr( $this->meta_field_name_till ),
				esc_attr( $current ),
				esc_attr( $alt ),
				esc_attr( date( 'y-m-d', time() ) )
			);
			printf(
				'<input type="hidden" name="%s[alt]" value="%s" id="%s" />',
				esc_attr( $this->meta_field_name_till ),
				esc_attr( $current ),
				esc_attr( $alt )
			);
			// Styles and scripts for the date picker.
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_localize_jquery_ui_datepicker();
			wp_enqueue_style(
				$this->get_name( 'ui', 'jquery' ),
				branda_url( 'assets/css/vendor/jquery-ui.min.css' ),
				array(),
				'1.12.1'
			);
		}

		/**
		 * Save meta for expiry date of tips.
		 *
		 * While saving a admin tips post, save the meta
		 * to know when should stop showing the tips.
		 *
		 * @param int $post_id Post ID.
		 *
		 * @since 2.3.0
		 */
		public function save_post_till( $post_id ) {
			// Continue only for tips post.
			if ( get_post_type( $post_id ) !== $this->post_type ) {
				return;
			}
			// Security check.
			if ( ! isset( $_POST['till_date_nonce'] ) || ! wp_verify_nonce( $_POST['till_date_nonce'], '_till_date_nonce' ) ) {
				return;
			}
			// Get the values.
			$values = array();
			if ( isset( $_POST[ $this->meta_field_name_till ] ) ) {
				$values = $_POST[ $this->meta_field_name_till ];
			}
			// Delete existing value.
			delete_post_meta( $post_id, $this->meta_field_name_till );
			if ( isset( $values['human'] ) ) {
				if ( empty( $values['human'] ) ) {
					return;
				}
			} else {
				return;
			}
			if ( ! isset( $values['alt'] ) || empty( $values['alt'] ) ) {
				return;
			}
			$date = strtotime( sprintf( '%s 23:59:59', $values['alt'] ) );
			// Save new date.
			add_post_meta( $post_id, $this->meta_field_name_till, $date, true );
		}
	}
}
new Branda_Admin_Panel_Tips();
