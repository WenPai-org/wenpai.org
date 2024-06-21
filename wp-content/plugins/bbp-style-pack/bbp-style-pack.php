<?php

/*
Plugin Name: bbp style pack
Plugin URI: http://www.rewweb.co.uk/bbp-style-pack/
Description: This plugin adds styling and features to bbPress.
Version: 6.0.5
Author: Robin Wilson
Text Domain: bbp-style-pack
Domain Path: /languages
Author URI: http://www.rewweb.co.uk
License: GPL2
*/
/*  Copyright 2016-2024  Robin Wilson  (email : wilsonrobine@btinternet.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

   https://www.gnu.org/licenses/gpl-2.0.html

*/


/*******************************************
* global variables
*******************************************/

// load the plugin options
$bsp_style_settings_f = get_option( 'bsp_style_settings_f' );
$bsp_templates = get_option( 'bsp_templates' );
$bsp_forum_display = get_option( 'bsp_forum_display' );
$bsp_forum_order = get_option( 'bsp_forum_order' );
$bsp_style_settings_freshness = get_option( 'bsp_style_settings_freshness' );
$bsp_breadcrumb = get_option( 'bsp_breadcrumb' );
$bsp_style_settings_buttons = get_option( 'bsp_style_settings_buttons' );
$bsp_login = get_option( 'bsp_login' );
$bsp_login_fail = get_option( 'bsp_login_fail' );
$bsp_roles = get_option( 'bsp_roles' );
$bsp_style_settings_email = get_option( 'bsp_style_settings_email' );
$bsp_style_settings_sub_management = get_option( 'bsp_style_settings_sub_management' );
$bsp_topic_order = get_option( 'bsp_topic_order' );
$bsp_style_settings_ti = get_option( 'bsp_style_settings_ti' );
$bsp_style_settings_topic_preview = get_option( 'bsp_style_settings_topic_preview' );
$bsp_style_settings_t = get_option( 'bsp_style_settings_t' );
$bsp_settings_topic_count = get_option ('bsp_settings_topic_count');
$bsp_style_settings_form = get_option( 'bsp_style_settings_form' );
$bsp_profile = get_option( 'bsp_profile' );
$bsp_style_settings_search = get_option( 'bsp_style_settings_search' );
$bsp_style_settings_unread = get_option( 'bsp_style_settings_unread' );
$bsp_style_settings_quote = get_option( 'bsp_style_settings_quote' );
$bsp_style_settings_modtools = get_option( 'bsp_style_settings_modtools' );
$bsp_style_settings_la = get_option( 'bsp_style_settings_la' );
$bsp_css_location = get_option( 'bsp_css_location' );
$bsp_style_settings_translation = get_option( 'bsp_style_settings_translation' );
$bsp_settings_admin  = get_option ('bsp_settings_admin') ;
$bsp_style_settings_bugs = get_option( 'bsp_style_settings_bugs' );
$bsp_style_settings_block_widgets = get_option( 'bsp_style_settings_block_widgets' );
$bsp_css = get_option( 'bsp_css' );
$bsp_style_settings_theme_support = get_option( 'bsp_style_settings_theme_support' );
$bsp_buddypress_support = get_option( 'bsp_buddypress_support' );
$bsp_style_settings_column_display = get_option( 'bsp_style_settings_column_display' );
$bsp_style_settings_topic_fields = get_option( 'bsp_style_settings_topic_fields' );

//set bbpress version which is needed for function below and template order (around line 187)

