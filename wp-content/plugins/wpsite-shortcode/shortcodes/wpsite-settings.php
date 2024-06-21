<?php
/*
Includes shortocde
Since: 1.0
Author: WPSite.cn
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


//Site Url Shortocde:[wpsite_url]
function wpsite_shortcode_siteurl() {
  $site_url = get_site_url();
  $site_url = str_replace( 'http://', '', $site_url );
  $site_url = str_replace( 'https://', '', $site_url );
  return $site_url;
}
add_shortcode( 'wpsite_url', 'wpsite_shortcode_siteurl' );


//Site Home Shortocde:[wpsite_home]
function wpsite_shortcode_home() {
  $home_url = get_home_url();
  $home_url = str_replace( 'http://', '', $home_url );
  $home_url = str_replace( 'https://', '', $home_url );
  return $home_url;
}
add_shortcode( 'wpsite_home', 'wpsite_shortcode_home' );


//Site Title Shortocde:[wpsite_title]
function wpsite_shortcode_site_title() {
  $title = get_bloginfo( 'name' );
  return $title;
}
add_shortcode( 'wpsite_title', 'wpsite_shortcode_site_title' );


//Tagline Shortocde:[wpsite_tagline]
function wpsite_shortcode_tagline() {
  $tagline = get_bloginfo( 'description' );
  return $tagline;
}
add_shortcode( 'wpsite_tagline', 'wpsite_shortcode_tagline' );


//Administration Email Address Shortocde:[wpsite_email]
function wpsite_shortcode_admin_email() {
  $admin_email = get_option( 'admin_email' );
  return $admin_email;
}
add_shortcode( 'wpsite_email', 'wpsite_shortcode_admin_email' );


//Date Format Shortocde:[wpsite_date]
function wpsite_shortcode_date() {
  $current_date = date_i18n( get_option( 'date_format' ) );
  return $current_date;
}
add_shortcode( 'wpsite_date', 'wpsite_shortcode_date' );


//Time Format Shortocde:[wpsite_time]
function wpsite_shortcode_time() {
  $current_time = date_i18n( get_option( 'time_format' ) );
  return $current_time;
}
add_shortcode( 'wpsite_time', 'wpsite_shortcode_time' );
