<?php
if ( ! class_exists( 'Branda_Email_Logs_CPT' ) ) {
	/**
	 * Branda Email Logs post type class.
	 *
	 * @package Branda
	 * @subpackage Emails
	 */
	class Branda_Email_Logs_CPT {
		/**
		 * Instance
		 *
		 * @var object
		 */
		private static $instance;

		const CPT_NAME = 'branda_email_log';

		/**
		 * Instance
		 *
		 * @return object
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Get total amount of the CPT items
		 *
		 * @return int
		 */
		public static function get_total_cpt() {
			$counts = (array) wp_count_posts( self::CPT_NAME );
			$total  = array_sum( $counts );

			return $total;
		}

		/**
		 * Get email logs CPT items
		 *
		 * @param array $attrs Main attributes based on GET params.
		 * @return array
		 */
		private static function get_email_logs( $attrs ) {
			$args = self::prepare_args_for_get_posts( $attrs );
			$logs = get_posts( $args );

			return $logs;
		}

		/**
		 * Slice relevant items from all CPT items
		 *
		 * @param array $items All CPT items.
		 * @param array $attrs Main attributes based on GET params.
		 * @return array
		 */
		public static function items_slice( $items, $attrs ) {
			$slice = array_slice( $items, $attrs['offset'], $attrs['limit'] );

			// Go to the first paginated list if $slice doesn't have items.
			if ( $attrs['offset'] && $items && ! $slice ) {
				$slice = array_slice( $items, 0, $attrs['limit'] );
				unset( $_GET['paged'] );
			}

			return $slice;
		}

		/**
		 * Prepare arguments for getting CPT
		 *
		 * @param array $attrs Main attributes based on GET params.
		 * @return array
		 */
		private static function prepare_args_for_get_posts( $attrs ) {
			$args = array(
				'post_type'   => self::CPT_NAME,
				'order'       => strtoupper( $attrs['order'] ),
				'post_status' => 'any',
				'numberposts' => -1,
			);

			$args = self::add_orderby_attrs( $args, $attrs['order_by'] );

			if ( ! empty( $attrs['id'] ) ) {
				$args['include'] = (int) $attrs['id'];
			}

			if ( ! empty( $attrs['is_filtered'] ) ) {
				$post_meta = self::get_post_meta_query_args( $attrs );

				if ( ! empty( $post_meta ) ) {
					$args['meta_query'] = array(
						'relation' => 'AND',
						$post_meta,
					);
				}

				if ( ! empty( $attrs['keyword'] ) ) {
					$args['s'] = $attrs['keyword'];
				}

				if ( ! empty( $attrs['date_range'] ) ) {
					list( $after, $before ) = explode( '-', $attrs['date_range'] );
					$args['date_query']     = array(
						array(
							'after'     => trim( $after ),
							'before'    => trim( $before ),
							'inclusive' => true,
						),
					);
				}
			}

			return $args;
		}

		/**
		 * Prepare orderby arguments for getting CPT items.
		 *
		 * @param type $args Prepared arguments.
		 * @param type $order_by Selected `order by` field.
		 * @return array $args
		 */
		private static function add_orderby_attrs( $args, $order_by ) {
			$prefix          = 'ub_email_logs_';
			$args['orderby'] = 'meta_value';
			switch ( $order_by ) {
				case 'from_name':
				case 'from_email':
				case 'recipient':
					$args['meta_key'] = $prefix . $order_by;
					break;

				default:
					$args['orderby'] = $order_by;
					break;
			}

			return $args;
		}

		/**
		 * Prepare meta_query arguments.
		 *
		 * @param array $attrs Main attributes based on GET params.
		 * @return array
		 */
		private static function get_post_meta_query_args( $attrs ) {
			$prefix    = 'ub_email_logs_';
			$post_meta = array();
			if ( ! empty( $attrs['from_name'] ) ) {
				$post_meta[] = array(
					'key'   => $prefix . 'from_name',
					'value' => $attrs['from_name'],
				);
			}
			if ( ! empty( $attrs['from_email'] ) ) {
				$post_meta[] = array(
					'key'   => $prefix . 'from_email',
					'value' => $attrs['from_email'],
				);
			}
			if ( ! empty( $attrs['recipient'] ) ) {
				$post_meta[] = array(
					'key'   => $prefix . 'recipient',
					'value' => $attrs['recipient'],
				);
			}

			return $post_meta;
		}

		/**
		 * Get prepared Email Logs array for the main list
		 *
		 * @param array $attrs Main attributes based on GET params.
		 * @return array
		 */
		public static function get_items( $attrs ) {
			$logs  = self::get_email_logs( $attrs );
			$items = self::prepared_items( $logs );

			return $items;
		}

		/**
		 * Get prepared data from CPT
		 *
		 * @param array $logs Array of CPT.
		 * @return array
		 */
		private static function prepared_items( $logs ) {
			$prefix = 'ub_email_logs_';
			$items  = array();
			foreach ( $logs as $post ) {
				$id      = $post->ID;
				$items[] = array(
					'id'         => $id,
					'post_title' => $post->post_title,
					'date'       => wp_date( 'F d, Y @ h:ia', strtotime( $post->post_date_gmt ) ),
					'recipient'  => get_post_meta( $id, $prefix . 'recipient', true ),
					'from_name'  => get_post_meta( $id, $prefix . 'from_name', true ),
					'from_email' => get_post_meta( $id, $prefix . 'from_email', true ),
				);
			}

			return $items;
		}

		/**
		 * Save email history
		 *
		 * @param object $phpmailer Phpmailer object.
		 * @param bool   $is_sent result of the send action.
		 * @param array  $to email addresses of the recipients.
		 * @param array  $cc cc email addresses.
		 * @param array  $bcc bcc email addresses.
		 * @param string $subject email subject.
		 * @param string $body email body.
		 * @param string $from email address of sender.
		 */
		public static function save_email_history( $phpmailer, $is_sent, $to, $cc, $bcc, $subject, $body, $from ) {
			if ( ! $is_sent ) {
				return;
			}
			$prefix    = 'ub_email_logs_';
			$post_data = array(
				'post_title'    => $subject,
				'post_type'     => self::CPT_NAME,
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s' ),
				'meta_input'    => array(
					$prefix . 'from_name'  => $phpmailer->FromName,
					$prefix . 'from_email' => $from,
					$prefix . 'recipient'  => $to[0],
				),
			);

			wp_insert_post( $post_data );
		}

		/**
		 * Delete post.
		 *
		 * @param int $id Post id.
		 */
		public static function delete_post( $id ) {
			if ( self::CPT_NAME === get_post_type( $id ) ) {
				wp_delete_post( $id, true );
			}
		}
		/**
		 * Register Custom Post Type for Email Logs
		 */
		public static function register_email_logs_cpt() {
			$labels = array(
				'name'          => _x( 'Email Logs', 'Post Type General Name', 'ub' ),
				'singular_name' => _x( 'Email Log', 'Post Type Singular Name', 'ub' ),
			);
			$args   = array(
				'label'               => __( 'Email Log', 'ub' ),
				'description'         => __( 'Post Type Description', 'ub' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'custom-fields' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'menu_position'       => 5,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'show_in_rest'        => false,
			);

			register_post_type( self::CPT_NAME, $args );
		}
	}
}
