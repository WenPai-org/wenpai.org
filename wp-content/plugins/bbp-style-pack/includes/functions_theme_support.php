<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $bsp_theme_check ;
global $bsp_style_settings_theme_support ;	


if (!empty ($bsp_style_settings_theme_support['fse'])  && $bsp_theme_check == 'block_theme') {
add_action( 'after_setup_theme', 'bsp_fse_support' );
}

if (!empty ($bsp_style_settings_theme_support['astra'])  && $bsp_theme_check == 'astra') {
add_filter ('astra_single_layout_one_banner_visibility', 'bsp_astra_bbpress_fix', 50) ;
}

if (!empty ($bsp_style_settings_theme_support['kadence'])  && $bsp_theme_check == 'kadence') {
	add_action( 'wp_enqueue_scripts', 'bsp_kadence_bbpress_fix', 110 );
}

if (!empty ($bsp_style_settings_theme_support['hello_elementor'])  && $bsp_theme_check == 'hello-elementor') {
	add_filter ('bbp_template_include_theme_compat' , 'bsp_hello_bbpress_fix' ) ;
	add_filter( 'bbp_register_topic_post_type', 'bsp_hello_bbpress_fix_topic_header') ;
	add_filter( 'bbp_register_reply_post_type', 'bsp_hello_bbpress_fix_reply_header') ;
}


/*******   FSE FUNCTIONS   */
// main function for handling which theme file needs to be included
function bsp_fse_support() {
	// include wp-includes/template-canvas.php only if needed
			if ( !basename( get_page_template() ) == 'template-canvas.php' ) {
				add_filter( 'template_include', 'bsp_fse_bbpress_template' );
			}

			// include either the BSP template, or default BBPress template
			add_filter ( 'bbp_template_include_theme_compat', 'bsp_fse_theme_compat' );

		}
	

// function to include the wp-includes/template-canvas.php file if needed
function bsp_fse_bbpress_template( $template ) {
	if ( !is_bbpress() ) {
		$template = ABSPATH . WPINC . '/template-canvas.php';
		}
	return $template;
}

// function to include the bbpress forum template file if needed
function bsp_fse_theme_compat( $template ) {
	if ( is_bbpress() ) {
		$template = BSP_PLUGIN_DIR . '/templates/bbpress.php';
		}
	return apply_filters ('bsp_fse_theme_compat' , $template) ;

}

/*******   ASTRA FUNCTIONS   */

function bsp_astra_bbpress_fix ($value) {
		if (bbp_is_single_user()) return false ;
		if (bbp_is_search()) return false ;
		if (bbp_is_topic_tag()) return false ;
		if (bbp_is_single_view()) return false ;
return $value ;
}


/*******  DIVI FUNCTIONS   */
//Divi has no functions, but the theme support settings give details on how to get working with bbpress

/*******   KADENCE FUNCTIONS   */
function bsp_kadence_bbpress_fix () {
$handle = 'kadence-bbpress' ;
wp_dequeue_style($handle);
}

/*******   HELLO ELEMENTOR FUNCTIONS   */


function bsp_hello_bbpress_fix ($template) {
	$template = BSP_PLUGIN_DIR . '/templates/hello_elementor/bbpress.php' ;
return $template ;
}

//this is added to fix this support thread   https://bbpress.org/forums/topic/bbpress-elementor-header/
function bsp_hello_bbpress_fix_topic_header ($topic_post_type) {

$topic_post_type = array(
				'labels'              => bbp_get_topic_post_type_labels(),
				'rewrite'             => bbp_get_topic_post_type_rewrite(),
				'supports'            => bbp_get_topic_post_type_supports(),
				'description'         => esc_html__( 'bbPress Topics', 'bbpress' ),
				'capabilities'        => bbp_get_topic_caps(),
				'capability_type'     => array( 'topic', 'topics' ),
				'menu_position'       => 555555,
				'has_archive'         => ( 'forums' === bbp_show_on_root() ) ? bbp_get_topic_archive_slug() : false,
				'exclude_from_search' => true,
				'show_in_nav_menus'   => true,
				'public'              => true,
				'show_ui'             => current_user_can( 'bbp_topics_admin' ),
				'can_export'          => true,
				'hierarchical'        => false,
				'query_var'           => true,
				'menu_icon'           => '',
				'source'              => 'bbpress',
			)  ;
			
return $topic_post_type ;
}

function bsp_hello_bbpress_fix_reply_header ($reply_post_type) {

$reply_post_type = array(
				'labels'              => bbp_get_reply_post_type_labels(),
				'rewrite'             => bbp_get_reply_post_type_rewrite(),
				'supports'            => bbp_get_reply_post_type_supports(),
				'description'         => esc_html__( 'bbPress Replies', 'bbpress' ),
				'capabilities'        => bbp_get_reply_caps(),
				'capability_type'     => array( 'reply', 'replies' ),
				'menu_position'       => 555555,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'show_in_nav_menus'   => true,
				'public'              => true,
				'show_ui'             => current_user_can( 'bbp_replies_admin' ),
				'can_export'          => true,
				'hierarchical'        => false,
				'query_var'           => true,
				'menu_icon'           => '',
				'source'              => 'bbpress',
			)  ;
			
return $reply_post_type ;
}


