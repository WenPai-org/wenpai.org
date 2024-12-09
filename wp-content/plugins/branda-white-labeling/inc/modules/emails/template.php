<?php
/**
 * Branda Email Template class.
 *
 * @package Branda
 * @subpackage Emails
 */
if ( ! class_exists( 'Branda_Email_Template' ) ) {
	class Branda_Email_Template extends Branda_Helper {
		// This is where the class variables go, don't forget to use @var to tell what they're for
		/**
		 * @var string The options string name for this plugin
		 */
		var $option_name = 'ub_email_template';

		/**
		 * @var path Template Directory
		 */
		var $template_directory = '';

		/**
		 * @var path Template URL
		 */
		var $template_url = '';

		/**
		 * @var path to assets
		 */
		var $settings = array();

		/**
		 * Content type of email
		 *
		 * @var bool
		 */
		var $is_html = '';

		/**
		 * Plain Text Message
		 *
		 * @var string
		 */
		var $plain_text_message = '';

		/**
		 * @var string
		 */
		public $location = '';

		/**
		 * @var string
		 */
		public $plugin_dir = '';

		/**
		 * @var string
		 */
		public $plugin_url = '';

		/**
		 * Variables
		 *
		 * @since 3.0.0
		 */
		private $variables = array();

		/**
		 * Allowed ID
		 *
		 * @since 3.0.6
		 */
		private $allowed_ids = array(
			'disco'            => 'disco',
			'handwritten'      => 'handwritten',
			'hero'             => 'hero',
			'iletter'          => 'iletter',
			'minimise'         => 'minimise',
			'promy'            => 'promy',
			'sidebar'          => 'sidebar',
			'simplicity-dark'  => 'simplicity-dark',
			'simplicity-light' => 'simplicity-light',
		);


		public function __construct() {
			parent::__construct();
			$this->module = 'email-template';
			add_filter( 'ultimatebranding_settings_email_template', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_email_template_process', array( $this, 'update' ) );
			// setup proper directories
			if ( file_exists( plugin_dir_path( __FILE__ ) . basename( __FILE__ ) ) ) {
				$this->location   = 'plugins';
				$this->plugin_dir = plugin_dir_path( __FILE__ );
				$this->plugin_url = plugins_url( '', __FILE__ ) . '/';
			} elseif ( defined( 'WPMU_PLUGIN_URL' ) && defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/' . basename( __FILE__ ) ) ) {
				$this->location   = 'mu-plugins';
				$this->plugin_dir = WPMU_PLUGIN_DIR . '/';
				$this->plugin_url = WPMU_PLUGIN_URL . '/';
			} else {
				wp_die( __( 'There was an issue determining where HTML Email is installed. Please reinstall.', 'ub' ) );
			}
			// Template Directory
			$this->template_directory = $this->plugin_dir . 'templates/';
			$this->template_url       = $this->plugin_url . 'templates/';
			// Actions
			add_action( 'phpmailer_init', array( $this, 'convert_plain_text' ) );
			// Filters
			add_filter( 'wp_mail', array( $this, 'wp_mail' ) );
			// Return template data
			add_action( 'wp_ajax_branda_email_template_preview_email', array( $this, 'ajax_preview' ) );
			// Set Content type HTML
			add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 11 );
			add_filter( 'woocommerce_email_headers', array( $this, 'set_woocommerce_content_type' ) );
			/**
			 * Add settings button.
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_settings_after_box_title', array( $this, 'add_button_after_title' ), 10, 2 );
			/**
			 * AJAX Action: Set template.
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_email_template_set_template', array( $this, 'ajax_set_template' ) );
			/**
			 * AJAX Action: Send test email.
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_' . $this->get_name( 'send' ), array( $this, 'ajax_send_test_email' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );

			// First skip default general escape.
			$this->skip_escape = true;
			// Then use custom escape using `ub_escaped_value`.
			add_filter( 'ub_escaped_value', array( $this, 'esc_data' ), 10, 5 );
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			if (
				isset( $value['plugin_version'] )
				&& - 1 !== version_compare( $value['plugin_version'], $this->build )
			) {
				return;
			}
			$value = branda_get_option( 'html_template' );
			if ( ! empty( $value ) ) {
				$data = array(
					'content' => array(
						'email' => $value,
					),
				);
				$this->update_value( $data );
				branda_delete_option( 'html_template' );
			}
		}

		/**
		 * Set options
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			/**
			 * variables
			 */
			$variables = array(
				'email'   => array(
					'{EMAIL_SUBJECT}' => __( 'Email Subject', 'ub' ),
					'{MESSAGE}'       => __( 'Message', 'ub' ),
					'{FROM_NAME}'     => __( 'From Name', 'ub' ),
					'{FROM_EMAIL}'    => __( 'From Email', 'ub' ),
					'{ADMIN_EMAIL}'   => __( 'Admin Email', 'ub' ),
				),
				'general' => array(
					'{SIDEBAR_TITLE}'    => __( 'Sidebar Title', 'ub' ),
					'{BLOG_URL}'         => $this->is_network ? __( 'Blog URL', 'ub' ) : __( 'Site URL', 'ub' ),
					'{BLOG_NAME}'        => $this->is_network ? __( 'Blog Name', 'ub' ) : __( 'Site Name', 'ub' ),
					'{BLOG_DESCRIPTION}' => $this->is_network ? __( 'Blog Description', 'ub' ) : __( 'Site Description', 'ub' ),
					'{DATE}'             => __( 'Current Date', 'ub' ),
					'{TIME}'             => __( 'Current Time', 'ub' ),
				),
			);
			/**
			 * set options
			 */
			$options       = array(
				'theme'   => array(
					'title'       => __( 'Template', 'ub' ),
					'description' => __( 'Choose one of our pre-designed email templates and customize it further as per your need.', 'ub' ),
					'fields'      => array(
						'template' => array(
							'type'     => 'callback',
							'callback' => array( $this, 'get_template_configuration' ),
						),
						'id'       => array(
							'type' => 'hidden',
						),
					),
				),
				'preview' => array(
					'title'  => __( 'Preview', 'ub' ),
					'fields' => array(
						'preview' => array(
							'type'    => 'button',
							'data'    => array(
								'modal-open' => $this->get_name( 'preview' ),
								'modal-mask' => 'true',
								'nonce'      => $this->get_nonce_value( 'preview' ),
								'message'    => __( 'Please wait…', 'ub' ),
							),
							'value'   => __( 'Preview', 'ub' ),
							'icon'    => 'eye',
							'classes' => array( $this->get_name( 'preview' ) ),
						),
					),
				),
				'content' => array(
					'no-sui-columns' => true,
					'title'          => __( 'HTML Editor', 'ub' ),
					'description'    => __( 'Start editing your chosen email template or writing a new one. You can use the variables provided on top of the editor to insert dynamic data such as email message.', 'ub' ),
					'fields'         => array(
						'email' => array(
							'type'          => 'html_editor',
							'placeholder'   => esc_html__( 'Start typing or paste your HTML markup here…', 'ub' ),
							'ace_selectors' => array(
								array(
									'title'     => __( 'Email variables', 'ub' ),
									'selectors' => $variables['email'],
								),
								array(
									'title'     => __( 'General variables', 'ub' ),
									'selectors' => $variables['general'],
								),
							),
						),
					),
				),
			);
			$this->options = $options;
		}

		/**
		 * Removes the <> from password reset link sent in new user emails
		 *
		 * @return mixed
		 */
		public function clean_links( $message ) {
			return preg_replace( '#<(https?://[^*]+)>#', '$1', $message );
		}

		/**
		 * Filter the Content, add the template to actual email and then send it
		 *
		 * @param $args
		 *
		 * @return array
		 */
		public function wp_mail( $args ) {
			extract( $args );
			$modify_html_email = branda_get_option( 'modify_html_email', 1 );
			/**
			 * Check if the current mail is a html mail and template adding is allowed or not
			 */
			if ( ! empty( $this->is_html ) ) {
				if ( $this->is_html && ! $modify_html_email ) {
					return $args;
				}
			} elseif ( ! empty( $headers ) ) {
				// check headers
				if ( is_array( $headers ) ) {
					if ( in_array( 'text/html', $headers ) && ! $modify_html_email ) {
						return $args;
					}
				} elseif ( strpos( $headers, 'text/html' ) !== false && ! $modify_html_email ) {
					return $args;
				}
			}
			$html_template            = $htmlemail_settings = '';
			$this->plain_text_message = $message;
			// Clean Links
			$message = $this->clean_links( $message );
			/**
			 * Allows to enable or disable Next Line to br tag conversion
			 */
			$nl2br   = apply_filters( 'wp_htmlemail_nl2br', false );
			$message = $nl2br ? nl2br( $message ) : $message;
			// Force WP to add <p> tags to the message content
			$stripped = strip_tags( $message );
			if ( $message == $stripped ) {
				// No HTML, do wpautop
				$message = wpautop( $message );
			}
			$html_template = $this->get_value( 'content', 'email', false );
			if ( ! empty( $html_template ) ) {
				if ( strpos( $html_template, '{MESSAGE}' ) !== false ) {
					// Replace {MESSAGE} in template with actual email content
					$key = '{MESSAGE}';
				} else {
					// Compatibilty with previous version of the plugin, as it used MESSAGE instead of {MESSAGE}
					$key = 'MESSAGE';
				}
				$html_template = preg_replace( '~\{EMAIL_SUBJECT}~', $subject, $html_template );
				$message = str_replace( $key, $message, $html_template );
				//Replace User name
				if ( ! is_array( $to ) ) {
					$to = array( $to );
				}
				foreach ( $to as $t ) {
					$user = get_user_by( 'email', $t );
					if ( $user ) {
						$message = preg_replace( '~\{USER_NAME}~', $user->data->display_name, $message );
					} else {
						$message = preg_replace( '~\{USER_NAME}~', '', $message );
					}
				}
				$message = $this->replace_placeholders( $message, array(), false );
			}
			// Compact & return all the vars
			return compact( 'to', 'subject', 'message', 'headers', 'attachments' );
		}

		public function convert_plain_text( $phpmailer ) {
			// Create plain text version of email if it doesn't exist
			if ( '' == $phpmailer->AltBody ) {
				$phpmailer->AltBody = $this->plain_text_message;
			}
		}

		/**
		 * Returns template list for Emails
		 *
		 * @return type
		 */
		public function get_themes() {
			$templates = array();
			// get all template folders inside template directory
			foreach ( glob( $this->template_directory . '*', GLOB_ONLYDIR ) as $template_path ) {
				$template_url = $this->template_url . basename( $template_path );
				if ( $template_path ) {
					$template_html       = $template_path . '/template.html';
					$template_screenshot = glob( $template_path . '/screenshot.*' );
					// Check if it contains template.html and a screenshot
					if ( ! file_exists( $template_html ) || ! file_exists( $template_screenshot[0] ) ) {
						continue;
					}
					$theme_name               = get_file_data( $template_path . '/style.css', array( 'Name' => 'Theme Name' ) );
					$theme_name['screenshot'] = $template_url . '/' . basename( $template_screenshot[0] );
					$theme_name['id']         = sanitize_title( $theme_name['Name'] );
					$templates[]              = $theme_name;
				}
			}
			return $templates;
		}

		/**
		 * Get theme screenshot
		 *
		 * @since 3.0.0
		 */
		private function get_theme_screenshot( $id ) {
			$themes = $this->get_themes();
			foreach ( $themes as $theme ) {
				if ( $id === $theme['id'] ) {
					return $theme['screenshot'];
				}
			}
			return '';
		}

		/**
		 * Get theme data
		 */
		private function get_theme_data( $theme_name ) {
			$theme_data = array();
			if ( empty( $theme_name ) ) {
				return $theme_data;
			}
			$theme_name = explode( ' ', $theme_name );
			$theme_name = implode( '', $theme_name );
			$theme_name = ucfirst( strtolower( $theme_name ) );
			$theme_data = array();
			/**
			 * Get manaualy name to avoid "Path Traversal" Server-side
			 * Vulnerability
			 */
			switch ( $theme_name ) {
				case 'Disco':
					$dir = 'Disco';
					break;
				case 'Handwritten':
					$dir = 'Handwritten';
					break;
				case 'Hero':
					$dir = 'Hero';
					break;
				case 'Iletter':
					$dir = 'Iletter';
					break;
				case 'Minimise':
					$dir = 'Minimise';
					break;
				case 'Promy':
					$dir = 'Promy';
					break;
				case 'Sidebar':
					$dir = 'Sidebar';
					break;
				case 'Simplicity-dark':
					$dir = 'Simplicity-dark';
					break;
				case 'Simplicity-light':
					$dir = 'Simplicity-light';
					break;
				default:
					return $theme_name;
			}
			/**
			 * Get Default Variables
			 */
			$filename = sprintf(
				'%s%s/index.php',
				$this->template_directory,
				$dir
			);
			if ( is_file( $filename ) ) {
				$theme_data = include $filename;
			}
			/**
			 * Set path & url
			 */
			$theme_data['path'] = $this->template_directory . $theme_name;
			$theme_data['url']  = $this->template_url . $theme_name;
			return $theme_data;
		}

		/**
		 * Fetches content from template files and returns content with inline styling
		 *
		 * @param type $theme_name
		 *
		 * @return boolean
		 */
		public function get_contents_elements( $theme_name = '' ) {
			if ( ! $theme_name ) {
				return false;
			}
			$contents = array();
			// Get Default Variables
			$theme_data = $this->get_theme_data( $theme_name );
			// Template Files
			$build_htmls['header'][]  = $theme_data['path'] . '/header.html';
			$build_htmls['content'][] = $theme_data['path'] . '/template.html';
			$build_htmls['footer'][]  = $theme_data['path'] . '/footer.html';
			if (
				isset( $theme_data['BUILDER_SETTING_USE_DEFAULT_HEADER_FOOTER'] )
				&& $theme_data['BUILDER_SETTING_USE_DEFAULT_HEADER_FOOTER']
			) {
				$build_htmls['header'][] = $this->template_directory . 'default_header.html';
				$build_htmls['footer'][] = $this->template_directory . 'default_footer.html';
			}
			$build_styles['style'][]        = $theme_data['path'] . '/style.css';
			$build_styles['style_header'][] = $theme_data['path'] . '/style_header.css';
			if (
				isset( $theme_data['BUILDER_SETTING_USE_DEFAULT_STYLES'] )
				&& $theme_data['BUILDER_SETTING_USE_DEFAULT_STYLES']
			) {
				$build_styles['default_style'][] = $this->template_directory . 'default_style.css';
			}
			$build_theme    = array_merge( $build_htmls, $build_styles );
			$dirname        = dirname( __FILE__ );
			$contents_parts = array(
				'header'       => null,
				'content'      => null,
				'footer'       => null,
				'style_header' => null,
			);
			foreach ( $build_theme as $type => $possible_files ) {
				foreach ( $possible_files as $possible_file ) {
					if ( isset( $contents_parts[ $type ] ) && ! empty( $contents_parts[ $type ] ) ) {
						continue;
					}
					/**
					 * Apply realpath
					 */
					$possible_file = realpath( $possible_file );
					if ( empty( $possible_file ) ) {
						continue;
					}
					if ( ! preg_match( '/\.(css|html)$/', $possible_file ) ) {
						continue;
					}
					/**
					 * Check is file inside our directory
					 */
					$position = strpos( $possible_file, $dirname );
					if ( 0 !== $position ) {
						continue;
					}
					if ( file_exists( $possible_file ) ) {
						$contents_parts[ $type ] = file_get_contents( $possible_file );
						if ( strpos( $type, 'style' ) !== false ) {
							$contents_parts[ $type ] = preg_replace( '/^\s*\/\*[^(\*\/)]*\*\//m', '', $contents_parts[ $type ] );
						}
					}
					if ( ! isset( $contents_parts[ $type ] ) ) {
						$contents_parts[ $type ] = '';
					}
				}
			}
			// if head missing - fix it!
			if ( strpos( $contents_parts['header'] . $contents_parts['content'], '<html' ) === false && strpos( $contents_parts['content'] . $contents_parts['footer'], '</html>' ) === false ) {
				if ( strpos( $contents_parts['header'] . $contents_parts['content'], '<body' ) === false && strpos( $contents_parts['content'] . $contents_parts['footer'], '</body>' ) === false ) {
					$body_header = '<body>';
					$body_footer = '</body>';
				} else {
					$body_header = $body_footer = '';
				}
				$contents_parts['header'] = '
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>{EMAIL_TITLE}</title>
					<style type="text/css">
				{DEFAULT_STYLE_HEADER}
				{STYLE_HEADER}
				</style>
				{HEADER}
				</head>' . $body_header . $contents_parts['header'];
				$contents_parts['footer'] = $contents_parts['footer'] . $body_footer . '
					</html>';
			}
			// Merge header, content and footer
			$content  = $contents_parts['header'] . $contents_parts['content'] . $contents_parts['footer'];
			$blog_url = get_option( 'siteurl' );
			// Replace BLOG_URL with actual URL as DOM compatibility escapes img src
			$content = preg_replace( '/{BLOG_URL}/', $blog_url . '/', $content );
			$style   = isset( $contents_parts['default_style'] ) ? $contents_parts['default_style'] . $contents_parts['style'] . $contents_parts['style_header'] : $contents_parts['style'] . $contents_parts['style_header'];
			// Do the inline styling
			$content = $this->do_inline_styles( $content, $style );
			// Check for DOM compatibilty from E-Newsletter
			$content = $this->dom_compatibility( $content );
			// Replace CSS Variabls
			$possible_settings = array(
				'BG_COLOR',
				'BG_IMAGE',
				'HEADER_BG_IMAGE',
				'LINK_COLOR',
				'BODY_COLOR',
				'ALTERNATIVE_COLOR',
				'TITLE_COLOR',
				'EMAIL_TITLE',
			);
			foreach ( $possible_settings as $possible_setting ) {
				$id = 'BUILDER_DEFAULT_' . $possible_setting;
				if (
					isset( $theme_data[ $id ] )
				) {
					$this->settings[] = $possible_setting;
				}
			}
			foreach ( $this->settings as $setting ) {
				$value = '';
				$id    = 'BUILDER_DEFAULT_' . $setting;
				if ( isset( $theme_data[ $id ] ) ) {
					$value = $theme_data[ $id ];
					if (
						in_array( $setting, array( 'BG_IMAGE', 'HEADER_BG_IMAGE' ), true )
						&& ! empty( $theme_data[ $id ] )
					) {
						$value = sprintf(
							'%s/%s',
							$theme_data['url'],
							$theme_data[ $id ]
						);
					}
				}
				if ( stripos( $setting, 'color' ) ) {
					$value = preg_replace( '/[^A-Za-z0-9\-]/', '', $value );
				}
				if ( 'EMAIL_TITLE' !== $setting ) {
					$content = preg_replace( "/\b($setting)\b/i", $value, $content );
				}
			}
			if ( is_ssl() ) {
				$content = preg_replace( '@http://@', 'https://', $content );
			}
			return $content;
		}

		/**
		 * Prepare inline styles
		 **/
		public function do_inline_styles( $contents, $styles ) {
			if ( $contents && $styles ) {
				if ( ! class_exists( 'CssToInlineStyles' ) ) {
					require_once $this->plugin_dir . 'lib/builder/css-inline.php';
				}
				$css_inline = new CssToInlineStyles( $contents, $styles );
				$contents   = $css_inline->convert();
			}
			return $contents;
		}

		/**
		 * DOM Walker to ensure compatibility
		 *
		 * @param type $contents
		 *
		 * @return type
		 */
		public function dom_compatibility( $contents ) {
			if ( ! class_exists( 'DOMDocument' ) ) {
				return $contents;
			}
			$dom = new DOMDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( $contents );
			libxml_clear_errors();
			$imgs = $dom->getElementsByTagName( 'img' );
			$ps   = $dom->getElementsByTagName( 'p' );
			foreach ( $ps as $p ) {
				$p_style = $p->getAttribute( 'style' );
				if ( ! empty( $p_style ) ) {
					break;
				}
			}
			foreach ( $imgs as $img ) {
				$classes_to_aligns = array( 'left', 'right' );
				foreach ( $classes_to_aligns as $class_to_align ) {
					if ( $img->hasAttribute( 'class' ) && strstr( $img->getAttribute( 'class' ), 'align' . $class_to_align ) ) {
						$img->setAttribute( 'align', $class_to_align );
					}
				}
				if ( $img->hasAttribute( 'class' ) && strstr( $img->getAttribute( 'class' ), 'aligncenter' ) ) {
					$img_style = $img->getAttribute( 'style' );
					$img_style = preg_replace( '#display:(.*?);#', '', $img_style );
					$img->setAttribute( 'style', $img_style );
					$parent = $img->parentNode;
					if ( 'a' == $parent->nodeName ) {
						$parent = $parent->parentNode;
					}
					if ( 'div' != $parent->nodeName ) {
						$parent->setAttribute( 'style', 'text-align:center;' . $parent->getAttribute( 'style' ) );
					} else {
						$element = $dom->createElement( 'p' );
						$element->setAttribute( 'style', 'text-align:center;' . $p_style );
						$img->parentNode->replaceChild( $element, $img );
						$element->appendChild( $img );
					}
				}
				$style = $img->getAttribute( 'style' );
				preg_match( '#margin:(.*?);#', $style, $matches );
				if ( $matches ) {
					$space_px      = explode( 'px', $matches[1] );
					$space_procent = explode( '%', $matches[1] );
					$space         = ( $space_procent > $space_px ) ? $space_procent : $space_px;
					$space_unit    = ( $space_procent > $space_px ) ? '%' : '';
					if ( $space ) {
						$hspace = trim( $space[0] );
						$vspace = ( isset( $space[1] ) ) ? $hspace : trim( $space[0] );
						$img->setAttribute( 'hspace', $hspace . $space_unit );
						$img->setAttribute( 'vspace', $vspace . $space_unit );
					}
					$style = preg_replace( '#margin:(.*?);#', '', $style );
					if ( $style ) {
						$img->setAttribute( 'style', $style );
					} else {
						$img->removeAttribute( 'style' );
					}
				}
			}
			$contents = $dom->saveHTML();
			return $contents;
		}

		/**
		 * Returns the list of placeholders in template content
		 */
		public function list_placeholders( $content, $desc = false ) {
			if ( $desc ) {
				// Return Placeholder desc table
				$placeholder_desc = array(
					'{MESSAGE}'          => __( 'Email content (required)', 'ub' ),
					'{EMAIL_SUBJECT}'    => __( 'Email subject', 'ub' ),
					'{SIDEBAR_TITLE}'    => __( "Title for the sidebar in email e.g. What's trending", 'ub' ),
					'{FROM_NAME}'        => __( "Sender's name if sender's email is associated with a user account", 'ub' ),
					'{FROM_EMAIL}'       => __( "Sender's email, email specified in site settings", 'ub' ),
					'{BLOG_URL}'         => __( 'Blog / Site URL', 'ub' ),
					'{BLOG_NAME}'        => __( 'Blog / Site name', 'ub' ),
					'{ADMIN_EMAIL}'      => __( 'Email address of the support or contact person. Same as {FROM_EMAIL}', 'ub' ),
					'{BLOG_DESCRIPTION}' => __( 'Blog Description', 'ub' ),
					'{DATE}'             => __( 'Current Date', 'ub' ),
					'{TIME}'             => __( 'Current Time', 'ub' ),
				);
				$output           = '<h4><a href="#placeholder-list-wrapper" class="template-toggle" title="' . esc_attr__( 'Variable list', 'ub' ) . '">' . __( 'List of variables that can be used in template', 'ub' ) .
						  '[<span class="toggle-indicator">+</span>]</a></h4>'
						  . '<div class="placeholders-list-wrapper" id="placeholder-list-wrapper">'
						  . '<table class="template-placeholders-list">';
				$output          .= '<th>Variable name</th>';
				$output          .= '<th>Default value</th>';
				// Get list of common variables
				foreach ( $placeholder_desc as $p_name => $p_desc ) {
					$output .= '<tr>';
					$output .= '<td>' . $p_name . '</td>';
					$output .= '<td>' . $p_desc . '</td>';
					$output .= '</tr>';
				}
				$output .= '</table>';
				$output .= '</div>';
				return $output;
			}
			$placeholders = $links = '';
			preg_match_all( '/\{.+\}/U', $content, $placeholders );
			// Jugaad, need to find a fix for this
			preg_match_all( '/\%7B.+\%7D/U', $content, $links );
			$placeholders = ! empty( $placeholders ) ? $placeholders[0] : '';
			$links        = ! empty( $links ) ? $links[0] : '';
			$placeholders = array_merge( $placeholders, $links );
			return $placeholders;
		}

		/**
		 * Replaces placeholder text in email templates
		 */
		public function replace_placeholders( $content, $theme_data, $demo_message = true ) {
			$theme_name = $this->get_value( 'theme', 'id' );
			if ( empty( $theme_data ) ) {
				$theme_data = $this->get_theme_data( $theme_name );
			}
			$elements         = $this->get_contents_elements( $theme_name );
			$placeholders     = $this->list_placeholders( $content );
			$current_blog_id  = get_current_blog_id();
			$blog_url         = get_option( 'siteurl' );
			$admin_email      = get_option( 'admin_email' );
			$blog_name        = get_option( 'blogname' );
			$blog_description = get_option( 'blogdescription' );
			$date             = date_i18n( get_option( 'date_format' ) );
			$time             = date_i18n( get_option( 'time_format' ) );
			$message          = __( 'This is a test message I want to try out to see if it works. This will be replaced with WordPress email content.', 'ub' );
			$message         .= PHP_EOL;
			$message         .= PHP_EOL;
			$message         .= __( 'Is it working well?', 'ub' );
			$from_email       = get_option( 'admin_email' );
			$user_info        = get_userdata( $from_email );
			if ( $user_info ) {
				$display_name = $user_info->display_name;
			} else {
				$display_name = '';
			}
			/**
			 * Background Image
			 */
			$bg_image = '';
			if (
				isset( $theme_data['BUILDER_DEFAULT_BG_IMAGE'] )
				&& ! empty( $theme_data['BUILDER_DEFAULT_BG_IMAGE'] )
			) {
				$bg_image = $theme_data['url'] . '/' . $theme_data['BUILDER_DEFAULT_BG_IMAGE'];
			}
			/**
			 * Header Image
			 */
			$header_image = '';
			if (
				isset( $theme_data['BUILDER_DEFAULT_HEADER_IMAGE'] )
				&& ! empty( $theme_data['BUILDER_DEFAULT_HEADER_IMAGE'] )
			) {
				if ( preg_match( '/^http/i', $theme_data['BUILDER_DEFAULT_HEADER_IMAGE'] ) ) {
					$header_image = sprintf(
						'<img src="%s" />',
						$theme_data['BUILDER_DEFAULT_HEADER_IMAGE']
					);
				} else {
					$header_image = sprintf(
						'<img src="%s/%s" />',
						esc_attr( $theme_data['url'] ),
						esc_attr( $theme_data['BUILDER_DEFAULT_HEADER_IMAGE'] )
					);
				}
			}
			// Sidebar
			$posts_list = $this->htmlemail_recent_posts();
			/**
			 * Filter the post list displayed in email sidebar
			 *
			 * @since 2.0
			 *
			 * @param array $posts_list , An array of posts, containing ID and post_title for each post
			 */
			$posts_list = apply_filters( 'htmlemail_sidebar_posts', $posts_list );
			/**
			 * Filter the sidebar title in email template
			 *
			 * @since 2.0
			 *
			 * @param string $title , Title to be displayed in email
			 */
			$sidebar_title = apply_filters( 'htmlemail_sidebar_title', $title = "What's new" );
			// Placeholder for posts
			$count             = 1;
			$placeholder_posts = array();
			foreach ( $posts_list as $post ) {
				if ( $count > 4 ) {
					break;
				}
				$placeholder_posts[ "{POST_$count}" ] = $this->short_str( $post['post_title'], '...', 10 );
				// Jugaad, to keep the template styling and links
				$placeholder_posts[ '%7BPOST_' . $count . '_LINK%7D' ] = esc_url( get_permalink( $post['ID'] ) );
				$count ++;
			}
			// Show for preview only
			if ( $demo_message ) {
				if ( strpos( $content, '{MESSAGE}' ) !== false ) {
					// Replace {MESSAGE} in template with actual email content
					$key = '{MESSAGE}';
				} else {
					// Compatibility with previous version of the plugin, as it used MESSAGE instead of {MESSAGE}
					$key = 'MESSAGE';
				}
				$content = str_replace( $key, $message, $content );
				$content = preg_replace( '/({USER_NAME})/', 'Jon', $content );
				$content = preg_replace( '/({EMAIL_SUBJECT})/', __( 'Test Subject', 'ub' ), $content );
			}
			$placeholders_list = array(
				'{}'                 => '',
				'{SIDEBAR_TITLE}'    => $sidebar_title,
				'{CONTENT_HEADER}'   => '',
				'{CONTENT_FOOTER}'   => '',
				'{FOOTER}'           => '',
				'{FROM_NAME}'        => $display_name,
				'{FROM_EMAIL}'       => $from_email,
				'{BLOG_URL}'         => $blog_url,
				'{BLOG_NAME}'        => $blog_name,
				'{EMAIL_TITLE}'      => $blog_name,
				'{ADMIN_EMAIL}'      => $admin_email,
				'{BG_IMAGE}'         => $bg_image,
				'{HEADER_IMAGE}'     => $header_image,
				'{BLOG_DESCRIPTION}' => $blog_description,
				'{DATE}'             => $date,
				'{TIME}'             => $time,
			);
			$placeholders_list = $placeholders_list + $placeholder_posts;
			foreach ( $placeholders as $placeholder ) {
				if ( ! isset( $placeholders_list [ $placeholder ] ) ) {
					continue;
				}
				$content = preg_replace( "/($placeholder)/", $placeholders_list[ $placeholder ], $content );
			}
			// Replace admin email, left out due to escaped html
			$content = preg_replace( '/(%7BADMIN_EMAIL%7D)/', $admin_email, $content );
			return $content;
		}

		/**
		 * Send SUI notice via ajax
		 *
		 * @param string $message Notice text.
		 */
		private static function ajax_send_notice( $message ) {
			wp_send_json_error( Branda_Helper::sui_notice( $message ) );
		}

		/**
		 * Returns data for preview
		 */
		public function ajax_preview() {
			if ( ! current_user_can( 'manage_options' ) ) {
				self::ajax_send_notice( __( "Whoops, you don't have permissions to preview html.", 'ub' ) );
			}
			if ( empty( $_POST ) ) {
				self::ajax_send_notice( __( 'Whoops, you need to enter some html to preview it!', 'ub' ) );
			}
			$content = stripslashes( filter_input( INPUT_POST, 'content', FILTER_UNSAFE_RAW ) );

			$allow_word_break = array( $this, 'allow_break_word' );
			add_filter( 'safe_style_css', $allow_word_break );
			$content = wp_kses_post( $content );
			remove_filter( 'safe_style_css', $allow_word_break );

			/**
			 * Theme ID
			 */
			$theme_name = ! empty( $_POST['theme_id'] ) ? sanitize_text_field( $_POST['theme_id'] ) : '';
			$theme_data = $this->get_theme_data( $theme_name );
			$content    = $this->replace_placeholders( $content, $theme_data );
			if ( empty( $content ) ) {
				self::ajax_send_notice( __( 'Whoops, you need to enter some html to preview it!', 'ub' ) );
			}
			wp_send_json_success( $content );
		}

		public function allow_break_word( $allowed ) {
			$allowed[] = 'word-break';
			$allowed[] = 'display';

			return $allowed;
		}

		/**
		 * Shortens string
		 *
		 * @param type $after
		 * @param type $length
		 *
		 * @return type
		 */
		public function short_str( $str, $after, $length ) {
			if ( empty( $str ) ) {
				$str = explode( ' ', get_the_title(), $length );
			} else {
				$str = explode( ' ', $str, $length );
			}
			if ( count( $str ) >= $length ) {
				array_pop( $str );
				$str = implode( ' ', $str ) . $after;
			} else {
				$str = implode( ' ', $str );
			}
			return $str;
		}

		/**
		 * Returns an array for recent posts
		 *
		 * @return boolean
		 */
		public function htmlemail_recent_posts() {
			// Recent Posts with their links
			$args         = array(
				'numberposts' => '4',
				'post_type'   => 'post',
				'post_status' => 'publish',
			);
			$recent_posts = wp_get_recent_posts( $args );
			return $recent_posts;
		}

		/**
		 * Send a preview email
		 */
		public function ajax_send_test_email() {
			$nonce_action = $this->get_nonce_action( 'send' );
			$this->check_input_data( $nonce_action, array( 'email' ) );

			$email = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

			if ( ! is_email( $email ) ) {
				$this->json_error( __( 'Unable to send: wrong email address.', 'ub' ) );
			}
			$sent = wp_mail(
				$email,
				esc_html__( 'Test HTML Email Subject', 'ub' ),
				esc_html__( "This is a test message I want to try out to see if it works\n\nIs it working well?", 'ub' )
			);
			if ( $sent ) {
				$this->uba->add_message(
					array(
						'type'    => 'success',
						'message' => sprintf( __( 'Test email sent to %s!', 'ub' ), $this->bold( $email ) ),
					)
				);
				wp_send_json_success();
			}
			$this->json_error( __( 'Unable to send test email', 'ub' ) );
		}

		/**
		 * Return Content type as HTML for plain text email
		 *
		 * @param $content_type
		 *
		 * @return string, Content type
		 */

		public function set_content_type( $content_type ) {
			if ( 'text/plain' == $content_type ) {
				$this->is_html = false;
				return 'text/html';
			}
			$this->is_html = true;
			return $content_type;
		}

		/**
		 * Set Content type for Woocommerce emails
		 */
		public function set_woocommerce_content_type( $content_type ) {
			return 'Content-Type: ' . 'text/html' . "\r\n";
		}

		/**
		 * Helper function to get template chooser
		 *
		 * @since 3.0.0
		 */
		public function get_template_configuration() {
			$has_configuration = $this->has_configuration();
			$content           = $this->template_button( $has_configuration );
			/**
			 * Dialog: Content
			 */
			$dialog = sprintf(
				'<p class="sui-description">%s</p>',
				esc_html__( 'Customize one of our pre-designed email templates, or start styling from scratch.', 'ub' )
			);
			/**
			 * Warning
			 */
			$value = $this->has_configuration();
			if ( $value ) {
				$dialog .= Branda_Helper::sui_notice( esc_html__( 'Be careful, changing a template would override the customization you\'ve done.', 'ub' ), 'warning' );
			}
			$dialog  .= '<div class="sui-box-selectors">';
			$dialog  .= '<ul>';
			$elements = $this->get_themes();
			$scratch  = array(
				'Name' => __( 'Start from Scratch', 'ub' ),
				'id'   => 'scratch',
			);
			array_unshift( $elements, $scratch );
			$theme_id = $this->get_value( 'theme', 'id' );
			foreach ( $elements as $value ) {
				$checked = false;
				$id      = $this->get_name( $value['id'] );
				$classes = array(
					'branda-email-template-li',
					$id,
				);
				if ( $theme_id === $value['id'] ) {
					$classes[] = 'branda-selected';
					$checked   = true;
				}
				$classes = implode( ' ', $classes );
				$dialog .= sprintf(
					'<li class="%s">',
					esc_attr( $classes )
				);
				$dialog .= sprintf(
					'<label for="%s" class="sui-box-selector">',
					esc_attr( $id )
				);
				$dialog .= sprintf(
					'<input type="radio" name="branda-email-template-template" value="%s" id="%s" %s />',
					esc_attr( $value['id'] ),
					esc_attr( $id ),
					checked( $checked, true, false )
				);
				if ( isset( $value['screenshot'] ) ) {
					$dialog .= sprintf(
						'<span class="branda-template-container" style="background-image:url(%s);">',
						esc_attr( $value['screenshot'] )
					);
				} else {
					$dialog .= '<span class="branda-template-container">';
				}
				$dialog .= '<span class="email-template-icon"><i class="sui-icon-clipboard-notes" aria-hidden="true"></i></span>';
				$dialog .= sprintf(
					'<span class="email-template-title">%s</span>',
					esc_html( $value['Name'] )
				);
				$dialog .= '</span>';
				$dialog .= '</label></li>';
			}
			$dialog .= '</ul>';
			$dialog .= '</div>';
			/**
			 * Dialog: footer
			 */
			$footer = '';
			if ( $has_configuration ) {
				$args    = array(
					'text' => __( 'Cancel', 'ub' ),
					'sui'  => 'ghost',
					'data' => array(
						'modal-close' => '',
					),
				);
				$footer .= $this->button( $args );
			}
			$args     = array(
				'text'  => __( 'Continue', 'ub' ),
				'sui'   => false,
				'class' => 'branda-email-template-choose-template',
				'data'  => array(
					'nonce' => $this->get_nonce_value( 'template' ),
				),
			);
			$footer  .= $this->button( $args );
			$args     = array(
				'id'      => $this->get_name( 'choose-template' ),
				'title'   => __( 'Choose a Template', 'ub' ),
				'content' => $dialog,
				'footer'  => array(
					'content' => $footer,
					'classes' => array(
						$has_configuration ? 'sui-space-between' : 'sui-actions-right',
					),
				),
				'classes' => array(
					'sui-modal-sm',
					'branda-email-template-choose-template-dialog',
					'branda-choose-template-dialog',
				),
			);
			$content .= $this->sui_dialog( $args );
			return $content;
		}

		private function template_button( $has_configuration ) {
			$value = $this->get_value( 'theme', 'id' );

			return $this->render(
				'admin/common/options/template-picker',
				array(
					'has_configuration' => $has_configuration,
					'screenshot'        => empty( $value ) ? '' : $this->get_theme_screenshot( $value ),
					'dialog_id'         => $this->get_name( 'choose-template' ),
				),
				true
			);
		}

		/**
		 * Add button after title.
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
		 * Returns HTML for a selected template
		 *
		 * @since 3.0.0
		 */
		public function ajax_set_template() {
			$nonce_action = $this->get_nonce_action( 'template' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$id = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			if ( ! isset( $this->allowed_ids[ $id ] ) ) {
				wp_send_json_error();
			}
			$theme_id = $this->allowed_ids[ $id ];
			$data     = array(
				'id'         => $theme_id,
				'content'    => $this->get_contents_elements( $theme_id ),
				'screenshot' => $this->get_theme_screenshot( $theme_id ),
			);
			wp_send_json_success( $data );
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module  Current module.
		 */
		public function add_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$template = '/admin/common/dialogs/test-email';
			$args     = array(
				'id'          => $this->get_name( 'send' ),
				'description' => __( 'Send a test email to preview the HTML email template in your preferred email client.', 'ub' ),
				'nonce'       => $this->get_nonce_value( 'send' ),
				'action'      => $this->get_name( 'send' ),
			);
			$content .= $this->render( $template, $args, true );
			/**
			 * Preview
			 */
			$args     = array(
				'id' => $this->get_name( 'preview' ),
			);
			$template = sprintf( '/admin/modules/%s/dialogs/preview', $this->module );
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		public function esc_data( $data, $data_orig, $module, $section, $name ) {
			if ( $this->module !== $module ) {
				return $data;
			}

			return $data_orig;
		}
	}
}
new Branda_Email_Template();
