<?php
/**
 * Branda Login Screen class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Login_Screen' ) ) {

	/**
	 * Class Branda_Login_Screen.
	 */
	class Branda_Login_Screen extends Branda_Helper {

		/**
		 * Should proceed with gttext.
		 *
		 * @var bool
		 */
		private $proceed_gettext = false;

		/**
		 * Patterns.
		 *
		 * @var array
		 */
		private $patterns = array();

		/**
		 * Module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_login_screen';

		/**
		 * Module file
		 *
		 * @since 3.0.0
		 *
		 * @var string
		 */
		protected $file = __FILE__;

		/**
		 * Escape callback function to be called in parent class's `esc_deep()` method.
		 * `esc_html` or `wp_kses_post` will break css
		 *
		 * @since 3.4.9.1
		 */
		protected $esc_callback = array( __CLASS__, 'esc_data' );

		/**
		 * Branda_Login_Screen constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'login-screen';
			add_filter( 'ultimatebranding_settings_login_screen', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_login_screen_process', array( $this, 'update' ), 10, 1 );
			add_filter( 'ultimatebranding_settings_login_screen_preserve', array( $this, 'add_preserve_fields' ) );
			add_action( 'signup_header', array( $this, 'output' ), 99 );
			add_action( 'login_head', array( $this, 'output' ), 99 );
			add_filter( 'login_headerurl', array( $this, 'login_header_url' ) );
			add_filter( 'login_headertext', array( $this, 'login_header_text' ) );
			add_filter( 'wp_login_errors', array( $this, 'wp_login_errors' ) );
			add_filter( 'wp_login_errors', array( $this, 'set_remember_me' ) );
			add_filter( 'gettext', array( $this, 'gettext_login_form_labels' ), 20, 3 );
			add_filter( 'logout_redirect', array( $this, 'logout_redirect' ), 99 );
			add_filter( 'login_redirect', array( $this, 'login_redirect' ), 99 );
			add_filter( 'ultimatebranding_reset_section_login-screen', array( $this, 'reset_section' ), 10, 3 );
			add_action( 'branda_helper_admin_options_page_before_options', array( $this, 'before_admin_options_page' ) );
			add_filter( 'branda_sanitize_input_by_type', array( $this, 'refactor_data' ), 10, 7 );
			/**
			 * Add login message
			 *
			 * @since 3.2.0
			 */
			add_filter( 'login_message', array( $this, 'login_message' ) );
			/**
			 * wp-login.php actions
			 */
			add_action( 'login_header', array( $this, 'add_login_header' ) );
			add_action( 'login_footer', array( $this, 'add_login_footer' ) );
			/**
			 * Signup Password
			 *
			 * Add password field on register form
			 *
			 * @since 1.9.5
			 */
			add_action( 'after_setup_theme', array( $this, 'signup_password_init' ) );
			/**
			 * Force language on login form
			 *
			 * @since 2.3.0
			 */
			add_action( 'setup_theme', array( $this, 'set_language_on_login_form' ) );
			/**
			 * Upgrade options.
			 *
			 * Merge date from "Login CSS" module.
			 *
			 * @since 2.3.0
			 */
			add_action( 'init', array( $this, 'upgrade_options_module_custom_login_css' ) );
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add related config.
			 *
			 * @since 2.3.0
			 */
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_logo' ) );
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_background' ) );
			/**
			 * Set template
			 */
			add_action( 'wp_ajax_branda_login_screen_set_template', array( $this, 'ajax_set_template' ) );
			/**
			 * Validate password
			 *
			 * @since x.x.x
			 */
			add_filter( 'registration_errors', array( $this, 'validate_password' ) );
			add_filter( 'wpmu_validate_user_signup', array( $this, 'validate_password_wpmu' ) );
		}

		/**
		 * Reset section for templates means to set their default values
		 *
		 * @since 3.3.1
		 * @param bool   $success
		 * @param string $option_name
		 * @param string $section
		 * @return boolean
		 */
		public function reset_section( $success, $option_name, $section ) {
			$value = branda_get_option( $option_name );
			if ( ! empty( $value['theme']['id'] ) ) {
				$theme_data        = $this->get_theme_data( $value['theme']['id'] );
				$new_data          = isset( $theme_data[ $section ] ) ? $theme_data[ $section ] : array();
				$value[ $section ] = $new_data;
				branda_update_option( $option_name, $value );

				return true;
			}

			return $success;
		}

		/**
		 * Upgrade options to new structure.
		 *
		 * @since 2.3.0
		 */
		public function upgrade_options_module_custom_login_css() {
			global $ub_version;
			$compare = version_compare( $ub_version, '2.2.0' );
			if ( 0 < $compare ) {
				// Move settings from "Login CSS" module.
				$module_name = 'custom-login-css.php';
				$is_active   = branda_is_active_module( $module_name );
				if ( $is_active ) {
					$value = branda_get_option( 'global_login_css' );
					if ( ! empty( $value ) ) {
						if (
							is_array( $value )
							&& isset( $value['login'] )
							&& isset( $value['login']['css'] )
							&& ! empty( $value['login']['css'] )
						) {
							$options = $this->get_value();
							if ( ! is_array( $options ) ) {
								$options = array();
							}
							$options['css']['css'] = $value['login']['css'];
							$this->update_value( $options );
						}
						branda_delete_option( 'global_login_css' );
						$uba = new Branda_Admin();
						$uba->deactivate_module( $module_name );
					}
				}
			}
		}

		/**
		 * Load signup password submodule.
		 *
		 * @since 1.9.5
		 */
		public function signup_password_init() {
			$value = $this->get_value( 'content', 'form_signup_password', 'off' );
			if ( 'on' !== $value ) {
				return;
			}
			$file = dirname( __FILE__ ) . '/signup-password.php';
			include_once $file;
			new Branda_Signup_Password();
		}

		/**
		 * Modify option name.
		 *
		 * @param string $option_name Option name.
		 * @param string $module      Module.
		 *
		 * @since 1.9.2
		 *
		 * @return string
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && preg_match( '/^login[-_]screen$/', $module ) ) {
				return $this->option_name;
			}
			return $option_name;
		}

		private function move_language_switcher() {
			ob_start();
			?>
			<style>
				.language-switcher {
					display: none;
				}
				form#language-switcher {
					margin-top: 40px;
				}
			</style>
			<script type="application/javascript">
				function login_page_loaded(callback) {
					if (document.readyState !== 'loading') {
						callback();
					} else {
						document.addEventListener('DOMContentLoaded', callback);
					}
				}

				login_page_loaded(function () {
					var switcher = jQuery('.language-switcher');
					if (switcher.length) {
						switcher.detach().appendTo('#login').show();
					}
				});
			</script>
			<?php
			echo ob_get_clean();
		}

		/**
		 * Output the login page content.
		 *
		 * Apply all the customization to the login
		 * page and echo them.
		 */
		public function output() {
			$this->proceed_gettext = true;
			$module_value          = $this->get_value();
			if ( 'empty' === $module_value || empty( $module_value ) ) {
				return;
			}
			$this->move_language_switcher();
			printf( '<style id="%s" type="text/css">', $this->get_name( 'css' ) );
			/**
			 * Logo style
			 */
			$this->css_logo_common( '#login h1', true, true );
			/**
			 * Form container
			 */
			echo '#login {';
			$value = $this->get_value( 'colors', 'form_container_background' );
			if ( ! empty( $value ) ) {
				echo $this->css_background_color( $value );
			}
			$value = $this->get_value( 'design', 'container_width' );
			if ( ! empty( $value ) ) {
				$units = $this->get_value( 'design', 'container_width_units', 'px' );
				printf(
					'width: %d%s;%s',
					$value,
					$units,
					PHP_EOL
				);
			}
			foreach ( $this->positions as $p ) {
				$value = $this->get_value( 'design', 'container_padding_' . $p, false );
				if ( false !== $value ) {
					$units = '';
					$value = intval( $value );
					if ( 0 !== $value ) {
						$units = $this->get_value( 'design', 'container_padding_units', 'px' );
					}
					printf( 'padding-%s: %d%s;%s', $p, $value, $units, PHP_EOL );
				}
			}
			echo '}';
			echo PHP_EOL;
			/**
			 * Form style start.
			 */
			echo '.login form {';
			echo $this->css_background_color( $this->get_value( 'colors', 'form_background' ) );
			// Form border.
			$value = intval( $this->get_value( 'design', 'form_border_width', 0 ) );
			printf( 'border-width: %dpx;', $value );
			if ( 0 < $value ) {
				$value = $this->get_value( 'design', 'form_border_style', 'solid' );
				printf( 'border-style: %s;', $value );
				$value = $this->get_value( 'colors', 'form_border_color', false );
				if ( ! empty( $value ) ) {
					printf( 'border-color: %s;', $value );
				}
			}
			// Form background.
			$value = $this->get_value( 'content', 'form_background_image_meta' );
			if ( is_array( $value ) ) {
				echo 'background-repeat: no-repeat;background-position: 50%;';
				printf( 'background-image: url("%s");', esc_url( $value[0] ) );
				$value = $this->get_value( 'design', 'form_background_size', 'none' );
				if ( 'none' !== $value ) {
					printf( 'background-size: %s;', esc_attr( $value ) );
				}
				echo PHP_EOL;
			}
			// Form: rounded.
			$value = intval( $this->get_value( 'design', 'form_rounded', 0 ) );
			if ( 0 < $value ) {
				echo $this->css_radius( $value );
				echo PHP_EOL;
			}
			// Form shadow.
			$value = $this->get_value( 'design', 'form_style' );
			if ( 'flat' === $value ) {
				echo 'box-shadow: none;';
			} else {
				$vertical   = intval( $this->get_value( 'design', 'form_shadow_x_offset' ) );
				$horizontal = intval( $this->get_value( 'design', 'form_shadow_y_offset' ) );
				$blur       = intval( $this->get_value( 'design', 'form_shadow_blur_offset' ) );
				$spread     = intval( $this->get_value( 'design', 'form_shadow_spread_offset' ) );
				$color      = $this->get_value( 'colors', 'form_shadow', 'rgba(0,0,0,0.13)' );
				$this->css_box_shadow( $vertical, $horizontal, $blur, $spread, $color );
			}
			$margins = $padings = array();
			foreach ( $this->positions as $p ) {
				$value = intval( $this->get_value( 'design', 'form_margin_' . $p, false ) );
				if ( 0 < $value ) {
					$units = $this->get_value( 'design', 'form_margin_units', 'px' );
					printf( 'margin-%s:%d%s;', $p, $value, $units );
				}
				$value = $this->get_value( 'design', 'form_padding_' . $p, false );
				if ( false !== $value ) {
					$units = $this->get_value( 'design', 'form_padding_units', 'px' );
					printf( 'padding-%s:%d%s;', $p, $value, $units );
				}
			}
			echo '}';
			echo PHP_EOL;
			/**
			 * Form style end.
			 */
			// Label styles.
			$this->css_color_from_data( 'colors', 'form_input_label', '.login form label' );
			$this->css_actions_colors(
				'colors',
				'form_input',
				array( '.login input[type=text]', '.login input[type=password]', '.login input[type=checkbox]' )
			);
			$this->css_actions_colors(
				'colors',
				'form_button',
				array( '.login input[type=submit]' )
			);
			/**
			 * Form buttons start.
			 */
			echo '.login form input.button.button-large{';
			// Form button shadow.
			$value = $this->get_value( 'design', 'form_button_shadow', 'shadow' );
			if ( preg_match( '/^(off|flat)$/', $value ) ) {
				echo '-webkit-box-shadow: none;';
				echo '-moz-box-shadow: none;';
				echo 'box-shadow: none;';
			}
			// Form button text shadow.
			$value = $this->get_value( 'design', 'form_button_text_shadow', 'shadow' );
			if ( preg_match( '/^(off|flat)$/', $value ) ) {
				echo 'text-shadow: none;';
			}
			// Form button border.
			$value = intval( $this->get_value( 'design', 'form_button_border_width', 1 ) );
			printf( 'border-width: %dpx;', $value );
			if ( 0 < $value ) {
				printf( 'height: %dpx;', 28 + 2 * $value );
				$value = $this->get_value( 'design', 'form_button_border_style', 'solid' );
				if ( 'solid' !== $value ) {
					printf( 'border-style: %s;', $value );
				}
			}
			// Form button radius.
			$value = intval( $this->get_value( 'design', 'form_button_rounded', 0 ) );
			echo $this->css_radius( $value );
			echo '}';
			echo PHP_EOL;
			/**
			 * End of form button.
			 */
			$map = array(
				'register' => '#nav',
				'back'     => '#backtoblog',
				'policy'   => '.privacy-policy-page-link',
			);
			foreach ( $map as $n => $selector ) {
				$name  = sprintf( 'content_show_%s', $n );
				$value = $this->get_value( 'content', $name, 'on' );
				if ( 'off' === $value ) {
					printf( '%s {display: none;}', $selector );
					echo PHP_EOL;
					unset( $map[ $n ] );
				}
			}
			echo '#backtoblog, #nav, .privacy-policy-page-link{';
			$value = $this->get_value( 'design', 'links_below_form_aligment', 'left' );
			printf( 'text-align: %s;', esc_attr( $value ) );
			echo '}';
			echo PHP_EOL;
			$keys = array( '', 'focus', 'hover', 'active' );
			foreach ( $map as $n => $selector ) {
				foreach ( $keys as $key ) {
					$subclass = ( $key ? ':' : '' ) . $key;
					$name     = sprintf(
						'links_below_form_%s%s%s',
						$n,
						$key ? '_' : '',
						$key
					);
					$value    = $this->get_value( 'colors', $name );
					if ( empty( $value ) ) {
						continue;
					}
					if ( empty( $key ) ) {
						echo sprintf( '.login %s, ', $selector );
					}
					printf( '.login %s a%s {', $selector, esc_attr( $subclass ) );
					printf( 'color: %s;', $value );
					echo '}';
					echo PHP_EOL;
				}
			}
			$value = $this->get_value( 'colors', 'form_input_border_focus' );
			// Input border color focus.
			if ( ! empty( $value ) ) {
				$shadow = $this->convert_hex_to_rbg( $value );
				if ( is_array( $shadow ) ) {
					$shadow = implode( ',', $shadow ) . ',0.8';
					?>
					.login form input[type=text]:focus,
					.login form input[type=password]:focus,
					.login form input[type=checkbox]:focus,
					.login form input[type=submit]:focus
					{
					border-color:<?php echo esc_attr( $value ); ?>;
					-webkit-box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
					-moz-box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
					box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
					}
					<?php
				}
			}
			// Form background color and transparency.
			$this->css_background_transparency( 'colors', 'form_background', 'form_bg_transparency', '.login form' );
			// Form button color.
			$this->css_background_color_from_data( 'colors', 'form_button_background', '.login form .button' );
			// Form button text color.
			$this->css_color_from_data( 'colors', 'form_button_label', '.login form .button' );
			$this->css_color_from_data( 'colors', 'form_button_background', '.login form .button.wp-hide-pw' );
			// Form button states: active, focus, hover.
			$button_keys = array( 'focus', 'hover', 'active' );
			foreach ( $button_keys as $button_key ) {
				// Background Color.
				$bkey   = sprintf( 'form_button_background_%s', $button_key );
				$bvalue = sprintf( '.login form .button:%s', $button_key );
				$this->css_background_color_from_data( 'colors', $bkey, $bvalue );
				$hide_icon = sprintf( '.login form .button.wp-hide-pw:%s', $button_key );
				$this->css_color_from_data( 'colors', 'form_button_background', $hide_icon );
				// Color.
				$bkey = sprintf( 'form_button_label_%s', $button_key );
				$this->css_color_from_data( 'colors', $bkey, $bvalue );
			}
			// Show/hide remember me.
			$v = $this->get_value( 'content' );
			$this->css_hide( $v, 'form_show_remember_me', '.login .forgetmenot' );
			// Form radius.
			$value = intval( $this->get_value( 'design', 'form_button_rounded', 3 ) );
			echo '.login form input[type=submit] {';
			echo $this->css_radius( $value );
			echo '}';
			// Login error background color.
			$this->css_background_color_from_data( 'colors', 'error_messages_background', '.login #login #login_error' );
			// Login error border color.
			$value = $this->get_value( 'colors', 'error_messages_border' );
			if ( ! empty( $value ) ) {
				?>
				.login #login #login_error {
				border-color: <?php echo $value; ?>;
				}
				<?php
			}
			// Login error text color.
			$this->css_color_from_data( 'colors', 'error_messages_text', '.login #login #login_error' );
			$this->css_color_from_data( 'colors', 'error_messages_link', '.login #login #login_error a' );
			$this->css_color_from_data( 'colors', 'error_messages_link_hover', '.login #login #login_error a:hover' );
			$this->css_color_from_data( 'colors', 'error_messages_link_active', '.login #login #login_error a:active' );
			$this->css_color_from_data( 'colors', 'error_messages_link_focus', '.login #login #login_error a:focus' );
			// $this->css_opacity( $v, 'login_error_transarency', '.login #login #login_error' );
			// Below form elements.
			$value = isset( $module_value['content'] ) ? $module_value['content'] : '';
			// Show register link and forgot pass link.
			$this->css_hide( $value, 'content_show_register', '.login #nav' );
			// Link colors.
			$this->css_color_from_data( 'colors', 'links_below_form_register', '.login #nav a' );
			// Link hover actions.
			$this->css_color_from_data( 'colors', 'links_below_form_register_hover', '.login #nav a:hover' );
			// "Back to" link.
			$this->css_hide( $value, 'content_show_back', '.login #backtoblog' );
			$this->css_color_from_data( 'colors', 'links_below_form_back', '.login #backtoblog a' );
			$this->css_color_from_data( 'colors', 'links_below_form_back_hover', '.login #backtoblog a:hover' );
			// "Privacy Policy" link.
			$this->css_hide( $value, 'content_show_policy', '.login .privacy-policy-page-link' );
			$this->css_color_from_data( 'colors', 'links_below_form_policy', '.login .privacy-policy-page-link a' );
			$this->css_color_from_data( 'colors', 'links_below_form_policy_hover', '.login .privacy-policy-page-link a:hover' );
			// Canvas.
			echo '.branda-login {';
			// Canvas margins & paddings.
			$margins = $padings = array();
			foreach ( $this->positions as $p ) {
				$value = $this->get_value( 'design', 'canvas_margin_' . $p, false );
				if ( false !== $value ) {
					$margins[ $p ] = $value;
				}
				$default = false;
				if ( 'top' === $p ) {
					$default = 0;
				}
				$value = intval( $this->get_value( 'design', 'canvas_padding_' . $p, $default ) );
				if ( 0 < $value ) {
					$paddings[ $p ] = $value;
				}
			}
			$padding_units = $this->get_value( 'design', 'canvas_padding_units', '%' );
			$margin_units  = $this->get_value( 'design', 'canvas_margin_units', 'px' );
			/**
			 * Set vertical auto if both are '0'.
			 */
			if (
				isset( $margins['left'] )
				&& isset( $margins['right'] )
				&& 0 === $margins['left']
				&& 0 === $margins['right']
			) {
				$margins['left'] = $margins['right'] = 'auto';
			}
			foreach ( $this->positions as $p ) {
				if ( isset( $margins[ $p ] ) ) {
					if (
						preg_match( '/^(right|left)$/', $p )
						&& 0 === $margins[ $p ]
					) {
						continue;
					}
					/**
					 * Handle exception "auto" value.
					 */
					if ( 'auto' === $margins[ $p ] ) {
						printf( 'margin-%s:%s;', $p, $margins[ $p ] );
					} else {
						printf( 'margin-%s:%d%s;', $p, $margins[ $p ], $margin_units );
					}
				}
				if ( isset( $paddings[ $p ] ) ) {
					printf( 'padding-%s:%d%s;', $p, $paddings[ $p ], $padding_units );
				}
			}
			// Canvas width.
			$value = $this->get_value( 'design', 'canvas_width', 100 );
			if ( ! empty( $value ) ) {
				$units = $this->get_value( 'design', 'canvas_width_units', '%' );
				printf( 'width:%d%s;', $value, $units );
			}
			// Canvas fit.
			$absolute_already_print = false;
			$value                  = $this->get_value( 'design', 'canvas_fit', 'off' );
			if ( 'on' === $value ) {
				echo 'position:absolute;';
				echo 'top:0;';
				echo 'bottom:0;';
				$absolute_already_print = true;
			}
			// Canvas position.
			$value = $this->get_value( 'design', 'canvas_position', 'default' );
			if ( preg_match( '/^(left|right)$/', $value ) ) {
				if ( ! $absolute_already_print ) {
					echo 'position:absolute;';
				}
				printf( '%s:0;', $value );
			}
			// Canvas background.
			$value = $this->get_value( 'colors', 'canvas_background', false );
			if ( ! empty( $value ) ) {
				printf( 'background-color:%s;', $value );
			}
			echo '}';
			echo PHP_EOL;
			echo '</style>';
			echo PHP_EOL;
			// Custom CSS.
			$v = $this->get_value( 'css', 'css' );
			if ( ! empty( $v ) ) {
				if ( ! preg_match( '/<style/', $v ) ) {
					printf( '<style type="text/css" id="%s-custom-css">', esc_attr( __CLASS__ ) );
					echo PHP_EOL;
				}
				echo stripslashes( $v );
				if ( ! preg_match( '/<\/style/', $v ) ) {
					echo PHP_EOL;
					echo '</style>';
				}
				echo PHP_EOL;
			}
			/**
			 * Common Background
			 *
			 * @since 2.3.0
			 */
			$this->css_background_common( 'body' );
			$args     = array(
				'colors' => $this->get_value( 'colors' ),
				'design' => $this->get_value( 'design' ),
				'id'     => $this->get_name(),
			);
			$template = $this->get_template_name( 'css', 'front-end/modules' );
			$this->render( $template, $args );
		}

		/**
		 * Set admin option page fields.
		 */
		protected function set_options() {
			// Set options.
			$options = array(
				'theme'    => array(
					'title'       => __( 'Template', 'ub' ),
					'description' => __( 'Customize one of our pre-designed templates, or start styling login page from scratch.', 'ub' ),
					'fields'      => array(
						'id' => array(
							'type'     => 'callback',
							'callback' => array( $this, 'get_template_configuration' ),
						),
					),
				),
				'content'  => array(
					'title'       => __( 'Content', 'ub' ),
					'description' => __( 'Adjust the default content of the login screen.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields(
						'content',
						array(
							'language',
							'logo',
							'message',
							'form',
							'error_messages',
							'links_below_form',
							'reset',
						)
					),
				),
				'design'   => array(
					'title'       => __( 'Design', 'ub' ),
					'description' => __( 'Adjust the default design of the login screen.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields(
						'design',
						array(
							'logo',
							'background',
							'message',
							'form',
							'error_messages',
							'links_below_form',
							'canvas',
							'container',
							'reset',
						)
					),
				),
				'colors'   => array(
					'title'       => __( 'Colors', 'ub' ),
					'description' => __( 'Adjust the default colour combinations as per your liking.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields(
						'colors',
						array(
							'background',
							'form',
							'error_messages',
							'links_below_form',
							'canvas',
							'reset',
						)
					),
				),
				// Redirects.
				'redirect' => array(
					'title'       => __( 'Redirection', 'ub' ),
					'description' => __( 'Choose where do you want to redirect users after the successful login or logout.', 'ub' ),
					'fields'      => array(
						'login_url'  => array(
							'type'         => 'url',
							'label'        => __( 'After login redirect URL', 'ub' ),
							'master'       => $this->get_name( 'login-related' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
							'description'  => array(
								'content'  => __( 'You can use relative or absolute internal URLs.', 'ub' ),
								'position' => 'bottom',
							),
						),
						'login'      => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Login', 'ub' ),
							'description' => __( 'Choose the URL where you want the users to redirect after successful login.', 'ub' ),
							'options'     => array(
								'off' => __( 'Default', 'ub' ),
								'on'  => __( 'Custom', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'login-related' ),
						),
						'logout_url' => array(
							'type'         => 'url',
							'label'        => __( 'After logout redirect URL', 'ub' ),
							'master'       => $this->get_name( 'logout-related' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
							'description'  => array(
								'content'  => __( 'You can use relative or absolute URLs, and the redirect can be both to an internal or external page.', 'ub' ),
								'position' => 'bottom',
							),
						),
						'logout'     => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Logout', 'ub' ),
							'description' => __( 'Choose the URL where you want the users to redirect after successful logout.', 'ub' ),
							'options'     => array(
								'off' => __( 'Default', 'ub' ),
								'on'  => __( 'Custom', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'logout-related' ),
						),
					),
				),
				'css'      => $this->get_custom_css_array(
					array(
						'extra_description' => __( 'This will be added to the header of every Login page.', 'ub' ),
						'ace_selectors'     => $this->get_ace_selectors(),
					)
				),
			);
			$this->options = $options;
		}

		/**
		 * Login header url.
		 *
		 * @param string $value Header url.
		 *
		 * @return mixed|null|string
		 */
		public function login_header_url( $value ) {
			$new = $this->get_value( 'content', 'logo_url' );
			if ( null === $new ) {
				return $value;
			}
			return $new;
		}

		/**
		 * Login header title.
		 *
		 * @param string $value Header title.
		 *
		 * @return mixed|null|string
		 */
		public function login_header_text( $value ) {
			$new = $this->get_value( 'content', 'logo_alt' );
			if ( null === $new ) {
				return $value;
			}
			return $new;
		}

		/**
		 * Handle login page errors.
		 *
		 * @param object $errors Error object.
		 *
		 * @return mixed
		 */
		public function wp_login_errors( $errors ) {
			if ( ! empty( $_POST ) ) {
				if ( 'incorrect_password' == $errors->get_error_code() ) {
					$value = $this->get_value( 'content', 'content_incorrect_password' );
					if ( ! empty( $value ) ) {
						$errors->remove( 'incorrect_password' );
						$errors->add( 'incorrect_password', $value, 'error' );
					}
				}
				if ( isset( $_POST['log'] ) && empty( $_POST['log'] ) ) {
					$value = $this->get_value( 'content', 'content_empty_username' );
					if ( ! empty( $value ) ) {
						$errors->remove( 'empty_username' );
						$errors->add( 'empty_username', $value, 'error' );
					}
				}
				if ( isset( $_POST['pwd'] ) && empty( $_POST['pwd'] ) ) {
					$value = $this->get_value( 'content', 'content_empty_password' );
					if ( ! empty( $value ) ) {
						$errors->remove( 'empty_password' );
						$errors->add( 'empty_password', $value, 'error' );
					}
				}
			}
			$value = $this->get_value( 'content' );
			if ( is_array( $value ) ) {
				foreach ( $value as $code => $message ) {
					// Get the error message key.
					$code = str_replace( 'content_', '', $code );
					if ( isset( $errors->errors[ $code ] ) ) {
						$errors->errors[ $code ][0] = stripslashes( $this->replace_placeholders( $message, $code ) );
					}
				}
			}
			return $errors;
		}

		/**
		 * Translated text.
		 *
		 * @param string $translated_text Translated text.
		 * @param string $text            Actual text.
		 * @param string $domain          Text domain.
		 *
		 * @return string
		 */
		public function gettext_login_form_labels( $translated_text, $text, $domain ) {
			if ( $this->proceed_gettext && 'default' == $domain ) {
				if ( empty( $this->patterns ) ) {
					$options = $this->options['content'];
					foreach ( $options['fields'] as $key => $data ) {
						if ( preg_match( '/^form_label_/', $key ) ) {
							$this->patterns[ $data['default'] ] = $this->get_value( 'content', $key );
						}
					}
				}
				if ( isset( $this->patterns[ $translated_text ] ) ) {
					return stripslashes( $this->patterns[ $translated_text ] );
				}
			}
			return $translated_text;
		}

		/**
		 * Replace place holders.
		 *
		 * @param string $string Text.
		 * @param string $code   Error code.
		 *
		 * @return mixed|string
		 */
		private function replace_placeholders( $string, $code = '' ) {
			/**
			 * Exception for user name
			 * https://app.asana.com/0/47431170559378/47431170559399
			 */
			if ( 'incorrect_password' === $code ) {
				$string = sprintf( $string, 'USERNAME' );
			}
			$lost_password_url = wp_lostpassword_url();
			$string            = preg_replace( '/WP_LOSTPASSWORD_URL/', $lost_password_url, $string );
			$username          = '';
			if ( isset( $_POST['log'] ) ) {
				$username = esc_attr( $_POST['log'] );
			}
			$string = preg_replace( '/USERNAME/', $username, $string );
			return $string;
		}

		/**
		 * Get theme dirs for login themes
		 *
		 * @return array
		 */
		private static function get_theme_dirs() {
			$theme_root = dirname( __FILE__ ) . '/themes/';
			$dirs       = array();
			$dir        = dir( $theme_root );
			while ( false !== ( $entry = $dir->read() ) ) {
				if (
					'.' === $entry
					|| '..' === $entry
					|| ! is_dir( $theme_root . '/' . $entry )
				) {
					continue;
				}
				$dirs[] = $entry;
			}

			return $dirs;
		}

		/**
		 * Get themes array.
		 *
		 * Based on wp-includes/theme.php search_theme_directories() function.
		 *
		 * @since 1.8.9
		 */
		private function get_themes() {
			$found_themes = array();
			$file_headers = array(
				'Name'        => 'Theme Name',
				'ThemeURI'    => 'Theme URI',
				'Description' => 'Description',
				'Author'      => 'Author',
				'AuthorURI'   => 'Author URI',
				'Version'     => 'Version',
			);
			$theme_root   = dirname( __FILE__ ) . '/themes/';
			$dirs         = self::get_theme_dirs();
			foreach ( $dirs as $dir ) {
				if ( ! is_dir( $theme_root . '/' . $dir ) || '.' === $dir[0] || 'CVS' === $dir ) {
					continue;
				}
				if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
					$found_themes[ $dir ] = array(
						'id'         => sanitize_title( $dir ),
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
					foreach ( array( 'png', 'gif', 'jpg', 'jpeg' ) as $ext ) {
						$file = $theme_root . $dir . "/screenshot.$ext";
						if ( file_exists( $file ) ) {
							$found_themes[ $dir ]['screenshot'] = plugins_url( 'themes/' . $dir . "/screenshot.$ext", $theme_root );
						}
					}
					$data = get_file_data( $theme_root . $dir . '/style.css', $file_headers, 'theme' );
					if ( is_array( $data ) ) {
						$found_themes[ $dir ] = array_merge( $found_themes[ $dir ], $data );
					}
				}
			}
			uasort( $found_themes, array( $this, 'sort_themes_by_name' ) );
			$scratch = array(
				'scratch' => array(
					'id'   => 'start-from-scratch',
					'Name' => __( 'Start from scratch', 'ub' ),
				),
			);
			$themes  = array_merge( $scratch, $found_themes );
			foreach ( $themes as $k => $value ) {
				$themes[ $k ]['branda_id'] = $this->get_name( $k );
			}
			return $themes;
		}

		/**
		 * Sort Themes by name helper function.
		 *
		 * @since 3.0.0
		 */
		private function sort_themes_by_name( $a, $b ) {
			return strcmp( $a['Name'], $b['Name'] );
		}

		/**
		 * Import theme data.
		 *
		 * @param string $id Theme ID.
		 *
		 * @since 1.8.9
		 *
		 * @return string
		 */
		private function set_theme( $id ) {
			$data = $this->get_theme_data( $id );
			$this->update_value( $data );
			return $data;
		}

		/**
		 * Get theme data by id
		 *
		 * @since 3.3.1
		 * @param string $id
		 * @return array
		 */
		private function get_theme_data( $id ) {
			$themes     = $this->get_themes();
			$theme      = $themes[ $id ];
			$theme_root = dirname( __FILE__ ) . '/themes/';
			$theme_dirs = self::get_theme_dirs();
			if ( in_array( $id, $theme_dirs, true ) ) {
				$data = include_once $theme_root . $id . '/index.php';
			}
			if ( empty( $data ) ) {
				$message = sprintf(
					__( 'Failed to load "%s" template configuration!', 'ub' ),
					$theme['Name']
				);
				$this->json_error( $message );
			}
			$data['theme'] = $theme;

			return $data;
		}

		/**
		 * Set remember me option.
		 *
		 * @param object $errors Errors.
		 *
		 * @since 1.9.4
		 *
		 * @return object
		 */
		public function set_remember_me( $errors ) {
			$value = $this->get_value( 'content', 'form_check_remember_me' );
			if ( 'on' === $value ) {
				$_POST['rememberme'] = 1;
			}
			return $errors;
		}

		/**
		 * Set login redirect.
		 *
		 * @param string $redirect_to Redirect to url.
		 *
		 * @since 1.9.4
		 *
		 * @return string
		 */
		public function login_redirect( $redirect_to ) {
			$value = $this->get_value( 'redirect', 'login' );
			if ( 'on' === $value ) {
				$value = $this->get_value( 'redirect', 'login_url' );
				if ( ! empty( $value ) ) {
					$value       = $this->get_full_internal_link( $value );
					$redirect_to = $this->add_http_if_is_missing( $value );
				}
			}
			return $redirect_to;
		}

		/**
		 * Set logout redirect.
		 *
		 * @param string $redirect_to Redirect url.
		 *
		 * @since 1.9.4
		 *
		 * @return string
		 */
		public function logout_redirect( $redirect_to ) {
			$value = $this->get_value( 'redirect', 'logout' );
			if ( 'on' === $value ) {
				$value = $this->get_value( 'redirect', 'logout_url' );
				if ( ! empty( $value ) ) {
					$value       = $this->get_full_internal_link( $value );
					$redirect_to = $this->add_http_if_is_missing( $value );
					wp_redirect( $redirect_to );
					exit();
				}
			}
			return $redirect_to;
		}

		/**
		 * Upgrade options to new structure.
		 *
		 * @since 2.3.0
		 */
		public function upgrade_options() {
			$update = false;
			$value  = $this->get_value();
			if ( empty( $value ) ) {
				return;
			}
			/**
			 * Check we have plugin_version in saved data
			 */
			if ( isset( $value['plugin_version'] ) ) {
				/**
				 * do not run again big upgrade if config was saved by Branda
				 */
				$version_compare = version_compare( $value['plugin_version'], '3.0.0' );
				if ( -1 < $version_compare ) {
					return;
				}
				return;
			}
			// Convert 'logo_and_background' section into 'logo'.
			if ( isset( $value['logo_and_background'] ) ) {
				if ( ! isset( $value['background'] ) || ! is_array( $value['background'] ) ) {
					$value['background'] = array();
				}
				if (
					isset( $value['logo_and_background'] )
					&& isset( $value['logo_and_background']['bg_color'] )
				) {
					$value['background']['color'] = $value['logo_and_background']['bg_color'];
					unset( $value['logo_and_background']['bg_color'] );
					$update = true;
				}
				if (
					isset( $value['logo_and_background'] )
					&& isset( $value['logo_and_background']['fullscreen_bg'] )
				) {
					$value['background']['image'] = $value['logo_and_background']['fullscreen_bg'];
					unset( $value['logo_and_background']['fullscreen_bg'] );
					$update = true;
				}
				$value['logo'] = $value['logo_and_background'];
				unset( $value['logo_and_background'] );
				$update = true;
			}
			// Convert logo section.
			if ( isset( $value['logo'] ) ) {
				$translate = array(
					'show_logo'          => 'show',
					'logo_upload'        => 'image',
					'logo_upload_meta'   => 'image_meta',
					'logo_width'         => 'width',
					'logo_transparency'  => 'transparency',
					'logo_rounded'       => 'rounded',
					'logo_bottom_margin' => 'bottom_margin',
					'login_header_url'   => 'url',
					'login_header_title' => 'alt',
					'logo_bottom_margin' => 'margin_bottom',
				);
				foreach ( $translate as $old => $new ) {
					if ( isset( $value['logo'][ $old ] ) ) {
						$value['logo'][ $new ] = $value['logo'][ $old ];
						unset( $value['logo'][ $old ] );
						$update = true;
					}
				}
			}
			if ( $update ) {
				$this->update_value( $value );
			}
			// New data @since 3.0.0.
			$data = array(
				'updated'  => time(),
				'redirect' => array(),
				'content'  => array(),
				'design'   => array(),
				'colors'   => array(),
			);
			// Common data migration.
			$data = $this->common_upgrade_options( $data, $value );
			/**
			 * convert themes url
			 */
			if ( isset( $data['content'] ) ) {
				if ( isset( $data['content']['logo_image'] ) ) {
					$data['content']['logo_image'] = $this->convert_old_themes_path( $data['content']['logo_image'] );
				}
				if (
					isset( $data['content']['logo_image_meta'] )
					&& is_array( $data['content']['logo_image_meta'] )
				) {
					$data['content']['logo_image_meta'][0] = $this->convert_old_themes_path( $data['content']['logo_image_meta'][0] );
				}
				if ( isset( $data['content']['content_background'] ) ) {
					foreach ( $data['content']['content_background'] as $i => $one ) {
						if ( isset( $one['meta'] ) && isset( $one['meta'][0] ) ) {
							$data['content']['content_background'][ $i ]['meta'][0] = $this->convert_old_themes_path( $one['meta'][0] );
						}
					}
				}
			}
			// Redirects.
			if ( isset( $value['redirect'] ) ) {
				$data['redirect'] = $value['redirect'];
			}
			// Settings.
			if ( isset( $value['settings'] ) ) {
				$v = $value['settings'];
				if ( isset( $v['locale'] ) ) {
					$data['content']['locale'] = $v['locale'];
				}
			}
			// Form data.
			if ( isset( $value['form'] ) ) {
				$v = $value['form'];
				if ( isset( $v['rounded_nb'] ) ) {
					$data['design']['form_rounded'] = $v['rounded_nb'];
				}
				if ( isset( $v['signup_password'] ) ) {
					$data['content']['form_signup_password'] = $v['signup_password'];
				}
				if ( isset( $v['label_color'] ) ) {
					$data['colors']['form_input_label'] = $v['label_color'];
				}
				if ( isset( $v['input_border_color_focus'] ) ) {
					$data['colors']['form_input_border_focus'] = $v['input_border_color_focus'];
				}
				if ( isset( $v['form_bg_color'] ) ) {
					$color = $v['form_bg_color'];
					if ( isset( $v['form_bg_transparency'] ) ) {
						$color = sprintf(
							'rgba(%s,%.2f)',
							implode( ',', $this->convert_hex_to_rbg( $color ) ),
							$v['form_bg_transparency'] / 100
						);
					}
					$data['colors']['form_background'] = $color;
				}
				if ( isset( $v['form_bg'] ) ) {
					$data['content']['form_background_image'] = $v['form_bg'];
				}
				if ( isset( $v['form_bg_meta'] ) ) {
					$data['content']['form_background_image_meta'] = $v['form_bg_meta'];
				}
				if ( isset( $v['form_style'] ) ) {
					$data['design']['form_style'] = $v['form_style'];
				}
				if ( isset( $v['form_button_color'] ) ) {
					$data['colors']['form_button_background'] = $v['form_button_color'];
				}
				if ( isset( $v['form_button_text_color'] ) ) {
					$data['colors']['form_button_label'] = $v['form_button_text_color'];
				}
				if ( isset( $v['form_button_color_active'] ) ) {
					$data['colors']['form_button_background_active'] = $v['form_button_color_active'];
				}
				if ( isset( $v['form_button_text_color_active'] ) ) {
					$data['colors']['form_button_label_active'] = $v['form_button_text_color_active'];
				}
				if ( isset( $v['form_button_color_focus'] ) ) {
					$data['colors']['form_button_background_focus'] = $v['form_button_color_focus'];
				}
				if ( isset( $v['form_button_text_color_focus'] ) ) {
					$data['colors']['form_button_label_focus'] = $v['form_button_text_color_focus'];
				}
				if ( isset( $v['form_button_color_hover'] ) ) {
					$data['colors']['form_button_background_hover'] = $v['form_button_color_hover'];
				}
				if ( isset( $v['form_button_text_color_hover'] ) ) {
					$data['colors']['form_button_label_hover'] = $v['form_button_text_color_hover'];
				}
				if ( isset( $v['form_button_border'] ) ) {
					$data['design']['form_button_border_width'] = $v['form_button_border'];
				}
				if ( isset( $v['form_button_rounded'] ) ) {
					$data['design']['form_button_rounded'] = $v['form_button_rounded'];
				}
				if ( isset( $v['show_remember_me'] ) ) {
					$data['content']['form_show_remember_me'] = $v['show_remember_me'];
				}
				if ( isset( $v['check_remember_me'] ) ) {
					$data['content']['form_check_remember_me'] = $v['check_remember_me'];
				}
				if ( isset( $v['form_button_text_shadow'] ) ) {
					$data['design']['form_button_text_shadow'] = $v['form_button_text_shadow'];
				}
				if ( isset( $v['form_button_shadow'] ) ) {
					$data['design']['form_button_shadow'] = $v['form_button_shadow'];
				}
			}
			// Form labels.
			if ( isset( $value['form_labels'] ) ) {
				$v = $value['form_labels'];
				if ( isset( $v['label_username'] ) ) {
					$data['content']['form_label_username'] = $v['label_username'];
				}
				if ( isset( $v['label_password'] ) ) {
					$data['content']['form_label_password'] = $v['label_password'];
				}
				if ( isset( $v['label_log_in'] ) ) {
					$data['content']['form_label_log_in'] = $v['label_log_in'];
				}
			}
			// Form errors.
			if ( isset( $value['form_errors'] ) ) {
				$v = $value['form_errors'];
				if ( isset( $v['empty_username'] ) ) {
					$data['content']['content_empty_username'] = $v['empty_username'];
				}
				if ( isset( $v['invalid_username'] ) ) {
					$data['content']['content_invalid_username'] = $v['invalid_username'];
				}
				if ( isset( $v['empty_password'] ) ) {
					$data['content']['content_empty_password'] = $v['empty_password'];
				}
				if ( isset( $v['incorrect_password'] ) ) {
					$data['content']['content_incorrect_password'] = $v['incorrect_password'];
				}
				if ( isset( $v['login_error_background_color'] ) ) {
					$data['colors']['error_messages_background'] = $v['login_error_background_color'];
				}
				if ( isset( $v['login_error_border_color'] ) ) {
					$data['colors']['error_messages_border'] = $v['login_error_border_color'];
				}
				if ( isset( $v['login_error_text_color'] ) ) {
					$data['colors']['error_messages_text'] = $v['login_error_text_color'];
				}
				if ( isset( $v['login_error_link_color'] ) ) {
					$data['colors']['error_messages_link'] = $v['login_error_link_color'];
				}
				if ( isset( $v['login_error_link_color_hover'] ) ) {
					$data['colors']['error_messages_link_hover'] = $v['login_error_link_color_hover'];
				}
				if ( isset( $v['login_error_transarency'] ) ) {
					$data['design']['form_messages_opacity'] = $v['login_error_transarency'];
				}
			}
			// Below form elements.
			if ( isset( $value['below_form'] ) ) {
				$v = $value['below_form'];
				if ( isset( $v['show_register_and_lost'] ) ) {
					$data['content']['content_show_register'] = $v['show_register_and_lost'];
				}
				if ( isset( $v['register_and_lost_color_link'] ) ) {
					$data['colors']['links_below_form_register'] = $v['register_and_lost_color_link'];
				}
				if ( isset( $v['register_and_lost_color_hover'] ) ) {
					$data['colors']['links_below_form_register_hover'] = $v['register_and_lost_color_hover'];
				}
				if ( isset( $v['show_back_to'] ) ) {
					$data['content']['content_show_back'] = $v['show_back_to'];
				}
				if ( isset( $v['back_to_color_link'] ) ) {
					$data['colors']['links_below_form_back'] = $v['back_to_color_link'];
				}
				if ( isset( $v['back_to_color_hover'] ) ) {
					$data['colors']['links_below_form_back_hover'] = $v['back_to_color_hover'];
				}
				if ( isset( $v['show_privacy'] ) ) {
					$data['content']['content_show_policy'] = $v['show_privacy'];
				}
				if ( isset( $v['privacy_color_link'] ) ) {
					$data['colors']['links_below_form_policy'] = $v['privacy_color_link'];
				}
				if ( isset( $v['privacy_color_hover'] ) ) {
					$data['colors']['links_below_form_policy_hover'] = $v['privacy_color_hover'];
				}
			}
			// Canvas.
			if ( isset( $value['form_canvas'] ) ) {
				$v = $value['form_canvas'];
				if ( isset( $v['position'] ) ) {
					$data['design']['canvas_position'] = $v['position'];
				}
				if ( isset( $v['padding_top'] ) ) {
					$data['design']['canvas_padding_top'] = $v['padding_top'];
				}
				if ( isset( $v['width'] ) ) {
					$data['design']['canvas_width']        = $v['width'];
					$data['design']['canvas_width_units']  = 'px';
					$data['design']['canvas_margin_right'] = 'auto';
					$data['design']['canvas_margin_left']  = 'auto';
				}
				if ( isset( $v['form_margin'] ) ) {
					$data['design']['form_margin_top']    = 0;
					$data['design']['form_margin_right']  = $v['form_margin'];
					$data['design']['form_margin_bottom'] = 0;
					$data['design']['form_margin_left']   = $v['form_margin'];
				}
				if ( isset( $v['fit'] ) ) {
					$data['design']['canvas_fit'] = $v['fit'];
				}
				if ( isset( $v['background_color'] ) ) {
					$color = $v['background_color'];
					if (
						! empty( $color )
						&& isset( $v['background_transparency'] )
					) {
						$hex = $this->convert_hex_to_rbg( $color );
						if ( is_array( $hex ) ) {
							$color = sprintf(
								'rgba(%s,%.2f)',
								implode( ',', $hex ),
								( 100 - $v['background_transparency'] ) / 100
							);
						}
					}
					$data['colors']['canvas_background'] = $color;
				}
			}
			$this->update_value( $data );
		}

		/**
		 * Force language on login form.
		 *
		 * @since 2.3.0
		 */
		public function set_language_on_login_form() {
			$pages = array(
				'wp-login.php',
				'wp-register.php',
				'wp-signup.php',
			);
			if ( in_array( $GLOBALS['pagenow'], $pages ) ) {
				add_filter( 'locale', array( $this, 'set_locale' ), 11 );
			}
		}

		/**
		 * Set locale for the page.
		 *
		 * @param string $locale Current locale.
		 *
		 * @since 2.3.0
		 *
		 * @return string
		 */
		public function set_locale( $locale ) {
			$value = $this->get_value( 'content', 'locale', 'default' );
			if ( ! empty( $value ) && 'default' !== $value ) {
				return $value;
			}
			return $locale;
		}

		/**
		 * Options: Content -> Language.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_content_language( $defaults = array() ) {
			/**
			 * Languages.
			 *
			 * @since 2.3.0
			 */
			$languages = array(
				'default' => __( 'Site Default', 'ub' ),
			);
			$l         = get_available_languages();
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';
			$translations = wp_get_available_translations();
			foreach ( $l as $locale ) {
				if ( isset( $translations[ $locale ] ) ) {
					$translation                           = $translations[ $locale ];
					$languages[ $translation['language'] ] = $translation['native_name'];
				} else {
					$languages[ $locale ] = $locale;
				}
			}
			if ( 2 > count( $languages ) ) {
				return array();
			}
			$data = array(
				'locale' => array(
					'type'      => 'select',
					'label'     => __( 'Use', 'ub' ),
					'options'   => $languages,
					'default'   => 'default',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Language', 'ub' ),
						'end'   => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Content -> Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_content_form( $defaults = array() ) {
			$data = array(
				'form_label_username'    => array(
					'type'      => 'text',
					'label'     => __( 'Username input label', 'ub' ),
					'default'   => __( 'Username or Email Address', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'form_label_password'    => array(
					'type'    => 'text',
					'label'   => __( 'Password input label', 'ub' ),
					'default' => __( 'Password', 'ub' ),
				),
				'form_label_log_in'      => array(
					'type'    => 'text',
					'label'   => __( 'Login button text', 'ub' ),
					'default' => __( 'Log In', 'ub' ),
				),
				'form_check_remember_me' => array(
					'label'        => __( 'Default value', 'ub' ),
					'type'         => 'sui-tab',
					'options'      => array(
						'off' => __( 'Unchecked', 'ub' ),
						'on'  => __( 'Checked', 'ub' ),
					),
					'default'      => 'off',
					'master'       => $this->get_name( 'remember-me-related' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'form_show_remember_me'  => array(
					'type'        => 'sui-tab',
					'label'       => __( '"Remember Me" checkbox', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'on',
					'slave-class' => $this->get_name( 'remember-me-related' ),
				),
				'password_notice'        => array(
					'type'         => 'description',
					'value'        => Branda_Helper::sui_notice(
						__( 'Note: This setting adds the password field to the default WP login form. If you use a custom login form, add the password field from the forms editor.', 'ub' ),
						'default'
					),
					'master'       => $this->get_name( 'form_signup_password' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'form_signup_password'   => array(
					'label'       => __( 'Password field on register screen', 'ub' ),
					'type'        => 'sui-tab',
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'off',
					'slave-class' => $this->get_name( 'form_signup_password' ),
				),
				'form_background_image'  => array(
					'type'      => 'media',
					'label'     => __( 'Background image', 'ub' ),
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Content -> Error Messages.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_content_error_messages( $defaults = array() ) {
			// Invalid username.
			$invalid_username  = __( '<strong>ERROR</strong>: Invalid username.', 'ub' );
			$invalid_username .= ' <a href="WP_LOSTPASSWORD_URL">';
			$invalid_username .= __( 'Lost your password?', 'ub' );
			$invalid_username .= '</a>';
			// Invalid password.
			$invalid_password  = __( '<strong>ERROR</strong>: The password you entered for the username %s is incorrect.', 'ub' );
			$invalid_password .= ' <a href="WP_LOSTPASSWORD_URL">';
			$invalid_password .= __( 'Lost your password?', 'ub' );
			$invalid_password .= '</a>';
			$data              = array(
				'content_empty_username'     => array(
					'type'      => 'textarea',
					'label'     => __( 'Empty username', 'ub' ),
					'default'   => __( '<strong>ERROR</strong>: The username field is empty.', 'ub' ),
					'accordion' => array(
						'begin'   => true,
						'title'   => __( 'Error Messages', 'ub' ),
						'classes' => array(
							'body' => array( $this->get_name( 'error-messages' ) ),
						),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'content_invalid_username'   => array(
					'type'        => 'textarea',
					'label'       => __( 'Invalid username', 'ub' ),
					'description' => __( 'Use "<strong>{WP_LOSTPASSWORD_URL}</strong>" placeholder to replace it by WordPress.', 'ub' ),
					'default'     => $invalid_username,
				),
				'content_empty_password'     => array(
					'type'    => 'textarea',
					'label'   => __( 'Empty password', 'ub' ),
					'default' => __( '<strong>ERROR</strong>: The password field is empty.', 'ub' ),
				),
				'content_incorrect_password' => array(
					'type'        => 'textarea',
					'label'       => __( 'Invalid password', 'ub' ),
					'description' => __( 'Use "<strong>{WP_LOSTPASSWORD_URL}</strong>", "USERNAME" placeholder to replace it by WordPress.', 'ub' ),
					'default'     => $invalid_password,
					'accordion'   => array(
						'end' => true,
					),
					'group'       => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Content -> Links Below Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_content_links_below_form( $defaults = array() ) {
			$data = array(
				'content_show_register' => array(
					'type'      => 'sui-tab',
					'label'     => __( '"Register | Lost your password?" links', 'ub' ),
					'options'   => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'   => 'on',
					'accordion' => array(
						'begin'   => true,
						'title'   => __( 'Links Below Form', 'ub' ),
						'classes' => array(
							'body' => array( $this->get_name( 'error-messages' ) ),
						),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'content_show_back'     => array(
					'type'    => 'sui-tab',
					'label'   => __( '"Back to" link', 'ub' ),
					'options' => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default' => 'on',
				),
				'content_show_policy'   => array(
					'type'      => 'sui-tab',
					'label'     => __( '"Privacy Policy" link', 'ub' ),
					'options'   => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'   => 'on',
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Design -> Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_design_form( $defaults = array() ) {
			$value  = $this->get_value( 'design', 'form_style' );
			$hidden = 'shadow' === $value ? '' : ' hidden';
			$data   = array(
				'form_style'               => array(
					'type'      => 'sui-tab',
					'label'     => __( 'Style', 'ub' ),
					'options'   => array(
						'flat'   => __( 'Flat', 'ub' ),
						'shadow' => __( 'Shadowed Box', 'ub' ),
					),
					'default'   => 'shadow',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
					'classes'   => array(
						$this->get_name( 'form-style' ),
					),
				),
				'form_shadow_x_offset'     => array(
					'type'         => 'number',
					'label'        => __( 'X offset', 'ub' ),
					'default'      => 0,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => sprintf(
						'<div class="sui-row %s%s"><div class="sui-col">',
						$this->get_name( 'form-style' ),
						esc_attr( $hidden )
					),
					'after_field'  => '</div>',
				),
				'form_shadow_y_offset'     => array(
					'type'         => 'number',
					'label'        => __( 'Y offset', 'ub' ),
					'default'      => 1,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_shadow_blur'         => array(
					'type'         => 'number',
					'label'        => __( 'Blur', 'ub' ),
					'default'      => 3,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_shadow_spread'       => array(
					'type'         => 'number',
					'label'        => __( 'Spread', 'ub' ),
					'default'      => 0,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'form_rounded'             => array(
					'type'         => 'number',
					'label'        => __( 'Corner radius', 'ub' ),
					'attributes'   => array( 'placeholder' => '20' ),
					'default'      => 0,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_button_rounded'      => array(
					'type'         => 'number',
					'label'        => __( 'Button radius corners', 'ub' ),
					'min'          => 0,
					'default'      => 3,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'form_padding_top'         => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'min'          => 0,
					'default'      => 26,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Padding', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'form_padding_units',
						'default'  => 'px',
					),
				),
				'form_padding_right'       => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'min'          => 0,
					'default'      => 24,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_padding_bottom'      => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'min'          => 0,
					'default'      => 46,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_padding_left'        => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'min'          => 0,
					'default'      => 24,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end' => true,
					),
				),
				'form_margin_top'          => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'min'          => 0,
					'default'      => 20,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Margin', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'form_margin_units',
					),
				),
				'form_margin_right'        => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_margin_bottom'       => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_margin_left'         => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end' => true,
					),
				),
				'form_border_width'        => array(
					'type'         => 'number',
					'label'        => __( 'Thickness', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Border', 'ub' ),
						'classes' => 'sui-border-frame',
					),
				),
				'form_border_style'        => array(
					'type'         => 'select',
					'label'        => __( 'Style', 'ub' ),
					'default'      => 'solid',
					'options'      => $this->css_border_options(),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end' => true,
					),
				),
				'form_button_shadow'       => array(
					'type'         => 'sui-tab',
					'label'        => __( 'Shadow', 'ub' ),
					'options'      => array(
						'flat'   => __( 'Flat', 'ub' ),
						'shadow' => __( 'Shadowed button', 'ub' ),
					),
					'default'      => 'shadow',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Button', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_button_text_shadow'  => array(
					'type'         => 'sui-tab',
					'label'        => __( 'Text shadow', 'ub' ),
					'options'      => array(
						'flat'   => __( 'Flat', 'ub' ),
						'shadow' => __( 'Shadowed text', 'ub' ),
					),
					'default'      => 'shadow',
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'form_button_border_width' => array(
					'type'         => 'number',
					'label'        => __( 'Thickness', 'ub' ),
					'min'          => 0,
					'default'      => 1,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'form_button_border_style' => array(
					'type'         => 'select',
					'label'        => __( 'Style', 'ub' ),
					'default'      => 'solid',
					'options'      => $this->css_border_options(),
					'group'        => array(
						'end' => true,
					),
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'form_background_size'     => array(
					'type'      => 'sui-tab',
					'label'     => __( 'Image fitting', 'ub' ),
					'options'   => array(
						'cover'   => __( 'Cover', 'ub' ),
						'fill'    => __( 'Fill', 'ub' ),
						'contain' => __( 'Contain', 'ub' ),
						'none'    => __( 'None', 'ub' ),
					),
					'default'   => 'cover',
					'group'     => array(
						'end' => true,
					),
					'accordion' => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Design -> Error Messages
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_design_error_messages( $defaults = array() ) {
			$data = array(
				'form_messages_opacity' => array(
					'type'        => 'number',
					'label'       => __( 'Opacity', 'ub' ),
					'min'         => 0,
					'max'         => 100,
					'default'     => 100,
					'after_label' => '%',
					'accordion'   => array(
						'begin' => true,
						'title' => __( 'Error Messages', 'ub' ),
						'end'   => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Design -> Links Below Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_design_links_below_form( $defaults = array() ) {
			$data = array(
				'links_below_form_aligment' => array(
					'type'      => 'sui-tab-icon',
					'label'     => __( 'Alignment', 'ub' ),
					'options'   => array(
						'left'   => 'align-left',
						'center' => 'align-center',
						'right'  => 'align-right',
					),
					'default'   => is_rtl() ? 'right' : 'left',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Links Below Form', 'ub' ),
						'end'   => true,
					),
					'group'     => array(
						'begin' => true,
						'end'   => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Design -> Form Canvas.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_options_fields_design_canvas( $defaults = array() ) {
			$data = array(
				'canvas_position'       => array(
					'type'      => 'sui-tab',
					'label'     => __( 'Position', 'ub' ),
					'options'   => array(
						'default' => __( 'Default', 'ub' ),
						'left'    => __( 'Left', 'ub' ),
						'right'   => __( 'Right', 'ub' ),
					),
					'default'   => 'default',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form Canvas', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'canvas_width'          => array(
					'type'    => 'number',
					'label'   => __( 'Width', 'ub' ),
					'min'     => 0,
					'max'     => 2000,
					'default' => 100,
					'units'   => array(
						'name'     => 'canvas_width_units',
						'position' => 'field',
						'default'  => '%',
					),
				),
				'canvas_fit'            => array(
					'type'    => 'sui-tab',
					'label'   => __( 'Height', 'ub' ),
					'options' => array(
						'off' => __( 'Default', 'ub' ),
						'on'  => __( 'Device Height', 'ub' ),
					),
					'default' => 'off',
				),
				'canvas_padding_top'    => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Padding', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'canvas_padding_units',
						'default'  => '%',
					),
				),
				'canvas_padding_right'  => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'canvas_padding_bottom' => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'min'          => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'canvas_padding_left'   => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end' => true,
					),
				),
				'canvas_margin_top'     => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Margin', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'canvas_margin_units',
					),
				),
				'canvas_margin_right'   => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'canvas_margin_bottom'  => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'canvas_margin_left'    => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end'        => true,
						'double-end' => true,
					),
					'accordion'    => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_form( $defaults = array() ) {
			$data = array(
				'form_input_label'              => array(
					'type'      => 'color',
					'label'     => __( 'Input label', 'ub' ),
					'default'   => '#777',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form', 'ub' ),
					),
					'panes'     => array(
						'begin'      => true,
						'title'      => __( 'Default', 'ub' ),
						'begin_pane' => true,
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'form_input_color'              => array(
					'type'    => 'color',
					'label'   => __( 'Input color', 'ub' ),
					'default' => '#32373c',
				),
				'form_input_border'             => array(
					'type'    => 'color',
					'label'   => __( 'Input border', 'ub' ),
					'default' => '#ddd',
				),
				'form_input_background'         => array(
					'type'    => 'color',
					'label'   => __( 'Input background', 'ub' ),
					'default' => '#fbfbfb',
				),
				'form_button_label'             => array(
					'type'    => 'color',
					'label'   => __( 'Button label', 'ub' ),
					'default' => '#fff',
				),
				'form_button_border'            => array(
					'type'    => 'color',
					'label'   => __( 'Button border', 'ub' ),
					'default' => '#006799',
				),
				'form_button_background'        => array(
					'type'    => 'color',
					'label'   => __( 'Button background', 'ub' ),
					'default' => '#0085ba',
				),
				'form_background'               => array(
					'type'    => 'color',
					'label'   => __( 'Form background', 'ub' ),
					'default' => '#fff',
					'data'    => array(
						'alpha' => true,
					),
				),
				'form_border_color'             => array(
					'type'    => 'color',
					'label'   => __( 'Form border', 'ub' ),
					'default' => 'transparent',
					'data'    => array(
						'alpha' => true,
					),
				),
				'form_shadow'                   => array(
					'type'    => 'color',
					'label'   => __( 'Form box shadow', 'ub' ),
					'default' => 'rgba(0,0,0,0.13)',
					'data'    => array(
						'alpha' => true,
					),
				),

				'form_container_background'     => array(
					'type'    => 'color',
					'label'   => __( 'Form container background', 'ub' ),
					'default' => 'transparent',
					'data'    => array(
						'alpha' => true,
					),
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * active
				 */
				'form_input_color_active'       => array(
					'type'    => 'color',
					'label'   => __( 'Input color', 'ub' ),
					'default' => '#32373c',
					'panes'   => array(
						'title'      => __( 'Active', 'ub' ),
						'begin_pane' => true,
					),
				),
				'form_input_border_active'      => array(
					'type'    => 'color',
					'label'   => __( 'Input border', 'ub' ),
					'default' => '#ddd',
				),
				'form_input_background_active'  => array(
					'type'    => 'color',
					'label'   => __( 'Input background', 'ub' ),
					'default' => '#fbfbfb',
				),
				'form_button_label_active'      => array(
					'type'    => 'color',
					'label'   => __( 'Button label', 'ub' ),
					'default' => '#fff',
				),
				'form_button_border_active'     => array(
					'type'    => 'color',
					'label'   => __( 'Button border', 'ub' ),
					'default' => '#006799',
				),
				'form_button_background_active' => array(
					'type'    => 'color',
					'label'   => __( 'Button background', 'ub' ),
					'default' => '#0073aa',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * Focus
				 */
				'form_input_color_focus'        => array(
					'type'    => 'color',
					'label'   => __( 'Input color', 'ub' ),
					'default' => '#32373c',
					'panes'   => array(
						'title'      => __( 'Focus', 'ub' ),
						'begin_pane' => true,
					),
				),
				'form_input_border_focus'       => array(
					'type'    => 'color',
					'label'   => __( 'Input border', 'ub' ),
					'default' => '#5b9dd9',
				),
				'form_input_background_focus'   => array(
					'type'    => 'color',
					'label'   => __( 'Input background', 'ub' ),
					'default' => '#fbfbfb',
				),
				'form_button_label_focus'       => array(
					'type'    => 'color',
					'label'   => __( 'Button label', 'ub' ),
					'default' => '#fff',
				),
				'form_button_border_focus'      => array(
					'type'    => 'color',
					'label'   => __( 'Button border', 'ub' ),
					'default' => '#5b9dd9',
				),
				'form_button_background_focus'  => array(
					'type'    => 'color',
					'label'   => __( 'Button background', 'ub' ),
					'default' => '#008ec2',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * hover
				 */
				'form_input_color_hover'        => array(
					'type'    => 'color',
					'label'   => __( 'Input color', 'ub' ),
					'default' => '#32373c',
					'panes'   => array(
						'title'      => __( 'Hover', 'ub' ),
						'begin_pane' => true,
					),
				),
				'form_input_border_hover'       => array(
					'type'    => 'color',
					'label'   => __( 'Input border', 'ub' ),
					'default' => '#5b9dd9',
				),
				'form_input_background_hover'   => array(
					'type'    => 'color',
					'label'   => __( 'Input background', 'ub' ),
					'default' => '#ddd',
				),
				'form_button_label_hover'       => array(
					'type'    => 'color',
					'label'   => __( 'Button label', 'ub' ),
					'default' => '#fff',
				),
				'form_button_border_hover'      => array(
					'type'    => 'color',
					'label'   => __( 'Button border', 'ub' ),
					'default' => '#006799',
				),
				'form_button_background_hover'  => array(
					'type'      => 'color',
					'label'     => __( 'Button background', 'ub' ),
					'default'   => '#008ec2',
					'panes'     => array(
						'end_pane' => true,
						'end'      => true,
					),
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Links Below Form.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_links_below_form( $defaults = array() ) {
			$data = array(
				'links_below_form_register'        => array(
					'type'      => 'color',
					'label'     => __( '"Register | Lost your password?" links', 'ub' ),
					'default'   => '#555d66',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Links Below Form', 'ub' ),
					),
					'panes'     => array(
						'begin'      => true,
						'title'      => __( 'Default', 'ub' ),
						'begin_pane' => true,
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'links_below_form_back'            => array(
					'type'    => 'color',
					'label'   => __( '“Back to Website” link', 'ub' ),
					'default' => '#555d66',
				),
				'links_below_form_policy'          => array(
					'type'    => 'color',
					'label'   => __( 'Privacy Policy', 'ub' ),
					'default' => '#0073aa',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * active
				 */
				'links_below_form_register_active' => array(
					'type'    => 'color',
					'label'   => __( '"Register | Lost your password?" links', 'ub' ),
					'default' => '#555d66',
					'panes'   => array(
						'title'      => __( 'Active', 'ub' ),
						'begin_pane' => true,
					),
				),
				'links_below_form_back_active'     => array(
					'type'    => 'color',
					'label'   => __( '“Back to Website” link', 'ub' ),
					'default' => '#999',
				),
				'links_below_form_policy_active'   => array(
					'type'    => 'color',
					'label'   => __( 'Privacy Policy', 'ub' ),
					'default' => '#999',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * Focus
				 */
				'links_below_form_register_focus'  => array(
					'type'    => 'color',
					'label'   => __( '"Register | Lost your password?" links', 'ub' ),
					'default' => '#555d66',
					'panes'   => array(
						'title'      => __( 'Focus', 'ub' ),
						'begin_pane' => true,
					),
				),
				'links_below_form_back_focus'      => array(
					'type'    => 'color',
					'label'   => __( '“Back to Website” link', 'ub' ),
					'default' => '#999',
				),
				'links_below_form_policy_focus'    => array(
					'type'    => 'color',
					'label'   => __( 'Privacy Policy', 'ub' ),
					'default' => '#999',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				/**
				 * hover
				 */
				'links_below_form_register_hover'  => array(
					'type'    => 'color',
					'label'   => __( '"Register | Lost your password?" links', 'ub' ),
					'default' => '#555d66',
					'panes'   => array(
						'title'      => __( 'Hover', 'ub' ),
						'begin_pane' => true,
					),
				),
				'links_below_form_back_hover'      => array(
					'type'    => 'color',
					'label'   => __( '“Back to Website” link', 'ub' ),
					'default' => '#999',
				),
				'links_below_form_policy_hover'    => array(
					'type'      => 'color',
					'label'     => __( 'Privacy Policy', 'ub' ),
					'default'   => '#999',
					'panes'     => array(
						'end_pane' => true,
						'end'      => true,
					),
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Form Canvas.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_canvas( $defaults = array() ) {
			$data = array(
				'canvas_background' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'default'   => 'transparent',
					'data'      => array(
						'alpha' => true,
					),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form Canvas', 'ub' ),
						'end'   => true,
					),
					'group'     => array(
						'begin' => true,
						'end'   => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Helper function to get template chooser.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_template_configuration() {
			$id_name           = $this->get_nonce_action( 'choose', 'predefined', 'template' );
			$has_configuration = $this->has_configuration();
			$value             = $this->get_value( 'theme' );
			$content           = $this->template_button( $has_configuration, $value, $id_name );
			// Dialog.
			$template = sprintf( '/admin/modules/%s/dialogs/choose-template', $this->module );
			$args     = array(
				'elements'     => $this->get_themes(),
				'show_warning' => $this->has_configuration(),
				'current'      => is_array( $value ) && isset( $value['id'] ) ? $value['id'] : false,
			);
			$dialog   = $this->render( $template, $args, true );
			/**
			 * footer
			 */
			$footer   = '';
			$args     = array(
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
				'data' => array(
					'modal-close' => '',
				),
			);
			$footer  .= $this->button( $args );
			$args     = array(
				'text'  => __( 'Continue', 'ub' ),
				'sui'   => false,
				'class' => 'branda-login-screen-choose-template',
				'data'  => array(
					'nonce' => $this->get_nonce_value( 'template' ),
				),
			);
			$footer  .= $this->button( $args );
			$args     = array(
				'id'      => $id_name,
				'content' => $dialog,
				'footer'  => array(
					'content' => $footer,
					'classes' => array(
						'sui-space-between',
					),
				),
				'title'   => __( 'Choose a Template', 'ub' ),
				'classes' => array(
					'branda-login-screen-choose-template-dialog',
					'branda-choose-template-dialog',
					'sui-modal-sm',
				),
			);
			$content .= $this->sui_dialog( $args );
			return $content;
		}

		private function template_button( $has_configuration, $value, $dialog_id ) {
			return $this->render(
				'admin/common/options/template-picker',
				array(
					'has_configuration' => $has_configuration,
					'screenshot'        => empty( $value['screenshot'] ) ? '' : $value['screenshot'],
					'dialog_id'         => $dialog_id,
				),
				true
			);
		}

		/**
		 * Set template in ajax.
		 *
		 * @since 3.0.0
		 */
		public function ajax_set_template() {
			$nonce_action = $this->get_nonce_action( 'template' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$id = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$uba = branda_get_uba_object();
			/**
			 * reset settings by scratch
			 */
			if ( 'start-from-scratch' === $id ) {
				branda_delete_option( $this->option_name );
				$this->update_value( array( 'theme' => 'start-from-scratch' ) );
				$message = array(
					'type'    => 'success',
					'message' => __( 'You can now start from scratch!', 'ub' ),
				);
				$uba->add_message( $message );
				wp_send_json_success();
			}
			$result = $this->set_theme( $id );
			if ( is_array( $result ) && ! empty( $result ) ) {
				$message = array(
					'type'    => 'success',
					'message' => sprintf(
						__( '"%s" theme configuration was successfully loaded!', 'ub' ),
						sprintf( '<b>%s</b>', esc_html( $result['theme']['Name'] ) )
					),
				);
				$uba->add_message( $message );
				wp_send_json_success();
			}
			$this->json_error();
		}

		/**
		 * Convert old themes path to assets
		 *
		 * @since 3.0.0
		 */
		private function convert_old_themes_path( $path ) {
			$re = '@ultimate-branding-files/modules/custom-login-screen/themes@';
			$to = 'inc/modules/login-screen/themes';
			return preg_replace( $re, $to, $path );
		}

		/**
		 * Before Login form
		 *
		 * @since 3.0.0
		 */
		public function add_login_header() {
			$this->html_background_common();
			echo '<div class="branda-login">';
		}

		/**
		 * After Login form
		 *
		 * @since 3.0.0
		 */
		public function add_login_footer() {
			echo '</div>';
		}

		/**
		 * Get ACE editor buttons
		 *
		 * @since 3.0.0
		 */
		private function get_ace_selectors() {
			$selectors = array(
				'general' => array(
					'selectors' => array(
						'.branda-login' => __( 'Container', 'ub' ),
						'h1'            => __( 'Logo', 'ub' ),
						'#login'        => __( 'Form Wrapper', 'ub' ),
						'#loginform'    => __( 'Form', 'ub' ),
					),
				),
			);
			return $selectors;
		}

		/**
		 * Add settings sections to prevent delete on save.
		 *
		 * Add settings sections (virtual options not included in
		 * "set_options()" function to avoid delete during update.
		 *
		 * @since 3.0.0
		 */
		public function add_preserve_fields() {
			return array(
				'theme' => null,
			);
		}

		/**
		 * add http:// to a URL
		 *
		 * add http:// to a URL if it doesn't already include a protocol (e.g.
		 * http://, https:// or ftp://)?
		 *
		 * @since 3.0.1
		 */
		private function add_http_if_is_missing( $value ) {
			if ( ! preg_match( '~^(?:f|ht)tps?://~i', $value ) ) {
				$value = 'http://' . $value;
			}
			return $value;
		}

		/**
		 * Add current domain if it didn't set in the $url
		 *
		 * @param string $url URL
		 * @return string
		 */
		private function get_full_internal_link( $url ) {
			$host = wp_parse_url( $url, PHP_URL_HOST );
			if ( is_null( $host ) ) {
				$slash = '/' !== $url[0] ? '/' : '';
				$url   = get_home_url() . $slash . $url;
			}
			return $url;
		}

		/**
		 * Options: Content -> Message.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.2.0
		 *
		 * @return array
		 */
		public function get_options_fields_content_message( $defaults = array() ) {
			$data = array(
				'message' => array(
					'type'      => 'text',
					'label'     => __( 'Message', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'end'   => true,
						'title' => __( 'Message', 'ub' ),
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Options: Design -> Message.
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.2.0
		 *
		 * @return array
		 */
		public function get_options_fields_design_message( $defaults = array() ) {
			$data = array(
				'message' => array(
					'type'      => 'sui-tab',
					'label'     => __( 'Type', 'ub' ),
					'options'   => array(
						'none'    => __( 'Inform', 'ub' ),
						'success' => __( 'Success', 'ub' ),
					),
					'default'   => 'none',
					'accordion' => array(
						'begin' => true,
						'end'   => true,
						'title' => __( 'Message', 'ub' ),
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Show Login Message
		 *
		 * @since 3.2.0
		 */
		public function login_message( $content ) {
			$value = $this->get_value( 'content', 'message', '' );
			if ( empty( $value ) ) {
				return $content;
			}
			$classes = array(
				'message',
			);
			$class   = $this->get_value( 'design', 'message', false );
			if ( 'success' === $class ) {
				$classes[] = $class;
			}
			$content .= sprintf( '<div class="%s">', esc_attr( implode( ' ', $classes ) ) );
			$content .= sprintf( '<p>%s</p>', $value );
			$content .= '</div>';
			return $content;
		}

		/**
		 * Options: Design -> Form Container
		 *
		 * @param array $defaults Default values.
		 *
		 * @since 3.1.2
		 *
		 * @return array
		 */
		public function get_options_fields_design_container( $defaults = array() ) {
			$data = array(
				'container_width'          => array(
					'type'      => 'number',
					'label'     => __( 'Width', 'ub' ),
					'min'       => 0,
					'max'       => 2000,
					'default'   => 320,
					'units'     => array(
						'name'     => 'container_width_units',
						'position' => 'field',
						'default'  => 'px',
					),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Form Container', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'container_padding_top'    => array(
					'type'         => 'number',
					'label'        => __( 'Top', 'ub' ),
					'min'          => 0,
					'default'      => 8,
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
					'group'        => array(
						'begin'   => true,
						'label'   => __( 'Padding', 'ub' ),
						'classes' => 'sui-border-frame',
					),
					'units'        => array(
						'position' => 'group',
						'name'     => 'container_padding_units',
						'default'  => '%',
					),
				),
				'container_padding_right'  => array(
					'type'         => 'number',
					'label'        => __( 'Right', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'container_padding_bottom' => array(
					'type'         => 'number',
					'label'        => __( 'Bottom', 'ub' ),
					'min'          => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div>',
				),
				'container_padding_left'   => array(
					'type'         => 'number',
					'label'        => __( 'Left', 'ub' ),
					'min'          => 0,
					'default'      => 0,
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
					'group'        => array(
						'end'        => true,
						'double-end' => true,
					),
					'accordion'    => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change content logo fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data     logo options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Validate password.
		 *
		 * @since x.x.x
		 */
		public function validate_password( $wp_error ) {
			$value = $this->get_value( 'content', 'form_signup_password', 'off' );
			if ( 'on' !== $value ) {
				return $wp_error;
			}

			$password_1 = ! empty( $_POST['password_1'] ) ? sanitize_text_field( $_POST['password_1'] ) : '';
			$password_2 = ! empty( $_POST['password_2'] ) ? sanitize_text_field( $_POST['password_2'] ) : '';

			if ( empty( $password_1 ) ) {
				$wp_error->add( 'password_1', __( '<strong>ERROR</strong>: Please enter a password.', 'ub' ) );
			} elseif ( $password_1 !== $password_2 ) {
				$wp_error->add( 'password_1', __( '<strong>ERROR</strong>: Please enter the same password in both password fields.', 'ub' ) );
			}
			return $wp_error;
		}

		/**
		 * Validate password (MultiSite).
		 *
		 * @since x.x.x
		 */
		public function validate_password_wpmu( $results ) {
			$value = $this->get_value( 'content', 'form_signup_password', 'off' );
			if ( is_admin() || 'on' !== $value ) {
				return $results;
			}

			$password_1 = ! empty( $_POST['password_1'] ) ? sanitize_text_field( $_POST['password_1'] ) : '';
			$password_2 = ! empty( $_POST['password_2'] ) ? sanitize_text_field( $_POST['password_2'] ) : '';

			if ( empty( $password_1 ) ) {
				$results['errors']->add( 'password_1', __( '<strong>ERROR</strong>: Please enter a password.', 'ub' ) );
			} elseif ( $password_1 !== $password_2 ) {
				$results['errors']->add( 'password_1', __( '<strong>ERROR</strong>: Please enter the same password in both password fields.', 'ub' ) );
			}
			return $results;
		}

		/**
		 * Escape module fields.
		 *
		 * @param mixed|array|string $data
		 * @return string
		 */
		public static function esc_data( $data ) {
			if ( ! is_array( $data ) ) {
				return esc_html( $data );
			}

			if ( empty( $data['key'] ) || empty( $data['value'] ) ) {
				return $data;
			}

			return wp_kses_post( $data['value'] );
		}

		public function refactor_data( $value, $type, $section_key, $key, $current_value, $data, $module ) {
			// Refactor Logo & Background > Background Image, so that it stores the attachment url as a value instead of attachment id.
			// We need to do that in order to avoid keep generating new attachments loaded in frontend.
			// Before saving we need to make sure that the dimensions are acceptable.
			if ( $this->module === $module && 'content' === $section_key && 'content_background' === $key ) {
				if ( ! empty( $value['content']['content_background'] ) && is_array( $value['content']['content_background'] ) ) {
					remove_filter( 'ub_escaped_value', array( $this, 'refactor_data' ), 10 );

					foreach ( $value['content']['content_background'] as $item_key => $item ) {
						// If image has already been cropped, no need to resize, 
						// unless the `Background > Background Crop` option has been updated.
						if ( 
							( empty( $item['cropped'] ) || $item['cropped'] !== $this->get_value( 'design', 'background_crop', 'auto' ) ) &&
							! empty( $item['value'] ) && is_numeric( $item['value'] ) ) {
							if ( $url = $this->maybe_crop( $item, $item_key ) ) {
								$original_url = ! empty( $item['meta'][0] ) ? $item['meta'][0] : null;

								//if ( $original_url !== $url ) {
									$attachment_id = attachment_url_to_postid( $url );

									if ( ! empty( $attachment_id ) ) {
										$attachment_meta = wp_get_attachment_metadata( $attachment_id );

										$value['content']['content_background'][ $item_key ] = array(
											'value'   => $attachment_id,
											'meta'    => array(
												$url,
												$attachment_meta['width'],
												$attachment_meta['height'],
											),
											'cropped' => $this->get_value( 'design', 'background_crop', 'auto' ),
										);
									}
								//}
							}
						}
					}
				}
			}

			return $value;
		}
		
	}
}
new Branda_Login_Screen();
