<?php
/**
 * Branda SMTP class.
 *
 * @package Branda
 * @subpackage Emails
 */
if ( ! class_exists( 'Branda_SMTP' ) ) {

	class Branda_SMTP extends Branda_Helper {
		protected $option_name = 'ub_smtp';
		private $is_ready      = false;

		/**
		 * Conflicted plugins list
		 *
		 * @since 3.1.0
		 */
		private $plugins_list = array();

		/**
		 * Escape callback function to be called in parent class's `esc_deep()` method.
		 * `esc_html` or `wp_kses_post` will break passwords with special characters (like &).
		 *
		 * @since 3.4.9.1
		 */
		protected $esc_callback = array( __CLASS__, 'esc_data' );

		/**
		 * The option name for encryption key.
		 *
		 * @var string
		 */
		protected $encryption_key_option = 'wpmudev_branda_smtp_encryption_key';

		public function __construct() {
			parent::__construct();
			$this->check();
			$this->module = 'smtp';
			/**
			 * hooks
			 */
			if ( $this->is_network ) {
				add_action( 'network_admin_notices', array( $this, 'configure_credentials_notice' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'configure_credentials_notice' ) );
			}
			add_action( 'phpmailer_init', array( $this, 'init_smtp' ), 999 );
			add_filter( 'ultimatebranding_settings_smtp', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_smtp_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_smtp_reset', array( $this, 'reset_module' ) );
			add_filter( 'ultimatebranding_settings_smtp_preserve', array( $this, 'preserve' ) );
			add_filter( 'branda_sanitize_input_by_type', array( $this, 'sanitize_input' ), 10, 7 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_' . $this->get_name( 'send' ), array( $this, 'ajax_send_test_email' ) );
			/**
			 * @since 3.1.0
			 */
			add_action( 'wp_ajax_' . $this->get_name( 'deactivate' ), array( $this, 'ajax_deactivate_coflicted_plugin' ) );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add "Send Test Email" button.
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_settings_after_box_title', array( $this, 'add_button_after_title' ), 10, 2 );
			/**
			 * Add dialog
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * add to javascript messages
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_admin_messages_array', array( $this, 'add_messages' ) );
		}

		/**
		 * Add messages to js localize
		 */
		public function add_messages( $array ) {
			$array['messages']['smtp'] = array(
				'empty'   => __( 'Field "To" can not be empty!', 'ub' ),
				'sending' => __( 'Sending message, please wait...', 'ub' ),
				'send'    => __( 'The test message was send successful.', 'ub' ),
			);
			return $array;
		}

		/**
		 * Upgrade option
		 *
		 * @since 2.1.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			if ( empty( $value ) || ! is_array( $value ) || ! isset( $value['settings'] ) ) {
				return;
			}
			$data = array(
				'header'              => array(
					'from_email'      => '',
					'from_name_force' => 'on',
					'from_name'       => '',
				),
				'server'              => array(
					'smtp_host'            => '',
					'smtp_type_encryption' => 'ssl',
					'smtp_port'            => '25',
					'smtp_insecure_ssl'    => 'on',
				),
				'smtp_authentication' => array(
					'smtp_authentication' => 'on',
					'smtp_username'       => '',
					'smtp_password'       => '',
				),
			);
			foreach ( $data as $g => $keys ) {
				foreach ( $keys as $k => $v ) {
					if ( isset( $value['settings'][ $k ] ) ) {
						$data[ $g ][ $k ] = $value['settings'][ $k ];
					}
				}
			}

			$this->update_value( $data );
		}

		/**
		 * Add "add feed" button.
		 *
		 * @since 3.0.0
		 */
		public function add_button_after_title( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$args = array(
				'data' => array(
					'modal-open' => $this->get_name( 'send' ),
				),
				'text' => __( 'Send Test Email', 'ub' ),
				'sui'  => 'ghost',
			);
			if ( is_wp_error( $this->is_ready ) ) {
				$args['disabled'] = true;
			}
			$content .= '<div class="sui-actions-left">';
			$content .= $this->button( $args );
			$content .= '</div>';
			return $content;
		}

		/**
		 * Send test email
		 *
		 * @since 2.0.0
		 */
		public function ajax_send_test_email() {
			global $wp_version;
			$nonce_action = $this->get_nonce_action( 'send' );
			$this->check_input_data( $nonce_action, array( 'email' ) );
			if ( is_wp_error( $this->is_ready ) ) {
				$this->json_error( $this->is_ready->get_error_message() );
			}
			//$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_STRING );
			$email = sanitize_email( $_POST['email'] );
			if ( ! is_email( $email ) ) {
				$this->json_error( __( 'Unable to send: wrong email address.', 'ub' ) );
			}
			$errors = '';
			$config = $this->get_value();
			if ( version_compare( $wp_version, '5.5', '<' ) ) {
				require_once ABSPATH . WPINC . '/class-phpmailer.php';
				$mail          = new PHPMailer();
			} else {
				require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
				require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
				require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
				$mail          = new PHPMailer\PHPMailer\PHPMailer();
			}
			$charset       = get_bloginfo( 'charset' );
			$mail->CharSet = $charset;
			$from_email    = $this->get_value( 'header', 'from_email', null, false );
			$mail->IsSMTP();
			// send plain text test email
			$mail->ContentType = 'text/plain';
			$mail->IsHTML( false );
			/* If using smtp auth, set the username & password */
			$use_auth = $this->get_value( 'smtp_authentication', 'smtp_authentication', null, false );
			if ( 'on' === $use_auth ) {
				$mail->SMTPAuth = true;
				$mail->Username = $this->get_value( 'smtp_authentication', 'smtp_username', null, false );
				$mail->Password = $this->decrypt( $this->get_value( 'smtp_authentication', 'smtp_password', null, false ) );
			}

			$force     = $this->get_value( 'header', 'from_name_force', null, false );
			$from_name = $this->get_value( 'header', 'from_name', null, false );

			if ( 'on' === $force && ! empty( $from_name ) ) {
				$mail->FromName = $from_name;
			}

			/* Set the SMTPSecure value, if set to none, leave this blank */
			$type = $this->get_value( 'server', 'smtp_type_encryption', null, false );
			if ( 'none' !== $type ) {
				$mail->SMTPSecure = $type;
			}
			/* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
			$mail->SMTPAutoTLS = false;
			$insecure_ssl      = $this->get_value( 'server', 'smtp_insecure_ssl', null, false );
			if ( 'on' === $insecure_ssl ) {
				// Insecure SSL option enabled
				$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					),
				);
			}
			/* Set the other options */
			$mail->Host = $this->get_value( 'server', 'smtp_host', null, false );
			$mail->Port = $this->get_value( 'server', 'smtp_port', null, false );
			$mail->SetFrom( $from_email, $mail->FromName );

			// Set Reply To header
			$reply_to_email = $this->get_value( 'reply-to', 'email', null, false );
			if ( ! empty( $reply_to_email ) && is_email( $reply_to_email ) ) {
				$reply_to_name = $this->get_value( 'reply-to', 'name', '', false );
				$mail->addReplyTo( $reply_to_email, $reply_to_name );
			}
			$mail->Subject = sprintf( __( 'This is test email sent from "%s"', 'ub' ), get_bloginfo( 'name' ) );
			$mail->Body    = __( 'This is a test mail...', 'ub' );
			$mail->Body   .= PHP_EOL;
			$mail->Body   .= PHP_EOL;
			$mail->Body   .= sprintf( __( 'Send date: %s.', 'ub' ), date( 'c' ) );
			$mail->Body   .= PHP_EOL;
			$mail->Body   .= PHP_EOL;
			$mail->Body   .= '-- ';
			$mail->Body   .= PHP_EOL;
			$mail->Body   .= sprintf( __( 'Site: %s.', 'ub' ), get_bloginfo( 'url' ) );
			$mail->AddAddress( $email );
			if ( Branda_Helper::is_debug() ) {
				$mail->SMTPDebug = 1;
				ob_start();
			}
			/* Send mail and return result */
			if ( ! $mail->Send() ) {
				$errors = $mail->ErrorInfo;
			}
			if ( Branda_Helper::is_debug() ) {
				$debug = ob_get_contents();
				ob_end_clean();
				error_log( $debug );
			}
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
			if ( ! empty( $errors ) ) {
				$data = array(
					'message' => __( 'Hey! Failed to send the test email. Please check your SMTP credentials and try again.', 'ub' ),
					'errors'  => $errors,
				);
				wp_send_json_error( $data );
			}
			$success_message = sprintf( __( 'Test email sent to %s.', 'ub' ), '<strong>' . $email . '</strong>' );
			wp_send_json_success( array( 'message' => $success_message ) );
		}

		/**
		 * Check required credentials
		 *
		 * @since 2.0.0
		 */
		private function check() {
			if (
				isset( $_POST['simple_options'] )
				&& isset( $_POST['simple_options']['server'] )
				&& isset( $_POST['simple_options']['server']['smtp_host'] )
				&& ! empty( $_POST['simple_options']['server']['smtp_host'] )
			) {
				$this->is_ready = true;
				return $this->is_ready;
			}
			$this->is_ready = new WP_Error( 'credentials', __( 'Please configure credentials first.', 'ub' ) );
			$config         = $this->get_value();
			if ( empty( $config ) ) {
				return $this->is_ready;
			}
			if ( ! isset( $config['header'] ) ) {
				return $this->is_ready;
			}
			$config = $this->get_value( 'server', 'smtp_host', false );
			if ( empty( $config ) ) {
				return $this->is_ready;
			}
			$this->is_ready = true;
			return $this->is_ready;
		}

		/**
		 * Init SMTP
		 *
		 * @since 2.0.0
		 */
		public function init_smtp( &$phpmailer ) {
			/**
			 * check if SMTP credentials have been configured.
			 */
			if ( is_wp_error( $this->is_ready ) ) {
				return $this->is_ready->get_error_message();
			}
			/* Set the mailer type as per config above, this overrides the already called isMail method */
			$phpmailer->IsSMTP();
			/**
			 * from name
			 */
			$from_name = $this->get_value( 'header', 'from_name', null, false );
			$force     = $this->get_value( 'header', 'from_name_force', null, false );

			if ( 'on' === $force && ! empty( $from_name ) ) {
				$phpmailer->FromName = $from_name;
			}

			/**
			 * from email
			 */
			$from_email = $this->get_value( 'header', 'from_email' );
			/**
			 * set PHPMailer
			 */
			if ( ! empty( $from_email ) ) {
				$phpmailer->From = $from_email;
			}

			$phpmailer->SetFrom( $phpmailer->From, $phpmailer->FromName );
			/* Set the SMTPSecure value */
			$type = $this->get_value( 'server', 'smtp_type_encryption' );
			if ( 'none' !== $type ) {
				$phpmailer->SMTPSecure = $type;
			}
			/* Set the other options */
			$phpmailer->Host = $this->get_value( 'server', 'smtp_host' );
			$phpmailer->Port = $this->get_value( 'server', 'smtp_port' );
			/* If we're using smtp auth, set the username & password */
			$use_auth = $this->get_value( 'smtp_authentication', 'smtp_authentication' );
			if ( 'on' === $use_auth ) {
				$phpmailer->SMTPAuth = true;
				$phpmailer->Username = $this->get_value( 'smtp_authentication', 'smtp_username', null, false );
				$phpmailer->Password = $this->decrypt( $this->get_value( 'smtp_authentication', 'smtp_password', null, false ) );
			}
			// PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
			$phpmailer->SMTPAutoTLS = false;
			/* Set the SMTPSecure value, if set to none, leave this blank */
			$insecure_ssl = $this->get_value( 'server', 'smtp_insecure_ssl' );
			if ( 'on' === $insecure_ssl ) {
				$phpmailer->SMTPOptions = array(
					'ssl' => array(
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					),
				);
			}

			// Save Email history if Email Logs module is enabled.
			if ( branda_is_active_module( 'emails/email-logs.php' ) && class_exists( 'Branda_Email_Logs_CPT' ) && method_exists( 'Branda_Email_Logs_CPT', 'save_email_history' ) ) {
				$phpmailer->action_function = function ( ...$args ) use ( $phpmailer ) {
					call_user_func( array( 'Branda_Email_Logs_CPT', 'save_email_history' ), $phpmailer, ...$args );
				};
			}
		}

		/**
		 * modify option name
		 *
		 * @since 2.0.0
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
			//We have option for `raw`
			$options = array(
				'reset-module'        => true,
				'plugins'             => array(),
				'header'              => array(
					'title'       => __( 'From Headers', 'ub' ),
					'description' => __( 'Choose the default from email id and from name for all of your WordPress outgoing emails.', 'ub' ),
					'fields'      => array(
						'from_email'      => array(
							'label'       => __( 'Sender email address', 'ub' ),
							'description' => __( 'You can specify the email address that emails should be sent from.', 'ub' ),
							'default'     => get_bloginfo( 'admin_email' ),
						),
						'from_name'       => array(
							'label'        => __( 'Sender name', 'ub' ),
							'placeholder'  => esc_attr__( 'Enter the sender name', 'ub' ),
							'description'  => array(
								'content'  => __( 'For example, you can use your website’s title as the default sender name.', 'ub' ),
								'position' => 'bottom',
							),
							'master'       => 'from-name',
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'from_name_force' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'From name replacement', 'ub' ),
							'description' => __( 'Set your own from name for each email sent from your website. Be carefully since it will override the from name provided by other plugins such as Contact Form.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Enable', 'ub' ),
								'off' => __( 'Disable', 'ub' ),
							),
							'default'     => 'on',
							'slave-class' => 'from-name',
						),
					),
				),
				'server'              => array(
					'title'       => __( 'SMTP Server', 'ub' ),
					'description' => __( 'Choose the SMTP server options such as host, port details, encryption etc.', 'ub' ),
					'fields'      => array(
						'smtp_host'            => array(
							'label'       => __( 'Host', 'ub' ),
							'description' => __( 'Enter the host name of your mail server.', 'ub' ),
							'placeholder' => esc_attr__( 'E.g. smtp.example.com', 'ub' ),
						),
						'smtp_type_encryption' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Encryption', 'ub' ),
							'options'     => array(
								'none' => __( 'None', 'ub' ),
								'ssl'  => __( 'SSL', 'ub' ),
								'tls'  => __( 'TLS', 'ub' ),
							),
							'default'     => 'ssl',
							'description' => __( 'Choose the encryption for your mail server. For most servers, SSL is recommended.', 'ub' ),
						),
						'smtp_port'            => array(
							'type'        => 'number',
							'label'       => __( 'Port', 'ub' ),
							'description' => __( 'Choose the SMTP port as recommended by your mail server.', 'ub' ),
							'default'     => 25,
							'min'         => 1,
						),
						'smtp_insecure_ssl'    => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Insecure SSL certificates', 'ub' ),
							'description' => __( 'You can enable the insecure and self-signed SSL certificates on SMTP server. However, it\'s highly recommended to keep this option disabled.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Enable', 'ub' ),
								'off' => __( 'Disable', 'ub' ),
							),
							'default'     => 'off',
						),
					),
				),
				'smtp_authentication' => array(
					'title'       => __( 'SMTP Authentication', 'ub' ),
					'description' => __( 'Choose whether you want to use SMTPAuth or not. It is recommended to keep this enabled.', 'ub' ),
					'fields'      => array(
						'smtp_username'       => array(
							'label'        => __( 'Username', 'ub' ),
							'placeholder'  => esc_attr__( 'Enter your SMTP username here', 'ub' ),
							'master'       => 'smtp-authentication',
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'smtp_password'       => array(
							'type'                            => 'password',
							'label'                           => __( 'Password', 'ub' ),
							'placeholder'                     => esc_attr__( 'Enter your SMTP password here', 'ub' ),
							'master'                          => 'smtp-authentication',
							'master-value'                    => 'on',
							'display'                         => 'sui-tab-content',
							'class'                           => 'large-text',
							'field_protection'                => true,
							'field_protection_show_message'   => esc_attr__( 'Set new SMTP password', 'ub' ),
							'field_protection_cancel_message' => esc_attr__( 'Cancel', 'ub' ),
						),
						'smtp_authentication' => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'on'  => __( 'Enable', 'ub' ),
								'off' => __( 'Disable', 'ub' ),
							),
							'default'     => 'on',
							'slave-class' => 'smtp-authentication',
						),
						'encryption_method' => array(
							'type'        => 'hidden',
							'default'     => '',
						),
					),
				),
			);
			/**
			 * check other SMTP plugin, only on admin page
			 *
			 * @since 3.1.0
			 */
			if ( is_admin() ) {
				$this->check_plugins();
				if ( ! empty( $this->plugins_list ) ) {
					$options['plugins'] = array(
						'title'       => __( 'Conflicted Plugins', 'ub' ),
						'description' => __( 'Branda has detected the following plugins are activated. Please deactivate them to prevent conflicts.', 'ub' ),
						'fields'      => array(
							'message' => array(
								'type'  => 'description',
								'value' => Branda_Helper::sui_notice( esc_html__( 'Branda has detected the following plugins are activated. Please deactivate them to prevent conflicts.', 'ub' ) ),
							),
							'plugins' => array(
								'type'     => 'callback',
								'callback' => array( $this, 'get_list_of_active_plugins' ),
							),
						),
					);
				}
			}
			$this->options = $options;
		}

		/**
		 * Add admin notice about configuration.
		 *
		 * @since 2.0.0
		 */
		public function configure_credentials_notice() {
			// Just checking the page so no need for nonce verification.
			// phpcs:ignore WordPress.Security.NonceVerification
			if ( ! $this->can_encrypt() && 'on' === $this->get_value( 'smtp_authentication', 'smtp_authentication', null, false ) && 'branding_group_emails' === $_GET['page'] ) {
				$message      = array(
					'can_dismiss' => true,
					'message'     => __( 'The SMTP password cannot be encrypted when stored in the database, possibly due to a missing or outdated <a href="https://www.php.net/manual/en/sodium.installation.php" target="_blank">Sodium library</a>.', 'ub' ),
				);
				$this->uba->add_message( $message );
			}

			if ( true === $this->is_ready ) {
				return;
			}
			if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
				return;
			}
			/**
			 * Only show in Branda plugin
			 */
			if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'branding' ) === false ) {
				return;
			}
			$module_data  = $this->uba->get_module_by_module( $this->module );
			$settings_url = add_query_arg(
				array(
					'page'   => 'branding_group_' . $module_data['group'],
					'module' => $this->module,
				),
				network_admin_url( 'admin.php' )
			);
			$message      = array(
				'can_dismiss' => true,
				'message'     => sprintf(
					__( 'Please configure your <a href="%s">SMTP credentials</a> in order to send email using SMTP module.', 'ub' ),
					esc_url( $settings_url )
				),
			);
			$this->uba->add_message( $message );
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$template = '/admin/common/dialogs/test-email';
			$args     = array(
				'id'          => $this->get_name( 'send' ),
				'description' => __( 'Send a dummy email to test the SMTP configurations.', 'ub' ),
				'nonce'       => $this->get_nonce_value( 'send' ),
				'action'      => $this->get_name( 'send' ),
			);
			$content .= $this->render( $template, $args, true );
			/**
			 * reset module
			 */
			$template = '/admin/common/dialogs/reset-module';
			$title    = __( 'Unknown', 'ub' );
			if ( isset( $module['name_alt'] ) ) {
				$title = $module['name_alt'];
			} elseif ( isset( $module['name'] ) ) {
				$title = $module['name'];
			}
			$args     = array(
				'module' => $this->module,
				'title'  => $title,
				'nonce'  => wp_create_nonce( 'reset-module-' . $this->module ),
			);
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		private function check_plugins() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$list = array(
				'wp-smtp/wp-smtp.php'                     => array(
					'name'  => 'WP SMTP',
					'class' => 'Branda_SMTP_Importer_WP_SMTP',
				),
				'wp-mail-smtp/wp_mail_smtp.php'           => array(
					'name'  => 'WP Mail SMTP by WPForms',
					'class' => 'Branda_SMTP_Importer_WP_Mail_SMTP',
				),
				'post-smtp/postman-smtp.php'              => array(
					'name' => 'Post SMTP Mailer/Email Log',
				),
				'easy-wp-smtp/easy-wp-smtp.php'           => array(
					'name'  => 'Easy WP SMTP',
					'class' => 'Branda_SMTP_Importer_Easy_WP_SMTP',
				),
				'gmail-smtp/main.php'                     => array(
					'name' => 'Gmail SMTP',
				),
				'smtp-mailer/main.php'                    => array(
					'name' => 'SMTP Mailer',
				),
				'wp-email-smtp/wp_email_smtp.php'         => array(
					'name' => 'WP Email SMTP',
				),
				'bws-smtp/bws-smtp.php'                   => array(
					'name' => 'SMTP by BestWebSoft',
				),
				'wp-sendgrid-smtp/wp-sendgrid-smtp.php'   => array(
					'name' => 'WP SendGrid SMTP',
				),
				'cimy-swift-smtp/cimy_swift_smtp.php'     => array(
					'name' => 'Cimy Swift SMTP',
				),
				'sar-friendly-smtp/sar-friendly-smtp.php' => array(
					'name' => 'SAR Friendly SMTP',
				),
				'wp-easy-smtp/wp-easy-smtp.php'           => array(
					'name' => 'WP Easy SMTP',
				),
				'wp-gmail-smtp/wp-gmail-smtp.php'         => array(
					'name' => 'WP Gmail SMTP',
				),
				'email-log/email-log.php'                 => array(
					'name' => 'Email Log',
				),
				'sendgrid-email-delivery-simplified/wpsendgrid.php' => array(
					'name' => 'SendGrid',
				),
				'mailgun/mailgun.php'                     => array(
					'name' => 'Mailgun for WordPress',
				),
				'wp-mail-bank/wp-mail-bank.php'           => array(
					'name'  => 'WP Mail Bank',
					'class' => 'Branda_SMTP_Importer_WP_Mail_Bank',
				),
			);
			foreach ( $list as $path => $data ) {
				if ( is_plugin_active( $path ) ) {
					$data['file']                = basename( $path );
					$this->plugins_list[ $path ] = $data;
				}
			}
			return;
		}

		public function get_list_of_active_plugins() {
			foreach ( $this->plugins_list as $path => $data ) {
				$this->plugins_list[ $path ]['nonce'] = $this->get_nonce_value( $path );
			}
			$template = sprintf( '/admin/modules/%s/plugins-list', $this->module );
			$args     = array(
				'plugins' => $this->plugins_list,
				'action'  => $this->get_name( 'deactivate' ),
			);
			$content  = $this->render( $template, $args, true );
			return $content;
		}

		public function ajax_deactivate_coflicted_plugin() {
			$id           = sanitize_text_field( $_POST['id'] );
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'mode' ) );
			$mode = sanitize_text_field( $_POST['mode'] );

			switch ( $mode ) {
				case 'deactivate':
					$plugin = wp_unslash( $id );
					deactivate_plugins( $plugin );
					wp_send_json_success();
					break;
				case 'import':
					if ( ! isset( $this->plugins_list[ $id ] ) ) {
						$this->json_error();
					}
					$plugin = $this->plugins_list[ $id ];
					$file   = dirname( __FILE__ ) . '/importers/' . $plugin['file'];
					if ( ! is_file( $file ) ) {
						$this->json_error();
					}
					include_once $file;
					if ( ! class_exists( $plugin['class'] ) ) {
						$this->json_error();
					}
					$importer = new $plugin['class']();
					$importer->import( $this );
					$plugin = wp_unslash( $id );
					deactivate_plugins( $plugin );
					wp_send_json_success();
					break;
				default:
					break;
			}
			$this->json_error();
		}

		public function smtp_get_value() {
			return $this->get_value();
		}

		public function smtp_update_value( $value ) {
			return $this->update_value( $value );
		}

		/**
		 * Escape module fields.
		 *
		 * @param mixed|array|string $data
		 * @return string|array
		 */
		public static function esc_data( $data ) {
			if ( ! is_array( $data ) ) {
				return esc_html( $data );
			}

			if ( empty( $data['key'] ) || empty( $data['value'] ) ) {
				return $data;
			}

			// Password might contain special characters like `> &` that when escaped will break password.
			// Password is also returned with index `smtp_authentication`.
			if ( in_array( $data['key'], array( 'smtp_authentication', 'smtp_password' ) ) ) {
				return strip_tags( $data['value'] );
			}

			return esc_html( $data['value'] );
		}

		/**
		 * Preserve fields. If the smtp password field is empty, it means that the user didn't change the password so the old one needs to be preserved.
		 *
		 * @param array $fields
		 *
		 * @return array
		 */
		public function preserve( array $fields = array() ) : array {
			$smtp_password = $_POST['simple_options']['smtp_authentication']['smtp_password'];

			if ( empty( $smtp_password ) ) {
				$fields['smtp_authentication'] = array( 'smtp_password' );
			}

			// The encryption method is stored only by the plugin, so we need to preserve it.
			$fields['smtp_authentication'] = array( 'encryption_method' );

			return $fields;
		}

		/**
		 * Encrypt input. This method is used to encrypt the smtp password before saving it to the database.
		 * @param mixed $input
		 * @return string
		 */
		public function encrypt( ?string $input = '' ): string {
			if ( empty( $input ) || ! $this->can_encrypt() ) {
				return $input;
			}

			if ( $this->encrypt_method_available( 'sodium' ) ) {
				return $this->encrypt_sodium( $input );
			}

			if ( $this->encrypt_method_available( 'openssl' ) ) {
				return $this->encrypt_openssl( $input );
			}

			return $input;
		}

		/**
		 * Decrypt output. This method is used to decrypt the smtp password before usage.
		 * @param mixed $input
		 * @return string
		 */
		public function decrypt( ?string $input = '' ): string {
			if ( empty( $input ) || ! $this->can_encrypt() ) {
				return $input;
			}

			// In case the SMTP Password is already set before the encryption was introduced, we need to encrypt it.
			// We can confirm if the password is encrypted by checking if.
			$input = $this->handle_existing_password( $input );

			if ( $this->encrypt_method_available( 'sodium' ) ) {
				return $this->decrypt_sodium( $input );
			}

			if ( $this->encrypt_method_available( 'openssl' ) ) {
				return $this->decrypt_openssl( $input );
			}

			return $input;
		}

		/**
		 * Encrypt using sodium library.
		 * @param mixed $input
		 * @return string
		 */
		public function encrypt_sodium( ?string $input = '' ): string {
			$key = $this->get_encryption_key();

			// Random nonce, unique for each encryption.
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			// Encrypt input with secret key and nonce.
			$ciphertext = sodium_crypto_secretbox( $input, $nonce, $key );

			// We then convert the encrypted message with the nonce to base64 for safe transport or storage.
			// Again we use a timing-safe variant of base64_encode() to do this.
			$result = sodium_bin2base64( $nonce . $ciphertext, SODIUM_BASE64_VARIANT_ORIGINAL );

			$this->set_encryption_method( 'sodium' );

			// Overwrite $input, $nonce and $key with null bytes in order to prevent sensitive data leak.
			// Let's use try-catch block to prevent following exception:
			// Uncaught SodiumException: This is not implemented in sodium_compat, as it is not possible to securely wipe memory from PHP. To fix this error, make sure libsodium is installed and the PHP extension is enabled.
			// We provide an alternative manual method to overwrite the data in case the sodium_memzero is not available.
			try {
				sodium_memzero( $input );
				sodium_memzero( $nonce );
				sodium_memzero( $key );
			} catch ( \Exception $e ) {
				$this->secure_zero( $input );
				$this->secure_zero( $nonce );
				$this->secure_zero( $key );
			}

			return $result;
		}

		/**
		 * Encrypt using openssl library.
		 * @param mixed $input
		 * @return string
		 */
		public function encrypt_openssl( ?string $input = '' ): string {
			$encryption_key     = $this->get_encryption_key(); // Or openssl_random_pseudo_bytes() for a random key.
			$cipher_algo        = 'aes-256-cbc';
			$iv                 = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $cipher_algo ) );
			$encrypted_password = openssl_encrypt( $input, $cipher_algo, $encryption_key, 0, $iv );

			$this->set_encryption_method( 'openssl' );

			return base64_encode( $encrypted_password . '::' . $iv );
		}

		/**
		 * Decrypt using sodium library.
		 * @param string $input
		 * @param string $key
		 * @return string
		 */
		public function decrypt_sodium( ?string $input = '', ?string $key ='' ): string {
			// Fetch secret key.
			$key = ! empty( $key ) ? $key : $this->get_encryption_key();

			if ( strlen( $key ) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES ) {
				return '';
				//throw new Exception( 'Encryption Key must be 32 bytes long.' );
			}

			// Convert the base64 encoded message to binary using sodium_base642bin().
			try {
				$ciphertext = sodium_base642bin( $input, SODIUM_BASE64_VARIANT_ORIGINAL );
			} catch ( \Exception $e ) {
				return '';
			}

			// Extract nonce from the ciphertext by taking the first 24 (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) chars.
			$nonce = mb_substr( $ciphertext, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );

			// Remaining part is the encrypted message.
			$ciphertext = mb_substr( $ciphertext, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );

			// Decrypt the message with the secret key and nonce.
			$plaintext = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );
			$plaintext = $plaintext !== false ? $plaintext : '';

			// Overwrite $ciphertext, $nonce and $key with null bytes in order to prevent sensitive data leak.
			// Let's use try-catch block to prevent following exception:
			// Uncaught SodiumException: This is not implemented in sodium_compat, as it is not possible to securely wipe memory from PHP. To fix this error, make sure libsodium is installed and the PHP extension is enabled.
			// We provide an alternative manual method to overwrite the data in case the sodium_memzero is not available.
			try {
				sodium_memzero( $nonce );
				sodium_memzero( $key );
				sodium_memzero( $ciphertext );
			} catch ( \Exception $e ) {
				$this->secure_zero( $nonce );
				$this->secure_zero( $key );
				$this->secure_zero( $ciphertext );
			}

			return $plaintext;
		}

		/**
		 *
		 * Decrypt openssl.
		 * @param mixed $input
		 * @return string
		 */
		public function decrypt_openssl( ?string $input = '' ): string {
			$encryption_key              = $this->get_encryption_key();
			$cipher_algo                 = 'aes-256-cbc';
			list( $encrypted_data, $iv ) = explode( '::', base64_decode( $input ), 2 );

			return openssl_decrypt( $encrypted_data, $cipher_algo, $encryption_key, 0, $iv );
		}

		/**
		 * Get encryption key
		 * @return string
		 */
		public function get_encryption_key(): string {
			if ( defined( 'WPMU_DEV_UB_SMTP_ENCRYPTION_KEY' ) ) {
				return WPMU_DEV_UB_SMTP_ENCRYPTION_KEY;
			}

			static $key = null;

			if ( is_null( $key ) ) {
				$encrypted_data = branda_get_option( $this->encryption_key_option );

				if ( empty( $encrypted_data ) ) {
					$encrypted_data = $this->set_encryption_data();
				} else {
					// If `encryption_data` is a string, it means it's the key set in previous format used in version 3.4.20.
					// We need to pass the key as part of the new array format.
					if ( is_string( $encrypted_data ) ) {
						$encrypted_data = $this->set_encryption_data( array( 'key' => $encrypted_data ) );
					}
				}
			}

			return isset( $encrypted_data['key'] ) && is_string( $encrypted_data['key'] ) ? $encrypted_data['key'] : '';
		}

		/**
		 *
		 * Sets encryption data.
		 * @return array
		 */
		protected function set_encryption_data( array $args = array() ): array {
			$key			   = '';
			$encryption_method = '';

			if ( $this->encrypt_method_available( 'sodium' ) ) {
				$key               = wp_generate_password( SODIUM_CRYPTO_SECRETBOX_KEYBYTES, false ); //sodium_crypto_secretbox_keygen();
				$encryption_method = 'sodium';
			} elseif ( $this->encrypt_method_available( 'openssl' ) ) {
				$key               = wp_generate_password( 32, false );
				$encryption_method = 'openssl';
			}

			$data = array(
				'key'               => isset( $args['key'] ) && is_string( $args['key'] ) ? $args['key'] : $key,
				'encryption_method' => isset( $args['encryption_method'] ) && is_string( $args['encryption_method'] ) ? $args['encryption_method'] : $encryption_method,
			);

			//branda_add_option( $this->encryption_key_option, $data, 'no' );
			branda_update_option( $this->encryption_key_option, $data );

			return $data;
		}

		/**
		 * Gets encryption data.
		 * @return array
		 */
		protected function get_encryption_data(): array {
			static $encryption_data;

			if ( ! is_null( $encryption_data ) && is_array( $encryption_data ) ) {
				return $encryption_data;
			}

			$encryption_data = branda_get_option( $this->encryption_key_option );

			if ( empty( $encryption_data ) || ! is_array( $encryption_data ) ) {
				$encryption_args = array();

				// If `encryption_data` is a string, it means it's the key set in previous format used in version 3.4.20.
				// We need to pass the key as part of the new array format.
				if (  is_string( $encryption_data ) ) {
					$encryption_args['key'] = $encryption_data;
				}

				$encryption_data = $this->set_encryption_data( $encryption_args );
			}

			return $encryption_data;
		}

		/**
		 * Encrypt existing password if it's not encrypted.
		 *
		 * @param mixed $input
		 * @return string
		 */
		protected function handle_existing_password( ?string $input = '' ): string {
			// If SMTP Password is empty we don't need to encrypt it.
			if ( ! empty( $this->get_value( 'smtp_authentication', 'smtp_password', null, false ) ) ) {
				$smtp_password       = $input;
				$encryption_data     = branda_get_option( $this->encryption_key_option );

				// If there is no encryption_data, it means the password is not encrypted so we need to encrypt it.
				// If encryption_data is set but it is in string format, it means that the password has been encrypted with old key.
				if ( empty( $encryption_data ) ) {
					$input = $this->force_set_smtp_password( $smtp_password );
				} elseif ( is_string( $encryption_data ) ) {
					// If Sodium is not supported, it means that previous password has not been encrypted.
					if ( ! $this->encrypt_method_available( 'sodium' ) ) {
						// The $this->set_encryption_data() method is called indirectly through $this->get_encryption_key() which is called form $this->encrypt(), so we don't need to call it again.
						$input = $this->force_set_smtp_password( $smtp_password );
					} else {
						// No need to change the $input value (the password), it's already encrypted and we are keeping the same encryption key so it can be decrypted.
						$this->set_encryption_data( array( 'key' => $encryption_data ) );
					}
				}
			}

			return $input;
		}

		/**
		 * Force set SMTP password.
		 * @param string $password The new password.
		 * @param bool $encrypt A boolean that indicates if the new password needs to be encrypted.
		 * @return void
		 */
		protected function force_set_smtp_password( ?string $password = '', bool $encrypt = true ): string {
			$smtp_options = branda_get_option( 'ub_smtp' );
			$password     = $encrypt ? $this->encrypt( $password ) : $password;

			$smtp_options['smtp_authentication']['smtp_password'] = $password;

			branda_update_option( 'ub_smtp', $smtp_options );

			return $password;
		}

		/**
		 * Fetches the encryption method used to encrypt. Atm sodium or openssl
		 *
		 * @return string
		 */
		protected function get_encryption_method(): string {
			$encryption_data   = $this->get_encryption_data();
			$encryption_method = $encryption_data['encryption_method'] ?? '';

			return in_array( $encryption_method, array( 'sodium', 'openssl' ) ) ? $encryption_method : '';
		}

		/**
		 *
		 * Set encryption method.
		 * @param string $method
		 * @return void
		 */
		protected function set_encryption_method( string $method = '' ): void {
			if ( in_array( $method, array( 'sodium', 'openssl' ) ) ) {
				$smtp_options                                             = branda_get_option( 'ub_smtp' );
				$smtp_options['smtp_authentication']['encryption_method'] = $method;

				branda_update_option( 'ub_smtp', $smtp_options );
			}
		}

		/**
		 * Check if encryption is available.
		 * @return bool
		 */
		protected function can_encrypt(): bool {
			return ! apply_filters( 'branda_smtp_password_encryption_disable', false );
		}

		protected function encrypt_method_available( ?string $method = 'sodium' ): bool {
			switch ( $method ) {
				case 'sodium':
					return function_exists( 'sodium_crypto_secretbox' ) &&
						function_exists( 'sodium_base642bin' ) &&
						function_exists( 'sodium_crypto_secretbox_keygen' );
				case 'openssl':
					return function_exists( 'openssl_encrypt' ) &&
						function_exists( 'openssl_decrypt' ) &&
						function_exists( 'openssl_random_pseudo_bytes' ) &&
						function_exists( 'openssl_cipher_iv_length' ) &&
						function_exists( 'base64_encode' );
				default:
					return false;
			}
		}

		/**
		 * Manually overwrite sensitive data with zeros in memory.
		 *
		 * @param mixed $data
		 * @return void
		 */
		protected function secure_zero( ?string &$data = '' ): void {
			/*
			When sensitive data like passwords or encryption keys are stored in memory,
			setting the variable to 0 or an empty string ('') only replaces the reference to that memory location with a new value (like 0 or an empty string).
			The original sensitive data may still exist in memory and can be recovered, even though the variable no longer references it.

			By using str_repeat("\0", strlen($data)), we’re actively overwriting each byte of the original string with null bytes (\0).
			This ensures that the sensitive data in memory is replaced with non-sensitive data.

			When dealing with cryptographic data, the goal is to prevent recovery of the sensitive information.
			Setting a variable to 0 or '' doesn’t actually ensure that the original sensitive data is gone from memory.
			It just changes the value the variable holds.
			What is does is simply reallocating the memory used by the variable to store the new value, but the old value might still be present in memory

			Overwriting with null bytes (\0) is a well-established security practice to ensure that the memory used to store sensitive information is
			thoroughly cleared before it’s deallocated or reused by the system.

			The purpose of using str_repeat("\0", strlen($data)) is to overwrite each byte of the original data with a null byte.
			This ensures that the entire memory previously allocated for the sensitive data is replaced, making recovery of the sensitive information much more difficult.

			Setting it to just "\0" only overwrites the first byte of the memory, and doesn't address the remaining bytes where sensitive information might still be stored.
			*/
			$data = str_repeat( "\0", strlen( $data ) );  // Overwrite the string
			unset( $data );  // Optionally unset
		}

		/**
		 * Sanitize input.
		 * @param mixed $value
		 * @param mixed $type
		 * @param mixed $section_key
		 * @param mixed $key
		 * @param mixed $current_value
		 * @param mixed $data
		 * @param mixed $module
		 * @return mixed
		 */
		public function sanitize_input( $value, $type, $section_key, $key, $current_value, $data, $module ) {
			// Let's not declare args types at this point.
			// We do expect $value and $current_value to be of type array or null,
			// however we need to inspect and confirm this first with all fields in all modules in order to avoid errors.
			if ( 'smtp' === $module && 'smtp_password' === $key && 'smtp_authentication' === $section_key && ! empty( $value['smtp_authentication']['smtp_password'] ) ) {
				$value['smtp_authentication']['smtp_password'] = $this->encrypt( $value['smtp_authentication']['smtp_password'] );
			}

			return $value;

		}
	}
}
new Branda_SMTP();
