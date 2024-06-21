<?php

/**
 * Freemius functions
 *
 * @package    MetaFieldBlock
 * @author     Phi Phan <mrphipv@gmail.com>
 * @copyright  Copyright (c) 2023, Phi Phan
 */
namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
if ( !function_exists( 'mfb_fs' ) ) {
    // Create a helper function for easy SDK access.
    function mfb_fs() {
        global $mfb_fs;
        if ( !isset( $mfb_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_14507_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_14507_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $first_path = 'options-general.php?page=mfb-settings&tab=getting-started';
            $parent_slug = 'options-general.php';
            if ( fs_is_network_admin() ) {
                $first_path = 'plugins.php';
                $parent_slug = 'admin.php';
            }
            $menu = array(
                'slug'       => 'mfb-settings',
                'first-path' => $first_path,
                'account'    => true,
                'pricing'    => false,
                'contact'    => false,
                'support'    => false,
                'parent'     => array(
                    'slug' => $parent_slug,
                ),
            );
            $mfb_fs = fs_dynamic_init( array(
                'id'             => '14507',
                'slug'           => 'display-a-meta-field-as-block',
                'type'           => 'plugin',
                'public_key'     => 'pk_b9783b047cc4acfa426420f8ed37d',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'navigation'     => 'tabs',
                'menu'           => $menu,
                'is_live'        => true,
            ) );
        }
        return $mfb_fs;
    }

    // Init Freemius.
    mfb_fs();
    // Disable some Freemius features.
    mfb_fs()->add_filter( 'show_deactivation_feedback_form', '__return_false' );
    mfb_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );
    // Disable opt-in option by default.
    mfb_fs()->add_filter( 'permission_diagnostic_default', '__return_false' );
    mfb_fs()->add_filter( 'permission_extensions_default', '__return_false' );
    // Hide annoying notices.
    mfb_fs()->add_filter(
        'show_admin_notice',
        function ( $show, $message ) {
            if ( in_array( $message['id'], array('license_activated', 'premium_activated', 'connect_account') ) ) {
                return false;
            }
            return $show;
        },
        10,
        2
    );
    // Signal that SDK was initiated.
    do_action( 'mfb_fs_loaded' );
}
if ( !function_exists( 'mfb_fs_custom_connect_message_on_update' ) ) {
    /**
     * Customize the opt-in messages
     *
     * @param string $message
     * @param string $user_first_name
     * @param string $plugin_title
     * @param string $user_login
     * @param string $site_link
     * @param string $freemius_link
     * @return string
     */
    function mfb_fs_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    ) {
        return sprintf(
            __( 'Hey %1$s' ) . ',<br>' . __( 'Thank you for using %2$s. We invite you to help the %2$s community by opting in to share some data about your usage of %2$s with us. This will help us make this plugin more compatible with your site and better at doing what you need it to. You can opt out at any time. And if you skip this, that\'s okay! %2$s will still work just fine.', 'display-a-meta-field-as-block' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }

    mfb_fs()->add_filter(
        'connect_message_on_update',
        __NAMESPACE__ . '\\mfb_fs_custom_connect_message_on_update',
        10,
        6
    );
}