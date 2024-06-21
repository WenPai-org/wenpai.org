<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// load_dashicons
add_action( 'wp_enqueue_scripts', 'dashicons_style_front_end' );
function dashicons_style_front_end() {
	wp_enqueue_style( 'dashicons' );
}


/**
 * 在 WordPress 搜索中包括bbPress论坛搜索
 */
function bbpress_search_filter( $query ) {
	if ( ! is_admin() && $query->is_search ) {
		$query->set( 'post_type', array( 'post', 'topic' ) );
	}

	return $query;
}

add_filter( 'pre_get_posts', 'bbpress_search_filter' );

/**
 * 修改搜索结果页面的URL
 */
function change_search_url_rewrite() {
	if ( is_search() && ! empty( $_GET['s'] ) ) {
		wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
		exit();
	}
}

add_action( 'template_redirect', 'change_search_url_rewrite' );


/*bbPress email fix
 * https://bbpress.org/forums/topic/remove-noreply-email-from-notification-emails/
 * */

add_filter( 'wp_mail_from', 'email_sent_from' );
function email_sent_from( $email ) {
	return 'no-reply@e-mail.weixiaoduo.com';
}


/**
 * 解决标题过长的问题
 */
add_filter( 'bbp_get_title_max_length', 'rkk_change_title' );

function rkk_change_title( $default ) {
	$default = 150;

	return $default;
}


//去掉bbpress论坛发帖时的 tag 标签输入框
function remove_bbpress_tag_input() {
	if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
		remove_action( 'bbp_theme_before_topic_form_tags', 'bbp_topic_tags_dropdown' );
	}
}

add_action( 'init', 'remove_bbpress_tag_input' );


function set_default_bbpress_forum( $forum_id ) {
	if ( empty( $forum_id ) && is_bbpress() ) {
		// 设置你想要默认选中的论坛ID
		$default_forum_id = 17758; // 替换为你的论坛ID

		return $default_forum_id;
	}

	return $forum_id;
}

add_filter( 'bbp_get_form_topic_forum', 'set_default_bbpress_forum' );


// Display author website without http:// or https:// and www.
function bbp_post_starter() {
	$topic_author = bbp_get_topic_author_id();
	$reply_author = bbp_get_reply_author_id();

	if ( $reply_author === $topic_author ) {
		?>
        <div class="bbp-starter"><span class="bbp-starter-bq">楼主</span></div>
		<?php
	}
}

add_action( 'bbp_theme_after_reply_author_details', 'bbp_post_starter' );


// Display user avatar
function wpavatar_latest_users_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '10'
	), $atts );

	$users  = get_users( array( 'orderby' => 'registered', 'order' => 'DESC', 'number' => $atts['number'] ) );
	$output = '<div class="wpavatar-latest-users">';
	foreach ( $users as $user ) {
		$output .= '<div class="wpavatar-latest-user">';
		$output .= get_avatar( $user->ID, 45 );
		$output .= '<div class="wpavatar-latest-user-name">' . $user->display_name . '</div>';
		$output .= '</div>';
	}
	$output .= '</div>';

	return $output;
}

add_shortcode( 'wpavatar_latest_users', 'wpavatar_latest_users_shortcode' );


// Display random user avatar
function wpavatar_random_users_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '10'
	), $atts );

	$users  = get_users( array( 'orderby' => 'rand', 'number' => $atts['number'] ) );
	$output = '<div class="wpavatar-random-users">';
	foreach ( $users as $user ) {
		$output .= '<div class="wpavatar-random-user">';
		$output .= get_avatar( $user->ID, 45 );
		$output .= '<div class="wpavatar-random-user-name">' . $user->display_name . '</div>';
		$output .= '</div>';
	}
	$output .= '</div>';

	return $output;
}

add_shortcode( 'wpavatar_random_users', 'wpavatar_random_users_shortcode' );


