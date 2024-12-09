<?php
/**
 * Branda Google OAuth class.
 *
 * @package    Branda
 * @subpackage Emails
 *
 * @sicne      3.3.0
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Do not allow directly accessing this file.
defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'Branda_Google_OAuth' ) ) {

	if ( ! class_exists( '\\Branda_Vendor\\Google\\Client' ) ) {
		require plugin_dir_path( __FILE__ ) . '../../../vendor_prefixed/autoload.php';
	}

	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	/**
	 * Google OAuth class to send email using Gmail API.
	 */
	class Branda_Google_OAuth extends Branda_Helper {
		/**
		 * Google OAuth configuration option name
		 *
		 * @var string
		 */
		protected $option_name = 'ub_google_oauth';
		/**
		 * Variable to check the status of Google OAuth configuration.
		 *
		 * @var WP_Error|bool
		 *
		 * @since 3.3.0
		 */
		private $is_ready = false;
		/**
		 * Google Client instance
		 *
		 * @var \Branda_Vendor\Google\Client
		 *
		 * @since 3.3.0
		 */
		private $client;

		/**
		 * Class constructor
		 */
		public function __construct() {
			parent::__construct();

			$this->module = 'google-oauth';

			add_action( 'admin_init', array( $this, 'admin_init' ) );

			if ( $this->is_network ) {
				add_action( 'network_admin_notices', array( $this, 'configure_client_notice' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'configure_client_notice' ) );
			}

			add_action(
				'wp_ajax_' . $this->get_name( 'send' ),
				array(
					$this,
					'send_test_email',
				)
			);

			add_filter( 'pre_wp_mail', array( $this, 'pre_wp_mail' ), 10, 2 );

			add_filter(
				'ultimatebranding_settings_google_oauth',
				array(
					$this,
					'admin_options_page',
				)
			);
			add_filter(
				'ultimatebranding_settings_google_oauth_reset',
				array(
					$this,
					'reset_module',
				)
			);
			add_filter(
				'ultimatebranding_settings_google_oauth_process',
				array(
					$this,
					'update',
				)
			);
			add_filter(
				'branda_settings_after_box_title',
				array(
					$this,
					'add_button_after_title',
				),
				10,
				2
			);
			add_filter(
				'branda_get_module_content',
				array(
					$this,
					'add_test_email_dialog',
				),
				10,
				2
			);
		}

		/**
		 * Add SUI dialog
		 *
		 * @param string $content Current module content.
		 * @param array  $module  Current module.
		 *
		 * @since 3.0.0
		 */
		public function add_test_email_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$template = '/admin/common/dialogs/test-email';
			$args     = array(
				'id'          => $this->get_name( 'send' ),
				'description' => __(
					'Send a dummy email to test the Gmail API configurations.',
					'ub'
				),
				'nonce'       => $this->get_nonce_value( 'send' ),
				'action'      => $this->get_name( 'send' ),
			);
			$content .= $this->render( $template, $args, true );
			/**
			 * Reset module.
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

		/**
		 * Send test email
		 *
		 * @throws Exception Invalid email.
		 *
		 * @since 2.0.0
		 */
		public function send_test_email() {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$nonce_action = $this->get_nonce_action( 'send' );
			$this->check_input_data( $nonce_action, array( 'email' ) );

			if ( is_wp_error( $this->is_ready ) ) {
				$this->json_error( $this->is_ready->get_error_message() );
			}

			$email = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

			if ( ! is_email( $email ) ) {
				$this->json_error(
					__( 'Unable to send: wrong email address.', 'ub' )
				);
			}

			$phpmailer = new PHPMailer();

			$phpmailer->CharSet     = get_bloginfo( 'charset' );
			$phpmailer->ContentType = 'text/plain';
			$phpmailer->IsHTML( false );

			$send_as_enabled = $this->get_value( 'google-oauth', 'send_as_enabled' );

			if ( 'on' === $send_as_enabled ) {
				$from_email_index = $this->get_value( 'header', 'from_email' );
				$from_name        = '';
				$from_name_force  = $this->get_value( 'header', 'from_name_force' );

				if ( 'on' === $from_name_force ) {
					$from_name = $this->get_value( 'header', 'from_name' );
				}

				$send_as_array = $this->get_value( 'google-oauth', 'send_as_array' );
				$phpmailer->setFrom( $send_as_array[ $from_email_index ], $from_name );
			}

			$phpmailer->AddAddress( $email );

			/* translators: %s: site title */
			$phpmailer->Subject = sprintf( __( 'This is test email sent from "%s"', 'ub' ), get_bloginfo( 'name' ) );

			$phpmailer->Body  = __( 'This is a test mail...', 'ub' );
			$phpmailer->Body .= PHP_EOL;
			$phpmailer->Body .= PHP_EOL;
			/* translators: %s: send date */
			$phpmailer->Body .= sprintf( __( 'Send date: %s.', 'ub' ), gmdate( 'c' ) );
			$phpmailer->Body .= PHP_EOL;
			$phpmailer->Body .= PHP_EOL;
			$phpmailer->Body .= '-- ';
			$phpmailer->Body .= PHP_EOL;
			/* translators: %s: site url */
			$phpmailer->Body .= sprintf( __( 'Site: %s.', 'ub' ), get_bloginfo( 'url' ) );

			try {
				// Prepare a message for sending if any changes happened above.
				$phpmailer->preSend();

				// Get the raw MIME email using MailCatcher data. We need to make base64URL-safe string.
				$base64 = str_replace(
					array( '+', '/', '=' ),
					array( '-', '_', '' ),
					base64_encode( $phpmailer->getSentMIMEMessage() ) // phpcs:ignore
				);

				$message = new \Branda_Vendor\Google\Service\Gmail\Message();
				$message->setRaw( $base64 );
				$service  = new \Branda_Vendor\Google\Service\Gmail( $this->get_client() );
				$response = $service->users_messages->send( 'me', $message );

				$success_message = sprintf(
					/* translators: %s: email string */
					__( 'Test email sent to <strong>%s</strong>.', 'ub' ),
					$email
				);

				wp_send_json_success(
					array(
						'message'  => $success_message,
						'response' => $response,
					)
				);
			} catch ( Exception $e ) {
				$err = json_decode( $e->getMessage(), true );

				wp_send_json_error( $err['error'] );
			}

			// phpcs:enabled
		}

		/**
		 * Send email using Gmail API and skip default sending functionality.
		 *
		 * @param null|bool $return      Short-circuit return value.
		 * @param array     $atts        {
		 *                               Array of the `wp_mail()` arguments.
		 *
		 * @type string|string[] $to          Array or comma-separated list of email addresses to send message.
		 * @type string          $subject     Email subject.
		 * @type string          $message     Message contents.
		 * @type string|string[] $headers     Additional headers.
		 * @type string|string[] $attachments Paths to files to attach.
		 * }
		 *
		 * @return bool
		 *
		 * @throws Exception Error in sending email using Gmail api.
		 */
		public function pre_wp_mail( $return, $atts ) {
			if ( true !== $this->is_ready ) {
				return $return;
			}

			if ( isset( $atts['to'] ) ) {
				$to = $atts['to'];
			}

			if ( ! is_array( $to ) ) {
				$to = explode( ',', $to );
			}

			if ( isset( $atts['subject'] ) ) {
				$subject = $atts['subject'];
			}

			if ( isset( $atts['message'] ) ) {
				$message = $atts['message'];
			}

			if ( isset( $atts['headers'] ) ) {
				$headers = $atts['headers'];
			}

			if ( isset( $atts['attachments'] ) ) {
				$attachments = $atts['attachments'];
			}

			if ( ! is_array( $attachments ) ) {
				$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
			}

			$phpmailer = new PHPMailer();

			// Headers.
			$cc       = array();
			$bcc      = array();
			$reply_to = array();

			if ( empty( $headers ) ) {
				$headers = array();
			} else {
				if ( ! is_array( $headers ) ) {
					// Explode the headers out, so this function can take
					// both string headers and an array of headers.
					$temp_headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
				} else {
					$temp_headers = $headers;
				}
				$headers = array();

				// If it's actually got contents.
				if ( ! empty( $temp_headers ) ) {
					// Iterate through the raw headers.
					foreach ( $temp_headers as $header ) {
						if ( strpos( $header, ':' ) === false ) {
							if ( false !== stripos( $header, 'boundary=' ) ) {
								$parts    = preg_split( '/boundary=/i', trim( $header ) );
								$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
							}
							continue;
						}
						// Explode them out.
						list( $name, $content ) = explode( ':', trim( $header ), 2 );

						// Cleanup crew.
						$name    = trim( $name );
						$content = trim( $content );

						switch ( strtolower( $name ) ) {
							// Mainly for legacy -- process a "From:" header if it's there.
							case 'from':
								$bracket_pos = strpos( $content, '<' );
								if ( false !== $bracket_pos ) {
									// Text before the bracketed email is the "From" name.
									if ( $bracket_pos > 0 ) {
										$from_name = substr( $content, 0, $bracket_pos - 1 );
										$from_name = str_replace( '"', '', $from_name );
										$from_name = trim( $from_name );
									}

									$from_email = substr( $content, $bracket_pos + 1 );
									$from_email = str_replace( '>', '', $from_email );
									$from_email = trim( $from_email );

									// Avoid setting an empty $from_email.
								} elseif ( '' !== trim( $content ) ) {
									$from_email = trim( $content );
								}
								break;
							case 'content-type':
								if ( strpos( $content, ';' ) !== false ) {
									list( $type, $charset_content ) = explode( ';', $content );
									$content_type                   = trim( $type );
									if ( false !== stripos( $charset_content, 'charset=' ) ) {
										$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
									} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
										$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
										$charset  = '';
									}

									// Avoid setting an empty $content_type.
								} elseif ( '' !== trim( $content ) ) {
									$content_type = trim( $content );
								}
								break;
							case 'cc':
								$cc = array_merge( (array) $cc, explode( ',', $content ) );
								break;
							case 'bcc':
								$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
								break;
							case 'reply-to':
								$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
								break;
							default:
								// Add it to our grand headers array.
								$headers[ trim( $name ) ] = trim( $content );
								break;
						}
					}
				}
			}

			// Empty out the values that may be set.
			$phpmailer->clearAllRecipients();
			$phpmailer->clearAttachments();
			$phpmailer->clearCustomHeaders();
			$phpmailer->clearReplyTos();

			// Set "From" name and email.

			// If we don't have a name from the input headers.
			if ( ! isset( $from_name ) ) {
				$from_name = 'WordPress';
			}

			/*
			 * If we don't have an email from the input headers, default to wordpress@$sitename
			 * Some hosts will block outgoing mail from this address if it doesn't exist,
			 * but there's no easy alternative. Defaulting to admin_email might appear to be
			 * another option, but some hosts may refuse to relay mail from an unknown domain.
			 * See https://core.trac.wordpress.org/ticket/5007.
			 */
			if ( ! isset( $from_email ) ) {
				// Get the site domain and get rid of www.
				$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
				if ( 'www.' === substr( $sitename, 0, 4 ) ) {
					$sitename = substr( $sitename, 4 );
				}

				$from_email = 'wordpress@' . $sitename;
			}

			/**
			 * Filters the email address to send from.
			 *
			 * @since 2.2.0
			 *
			 * @param string $from_email Email address to send from.
			 */
			$from_email = apply_filters( 'wp_mail_from', $from_email );

			/**
			 * Filters the name to associate with the "from" email address.
			 *
			 * @since 2.3.0
			 *
			 * @param string $from_name Name associated with the "from" email address.
			 */
			$from_name = apply_filters( 'wp_mail_from_name', $from_name );

			try {
				$phpmailer->setFrom( $from_email, $from_name, false );
			} catch ( Exception $e ) {
				$mail_error_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );

				$mail_error_data['phpmailer_exception_code'] = $e->getCode();

				/** This filter is documented in wp-includes/pluggable.php */
				do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );

				return false;
			}

			// Set mail's subject and body.
			$phpmailer->Subject = $subject; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$phpmailer->Body    = $message; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			// Set destination addresses, using appropriate methods for handling addresses.
			$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

			foreach ( $address_headers as $address_header => $addresses ) {
				if ( empty( $addresses ) ) {
					continue;
				}

				foreach ( $addresses as $address ) {
					try {
						// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
						$recipient_name = '';

						if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
							if ( 3 === count( $matches ) ) {
								$recipient_name = $matches[1];
								$address        = $matches[2];
							}
						}

						switch ( $address_header ) {
							case 'to':
								$phpmailer->addAddress( $address, $recipient_name );
								break;
							case 'cc':
								$phpmailer->addCc( $address, $recipient_name );
								break;
							case 'bcc':
								$phpmailer->addBcc( $address, $recipient_name );
								break;
							case 'reply_to':
								$phpmailer->addReplyTo( $address, $recipient_name );
								break;
						}
					} catch ( Exception $e ) {
						continue;
					}
				}
			}

			// Set to use PHP's mail().
			$phpmailer->isMail();

			// Set Content-Type and charset.

			// If we don't have a content-type from the input headers.
			if ( ! isset( $content_type ) ) {
				$content_type = 'text/plain';
			}

			/**
			 * Filters the wp_mail() content type.
			 *
			 * @since 2.3.0
			 *
			 * @param string $content_type Default wp_mail() content type.
			 */
			$content_type = apply_filters( 'wp_mail_content_type', $content_type );

			$phpmailer->ContentType = $content_type; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			// Set whether it's plaintext, depending on $content_type.
			if ( 'text/html' === $content_type ) {
				$phpmailer->isHTML( true );
			}

			// If we don't have a charset from the input headers.
			if ( ! isset( $charset ) ) {
				$charset = get_bloginfo( 'charset' );
			}

			/**
			 * Filters the default wp_mail() charset.
			 *
			 * @since 2.3.0
			 *
			 * @param string $charset Default email charset.
			 */
			$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			// Set custom headers.
			if ( ! empty( $headers ) ) {
				foreach ( $headers as $name => $content ) {
					// Only add custom headers not added automatically by PHPMailer.
					if ( ! in_array( $name, array( 'MIME-Version', 'X-Mailer' ), true ) ) {
						try {
							$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
						} catch ( Exception $e ) {
							continue;
						}
					}
				}

				if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) ) {
					$phpmailer->addCustomHeader( sprintf( 'Content-Type: %s; boundary="%s"', $content_type, $boundary ) );
				}
			}

			if ( ! empty( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					try {
						$phpmailer->addAttachment( $attachment );
					} catch ( Exception $e ) {
						continue;
					}
				}
			}

			$send_as_enabled = $this->get_value( 'google-oauth', 'send_as_enabled' );

			if ( 'on' === $send_as_enabled ) {
				$from_email_index = $this->get_value( 'header', 'from_email' );
				$from_name        = '';
				$from_name_force  = $this->get_value( 'header', 'from_name_force' );

				if ( 'on' === $from_name_force ) {
					$from_name = $this->get_value( 'header', 'from_name' );
				}

				$send_as_array = $this->get_value( 'google-oauth', 'send_as_array' );
				$phpmailer->setFrom( $send_as_array[ $from_email_index ], $from_name );
			}

			// Send!
			try {
				// Prepare a message for sending if any changes happened above.
				$phpmailer->preSend();

				// Get the raw MIME email. We need to make base64URL-safe string.
				$base64 = str_replace(
					array( '+', '/', '=' ),
					array( '-', '_', '' ),
					base64_encode( $phpmailer->getSentMIMEMessage() ) // phpcs:ignore
				);

				$message = new \Branda_Vendor\Google\Service\Gmail\Message();
				$message->setRaw( $base64 );
				$service = new \Branda_Vendor\Google\Service\Gmail( $this->client );
				$service->users_messages->send( 'me', $message );
			} catch ( Exception $e ) {
				error_log( wp_json_encode( $e->getMessage() ) ); // phpcs:ignore
			}

			return true;
		}

		/**
		 * Add "add feed" button.
		 *
		 * @param string $content Title content.
		 * @param array  $module  Module where the content to be added.
		 *
		 * @since 3.0.0
		 */
		public function add_button_after_title( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$content .= '<div class="sui-actions-left">';
			$args     = array(
				'data' => array(
					'modal-open' => $this->get_name( 'send' ),
				),
				'text' => __( 'Send Test Email', 'ub' ),
				'sui'  => 'ghost',
			);
			$content .= $this->button( $args );
			$content .= '</div>';
			return $content;
		}

		/**
		 * Check required configuration
		 *
		 * @since 3.3.0
		 */
		private function check() {
			$client = $this->get_client();

			if ( is_wp_error( $client ) ) {
				$this->is_ready = $client;
				return;
			}

			$token = $this->get_access_token();

			if ( is_wp_error( $token ) ) {
				$this->is_ready = $token;
				return;
			}

			$this->is_ready = true;
		}

		/**
		 * Modify option name
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $module      Module name.
		 *
		 * @since 3.3.0
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && $this->module === $module ) {
				return $this->option_name;
			}

			return $option_name;
		}

		/**
		 * Set options
		 *
		 * @since 3.3.0
		 */
		protected function set_options() {
			$options = array(
				'reset-module' => true,
				'google-oauth' => array(
					'title'       => __( 'Google OAuth 2.0', 'ub' ),
					'description' => sprintf(
					/* translators: %s: Google Workspace Create Project url */
						__( 'A Google Cloud Platform project with the Gmail API enabled is required. To create a project and enable an API, refer to <a href="%s" target="_blank">Create a project and enable API <span class="sui-icon-open-new-window sui-info" aria-hidden="true"></span></a>.', 'ub' ),
						esc_url(
							'https://developers.google.com/workspace/guides/create-project'
						)
					),
					'fields'      => array(
						'client_id'     => array(
							'label' => __( 'Client ID', 'ub' ),
						),
						'client_secret' => array(
							'label' => __( 'Client Secret', 'ub' ),
							'type'  => 'password',
						),
						'token'         => array(
							'type'     => 'callback',
							'callback' => array( $this, 'render_token' ),
						),
						'send_as'       => array(
							'type'  => 'hidden',
							'value' => $this->get_value( 'google-oauth', 'send_as_enabled', 'off' ),
						),
						'notice'        => array(
							'type'  => 'description',
							'value' => self::sui_notice(
								__( 'Please save changes after you make any change.', 'ub' )
							),
						),
					),
				),
				'header'       => array(
					'title'       => __( 'From Headers', 'ub' ),
					'description' => __( 'Choose the default from email and from name for all of your WordPress outgoing emails.', 'ub' ),
					'master'      => array(
						'section' => 'google-oauth',
						'field'   => 'send_as',
						'value'   => 'on',
					),
					'fields'      => array(
						'from_email'      => array(
							'label'       => __( 'Sender email address', 'ub' ),
							'description' => __( 'You can specify the email address that emails should be sent from.', 'ub' ),
							'type'        => 'select',
							'options'     => $this->get_value( 'google-oauth', 'send_as_array' ),
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
							'description' => __( 'Set your own from name for each email sent from your website. Be careful since it will override the from name provided by other plugins such as Contact Form.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Enable', 'ub' ),
								'off' => __( 'Disable', 'ub' ),
							),
							'default'     => 'on',
							'slave-class' => 'from-name',
						),
					),
				),
			);

			$this->options = $options;
		}

		/**
		 * Add admin notice about configuration.
		 *
		 * @since 3.3.0
		 */
		public function configure_client_notice() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_GET['page'] ) || strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'branding' ) === false ) {
				return;
			}

			if ( is_wp_error( $this->is_ready ) ) {
				$this->uba->add_message(
					array(
						'class'   => 'error sui-can-dismiss',
						'message' => $this->is_ready->get_error_message(),
					)
				);
			}
		}

		/**
		 * Get current admin page url.
		 *
		 * @return string|void
		 */
		public function get_current_page_url() {
			if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
				return;
			}

			$module_data = $this->uba->get_module_by_module( $this->module );

			return add_query_arg(
				array(
					'page'   => 'branding_group_' . $module_data['group'],
					'module' => $this->module,
				),
				network_admin_url( 'admin.php' )
			);
		}

		/**
		 * Update settings.
		 *
		 * @param bool $status Status.
		 */
		public function update( $status ) {
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$value = isset( $_POST['simple_options'] ) && ! empty( $_POST['simple_options'] ) ? wp_unslash( $_POST['simple_options'] ) : array();

			$google_oauth = $this->get_value( 'google-oauth' );

			if (
				$value['google-oauth']['client_id'] === $google_oauth['client_id'] &&
				$value['google-oauth']['client_secret'] === $google_oauth['client_secret']
			) {
				$value['google-oauth'] = $google_oauth;
			} else {
				unset( $value['header'] );
			}

			return $this->update_value( $value );
		}

		/**
		 * Render Google OAuth token.
		 *
		 * @return string|void
		 */
		public function render_token() {
			$client = $this->get_client();

			if ( is_wp_error( $client ) ) {
				return;
			}

			$token = $this->get_access_token();

			if ( ! is_wp_error( $token ) ) {
				return;
			}

			return $this->render(
				sprintf( '/admin/modules/%s/token', $this->module ),
				array(
					'auth_url' => $client->createAuthUrl(),
				),
				true
			);
		}

		/**
		 * Retrieve Google Client.
		 *
		 * @return \Branda_Vendor\Google\Client|WP_Error
		 */
		public function get_client() {
			if ( ! $this->client ) {
				$client_id     = $this->get_value( 'google-oauth', 'client_id' );
				$client_secret = $this->get_value( 'google-oauth', 'client_secret' );

				if ( empty( $client_id ) || empty( $client_secret ) ) {
					return new WP_Error(
						'client_error',
						sprintf(
							/* translators: %s: current page url */
							__( 'Please configure your <a href="%s">Google OAuth 2.0</a> in order to send email using Gmail API.', 'ub' ),
							esc_url( $this->get_current_page_url() )
						)
					);
				}

				$this->client = new \Branda_Vendor\Google\Client();
				$this->client->setApplicationName( 'Branda Pro - Gmail Connect' );
				$this->client->setScopes(
					array(
						\Branda_Vendor\Google\Service\Gmail::GMAIL_SEND,
						\Branda_Vendor\Google\Service\Gmail::GMAIL_SETTINGS_BASIC,
					)
				);
				$this->client->setClientId( $client_id );
				$this->client->setClientSecret( $client_secret );
				$this->client->setRedirectUri( $this->get_current_page_url() );
				$this->client->setAccessType( 'offline' );
				$this->client->setPrompt( 'select_account consent' );
			}

			return $this->client;
		}

		/**
		 * Get the connected emails which can be used as "Send As".
		 *
		 * @return array|false The list of possible send-as emails.
		 */
		public function get_send_as() {
			return $this->get_value( 'google-oauth', 'send_as_array', false );
		}

		/**
		 * Get the token, refresh the token if necessary.
		 *
		 * @return WP_Error|true
		 */
		public function get_access_token() {
			$token = $this->get_value( 'google-oauth', 'token' );

			if ( empty( $token ) ) {
				return new WP_Error(
					'token_empty',
					__( 'Please get a new token.', 'ub' )
				);
			} elseif ( isset( $token['error'] ) ) {
				return new WP_Error(
					$token['error'],
					sprintf(
						/* translators: %s: error description */
						__( '%s. Please check your Google Account and API configurations again.', 'ub' ),
						$token['error_description']
					)
				);
			}

			$client = $this->get_client();

			$client->setAccessToken( $token );

			// If there is no previous token, or it's expired.
			if ( $client->isAccessTokenExpired() ) {
				// Refresh the token if possible, else fetch a new one.
				if ( $client->getRefreshToken() ) {
					$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
				} else {
					// Request authorization from the user.
					return new WP_Error(
						'token_expired',
						sprintf(
						/* translators: %s: authorization url */
							__( 'Token does not exist or has expired. Please <a href="%s">retrieve token</a> again.', 'ub' ),
							esc_url( $client->createAuthUrl() )
						)
					);
				}

				$new_token = $client->getAccessToken();

				if ( $token['access_token'] !== $new_token['access_token'] ) {
					$this->set_value( 'google-oauth', 'token', $new_token );
					$token = $new_token;
				}
			}

			return $token;
		}

		/**
		 * Admin init action hook.
		 */
		public function admin_init() {
			$this->check();

			if ( ! is_a( $this->uba, 'Branda_Admin' ) ) {
				return;
			}

			$module_data = $this->uba->get_module_by_module( $this->module );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_GET['page'] ) || 'branding_group_' . $module_data['group'] !== $_GET['page'] || ! isset( $_GET['module'] ) || $this->module !== $_GET['module'] ) {
				return;
			}

			/**
			 * Set access token if required.
			 */
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['code'] ) && ! empty( $_GET['code'] ) && isset( $_GET['scope'] ) ) {
				$client = $this->get_client();

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$token = $client->fetchAccessTokenWithAuthCode( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );

				$gmail = new \Branda_Vendor\Google\Service\Gmail( $client );

				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$response = $gmail->users_settings_sendAs->listUsersSettingsSendAs( 'me' );

				$send_as_array = array_map(
					function( $send_as_obj ) {
						return $send_as_obj['sendAsEmail'];
					},
					$response->getSendAs()
				);

				$this->set_value( 'google-oauth', 'token', $token );
				$this->set_value( 'google-oauth', 'send_as_array', $send_as_array );
				$this->set_value( 'google-oauth', 'send_as_enabled', 'on' );
				$this->set_value( 'header', 'from_email' );
				$this->set_value( 'header', 'from_name_force', 'on' );
				$this->set_value( 'header', 'from_name' );

				wp_safe_redirect( $this->get_current_page_url() );
				exit;
			}
		}
	}
}

new Branda_Google_OAuth();
