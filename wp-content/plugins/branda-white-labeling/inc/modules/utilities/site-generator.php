<?php
/**
 * Branda Site Generator class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Branda_Site_Generator' ) ) {

	class Branda_Site_Generator extends Branda_Helper {

		/**
		 * DB option name
		 *
		 * @since 3.0.0
		 */
		protected $option_name = 'ub_site_generator_replacement';

		/**
		 * Check Defender
		 *
		 * @since 3.0.0
		 */
		private $check_defender = false;

		public function __construct() {
			parent::__construct();
			$this->module = 'site-generator';
			add_filter( 'ultimatebranding_settings_site_generator', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_site_generator_process', array( $this, 'update' ) );
			/**
			 * replace
			 */
			add_filter( 'get_the_generator_html', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_xhtml', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_atom', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_rss2', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_rdf', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_comment', array( $this, 'replace' ), 99, 2 );
			add_filter( 'get_the_generator_export', array( $this, 'replace' ), 99, 2 );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add class when defender!
			 */
			if ( $this->check_defender ) {
				add_filter( 'branda_settings_tab_content_classes', array( $this, 'add_class_if_defender' ), 10, 2 );
			}
		}

		/**
		 * Set module options
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$options = array(
				'generator' => array(
					'title'       => __( 'Site Generator Info', 'ub' ),
					'description' => __( 'Change the default generator information shown in the page source of your website.', 'ub' ),
					'fields'      => array(
						'text'    => array(
							'label'       => __( 'Text', 'ub' ),
							'placeholder' => 'WordPress',
						),
						'link'    => array(
							'label' => __( 'Link', 'ub' ),
							'placeholder' => site_url(),
						),
						'version' => array(
							'label'       => __( 'Version', 'ub' ),
							'placeholder' => get_bloginfo( 'version' ),
						),
					),
				),
			);
			/**
			 * Check Defender
			 */
			if ( $this->check_defender ) {
				$defender = $this->check_wp_defender_plugin();
				if ( $defender ) {
					$options['generator']['classes'] = array( 'branda-not-affected' );
					/**
					 * get template
					 */
					$template            = $this->get_template_name( 'defender' );
					$args                = array(
						'link' => add_query_arg( 'page', 'wp-defender', network_admin_url( 'admin.php' ) ),
					);
					$options['defender'] = array(
						'fields' => array(
							'notice' => array(
								'type'    => 'raw',
								'content' => $this->render( $template, $args, true ),
							),
						),
					);
				}
			}
			$this->options = $options;
		}

		/**
		 *
		 * Upgrade module options
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = branda_get_option( 'site_generator_replacement' );
			if ( ! empty( $value ) ) {
				$this->set_value( 'generator', 'text', $value );
				branda_delete_option( 'site_generator_replacement' );
			}
			$value = branda_get_option( 'site_generator_replacement_link' );
			if ( ! empty( $value ) ) {
				$this->set_value( 'generator', 'link', $value );
				branda_delete_option( 'site_generator_replacement_link' );
			}
		}

		public function replace( $gen, $type ) {
			$text = $this->get_value( 'generator', 'text' );
			if ( empty( $text ) ) {
				$text = get_bloginfo( 'name' );
			}
			$link = $this->get_value( 'generator', 'link' );
			if ( empty( $link ) ) {
				$link = get_home_url();
			}
			$version = $this->get_value( 'generator', 'version' );
			if ( empty( $version ) ) {
				$version = get_bloginfo( 'version' );
			}
			/**
			 * mask
			 */
			$mask  = '<meta name="generator" content="%1$s %3$s - %2$s" />';
			$masks = array(
				'atom'    => '<generator uri="%2$s" version="%3$s">%1$s</generator>',
				'rss2'    => '<generator>%2$s?v=%3$s</generator>',
				'rdf'     => '<admin:generatorAgent rdf:resource="%2$s?v=%1$s" />',
				'comment' => '<!-- generator="%1$s/%1$s" -->',
				'export'  => '<!-- generator="%1$s/%1$s" created="%4$s"-->',
			);
			if ( isset( $masks[ $type ] ) ) {
				$mask = $masks[ $type ];
			}
			/**
			 * Generate
			 */
			$generator = sprintf(
				$mask,
				esc_attr( $text ),
				esc_attr( $link ),
				esc_attr( $version ),
				esc_attr( date( 'Y-m-d H:i' ) )
			);
			return $generator;
		}

		/**
		 * Add class to branda-settings-tab-content
		 *
		 * @since 3.0.0
		 *
		 * @param array $classes Classes
		 * @param array $module
		 */
		public function add_class_if_defender( $classes, $module ) {
			if (
				$this->check_defender
				&& $module['module'] === $this->module
			) {
				$defender = $this->check_wp_defender_plugin();
				if ( $defender ) {
					$classes[] = $this->get_name( 'is-defender' );
				}
			}
			return $classes;
		}

		/**
		 * Check Defender!
		 *
		 * @since 3.0.0
		 */
		private function check_wp_defender_plugin() {
			if ( ! $this->check_defender ) {
				return false;
			}
			if ( ! class_exists( 'wp_defender' ) ) {
				return false;
			}
			if ( ! is_object( $this->uba ) ) {
				return false;
			}
			$defender = wp_defender();
			return is_a( $defender, 'WP_Defender' );
		}
	}
}
new Branda_Site_Generator();
