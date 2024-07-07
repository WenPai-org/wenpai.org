<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 全局静态文件引入
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'wenpai-translate', get_stylesheet_directory_uri() . '/assets/css/translate.css' );
} );


// 前台调用 Dashicons 图标
add_action( 'wp_enqueue_scripts', 'dashicons_style_front_end' );
function dashicons_style_front_end() {
  wp_enqueue_style( 'dashicons' );
}


// 统计最新用户
function latest_users_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '10'
	), $atts );

	$users  = get_users( array( 'orderby' => 'registered', 'order' => 'DESC', 'number' => $atts['number'] ) );
	$output = '<div class="wpavatar-latest-users">';
	foreach ( $users as $user ) {
		$output .= '<div class="wpavatar-latest-user">';
		$output .= get_avatar( $user->ID, 50 );
		$output .= '<div class="wpavatar-latest-user-name">' . $user->display_name . '</div>';
		$output .= '</div>';
	}
	$output .= '</div>';

	return $output;
}

add_shortcode( 'latest_users', 'latest_users_shortcode' );


// 翻译员排行榜
function count_translator_rank( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '6'
	), $atts );

	$html = '<div class="translator-list">';

	global $wpdb;

	// 查询统计数据
	$bot_id = BOT_USER_ID;
	$sql    = <<<SQL
        SELECT *
        FROM {$wpdb->prefix}gp_user_translations_count
        WHERE user_id != 0 AND user_id != {$bot_id}
        AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= DATE(date_modified)
        GROUP BY user_id
        ORDER BY suggested DESC
        LIMIT {$atts['number']};
SQL;

	$translators_month = $wpdb->get_results( $sql );

	foreach ( $translators_month as $k => $v ) {
		$user_info = get_user_by( 'id', $v->user_id );
		if ( $user_info ) {
			$suggested = number_format( $v->suggested );
			$accepted  = number_format( $v->accepted );
			$html      .= sprintf( '<li><em>%d.</em> <div class="rank-list__name"><a href="https://profiles.wenpai.org/%s">%s%s</a></div><span class="rank-list__number">提交%s条，接受%s条</span></li>', $k + 1, $user_info->data->user_login, get_avatar( $user_info->data->user_email, 32 ), $user_info->data->display_name, $suggested, $accepted );
		}
	}

	$html .= '</div>';

	return $html;
}

add_shortcode( 'translator_rank', 'count_translator_rank' );