// 注册一个新的简码 [wpavatar_bbp_reply_users]
function wpavatar_bbp_reply_users_shortcode( $atts ) {
	ob_start();

	// 获取当前帖子的 ID
	$post_id = get_the_ID();

	// 获取发帖用户的头像和链接
	$author_id          = get_post_field( 'post_author', $post_id );
	$author_avatar      = get_avatar( $author_id, 50 );
	$author_profile_url = bbp_get_user_profile_url( $author_id );

	// 获取参与回复的用户头像
	$args = array(
		'post_parent' => $post_id,
		'post_type'   => 'reply',
		'post_status' => 'publish',
		'orderby'     => 'date',
		'order'       => 'ASC',
	);

	$replies            = get_posts( $args );
	$displayed_user_ids = array();

	// 先显示发帖用户的头像和链接
	echo '<div class="wpavatar-bbp-reply-users">';
	echo '<div class="wpavatar-author-avatar"><a href="' . $author_profile_url . '">' . $author_avatar . '</a></div>';
	echo '<div class="wpavatar-reply-avatars">';

	// 添加发帖用户的 ID 到已显示的数组中
	$displayed_user_ids[] = $author_id;

	if ( $replies ) {
		foreach ( $replies as $reply ) {
			$reply_author_id = $reply->post_author;

			// 检查是否已经显示过该用户头像
			if ( ! in_array( $reply_author_id, $displayed_user_ids ) ) {
				$reply_author_avatar      = get_avatar( $reply_author_id, 50 );
				$reply_author_profile_url = bbp_get_user_profile_url( $reply_author_id );
				echo '<a href="' . $reply_author_profile_url . '">' . $reply_author_avatar . '</a>';

				// 将用户 ID 添加到已显示的数组中
				$displayed_user_ids[] = $reply_author_id;
			}
		}
	}

	echo '</div></div>';

	return ob_get_clean();
}

add_shortcode( 'wpavatar_bbp_reply_users', 'wpavatar_bbp_reply_users_shortcode' );


function get_author_bbp_count( $atts ) {
	$author_id = get_post_field( 'post_author' ); // 获取当前文章作者 ID

	$topic_count = bbp_get_user_topic_count_raw( $author_id ); // 获取用户话题数量
	$reply_count = bbp_get_user_reply_count_raw( $author_id ); // 获取用户回复数量

	$output = "参与 {$topic_count} 个话题和 {$reply_count} 条回复";

	return $output;
}

add_shortcode( 'author_bbp_count', 'get_author_bbp_count' );


function bbpress_all_forums_shortcode() {
	ob_start();
	?>
    <script>
        function redirectToForum() {
            var selectedForum = document.getElementById('bbp_forum');
            if (selectedForum.value !== '') {
                window.location.href = selectedForum.value;
            }
        }
    </script>

	<?php
	// 获取所有论坛
	$forums = get_posts( array(
		'post_type'      => bbp_get_forum_post_type(),
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	if ( $forums ) {
		echo '<select name="bbp_forum" id="bbp_forum">';
		echo '<option value="">选择版块</option>';

		foreach ( $forums as $forum ) {
			echo '<option value="' . esc_url( get_permalink( $forum->ID ) ) . '">' . esc_html( $forum->post_title ) . '</option>';
		}

		echo '</select>';
	} else {
		echo '没有找到论坛。';
	}
	?>
    <script>
        document.getElementById('bbp_forum').addEventListener('change', redirectToForum);
    </script>
	<?php
	return ob_get_clean();
}

add_shortcode( 'bbp_all_forums', 'bbpress_all_forums_shortcode' );


// bbpress count 简码解析

function bbp_forum_count_shortcode() {
	$forum_count = wp_count_posts( 'forum' );

	return '<div class="bbpress-count"><p><span>' . number_format( $forum_count->publish ) . '</span></p></div>';
}

add_shortcode( 'bbp_forum_count', 'bbp_forum_count_shortcode' );


function bbp_topic_count_shortcode() {
	$topic_count = wp_count_posts( 'topic' );

	return '<div class="bbpress-count"><p><span>5' . number_format( $topic_count->publish ) . '</span></p></div>';
}

add_shortcode( 'bbp_topic_count', 'bbp_topic_count_shortcode' );


function bbp_reply_count_shortcode() {
	$reply_count = wp_count_posts( 'reply' );

	return '<div class="bbpress-count"><p><span>3' . number_format( $reply_count->publish ) . '</span></p></div>';
}

add_shortcode( 'bbp_reply_count', 'bbp_reply_count_shortcode' );


function pw_bbp_shortcodes( $content, $reply_id ) {

	$reply_author = bbp_get_reply_author_id( $reply_id );

	if ( user_can( $reply_author, pw_bbp_parse_capability() ) ) {
		return do_shortcode( $content );
	}

	return $content;
}

add_filter( 'bbp_get_reply_content', 'pw_bbp_shortcodes', 10, 2 );
add_filter( 'bbp_get_topic_content', 'pw_bbp_shortcodes', 10, 2 );

function pw_bbp_parse_capability() {
	return apply_filters( 'pw_bbp_parse_shortcodes_cap', 'publish_forums' );
}

