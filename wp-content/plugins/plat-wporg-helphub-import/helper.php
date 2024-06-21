<?php

namespace Platform\Translate\WPOrgHelpHubImport;

use DiDom\Document;
use Exception;

/**
 * 压缩 HTML
 *
 * @param $string
 *
 * @return string
 */
function compress_html( $string ): string {
	$string  = str_replace( "\r\n", '', $string ); //清除换行符
	$string  = str_replace( "\n", '', $string ); //清除换行符
	$string  = str_replace( "\t", '', $string ); //清除制表符
	$pattern = array(
		"/> *([^ ]*) *</", //去掉注释标记
		"/[\s]+/", //多个空白字符 -- 置为1个空格
		"/<!--[\\w\\W\r\\n]*?-->/", //<!-- -->注释之间的空白字符 -- 置空
	);
	$replace = array(
		">\\1<",
		" ",
		"",
	);

	return preg_replace( $pattern, $replace, $string );
}

/**
 * 为 HTML 文本切片
 *
 * 切片的依据是 标题、li标签、p标签，他们中包含的元素都会成为一个单独的片
 *
 * @param string $html
 *
 * @return array
 * @throws Exception
 */
function html_split( string $html ): array {
	$section_strings = array();

	$dom = new Document( $html );

	$body = $dom->find( 'body' );

	foreach ( $body[0]->children() as $node ) {
		// 有一些用作列表的 HTML 标签，对于它们，需要取子元素
		$list_tag = array(
			'ol',
			'ul',
		);
		if ( ! empty( $node->getNode()->tagName ) && in_array( $node->getNode()->tagName, $list_tag ) ) {
			foreach ( $node->children() as $child_node ) {
				if ( ! empty( $child_node->html() ) ) {
					$section_strings[] = $child_node->html();
				}
			}
		} else {
			if ( ! empty( $node->html() ) ) {
				$section_strings[] = $node->html();
			}
		}
	}

	// 进行一次预处理，去掉所有字符串最外侧的 HTML 标签
	foreach ( $section_strings as &$section_string ) {
		if ( preg_match( '|^<\w+[^>]*>([\s\S]*?)</\w+>$|', $section_string, $matches ) ) {
			if ( ! empty( $matches[1] ) ) {
				$section_string = compress_html( $matches[1] );
			}
		}
	}
	unset( $section_string );

	return $section_strings;
}

/**
 * 对所有来自 w.org 的字符串数据进行预处理（主要是替换各种关键字）
 *
 * @param string $str
 *
 * @return string
 */
function prepare_w_org_string( string $str ): string {
	$items = array(
		'translate.wordpress.org' => 'translate.wenpai.org',
		'developer.wordpress.org' => 'developer.wenpai.org',
		//'wordpress.org'           => 'wenpai.org',
	);

	$search  = array();
	$replace = array();

	foreach ( $items as $k => $v ) {
		$search[]  = $k;
		$replace[] = $v;
	}

	return str_replace( $search, $replace, $str );
}
