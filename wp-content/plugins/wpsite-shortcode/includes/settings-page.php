<?php


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}



// Render the plugin settings page.
function wpsite_shortcode_render_settings_page() {
  $total_shortcodes = 0;
  ob_start();
  ?>

  <div class="wrap">
    <h1><?php _e( 'WPSite Shortcode Settings', 'wpsite-shortcode' ); ?></h1>
    <p><?php _e( 'The WPSite.cn Shortcode plugin provides a set of pre-built shortcodes that can be used by WordPress website owners and developers without having to write custom code.', 'wpsite-shortcode' ); ?></p>
    <p><?php _e( 'By using the dynamic data of WordPress itself, the content of the website can be displayed more flexibly.It can also usually be used as a wildcard for SEO.', 'wpsite-shortcode' ); ?></p>


    <div class="wpsite-shortcode-info">
      <h2><?php _e( 'Plugin Information', 'wpsite-shortcode' ); ?></h2>
      <ul>
        <?php
        $plugin_data = get_plugin_data( __FILE__ );
        ?>
        <li><?php printf( __( 'Total shortcodes: %d', 'wpsite-shortcode' ), wpsite_shortcode_count() ); ?></li>
        <li><?php printf( __( 'Support: <a href="%s" target="_blank">View Document</a>', 'wpsite-shortcode' ), 'https://wpsite.cn/document' ); ?></li>
      </ul>
    </div>
    <div id="wordpress-shortcodes" class="shortcode-wrapper">

    <?php // Load WordPress shortcodes ?>
    <h2><?php _e( 'WordPress Metadata', 'wpsite-shortcode' ); ?></h2>

      <?php include plugin_dir_path( __FILE__ ) . 'wordpress-shortcodes.php'; ?>

  </div>
    <?php } ?>
