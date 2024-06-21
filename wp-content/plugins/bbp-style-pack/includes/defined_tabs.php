<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// define option_groups used to save/load related settings
function bsp_defined_tabs() {
        return array(
                'bsp_block_theme' => __( 'Theme Support', 'bbp-style-pack' ),
                'bsp_buddypress' => __( 'BuddyPress', 'bbp-style-pack' ),
                'forums_index_styling' => __( 'Forums Index Styling', 'bbp-style-pack' ),
                'templates' => __( 'Forum Templates', 'bbp-style-pack' ),
                'forum_display' => __( 'Forum Display', 'bbp-style-pack' ),
                'forum_order' => __( 'Forum Order', 'bbp-style-pack' ),
                'freshness' => __( 'Freshness Display', 'bbp-style-pack' ),
                'breadcrumb' => __( 'Breadcrumbs', 'bbp-style-pack' ),
                'buttons' => __( 'Forum Buttons', 'bbp-style-pack' ),
                'login' => __( 'Login', 'bbp-style-pack' ),
                'login_fail' => __( 'Login Failures', 'bbp-style-pack' ),
                'roles' => __( 'Forum Roles', 'bbp-style-pack' ),
                'email' => __( 'Subscription Emails', 'bbp-style-pack' ),
                'sub_management' => __( 'Subscription Management', 'bbp-style-pack' ),
                'topic_order' => __( 'Topic/Reply Order', 'bbp-style-pack' ),
                'topic_index_styling' => __( 'Topic Index Styling', 'bbp-style-pack' ),
                'topic_preview' => __( 'Topic Previews', 'bbp-style-pack' ),
                'topic_display' => __( 'Topic/Reply Display', 'bbp-style-pack' ),
                'topic_count' => __( 'Topic Counts', 'bbp-style-pack' ),
                'topic_form' => __( 'Topic/Reply Form', 'bbp-style-pack' ),
				'topic_form_fields' => __( 'Topic Form Additional Fields', 'bbp-style-pack' ),
				'column_display' => __( 'Column Display', 'bbp-style-pack' ),
				'profile' => __( 'Profile', 'bbp-style-pack' ),
                'search' => __( 'Search Styling', 'bbp-style-pack' ),
                'shortcodes' => __( 'Shortcodes', 'bbp-style-pack' ),
                'unread' => __( 'Unread Posts', 'bbp-style-pack' ),
                'quote' => __( 'Quotes', 'bbp-style-pack' ),
                'modtools' => __( 'Moderation', 'bbp-style-pack' ),
                'widgets' => __( 'Widgets', 'bbp-style-pack' ),
                'block_widgets' => __( 'Block Widgets', 'bbp-style-pack' ),
                'la_widget' => __( 'Latest Activity Widget Styling', 'bbp-style-pack' ),
                'css' => __( 'Custom CSS', 'bbp-style-pack' ),
                'css_location' => __( 'CSS/JS Location', 'bbp-style-pack' ),
                'translation' => __( 'Translations', 'bbp-style-pack' ),
                'admin' => __( 'Dashboard Admin', 'bbp-style-pack' ),
                'plugins' => __( 'Other bbPress Plugins', 'bbp-style-pack' ),
                'plugin_info' => __( 'Plugin Information', 'bbp-style-pack' ),
                'reset' => __( 'Reset Settings', 'bbp-style-pack' ),
                'export' => __( 'Export Plugin Settings', 'bbp-style-pack' ),
                'import' => __( 'Import Plugin Settings', 'bbp-style-pack' ),
                'help' => __( 'Help', 'bbp-style-pack' ),
                'not_working' => __( 'Not Working?', 'bbp-style-pack' ),
                'bug_fixes' => __( 'bbPress Bug Fixes', 'bbp-style-pack' ), 
                'new' => __( 'What\'s New?', 'bbp-style-pack' )         
        );
}
