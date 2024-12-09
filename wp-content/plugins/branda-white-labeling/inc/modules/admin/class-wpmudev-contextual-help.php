<?php
/**
 * Branda Contextual Help class.
 *
 * @package Branda
 * @subpackage AdminArea
 */
/**
 * The new contextual help API helper.
 *
 * Example usage:
 * <code>
 * $help = new Branda_Contextual_Help();
 * $help->add_page(
 *        'dashboard',
 *        array(
 *            array(
 *                'id' => 'myid',
 *                'title' => __('My title', 'textdomain'),
 *                'content' => '<p>' . __('My content lalala', 'textdomain') . '</p>',
 *            ),
 *        ),
 *        '<p>My awesome sidebar!</p>',
 *        false // Don't clear existing items.
 *    );
 * $help->initialize();
 * </code>
 */
if ( ! class_exists( 'Branda_Contextual_Help' ) ) {

	/**
	 * Class Branda_Contextual_Help.
	 */
	class Branda_Contextual_Help {

		/**
		 * Pages for contextual help.
		 *
		 * @var array
		 */
		private $_pages = array();

		/**
		 * Should automatically clear help.
		 *
		 * @var bool
		 */
		private $_auto_clear_wp_help = false;

		/**
		 * Add an array of tabs to a certain page.
		 *
		 * @param string $screen_id Screen ID to which to bind the help.
		 *      Can be obtained by something like <code>$screen = get_current_screen(); $screen_id = @$screen->id;</code>.
		 * @param array  $tabs (optional) Array of tabs to add.
		 *      Each tab is an associative array, with these keys: id, title, content.
		 * @param string $sidebar (optional) HTML string to be displayed as contextual help sidebar.
		 * @param bool   $clear (optional) Clear the existing contextual help content for this page before the new tabs.
		 *
		 * @return bool|void
		 */
		public function add_page( $screen_id, $tabs = array(), $sidebar = '', $clear = false ) {
			if ( ! is_array( $tabs ) ) {
				return false;
			}
			$this->_pages[ $screen_id ] = array(
				'tabs'    => $tabs,
				'sidebar' => $sidebar,
				'clear'   => $clear,
			);
		}

		/**
		 * Removes a page from instance queue.
		 *
		 * @param string $screen_id Screen ID to clear.
		 */
		public function remove_page( $screen_id ) {
			$this->_pages[ $screen_id ] = array();
		}

		/**
		 * Adds a contextual tab to be displayed on a page.
		 *
		 * @param string $screen_id Screen ID to which to bind the tab.
		 * @param array  $tab An associative array representing a tab.
		 *
		 * @return bool|void
		 */
		public function add_tab( $screen_id, $tab = array() ) {
			if ( ! is_array( $tab ) ) {
				return false;
			}
			if ( ! isset( $this->_pages[ $screen_id ] ) ) {
				$this->_pages[ $screen_id ] = array(
					'tabs' => array(),
				);
			}
			// If in case tabs not set.
			if ( ! isset( $this->_pages[ $screen_id ]['tabs'] ) ) {
				$this->_pages[ $screen_id ]['tabs'] = array();
			}
			// Add new tab item.
			$this->_pages[ $screen_id ]['tabs'][] = $tab;
		}

		/**
		 * Removes a tab from instance queue.
		 *
		 * @param string $screen_id Screen ID to which to bind the tab.
		 * @param string $tab_id The value of "id" key of tab to be removed.
		 *
		 * @return bool|void
		 */
		public function remove_tab( $screen_id, $tab_id ) {
			if ( ! $tab_id ) {
				return false;
			}
			if ( ! isset( $this->_pages[ $screen_id ]['tabs'] ) ) {
				return false;
			}
			$tabs = $this->_pages[ $screen_id ]['tabs'];
			foreach ( $tabs as $tab ) {
				if ( isset( $tab['id'] ) && $tab['id'] === $tab_id ) {
					unset( $this->_pages[ $screen_id ]['tabs'][ $tab_id ] );
				}
			}
		}

		/**
		 * Set up automatic clearing of existing help
		 * prior to adding queued help content.
		 */
		public function clear_wp_help() {
			$this->_auto_clear_wp_help = true;
		}

		/**
		 * Sets up queued items as contextual help.
		 */
		public function initialize() {
			$this->add_hooks();
		}

		/**
		 * Add contextual help hooks.
		 *
		 * Use backward compatibility.
		 */
		private function add_hooks() {
			global $wp_version;
			$version = preg_replace( '/-.*$/', '', $wp_version );
			if ( version_compare( $version, '3.3', '>=' ) ) {
				add_action( 'admin_head', array( $this, 'add_contextual_help' ), 999 );
			} else {
				add_filter( 'contextual_help', array( $this, 'add_compatibility_contextual_help' ), 1, 1 );
			}
		}

		/**
		 * Add helper notice to admin.
		 *
		 * No need to be called manually.
		 *
		 * @return bool|void
		 */
		public function add_contextual_help() {
			$screen = get_current_screen();
			if ( ! is_object( $screen ) || empty( $screen->id ) ) {
				return false;
			}
			if ( ! isset( $this->_pages['_global_'] ) ) {
				if ( ! isset( $this->_pages[ $screen->id ] ) || ! isset( $this->_pages[ $screen->id ]['tabs'] ) ) {
					return false;
				}
				$info = $this->_pages[ $screen->id ];
			} else {
				$info = $this->_pages['_global_'];
			}
			if ( ! empty( $info['clear'] ) || $this->_auto_clear_wp_help ) {
				$screen->remove_help_tabs();
			}
			if ( isset( $info['sidebar'] ) ) {
				$screen->set_help_sidebar( $info['sidebar'] );
			}
			foreach ( $info['tabs'] as $tab ) {
				$screen->add_help_tab( $tab );
			}
		}

		/**
		 * Compatibility layer for pre-3.3 installs.
		 *
		 * @param string $help Help content.
		 *
		 * @return string $help
		 */
		public function add_compatibility_contextual_help( $help ) {
			if ( ! isset( $this->_pages['_global_'] ) ) {
				$screen = get_current_screen();
				if ( ! is_object( $screen ) || empty( $screen->id ) ) {
					return $help;
				}
				if ( ! isset( $this->_pages[ $screen->id ] ) || ! isset( $this->_pages[ $screen->id ]['tabs'] ) ) {
					return $help;
				}
				$info = $this->_pages[ $screen->id ];
			} else {
				$info = $this->_pages['_global_'];
			}
			if ( ! empty( $info['clear'] ) || $this->_auto_clear_wp_help ) {
				$help = '';
			}
			foreach ( $info['tabs'] as $tab ) {
				$help .= '<h3>' . $tab['title'] . '</h3>' . $tab['content'];
			}
			return $help;
		}
	}
}
