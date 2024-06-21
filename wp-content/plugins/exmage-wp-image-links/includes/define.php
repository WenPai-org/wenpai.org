<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'EXMAGE_WP_IMAGE_LINKS_ADMIN', EXMAGE_WP_IMAGE_LINKS_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'EXMAGE_WP_IMAGE_LINKS_LANGUAGES', EXMAGE_WP_IMAGE_LINKS_DIR . "languages" . DIRECTORY_SEPARATOR );
$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'EXMAGE_WP_IMAGE_LINKS_ASSETS', $plugin_url . "/assets/" );
define( 'EXMAGE_WP_IMAGE_LINKS_ASSETS_DIR', EXMAGE_WP_IMAGE_LINKS_DIR . "assets" . DIRECTORY_SEPARATOR );
define( 'EXMAGE_WP_IMAGE_LINKS_CSS', EXMAGE_WP_IMAGE_LINKS_ASSETS . "css/" );
define( 'EXMAGE_WP_IMAGE_LINKS_CSS_DIR', EXMAGE_WP_IMAGE_LINKS_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'EXMAGE_WP_IMAGE_LINKS_JS', EXMAGE_WP_IMAGE_LINKS_ASSETS . "js/" );
define( 'EXMAGE_WP_IMAGE_LINKS_JS_DIR', EXMAGE_WP_IMAGE_LINKS_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'EXMAGE_WP_IMAGE_LINKS_IMAGES', EXMAGE_WP_IMAGE_LINKS_ASSETS . "images/" );
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "functions.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "functions.php";
}
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "support.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "support.php";
}
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "wp-async-request.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "wp-async-request.php";
}
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "wp-background-process.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "wp-background-process.php";
}
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "exmage-background-process.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "exmage-background-process.php";
}
if ( is_file( EXMAGE_WP_IMAGE_LINKS_INCLUDES . "background-process-images.php" ) ) {
	require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "background-process-images.php";
}