if( ! function_exists('get_plugin_data') ){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$bbpress_plugin  = get_plugin_data(ABSPATH . 'wp-content/plugins/bbpress/bbpress.php') ;
$bsp_bbpress_full_version = $bbpress_plugin['Version'];
//and shortened 2.5/2.6 version used in other files
$bsp_bbpress_version = substr($bsp_bbpress_full_version, 0, 3) ;

if(!defined('BSP_PLUGIN_DIR'))
	define('BSP_PLUGIN_DIR', dirname(__FILE__));

if(!defined('BSP_PLUGIN_URL'))
	define('BSP_PLUGIN_URL', plugin_dir_url( __FILE__ ));

//now we add the class-bbp-admin class as this throws a dynamic property error as php 8.2 does not allow dynamic properties - we need to do this before bbpress loads
// and the bbopress extend buddypress loader.php for the same reason if buddypress is active
		//unless we are in buddyboss which has it's own admin.  is_plugin_active checks whether it is in wp_options active_plugins list, so tells us if buddyboss loader plugin is active - this doesn't guarantee that buddyboss is active, but unlikely not to be !
		// OR we are not on bbpress 2.6.9
		
		if (!is_plugin_active ('buddyboss-platform/bp-loader.php') && $bsp_bbpress_full_version == '2.6.9') {
			include(BSP_PLUGIN_DIR . '/bbpress-admin/class-bbp-admin.php');
		}


function bbp_style_pack_init() {
    	unload_textdomain( 'bbpress' );
        load_plugin_textdomain('bbp-style-pack', false, basename( dirname( __FILE__ ) ) . '/languages' );
    	load_plugin_textdomain('bbpress', false, 'bbpress/languages' );
        //load the plugin stuff
        bsp_load_plugin() ;
}

add_action('plugins_loaded', 'bbp_style_pack_init');


//  TEMPLATES - done now as needed when bbpress loads
//register the new templates location
if (!empty ($bsp_templates['template'] ) && ($bsp_templates['template'] == 1)) {
        add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_template1' );
		$alt_template = 1 ;
}

//add in loop-forums if we need to and not done by above alternate template
if (!empty ($bsp_style_settings_f['forum_icons']) && empty ($alt_template) ) { 
	add_action( 'bbp_register_theme_packages', 'bsp_register_loop_forums' );
}

//add in the forum search if set
if (!empty ($bsp_style_settings_search['SearchingActivate'] )) {
	add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_search_template' );
}

//add in the topic/reply_form if we need to
if (!empty ($bsp_style_settings_form['Remove_Edit_LogsActivate'] ) || !empty ($bsp_style_settings_form['Remove_Edit_ReasonActivate'] )  || !empty ($bsp_style_settings_form ['htmlActivate'])  ||  !empty ($bsp_style_settings_form ['nologinActivate']) || !empty ($bsp_style_settings_form['topic_tag_list'])) { 
	add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_form_topicandreply_template' );
}

//add in loop-topics if we need to
if (!empty ($bsp_style_settings_ti['topic_icons']) ) { 
	add_action( 'bbp_register_theme_packages', 'bsp_register_loop_topics' );
}



//add in the mod tools pending shortcode if modtools activated
if( !class_exists( 'bbPressModToolsPlugin') && !empty($bsp_style_settings_modtools['modtools_activate']) ) {
	add_action( 'bbp_register_theme_packages', 'bsp_register_modtools_template' );
}

//add in the feedback no topics is this is to be blank
if (!empty ($bsp_style_settings_ti['empty_forumActivate'] ) ) {
        add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_form_no_feedback_template' );
}

//add in the loop-single-topic if needed
if (!empty ($bsp_style_settings_ti['topic_title_link'])) {
	add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_topic_title' );
}

//add in the form-anonymous if needed - if any of the three form anon fields are completed
if (!empty ($bsp_style_settings_form['no_anon_nameActivate']) || !empty ($bsp_style_settings_form['no_anon_emailActivate']) || !empty ($bsp_style_settings_form['no_anon_websiteActivate'])) {
	add_action( 'bbp_register_theme_packages', 'bsp_register_plugin_form_anonymous' );
}

//get the template paths
function bsp_get_template1_path() {
	return BSP_PLUGIN_DIR . '/templates/templates1';
}

function bsp_get_search_template_path() {
	return BSP_PLUGIN_DIR . '/templates/searchform';
}

function bsp_get_form_topicandreply_template_path5() {
	return BSP_PLUGIN_DIR . '/templates/topicandreplyform5';
}

function bsp_get_form_topicandreply_template_path6() {
	return BSP_PLUGIN_DIR . '/templates/topicandreplyform6';
}

function bsp_get_form_no_feedback_template_path() {
	return BSP_PLUGIN_DIR . '/templates/feedbacknotopics';
}

function bsp_get_modtools_template_path() {
	return BSP_PLUGIN_DIR . '/templates/modtools';
}

function bsp_get_topic_title_template_path() {
	return BSP_PLUGIN_DIR . '/templates/topictitle';
}

function bsp_get_loop_topics_template_path() {
	return BSP_PLUGIN_DIR . '/templates/loop_topics';
}

function bsp_get_loop_forums_template_path() {
	return BSP_PLUGIN_DIR . '/templates/loop_forums';
}

function bsp_get_form_anonymous_template_path() {
	return BSP_PLUGIN_DIR . '/templates/form_anonymous';
}

//register the templates

//  TEMPLATES - fix 

/*Template Loading

The method is different (I think) between 2.5.12 and 2.6, it certainly seems to affect the load order, so we find out which version we are on, and allocate dependant on that.
We also allow an override, so admins can try different numbers
*/


/* This is the bit that determines the order they load
in 2.5 we have
		bbp_register_template_stack( 'get_template_directory',   12 );
		bbp_register_template_stack( 'bbp_get_theme_compat_dir', 14 );
		
so we load at 12 to get the templates to work, so not sure which loads first - theme or bsp - one to test when I get a moment

in 2.6 we have 
		bbp_register_template_stack( 'get_template_directory',   8 );
		bbp_register_template_stack( array( $bbp->theme_compat->theme, 'get_dir' ) );
		
which is different, and seems to cause issues if left at 12 as other templates have loaded before, so we alter to 6 as default
it actualy looks like something is using a default of 10, as setting to that works, but not 11. 
*/

//$bbpress version is set at the beginning on this file.

if ($bsp_bbpress_version == '2.5') $priority = 12; 
elseif ($bsp_bbpress_version == '2.6') $priority = 6 ;
//allow for case where neither is set and assume version 2.6.x
else $priority = 6 ;

//then allow custom setting
if (!empty($bsp_templates['template_priority'])  && is_numeric ($bsp_templates['template_priority']) ) $priority = $bsp_templates['template_priority'] ;

function bsp_register_plugin_template1() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_template1_path',  $priority);
}

