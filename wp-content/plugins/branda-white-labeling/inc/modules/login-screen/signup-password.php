<?php
/**
 * Branda Signup Password class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Signup_Password' ) ) {

	class Branda_Signup_Password extends Branda_Helper {
		var $signup_password_use_encryption = 'yes'; // Either 'yes' OR 'no'

		public function __construct() {
			if ( is_user_logged_in() ) {
				return;
			}
			$this->module = 'signup-password';
			add_action( 'template_redirect', array( $this, 'password_init_sessions' ) );
			add_action( 'register_form', array( $this, 'password_fields' ) );
			add_action( 'signup_extra_fields', array( $this, 'password_fields' ) );
			add_filter( 'wpmu_validate_user_signup', array( $this, 'password_filter' ) );
			add_filter( 'signup_blogform', array( $this, 'password_fields_pass_through' ) );
			add_filter( 'add_signup_meta', array( $this, 'password_meta_filter' ), 99 );
			add_filter( 'random_password', array( $this, 'password_random_password_filter' ) );
			add_filter( 'wp_new_user_notification_email', array( $this, 'new_user_notification_email' ), 10, 3 );
			add_action( 'login_enqueue_scripts', array( $this, 'enqueue_style' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );
			add_filter( 'wp_pre_insert_user_data', array( $this, 'pre_insert_user_data' ), 10, 3 );
			add_action( 'activate_wp_head', array( $this, 'wpmu_activate_stylesheet' ) );
		}

		/**
		 * Set admin option page fields.
		 */
		protected function set_options() {
		}

		public function password_encrypt( $data ) {
			if ( ! isset( $chars ) ) {
				// 3 different symbols (or combinations) for obfuscation
				// these should not appear within the original text
				$sym = array( '∂', '•xQ', '|' );
				foreach ( range( 'a', 'z' ) as $key => $val ) {
					$chars[ $val ] = str_repeat( $sym[0], ( $key + 1 ) ) . $sym[1];
				}
				$chars[' '] = $sym[2];
				unset( $sym );
			}
			// encrypt
			$data = base64_encode( strtr( $data, $chars ) );
			return $data;
		}

		public function password_decrypt( $data ) {
			if ( ! isset( $chars ) ) {
				// 3 different symbols (or combinations) for obfuscation
				// these should not appear within the original text
				$sym = array( '∂', '•xQ', '|' );
				foreach ( range( 'a', 'z' ) as $key => $val ) {
					$chars[ $val ] = str_repeat( $sym[0], ( $key + 1 ) ) . $sym[1];
				}
				$chars[' '] = $sym[2];
				unset( $sym );
			}

			// decrypt
			$charset = array_flip( $chars );
			$charset = array_reverse( $charset, true );
			$data    = strtr( base64_decode( $data ), $charset );
			unset( $charset );
			return $data;
		}

		public function password_filter( $content ) {
			$password_1 = isset( $_POST['password_1'] ) ? $_POST['password_1'] : '';
			$password_2 = isset( $_POST['password_2'] ) ? $_POST['password_2'] : '';
			if ( ! empty( $password_1 ) && 'validate-user-signup' === $_POST['stage'] ) {
				if ( $password_1 != $password_2 ) {
					$content['errors']->add( 'password_1', __( 'Passwords do not match.', 'ub' ) );
				}
			}
			return $content;
		}

		public function password_meta_filter( $meta ) {
			global $signup_password_use_encryption;
			$password_1 = isset( $_POST['password_1'] ) ? $_POST['password_1'] : '';
			if ( ! empty( $password_1 ) ) {
				if ( 'yes' === $signup_password_use_encryption ) {
					$password_1 = $this->wpmu_signup_password_encrypt( $password_1 );
				}
				$add_meta = array( 'password' => $password_1 );
				$meta     = array_merge( $add_meta, $meta );
			}
			return $meta;
		}

		public function password_random_password_filter( $password ) {
			global $wpdb, $signup_password_use_encryption;
			if ( isset( $_GET['key'] ) && ! empty( $_GET['key'] ) ) {
				$key = $_GET['key'];
			} elseif ( isset( $_POST['key'] ) && ! empty( $_POST['key'] ) ) {
				$key = $_POST['key'];
			}
			if ( ! empty( $_POST['password_1'] ) ) {
				$password = $_POST['password_1'];
			} elseif ( ! empty( $key ) ) {
				$signup = $wpdb->get_row(
					$wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE activation_key = '%s'", $key )
				);
				if ( ! ( empty( $signup ) || $signup->active ) ) {
					// check for password in signup meta
					$meta = maybe_unserialize( $signup->meta );
					if ( ! empty( $meta['password'] ) ) {
						if ( 'yes' === $signup_password_use_encryption ) {
							$password = $this->password_decrypt( $meta['password'] );
						} else {
							$password = $meta['password'];
						}

						unset( $meta['password'] );
						$meta = maybe_serialize( $meta );
						$wpdb->update(
							$wpdb->signups,
							array( 'meta' => $meta ),
							array( 'activation_key' => $key ),
							array( '%s' ),
							array( '%s' )
						);
					}
				}
			}
			return $password;
		}

		public function password_fields_pass_through() {
			$password = '';
			if ( ! empty( $_POST['password_1'] ) && ! empty( $_POST['password_2'] ) ) {
				$password   = $_POST['password_1'];
				$password_2 = filter_input( INPUT_POST, 'password_2' );
			} elseif ( isset( $_SESSION['password_1'] ) && ! empty( $_SESSION['password_1'] ) ) {
				$password = $_SESSION['password_1'];
			}
			if ( ! empty( $password ) ) {
				printf(
					'<input type="hidden" name="password_1" value="%s" />',
					esc_attr( $password )
				);
			}
			if ( ! empty( $password_2 ) ) {
				printf( '<input type="hidden" name="password_2" value="%s" />', esc_attr( $password_2 ) );
			}
		}

		/**
		 * Add password field to register_form
		 */
		public function password_fields( $errors ) {
			$error = '';
			if ( $errors && method_exists( $errors, 'get_error_message' ) ) {
				$error = $errors->get_error_message( 'password_1' );
			}
			$template = sprintf( '/admin/modules/%s/password-fields', $this->module );
			$args     = array(
				'error' => $error,
			);
			$this->render( $template, $args );
		}

		public function password_init_sessions() {
			if ( is_user_logged_in() ) {
				return;
			}
			if ( ! session_id() ) {
				session_start();
			}
		}

		public function wpmu_activate_stylesheet() {
			?>
			<style type="text/css">
				#signup-welcome p:last-child { display: none; }
			</style>
			<?php
		}

		/**
		 * new user notification email
		 *
		 * @since 1.9.4
		 */
		public function new_user_notification_email( $email, $user, $blogname ) {
			/**
			 * Email message.
			 */
			$text = __(
				'Howdy USERNAME,

Your new account is set up.

You can log in with the following information:
Username: USERNAME

LOGINLINK

Thanks!

--The Team @ SITE_NAME',
				'ub'
			);

			$text             = preg_replace( '/USERNAME/', $user->user_login, $text );
			$text             = preg_replace( '/LOGINLINK/', network_site_url( 'wp-login.php' ), $text );
			$text             = preg_replace( '/SITE_NAME/', $blogname, $text );
			$email['message'] = $text;
			return $email;
		}

		public function enqueue_style() {
			global $ub_version;
			$file = branda_files_url( 'modules/login-screen/assets/css/signup-password.css' );
			wp_enqueue_style( __CLASS__, $file, false, $ub_version );
		}

		/**
		 * generate new password if is empty
		 *
		 * @since 1.9.6
		 */
		public function pre_insert_user_data( $data, $update, $id ) {
			if ( is_multisite() ) {
				global $wpdb;
				$query  = $wpdb->prepare( "select meta from {$wpdb->signups} where user_login = %s", $data['user_login'] );
				$result = $wpdb->get_var( $query );
				$meta   = maybe_unserialize( $result );
				if ( is_array( $meta ) && isset( $meta['password'] ) ) {
					$data['user_pass'] = wp_hash_password( $meta['password'] );
					unset( $meta['password'] );
					$wpdb->update(
						$wpdb->signups,
						array( 'meta' => maybe_serialize( $meta ) ),
						array( 'user_login' => $data['user_login'] )
					);
				}
				return $data;
			}
			if ( empty( $data['user_pass'] ) && empty( $_POST['password_1'] ) ) {
				$data['user_pass'] = wp_hash_password( wp_generate_password( 20, false ) );
			}
			return $data;
		}
	}
}
