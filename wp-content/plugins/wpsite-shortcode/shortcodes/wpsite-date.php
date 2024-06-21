<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Year Shortcode: [wpsite_year format="" offset=""]
function wpsite_shortcode_year( $atts ) {
    $atts = shortcode_atts(
        array(
            'format' => 'Y',
            'offset' => '0',
        ), $atts, 'y' );
    $valid_formats = array( 'y', 'Y' );
    if ( in_array( $atts['format'], $valid_formats ) ) {
        return date_i18n( $atts['format'], strtotime( '+' . $atts['offset'] . ' years' ) );
    } else {
        return $atts['format'] . ' is not a valid year format!';
    }
}
add_shortcode( 'wpsite_y', 'wpsite_shortcode_year' );

// Month Shortcode: [wpsite_month format="" offset=""]
function wpsite_shortcode_month( $atts ) {
    $atts = shortcode_atts(
        array(
            'format' => 'F',
            'offset' => '0',
        ), $atts, 'm' );
    $valid_formats = array( 'F', 'm', 'M', 'n' );
    if ( in_array( $atts['format'], $valid_formats ) ) {
        return date_i18n( $atts['format'], strtotime( '+' . $atts['offset'] . ' months' ) );
    } else {
        return $atts['format'] . ' is not a valid month format!';
    }
}
add_shortcode( 'wpsite_m', 'wpsite_shortcode_month' );

// Day Shortcode: [wpsite_day format="" offset=""]
function wpsite_shortcode_day( $atts ) {
    $atts = shortcode_atts(
        array(
            'format' => 'd',
            'offset' => '0',
        ), $atts, 'd' );
    $valid_formats = array( 'd', 'D', 'j', 'N', 'S', 'w', 'z', 't' );
    if ( in_array( $atts['format'], $valid_formats ) ) {
        return date_i18n( $atts['format'], strtotime( '+' . $atts['offset'] . ' days' ) );
    } else {
        return $atts['format'] . ' is not a valid day format!';
    }
}
add_shortcode( 'wpsite_d', 'wpsite_shortcode_day' );
