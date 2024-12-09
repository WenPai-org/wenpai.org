<?php
/**
 * The settings.
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( Settings::class ) ) :
	/**
	 * The controller class for the settings.
	 */
	class Settings extends CoreComponent {
		/**
		 * Setting page's hook suffix.
		 *
		 * @var string
		 */
		private $hook_suffix;

		/**
		 * The plugin title
		 *
		 * @var string
		 */
		private $plugin_title = 'Meta Field Block';

		/**
		 * The first path
		 *
		 * @var string
		 */
		private $first_path = '/options-general.php?page=mfb-settings&tab=getting-started';

		/**
		 * Setting pages
		 *
		 * @var array
		 */
		private $setting_pages = [
			'settings_page_mfb-settings-account',
			'admin_page_mfb-settings-account',
			'admin_page_mfb-settings-account-network',
			'settings_page_mfb-settings-contact',
			'admin_page_mfb-settings-contact',
			'admin_page_mfb-settings-contact-network',
			'settings_page_mfb-settings-pricing',
			'admin_page_mfb-settings-pricing',
			'admin_page_mfb-settings-pricing-network',
		];

		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Create the settings page.
			add_action( 'admin_menu', [ $this, 'add_admin_page' ] );

			// Enqueue settings script.
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_settings_scripts' ] );

			// Do setting up stuff when the plugin is activated.
			add_action( 'meta_field_block_activate', [ $this, 'run_the_plugin_setup' ] );

			// Redirect to the getting started page.
			add_action( 'admin_init', [ $this, 'meta_field_block_activation_redirect' ] );

			// Add the settings page link to plugin list screen.
			add_action( 'plugin_action_links_' . plugin_basename( MFB_ROOT_FILE ), [ $this, 'plugin_settings_links' ] );

			// Add rest api endpoint to query docs.
			add_action( 'rest_api_init', [ $this, 'register_docs_endpoint' ] );

			// Add admin toolbar.
			add_action( 'in_admin_header', [ $this, 'in_admin_header' ] );

			// Change the footer text for the settings pages.
			add_action( 'admin_footer_text', [ $this, 'admin_footer_text' ] );
		}

		/**
		 * Print the admin toolbar.
		 *
		 * @return void
		 */
		public function in_admin_header() {
			$screen = get_current_screen();
			if ( $this->hook_suffix === $screen->id || in_array( $screen->id, $this->setting_pages, true ) ) {
				// Left links.
				$left_links = apply_filters( 'meta_field_block_get_header_left_links', [] );

				$right_links = [
					[
						'url'    => 'https://wordpress.org/support/plugin/display-a-meta-field-as-block/',
						'title'  => __( 'Help & Support ↗', 'display-a-meta-field-as-block' ),
						'target' => '_blank',
						'icon'   => '<span class="dashicons dashicons-editor-help"></span> ',
					],
					[
						'url'    => 'https://wordpress.org/support/plugin/display-a-meta-field-as-block/reviews/#new-post',
						'title'  => __( 'Review ↗', 'display-a-meta-field-as-block' ),
						'target' => '_blank',
						'icon'   => '<span class="dashicons dashicons-star-filled"></span> ',
					],
				];

				$right_links = apply_filters( 'meta_field_block_get_header_right_links', $right_links );
				?>
				<div class="mfb-settings-header">
					<h1><strong><?php esc_html_e( $this->plugin_title ); ?></strong> <code><?php esc_html_e( $this->the_plugin_instance->get_plugin_version() ); ?></code></h1>
					<ul class="lelf-links">
						<?php foreach ( $left_links as $link ) : ?>
							<?php printf( '<li %5$s><a href="%1$s" target="%3$s"%6$s>%4$s%2$s</a></li>', $link['url'], $link['title'], $link['target'], $link['icon'], $screen->id === $link['id'] ? 'class="is-active"' : '', $link['style'] ?? '' ? ' style="' . $link['style'] . '"' : '' ); ?>
						<?php endforeach; ?>
					</ul>
					<ul class="right-links">
						<?php foreach ( $right_links as $link ) : ?>
							<?php printf( '<li><a href="%1$s" target="%3$s">%4$s%2$s</a></li>', $link['url'], $link['title'], $link['target'], $link['icon'] ); ?>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
			}
		}

		/**
		 * Create the admin page
		 *
		 * @return void
		 */
		public function add_admin_page() {
			$this->hook_suffix = add_options_page(
				$this->plugin_title,
				$this->plugin_title,
				'manage_options',
				'mfb-settings',
				function () {
					?>
					<div class="wrap">
						<h2 class="screen-reader-text"><?php esc_html_e( $this->plugin_title ); ?></h2>
						<div class="mfb-settings js-mfb-settings-root"></div>
					</div>
					<?php
				},
				80
			);
		}

		/**
		 * Enqueue scripts for the settings page.
		 *
		 * @param string $hook_suffix
		 * @return void
		 */
		public function enqueue_settings_scripts( $hook_suffix ) {
			// Only load scripts for the settings page.
			if ( $this->hook_suffix === $hook_suffix || in_array( $hook_suffix, $this->setting_pages, true ) ) {
				// Settings asset file.
				$settings_asset = $this->the_plugin_instance->include_file( 'build/settings.asset.php' );

				// Enqueue scripts.
				wp_enqueue_script(
					'mfb-settings',
					$this->the_plugin_instance->get_file_uri( 'build/settings.js' ),
					$settings_asset['dependencies'] ?? [],
					$this->the_plugin_instance->get_script_version( $settings_asset ),
					true
				);

				wp_set_script_translations( 'mfb-settings', 'display-a-meta-field-as-block' );

				// Enqueue style.
				wp_enqueue_style(
					'mfb-settings',
					$this->the_plugin_instance->get_file_uri( 'build/settings.css' ),
					[],
					$this->the_plugin_instance->get_script_version( $settings_asset )
				);

				// Load components style.
				wp_enqueue_style( 'wp-components' );
			}
		}

		/**
		 * Run activated stuff
		 *
		 * @return void
		 */
		public function run_the_plugin_setup() {
			if ( ! function_exists( 'get_current_screen' ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( ! $screen || 'plugins' !== $screen->id ) {
				return;
			}

			// Redirect to the getting started page, ignore bulk activation.
			if (
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				! ( ( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
			( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) ) {
				add_option( 'meta_field_block_activation_redirect', wp_get_current_user()->ID );
			}
		}

		/**
		 * Redirect to the getting started page.
		 *
		 * @return void
		 */
		public function meta_field_block_activation_redirect() {
			// Make sure it's the correct user.
			if ( ! wp_doing_ajax() && wp_get_current_user()->ID > 0 && intval( get_option( 'meta_field_block_activation_redirect', false ) ) === wp_get_current_user()->ID ) {
				// Make sure we don't redirect again after this one.
				delete_option( 'meta_field_block_activation_redirect' );
				if ( ! is_network_admin() ) {
					wp_safe_redirect( admin_url( $this->first_path ) );
					exit;
				}
			}
		}

		/**
		 * Add the settings page link to the plugin admin screen.
		 *
		 * @param array $links
		 * @return array
		 */
		public function plugin_settings_links( $links ) : array {
			array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', admin_url( $this->first_path ), esc_html__( 'Settings', 'display-a-meta-field-as-block' ) ) );
			return $links;
		}

		/**
		 * Build a custom endpoint to query docs.
		 *
		 * @return void
		 */
		public function register_docs_endpoint() {
			register_rest_route(
				'mfb/v1',
				'/getDocs/',
				array(
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_docs' ],
					'permission_callback' => function () {
						return current_user_can( 'publish_posts' );
					},
				)
			);
		}

		/**
		 * Get docs.
		 *
		 * @param WP_REST_Request $request The request object.
		 * @return void
		 */
		public function get_docs( $request ) {
			$external_url = 'https://metafieldblock.com/docs/';
			$data         = [
				'videos' => [
					'basicFields'      => [
						'url'     => $external_url . 'basic-fields.mp4',
						'title'   => 'How to use it in the Editor.',
						'caption' => 'Display core post meta field, ACF Text, Image, Link fields',
					],
					'siteEditorFields' => [
						'url'     => $external_url . 'fields-in-site-editor.mp4',
						'title'   => 'How to display term meta fields in the Site Editor.',
						'caption' => 'Display custom fields for terms in a taxonomy template',
					],
					'settingFields'    => [
						'url'     => $external_url . 'setting-fields.mp4',
						'caption' => 'Display core setting field, ACF Text, Image, Relationship, Group fields',
					],
					'queryFields'      => [
						'url'     => $external_url . 'query-fields.mp4',
						'caption' => 'Display ACF Relationship, or Post Object fields',
					],
					'otherItemFields'  => [
						'url'     => $external_url . 'other-item-fields.mp4',
						'caption' => 'Display an ACF field from another post',
					],
					'repeaterFields'   => [
						'url'     => $external_url . 'repeater-fields.mp4',
						'title'   => 'How to display the ACF Repeater field.',
						'caption' => 'Display an ACF repeater field as a grid',
					],
					'groupFields'      => [
						'url'     => $external_url . 'group-fields.mp4',
						'title'   => 'How to display the ACF Group field.',
						'caption' => 'Display an ACF group field',
					],
					'urlFields'        => [
						'url'     => $external_url . 'url-fields.mp4',
						'caption' => 'Display an ACF URL field as a link, a core button, or an image block',
					],
					'emailFileFields'  => [
						'url'     => $external_url . 'email-file-fields.mp4',
						'caption' => 'Display ACF Email and File fields',
					],
					'galleryFields'    => [
						'url'     => $external_url . 'gallery-field.mp4',
						'caption' => 'Display an ACF Gallery field',
					],
					'fileVideoFields'  => [
						'url'     => $external_url . 'file-video-field.mp4',
						'caption' => 'Display an ACF File field as a video',
					],
				],
			];

			wp_send_json(
				[
					'data'    => $data,
					'success' => true,
				]
			);
		}

		/**
		 * Clear transient cache
		 *
		 * @return void
		 */
		public function clear_transient_cache() {
			delete_transient( 'mfb_docs' );
		}

		/**
		 * Change the footer text for the settings pages
		 *
		 * @param string $footer_text
		 * @return string
		 */
		public function admin_footer_text( $footer_text ) {
			// Get current screen.
			$current_screen = get_current_screen();
			if ( $this->hook_suffix === $current_screen->id ) {
				$footer_text = '<i><strong>' . esc_html__( $this->plugin_title ) . '</strong> <code>' . esc_html__( $this->the_plugin_instance->get_plugin_version() ) . '</code>. Please <a target="_blank" href="https://wordpress.org/support/plugin/display-a-meta-field-as-block/reviews/#new-post" title="Rate the plugin" style="text-decoration:none">rate the plugin <span style="color:#ffb900">★★★★★</span></a> to help us spread the word. Thank you from the <a href="https://metafieldblock.com/?utm_source=Meta+Field+Block&utm_campaign=Meta+Field+Block+visit+site&utm_medium=link&utm_content=footer-text" target="_blank" title="Visit the Plugin website" style="text-decoration:none"><strong>MFB</strong></a> team!</i>';
			}

			return $footer_text;
		}
	}
endif;