function bsp_register_plugin_search_template() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_search_template_path', $priority );
}

function bsp_register_modtools_template() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_modtools_template_path', $priority );
}

function bsp_register_plugin_form_topicandreply_template() {
	global $priority ;
	//if version 2.5...
	if ($priority == 12) {
	bbp_register_template_stack( 'bsp_get_form_topicandreply_template_path5', $priority);
	}
	//if version 2.6...
	else {
	bbp_register_template_stack( 'bsp_get_form_topicandreply_template_path6', $priority);
	}
}

function bsp_register_loop_topics() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_loop_topics_template_path', $priority );
}

function bsp_register_loop_forums() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_loop_forums_template_path', $priority );
}

function bsp_register_plugin_form_no_feedback_template() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_form_no_feedback_template_path', $priority);
}

function bsp_register_plugin_topic_title() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_topic_title_template_path', $priority);
}

function bsp_register_plugin_form_anonymous() {
	global $priority ;
	bbp_register_template_stack( 'bsp_get_form_anonymous_template_path', $priority);
}

	
//add our version of wp_authenticate (pluggable wordpress function) if failed login tab activated - done now to ensure it loads
if( ! function_exists('wp_authenticate') && !empty($bsp_login_fail['activate_failed_login']) ) { 
        function wp_authenticate( $username, $password ) {
                $username = sanitize_user( $username );
                $password = trim( $password );

                /**
                 * Filters whether a set of user login credentials are valid.
                 *
                 * A WP_User object is returned if the credentials authenticate a user.
                 * WP_Error or null otherwise.
                 *
                 * @since 2.8.0
                 * @since 4.5.0 `$username` now accepts an email address.
                 *
                 * @param null|WP_User|WP_Error $user     WP_User if the user is authenticated.
                 *                                        WP_Error or null otherwise.
                 * @param string                $username Username or email address.
                 * @param string                $password User password
                 */
                $user = apply_filters( 'authenticate', null, $username, $password );

                if ( null == $user ) {
                        // TODO: What should the error message be? (Or would these even happen?)
                        // Only needed if all authentication handlers fail to return anything.
                        $user = new WP_Error( 'authentication_failed', __( '<strong>Error</strong>: Invalid username, email address or incorrect password.' ) );
                }
                //***function amended to take out this line and add blank array to ensure we pass back to bbpress on any error
                //$ignore_codes = array( 'empty_username', 'empty_password' );
                $ignore_codes = array () ;
                if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
                        $error = $user;

                        /**
                         * Fires after a user login has failed.
                         *
                         * @since 2.5.0
                         * @since 4.5.0 The value of `$username` can now be an email address.
                         * @since 5.4.0 The `$error` parameter was added.
                         *
                         * @param string   $username Username or email address.
                         * @param WP_Error $error    A WP_Error object with the authentication failure details.
                         */
                        do_action( 'wp_login_failed', $username, $error );
                }

                return $user;
        }
}



