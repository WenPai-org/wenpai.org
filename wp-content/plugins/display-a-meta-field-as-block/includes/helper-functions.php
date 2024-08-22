<?php
/**
 * Helper functions
 *
 * @package   MetaFieldBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( __NAMESPACE__ . '\meta_field_block_get_block_markup' ) ) :
	/**
	 * Build block markup.
	 *
	 * @param  string     $content     Block content.
	 * @param  array      $attributes  Block attributes.
	 * @param  WP_Block   $block       Block instance.
	 * @param  int|string $post_id     Post Id.
	 * @param  string     $object_type Object type.
	 * @param  string     $classes     Additional block classes.
	 * @param  boolean    $is_dynamic  Is a static block.
	 * @param  array      $args {
	 *   Optional: The additional parameters.
	 *   @type string $label The ACF field label.
	 * }
	 *
	 * @return string Returns the markup for the block.
	 */
	function meta_field_block_get_block_markup( $content, $attributes, $block, $post_id, $object_type = '', $classes = '', $is_dynamic = true, $args = [] ) {
		// Allow third-party plugins to alter the content.
		$content = apply_filters( 'meta_field_block_get_block_content', $content, $attributes, $block, $post_id, $object_type );
		$content = is_array( $content ) || is_object( $content ) ? '<code><em>' . __( 'This data type is not supported!', 'display-a-meta-field-as-block' ) . '</em></code>' : $content;

		if ( ! $is_dynamic ) {
			return $content;
		}

		if ( '' === trim( $content ) ) {
			// Hide the block.
			if ( $attributes['hideEmpty'] ?? false ) {
				return '';
			}

			$content = $attributes['emptyMessage'] ?? '';
		}

		$tag_name  = $attributes['tagName'] ?? 'div';
		$inner_tag = 'div' === $tag_name ? 'div' : 'span';

		// Build allowed html tags.
		$allowed_html_tags = meta_field_block_get_allowed_html_tags();

		// Get field type.
		$field_type = $attributes['fieldType'] ?? '';

		// Get field name.
		$field_name = $attributes['fieldName'] ?? '';

		// Don't filter shortcode.
		$is_shortcode = 'dynamic' === $field_type && '[' === substr( $field_name, 0, 1 ) && ']' === substr( $field_name, -1 );
		if ( apply_filters( 'meta_field_block_kses_content', ! $is_shortcode, $attributes, $block, $post_id, $object_type, $content ) ) {
			// Escape content.
			$content = wp_kses( $content, $allowed_html_tags );
		}

		// Return early if we only need raw value.
		if ( 'dynamic' === $field_type && ( $attributes['fetchRawValue'] ?? false ) ) {
			return $content;
		}

		// Wrap around a div.
		$content = sprintf( '<%2$s class="value">%1$s</%2$s>', $content, $inner_tag );

		$prefix = $attributes['prefix'] ?? '';

		if ( ! $prefix && ( $attributes['labelAsPrefix'] ?? false ) ) {
			$field_label = $args['label'] ?? '';
			$prefix      = $field_label ? $field_label : meta_field_block_get_field_label( $attributes, $post_id );
		}
		$prefix = $prefix ? sprintf( '<%2$s class="prefix"%3$s>%1$s</%2$s>', wp_kses( $prefix, $allowed_html_tags ), $inner_tag, meta_field_block_build_prefix_suffix_style( $attributes['prefixSettings'] ?? '' ) ) : '';

		$suffix = $attributes['suffix'] ?? '';
		$suffix = $suffix ? sprintf( '<%2$s class="suffix"%3$s>%1$s</%2$s>', wp_kses( $suffix, $allowed_html_tags ), $inner_tag, meta_field_block_build_prefix_suffix_style( $attributes['suffixSettings'] ?? '' ) ) : '';

		if ( ! empty( $attributes['displayLayout'] ) ) {
			$classes .= " is-display-{$attributes['displayLayout']}";
		}
		if ( isset( $attributes['textAlign'] ) ) {
			$classes .= " has-text-align-{$attributes['textAlign']}";
		}
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => trim( $classes ) ) );

		return sprintf( '<%3$s %1$s>%2$s</%3$s>', $wrapper_attributes, $prefix . $content . $suffix, $tag_name );
	}
endif;

if ( ! function_exists( __NAMESPACE__ . '\meta_field_block_get_field_label' ) ) :
	/**
	 * Get field label.
	 *
	 * @param  array      $attributes Block attributes.
	 * @param  int|string $post_id    Post Id.
	 *
	 * @return string Returns the field label.
	 */
	function meta_field_block_get_field_label( $attributes, $post_id ) {
		$field_label = '';

		$field_type = $attributes['fieldType'] ?? 'meta';
		$field_name = $attributes['fieldName'] ?? '';

		if ( 'acf' === $field_type && \function_exists( 'get_field_object' ) ) {
			$field = get_field_object( $field_name, $post_id );

			if ( $field ) {
				$field_label = $field['label'] ?? '';
			}
		}

		return $field_label;
	}
endif;

