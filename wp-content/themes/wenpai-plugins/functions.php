<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// 目录 meta 区块过滤
add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id, $object_type ) {
	$field_name = $attributes['fieldName'] ?? '';

	if ( 'rating' === $field_name ) {
		global $post;
		$rating = get_post_meta( $post->ID, 'rating', true );
		$rating = (int) $rating / 20;
		// 渲染成星星
		$block_content = '<div class="rating">';
		for ( $i = 0; $i < 5; $i ++ ) {
			$block_content .= '<span class="dashicons dashicons-star-' . ( $i < $rating ? 'filled' : 'empty' ) . '"></span>';
		}
		$block_content .= '</div>';

		return $block_content;
	}
	if ( 'updated_at' === $field_name ) {
		global $post;
		$time          = get_the_modified_date( 'Y-m-d H:i:s', $post );
		$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
	}
	if ( 'created_at' === $field_name ) {
		global $post;
		$time          = get_the_date( 'Y-m-d H:i:s', $post );
		$block_content = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . '前';
	}
	if ( 'screenshots' === $field_name ) {
		global $post;
		$image_ids     = get_post_meta( $post->ID, 'screenshots', true );
		$block_content = '';
		if ( ! empty( $image_ids ) ) {
			$block_content = '<div class="screenshots">';
			foreach ( $image_ids as $image_id ) {
				$block_content .= wp_get_attachment_image( $image_id, 'full' );
			}
			$block_content .= '</div>';
		}
	}

	return $block_content;
}, 10, 5 );
