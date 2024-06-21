<?php
/**
 * Plugin Name: WPfanyi import
 * Description: Install translation package like a theme/plugin, no need for FTP/SFTP. this tool will save you a lot of time.
 * Author: WPfanyi.com
 * Author URI:https://wpfanyi.com/
 * Text Domain: wpfanyi-import
 * Domain Path: /languages
 * Version: 1.1.3
 * Network: True
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * WPfanyi import is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPfanyi import is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    if (is_admin() && current_user_can('install_plugins')) {
        define('WPF_VERSION', '1.1.2');
        define('WPF_DIR_PATH', plugin_dir_path(__FILE__));
        define('WPF_DIR_URL', plugin_dir_url(__FILE__));
        define('WPF_BASE_NAME', plugin_basename(__FILE__));

        /** Load translation */
		add_action( 'init', 'wpfanyi_load_textdomain' );
		function wpfanyi_load_textdomain() {
		load_plugin_textdomain( 'wpfanyi-import', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

        /** Load core */
        require_once 'core.php';
        new WPfanyi_Import();
    }
});