// 统计全部 GlotPress 项目翻译字符串
function gp_translation_statistics_shortcode() {
	$translation_statistics = get_transient( 'gp_translation_statistics' );

	if ( false === $translation_statistics ) {
		global $wpdb;

		// 获取所有项目的翻译字符串数量统计
		$total_current_strings = number_format_i18n( $wpdb->get_var( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}gp_translations
            WHERE status = 'current'
        " ) );

		// 获取等待状态的翻译字符串数量统计
		$total_waiting_strings = number_format_i18n( $wpdb->get_var( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}gp_translations
            WHERE status = 'waiting'
        " ) );

		// 获取模糊状态的翻译字符串数量统计
		$total_fuzzy_strings = number_format_i18n( $wpdb->get_var( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}gp_translations
            WHERE status = 'fuzzy'
        " ) );

		// 获取拒绝状态的翻译字符串数量统计
		$total_rejected_strings = number_format_i18n( $wpdb->get_var( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}gp_translations
            WHERE status = 'rejected'
        " ) );

		// 构建 HTML 输出
		$translation_statistics = '<div class="translation-statistics">';
		$translation_statistics .= '<ul>';
		$translation_statistics .= '<li><p>全部已翻译词条：' . $total_current_strings . '</p></li>';
		$translation_statistics .= '<li><p>待审批项目：' . $total_waiting_strings . '</p></li>';
		$translation_statistics .= '<li><p>模糊翻译：' . $total_fuzzy_strings . '</p></li>';
		$translation_statistics .= '<li><p>质量低：' . $total_rejected_strings . '</p></li>';
		$translation_statistics .= '</ul>';
		$translation_statistics .= '</div>';

		// 将结果存储到 Transients 中，有效期设置为 24 小时
		set_transient( 'gp_translation_statistics', $translation_statistics, DAY_IN_SECONDS );
	}

	return $translation_statistics;
}

add_shortcode( 'gp_translation_statistics', 'gp_translation_statistics_shortcode' );


// 统计全部 GlotPress 项目词条总数
function gp_total_entries_shortcode() {
	$total_entries = get_transient( 'gp_total_entries_count' );

	if ( false === $total_entries ) {
		global $wpdb;

		// 获取所有项目的词条总数
		$total_entries = number_format_i18n( $wpdb->get_var( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}gp_originals
        " ) );

		// 将结果存储到 Transients 中，有效期设置为 24 小时
		set_transient( 'gp_total_entries_count', $total_entries, DAY_IN_SECONDS );
	}

	return $total_entries;
}

add_shortcode( 'gp_total_entries', 'gp_total_entries_shortcode' );

function translate_current_time() {
	date_default_timezone_set( 'Asia/Shanghai' );

	return date( 'Y年n月j日 ~ H:i' );
}

add_shortcode( 'current_time', 'translate_current_time' );


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

	// Safety check.
	if ( empty( $usercount ) || ! is_array( $usercount ) ) {
		return false;
	}

	if ( ! empty( $atts['role'] ) ) {
		// Get the custom role. could be 'customer', 'administrator', 'editor' etc.
		$count = isset( $usercount['avail_roles'][ $atts['role'] ] ) ? $usercount['avail_roles'][ $atts['role'] ] : '0';

		// 使用 number_format() 函数将数字格式化为带千分号的形式
		return number_format( $count );
	}

	$count = ! empty( $usercount['total_users'] ) ? $usercount['total_users'] : '0';

	// 使用 number_format() 函数将数字格式化为带千分号的形式
	return number_format( $count );
}

// Creating a shortcode to display user count.
add_shortcode( 'total_user_count', 'tusc_user_count' );


/**
 * 用户角色下载权限限制
 */
// add_filter('gp_export_locale', function () {
//    $currentUser = wp_get_current_user();
//    if(!(!empty($currentUser->roles)  && in_array('administrator', $currentUser->roles) || in_array('editor', $currentUser->roles) || in_array('author', $currentUser->roles))) {
//        wp_die('您无权下载翻译');
//    }
//  });


/**
 * 未登录用户和订阅组用户只能查看翻译集的前5页
 */
// 这个限制是掩耳盗铃，直接通过导出 API 就无视掉了，注释之
/*add_action( 'gp_init', function () {
	preg_match( '/\/.+\/zh-cn\/default/', $_SERVER['REQUEST_URI'], $url_matches );

	if ( is_array( $url_matches ) && ! empty( $url_matches ) ) {
		if ( ! is_user_logged_in() && @(int) $_GET['page'] >= 8 ) {
			wp_die( '访问页数超过最大限制，请先登录后浏览：<a href="/login">点击跳转</a>' );
		} elseif ( ( current_user_can( 'read' ) && ! current_user_can( 'edit_posts' ) ) && @(int) $_GET['page'] >= 6 ) {
			wp_die( '为防止恶意爬虫抓取，您不具有查看当前页面的权限，如有问题，请联系管理员。' );
		}
	}
} );*/


/**
 * 文章数量统计简码
 */

function wpb_total_posts() {
	$total = wp_count_posts()->publish;

	return $total;
}

add_shortcode( 'total_posts', 'wpb_total_posts' );


/**
 * 项目数量统计
 */
add_shortcode( 'project_count', function () {
	global $wpdb;

	$res = $wpdb->get_results( 'SELECT COUNT(id) AS project_count FROM ' . $wpdb->prefix . 'gp_projects;' );
	foreach ( $res as $v ) {
		// 使用number_format()函数将数字格式化为带千分号的形式
		return number_format( $v->project_count );
	}

	return 0;
} );


/**
 * 根据slug获取项目描述
 */
add_shortcode( 'get_project_desc', function ( $atts ) {
	global $wpdb;

	if ( ! is_array( $atts ) || empty( $atts ) || ! key_exists( 'slug', $atts ) ) {
		return '';
	}

	$res = $wpdb->get_results( $wpdb->prepare( 'SELECT description FROM ' . $wpdb->prefix . 'gp_projects WHERE slug=%s;', [ $atts['slug'] ] ) );
	foreach ( $res as $v ) {
		return $v->description;
	}

	return '';
} );
