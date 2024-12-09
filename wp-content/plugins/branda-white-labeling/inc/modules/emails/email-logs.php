<?php
/**
 * Branda Email Logs class.
 *
 * @package Branda
 * @subpackage Emails
 */

// Include CPT class.
require_once 'email-logs-cpt.php';

if ( ! class_exists( 'Branda_Email_Logs' ) ) {

	class Branda_Email_Logs extends Branda_Helper {
		protected $option_name = 'ub_email_logs';
		private $module_name;

		public function __construct() {
			parent::__construct();
			$this->module      = 'email-logs';
			$this->module_name = Branda_Helper::hyphen_to_underscore( $this->module );

			add_filter( 'ultimatebranding_settings_' . $this->module_name, array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_' . $this->module_name . '_process', array( $this, 'update' ) );

			// Branda admin enqueue scripts.
			add_action( 'branda_admin_enqueue_module_admin_assets', array( $this, 'enqueue_scripts' ) );

			// Add "Settings" button.
			add_filter( 'branda_settings_after_box_title', array( $this, 'add_button_after_title' ), 10, 2 );

			// Disable button "Save Changes".
			add_filter( 'ultimatebranding_settings_panel_show_submit', array( $this, 'disable_save_changes_button' ), 10, 2 );

			// Change module content.
			add_filter( 'branda_get_module_content', array( $this, 'change_main_content' ), 10, 2 );

			add_action( 'init', array( 'Branda_Email_Logs_CPT', 'register_email_logs_cpt' ), 0 );

			// `Delete` dialog attrs.
			add_filter( 'branda_dialog_delete_attr', array( $this, 'dialog_delete_attr_filter' ), 10, 3 );

			// Handle Delete log(s).
			add_action( 'wp_ajax_branda_email_logs_delete', array( $this, 'ajax_delete' ) );
			add_action( 'wp_ajax_branda_email_logs_delete_bulk', array( $this, 'ajax_delete_bulk' ) );

			// Export.
			add_action( 'init', array( $this, 'singular_email_log_export' ), 0 );

			// Add crone for remove CPT items.
			add_action( 'init', array( self::class, 'add_schedule' ), 0 );
			// Handle crone action.
			add_action( 'branda_email_logs_cleaning', array( $this, 'email_logs_cleaning' ) );

			// Deactivation this module.
			add_action( 'branda_module_deactivated', array( $this, 'module_deactivated' ) );
		}

		/**
		 * Add crone for remove CPT items.
		 */
		public static function add_schedule() {
			if ( ! wp_next_scheduled( 'branda_email_logs_cleaning' ) ) {
				wp_schedule_event( time(), 'hourly', 'branda_email_logs_cleaning' );
			}
		}

		/**
		 * Actions after deactivation this module.
		 */
		public function module_deactivated() {
			// Remove cron.
			wp_unschedule_hook( 'branda_email_logs_cleaning' );
		}

		/**
		 * Run cron for remove CPT items.
		 */
		public function email_logs_cleaning() {
			$log_limit = $this->get_value( 'clear', 'items' );
			if ( empty( $log_limit ) || ! is_numeric( $log_limit ) ) {
				// Limit isn't set.
				return;
			}

			$log_limit        = intval( $log_limit );
			$args             = self::get_default_cpt_args();
			$args['order_by'] = 'ID';

			$items          = Branda_Email_Logs_CPT::get_items( $args );
			$args['limit']  = $log_limit;
			$all_ids        = wp_list_pluck( $items, 'id' );
			$ids_for_remove = array_slice( $all_ids, $log_limit );
			if ( empty( $ids_for_remove ) ) {
				// Limit isn't reached.
				return;
			}
			foreach ( $ids_for_remove as $id ) {
				Branda_Email_Logs_CPT::delete_post( $id );
			}
		}

		/**
		 * Enqueue scripts for the module.
		 *
		 * @param string $module Current module.
		 */
		public function enqueue_scripts( $module ) {
			if ( $this->module !== $module ) {
				return;
			}

			wp_enqueue_script(
				'branda-moment-js',
				branda_url( 'assets/js/vendor/moment.min.js' ),
				array( 'jquery' ),
				$this->build,
				true
			);

			wp_enqueue_script(
				'branda-datepicker-range',
				branda_url( 'assets/js/vendor/daterangepicker.min.js' ),
				array( 'jquery', 'branda-moment-js' ),
				$this->build,
				true
			);

			// Use inline script to allow hooking into this.
			$daterangepicker_ranges = sprintf(
				"
				var branda_datepicker_ranges = {
					'%s': [moment(), moment()],
					'%s': [moment().subtract(1,'days'), moment().subtract(1,'days')],
					'%s': [moment().subtract(6,'days'), moment()],
					'%s': [moment().subtract(29,'days'), moment()],
					'%s': [moment().startOf('month'), moment().endOf('month')],
					'%s': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
				};",
				__( 'Today', 'ub' ),
				__( 'Yesterday', 'ub' ),
				__( 'Last 7 Days', 'ub' ),
				__( 'Last 30 Days', 'ub' ),
				__( 'This Month', 'ub' ),
				__( 'Last Month', 'ub' )
			);

			/**
			 * Filter ranges to be used on submissions date range
			 *
			 * @since 3.4.0
			 * @param string $daterangepicker_ranges
			 */
			$daterangepicker_ranges = apply_filters( 'branda_datepicker_ranges', $daterangepicker_ranges );

			wp_add_inline_script( 'branda-datepicker-range', $daterangepicker_ranges );

			$locale = self::get_locale();
			wp_localize_script( 'branda-datepicker-range', 'branda_datepicker_locale', $locale );
		}

		/**
		 * Get locale for daterangepicker js.
		 *
		 * @return array
		 */
		private static function get_locale() {
			$data = array(
				'daysOfWeek' => array(
					esc_html__( 'Su', 'ub' ),
					esc_html__( 'Mo', 'ub' ),
					esc_html__( 'Tu', 'ub' ),
					esc_html__( 'We', 'ub' ),
					esc_html__( 'Th', 'ub' ),
					esc_html__( 'Fr', 'ub' ),
					esc_html__( 'Sa', 'ub' ),
				),
				'monthNames' => array(
					__( 'January', 'ub' ),
					__( 'February', 'ub' ),
					__( 'March', 'ub' ),
					__( 'April', 'ub' ),
					__( 'May', 'ub' ),
					__( 'June', 'ub' ),
					__( 'July', 'ub' ),
					__( 'August', 'ub' ),
					__( 'September', 'ub' ),
					__( 'October', 'ub' ),
					__( 'November', 'ub' ),
					__( 'December', 'ub' ),
				),
			);

			return $data;
		}

		/**
		 * Make email clickable via mailto:
		 *
		 * @param string $email Email.
		 * @return string
		 */
		public static function make_email_clickable( $email ) {
			if ( is_email( $email ) ) {
				$email = '<a href="mailto:' . $email . '" target="_blank" title="' . __( 'Send Email', 'ub' ) . '">' . $email . '</a>';
			}

			return $email;
		}

		/**
		 * Add "Settigs" button.
		 *
		 * @since 3.4
		 * @param string $content Current content.
		 * @param array  $module Current module.
		 * @return string
		 */
		public function add_button_after_title( $content, $module ) {
			if ( $this->module !== $module['module'] || ! self::is_full_pro() ) {
				return $content;
			}
			$args     = array(
				'data' => array(
					'modal-open' => $this->get_name( 'settings' ),
				),
				'icon' => 'widget-settings-config',
				'text' => __( 'Settings', 'ub' ),
			);
			$content .= '<div class="sui-actions-right">';
			$content .= $this->button( $args );
			$content .= '</div>';

			return $content;
		}


		/**
		 * Disable button "Save Changes".
		 *
		 * @param bool  $show Show this button or not.
		 * @param array $module Current module.
		 * @return bool
		 */
		public function disable_save_changes_button( $show, $module ) {
			if ( $this->module === $module['module'] ) {
				return false;
			}
			return $show;
		}

		/**
		 * Modify option name
		 *
		 * @param string $option_name Option name.
		 * @param string $module Module name.
		 * @return string
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && $this->module == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		/**
		 * Set options
		 *
		 * @since 2.0.0
		 */
		protected function set_options() {
			$options = array(
				'global-show-as' => 'flat',
				'export'         => array(
					'fields' => array(
						'items' => array(
							'label'       => __( 'Export Logs', 'ub' ),
							'description' => __( 'Export all email log data to CSV file.', 'ub' ),
							'type'        => 'submit',
							'icon'        => 'download',
							'name'        => 'branda_email_log_export',
							'classes'     => array(
								'branda-export-email-logs',
							),
							'value'       => __( 'Export', 'ub' ),
						),
						'nonce' => array(
							'type'  => ' hidden',
							'name'  => 'email_log_export_nonce',
							'value' => $this->get_nonce_value( 'email_log_export' ),
						),
					),
				),
				'clear'          => array(
					'fields' => array(
						'items'  => array(
							'type'         => 'number',
							'display'      => 'sui-tab-content',
							'label'        => __( 'Log limit', 'ub' ),
							'description'  => array(
								'content'  => __( 'Set the limit to 0 if you would like to keep your entire log history.', 'ub' ),
								'position' => 'bottom',
							),
							'master'       => $this->get_name( 'clear_log' ),
							'master-value' => 'clear_log',
							'min'          => 0,
							'default'      => 100,
						),
						'border' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Clear log', 'ub' ),
							'description' => __( 'Set the limit, after which the history of logs will be cleared.', 'ub' ),
							'options'     => array(),
							'default'     => 'clear_log',
							'slave-class' => $this->get_name( 'clear_log' ),

						),
					),
				),
			);

			$this->options = $options;
		}

		/**
		 * Add SUI dialog
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function change_main_content( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}

			if ( ! Branda_Helper::is_full_pro() ) {
				$content = $this->upgrade_to_pro();
			} else {

				// Add settings dialog.
				$content = $this->get_settins_popup( $content );

				$total = Branda_Email_Logs_CPT::get_total_cpt();

				if ( $total ) {
					$content .= $this->email_logs_table();
					$content .= $this->get_dialog_delete( 'bulk' );
				} else {
					$content .= $this->no_log_history();
				}
			}

			return $content;
		}

		/**
		 * Handle form send
		 *
		 * @param bool $status Update status.
		 */
		public function update( $status ) {
			// Email Logs export.
			$branda_email_log_export = filter_input( INPUT_POST, 'branda_email_log_export' );
			if ( ! is_null( $branda_email_log_export ) ) {
				$uba = branda_get_uba_object();

				$nonce = filter_input( INPUT_POST, 'email_log_export_nonce' );
				if ( is_null( $nonce ) ) {
					die( $uba->messages['wrong'] );
				}

				$nonce_name = $this->get_nonce_action( 'email_log_export' );
				if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
					die( $uba->messages['security'] );
				}

				$this->email_log_export();
			}

			parent::update( $status );
		}

		/**
		 * Singular email log export
		 */
		public function singular_email_log_export() {
			// Email Logs export.
			$branda_email_log_export = filter_input( INPUT_GET, 'branda_email_log_export' );
			if ( ! is_null( $branda_email_log_export ) ) {
				$uba = branda_get_uba_object();

				$id    = filter_input( INPUT_POST, 'email_log_id', FILTER_VALIDATE_INT );
				$nonce = filter_input( INPUT_POST, 'branda_nonce' );
				if ( is_null( $id ) || is_null( $nonce ) ) {
					die( $uba->messages['wrong'] );
				}

				$nonce_name = $this->get_nonce_action( 'email_log_export' . $id );
				if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
					die( $uba->messages['security'] );
				}

				$this->email_log_export( $id );
			}
		}

		/**
		 * Email Logs export.
		 *
		 * @param int $id Post id. Export all posts if id isn't set.
		 */
		public function email_log_export( $id = 0 ) {
			$sitename = $this->get_site_name();
			$filename = sprintf(
				'%s.branda.%s.csv',
				$sitename,
				$id ? 'email-log-' . $id : 'email-logs.' . date( 'Y-m-d.H-i-s' )
			);

			self::set_csv_header( $filename );

			$fp      = fopen( 'php://memory', 'w' ); // phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fopen -- disable phpcs because it writes memory
			$entries = self::get_csv_data( $id );
			foreach ( $entries as $entry ) {
				$fields = $entry;
				fputcsv( $fp, $fields );
			}
			fseek( $fp, 0 );

			// Send the generated csv lines to the browser.
			fpassthru( $fp );
			exit();
		}

		/**
		 * Get data for CSV
		 *
		 * @param int $id Post id. Get all posts if id isn't set.
		 * @return array
		 */
		private static function get_csv_data( $id = 0 ) {
			$columns = self::get_default_columns();
			$data    = array( array_values( $columns ) ); // Header lebels.
			$args    = self::get_default_cpt_args();
			if ( $id ) {
				$args['id'] = $id;
			}
			$items = Branda_Email_Logs_CPT::get_items( $args );
			foreach ( $items as $item ) {
				$row = array();
				foreach ( $columns as $column_key => $column_name ) {
					$value = isset( $item[ $column_key ] ) && is_scalar( $item[ $column_key ] ) ? $item[ $column_key ] : '';
					$row[] = $value;
				}
				$data[] = $row;
			}

			return $data;
		}


		/**
		 * Set headers for CSV
		 *
		 * @param string $filename File name.
		 */
		private static function set_csv_header( $filename ) {

			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
			header( 'Expires: 0' );
			header( 'Pragma: public' );

			// print BOM Char for Excel Compatible.
			echo chr( 239 ) . chr( 187 ) . chr( 191 ); // wpcs xss ok. excel generated content.
		}

		/**
		 * Replace default by module related
		 */
		public function dialog_delete_attr_filter( $args, $module, $id ) {
			if ( $this->module === $module ) {
				$args['title']       = __( 'Delete log', 'ub' );
				$args['description'] = __( 'Are you sure you wish to permanently delete this log?', 'ub' );
				if ( 'bulk' === $id ) {
					$args['title']       = __( 'Delete logs', 'ub' );
					$args['description'] = __( 'Are you sure you wish to permanently delete these logs?', 'ub' );
				}
			}
			return $args;
		}

		/**
		 * AJAX: delete item
		 */
		public function ajax_delete() {
			$nonce_action = 0;
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			if ( $id ) {
				$nonce_action = $this->get_nonce_action( $id, 'delete' );
			}
			$this->check_input_data( $nonce_action, array( 'id' ) );
			Branda_Email_Logs_CPT::delete_post( $id );
			$message = array(
				'type'    => 'success',
				'message' => esc_html__( 'Email log was deleted.', 'ub' ),
			);
			$uba     = branda_get_uba_object();
			$uba->add_message( $message );
			wp_send_json_success();
		}

		/**
		 * AJAX: delete feed data (bulk)
		 */
		public function ajax_delete_bulk() {
			$this->check_input_data( $this->get_nonce_action( 'bulk', 'delete' ), array( 'ids' ) );
			$ids = filter_input( INPUT_POST, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( ! is_array( $ids ) ) {
				$this->json_error();
			} else {
				foreach ( $ids as $id ) {
					Branda_Email_Logs_CPT::delete_post( $id );
				}
				$uba     = branda_get_uba_object();
				$message = array(
					'type'    => 'success',
					'message' => esc_html__( 'Selected Email logs were deleted!', 'ub' ),
				);
				$uba->add_message( $message );
				wp_send_json_success();
			}
		}

		/**
		 * Get main page content
		 *
		 * @return type
		 */
		private function email_logs_table() {
			$main_args          = self::get_main_args();
			$all_filtered_items = Branda_Email_Logs_CPT::get_items( $main_args );
			$filter_top_args    = array_merge(
				$main_args,
				array(
					'is_bottom'      => false,
					'module'         => $this->module,
					'total'          => count( $all_filtered_items ),
					'order_by_array' => self::get_default_columns(),
					'order_array'    => array(
						'desc' => esc_html__( 'Descending', 'ub' ),
						'asc'  => esc_html__( 'Ascending', 'ub' ),
					),
				)
			);

			$filter_bottom_args = array_merge( $filter_top_args, array( 'is_bottom' => true ) );
			$items              = Branda_Email_Logs_CPT::items_slice( $all_filtered_items, $main_args );
			$filter_template    = '/admin/modules/email-logs/pagination-section';
			$args               = array(
				'items'              => $items,
				'columns'            => self::get_columns(),
				'filter_bar_top'     => $this->render( $filter_template, $filter_top_args, true ),
				'filter_bar_bottom'  => $this->render( $filter_template, $filter_bottom_args, true ),
				'export_form_action' => add_query_arg( array( 'branda_email_log_export' => true ) ),
				'module_object'      => $this,
			);
			$content            = $this->render( '/admin/modules/email-logs/main-list', $args, true );

			return $content;
		}

		/**
		 * Get arguments for main Email Log template
		 *
		 * @return array
		 */
		private static function get_main_args() {
			$args = self::get_default_cpt_args();

			$offset = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );
			if ( is_int( $offset ) ) {
				$args['offset'] = ( $offset - 1 ) * $args['limit'];
			}

			$order_by = filter_input( INPUT_GET, 'order_by' );
			$columns  = self::get_default_columns();
			if ( is_string( $order_by ) && in_array( $order_by, array_keys( $columns ), true ) ) {
				$args['order_by'] = $order_by;
			}

			$order = filter_input( INPUT_GET, 'order' );
			if ( is_string( $order ) && in_array( $order, array( 'desc', 'asc' ), true ) ) {
				$args['order'] = $order;
			}

			$keyword = filter_input( INPUT_GET, 'keyword' );
			if ( is_string( $keyword ) ) {
				$args['keyword'] = $keyword;
			}

			$date_range = filter_input( INPUT_GET, 'date_range' );
			if ( is_string( $date_range ) ) {
				preg_match( '/[\d]{2}\/[\d]{2}\/[\d]{4} - [\d]{2}\/[\d]{2}\/[\d]{4}/', $date_range, $matches );
				if ( ! empty( $matches[0] ) ) {
					$args['date_range'] = $matches[0];
				}
			}

			$from_email = filter_input( INPUT_GET, 'from_email' );
			if ( is_string( $from_email ) && is_email( $from_email ) ) {
				$args['from_email'] = $from_email;
			}

			$recipient = filter_input( INPUT_GET, 'recipient' );
			if ( is_string( $recipient ) && is_email( $recipient ) ) {
				$args['recipient'] = $recipient;
			}

			$args['is_filtered'] = self::is_filter_applies( $args );

			return $args;
		}

		/**
		 * Get default arguments for main Email Log template
		 *
		 * @return array
		 */
		private static function get_default_cpt_args() {
			$limit = apply_filters( 'branda_email_logs_pagination_limit', 10 );
			$args  = array(
				'order_by'    => 'date',
				'order'       => 'desc',
				'limit'       => $limit,
				'offset'      => 0,
				'keyword'     => '',
				'date_range'  => '',
				'from_email'  => '',
				'recipient'   => '',
				'is_filtered' => false,
			);

			return $args;
		}

		/**
		 * Get columns
		 *
		 * @return array
		 */
		private static function get_columns() {
			$columns        = array();
			$defalt_columns = self::get_default_columns();
			foreach ( $defalt_columns as $key => $title ) {
				$columns[ $key ]['title'] = $title;
			}

			return $columns;
		}

		/**
		 * Get default columns
		 *
		 * @return array
		 */
		private static function get_default_columns() {
			$defalt_columns = array(
				'from_name'  => __( 'From Name', 'ub' ),
				'from_email' => __( 'From Email', 'ub' ),
				'recipient'  => __( 'Recipient', 'ub' ),
				'date'       => __( 'Sent at', 'ub' ),
				'post_title' => __( 'Subject', 'ub' ),
			);

			return $defalt_columns;
		}

		/**
		 * Is filter applies?
		 *
		 * @param array $args Prepared arguments.
		 * @return boolean
		 */
		private static function is_filter_applies( $args ) {
			$is_filtered = false;
			if ( $args['keyword'] || $args['date_range'] || $args['from_email'] || $args['recipient'] ) {
				$is_filtered = true;
			}

			return $is_filtered;
		}

		/**
		 * Show template for no log history
		 *
		 * @return string
		 */
		private function no_log_history() {
			$template = '/admin/modules/email-logs/no-log-history';
			$content  = $this->render( $template, array(), true );

			return $content;
		}

		/**
		 * Show template for free version
		 *
		 * @return string
		 */
		private function upgrade_to_pro() {
			$args     = array(
				'utm_campaign' => 'branda_emaillogs_upgrade',
				'description'  => __( 'Get detailed information about your emails with Branda Pro. You can check recipients information and export all log history. Try it today with a WPMU DEV Membership!', 'ub' ),
			);
			$template = '/admin/common/modules/only-for-pro';
			$content  = $this->render( $template, $args, true );

			return $content;
		}

		/**
		 * Add notice if SMTP module is deactivated
		 *
		 * @return string
		 */
		protected static function maybe_add_smtp_notice() {
			$is_smtp_activated = branda_is_active_module( 'emails/smtp.php' );
			if ( ! $is_smtp_activated ) {
				$url     = add_query_arg(
					array(
						'page'   => 'branding_group_emails',
						'module' => 'smtp',
					),
					network_admin_url( 'admin.php' )
				);
				$notice  = sprintf( esc_html__( 'Enable %1$sthe SMTP module%2$s to start storing the email log history.', 'ub' ), '<a href="' . esc_url( $url ) . '">', '</a>' );
				$content = Branda_Helper::sui_notice( $notice, 'default' );
			} else {
				$content = '';
			}

			return $content;
		}

		/**
		 * Get settings popup HTML
		 *
		 * @param string $content Default content.
		 * @return string
		 */
		private function get_settins_popup( $content ) {
			$template = '/admin/modules/email-logs/dialogs/settings';
			$args     = array(
				'id'      => $this->get_name( 'settings' ),
				'nonce'   => $this->get_nonce_value( 'settings' ),
				'action'  => $this->get_name( 'settings' ),
				'content' => $content,
			);
			$popup    = $this->render( $template, $args, true );

			return $popup;
		}

	}
}
new Branda_Email_Logs();
