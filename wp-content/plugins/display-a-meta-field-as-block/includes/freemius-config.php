<?php
/**
 * Freemius utilities
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( FreemiusConfig::class ) ) :
	/**
	 * The FreemiusConfig class.
	 */
	class FreemiusConfig extends CoreComponent {
		/**
		 * The constructor
		 */
		public function __construct( $the_plugin_instance ) {
			parent::__construct( $the_plugin_instance );
		}

		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Add header left links.
			add_filter( 'meta_field_block_get_header_left_links', [ $this, 'header_links' ] );

			// Add the settings page link to plugin list screen.
			// add_action( 'plugin_action_links_' . plugin_basename( MFB_ROOT_FILE ), [ $this, 'plugin_settings_links' ] );

			// Add data to the setting page.
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_data_on_the_setting_page' ] );
		}

		/**
		 * Add freemius pages
		 *
		 * @param array $links
		 * @return array
		 */
		public function header_links( $links ) {
			global $mfb_fs;

			if ( $mfb_fs->is_activation_mode() || $mfb_fs->is_pending_activation() ) {
				return $links;
			}

			$is_network_activate = is_multisite() && $mfb_fs->is_network_active();

			$custom_links   = [];
			$custom_links[] = [
				'url'    => admin_url( '/options-general.php?page=mfb-settings&tab=getting-started' ),
				'title'  => __( 'Getting started', 'display-a-meta-field-as-block' ),
				'target' => '_self',
				'icon'   => '<span class="dashicons dashicons-info"></span> ',
				'id'     => 'settings_page_mfb-settings',
			];

			if ( ! is_multisite() || is_main_site() ) {
				if ( $mfb_fs->is_registered() ) {
					$custom_links[] = [
						'url'    => $is_network_activate ? str_replace( 'options-general.php', 'admin.php', $mfb_fs->get_account_url() ) : $mfb_fs->get_account_url(),
						'title'  => __( 'Account', 'display-a-meta-field-as-block' ),
						'target' => '_self',
						'icon'   => '<span class="dashicons dashicons-admin-users"></span> ',
						'id'     => $is_network_activate ? 'admin_page_mfb-settings-account-network' : 'settings_page_mfb-settings-account',
					];
				}

				$custom_links[] = [
					'url'    => $is_network_activate ? str_replace( 'options-general.php', 'admin.php', $mfb_fs->contact_url() ) : $mfb_fs->contact_url(),
					'title'  => __( 'Contact', 'display-a-meta-field-as-block' ),
					'target' => '_self',
					'icon'   => '<span class="dashicons dashicons-feedback"></span> ',
					'id'     => $is_network_activate ? 'admin_page_mfb-settings-contact-network' : 'settings_page_mfb-settings-contact',
				];

				if ( $mfb_fs->is_not_paying() ) {
					$custom_links[] = [
						'url'    => $is_network_activate ? str_replace( 'options-general.php', 'admin.php', $mfb_fs->get_upgrade_url() ) : $mfb_fs->get_upgrade_url(),
						'title'  => $this->get_premium_label(),
						'target' => '_self',
						'icon'   => '<span class="dashicons dashicons-superhero-alt"></span> ',
						'id'     => $is_network_activate ? 'admin_page_mfb-settings-pricing-network' : 'settings_page_mfb-settings-pricing',
						'style'  => 'font-weight:bold;color:#d20962;',
					];
				}
			}

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add the premium link to the plugin admin screen.
		 *
		 * @param array $links
		 * @return array
		 */
		public function plugin_settings_links( $links ) {
			if ( mfb_fs()->is_not_paying() ) {
				$links[] = sprintf( '<a href="%1$s" target="_self" style="font-weight:bold;color:#d20962;">%2$s</a>', mfb_fs()->get_upgrade_url(), $this->get_premium_label() );
			}

			return $links;
		}

		/**
		 * Add data to the setting page.
		 *
		 * @return void
		 */
		public function enqueue_data_on_the_setting_page() {
			wp_add_inline_script( 'mfb-settings', 'var MFB=' . wp_json_encode( [ 'isPremium' => apply_filters( 'meta_field_block_is_premium', ! mfb_fs()->is_not_paying() ) ] ) . ';', 'before' );
		}

		/**
		 * Get the labels
		 *
		 * @return string
		 */
		private function get_premium_label() {
			return __( 'Upgrade', 'display-a-meta-field-as-block' );
		}
	}
endif;
