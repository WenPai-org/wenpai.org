<?php
/*
Class Name: Branda_Loader
Description: UB Modules table.
Version: 1.0.0
Author: WPMU Dev
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2018 Incsub (http://incsub.com)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! class_exists( 'Branda_Loader' ) ) {
	class Branda_Loader {
		protected $configuration = array();
		protected $build         = '';

		/**
		 * Submenu list
		 *
		 * @since 3.0.0
		 */
		protected $submenu;

		/**
		 * Is Network
		 *
		 * @since 3.0.0
		 */
		protected $is_network = false;

		/**
		 * Is Network Admin
		 *
		 * @since 3.2.0
		 */
		protected $is_network_admin = false;

		/**
		 * Is Main Blog
		 *
		 * @since 3.0.0
		 */
		protected $is_main_site = false;

		public function __construct() {
			branda_set_ub_version();
			global $ub_version, $branda_network;
			$this->build      = $ub_version;
			$this->is_network = $branda_network;
			if ( $this->is_network ) {
				$this->is_main_site = is_main_site();
				if ( is_admin() ) {
					$this->is_network_admin = is_network_admin();
				}
			}
			$this->set_configuration();
		}

		/**
		 * Should moduel be off?
		 *
		 * @since 1.9.8
		 */
		protected function should_be_module_off( $module ) {
			global $wp_version;
			/**
			 * get module key
			 */
			$key = $module;
			if ( is_array( $key ) ) {
				$key = $key['key'];
			}
			/**
			 * is module disabled by configuration?
			 *
			 * @since 2.3.0
			 */

			if (
				isset( $this->configuration[ $key ] )
				&& isset( $this->configuration[ $key ]['disabled'] )
				&& $this->configuration[ $key ]['disabled']
			) {
				return true;
			}
			/**
			 * is module allowed only for multisite?
			 */
			$is_avaialble = $this->can_load_module( $module );
			if ( ! $is_avaialble ) {
				return true;
			}
			/**
			 * check WP version
			 */
			if (
				isset( $this->configuration[ $key ] )
				&& isset( $this->configuration[ $key ]['wp'] )
			) {
				$compare = version_compare( $wp_version, $this->configuration[ $key ]['wp'] );
				if ( 0 > $compare ) {
					return true;
				}
			}
			/**
			 * avoid to compare with development version
			 * for deprecated check
			 */
			if ( preg_match( '/^PLUGIN_VER/', $this->build ) ) {
				return false;
			}
			if (
				isset( $this->configuration[ $key ] )
				&& isset( $this->configuration[ $key ]['deprecated'] )
				&& $this->configuration[ $key ]['deprecated']
				&& isset( $this->configuration[ $key ]['deprecated_version'] )
			) {
				$compare = version_compare( $this->configuration[ $key ]['deprecated_version'], $this->build );
				if ( 1 > $compare ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Set configuration
		 *
		 * @since 1.8.7
		 */
		public function set_configuration() {
			$modules             = branda_get_modules_list();
			$this->configuration = apply_filters( 'ultimatebranding_available_modules', $modules );
			/**
			 * add key to data
			 */
			foreach ( $this->configuration as $key => $data ) {
				$data['key'] = $this->configuration[ $key ]['key'] = $key;
				/**
				 * check is module deprecated
				 */
				if ( $this->should_be_module_off( $data ) ) {
					unset( $this->configuration[ $key ] );
					continue;
				}
				/**
				 * turn off module for dependiences
				 */
				if (
					isset( $this->configuration[ $key ] )
					&& isset( $this->configuration[ $key ]['replaced_by'] )
				) {
					$is_active = branda_is_active_module( $key );
					if ( $is_active ) {
						$replace_by = $this->configuration[ $key ]['replaced_by'];
						$is_active  = branda_is_active_module( $replace_by );
						if ( $is_active ) {
							$deactivate = $this->deactivate_module( $key );
							if ( $deactivate ) {
								$message = array(
									'type'    => 'info',
									'message' => sprintf(
										__( 'Module "<b>%1$s</b>" was turned off because module "<b>%2$s</b>" is active.', 'ub' ),
										$data['title'],
										$this->configuration[ $replace_by ]['title']
									),
								);
								$this->add_message( $message );
							}
							unset( $this->configuration[ $key ] );
							continue;
						}
					}
				}
				/**
				 * fix menu_title
				 */
				if ( isset( $data['page_title'] ) && ! isset( $data['menu_title'] ) ) {
					$this->configuration[ $key ]['menu_title'] = $data['page_title'];
				}
			}
			/**
			 * check modules to off
			 */
			$this->configuration = apply_filters( 'ultimatebranding_available_modules', $this->configuration );
			/**
			 * sort
			 */
			// uasort( $this->configuration, array( $this, 'sort_configuration' ) );
			/**
			 * Submenu items
			 */
			$this->submenu = branda_get_groups_list();
		}

		/**
		 * Sort helper for tab menu.
		 *
		 * @since 1.8.8
		 */
		private function sort_configuration( $a, $b ) {
			if ( isset( $a['menu_title'] ) && isset( $b['menu_title'] ) ) {
				return strcasecmp( $a['menu_title'], $b['menu_title'] );
			}
			return 0;
		}

		/**
		 * Add query_vars
		 *
		 * @since 3.0.0
		 */
		public function add_query_vars_filter( $vars ) {
			$vars[] = 'branda';
			return $vars;
		}
	}
}
