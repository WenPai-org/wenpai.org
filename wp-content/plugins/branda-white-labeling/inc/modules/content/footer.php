<?php
/**
 * Branda Content Footer class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Content_Footer' ) ) {

	/**
	 * Class Branda_Content_Footer.
	 */
	class Branda_Content_Footer extends Branda_Helper {

		/**
		 * Setting option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_global_footer_content';

		protected $esc_callback = array( __CLASS__, 'esc_data' );

		/**
		 * Constructor for Branda_Content_Footer.
		 *
		 * Register all hooks for the module.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'content-footer';
			add_filter( 'ultimatebranding_settings_content_footer', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_content_footer_process', array( $this, 'update' ), 10, 1 );
			add_action( 'wp_footer', array( $this, 'output' ) );
			add_filter( 'ultimate_branding_options_names', array( $this, 'add_legacy_options_names' ) );
			/**
			 * Try to get network-wide value
			 *
			 * @since 3.2.0
			 */
			add_filter( 'ub_get_option-' . $this->option_name, array( $this, 'get_network_value' ), 10, 3 );
		}

		/**
		 * Add legacy option names to delete.
		 *
		 * @param array $options Options array.
		 *
		 * @since 2.1.0
		 *
		 * @return array $options
		 */
		public function add_legacy_options_names( $options ) {
			$keys = array( 'content', 'bgcolor', 'fixedheight', 'themefooter', 'shortcodes' );
			foreach ( $keys as $key ) {
				$options[] = 'global_footer_' . $key;
				$options[] = 'global_footer_main_' . $key;
			}
			return $options;
		}

		/**
		 * Get options in section.
		 *
		 * @param string $prefix Prefix.
		 *
		 * @return array
		 */
		private function get_section_options( $prefix ) {
			return array(
				'content'       => array(
					'type'        => 'wp_editor',
					'placeholder' => esc_html__( 'You can write footer content here…', 'ub' ),
					'accordion'   => array(
						'begin' => true,
						'end'   => true,
						'title' => __( 'Content', 'ub' ),
					),
				),
				'height'        => array(
					'label'        => __( 'Height', 'ub' ),
					'after_label'  => __( 'px', 'ub' ),
					'type'         => 'number',
					'min'          => 0,
					'default'      => 50,
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Design', 'ub' ),
					),
					'master'       => $this->get_name( $prefix . '-height' ),
					'master-value' => 'custom',
					'display'      => 'sui-tab-content',
				),
				'height_status' => array(
					'label'       => __( 'Height', 'ub' ),
					'description' => __( 'Let your content define the height or set a fixed custom height for footer content.', 'ub' ),
					'type'        => 'sui-tab',
					'options'     => array(
						'auto'   => __( 'Auto', 'ub' ),
						'custom' => __( 'Custom', 'ub' ),
					),
					'default'     => 'auto',
					'slave-class' => $this->get_name( $prefix . '-height' ),
				),
				'color'         => array(
					'label'        => __( 'Text', 'ub' ),
					'type'         => 'color',
					'master'       => $this->get_name( $prefix . '-color' ),
					'master-value' => 'custom',
					'display'      => 'sui-tab-content',
					'default'      => '#000',
				),
				'background'    => array(
					'label'        => __( 'Background', 'ub' ),
					'type'         => 'color',
					'master'       => $this->get_name( $prefix . '-color' ),
					'master-value' => 'custom',
					'display'      => 'sui-tab-content',
					'default'      => '#fff',
				),
				'color_status'  => array(
					'label'       => __( 'Colors', 'ub' ),
					'description' => __( 'You can use the default color scheme or customize it to match your theme.', 'ub' ),
					'type'        => 'sui-tab',
					'options'     => array(
						'default' => __( 'Default', 'ub' ),
						'custom'  => __( 'Custom Colors', 'ub' ),
					),
					'default'     => 'default',
					'slave-class' => $this->get_name( $prefix . '-color' ),
					'accordion'   => array(
						'end' => true,
					),
				),
				'theme_footer'  => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Integrate into theme footer', 'ub' ),
					'description' => __( 'Enable this to place the footer content block inside the theme footer element.', 'ub' ),
					'options'     => array(
						'off' => __( 'Disable', 'ub' ),
						'on'  => __( 'Enable', 'ub' ),
					),
					'default'     => 'on',
					'accordion'   => array(
						'begin'   => true,
						'title'   => __( 'Settings', 'ub' ),
						'classes' => array(
							'body' => array(
								'branda-content-settings',
							),
						),
					),
				),
				'shortcodes'    => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Parse shortcodes', 'ub' ),
					'description' => __( 'Be careful, parsing shortcodes can break the theme UI.', 'ub' ),
					'options'     => array(
						'off' => __( 'Disable', 'ub' ),
						'on'  => __( 'Enable', 'ub' ),
					),
					'default'     => 'off',
					'accordion'   => array(
						'end' => true,
					),
				),
			);
		}

		/**
		 * Set options.
		 *
		 * @since 2.1.0
		 */
		protected function set_options() {
			$options = array(
				'content'  => array(
					'title'       => __( 'Content', 'ub' ),
					'description' => __( 'Insert any content that you like into the footer of every page of your website.', 'ub' ),
					'fields'      => array(
						'content' => array(
							'type'        => 'wp_editor',
							'label'       => __( 'Content', 'ub' ),
							'placeholder' => esc_html__( 'You can write footer content here…', 'ub' ),
						),
					),
				),
				'design'   => array(
					'title'       => __( 'Design', 'ub' ),
					'description' => __( 'Customize the design of footer content as per your liking.', 'ub' ),
					'fields'      => array(
						'height'        => array(
							'label'        => __( 'Height', 'ub' ),
							'after_label'  => __( 'px', 'ub' ),
							'type'         => 'number',
							'min'          => 0,
							'default'      => 50,
							'master'       => $this->get_name( 'height' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
						),
						'height_status' => array(
							'label'       => __( 'Height', 'ub' ),
							'description' => __( 'Let your content define the height or set a fixed custom height for footer content.', 'ub' ),
							'type'        => 'sui-tab',
							'options'     => array(
								'auto'   => __( 'Auto', 'ub' ),
								'custom' => __( 'Custom', 'ub' ),
							),
							'default'     => 'auto',
							'slave-class' => $this->get_name( 'height' ),
						),
						'color'         => array(
							'label'        => __( 'Text', 'ub' ),
							'type'         => 'color',
							'master'       => $this->get_name( 'color' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'default'      => '#000',
						),
						'background'    => array(
							'label'        => __( 'Background', 'ub' ),
							'type'         => 'color',
							'master'       => $this->get_name( 'color' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'default'      => '#fff',
						),
						'color_status'  => array(
							'label'       => __( 'Colors', 'ub' ),
							'description' => __( 'You can use the default color scheme or customize it to match your theme.', 'ub' ),
							'type'        => 'sui-tab',
							'options'     => array(
								'default' => __( 'Default', 'ub' ),
								'custom'  => __( 'Custom Colors', 'ub' ),
							),
							'default'     => 'default',
							'slave-class' => $this->get_name( 'color' ),
						),
					),
				),
				'settings' => array(
					'title'       => __( 'Settings', 'ub' ),
					'description' => __( 'These provide additional configuration options for the footer content.', 'ub' ),
					'fields'      => array(
						'theme_footer' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Integrate into theme footer', 'ub' ),
							'description' => __( 'Enable this to place the footer content block inside the theme footer element.', 'ub' ),
							'options'     => array(
								'off' => __( 'Disable', 'ub' ),
								'on'  => __( 'Enable', 'ub' ),
							),
							'default'     => 'on',
						),
						'shortcodes'   => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Parse shortcodes', 'ub' ),
							'description' => __( 'Be careful, parsing shortcodes can break the theme UI.', 'ub' ),
							'options'     => array(
								'off' => __( 'Disable', 'ub' ),
								'on'  => __( 'Enable', 'ub' ),
							),
							'default'     => 'off',
						),
					),
				),
			);

			/**
			 * On multisite if we have existing options and they are structured in the old (deprecated)
			 * way, display the old settings interface to match
			 */
			if (
				$this->is_network && is_network_admin()
				&& $this->has_deprecated_structure( branda_get_option( $this->option_name ) )
			) {
				$options = array(
					'main'            => array(
						'title'       => __( 'Main Site', 'ub' ),
						'description' => __( 'Insert any content that you like into the footer of the main site of your network.', 'ub' ),
						'show-as'     => 'accordion',
						'show-reset'  => true,
						'fields'      => $this->get_section_options( 'main' ),
					),
					// Sub section one.
					'subsites_option' => array(
						'network-only' => true,
						'title'        => __( 'Subsites', 'ub' ),
						'description'  => __( 'Insert any content that you like into the footer of all of the subsites on your network.', 'ub' ),
						'fields'       => array(
							'different' => array(
								'type'    => 'sui-tab',
								'options' => array(
									'off' => __( 'Same as Main Site', 'ub' ),
									'on'  => __( 'Insert Different Content', 'ub' ),
								),
								'default' => 'on',
								'classes' => 'ub-footer-subsites-toggle',
							),
						),
						'sub_section'  => 'start',
					),
					// Sub section two.
					'subsites'        => array(
						'show-as'     => 'accordion',
						'show-reset'  => true,
						'fields'      => $this->get_section_options( 'subsites' ),
						'sub_section' => 'end',
						'accordion'   => array(
							'classes' => array( 'ub-footer-subsites' ),
						),
					),
				);
				// Make sub site options hidden if disabled.
				$subsites_option = $this->get_value( 'subsites_option' );
				if ( isset( $subsites_option['different'] ) && 'off' === $subsites_option['different'] ) {
					$options['subsites']['accordion']['classes'][] = 'hidden';
				}
			} else {
				unset( $options['content']['fields']['content']['label'] );
			}
			$this->options = $options;
		}

		/**
		 * Output common helper.
		 *
		 * @param string/boolean $key Key, false for single site
		 *
		 * @access private
		 * @since  2.1.0
		 */
		private function output_content( $key ) {
			$content   = '';
			$is_single = false === $key;
			/**
			 * get settings
			 */
			if ( $is_single ) {
				$key = 'settings';
			}
			$value            = $this->get_value( $key, 'shortcodes', 'off' );
			$parse_shortcodes = 'on' === $value;
			$value            = $this->get_value( $key, 'theme_footer', 'off' );
			$use_theme_footer = 'on' === $value;
			/**
			 * get content
			 */
			if ( $is_single ) {
				$key = 'content';
			}
			// Try content.
			$value = $this->get_value( $key, 'content', false );
			
			if ( ! empty( $value ) ) {
				$content = $value;
				if ( ! empty( $content ) ) {
					$filters = array( 'wptexturize', 'convert_smilies', 'convert_chars', 'wpautop' );
					foreach ( $filters as $filter ) {
						$content = apply_filters( $filter, $content );
					}
				}
			}
			// At least - check.
			if ( empty( $content ) ) {
				return array();
			}
			/**
			 * Parese shortcodes if it is needed.
			 */
			if ( $parse_shortcodes ) {
				$content = do_shortcode( $content );
			}
			/**
			 * get design
			 */
			if ( $is_single ) {
				$key = 'design';
			}
			// Style: color & background
			$style = '';
			$value = $this->get_value( $key, 'color_status', false );
			if ( 'custom' === $value ) {
				$value = $this->get_value( $key, 'color', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_color( $value );
				}
				$value = $this->get_value( $key, 'background', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_background_color( $value );
				}
			}
			// Style: custom height
			$value = $this->get_value( $key, 'height_status', '' );
			if ( 'custom' === $value ) {
				$value = $this->get_value( $key, 'height', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_height( $value );
					$style .= 'overflow: hidden;';
				}
			}
			/**
			 * return
			 */
			return array(
				'style'            => preg_replace( '/[\r\n]/', '', $style ),
				'use_theme_footer' => $use_theme_footer,
				'content'          => $content,
			);
		}

		/**
		 * for single site
		 *
		 * @since 3.0.0
		 */
		private function output_content_for_single() {
			return $this->output_content( false );
		}

		/**
		 * Output the custom content.
		 *
		 * @since 2.0.0
		 */
		public function output() {
			$data = $this->output_content_for_single();
			if ( ! isset( $data['content'] ) || empty( $data['content'] ) ) {
				return;
			}
			/**
			 * JavaScript template
			 */
			$template    = sprintf( '/front-end/modules/%s/js-output', $this->module );
			$data['id']  = $this->get_name( 'js' );
			$data['tag'] = $data['use_theme_footer'] ? 'footer' : 'body';
			$this->render( $template, $data );
		}

		private function has_deprecated_structure( $options_array ) {
			return isset( $options_array['main'] )
				   && isset( $options_array['subsites'] );
		}

		/**
		 * Convert from network-wide value into sub-site value.
		 *
		 * @since 3.2.0
		 */
		public function get_network_value( $value, $option, $default ) {
			if ( ! $this->is_network ) {
				return $value;
			}
			if ( is_network_admin() ) {
				return $value;
			}
			if ( ! $this->has_deprecated_structure( $value ) ) {
				return $value;
			}
			$key      = is_main_site() || branda_get_array_value( $value, array( 'subsites_option', 'different' ) ) !== 'on'
				? 'main'
				: 'subsites';
			$skeleton = array(
				'content'  => array(),
				'design'   => array(),
				'settings' => array(),
			);
			$value    = wp_parse_args( $value[ $key ], $skeleton );
			if ( isset( $value['content'] ) ) {
				$value['content'] = array(
					'content' => $value['content'],
				);
			}
			$map = array(
				'content'  => array(
					'content_meta',
				),
				'design'   => array(
					'height_status',
					'height',
					'color',
					'color_status',
					'background',
				),
				'settings' => array(
					'theme_footer',
					'shortcodes',
				),
			);
			foreach ( $map as $group => $data ) {
				foreach ( $data as $key ) {
					if ( isset( $value[ $key ] ) ) {
						$value[ $group ][ $key ] = $value[ $key ];
						unset( $value[ $key ] );
					}
				}
			}
			return $value;
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

new Branda_Content_Footer();
