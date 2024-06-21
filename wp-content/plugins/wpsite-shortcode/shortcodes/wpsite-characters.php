<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Copyright Shortcode: [wpsite_c]
function wpsite_shortcode_copy() {
    return '©';
}
add_shortcode( 'wpsite_c', 'wpsite_shortcode_copy' );

// Copyright Long Shortcode: [wpsite_cr]
function wpsite_shortcode_copylong() {
    return 'Copyright';
}
add_shortcode( 'wpsite_cr', 'wpsite_shortcode_copylong' );

// Registered Trademark Shortcode: [wpsite_r]
function wpsite_shortcode_registered_trademark() {
    return '®';
}
add_shortcode( 'wpsite_r', 'wpsite_shortcode_registered_trademark' );

// Trademark Shortcode: [wpsite_tm]
function wpsite_shortcode_trademark() {
    return '™';
}
add_shortcode( 'wpsite_tm', 'wpsite_shortcode_trademark' );

// Service Mark Trademark Shortcode: [wpsite_sm]
function wpsite_shortcode_servicemark() {
    return '℠';
}
add_shortcode( 'wpsite_sm', 'wpsite_shortcode_servicemark' );