if ( ! function_exists( __NAMESPACE__ . '\meta_field_block_get_allowed_html_tags' ) ) :
	/**
	 * Get allowed html tags
	 *
	 * @return array Returns the array of allowed html tags.
	 */
	function meta_field_block_get_allowed_html_tags() {
		// Build allowed html tags from $allowedposttags .
		$allowed_html_tags = wp_kses_allowed_html( 'post' );

		// Allow displaying iframe.
		$allowed_html_tags['iframe'] = [
			'src'             => true,
			'srcdoc'          => true,
			'id'              => true,
			'name'            => true,
			'width'           => true,
			'height'          => true,
			'title'           => true,
			'loading'         => true,
			'allow'           => true,
			'allowfullscreen' => true,
			'frameborder'     => true,
			'class'           => true,
			'style'           => true,
		];

		// SVG.
		$svg_core_attributes = [
			'id'       => true,
			'tabindex' => true,
			'class'    => true,
			'style'    => true,
		];

		$svg_presentation_attributes = [
			'clip-path'           => true,
			'clip-rule'           => true,
			'color'               => true,
			'color-interpolation' => true,
			'cursor'              => true,
			'display'             => true,
			'fill'                => true,
			'fill-opacity'        => true,
			'fill-rule'           => true,
			'filter'              => true,
			'mask'                => true,
			'opacity'             => true,
			'pointer-events'      => true,
			'shape-rendering'     => true,
			'stroke'              => true,
			'stroke-dasharray'    => true,
			'stroke-dashoffset'   => true,
			'stroke-linecap'      => true,
			'stroke-linejoin'     => true,
			'stroke-miterlimit'   => true,
			'stroke-opacity'      => true,
			'stroke-width'        => true,
			'transform'           => true,
			'vector-effect'       => true,
			'visibility'          => true,
		];

		// Allow common attributes of SVG images.
		$allowed_html_tags['svg'] = array_merge(
			[
				'viewbox'             => true,
				'xmlns'               => true,
				'preserveaspectratio' => true,
				'width'               => true,
				'height'              => true,
				'x'                   => true,
				'y'                   => true,
				'title'               => true,
				'name'                => true,
				'role'                => true,
				'aria-hidden'         => true,
				'aria-labelledby'     => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['g'] = array_merge(
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['path'] = array_merge(
			[
				'd'          => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['line'] = array_merge(
			[
				'x1'         => true,
				'y1'         => true,
				'x2'         => true,
				'y2'         => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['rect'] = array_merge(
			[
				'x'          => true,
				'y'          => true,
				'rx'         => true,
				'ry'         => true,
				'width'      => true,
				'height'     => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['circle'] = array_merge(
			[
				'cx'         => true,
				'cy'         => true,
				'r'          => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['ellipse'] = array_merge(
			[
				'cx'         => true,
				'cy'         => true,
				'rx'         => true,
				'ry'         => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['polygon'] = array_merge(
			[
				'points'     => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['polyline'] = array_merge(
			[
				'points'     => true,
				'pathLength' => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		$allowed_html_tags['text'] = array_merge(
			[
				'x'            => true,
				'y'            => true,
				'dx'           => true,
				'dy'           => true,
				'rotate'       => true,
				'lengthAdjust' => true,
				'textLength'   => true,
			],
			$svg_core_attributes,
			$svg_presentation_attributes
		);

		// Allow third-party to change it.
		return apply_filters( 'meta_field_block_kses_allowed_html', $allowed_html_tags );
	}
endif;

if ( ! function_exists( __NAMESPACE__ . '\meta_field_block_build_prefix_suffix_style' ) ) :
	/**
	 * Build style for prefix/suffix
	 *
	 * @param  array $setting_value
	 *
	 * @return string Returns the style string.
	 */
	function meta_field_block_build_prefix_suffix_style( $setting_value ) {
		$style = '';
		if ( ! $setting_value || ! is_array( $setting_value ) ) {
			return $style;
		}

		if ( $setting_value['fontSize'] ?? '' ) {
			$style .= 'font-size:' . $setting_value['fontSize'] . ';';
		}
		if ( $setting_value['fontWeight'] ?? '' ) {
			$style .= 'font-weight:' . $setting_value['fontWeight'] . ';';
		}
		if ( $setting_value['fontStyle'] ?? '' ) {
			$style .= 'font-style:' . $setting_value['fontStyle'] . ';';
		}
		if ( $setting_value['lineHeight'] ?? '' ) {
			$style .= 'line-height:' . $setting_value['lineHeight'] . ';';
		}

		// Add gap to prefix, suffix and value.
		$gap = $setting_value['gap']['top'] ?? '';
		if ( $gap ) {
			$style .= '--mfb--gap:' . ( strpos( $gap, 'var:preset|spacing|' ) !== false ? 'var(--wp--preset--spacing--' . str_replace( 'var:preset|spacing|', '', $gap ) . ')' : $gap ) . ';';
		}

		if ( $style ) {
			$style = ' style="' . esc_attr( $style ) . '"';
		}

		return $style;
	}
endif;
