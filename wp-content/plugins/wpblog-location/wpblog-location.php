<?php
/*
* Plugin Name: WPblog Location
* Plugin URI: https://wpblog.cn/download
* Description: Display user account IP address attribution information in comments and articles.
* Author: WPfanyi
* Author URI: https://wpfanyi.com
* Text Domain: wpblog-location
* Domain Path: /languages
* Version: 1.0
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*
* WP blog Location is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* any later version.
*
* WP blog Location is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// All WP Blog Constant
require plugin_dir_path( __FILE__ ) . 'includes/const/WpBlogConst.php';

// Load required files
require_once plugin_dir_path( __FILE__ ) . 'includes/Reader.php';
require_once plugin_dir_path( __FILE__ ) . 'templates/WpBlogTemplate.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/WpblogPostIpCheckerService.php';

// Enqueue plugin CSS and dashicons CSS
add_action( 'wp_enqueue_scripts', 'wpblog_post_enqueue_css' );
function wpblog_post_enqueue_css() {
    wp_enqueue_style( 'wpblog_post_css', plugin_dir_url( __FILE__ ) . 'assets/css/location.css' );
    wp_enqueue_style( 'dashicons' );
}

// Enqueue plugin CSS and dashicons JS
add_action( 'admin_menu', 'wpblog_post_enqueue_js' );
function wpblog_post_enqueue_js() {
    wp_register_script('custom-script', plugin_dir_url( __FILE__ ) . 'assets/js/wpblog-location.js');
    wp_enqueue_script('custom-script');
}


// Load translation
add_action( 'init', 'wpblog_load_textdomain' );
function wpblog_load_textdomain() {
	load_plugin_textdomain( 'wpblog-location', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}


// Add settings page to WordPress admin menu
add_action( 'admin_menu', 'wpblog_post_add_settings_page' );
function wpblog_post_add_settings_page() {
    add_options_page(
        __( 'IP Location Settings', 'wpblog-location' ), // Page title
        __( 'IP Location', 'wpblog-location' ), // Menu name
        'manage_options', // User capability
        'wpblog-location', // Page ID
        'wpblog_post_settings_page' // Callback function
    );
    // Add new settings field to control author location display
    add_settings_field(
        'wpblog_post_show_author_location', // Field ID
        __( 'Show author location on post pages', 'wpblog-location' ), // Field title
        'wpblog_post_show_author_location_callback', // Callback function
        'wpblog_post_settings', // Settings page ID
        'wpblog_post_section' // Settings page section ID
    );
}

// Callback function to display the "Show author location" settings field
function wpblog_post_show_author_location_callback() {
    $show_author_location = get_option( 'wpblog_post_show_author_location', false );
    echo '<input type="checkbox" name="wpblog_post_show_author_location" value="1" ' . checked( 1, $show_author_location, false ) . ' />';
}


// Callback function to display the settings page HTML
function wpblog_post_settings_page() {
    // Check user permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wpblog-location' ) );
    }

    $nonce = 'wpblog-update-key';

    $allowIpCheckerArr = [
        'local',
        'ipapi',
    ];
    $addressFormatArr = [
        'city',
        'country, region, city',
        'city, region, country',
    ];

    // Handle form submission
    if ( isset( $_POST['wpblog_post_save_settings'] ) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], $nonce) ) {
        $show_post_location = isset($_POST['show_post_location']) ? true : false;
        $show_comment_location = isset($_POST['show_comment_location']) ? true : false;
        $show_author_location = isset($_POST['wpblog_post_show_author_location']) ? true : false;
        $post_location_ip_checker = $_POST['ip_channel'] ?? WpBlogConst::WPBLOG_POST_DEFAULT_IP_CHECKER;

        if ( ! empty( $_POST['ip_address_format'] ) && isset( $_POST['ip_address_format_custom'] )
            && '\c\u\s\t\o\m' === wp_unslash( $_POST['ip_address_format'] )
        ) {
            $_POST['ip_address_format'] = $_POST['ip_address_format_custom'];
        }
        $post_location_ip_address_format = $_POST['ip_address_format']?? WpBlogConst::WPBLOG_POST_DEFAULT_IP_ADDRESS_FORMAT;

        if ( ! empty( $_POST['ip_address_custom_for_admin'] ) && isset( $_POST['ip_address_custom_for_admin_cu'] )
            && '\c\u\s\t\o\m' === wp_unslash( $_POST['ip_address_custom_for_admin'] )
        ) {
            $_POST['ip_address_custom_for_admin'] = $_POST['ip_address_custom_for_admin_cu'];
        }
        $ip_address_custom_for_admin = $_POST['ip_address_custom_for_admin'] ?? WpBlogConst::WPBLOG_POST_DEFAULT_FALSE;

        update_option('wpblog_post_show_author_location', $show_author_location);
        update_option('wpblog_post_show_post_location', $show_post_location);
        update_option('wpblog_post_show_comment_location', $show_comment_location);
        update_option('wpblog_post_ip_checker', $post_location_ip_checker);
        update_option('wpblog_post_ip_address_format', $post_location_ip_address_format);
        update_option('wpblog_post_ip_address_custom_for_admin', $ip_address_custom_for_admin);

        // Display success message
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>' . esc_html__( 'Settings saved.', 'wpblog-location' ) . '</strong></p></div>';
    }

    // Get current options
    $show_post_location = get_option( 'wpblog_post_show_post_location', false );
    $show_comment_location = get_option( 'wpblog_post_show_comment_location', true );
    $post_location_ip_checker = get_option( 'wpblog_post_ip_checker', WpBlogConst::WPBLOG_POST_DEFAULT_IP_CHECKER );
    $post_location_ip_address_format = get_option(
        'wpblog_post_ip_address_format',
        WpBlogConst::WPBLOG_POST_DEFAULT_IP_ADDRESS_FORMAT
    );
    $ip_address_custom_for_admin = get_option(
        'wpblog_post_ip_address_custom_for_admin',
        WpBlogConst::WPBLOG_POST_DEFAULT_FALSE
    );

    $nonceStr = wp_create_nonce( $nonce );

    // Render HTML
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <h2><?php esc_html_e( 'WordPress Blog User IP address attribution', 'wpblog-location' ); ?></h2>
    <p><?php esc_html_e( '1. Display WordPress user IP address attribution and city location information, More information at', 'wpblog-location' ); ?> <a href="https://wpblog.cn" target="_blank" rel="noopener">WPblog.cn</a></p>
    <p><?php esc_html_e( '2. You can display the author or publisher location anywhere on your website. The shortcode is', 'wpblog-location' ); ?> <code>[wpblog_post_location]</code> <code>[wpblog_author_location]</code> </p>
    <form method="post" action="">
        <input type="hidden" name="_wpnonce" value="<?php echo $nonceStr; ?>"/>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Post/pages', 'wpblog-location' ); ?></th>
                    <td><label><input type="checkbox" name="show_post_location" value="1" <?php checked( $show_post_location, true ); ?>> <?php esc_html_e( 'Show location', 'wpblog-location' ); ?></label></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Comments', 'wpblog-location' ); ?></th>
                    <td><label><input type="checkbox" name="show_comment_location" value="1" <?php checked( $show_comment_location, true ); ?>> <?php esc_html_e( 'Show location', 'wpblog-location' ); ?></label></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'IPChannel', 'wpblog-location' ); ?></th>
                    <td>
                    <?php foreach ($allowIpCheckerArr as $checker) : ?>
                        <label style="margin-right: 20px;">
                            <input type="radio" name="ip_channel" value="<?php echo esc_attr($checker); ?>" <?php checked($post_location_ip_checker, $checker); ?>>
                            <?php esc_html_e('IPChannel-' . $checker, 'wpblog-location'); ?>
                        </label>
                    <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'IPAddressFormat', 'wpblog-location' ); ?></th>
                    <td>
                        <fieldset>
                            <?php

                            $custom = true;

                            foreach ( $addressFormatArr as $format ) {
                                echo "\t<label class='options-general-php'><input type='radio' name='ip_address_format' value='" .
                                    esc_attr($format ) . "'";
                                if ( $post_location_ip_address_format === $format ) {
                                    echo " checked='checked'";
                                    $custom = false;
                                }
                                echo ' /> <span class="date-time-text format-i18n">';
                                esc_html_e( $format, 'wpblog-location' );
                                echo '</span><code>' . $format . "</code></label><br />\n";
                            }

                            echo '<label><input type="radio" name="ip_address_format" id="ip_address_format_custom_radio" value="\c\u\s\t\o\m"';
                            checked( $custom );
                            echo '/> </label>' .
                                '<input style="width: 140px" type="text" name="ip_address_format_custom" id="ip_address_format_custom" value="' .
                                esc_attr( $post_location_ip_address_format ) .
                                '" class="small-text" />' .
                                '<br />';
                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'IPAddressCustomForAdmin', 'wpblog-location' ); ?></th>
                    <td>
                        <fieldset>
                            <?php
                            $custom_ip_address_admin = true;
                            echo "\t<label class='options-general-php'><input type='radio' name='ip_address_custom_for_admin' value='";
                            echo WpBlogConst::WPBLOG_POST_DEFAULT_FALSE."'";
                            if ( $ip_address_custom_for_admin == WpBlogConst::WPBLOG_POST_DEFAULT_FALSE ) {
                                echo " checked='checked'";
                                $custom_ip_address_admin = false;
                            }
                            echo ' /> <span class="date-time-text format-i18n">';
                            esc_html_e( WpBlogConst::WPBLOG_POST_DEFAULT_FALSE, 'wpblog-location' );
                            echo "</span></label><br />\n";

                            echo '<label><input type="radio" name="ip_address_custom_for_admin" id="ip_address_custom_for_admin_radio" value="\c\u\s\t\o\m"';
                            checked( $custom_ip_address_admin );
                            echo '/> </label>' .
                                '<input style="width: 140px" type="text" name="ip_address_custom_for_admin_cu" id="ip_address_custom_for_admin_cu" value="' .
                                esc_attr( $ip_address_custom_for_admin == WpBlogConst::WPBLOG_POST_DEFAULT_FALSE ? ''
                                    : $ip_address_custom_for_admin ) .
                                '" class="small-text" />';
                            $ipAddressItemStr = esc_html__('Tips');
                            echo "<code>";
                            esc_html_e("ip_address_custom_for_admin_tips", 'wpblog-location');
                            echo "</code><br />";
                            ?>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button( __( 'Save Changes', 'wpblog-location' ), 'primary', 'wpblog_post_save_settings' ); ?>
    </form>
</div>
<?php
}
