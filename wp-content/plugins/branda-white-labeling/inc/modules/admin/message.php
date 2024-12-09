<?php
/**
 * Branda Administrator Message class.
 *
 * @package Branda
 * @subpackage AdminArea
 */
if ( ! class_exists( 'Branda_Admin_Message' ) ) {
	class Branda_Admin_Message extends Branda_Helper {

		protected $option_name = 'ub_admin_message';

		protected $esc_callback = array( __CLASS__, 'esc_data' );

		public function __construct() {
			parent::__construct();
			$this->module = 'admin-message';
			/**
			 * UB admin actions
			 */
			add_filter( 'ultimatebranding_settings_admin_message', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_admin_message_process', array( $this, 'update' ) );
			/**
			 * Render module's output for admin pages
			 */
			add_action( 'admin_notices', array( $this, 'output' ) );
			/**
			 * Render module's output for network admin pages
			 */
			add_action( 'network_admin_notices', array( $this, 'output' ) );
			/**
			 * upgrade option
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * css output
			 */
			add_action( 'admin_head', array( $this, 'css' ) );
		}

		/**
		 * set options
		 *
		 * @since 2.2.0
		 */
		protected function set_options() {
			$this->options = array(
				'admin' => array(
					'title'       => __( 'Message', 'ub' ),
					'description' => __( 'This message will appear on top of every admin page. You can use this to show notifications or important announcements.', 'ub' ),
					'fields'      => array(
						'message' => array(
							'type'        => 'wp_editor',
							'hide-th'     => true,
							'placeholder' => esc_html__( 'Add the admin message here…', 'ub' ),
						),
					),
				),
			);
		}

		/**
		 * Upgrade option
		 *
		 * @since 2.2.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			if ( ! empty( $value ) && ! is_array( $value ) ) {
				$data = array(
					'admin' => array(
						'message' => $value,
					),
				);
				$this->update_value( $data );
			}
			/**
			 * Change option name
			 *
			 * @since 3.0.0
			 */
			$old_name = 'admin_message';
			$value    = branda_get_option( $old_name );
			if ( ! empty( $value ) ) {
				$this->update_value( $value );
				branda_delete_option( $old_name );
			}
		}

		/**
		 * Renders the admin message
		 *
		 * @since 1.8
		 */
		public function output() {
			$message         = $this->get_message();
			$trimmed_message = ! empty( $message ) ? trim( str_replace( array( '<p>', '</p>', '&nbsp;' ), '', $message ) ) : $message;

			if ( empty( $trimmed_message ) ) {
				return;
			}
			$message = stripslashes( $message );
			$message = wpautop( $message );
			$args    = array(
				'message' => $message,
			);
			$temlate = $this->get_template_name( 'message' );
			$this->render( $temlate, $args );
		}

		/**
		 * Print CSS if there is some message.
		 *
		 * @since 3.0.6
		 */
		public function css() {
			$message = $this->get_message();
			if ( empty( $message ) ) {
				return;
			}
			$args    = array(
				'id' => $this->get_name( 'css' ),
			);
			$temlate = $this->get_template_name( 'css' );
			$this->render( $temlate, $args );
		}

		/**
		 * Get content common function (DRY).
		 *
		 * @since 3.0.6
		 */
		private function get_message() {
			$value = $this->get_value( 'admin', 'message_meta' );
			if ( ! empty( $value ) ) {
				return $value;
			}
			return $this->get_value( 'admin', 'message' );
		}

		/**
		 * Escape module fields.
		 *
		 * @param mixed|array|string $data
		 * @return string
		 */
		public static function esc_data( $data ) {
			if ( empty( $data['key'] ) || empty( $data['value'] ) ) {
				return $data;
			}

			return self::kses_body_markup( $data['value'] );
		}
	}
}
new Branda_Admin_Message();
