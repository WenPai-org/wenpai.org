<?php
/**
 * Branda Accessibility class.
 *
 * Class that handle accessibility settings functionality.
 *
 * @since      3.0.0
 *
 * @package Branda
 * @subpackage Settings
 */
if ( ! class_exists( 'Branda_Accessibility' ) ) {

	/**
	 * Class Branda_Accessibility.
	 */
	class Branda_Accessibility extends Branda_Helper {

		/**
		 * Module option name.
		 *
		 * @since 3.0.0
		 *
		 * @var string
		 */
		protected $option_name = 'ub_accessibility';

		/**
		 * Branda_Accessibility constructor.
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// Set module name.
			$this->module = 'accessibility';
			parent::__construct();
			// Handle module settings.
			add_filter( 'ultimatebranding_settings_accessibility', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_accessibility_process', array( $this, 'update' ) );
			// Add custom content title.
			add_filter( 'branda_before_module_form', array( $this, 'add_title_before_form' ), 10, 2 );
			// Change bottom save button params.
			add_filter( 'branda_after_form_save_button_args', array( $this, 'change_bottom_save_button' ), 10, 2 );
		}

		/**
		 * Build form with options.
		 *
		 * Set settings form fields for the module.
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$options       = array(
				'description'   => array(
					'content' => __( 'Enable support for any accessibility enhancements available in the plugin interface.', 'ub' ),
				),
				'accessibility' => array(
					'title'       => __( 'High Contrast Mode', 'ub' ),
					'description' => __( 'Increase the visibility and accessibility of the elements and components of the plugin to meet WCAG AAA requirements.', 'ub' ),
					'fields'      => array(
						'high_contrast' => array(
							'checkbox_label' => __( 'Enable high contrast mode', 'ub' ),
							'description'    => array(
								'content'  => '',
								'position' => 'bottom',
							),
							'type'           => 'checkbox',
							'classes'        => array( 'switch-button' ),
							'default'        => 'off',
						),
					),
				),
			);
			$this->options = $options;
		}

		/**
		 * Add title before form.
		 *
		 * @param string $content Current content.
		 * @param array  $module  Current module.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function add_title_before_form( $content, $module ) {
			if ( $this->module === $module['module'] ) {
				$template = $this->get_template_name( 'header' );
				$content .= $this->render( $template, array(), true );
			}
			return $content;
		}
	}
}
new Branda_Accessibility();
