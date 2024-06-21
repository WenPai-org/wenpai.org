<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_settings_page() {
	global $bsp_theme_check ;
	
	?>
	<div class="wrap bsp-wrap">
		<div id="upb-wrap" class="upb-help">
                    
			<!-- main logo and plugin header -->
                        <table>	
                                <tr>
                                        <td>
                                                <?php echo bsp_logo('224', '224'); ?>
                                        </td>
                                        <td width="20">
                                        </td>
                                        <td>
                                                <h2>
                                                        <?php _e( 'RELAX !!!!', 'bbp-style-pack' ); ?>
                                                </h2>
                                                <p>
                                                        <?php _e( 'This plugin can look daunting, with lots of tabs and settings.  <b>But you do not need to set anything</b> - your bbpress forums will continue to work without changing anything here.', 'bbp-style-pack' ); ?>
                                                </p>
                                                <p>
                                                        <?php _e( 'Rather think of this plugin as the ability to change things as you want - so browse these tabs at your leisure to see what you can change, but don\'t think that you need to set something in every tab or work your way through this plugin. ', 'bbp-style-pack' ); ?>
                                                </p>
                                        </td>
                                </tr>
                        </table>

                        <?php
                        // handle settings saved message 
                        if ( ! isset( $_REQUEST['updated'] ) ) $_REQUEST['updated'] = false;
                        
                        if ( false !== $_REQUEST['updated'] ) : ?>
                                <div class="updated fade">
                                        <p>
                                                <strong>
                                                        <?php _e( 'Settings saved', 'bbp-style-pack'); ?>
                                                </strong>
                                        </p>
                                </div>
                        <?php endif; 
                        
                        // set active tab to false
                        $active_tab = false;
			
                        // is there a current valid tab selected? set as active_tab
                        if( isset( $_GET[ 'tab' ] ) ) $active_tab = esc_attr( $_GET[ 'tab' ] );
						
						
                        // is this a block or theme with support ? set as default active
                        elseif ( ! empty( $bsp_theme_check ) ) $active_tab = 'bsp_block_theme';
                        
                        // still no valid current tab set? is BuddyPress active?
                        elseif ( ( ! $active_tab ) && ( function_exists( 'bp_is_active' ) ) ) $active_tab = 'bsp_buddypress';
                        
                        // else, set forum index styling as default active
                        else $active_tab = 'forums_index_styling';
			?>
		
                        <!-- nav tabs -->			
                        <h2 class="nav-tab-wrapper">
                                <?php
                                foreach ( bsp_defined_tabs() as $slug => $title ) {

                                        // handle special case tabs first
                                        if ( $slug === 'bsp_block_theme' ) {
                                                // see if we have block or theme with support
                                                if ( ! empty( $bsp_theme_check ) )  {
                                                        echo '<a href="?page=bbp-style-pack&tab=' . $slug . '" class="nav-tab bsp-nav-tab' . ( $active_tab === $slug ? ' bsp-nav-tab-active' : '' ) . '">' . $title . '</a>';
                                                }
                                        } 
                                        elseif ( $slug === 'bsp_buddypress' ) {
                                                // see if we have buddypress active
                                                if ( function_exists( 'bp_is_active' ) )  {
                                                        echo '<a href="?page=bbp-style-pack&tab=' . $slug . '" class="nav-tab bsp-nav-tab' . ( $active_tab === $slug ? ' bsp-nav-tab-active' : '' ) . '">' . $title . '</a>';
                                                }
                                        } 
                                        else {
                                                // else, not a special case, so display the tab in the nav group
                                                echo '<a href="?page=bbp-style-pack&tab=' . $slug . '" class="nav-tab bsp-nav-tab' . ( $active_tab === $slug ? ' bsp-nav-tab-active' : '' ) . '">' . $title . '</a>';
                                        }  
                                }
                                ?>
                        </h2>
		
                        <!-- donate and special thanks info -->
                        <table class="form-table">
                                <tr>		
                                        <td>
                                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                                        <input type="hidden" name="cmd" value="_s-xclick" />
                                                        <input type="hidden" name="hosted_button_id" value="GEMT7XS7C8PS4" />
                                                        <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
                                                        <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
                                                </form>
                                        </td>
                                        <td>
                                                <?php _e('If you find this plugin useful, please consider donating just a few dollars to help me develop and maintain it. You support will be appreciated', 'bbp-style-pack'); ?>
                                        </td>
                                        <td>
                                        <?php _e('With thanks to Jacobo Feijóo for extensive testing !', 'bbp-style-pack'); ?>
                                        </td>
                                </tr>
                        </table>
	
                        <!-- active tab content -->
                        <?php
                        if ( $active_tab == 'forums_index_styling' ) bsp_style_settings_f();
                        elseif ( $active_tab == 'topic_index_styling' ) bsp_style_settings_ti();
                        elseif ( $active_tab == 'topic_display' ) bsp_style_settings_t();
                        elseif ( $active_tab == 'templates' )  bsp_forum_templates();
                        elseif ( $active_tab == 'forum_display' ) bsp_forum_display();
                        elseif ( $active_tab == 'forum_order' ) bsp_style_settings_forum_order();
                        elseif ( $active_tab == 'topic_form' ) bsp_style_settings_form();
						elseif ( $active_tab == 'topic_form_fields' ) bsp_settings_topic_fields();						
						elseif ( $active_tab == 'column_display' ) bsp_style_settings_column_display();
                        elseif ( $active_tab == 'freshness' ) bsp_style_settings_freshness();
                        elseif ( $active_tab == 'login' )  bsp_login_settings();
                        elseif ( $active_tab == 'login_fail' ) bsp_login_fail();
                        elseif ( $active_tab == 'profile' )  bsp_profile_settings();
                        elseif ( $active_tab == 'search' ) bsp_style_settings_search();
                        elseif ( $active_tab == 'breadcrumb' ) bsp_breadcrumb_settings();
                        elseif ( $active_tab == 'roles' )  bsp_roles();
                        elseif ( $active_tab == 'buttons' ) bsp_style_settings_buttons();
                        elseif ( $active_tab == 'topic_order' ) bsp_style_settings_topic_order();
                        elseif ( $active_tab == 'shortcodes' ) bsp_shortcodes_display();
                        elseif ( $active_tab == 'widgets' )  bsp_widgets();
                        elseif ( $active_tab == 'css' )  bsp_css_settings();
                        elseif ( $active_tab == 'help' ) bsp_help();
                        elseif ( $active_tab == 'plugins' ) bsp_plugins();
                        elseif ( $active_tab == 'translation' ) bsp_translation_settings();
                        elseif ( $active_tab == 'plugin_info' ) bsp_plugin_info();
                        elseif ( $active_tab == 'new' ) bsp_new();
                        elseif ( $active_tab == 'la_widget' ) bsp_style_settings_la();
                        elseif ( $active_tab == 'css_location' ) bsp_css_location();
                        elseif ( $active_tab == 'reset' ) bsp_style_settings_reset();
                        elseif ( $active_tab == 'export' ) bsp_style_settings_export();
                        elseif ( $active_tab == 'import' ) bsp_style_settings_import();
                        elseif ( $active_tab == 'not_working' ) bsp_not_working();
                        elseif ( $active_tab == 'unread' ) bsp_style_settings_unread();
                        elseif ( $active_tab == 'email' ) bsp_style_settings_email();
                        elseif ( $active_tab == 'bug_fixes' ) bsp_settings_bugs();
                        elseif ( $active_tab == 'topic_preview' ) bsp_style_settings_topic_preview();
                        elseif ( $active_tab == 'quote' ) bsp_style_settings_quote();
                        elseif ( $active_tab == 'modtools' ) bsp_style_settings_moderation();
                        elseif ( $active_tab == 'bsp_block_theme' ) bsp_style_settings_theme_support();
                        elseif ( $active_tab == 'bsp_buddypress' ) bsp_buddypress_support();
                        elseif ( $active_tab == 'sub_management' ) bsp_style_settings_subscriptions_management();
                        elseif ( $active_tab == 'topic_count' ) tc_settings();
                        elseif ( $active_tab == 'admin' ) bsp_settings_admin();
                        elseif ( $active_tab == 'block_widgets' ) bsp_style_settings_block_widgets();
						elseif ( $active_tab == 'column_display' ) bsp_style_settings_column_display();
                        ?>
                        
                        <a href="javascript:void(0);" id="back-to-top" class="button-back-to-top" title="<?php _e( 'Scroll To Top', 'bbp-style-pack' ); ?>">
                                <span class="to-top-dashicon dashicons dashicons-arrow-up-alt2"></span>
                        </a>
                        
                </div><!--end sf-wrap-->
	</div><!--end wrap-->
                        <?php

}	//end of function bsp_settings_page()


// register the plugin settings
function bsp_register_settings() {
        foreach ( bsp_defined_option_groups() as $slug => $title ) { 
                if ( $slug === 'bsp_style_settings_email' ) register_setting( $slug, $slug, 'bsp_test_email' ); // this uses the callback sanitization feature to add a callback function bsp_test_email to issue a text email if test has been selected
                else register_setting( $slug, $slug ); // regular option group with no callback function
        }	
}

//call register settings function
add_action( 'admin_init', 'bsp_register_settings' );


function bsp_settings_menu() {
	// add settings page
	add_submenu_page( 'options-general.php', __( 'bbp Style Pack', 'bbp-style-pack' ), __( 'bbp Style Pack', 'bbp-style-pack' ), 'manage_options', 'bbp-style-pack', 'bsp_settings_page' );
}

add_action( 'admin_menu', 'bsp_settings_menu' );