/*******************************************
* file includes 
*******************************************/

//only fires after all plugins loaded to ensure bbpress is loaded before we add bbpress functions and filters
function bsp_load_plugin() {
	
	if( class_exists( 'bbpress' ) ) {
		
		//add the blocks - note the register looks for a .asset file in the build folder with a prefix that matches both the .js and the .css file, so the css file enqueue below must match 

                include_once( 'generator/generator.php' );
                add_action( 'init', 'bsp_register_blocks' );
	
                add_action( 'wp_enqueue_scripts', 'bsp_enqueue_block_css' ) ;

                add_action( 'init', 'bsp_register_block_pattern_categories', 9 );


                //and add the patterns

                include(BSP_PLUGIN_DIR . '/blocks/patterns.php');

                add_action( 'init', 'bsp_register_forum_patterns' );
				
				
/*******************************************
* Theme Checks
*******************************************/
                global $bsp_theme_check ;

                // CHECK IF BLOCK THEME
                // get current theme dir
                $theme_dir = get_template_directory();
                //Detect if FSE (what WordPress calls block themes) theme or traditional - FSE Block themes require a theme.json file.
                if ( file_exists( $theme_dir . '/templates/index.html') ) {
                        $bsp_theme_check = 'block_theme' ;
                }
                //check for specific themes
                $theme_name = wp_get_theme() ;
				$parent = wp_get_theme()->parent();

                if ($theme_name == 'Astra' || $parent ==  'Astra') {
                        $version = $theme_name->get('Version') ;
                        //older version don't have this issue, and fixed in later, so only...
                        if ($version == '4.0.2' || $version == '4.1.0' || $version == '4.1.1' || $version == '4.1.2' || $version == '4.1.3' || $version == '4.1.4' || $version == '4.1.5' || $version == '4.1.6' )  {
                                $bsp_theme_check = 'astra' ;
                        }
                }

                if ($theme_name == 'Divi' || $parent ==  'Divi' ) $bsp_theme_check = 'divi' ;
                if ($theme_name == 'Kadence' || $parent ==  'Kadence' ) $bsp_theme_check = 'kadence' ;
				if ($theme_name == 'Hello Elementor' || $parent ==  'Hello Elementor') $bsp_theme_check = 'hello-elementor' ;

                if (!empty ($bsp_theme_check)) include(BSP_PLUGIN_DIR . '/includes/functions_theme_support.php');

/*******************************************
* front-end and admin files
*******************************************/		            
               
                global $bsp_style_settings_sub_management ;
                if (!function_exists( 'forums_toolkit_page') && !empty($bsp_style_settings_sub_management['subscriptions_management_activate']))
                       include(BSP_PLUGIN_DIR . '/includes/subscriptions_management.php');

                global $bsp_style_settings_unread ;
                //only load functions_unread if activated
                if (!empty($bsp_style_settings_unread['unread_activate'])) 
                        include(BSP_PLUGIN_DIR . '/includes/functions_unread.php');

                //only load functions_quote if activated
                global $bsp_style_settings_quote ;
                if (!empty($bsp_style_settings_quote['quote_activate'])) 
                        include(BSP_PLUGIN_DIR . '/includes/functions_quote.php');
					
				//only load functions_topic_fields if activated
                global $bsp_style_settings_topic_fields ;
                if (!empty($bsp_style_settings_topic_fields['number_of_fields'])) 
                        include(BSP_PLUGIN_DIR . '/includes/functions_topic_fields.php');

                //load moderation tools if activated
                //don't load if mod tools plugin already loaded
                global $bsp_style_settings_modtools ;
                if( !class_exists( 'bbPressModToolsPlugin') && !empty($bsp_style_settings_modtools['modtools_activate']) )  {
                        //load moderation tools	
                        require_once( BSP_PLUGIN_DIR. '/modtools/bbpress-modtools.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/settings.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/admin.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/bbpress.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/moderation.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/report.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/users.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/scripts.php' );
                        require_once( BSP_PLUGIN_DIR . '/modtools/notifications.php' );
                        //add shortcode function
                        include(BSP_PLUGIN_DIR . '/includes/functions_modtools.php');
                }
            
                include(BSP_PLUGIN_DIR . '/includes/functions.php');
                include(BSP_PLUGIN_DIR . '/includes/functions_email.php');
				include(BSP_PLUGIN_DIR . '/includes/forum_image_metabox.php');
                include(BSP_PLUGIN_DIR . '/includes/generate_css.php');
                include(BSP_PLUGIN_DIR . '/includes/widgets.php');
                include(BSP_PLUGIN_DIR . '/includes/functions_bugs.php');
            
                // admin-only files
                if ( is_admin() ) {
                        include(BSP_PLUGIN_DIR . '/includes/defined_option_groups.php');
                        include(BSP_PLUGIN_DIR . '/includes/defined_tabs.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_forums_index.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topics_index.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topic_reply_display.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_forum_display.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_forum_roles.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_custom_css.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topic_order.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_forum_order.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_freshness_display.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topic_reply_form.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_css_location.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_login.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_login_fail.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_search.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_forum_templates.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_breadcrumbs.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_buttons.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_profile.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_shortcodes.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_latest_activity_widget_styling.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_widgets.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_reset.php');
                        include(BSP_PLUGIN_DIR . '/includes/not_working.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_unread.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_export.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_import.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_email.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_quote.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_moderation.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_theme_support.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_buddypress_support.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_translation.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_bugs.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_subscriptions_management.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topic_count.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_admin.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_topic_preview.php');
                        include(BSP_PLUGIN_DIR . '/includes/help.php');
                        include(BSP_PLUGIN_DIR . '/includes/plugins.php');
                        include(BSP_PLUGIN_DIR . '/includes/plugin_info.php');
                        include(BSP_PLUGIN_DIR . '/includes/whats_new.php');
                        include(BSP_PLUGIN_DIR . '/includes/logo.php');
                        include(BSP_PLUGIN_DIR . '/includes/functions_admin.php');
                        include(BSP_PLUGIN_DIR . '/includes/settings_block_widgets.php');
						include(BSP_PLUGIN_DIR . '/includes/settings_column_display.php');
						include(BSP_PLUGIN_DIR . '/includes/settings_topic_fields.php');
			     }
                
                // frontend-only files
                if ( ! is_admin() ) {
                        include(BSP_PLUGIN_DIR . '/includes/buddypress.php');
                        include(BSP_PLUGIN_DIR . '/includes/functions_topic_count.php');
                        include(BSP_PLUGIN_DIR . '/includes/shortcodes.php');
                       
                }
    

		/*
		 * Handle upgrade actions
		 */
		if ( ! function_exists( 'get_plugin_data' ) ) {
                        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                }
			
		$new_version = get_plugin_data( __FILE__, false, false )['Version'];
                
		if ( ! defined( 'BSP_VERSION_KEY' ) )
                        define( 'BSP_VERSION_KEY', 'bsp_version' );

		if ( ! defined( 'BSP_VERSION_NUM' ) )
                        define( 'BSP_VERSION_NUM', $new_version );
					
		$curr_version = get_option( BSP_VERSION_KEY, false );

		if ($new_version != $curr_version)  {
                        
			// first set whether network activated or not
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
                        
                        $bsp_name = plugin_basename( __FILE__ );
                        
			$network_wide = ( is_multisite() && is_plugin_active_for_network( $bsp_name ) ) ? true : false; 
			 
			 // do the activation actions
			bsp_plugin_update( $network_wide );
		}
		
        } // end of if bbpress class exists - main plugin loading
        
        
        /*
         * Add plugin page links whether bbPress is active or not
         */
        // plugin title action links 
        add_filter( 'plugin_action_links', 'bsp_modify_plugin_action_links', 10, 2 );

        // plugin description links 
        add_filter( 'plugin_row_meta', 'bsp_modify_plugin_description_links', 10, 2 );
        
} //end of bsp_load_plugin


