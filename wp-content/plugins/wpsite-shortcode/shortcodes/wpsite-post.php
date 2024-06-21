<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


//Post Title Shortcode:[wpsite_posttitle]
function wpsite_shortcode_post_title( $atts ) {
	return get_the_title();
}
add_shortcode( 'wpsite_posttitle', 'wpsite_shortcode_post_title' );


//Post Excerpt Shortcode:[wpsite_postexcerpt]
function wpsite_shortcode_post_excerpt( $atts ) {
	global $post;
  $excerpt = $post->post_excerpt;
  if ( empty( $excerpt ) ) {
    $excerpt = get_the_content();
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = wp_trim_words( $excerpt, 55, '...' );
  }
  return $excerpt;
}
add_shortcode( 'wpsite_postexcerpt', 'wpsite_shortcode_post_excerpt' );


//Post Author Shortcode:[wpsite_postauthor]
function wpsite_shortcode_post_author( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $post_author = get_the_author();

  return $atts['before'] . $post_author . $atts['after'];
}
add_shortcode( 'wpsite_postauthor', 'wpsite_shortcode_post_author' );


//Post Date Shortcode:[wpsite_postdate]
function wpsite_shortcode_post_date( $atts ) {
$atts = shortcode_atts( array(
  'before' => '',
  'after' => '',
  'format' => get_option( 'date_format' ),
), $atts );

$post_date = get_the_date( $atts['format'] );

return $atts['before'] . $post_date . $atts['after'];
}
add_shortcode( 'wpsite_postdate', 'wpsite_shortcode_post_date' );


//Post Modified Date Shortcode:[wpsite_modifieddate]
function wpsite_shortcode_post_modified_date( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
    'format' => get_option('date_format'),
  ), $atts );

  $post_modified_date = get_the_modified_date( $atts['format'] );

  return $atts['before'] . $post_modified_date . $atts['after'];
}
add_shortcode( 'wpsite_modifieddate', 'wpsite_shortcode_post_modified_date' );


//Post Slug Shortcode:[wpsite_postslug]
function wpsite_shortcode_post_slug( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $post_slug = get_post_field( 'post_name', get_the_ID() );

  return $atts['before'] . $post_slug . $atts['after'];
}
add_shortcode( 'wpsite_postslug', 'wpsite_shortcode_post_slug' );


//Post Url Shortcode:[wpsite_posturl]
function wpsite_shortcode_post_url( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $post_url = get_permalink();

  return $atts['before'] . $post_url . $atts['after'];
}
add_shortcode( 'wpsite_posturl', 'wpsite_shortcode_post_url' );


//Post Featured Image Shortcode:[wpsite_postimage]
function wpsite_shortcode_post_featured_image( $atts ) {
  $atts = shortcode_atts( array(
    'size' => 'full',
  ), $atts );

  $post_id = get_the_ID();
  $thumbnail_id = get_post_thumbnail_id( $post_id );

  if ( ! $thumbnail_id ) {
    return ''; // Return empty string if there is no featured image for the post
  }

  $image = wp_get_attachment_image_src( $thumbnail_id, $atts['size'] );

  if ( ! $image || ! is_array( $image ) || ! isset( $image[0] ) ) {
    return ''; // Return empty string if there is an issue retrieving the image URL
  }

  return $image[0];
}
add_shortcode( 'wpsite_postimage', 'wpsite_shortcode_post_featured_image' );



//Post Tags Shortcode:[wpsite_posttag]
function wpsite_shortcode_post_tags( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
    'separator' => ', ',
  ), $atts );

  $tags = get_the_tag_list( $atts['before'], $atts['separator'], $atts['after'] );

  return $tags;
}
add_shortcode( 'wpsite_posttag', 'wpsite_shortcode_post_tags' );


//Post Categories Shortcode:[wpsite_postcat]
function wpsite_shortcode_post_categories( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
    'separator' => ', ',
  ), $atts );

  $categories = get_the_category_list( $atts['separator'], $atts['before'], $atts['after'] );

  return $categories;
}
add_shortcode( 'wpsite_postcat', 'wpsite_shortcode_post_categories' );



//Post Parent Category Shortcode:[wpsite_parentcat]
function wpsite_shortcode_post_parent_category( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $parent_category = '';

  $categories = get_the_category();
  foreach ( $categories as $category ) {
    if ( $category->parent ) {
      $parent_category = get_category( $category->parent );
      break;
    }
  }

  if ( $parent_category ) {
    return $atts['before'] . $parent_category->name . $atts['after'];
  } else {
    return '';
  }
}
add_shortcode( 'wpsite_parentcat', 'wpsite_shortcode_post_parent_category' );


//Post Comments Count Shortcode:[wpsite_commentscount]
function wpsite_shortcode_post_comments_count( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $comments_count = get_comments_number();

  return $atts['before'] . $comments_count . $atts['after'];
}
add_shortcode( 'wpsite_commentscount', 'wpsite_shortcode_post_comments_count' );
