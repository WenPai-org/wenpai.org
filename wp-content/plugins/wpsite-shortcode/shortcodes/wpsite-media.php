<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


//Show image Shortcode:[wpsite_images]
function wpsite_shortcode_images( $atts ) {
  $atts = shortcode_atts( array(
    'orderby' => 'date',
    'order' => 'DESC',
    'ids' => '',
    'columns' => 3,
  ), $atts );

  $images = get_posts( array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'orderby' => $atts['orderby'],
    'order' => $atts['order'],
    'include' => $atts['ids'],
    'numberposts' => -1,
  ) );

  if ( ! $images ) {
    return;
  }

  $output = '<div class="wpsite-gallery">';
  $i = 0;

  foreach ( $images as $image ) {
    $i++;

    $output .= '<div class="wpsite-gallery-item">';
    $output .= wp_get_attachment_image( $image->ID, 'large' );
    $output .= '</div>';

    if ( $i % $atts['columns'] == 0 ) {
      $output .= '<div class="wpsite-clearfix"></div>';
    }
  }

  $output .= '</div>';

  return $output;
}
add_shortcode( 'wpsite_images', 'wpsite_shortcode_images' );



//Show Audio Shortcode:[wpsite_audio]
function wpsite_shortcode_audio( $atts ) {
  $atts = shortcode_atts( array(
    'orderby' => 'date',
    'order' => 'DESC',
    'ids' => '',
    'columns' => 3,
  ), $atts );

  $audio = get_posts( array(
    'post_type' => 'attachment',
    'post_mime_type' => 'audio',
    'orderby' => $atts['orderby'],
    'order' => $atts['order'],
    'include' => $atts['ids'],
    'numberposts' => -1,
  ) );

  if ( ! $audio ) {
    return;
  }

  $output = '<div class="wpsite-gallery">';
  $i = 0;

  foreach ( $audio as $item ) {
    $i++;

    $output .= '<div class="wpsite-gallery-item">';
    $output .= wp_audio_shortcode( array( 'src' => wp_get_attachment_url( $item->ID ) ) );
    $output .= '</div>';

    if ( $i % $atts['columns'] == 0 ) {
      $output .= '<div class="wpsite-clearfix"></div>';
    }
  }

  $output .= '</div>';

  return $output;
}
add_shortcode( 'wpsite_audio', 'wpsite_shortcode_audio' );



//Show Video Shortcode:[wpsite_video]
function wpsite_shortcode_video( $atts ) {
  $atts = shortcode_atts( array(
    'orderby' => 'date',
    'order' => 'DESC',
    'ids' => '',
    'columns' => 3,
  ), $atts );

  $videos = get_posts( array(
    'post_type' => 'attachment',
    'post_mime_type' => 'video',
    'orderby' => $atts['orderby'],
    'order' => $atts['order'],
    'include' => $atts['ids'],
    'numberposts' => -1,
  ) );

  if ( ! $videos ) {
    return;
  }

  $output = '<div class="wpsite-gallery">';
  $i = 0;

  foreach ( $videos as $video ) {
    $i++;

    $output .= '<div class="wpsite-gallery-item wpsite-video" style="flex-basis: calc(' . ( 100 / $atts['columns'] ) . '% - 20px); margin: 10px;">';

    $output .= wp_video_shortcode( array(
      'src' => $video->guid,
    ) );

    $output .= '</div><!-- /.wpsite-gallery-item -->';

    if ( $i % $atts['columns'] === 0 ) {
      $output .= '<div style="flex-basis: 100%;"></div>';
    }
  }

  $output .= '</div><!-- /.wpsite-gallery -->';

  return $output;
}
add_shortcode( 'wpsite_video', 'wpsite_shortcode_video' );


// Show Document Shortcode:[wpsite_documents]
function wpsite_shortcode_documents( $atts ) {
  $atts = shortcode_atts( array(
    'orderby' => 'date',
    'order' => 'DESC',
    'ids' => '',
    'columns' => 3,
  ), $atts );

  $documents = get_posts( array(
    'post_type' => 'attachment',
    'post_mime_type' => 'application/pdf,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,text/plain',
    'orderby' => $atts['orderby'],
    'order' => $atts['order'],
    'include' => $atts['ids'],
    'numberposts' => -1,
  ) );

  if ( ! $documents ) {
    return;
  }

  $output = '<div class="wpsite-gallery">';
  $i = 0;

  foreach ( $documents as $document ) {
    $image = wp_get_attachment_image_src( $document->ID, 'thumbnail' );
    $title = apply_filters( 'the_title', $document->post_title );
    $url = wp_get_attachment_url( $document->ID );
    $description = apply_filters( 'the_content', $document->post_excerpt );

    $output .= '<div class="wpsite-gallery-item">';
    $output .= '<a href="' . $url . '" target="_blank"><img src="' . $image[0] . '" alt="' . $title . '"></a>';
    $output .= '<h4><a href="' . $url . '" target="_blank">' . $title . '</a></h4>';
    $output .= '<p>' . $description . '</p>';
    $output .= '</div>';

    $i++;

    if ( $i % $atts['columns'] == 0 ) {
      $output .= '<div class="clear"></div>';
    }
  }

  $output .= '</div>';

  return $output;
}
add_shortcode( 'wpsite_documents', 'wpsite_shortcode_documents' );


//Show Spreadsheets Shortcode:[wpsite_spreadsheets]
function wpsite_shortcode_spreadsheets( $atts ) {
  $atts = shortcode_atts( array(
    'orderby' => 'date',
    'order' => 'DESC',
    'ids' => '',
    'columns' => 3,
  ), $atts );

  $spreadsheets = get_posts( array(
    'post_type' => 'attachment',
    'post_mime_type' => 'application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'orderby' => $atts['orderby'],
    'order' => $atts['order'],
    'include' => $atts['ids'],
    'numberposts' => -1,
  ) );

  if ( ! $spreadsheets ) {
    return;
  }

  $output = '<div class="wpsite-gallery">';
  $i = 0;

  foreach ( $spreadsheets as $spreadsheet ) {
    $output .= '<div class="wpsite-gallery-item">';
    $output .= wp_get_attachment_image( $spreadsheet->ID, 'thumbnail' );
    $output .= '<h4>' . $spreadsheet->post_title . '</h4>';
    $output .= '</div>';

    $i++;

    if ( $i % $atts['columns'] == 0 ) {
      $output .= '<div style="clear:both;"></div>';
    }
  }

  $output .= '</div>';

  return $output;
}

add_shortcode( 'wpsite_spreadsheets', 'wpsite_shortcode_spreadsheets' );
