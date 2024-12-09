<?php
/**
 * Branda Registration Emails class.
 *
 * @package Branda
 * @subpackage Emails
 */
if ( ! class_exists( 'Branda_Registration_Emails' ) ) {

	class Branda_Registration_Emails  extends Branda_Helper {

		protected $option_name = 'ub_registration_emails';
		private $blog_title;

		public function __construct() {
			parent::__construct();
			$this->module = 'registration-emails';
			add_filter( 'ultimatebranding_settings_registration_emails', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_registration_emails_process', array( $this, 'update' ) );
			/**
			 * replace
			 */
			/** Those filters are documented in wp-includes/ms-functions.php */
			add_filter( 'wpmu_signup_blog_notification_email', array( $this, 'blog_signup_email' ) );
			add_filter( 'wpmu_signup_blog_notification_subject', array( $this, 'blog_signup_subject' ) );
			add_filter( 'wpmu_signup_user_notification_email', array( $this, 'user_signup_email' ) );
			add_filter( 'wpmu_signup_user_notification_subject', array( $this, 'user_signup_subject' ) );
			/**
			 * Notify a user that their blog activation has been successful.
			 */
			add_filter( 'update_welcome_email', array( $this, 'welcome_email' ), 11, 6 );
			add_filter( 'update_welcome_subject', array( $this, 'welcome_subject' ), 11 );
			/**
			 * AJAX axtions
			 */
			add_action( 'wp_ajax_branda_registration_emails_reset', array( $this, 'ajax_section_reset' ) );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
		}

		/**
		 * Upgrade option
		 *
		 * @since 2.1.0
		 */
		public function upgrade_options() {
			$value = branda_get_option( 'global_ms_register_mails' );
			if ( empty( $value ) ) {
				return;
			}
			branda_delete_option( 'global_ms_register_mails' );
			$this->update_value( $value );
		}

		/**
		 * modify option name
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && 'registration-emails' == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		/**
		 * Blog Signup Email Body
		 */
		public function blog_signup_email( $value ) {
			return $this->filter( $value, 'wpmu_signup_blog_notification', 'message' );
		}

		/**
		 * Blog Signup Email Subject
		 */
		public function blog_signup_subject( $value ) {
			return $this->filter( $value, 'wpmu_signup_blog_notification', 'title' );
		}

		/**
		 * User Signup Email Body
		 */
		public function user_signup_email( $value ) {
			return $this->filter( $value, 'wpmu_signup_user_notification', 'message' );
		}

		/**
		 * User Signup Email Subject
		 */
		public function user_signup_subject( $value ) {
			return $this->filter( $value, 'wpmu_signup_user_notification', 'title' );
		}

		/**
		 * User welcome Email Body
		 *
		 * @since 3.0.0
		 */
		public function welcome_email( $welcome_email, $blog_id, $user_id, $password, $title, $meta ) {
			$this->blog_title = $title;
			$welcome_email    = $this->filter( $welcome_email, 'wpmu_welcome_notification', 'message' );
			$current_network  = get_network();
			$user             = get_userdata( $user_id );
			$url              = get_blogaddress_by_id( $blog_id );
			$welcome_email    = str_replace( 'SITE_NAME', $current_network->site_name, $welcome_email );
			$welcome_email    = str_replace( 'BLOG_TITLE', $title, $welcome_email );
			$welcome_email    = str_replace( 'BLOG_URL', $url, $welcome_email );
			$welcome_email    = str_replace( 'USERNAME', $user->user_login, $welcome_email );
			$welcome_email    = str_replace( 'PASSWORD', $password, $welcome_email );
			return $welcome_email;
		}

		/**
		 * User welcome Email Subject
		 *
		 * @since 3.0.0
		 */
		public function welcome_subject( $subject ) {
			$current_network = get_network();
			$subject         = sprintf(
				$this->filter( $subject, 'wpmu_welcome_notification', 'title' ),
				$current_network->site_name,
				wp_unslash( $this->blog_title )
			);
			return $subject;
		}

		/**
		 * Chage value helper
		 *
		 * @since 3.0.0
		 *
		 * @param string $value Value to filter
		 * @param string $name Section name.
		 * @param string $key Section key.
		 *
		 * @return string $value Value after filter.
		 */
		private function filter( $value, $name, $key ) {
			$this->set_data();
			if ( 'on' == $this->get_value( $name, 'status', 'off' ) ) {
				$value = $this->get_value( $name, $key );
			}
			return $value;
		}

		protected function set_options() {
			$new_blog_message    = __( "To activate your blog, please click the following link:\n\n%1\$s\n\nAfter you activate, you will receive *another email* with your login.\n\nAfter you activate, you can visit your site here:\n\n%2\$s", 'ub' );
			$new_blog_title      = _x( '[%1$s] Activate %2$s', 'New site notification email subject', 'ub' );
			$new_sign_up_message = __( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.", 'ub' );
			$new_sign_up_title   = _x( '[%1$s] Activate %2$s', 'New user notification email subject', 'ub' );
			$welcome_email       = __(
				'Howdy USERNAME,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:

Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME',
				'ub'
			);
			$options             = array(
				'wpmu_signup_blog_notification' => array(
					'title'       => __( 'New Blog', 'ub' ),
					'description' => __( 'Send a customized copy whenever a new blog is published.', 'ub' ),
					'fields'      => array(
						'status'  => array(
							'type'           => 'checkbox',
							'checkbox_label' => __( 'Customize new blog notification email', 'ub' ),
							'options'        => array(
								'on'  => __( 'Yes', 'ub' ),
								'off' => __( 'No', 'ub' ),
							),
							'default'        => 'off',
							'classes'        => array( 'switch-button' ),
							'slave-class'    => 'wpmu_signup_blog_notification',
						),
						'title'   => array(
							'type'    => 'text',
							'label'   => __( 'Subject', 'ub' ),
							'master'  => 'wpmu_signup_blog_notification',
							'default' => $new_blog_title,
							'group'   => array(
								'begin'   => true,
								'classes' => array( 'sui-border-frame' ),
							),
						),
						'message' => array(
							'type'    => 'textarea',
							'label'   => __( 'Email Body', 'ub' ),
							'master'  => 'wpmu_signup_blog_notification',
							'default' => $new_blog_message,
						),
						'reset'   => $this->get_button_reset_array( 'wpmu_signup_blog_notification' ),
					),
				),
				'wpmu_signup_user_notification' => array(
					'title'       => __( 'User Sign-up', 'ub' ),
					'description' => __( 'Send a customized copy whenever a user sign up on your network.', 'ub' ),
					'fields'      => array(
						'status'  => array(
							'type'           => 'checkbox',
							'checkbox_label' => __( 'Customize new user sign-up email', 'ub' ),
							'options'        => array(
								'on'  => __( 'Yes', 'ub' ),
								'off' => __( 'No', 'ub' ),
							),
							'default'        => 'off',
							'classes'        => array( 'switch-button' ),
							'slave-class'    => 'wpmu_signup_user_notification',
						),
						'title'   => array(
							'type'    => 'text',
							'label'   => __( 'Subject', 'ub' ),
							'master'  => 'wpmu_signup_user_notification',
							'default' => $new_sign_up_title,
							'group'   => array(
								'begin'   => true,
								'classes' => array( 'sui-border-frame' ),
							),
						),
						'message' => array(
							'type'    => 'textarea',
							'label'   => __( 'Email Body', 'ub' ),
							'master'  => 'wpmu_signup_user_notification',
							'default' => $new_sign_up_message,
						),
						'reset'   => $this->get_button_reset_array( 'wpmu_signup_user_notification' ),
					),
				),
				'wpmu_welcome_notification'     => array(
					'title'       => __( 'Site Activation', 'ub' ),
					'description' => __( 'Send a customized copy whenever a new site is registered on your network.', 'ub' ),
					'fields'      => array(
						'status'  => array(
							'type'           => 'checkbox',
							'checkbox_label' => __( 'Customize new site activation email', 'ub' ),
							'options'        => array(
								'on'  => __( 'Yes', 'ub' ),
								'off' => __( 'No', 'ub' ),
							),
							'default'        => 'off',
							'classes'        => array( 'switch-button' ),
							'slave-class'    => 'wpmu_welcome_notification',
						),
						'title'   => array(
							'type'    => 'text',
							'label'   => __( 'Subject', 'ub' ),
							'master'  => 'wpmu_welcome_notification',
							'default' => __( 'New %1$s Site: %2$s', 'ub' ),
							'group'   => array(
								'begin'   => true,
								'classes' => array( 'sui-border-frame' ),
							),
						),
						'message' => array(
							'type'    => 'textarea',
							'label'   => __( 'Email Body', 'ub' ),
							'master'  => 'wpmu_welcome_notification',
							'default' => $welcome_email,
						),
						'reset'   => $this->get_button_reset_array( 'wpmu_welcome_notification' ),
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
					$url    = network_admin_url( 'settings.php' );
					$notice = array(
						'type'  => 'description',
						'value' => Branda_Helper::sui_notice(
							sprintf(
								__( 'Blog registration has been disabled. Click <a href="%s">here</a> to enable the site registration for your network.', 'ub' ),
								$url
							)
						),
					);
					$options['wpmu_signup_blog_notification']['fields'] = array( 'notice' => $notice );
				}
			}
			/**
			 * set users registration
			 */
			$options       = $this->set_users_can_register( $options );
			$this->options = $options;
		}

		/**
		 * Common reset button
		 *
		 * @since 3.0.0
		 */
		private function get_button_reset_array( $id ) {
			$args                   = $this->get_options_fields_reset( $id );
			$args['reset']['group'] = array(
				'end' => true,
			);
			return $args['reset'];
		}

		/**
		 * Reset section
		 *
		 * @since 3.0.0
		 */
		public function ajax_section_reset() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id, 'reset' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$keys = array(
				'new'  => 'wpmu_signup_blog_notification',
				'site' => 'wpmu_welcome_notification',
				'user' => 'wpmu_signup_user_notification',
			);
			if ( isset( $keys[ $id ] ) ) {
				$this->delete_value( $keys[ $id ] );
				$uba     = branda_get_uba_object();
				$message = array(
					'type'    => 'success',
					'message' => sprintf( 'Selected section was reset.', 'ub' ),
				);
				$uba->add_message( $message );
				wp_send_json_success();
			}
			$this->json_error();
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
			$data['wpmu_signup_user_notification']['fields'] = array( 'notice' => $this->get_users_can_register_notice() );
			return $data;
		}
	}
}
new Branda_Registration_Emails();
