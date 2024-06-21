<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//moderation settings page

function bsp_style_settings_moderation () {
	global $bsp_style_settings_modtools ;
	?> 
        <table class="form-table">
                <tr valign="top">
                        <th colspan="2">
                                <h3>
                                        <?php _e ('Moderation' , 'bbp-style-pack' ) ; ?>
                                </h3>
                </tr>

                <tr>
                        <td>
                                <p><i><b>
                                        <?php _e('This section adds the bbpress Moderation tools plugin which was withdrawn from the worpdress plugins directory.', 'bbp-style-pack'); ?>
                                </b></i></p>

                        </td>
                </tr>

        </table>

        <?php
        if (class_exists( 'bbPressModToolsPlugin')) { ?>

                <table>
                        <tr>
                                <td>
                                        <p>
                                                <?php _e('You already have the bbpress Moderation tools plugin activate, so do not need this section', 'bbp-style-pack' ); ?>
                                        </p>
                                </td>
                        </tr>

                </table>

        <?php
        }
        else {
        ?>
                <hr>


                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings_moderation', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_modtools' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>
                        <table class="form-table">
                        <!-- checkbox to activate  -->
                                <tr valign="top">  
                                        <th>
                                                1. <?php _e('Activate Moderation', 'bbp-style-pack'); ?>
                                        </th>

                                        <td>
                                                <?php 
                                                $item = (!empty( $bsp_style_settings_modtools['modtools_activate'] ) ?  $bsp_style_settings_modtools['modtools_activate'] : '');
                                                echo '<input name="bsp_style_settings_modtools[modtools_activate]" id="bsp_style_settings_modtools[modtools_activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
                                                ?>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan='2'>
                                                <p>
                                                        <?php _e('<strong>NOTE- </strong>Once activated the settings are in Dashboard>settings>forums - click <a href="' . site_url() . '/wp-admin/options-general.php?page=bbpress/#bsp-moderation" > here</a>', 'bbp-style-pack'); ?>
                                                </p>
                                        </td>
                                </tr>
                        </table>
                    
                        <!-- save the options -->
                        <p class="submit">
                                <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
                        </p>
                    
                </form>

                <p> <b> <?php _e( 'Additional Shortcode', 'bbp-style-pack' ); ?> </b></p>

                <p><b> <tt> [bsp-moderation-pending] </tt></b> </p>
                <?php _e('If you have activated the moderation tab, then this shortcode for keymasters and moderators will display all the pending topics and replies in one place, letting you approve, edit, delete, spam and do other administration tasks on the front end.  Add this shortcode to a page or post to use.', 'bbp-style-pack' ) ; ?>
                </p>

        <?php
	}
        ?>	

        <table>
                <tr>
                        <td>
                                <?php _e('Moderation Tools gives you more control over your forums by adding rules that can automatically detect spam from users and bots. Out of the box bbPress has limited moderation tools which means running a forum can be a constant battle to stop spam.', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <th>
                                <?php _e('When you activate this setting you will find a new set of rules added to the bbPress Forums settings page, found under Settings > Forums.', 'bbp-style-pack'); ?>
                        </th>
                </tr>
        </table>

        <table>


                <tr>

                        <td colspan="2">
                                <?php _e('Spam Detection Rules', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td colspan="2">
                                <?php _e('Use one or more rules to automatically hold posts for moderation, including:', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Anonymous/Guest posting', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Unapproved users posting', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Unapproved users posting links', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Unapproved users posting below the English character threshold (this is a percentage that can be tweaked)', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* All posts below the English character threshold', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* All posts (lockdown)', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Flag individual users for moderation', 'bbp-style-pack'); ?>
                        </td>
                </tr>
	</table>
                
	<table>
                <tr>


                        <td>
                                <?php _e('Email notifications', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>		
                        <td>
                        </td>
                        <td>
                                <?php _e('* When posts are held for moderation', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* When a user reports a post', 'bbp-style-pack'); ?>
                        </td>
                </tr>


                <tr>
                        <td colspan="2">
                                <?php _e('Send email notifications to any combination of the following:', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Keymasters', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Moderators', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Specified emails', 'bbp-style-pack'); ?>
                        </td>
                </tr>


                <tr>
                        <td>
                                <?php _e('Front end controls', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td colspan="2">
                                <?php _e('*Approval and unapproval of posts and blocking users is handled on the front end by showing pending posts to the post author, moderators and administrators.', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                                <?php _e('More Features', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td colspan="2">
                                <?php _e('* Redirect blocked users to a custom page instead of the default 404', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Adds a Senior Moderator role', 'bbp-style-pack'); ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php _e('* Support for single forum moderators', 'bbp-style-pack'); ?>
                        </td>
                </tr>

        </table>
        
<?php
}
