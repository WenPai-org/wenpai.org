<?php
/**
 * Helper functions
 *
 * @package     UsernameChanger\Functions
 * @since       2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get an array of user roles
 *
 * @since       3.0.0
 * @return      array $roles The available user roles
 */
function username_changer_get_user_roles() {
	global $wp_roles;

	$roles = $wp_roles->get_names();

	// Administrator can always edit.
	unset( $roles['administrator'] );

	return apply_filters( 'username_changer_user_roles', $roles );
}


/**
 * Check if a user can change a given username
 *
 * @since       3.0.0
 * @return      bool $allowed Whether or not this user can change their username
 */
function username_changer_can_change_own_username() {
	$allowed = false;

	if ( is_user_logged_in() ) {
		$allowed_roles = username_changer()->settings->get_option( 'allowed_roles', array() );
		$user_data     = wp_get_current_user();
		$user_roles    = $user_data->roles;

		if ( in_array( 'administrator', $user_roles, true ) ) {
			$allowed = true;
		} elseif ( is_array( $user_roles ) ) {
			foreach ( $user_roles as $user_role => $role_name ) {
				if ( in_array( $user_role, $allowed_roles, true ) ) {
					$allowed = true;
				}
			}
		}
	}

	return apply_filters( 'username_changer_can_change_own_username', $allowed );
}


/**
 * Process a username change
 *
 * @since       3.0.0
 * @param       string $old_username The old (current) username.
 * @param       string $new_username The new username.
 * @return      bool $return Whether or not we completed successfully
 */
function username_changer_process( $old_username, $new_username ) {
	global $wpdb;

	$return = false;

	// One last sanity check to ensure the user exists.
	$user_id = username_exists( $old_username );
	if ( $user_id ) {
		// Let devs hook into the process.
		do_action( 'username_changer_before_process', $old_username, $new_username );

		// Update username!
		$q = $wpdb->prepare( "UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $new_username, $old_username ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables

		if ( false !== $wpdb->query( $q ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
			// Update user_nicename.
			$qnn = $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE user_login = %s AND user_nicename = %s", $new_username, $new_username, $old_username ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables
			$wpdb->query( $qnn ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery

			// Update display_name.
			$qdn = $wpdb->prepare( "UPDATE $wpdb->users SET display_name = %s WHERE user_login = %s AND display_name = %s", $new_username, $new_username, $old_username ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables
			$wpdb->query( $qdn ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery

			// Update nickname.
			$nickname = get_user_meta( $user_id, 'nickname', true );
			if ( $nickname === $old_username ) {
				update_user_meta( $user_id, 'nickname', $new_username );
			}

			// If the user is a Super Admin, update their permissions.
			if ( is_multisite() && is_super_admin( $user_id ) ) {
				grant_super_admin( $user_id );
			}

			// Reassign Coauthor Attribution.
			if ( username_changer_plugin_installed( 'co-authors-plus/co-authors-plus.php' ) ) {
				global $coauthors_plus;

				$coauthor_posts = get_posts(
					array(
						'post_type'      => get_post_types(),
						'posts_per_page' => -1,
						'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
							array(
								'taxonomy' => $coauthors_plus->coauthor_taxonomy,
								'field'    => 'name',
								'terms'    => $old_username,
							),
						),
					)
				);

				$current_term = get_term_by( 'name', $old_username, $coauthors_plus->coauthor_taxonomy );
				wp_delete_term( $current_term->term_id, $coauthors_plus->coauthor_taxonomy );

				if ( ! empty( $coauthor_posts ) ) {
					foreach ( $coauthor_posts as $coauthor_post ) {
						$coauthors_plus->add_coauthors( $coauthor_post->ID, array( $new_username ), true );
					}
				}
			}

			$return = true;
		}

		// Let devs hook into the process.
		do_action( 'username_changer_after_process', $old_username, $new_username );

		return $return;
	}
}


/**
 * Check if a plugin is installed
 *
 * @since       1.0.0
 * @param       string $plugin The path to the plugin to check.
 * @return      boolean true if installed and active, false otherwise
 */
function username_changer_plugin_installed( $plugin = false ) {
	$ret = false;

	if ( $plugin ) {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		if ( in_array( $plugin, $active_plugins, true ) ) {
			$ret = true;
		}
	}

	return $ret;
}
