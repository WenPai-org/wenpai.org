<?php

use Platform\i18n;

defined( 'ABSPATH' ) || exit;

// 静态资源加载
add_action( 'init', 'load_assets' );
function load_assets(): void {
	wp_enqueue_script( 'translate', get_stylesheet_directory_uri() . '/assets/js/translate.js', array(), time(), true );
	wp_enqueue_style( 'translate', get_stylesheet_directory_uri() . '/assets/css/translate.css', array(), time() );
	wp_enqueue_style( 'docs', get_stylesheet_directory_uri() . '/assets/css/docs.css', array(), time() );
}

add_filter( 'the_title', 'do_translate', 1 );
add_filter( 'the_content', 'do_translate', 1 );
add_action( 'wp_footer', 'print_translate_project_path' );
add_action( 'wp_footer', 'add_translate_modal' );


/**
 * Create the user count shortcode.
 */
function tusc_user_count( $atts = array() ) {

	// Attributes from shortcode.
	$atts = shortcode_atts(
		array(
			'role' => false,
		),
		$atts,
		'total_user_count'
	);

	$usercount = count_users();

	// Saftey check.
	if ( empty( $usercount ) || ! is_array( $usercount ) ) {
		return false;
	}

	if ( ! empty( $atts['role'] ) ) {
		// Get the custom role. could be 'customer', 'administrator', 'editor' etc.
		$count = isset( $usercount['avail_roles'][ $atts['role'] ] ) ? $usercount['avail_roles'][ $atts['role'] ] : '0';

		return $count;
	}

	$count = ! empty( $usercount['total_users'] ) ? $usercount['total_users'] : '0';

	return $count;
}

// Creating a shortcode to display user count.
add_shortcode( 'total_user_count', 'tusc_user_count' );


// Display user avatar
function wpavatar_latest_users_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '8'
	), $atts );

	$users  = get_users( array( 'orderby' => 'registered', 'order' => 'DESC', 'number' => $atts['number'] ) );
	$output = '<div class="wpavatar-latest-users">';
	foreach ( $users as $user ) {
		$output .= '<div class="wpavatar-latest-user">';
		$output .= get_avatar( $user->ID, 40 );
		$output .= '<div class="wpavatar-latest-user-name">' . $user->display_name . '</div>';
		$output .= '</div>';
	}
	$output .= '</div>';

	return $output;
}

add_shortcode( 'wpavatar_latest_users', 'wpavatar_latest_users_shortcode' );


/**
 * 文章数量统计简码
 */

function wpb_total_posts() {
	$total = wp_count_posts()->publish;

	return $total;
}

add_shortcode( 'total_posts', 'wpb_total_posts' );


function do_translate( string $content ): string {
	if ( ! is_single() ) {
		return $content;
	}

	global $post;
	$slug = "docs/helphub/{$post->post_name}";

	return i18n::get_instance()->translate( '', $content, $slug, true );
}

function print_translate_project_path(): void {
	if ( ! is_single() ) {
		return;
	}

	global $post;
	$slug = "docs/helphub/{$post->post_name}";

	echo <<<JS
<script>
const translate_project_path = '{$slug}';
</script>
JS;
}

function add_translate_modal(): void {
	if ( ! is_single() ) {
		return;
	}
	?>
    <!-- Modal -->
    <div class="modal fade" id="translate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">翻译</h5>
                    <small id="translate-status">
                    </small>
                </div>
                <div class="modal-body">
                    <div class="source-string">
                        <p class="original"></p>
                        <small>
                            <blockquote class="original_zh-cn">
                            </blockquote>
                        </small>
                    </div>
                    <div class="form-floating">
                        <label>译文</label>
                        <textarea class="translation-text"
                                  style="height: 150px"></textarea>
                        <button class="copy-original"
                                title="将原始字符串复制到翻译区域（覆盖现有文本）。">
                            从原文复制
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submit-cancel">取消</button>
                    <button type="button" id="submit-translate">提交翻译</button>
                </div>
            </div>
        </div>
    </div>
	<?php
}