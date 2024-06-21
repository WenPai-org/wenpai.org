<?php
/**
 * Plugin Name:       Simple Tabs Block
 * Description:       Create rich tabbed layouts to organize information in a simple way using Simple Tabs Block
 * Requires at least: 6.4
 * Requires PHP:      7.0
 * Version:           2.1.0
 * Author:            CloudCatch LLC
 * Author URI:        https://cloudcatch.io
 * Contributors:      cloudcatch, dkjensen
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-tabs-block
 *
 * @package           CloudCatch\SimpleTabsBlock
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_wp_tabs_block_block_init() {
	register_block_type( __DIR__ . '/build/tabs' );
	register_block_type( __DIR__ . '/build/tab' );
}
add_action( 'init', 'create_block_wp_tabs_block_block_init' );

/**
 * Prevent auto updating to version 2.0.0 due to breaking changes
 *
 * @param boolean $should_update Whether to update.
 * @param object  $plugin The update offer.
 * @return boolean
 */
function create_block_wp_tabs_block_auto_update( $should_update, $plugin ) {
	if ( ! isset( $plugin->plugin, $plugin->new_version ) ) {
		return $should_update;
	}

	if ( 'simple-tabs-block/simple-tabs-block.php' !== $plugin->plugin ) {
		return $should_update;
	}

	if ( ! isset( $plugin->Version ) || version_compare( $plugin->Version, '2.0.0', '<' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return false;
	}

	return $should_update;
}
add_filter( 'auto_update_plugin', 'create_block_wp_tabs_block_auto_update', 99, 2 );
