<?php
/**
 * Branda Email Headers class.
 *
 * @package Branda
 * @subpackage Emails
 */
if ( ! class_exists( 'Branda_Email_Headers' ) ) {

	/**
	 * Class Branda_Email_Headers
	 */
	class Branda_Email_Headers extends Branda_Helper {

		/**
		 * Module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_emails_headers';

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'emails-header';
			/**
			 * Register hooks.
			 */
			add_filter( 'ultimatebranding_settings_emails_header', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_emails_header_process', array( $this, 'update' ), 10, 1 );
			add_filter( 'wp_mail_from', array( $this, 'from_email' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'from_email_name' ) );
			add_action( 'init', array( $this, 'upgrade_options' ) );

			/**
			 * Add Reply To header
			 *
			 * @since 3.4
			 */
			add_action( 'phpmailer_init', array( $this, 'add_reply_to' ), 10, 1 );

			add_filter( 'branda_admin_messages_array', array( $this, 'add_messages' ) );
		}

		/**
		 * Add messages to js localize
		 */
		public function add_messages( $array ) {
			$array['messages']['email_headers'] = array(
				'invalid_email' => __( 'Invalid Email', 'ub' ),
			);
			return $array;
		}

		/**
		 * Upgrade module data to new structure.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$ub_from_email = branda_get_option( 'ub_from_email', false );
			$ub_from_name  = branda_get_option( 'ub_from_name', false );
			if (
				false === $ub_from_email
				&& false === $ub_from_name
			) {
				return;
			}
			$data = array(
				'headers' => array(
					'email' => $ub_from_email,
					'name'  => $ub_from_name,
				),
			);
			$this->update_value( $data );
			branda_delete_option( 'ub_from_email' );
			branda_delete_option( 'ub_from_name' );
		}

		/**
		 * Set module options for admin page.
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$options      = array(
				'headers'  => array(
					'title'       => __( 'Email From', 'ub' ),
					'description' => __( 'Choose the default sender email and sender name for all of your WordPress outgoing emails.', 'ub' ),
					'fields'      => array(
						'email' => array(
							'label' => __( 'Sender email address', 'ub' ),
							'type'  => 'email',
						),
						'name'  => array(
							'label' => __( 'Sender name', 'ub' ),
						),
					),
				),
				'reply-to' => array(
					'title'       => __( 'Reply to', 'ub' ),
					'description' => __( 'Choose whether you want to add as `Reply to` header.', 'ub' ),
					'fields'      => array(
						'email' => array(
							'label' => __( 'Reply to email address', 'ub' ),
							'type'  => 'email',
						),
						'name'  => array(
							'label'       => __( 'Reply to name', 'ub' ),
							'type'        => 'text',
							'description' => array(
								'content'  => __( 'Note: In order to add reply to name, you should first add value for reply to email address.', 'ub' ),
								'position' => 'bottom',
							),
							'disabled'    => true,
						),
					),
				),
			);
			$current_user = wp_get_current_user();
			if ( is_a( $current_user, 'WP_User' ) ) {
				$options['headers']['fields']['email']['placeholder']  = $current_user->user_email;
				$options['headers']['fields']['name']['placeholder']   = $current_user->display_name;
				$options['reply-to']['fields']['email']['placeholder'] = sprintf( __( 'e. g. %s', 'ub' ), $current_user->user_email );
				$options['reply-to']['fields']['name']['placeholder']  = sprintf( __( 'e. g. %s', 'ub' ), $current_user->display_name );
			}
			$this->options = $options;
		}

		/**
		 * Change email from address.
		 *
		 * @param string $email From email.
		 *
		 * @return mixed|null|string
		 */
		public function from_email( $email ) {
			$value = $this->get_value( 'headers', 'email' );
			if ( is_email( $value ) ) {
				return $value;
			}
			return $email;
		}

		/**
		 * Change email from name.
		 *
		 * @param string $from From name.
		 *
		 * @return mixed|null|string
		 */
		public function from_email_name( $from ) {
			$value = $this->get_value( 'headers', 'name' );
			if ( ! empty( $value ) ) {
				return $value;
			}
			return $from;
		}

		/**
		 * Set Reply To email header
		 *
		 * @since 3.4
		 */
		public function add_reply_to( $phpmailer ) {
			$reply_to_email = $this->get_value( 'reply-to', 'email' );
			if ( ! empty( $reply_to_email ) && is_email( $reply_to_email ) ) {
				$reply_to_name = $this->get_value( 'reply-to', 'name', '' );
				$phpmailer->addReplyTo( $reply_to_email, $reply_to_name );
			}
		}
	}
}
new Branda_Email_Headers();
