<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**
 * 全局静态文件引入
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'wenpai-plugins', get_stylesheet_directory_uri() . '/assets/css/directory.css' );
} );


// 目录 meta 区块过滤
add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id, $object_type ) {
	$field_name = $attributes['fieldName'] ?? '';
	global $post;

	if ( 'rating' === $field_name ) {
		$rating = get_post_meta( $post->ID, 'rating', true );
		$rating = (int) $rating / 20;
		// 渲染成星星
		$block_content = '<div class="rating">';
		for ( $i = 0; $i < 5; $i ++ ) {
			$block_content .= '<span class="dashicons dashicons-star-' . ( $i < $rating ? 'filled' : 'empty' ) . '"></span>';
		}
		$block_content .= '</div>';
	}
	if ( 'updated_at' === $field_name ) {
		$time          = get_the_modified_date( 'Y-m-d H:i:s', $post );
		$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
	}
	if ( 'created_at' === $field_name ) {
		$time          = get_the_date( 'Y-m-d H:i:s', $post );
		$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
	}
	if ( 'screenshots' === $field_name ) {
		$image_ids     = get_post_meta( $post->ID, 'screenshots', true );
		$block_content = '';
		if ( ! empty( $image_ids ) ) {
			$block_content = '<div class="screenshots">';
			foreach ( $image_ids as $image_id ) {
				//$block_content .= wp_get_attachment_image( $image_id, 'full' );
				$block_content .= '<img width="100%" src="' . wp_get_attachment_image_url( $image_id, 'full' ) . '" />';
			}
			$block_content .= '</div>';
		}
	}
	if ( 'views' === $field_name ) {
		$views = (int) get_post_meta( $post->ID, 'views', true );
		if ( $views < 10000 ) {
			$block_content = $views;
		} elseif ( $views < 1000000 ) {
			$summary       = round( $views / 10000, 1 );
			$block_content = $summary . ' 万';
		} else {
			$block_content = '100 万+';
		}
		$block_content .= '个';
	}

	return $block_content;
}, 10, 5 );


// 前台调用 Dashicons 图标
add_action( 'wp_enqueue_scripts', 'dashicons_style_front_end' );
function dashicons_style_front_end() {
  wp_enqueue_style( 'dashicons' );
}
