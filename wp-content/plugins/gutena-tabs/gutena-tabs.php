<?php // @codingStandardsIgnoreLine
/**
 * Plugin Name:     Gutena Tabs
 * Description:     Gutena Tabs is a simple and easy-to-use WordPress plugin which allows you to create beautiful tabs in your posts and pages. The plugin is simple to use but provides many customization options so you can create tabs that look great and fit into your design. Additionally, You can add beautiful icons to the tabs.
 * Version:         1.0.7
 * Author:          ExpressTech
 * Author URI:      https://expresstech.io
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     gutena-tabs
 *
 * @package         gutena-tabs
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abort if the class is already exists.
 */
if ( ! class_exists( 'Gutena_Tabs' ) ) {

	/**
	 * Gutena Advanced Tabs class.
	 *
	 * @class Main class of the plugin.
	 */
	class Gutena_Tabs {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		public $version = '1.0.7';

		/**
		 * Child Block styles.
		 *
		 * @since 1.0.1
		 * @var array
		 */
		public $styles = [];

		/**
		 * Instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		protected static $instance;

		/**
		 * Get the singleton instance of this class.
		 *
		 * @since 1.0.0
		 * @return Gutena_Tabs
		 */
		public static function get() {
			if ( ! ( self::$instance instanceof self ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'init', [ $this, 'register' ] );
			add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );
			add_filter( 'block_type_metadata', [ $this, 'block_type_metadata' ] );
		}

		/**
		 * Register required functionalities.
		 */
		public function register() {
			// Register blocks.
			register_block_type( __DIR__ . '/build', [
				'render_callback' => [ $this, 'render_block' ],
			] );

			// Register blocks.
			register_block_type( __DIR__ . '/build/tab', [
				'render_callback' => [ $this, 'render_tab_block' ],
			] );
		}

		/**
		 * Render Gutena play button block.
		 */
		public function render_tab_block( $attributes, $content, $block ) {
			if ( ! empty( $attributes['blockStyles'] ) && is_array( $attributes['blockStyles'] ) && ! empty( $attributes['tabBorder']['enable'] ) ) {
				if ( ! empty( $attributes['parentUniqueId'] ) && ! empty( $attributes['tabId'] ) ) {
					$this->styles[ $attributes['parentUniqueId'] ][ $attributes['tabId'] ] = $attributes['blockStyles'];
				}
			}
			
			return $content;
		}

		/**
		 * Render Gutena play button block.
		 */
		public function render_block( $attributes, $content, $block ) {
			//$css = '';
			if ( ! empty( $attributes['uniqueId'] ) ) {
				$unique_id = $attributes['uniqueId'];

				if ( ! empty( $attributes['blockStyles'] ) && is_array( $attributes['blockStyles'] ) ) {
					$css = sprintf( 
						'.gutena-tabs-block-%1$s { %2$s }',
						esc_attr( $unique_id ),
						$this->render_css( $attributes['blockStyles'] ),
					);

					if ( ! empty( $this->styles[ $unique_id ] ) ) {
						foreach ( $this->styles[ $unique_id ] as $tab_id => $style ) {
							$css .= sprintf( 
								'.gutena-tabs-block-%1$s .gutena-tabs-tab .gutena-tab-title[data-tab="%2$s"] { %3$s }',
								esc_attr( $unique_id ),
								esc_attr( $tab_id ),
								$this->render_css( $style ),
							);
						}
					}

					// print css
					if ( ! empty( $css ) ) {
						$style_id = 'gutena-tabs-css-' . $unique_id;

						if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'gutena_tabs_render_head_css', true, $attributes ) ) {
							$this->render_inline_css( $css, $style_id, true );
						}
					}
				}
			}

			return $content;
		}

		/**
		 * Filter Core button metadata to add custom attributes.
		 */
		public function block_type_metadata( $metadata ) {
			if ( 'core/button' === $metadata['name'] ) {
				$metadata['attributes']['gutenaOpenTab'] = [
					'type'    => 'string',
					'default' => 'none',
				];
			}
	
			return $metadata;
		}

		/**
		 * Generate dynamic styles
		 *
		 * @param array $styles
		 * @return string
		 */
		private function render_css( $styles ) {
			$style = [];
			foreach ( ( array ) $styles as $key => $value ) {
				$style[] = $key . ': ' . $value;
			}

			return join( ';', $style );
		}

		/**
		 * Render Inline CSS helper function
		 *
		 * @param array  $css the css for each rendered block.
		 * @param string $style_id the unique id for the rendered style.
		 * @param bool   $in_content the bool for whether or not it should run in content.
		 */
		private function render_inline_css( $css, $style_id, $in_content = false ) {
			if ( ! is_admin() ) {
				wp_register_style( $style_id, false );
				wp_enqueue_style( $style_id );
				wp_add_inline_style( $style_id, $css );
				if ( 1 === did_action( 'wp_head' ) && $in_content ) {
					wp_print_styles( $style_id );
				}
			}
		}

		/**
		 * Register block category.
		 */
		public function register_category( $block_categories, $editor_context ) {
			$fields = wp_list_pluck( $block_categories, 'slug' );
			
			if ( ! empty( $editor_context->post ) && ! in_array( 'gutena', $fields, true ) ) {
				array_push(
					$block_categories,
					[
						'slug'  => 'gutena',
						'title' => __( 'Gutena', 'gutena-tabs' ),
					]
				);
			}

			return $block_categories;
		}
	}
}

/**
 * Check the existance of the function.
 */
if ( ! function_exists( 'gutena_tabs_init' ) ) {
	/**
	 * Returns the main instance of Gutena_Tabs to prevent the need to use globals.
	 *
	 * @return Gutena_Tabs
	 */
	function gutena_tabs_init() {
		return Gutena_Tabs::get();
	}

	// Start it.
	gutena_tabs_init();
}

// Gutena Ecosystem init.
if ( file_exists( __DIR__ . '/includes/gutena/gutena-ecosys-onboard/gutena-ecosys-onboard.php' ) ) {
	require_once  __DIR__ . '/includes/gutena/gutena-ecosys-onboard/gutena-ecosys-onboard.php';
}