<?php
/**
 * Plugin Name: GlotPress Extended API Plugins
 * Description: Expands the GP API by adding extended Translation endpoints
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\GP_Extended_API;

defined( 'ABSPATH' ) || exit;

require_once( plugin_dir_path( __FILE__ ) . 'gp-translation-extended-api/gp-translation-extended-api.php' );
