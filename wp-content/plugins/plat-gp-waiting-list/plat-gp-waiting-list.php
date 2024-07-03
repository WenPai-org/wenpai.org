<?php
/**
 * Plugin Name: GlotPress 待审核项目列表
 * Description: 为 GlotPress 添加一个待审核项目列表，通过简码<code>gp-waiting-list</code>调用。现支持分页。
 * Author: 树新蜂
 * Version: 1.1.0
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

function plat_gp_get_waiting_list_current_page() {
	// 获取当前URL
	$current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	// 使用正则表达式匹配分页数字
	preg_match( '/\/page\/(\d+)\//', $current_url, $matches );

	// 如果找到匹配项，则返回页码，否则默认为第1页
	if ( isset( $matches[1] ) ) {
		return (int) $matches[1];
	} else {
		return 1; // 默认返回第一页
	}
}

add_shortcode( 'gp-waiting-list', 'plat_gp_get_waiting_list' );

function plat_gp_get_waiting_list( $atts = [], $content = null, $tag = '' ) {
	global $wpdb;

	// 短码属性处理，允许从前台传入每页显示数量，默认为20
	$atts       = array_change_key_case( (array) $atts, CASE_LOWER );
	$wporg_atts = shortcode_atts( [
		'per_page' => 20, // 默认每页显示20条
	], $atts, $tag );

	$paged  = plat_gp_get_waiting_list_current_page();
	$offset = ( $paged - 1 ) * $wporg_atts['per_page'];

	$total_query = "SELECT COUNT(DISTINCT t.translation_set_id)
                    FROM wp_" . SITE_ID_TRANSLATE . "_gp_translations AS t
                    JOIN wp_" . SITE_ID_TRANSLATE . "_gp_originals AS o ON t.original_id=o.id
                    WHERE t.`status`='waiting' AND o.`status`='+active';";
	$total       = $wpdb->get_var( $total_query );
	$total_pages = ceil( $total / $wporg_atts['per_page'] );

	$waiting_list_query = $wpdb->prepare( "SELECT DISTINCT t.translation_set_id 
                                          FROM wp_" . SITE_ID_TRANSLATE . "_gp_translations AS t 
                                          RIGHT JOIN wp_" . SITE_ID_TRANSLATE . "_gp_originals AS o ON t.original_id=o.id 
                                          WHERE t.`status`='waiting' AND o.`status`='+active' 
                                          GROUP BY t.translation_set_id 
                                          LIMIT %d, %d;", $offset, $wporg_atts['per_page'] );
	$waiting_list       = $wpdb->get_results( $waiting_list_query );

	// 把所有待审核项目更新为已审核
	/*$res = $wpdb->query( "UPDATE wp_" . SITE_ID_TRANSLATE . "_gp_translations SET `status`='current' WHERE `status`='waiting';" );
	var_dump( $res );
	exit;*/

	$translation_set_ids = [];
	foreach ( $waiting_list as $v ) {
		$translation_set_ids[] = $v->translation_set_id;
	}

	if ( count( $translation_set_ids ) > 0 ) {
		$in_placeholders = implode( ',', array_fill( 0, count( $translation_set_ids ), '%d' ) );
		$sql             = $wpdb->prepare( "SELECT p.name, p.path, ts.id
                               FROM wp_" . SITE_ID_TRANSLATE . "_gp_projects p
                               INNER JOIN wp_" . SITE_ID_TRANSLATE . "_gp_translation_sets ts ON p.id = ts.project_id
                               WHERE ts.id IN ($in_placeholders);", $translation_set_ids );

		$projects = $wpdb->get_results( $sql );

		$html = "<style>
		.gp-content h3 {
			margin: 10px 0 3rem;
		}
		section.theme-boxshadow.bg-white.p-3 ul {
			padding-left: initial;
		}
		.list-group-item {
			position: relative;
			display: block;
			padding: 1.25rem 1.25rem;
			background-color: #fff;
			border: 1px solid #d9d9d9;
			color: #555;
			margin: 10px 0;
		}
        </style>";
		$html .= "<section class='theme-boxshadow bg-white p-3'><ul>";

		$html .= sprintf( '<h3>总计：<code>%d</code> 个待审核项目，当前 <code>%d</code> 页</h3>', $total, $paged );
		$html .= "<ul class='list-group mt-2'>";

		foreach ( $projects as $project ) {
			$html .= sprintf( '<li class="list-group-item">%s - <a href="/projects/%s/zh-cn/default/?filters[translated]=yes&filters[status]=waiting" target="_blank">%s</a></li>', $project->name, $project->path, $project->path );
		}
		$html .= "</ul>";
		$html .= "</section>";

		$pagination_args = array(
			'base'    => preg_replace( '/\?.*/', '', get_pagenum_link( 1 ) ) . '%_%',
			'total'   => $total_pages,
			'current' => $paged,
		);

		$paginate_links = paginate_links( $pagination_args );

		if ( $paginate_links ) {
			$html .= "<nav class='pagination'>";
			$html .= $paginate_links;
			$html .= "</nav>";
		}

	} else {
		$html = "<p>没有找到待审核的项目。</p>";
	}

	return $html;
}
