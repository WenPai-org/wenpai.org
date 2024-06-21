<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// define option_groups used to save/load related settings
function bsp_defined_option_groups() {
        return array (
                'bsp_style_settings_theme_support' => __( 'Theme Support', 'bbp-style-pack' ),
                'bsp_buddypress_support' => __( 'BuddyPress', 'bbp-style-pack' ),
                'bsp_style_settings_f' => __( 'Forums Index Styling', 'bbp-style-pack' ),
                'bsp_templates' => __( 'Forums Templates', 'bbp-style-pack' ),
                'bsp_forum_display' => __( 'Forums Display', 'bbp-style-pack' ),
                'bsp_forum_order' => __( 'Forums Order', 'bbp-style-pack' ),
                'bsp_style_settings_freshness' => __( 'Freshness Display', 'bbp-style-pack' ),
                'bsp_breadcrumb' => __( 'Breadcrumbs', 'bbp-style-pack' ),
                'bsp_style_settings_buttons' => __( 'Forum Buttons', 'bbp-style-pack' ),
                'bsp_login' => __( 'Login', 'bbp-style-pack' ),
                'bsp_login_fail' => __( 'Login Failures', 'bbp-style-pack' ),
                'bsp_roles' => __( 'Forum Roles', 'bbp-style-pack' ),
                'bsp_style_settings_email' => __( 'Subscription Emails', 'bbp-style-pack' ),
                'bsp_style_settings_sub_management' => __( 'Subscription Management', 'bbp-style-pack' ),
                'bsp_topic_order' => __( 'Topic Order', 'bbp-style-pack' ),
                'bsp_style_settings_ti' => __( 'Topic Index Styling', 'bbp-style-pack' ),
                'bsp_style_settings_topic_preview' => __( 'Topic Previews', 'bbp-style-pack' ),
                'bsp_style_settings_t' => __( 'Topic/Reply Display', 'bbp-style-pack' ),
                'bsp_settings_topic_count' => __( 'Topic Counts', 'bbp-style-pack' ),
                'bsp_style_settings_form' => __( 'Topic/Reply Form', 'bbp-style-pack' ),
                'bsp_profile' => __( 'Profile', 'bbp-style-pack' ),
                'bsp_style_settings_search' => __( 'Search Styling', 'bbp-style-pack' ),
                'bsp_style_settings_unread' => __( 'Unread Posts', 'bbp-style-pack' ),
                'bsp_style_settings_quote' => __( 'Quotes', 'bbp-style-pack' ),
                'bsp_style_settings_modtools' => __( 'Moderation', 'bbp-style-pack' ),
                'bsp_style_settings_la' => __( 'Latest Activity Widget Styling', 'bbp-style-pack' ),
                'bsp_css' => __( 'Custom CSS', 'bbp-style-pack' ),
                'bsp_css_location' => __( 'CSS Location', 'bbp-style-pack' ),
                'bsp_style_settings_translation' => __( 'Translations', 'bbp-style-pack' ),
                'bsp_settings_admin' => __( 'Dashboard Admin', 'bbp-style-pack' ),
                'bsp_style_settings_bugs' => __( 'bbPress Bug Fixes', 'bbp-style-pack' ),   
                'bsp_style_settings_block_widgets' => __( 'Block Widgets', 'bbp-style-pack' ), 
				'bsp_style_settings_column_display' => __( 'Column Display', 'bbp-style-pack' ), 
				'bsp_style_settings_topic_fields' => __( 'Topic Form Additional Fields', 'bbp-style-pack' ),  	 				
        );
}
