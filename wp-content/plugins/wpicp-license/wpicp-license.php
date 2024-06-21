<?php
/**
 * Plugin Name: WPICP License
 * Plugin URI: https://wpicp.com/download
 * Description: Must-have for WordPress sites in China, showing your ICP license.
 * Author: WPICP.com
 * Author URI: https://wpicp.com/
 * Text Domain: wpicp-license
 * Domain Path: /languages
 * Version: 1.2
 * Network: True
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * WP ICP License is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP ICP License is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */



 require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcode.php' );


 // Add admin menu page
 add_action( 'admin_menu', 'wpicp_license_menu' );

 function wpicp_license_menu() {
     add_options_page(
          __( 'WP ICP License Settings', 'wpicp-license' ),
          __( 'ICP License', 'wpicp-license' ),
         'manage_options',
         'wpicp_license_settings',
         'wpicp_license_settings_page'
     );
 }

 /** Load translation */
 add_action( 'init', 'wpicp_load_textdomain' );
 function wpicp_load_textdomain() {
 	load_plugin_textdomain( 'wpicp-license', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 }

 // Add settings page and field
 add_action( 'admin_init', 'wpicp_license_settings' );

 function wpicp_license_settings() {

     add_settings_section(
         'wpicp_license_section',
         __( 'WordPress ICP License Namber', 'wpicp-license' ),
         'wpicp_license_section_callback',
         'wpicp_license_settings'
     );
     add_settings_section(
         'wpicp_wangan_section',
         __( 'China Wangan License Number', 'wpicp-license' ),
         'wpicp_wangan_section_callback',
         'wpicp_license_settings'
     );
     add_settings_field(
         'wpicp_license_field',
         __( 'ICP License', 'wpicp-license' ),
         'wpicp_license_field_callback',
         'wpicp_license_settings',
         'wpicp_license_section'
     );

     add_settings_field(
         'wpicp_wangan_field',
         __( 'Wangan License', 'wpicp-license' ),
         'wpicp_wangan_field_callback',
         'wpicp_license_settings',
         'wpicp_wangan_section'
     );
     add_settings_field(
         'wpicp_province_field',
         __( 'Province', 'wpicp-license' ),
         'wpicp_license_settings',
         'wpicp_license_section'
     );


     register_setting( 'wpicp_license_settings', 'wpicp_province' );
     register_setting( 'wpicp_license_settings', 'wpicp_wangan' );
     register_setting( 'wpicp_license_settings', 'wpicp_license' );
 }


 function show_wpicp_license_field(){
     $wpicp_license = get_option('wpicp_license');
     echo '<input type="text" id="wpicp_license" name="wpicp_license" value="'.esc_attr($wpicp_license).'">';
     echo '<p class="description">'.__('Enter your ICP license number information.', 'wpicp-license').'</p>';
 }
 add_filter('admin_init', 'add_wpicp_license_setting');
 function add_wpicp_license_setting(){
     add_settings_field(
         'wpicp_license_field',
         __('ICP License', 'wpicp-license'),
         'show_wpicp_license_field',
         'general'
     );
     register_setting('general', 'wpicp_license');
 }


 // Settings section callback
 function wpicp_license_section_callback() {
     echo '<p>' . __( 'This plugin is free forever, and its purpose is to supplement the essential functions that the Chinese version of WordPress lacks. More information at <a href="https://wpicp.com" target="_blank" rel="noopener">WPICP.com</a>', 'wpicp-license' ) . '</p>';
     echo '<h3>' . __( 'Why do you need?', 'wpicp-license' ) . '</h3>';
     echo '<p>' . __( 'The ICP license is a state-issued registration, All public websites in mainland China must have an ICP number listed on the homepage of the website. <a href="https://wpicp.com/document/what-would-happen-if-not" target="_blank" rel="noopener">(What would happen if not?)</a>', 'wpicp-license' ) . '</p>';
     echo '<h3>' . __( 'How to use?', 'wpicp-license' ) . '</h3>';
     echo '<p>' . __( '1. Enter your ICP license information below. <a href="https://wpicp.com/document/find-my-license" target="_blank" rel="noopener">(Find My License?)</a>', 'wpicp-license' ) . '</p>';
     echo '<p>' . __( '2. Use the shortcode <code>[wpicp_license]</code> to display the license information and link on your website. <a href="https://wpicp.com/document/integrate-into-theme" target="_blank" rel="noopener">(Integrate into theme?)</a>', 'wpicp-license' ) . '</p>';
 }

// Settings field callback
function wpicp_license_field_callback() {
    $wpicp_license = get_option( 'wpicp_license' );
    echo '<input type="text" id="wpicp_license" name="wpicp_license" value="' . esc_attr( $wpicp_license ) . '"/>';
    echo '<p class="description" style="font-size:13px;">' . __( 'Enter your ICP license number information. <a href="https://wpicp.com/document/correct-format" target="_blank" rel="noopener">(Correct format?)</a>', 'wpicp-license' ) . '</p>';
}
// Settings section callback
function wpicp_wangan_section_callback() {
  echo '<p>' . __( 'Use the shortcode <code>[wpicp_wangan]</code>, You need to register with the Public Security Bureau (PSB) to have this license.  <a href="https://wpicp.com/document/what-is-psb-filing" target="_blank" rel="noopener">(What is PSB filing?)</a>', 'wpicp-license' ) . '</p>';
}

// Add Wangan license field
function wpicp_wangan_field_callback() {
    $wpicp_wangan = get_option( 'wpicp_wangan' );
    $wpicp_province = get_option( 'wpicp_province' );
?>
<input type="number" id="wpicp_wangan" name="wpicp_wangan" min="0" value="<?php echo esc_attr( $wpicp_wangan ); ?>"/>

<select id="wpicp_province" name="wpicp_province">
    <?php
    // Array of province abbreviations
    $provinces = array(
    '京' => __('Beijing', 'wpicp-license'),
    '津' => __('Tianjin', 'wpicp-license'),
    '冀' => __('Hebei', 'wpicp-license'),
    '晋' => __('Shanxi', 'wpicp-license'),
    '蒙' => __('Inner Mongolia', 'wpicp-license'),
    '辽' => __('Liaoning', 'wpicp-license'),
    '吉' => __('Jilin', 'wpicp-license'),
    '黑' => __('Heilongjiang', 'wpicp-license'),
    '沪' => __('Shanghai', 'wpicp-license'),
    '苏' => __('Jiangsu', 'wpicp-license'),
    '浙' => __('Zhejiang', 'wpicp-license'),
    '皖' => __('Anhui', 'wpicp-license'),
    '闽' => __('Fujian', 'wpicp-license'),
    '赣' => __('Jiangxi', 'wpicp-license'),
    '鲁' => __('Shandong', 'wpicp-license'),
    '豫' => __('Henan', 'wpicp-license'),
    '鄂' => __('Hubei', 'wpicp-license'),
    '湘' => __('Hunan', 'wpicp-license'),
    '粤' => __('Guangdong', 'wpicp-license'),
    '桂' => __('Guangxi', 'wpicp-license'),
    '琼' => __('Hainan', 'wpicp-license'),
    '渝' => __('Chongqing', 'wpicp-license'),
    '川' => __('Sichuan', 'wpicp-license'),
    '黔' => __('Guizhou', 'wpicp-license'),
    '滇' => __('Yunnan', 'wpicp-license'),
    '藏' => __('Tibet', 'wpicp-license'),
    '陕' => __('Shaanxi', 'wpicp-license'),
    '甘' => __('Gansu', 'wpicp-license'),
    '青' => __('Qinghai', 'wpicp-license'),
    '宁' => __('Ningxia', 'wpicp-license'),
    '新' => __('Xinjiang', 'wpicp-license'),
//    '港' => __('Hong Kong', 'wpicp-license'),
//    '澳' => __('Macao', 'wpicp-license')
//    '台' => __('Taiwan', 'wpicp-license')

    // ... add more provinces here
    );

    // Loop through the array to generate the options
    foreach ( $provinces as $abbr => $name ) {
        echo '<option value="' . esc_attr( $abbr ) . '"' . selected( $wpicp_province, $abbr, false ) . '>' . esc_html( $name ) . '</option>';
    }
    ?>
</select>
<p class="description" style="font-size:13px;"><?php _e( 'Enter your Wangan license number and select the abbreviation of your province.', 'wpicp-license' ); ?></p>

<?php
}


// Settings page callback
function wpicp_license_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'ICP License Settings', 'wpicp-license' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'wpicp_license_settings' ); ?>
            <?php do_settings_sections( 'wpicp_license_settings' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}



?>
