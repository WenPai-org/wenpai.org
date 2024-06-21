<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Search Widget Shortcode:[wpsite_search]
function wpsite_shortcode_search_form( $atts ) {
  $form = get_search_form( false );

  return $form;
}
add_shortcode( 'wpsite_search', 'wpsite_shortcode_search_form' );


// Calendar Widget Shortcode:[wpsite_calendar]
function wpsite_shortcode_calendar( $atts ) {
  $calendar = get_calendar( $atts );

  return $calendar;
}
add_shortcode( 'wpsite_calendar', 'wpsite_shortcode_calendar' );


// Tags Widget Shortcode:[wpsite_tags]
function wpsite_shortcode_tags( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
    'separator' => ', ',
  ), $atts );

  $tags = get_the_tag_list( $atts['before'], $atts['separator'], $atts['after'] );

  return $tags;
}
add_shortcode( 'wpsite_tags', 'wpsite_shortcode_tags' );


// Recent Posts Widget Shortcode:[wpsite_recentposts]
function wpsite_shortcode_recent_posts( $atts ) {
  $atts = shortcode_atts( array(
    'number' => '5',
  ), $atts );

  ob_start();
  the_widget( 'WP_Widget_Recent_Posts', $atts );
  $output = ob_get_clean();

  return $output;
}
add_shortcode( 'wpsite_recentposts', 'wpsite_shortcode_recent_posts' );



// Recent Comments Widget Shortcode:[wpsite_recentcomments]
function wpsite_shortcode_recent_comments( $atts ) {
  $atts = shortcode_atts( array(
    'number' => '5',
  ), $atts );

  ob_start();
  the_widget( 'WP_Widget_Recent_Comments', $atts );
  $output = ob_get_clean();

  return $output;
}
add_shortcode( 'wpsite_recentcomments', 'wpsite_shortcode_recent_comments' );
