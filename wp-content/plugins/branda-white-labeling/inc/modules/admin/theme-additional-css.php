<?php
/**
 * Theme Additional CSS  class.
 *
 * @package Branda
 * @subpackage Widgets
 *
 * @since 3.1.3
 */
if ( ! class_exists( 'Branda_Theme_Aditional_CSS' ) ) {
	class Branda_Theme_Aditional_CSS extends Branda_Helper {

		public function __construct() {
			parent::__construct();
			add_filter( 'ultimatebranding_settings_theme_additional_css', array( $this, 'admin_options_page' ) );
			add_filter( 'map_meta_cap', array( $this, 'allow_edit_css' ), 10, 4 );
		}

		/**
		 * set options
		 *
		 * @since 3.1.3
		 */
		protected function set_options() {
			$message       = sprintf(
				__( 'To add custom CSS, go to your theme\'s <a href="%s">Customizer</a> menu.', 'ub' ),
				admin_url( 'customize.php' )
			);
			$notice        = Branda_Helper::sui_notice( $message, 'info' );
			$options       = array(
				'desc' => array(
					'title'       => __( 'Customizer CSS', 'ub' ),
					'description' => __( 'This feature allows subsite admins to add custom CSS via the Theme Customizer tool.', 'ub' ),
					'fields'      => array(
						'html' => array(
							'type'  => 'description',
							'value' => $notice,
						),
					),
				),
			);
			$this->options = $options;
		}

		public function allow_edit_css( $caps, $cap, $user_id, $args ) {
			if ( 'edit_css' === $cap ) {
				if ( current_user_can( 'administrator' ) ) {
					return array( 'unfiltered_html' );
				}
			}
			return $caps;
		}
	}
}
new Branda_Theme_Aditional_CSS();