/*
 * Handle update actions
 */
function bsp_plugin_update( $network_wide ) { 
    
        if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        if ( is_multisite() ) {
        /* multisite install */

                $site_ids = get_sites( array( 'fields' => 'ids' ) );
                
                $bsp_name = plugin_basename( __FILE__ );
                
                foreach( $site_ids as $site_id ) {
                        switch_to_blog( $site_id );
                        // network-activated, or active for current site?
                        if ( $network_wide || in_array( $bsp_name, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                                bsp_plugin_update_actions();
                        }
                        restore_current_blog();
                }

        } else {
        /* single site install */
                bsp_plugin_update_actions();
        } // end plugin update
}


// repeat actions on plugin activation/upgrade that apply to single site and per-site within a multisite sintall
function bsp_plugin_update_actions() {
        $new_version = get_plugin_data( __FILE__, false, false )['Version'];
    
        if ( ! defined( 'BSP_VERSION_KEY' ) )
                define( 'BSP_VERSION_KEY', 'bsp_version' );

        if ( ! defined( 'BSP_VERSION_NUM' ) )
                define( 'BSP_VERSION_NUM', $new_version );
        
        //and update version whether new installation or update
        update_option( BSP_VERSION_KEY, BSP_VERSION_NUM );

        // do any necessary field/value conversions
        bsp_convert_values();

        /* 
        * Regenerate CSS/JS files
        * Pro-actively regenerate files to replace CSS/JS files removed during the upgrade process 
        */
        require_once( plugin_dir_path( __FILE__ ) . 'includes/generate_css.php' );
        copy_to_custom_dirs();
        generate_style_css();
        generate_quote_style_css();
        generate_delete_js();
        bsp_clear_cache();
}


/*
 * Convert Values
 * Some setting values have changed with the upcoming BETA release
 * This function handles auto-conversions between releases
 * Also includes backward compatibility conversions for when option groups were tied to specific tabs
 */
function bsp_convert_values( $option_group = false, $field_setting = false ) {
    
        /*
         * FIRST
         * Backward Compatibility with previous fields/values tied to specific tabs
         */
    
    
        //amend for searching activate being moved from forum index styling to search styling tab
        // bsp ?.?.?
        $options_f= get_option( 'bsp_style_settings_f', array() );
        if ( is_array( $options_f ) ) {
                if ( ! empty( $options_f["SearchingActivate"] ) ) {
                        //update bsp_style_settings_search
                        $options = get_option( 'bsp_style_settings_search', array() );
                        $options['SearchingActivate'] = '1';
                        $options['SearchingSearching'] = $options_f["SearchingSearching"];
                        $options['SearchingSpinner'] = $options_f["SearchingSpinner"];
                        //Update entire array
                        update_option( 'bsp_style_settings_search', $options );
                        //update bsp_style_settings_f
                        unset ( $options_f['SearchingActivate'] );
                        unset ( $options_f['SearchingSearching'] );
                        unset ( $options_f['SearchingSpinner'] );
                        //Update entire array
                        update_option( 'bsp_style_settings_f', $options_f );
                }
        }

        
        //update for bsp_login menus
        // bsp 4.4.8+
        $bsp_login = get_option( 'bsp_login', array() );
        if ( is_array( $bsp_login ) ) {
                if ( empty( $bsp_login['update448'] ) ) {

                        $options = get_option( 'bsp_login' );
                        $options['update448'] = '1' ;
                        $menu_locations = get_nav_menu_locations();
                        $menus = get_terms( 'nav_menu' );
                        $field_prefixes = array( 'register', 'login', 'profile' );

                        //run the update once per field
                        foreach ( $field_prefixes as $field_prefix ) {
                                if ( ! empty( $bsp_login[ ( $field_prefix === 'login' ? 'add_login' : ( $field_prefix === 'profile' ? 'edit_profile' : $field_prefix ) ) ] ) && ! empty( $menus ) ) {
                                        foreach( $menus as $menu ){
                                                if( ! empty( $bsp_login[ ( $field_prefix == 'login' ? 'only_primary' : $field_prefix . '_only_primary' ) ] ) ) {
                                                        if ( ! empty( $menu_locations ) && isset( $menu_locations['primary'] ) ) {
                                                                if ( $menu_locations['primary'] == $menu->term_id ) {
                                                                        $name =  $field_prefix . '_' . $menu->name;
                                                                        $options[$name] = '1';
                                                                        unset( $options[ ( $field_prefix == 'login' ? 'only_primary' : $field_prefix . '_only_primary' ) ] );
                                                                }
                                                        }
                                                }
                                                else {
                                                        $name = $field_prefix . '_' . $menu->name ;
                                                        $options[$name] = '1' ;
                                                }
                                        }
                                }
                        }
                        update_option('bsp_login', $options);
                }
        }

        
        //amend settings topic/reply form to allow for different topic/reply text
        // bsp 4.1.8+
        $options = get_option( 'bsp_style_settings_form', array() );
        if ( is_array( $options ) ) {
                if ( empty( $options['update418'] ) ) {
                        if ( ! empty( $options['topic_rules_text'] ) && ! empty( $options['topic_posting_rulesactivate_for_replies'] ) ) {
                                if ( empty( $options['reply_rules_text'] ) ) $options['reply_rules_text'] = $options['topic_rules_text'];
                                //and set it to stop running again
                                $options['update418'] = '1' ;
                                update_option('bsp_style_settings_form', $options);
                        }
                }
        }
        
        
        // amend for topic subscribe button separator field being moved from Forum Butons tab (bsp_style_settings_buttons) to Topic/Reply Display (bsp_style_settings_t)
        // bsp 5.6.0+
        $options = get_option( 'bsp_style_settings_buttons', array() );
        $new_options = get_option( 'bsp_style_settings_t', array() );
        if ( is_array( $options ) ) {
                // activated
                if ( array_key_exists( 'activate_topic_subscribe_button_prefix', $options ) ) {
                        if ( ! empty( $options['activate_topic_subscribe_button_prefix'] ) ) { 
                                // add the value to the new tab/option group and save
                                $new_options['activate_topic_subscribe_button_prefix'] = $options['activate_topic_subscribe_button_prefix'];
                                update_option( 'bsp_style_settings_t', $new_options );
                                //remove the value from the old tab/option group and save
                                unset ( $options['activate_topic_subscribe_button_prefix'] );
                                update_option( 'bsp_style_settings_buttons', $options );
                        }
                }
                // actual prefix
                if ( array_key_exists( 'topic_subscribe_button_prefix', $options ) ) {
                        if ( ! empty( $options['topic_subscribe_button_prefix'] ) ) { 
                                // add the value to the new tab/option group and save
                                $new_options['topic_subscribe_button_prefix'] = $options['topic_subscribe_button_prefix'];
                                update_option( 'bsp_style_settings_t', $new_options );
                                //remove the value from the old tab/option group and save
                                unset ( $options['topic_subscribe_button_prefix'] );
                                update_option( 'bsp_style_settings_buttons', $options );
                        }
                }
        }

        
        /*
         * SECOND
         * Convert any old values where the field type and/or value type has changed
         * Coming in a future release
         */
}


// plugin title action links 
function bsp_modify_plugin_action_links( $links, $file ) {

        // Return normal links if not bbPress style
        // revised to make sure links added to the Style Pack plugin regardless of directory
        if ( strpos( $file, 'bbp-style-pack.php' ) !== false ) {

                // New links to merge into existing links
                $new_links = array();

                // Settings page and what's new page
                if ( current_user_can( 'manage_options' ) ) {
                        $new_links['settings'] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'bbp-style-pack'   ), admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings', 'bbp-style-pack' ) . '</a>';
                        $new_links['about']    = '<a href="' . esc_url( add_query_arg( array( 'page' => 'bbp-style-pack', 'tab' => 'new' ), admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'What\'s New?',    'bbp-style-pack' ) . '</a>';
                }

                // Add a few links to the existing links array
                $links = array_merge( $links, $new_links );
                
        }
        return $links;
}


// plugin description links 
function bsp_modify_plugin_description_links( $links, $file ) {

        $slug = 'bbp-style-pack.php';

        // Return normal links if not bbPress style
        // revised to make sure links added to the Style Pack plugin regardless of directory
        if ( strpos( $file, $slug ) !== false ) {

                // New links to merge into existing links
                $new_links = array();

                // Support link
                // if inactive, or user can't admin, show WP support forum link
                $new_links['support'] = '<a href="' . esc_url( 'https://wordpress.org/support/plugin/bbp-style-pack/' ) . '" target="_blank">' . esc_html__( 'Official Support', 'bbp-style-pack' ) . '</a>';

                // Donate Link
                $new_links['donate']    = '<a href="' . esc_url( 'http://www.rewweb.co.uk/donate' ) . '" target="_blank">' . esc_html__( 'Donate',    'bbp-style-pack' ) . '</a>';

                // Rate/Review Link
                $new_links['rate']    = '<a href="' . esc_url( 'https://wordpress.org/support/plugin/bbp-style-pack/reviews/#new-post' ) . '" target="_blank">' . esc_html__( 'Rate Us',    'bbp-style-pack' ) . ' <span style="font-size:14px;color:gold;">&starf;&starf;&starf;&starf;&starf;</span></a>';

                // Add a few links to the existing links array
                $links = array_merge( $links, $new_links );
                
        }
        return $links;
        
}


// register blocks
function bsp_register_blocks () {
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-latest-activity-widget-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/login-widget-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-single-topic-information-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-single-forum-information-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-forums-list-widget-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/topic-views-list-widget-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-statistics-widget-block.json' );
        register_block_type( BSP_PLUGIN_DIR . '/blocks/bsp-search-widget-block.json' );
}
                      
                
//add pattern categories
function bsp_register_block_pattern_categories() {

        $block_pattern_categories = array(
            'bsp-forums' => array( 'label' => __( 'bbp style pack forum patterns', 'bbp-style-pack' ) ),
        );


        /**
        * Filters the theme block pattern categories.
        */
        $block_pattern_categories = apply_filters( 'bbp-style-pack_pattern_categories', $block_pattern_categories );

        foreach ( $block_pattern_categories as $name => $properties ) {
                if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
                        register_block_pattern_category( $name, $properties );
                }
        }
}
