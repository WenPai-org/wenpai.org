<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION

add_action( 'generate_after_header', function() {
    if ( function_exists('yoast_breadcrumb') ) {
        yoast_breadcrumb( '<div class="grid-container grid-parent"><p id="breadcrumbs">','</p></div>' );
    }
} );


// load_dashicons

add_action( 'wp_enqueue_scripts', 'dashicons_style_front_end' );
function dashicons_style_front_end() {
  wp_enqueue_style( 'dashicons' );
}


// Disable Google Fonts, fonts.googleapis.com slow down site
class Disable_Google_Fonts {
        public function __construct() {
                add_filter( 'gettext_with_context', array( $this, 'disable_open_sans' ), 888, 4 );
	}
	public function disable_open_sans( $translations, $text, $context, $domain ) {
		if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
		        $translations = 'off';
		}
		return $translations;
	}
}
$disable_google_fonts = new Disable_Google_Fonts;

function remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}
add_action( 'init', 'remove_open_sans' );

// 后台使用"PingFang SC"  Microsoft YaHei 字体
function Fanly_admin_lettering() {
	echo '<style type="text/css">
* { font-family: "PingFang SC",Microsoft YaHei,WooCommerce,dashicons;-webkit-font-smoothing: antialiased; }
#activity-widget #the-comment-list .avatar { max-width: 50px; max-height: 50px; }
</style>';
}
add_action( 'admin_head', 'Fanly_admin_lettering' );


// 添加文章浏览数

add_action( 'generate_after_entry_title', 'display_post_views', 15 );

function display_post_views() {
    if ( function_exists( 'the_views' ) ) {
        $post_views = do_shortcode( '[views]' );
        echo '<span class="post-views">' . $post_views . '</span>';
    }
}

add_action( 'generate_after_header', function() {
    if ( function_exists('yoast_breadcrumb') ) {
        yoast_breadcrumb( '<div class="grid-container grid-parent"><p id="breadcrumbs">','</p></div>' );
    }
} );


// remove header info
remove_action( 'wp_head', 'feed_links', 2 ); //移除feed
remove_action( 'wp_head', 'feed_links_extra', 3 ); //移除feed
remove_action( 'wp_head', 'wp_generator' ); //移除WordPress版本
