<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/*******************************************
* bbp topic count Settings Page
*******************************************/

/**************  NOTE this file is also used in the bbp-topic-count plugin, but with  different text domain.  If you update this file, you will need to update  bbp-topic-count */
/** the pre the above line code is in 6.1.1 see line 44 on how it was done */


if ( ! function_exists( 'tc_settings' ) ) {
        function tc_settings() {   

                // is bbp-topic-count active?
                $bbp_tc_active = ( in_array( 'bbp-topic-count/bbp-topic-count.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false );
                // if not active and is multisite check for network active
                if ( ! $bbp_tc_active && is_multisite() ) {
                        $bbp_tc_active = is_plugin_active_for_network('bbp-topic-count/bbp-topic-count.php');
                }
                
                // is bbp-style-pack active?
                $bbp_bsp_active = ( in_array( 'bbp-style-pack/bbp-style-pack.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false );
                // if not active and is multisite check for network active
                if ( ! $bbp_bsp_active && is_multisite() ) {
                        $bbp_bsp_active = is_plugin_active_for_network('bbp-style-pack/bbp-style-pack.php');
                }
                
                if ( isset( $_GET["page"] ) ) {
                        // set values for bbp-topic-count
                        if ( $_GET["page"] == 'bbp-topic-count' ) {
                                $tc_field_name = 'tc_settings';
                                $tc_textdomain = 'bbp-topic-count'; // bad practice, but still works and is necessary for code portability - not used after 6.1.1
                                global $tc_options;
                                $tc_options_values = $tc_options;
                        }

                        // set values for bbp-style-pack
                        if ( $_GET["page"] == 'bbp-style-pack' ) {
                                $tc_field_name = 'bsp_settings_topic_count';
                                $tc_textdomain = 'bbp-style-pack'; //only  now used on line
								// bad practice, but still works and is necessary for code portability - taken out in 6.1.2
                                // is bbp-topic-count active?
                                if ( $bbp_tc_active ) {
                                        global $tc_options;
                                        $tc_options_values = $tc_options;
                                } else {
                                        global $bsp_settings_topic_count;
                                        $tc_options_values = $bsp_settings_topic_count;
                                }
                        }
                }

                
                bsp_clear_cache();
                
                ?>

                                <form method="post" action="options.php">

                                        <table class="form-table">

                                                <tr valign="top">
                                                        <th colspan="2">
                                                                <h3>
                                                                <?php _e( 'Topic Count Settings', 'bbp-style-pack' ); ?>
                                                                </h3>
                                                        </th>
                                                </tr>

                                                <?php 
                                                // is bbp-topic-count active & we're in bsp settings page?
                                                if ( $bbp_tc_active && ( $tc_textdomain == 'bbp-style-pack' ) ) { ?>
                                                        <tr valign="top">
                                                                <td colspan="2"> 
                                                                        <b> 
                                                                                <?php echo "*****"; ?> <br/>
                                                                                <?php _e( '* You currently have the bbP Topic Count plugin active.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php _e( '* The settings have been copied here to the bbP Style Pack plugin.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php _e( '* Click "Save Options" below to apply those settings.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php _e( '* After that, you can safely deactivate the bbP Topic Count plugin.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php echo "*****"; ?> <br/>
                                                                        </b>
                                                                </td>
                                                        </tr>
                                                <?php } ?>
                                                        
                                                <?php 
                                                // is bbp-style-pack active & we're in bbp-topic-count settings page?
                                                if ( $bbp_bsp_active && ( $tc_textdomain == 'bbp-topic-count' ) ) { ?>
                                                        <tr valign="top">
                                                                <td colspan="2"> 
                                                                        <b> 
                                                                                <?php echo "*****"; ?> <br/>
                                                                                <?php _e( '* You currently have the bbP Style Pack plugin active.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php _e( '* It includes all of the same functionality found in the bbP Topic Count plugin.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php 
                                                                                        printf(
                                                                                                /* translators: %1$s is the opening URL HTML code string and %2$s is the closing HTML tag. */
                                                                                                __( '* Please head over to the %1$ssettings tab for "Topic Counts"%2$s to apply your settings.', 'bbp-style-pack' ), 
                                                                                                '<a href="'.admin_url('options-general.php?page=bbp-style-pack&tab=topic_count').'">',
                                                                                                '</a>'
                                                                                        );
                                                                                ?> <br/>
                                                                                <?php _e( '* After that, you can safely deactivate the bbP Topic Count plugin.', 'bbp-style-pack' ); ?> <br/>
                                                                                <?php echo "*****"; ?> <br/>
                                                                        </b>
                                                                </td>
                                                        </tr>
                                                <?php } ?>

                                                <tr valign="top">
                                                        <td colspan="2">    
                                                                <p>
                                                                        <?php 
                                                                        $tab_str = __( 'tab', 'bbp-style-pack' );
                                                                        $plugin_str = __( 'plugin', 'bbp-style-pack' );
                                                                        printf(
                                                                                /* translators: %s is string for either tab or plugin depending */
                                                                                __( "This %s allows you to display topic count, reply count and total posts under the authors name and avatar in topics and replies, or in the reply content.", 'bbp-style-pack' ), 
                                                                                ( ( function_exists( 'tc_settings' ) && ( basename( __FILE__ ) == 'settings_topic_count.php' ) ) ? $tab_str : $plugin_str )
                                                                        );
                                                                        ?>
                                                                </p>
                                                        </td>
                                                </tr>

                                                <?php 
                                                // is bbp-topic-count active & we're in bsp settings page?
                                                if ( $bbp_tc_active && ( $tc_textdomain == 'bbp-style-pack' ) ) { ?>
                                                        <tr valign="top">
                                                                <td colspan="2">
                                                                        <?php echo '<img src="' . WP_PLUGIN_URL.'/'.'bbp-style-pack'.'/images/topic-count.png'.'" width="650px" > '; ?>
                                                                </td>
                                                        </tr>
                                                <?php } ?>

                                                <?php settings_fields( $tc_field_name ); ?>

                                                <tr valign="top">
                                                        <td colspan="2">
                                                                <!-- save the options -->
                                                                <p class="submit">
                                                                        <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'bbp-style-pack' ); ?>" />
                                                                </p>
                                                        </td>
                                                </tr>


                                                <!-------------------------------Topics ---------------------------------------->

                                                <tr valign="top">
                                                        <th colspan="2"><h3>
                                                                <?php _e( 'Topics', 'bbp-style-pack' ); ?>
                                                        </h3></th>
                                                </tr>

                                                <!-- checkbox to activate -->
                                                <tr valign="top">  
                                                        <th>
                                                                <?php _e( 'Activate', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php tc_activate_checkbox( 'activate_topics' ); ?>
                                                        </td>
                                                </tr>


                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Topic Label Name', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <input id="<?php echo $tc_field_name ?>[topic_label]" class="large-text" name="<?php echo $tc_field_name ?>[topic_label]" type="text" value="<?php echo isset( $tc_options_values['topic_label'] ) ? esc_html( $tc_options_values['topic_label'] ) : ''; ?>" /><br/>
                                                                <label class="description" for="<?php echo $tc_field_name ?>[topic_label]"><?php _e( 'Enter the description eg "Topics:", "Topics - ", "Posts :" "Started : " ebsp.', 'bbp-style-pack' ); ?></label><br/>
                                                        </td>
                                                </tr>


                                                <!------------------------------- Replies ------------------------------------------>
                                                <tr valign="top">
                                                        <th colspan="2"><h3>
                                                                <?php _e( 'Replies', 'bbp-style-pack' ); ?>
                                                        </h3></th>
                                                </tr>

                                                <!-- checkbox to activate -->
                                                <tr valign="top">  
                                                        <th>
                                                                <?php _e( 'Activate', 'bbp-topic count' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php tc_activate_checkbox( 'activate_replies' ); ?>
                                                        </td>
                                                </tr>


                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Reply Label Name', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <input id="<?php echo $tc_field_name ?>[reply_label]" class="large-text" name="<?php echo $tc_field_name ?>[reply_label]" type="text" value="<?php echo isset( $tc_options_values['reply_label'] ) ? esc_html( $tc_options_values['reply_label'] ) : ''; ?>" /><br/>
                                                                <label class="description" for="<?php echo $tc_field_name ?>[reply_label]"><?php _e( 'Enter the description eg "Replies:", "Replies - ", "Posts", "joined in : " ebsp.', 'bp-topic-count' ); ?></label><br/>
                                                        </td>
                                                </tr>



                                                <!------------------------------- Total Posts ------------------------------------------>
                                                <tr valign="top">
                                                        <th colspan="2"><h3>
                                                                <?php _e( 'Total posts (Topics + Replies)', 'bbp-style-pack' ); ?>
                                                        </h3></th>
                                                </tr>

                                                <!-- checkbox to activate -->
                                                <tr valign="top">  
                                                        <th>
                                                                <?php _e( 'Activate', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php tc_activate_checkbox( 'activate_posts' ); ?>
                                                        </td>
                                                </tr>


                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Total Posts Name', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <input id="<?php echo $tc_field_name ?>[posts_label]" class="large-text" name="<?php echo $tc_field_name ?>[posts_label]" type="text" value="<?php echo isset( $tc_options_values['posts_label'] ) ? esc_html( $tc_options_values['posts_label'] ) : ''; ?>" /><br/>
                                                                <label class="description" for="<?php echo $tc_field_name ?>[item3_label]"><?php _e( 'Enter the description eg "Total posts:", "Total Posts - ", "Total", "Posts: " ebsp.', 'bp-topic-count' ); ?></label><br/>
                                                        </td>
                                                </tr>


                                                <!------------------------------- Display parameters ------------------------------------------>
                                                <tr valign="top">
                                                        <th colspan="2"><h3>
                                                                <?php _e( 'Display parameters', 'bbp-style-pack' ); ?>
                                                        </h3></th>
                                                </tr>

                                                <?php
                                                        $item0 = $tc_field_name."[sep]";
                                                        $value0 = ( ! empty( $tc_options_values['sep'] ) ? $tc_options_values['sep'] : 0 );
                                                ?>
                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Thousands Seperator', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />';
                                                                _e( 'No seperator (eg 1000)', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />';
                                                                _e( 'Comma Seperator (eg 1,000)', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />';
                                                                _e( 'Space Seperator (eg 1 000)', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="3" class="code"  ' . checked( 3,$value0, false ) . ' />';
                                                                _e( 'Show counts over 1000 as "x.xk" eg "1.6k"', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="4" class="code"  ' . checked( 4,$value0, false ) . ' />';
                                                                _e( 'Show counts over 1000 as "x,xk" eg "1,6k"', 'bbp-style-pack' ); ?>
                                                        </td>
                                                </tr>

                                                <?php
                                                        $item0 = $tc_field_name."[link_counts]";
                                                        $value0 = ( ! empty( $tc_options_values['link_counts'] ) ? $tc_options_values['link_counts'] : 0 );
                                                ?>
                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Link Counts', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />';
                                                                _e( 'Do Not Link Number Counts to User Profile Page Sections', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />';
                                                                _e( 'Link Number Counts to User Profile Page Sections', 'bbp-style-pack' ); ?>
                                                                <br>
                                                        </td>
                                                </tr>

                                                <?php
                                                        $item0 = $tc_field_name."[order]";
                                                        $value0 = ( ! empty( $tc_options_values['order'] ) ? $tc_options_values['order'] : 0 );
                                                ?>
                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Display order', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />';
                                                                _e ( "Text then count eg 'Topics: 10'", 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />';
                                                                _e ( "Count then text eg '10 Topics'", 'bbp-style-pack' ); ?>
                                                                <br>
                                                        </td>
                                                </tr>

                                                <?php
                                                        $item0 = $tc_field_name."[location]";
                                                        $value0 = ( ! empty( $tc_options_values['location'] ) ? $tc_options_values['location'] : 0 );
                                                ?>
                                                <tr valign="top">
                                                        <th>
                                                                <?php _e( 'Display Location', 'bbp-style-pack' ); ?>
                                                        </th>
                                                        <td>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />';
                                                                _e( 'Display in Author Details', 'bbp-style-pack' ); ?>
                                                                <br>
                                                                <?php echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />';
                                                                _e( 'Display in Reply content', 'bbp-style-pack' ); ?>
                                                                <br>
                                                        </td>
                                                </tr>

                                                <tr valign="top">
                                                        <td colspan="2">
                                                                <!-- save the options -->
                                                                <p class="submit">
                                                                        <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'bbp-style-pack' ); ?>" />
                                                                </p>
                                                        </td>
                                                </tr>

                                        </table>
                                </form>

                <?php
                // in bsp settings page?
                if ( 'bbp-style-pack' == 'bbp-style-pack' ) {
                        // display the shortcodes help text
                        // bbp-topic-count displays them in a separate tab (settings_shortcodes.php)
                        echo tc_shortcodes_display();
                }
        }
}


/*****************************   Checkbox functions **************************/

if ( ! function_exists( 'tc_activate_checkbox' ) ) {
        function tc_activate_checkbox( $field ) {
    
                // is bbp-topic-count active?
                $bbp_tc_active = ( in_array( 'bbp-topic-count/bbp-topic-count.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false );
                // if not active and is multisite check for network active
                if ( ! $bbp_tc_active && is_multisite() ) {
                        $bbp_tc_active = is_plugin_active_for_network('bbp-topic-count/bbp-topic-count.php');
                }
                
                if ( isset( $_GET["page"] ) ) {
                        // set values for bbp-topic-count
                        if ( $_GET["page"] == 'bbp-topic-count' ) {

                                $tc_field_name = 'tc_settings';
                                $tc_textdomain = 'bbp-topic-count'; // bad practice, but still works and is necessary for code portability - not used after 6.1.1

                                global $tc_options;
                                $tc_options_values = $tc_options;

                        }

                        // set values for bbp-style-pack
                        if ( $_GET["page"] == 'bbp-style-pack' ) {

                                $tc_field_name = 'bsp_settings_topic_count';
                                $tc_textdomain = 'bbp-style-pack'; // bad practice, but still works and is necessary for code portability - not used after 6.1.1

                                // is bbp-topic-count active?
                                if ( $bbp_tc_active ) {
                                        global $tc_options;
                                        $tc_options_values = $tc_options;
                                } else {
                                        global $bsp_settings_topic_count;
                                        $tc_options_values = $bsp_settings_topic_count;
                                }

                        }
                }

                $item = ! empty( $tc_options_values[$field] ) ? $tc_options_values[$field] : '';
                echo '<input name="'.$tc_field_name.'['.$field.']" id="'.$tc_field_name.'['.$field.']" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />';
                _e( 'Add this item to the display', 'bbp-style-pack' );
        }
}


/*******************************************
* bbp topic count Shotcodes Settings
*******************************************/


/*****************************   Shortcodes Help **************************/

if ( ! function_exists( 'tc_shortcodes_display' ) ) {
        function tc_shortcodes_display() {
                
                if ( isset( $_GET["page"] ) ) {
                        // set values for bbp-topic-count
                        if ( $_GET["page"] == 'bbp-topic-count' ) {
                                $tc_textdomain = 'bbp-topic-count'; // bad practice, but still works and is necessary for code portability 
                        }
                        // set values for bbp-style-pack
                        if ( $_GET["page"] == 'bbp-style-pack' ) {
                                $tc_textdomain = 'bbp-style-pack'; // bad practice, but still works and is necessary for code portability 
                        }
                }

                ?>
                <table class="form-table">
                        <tr valign="top">
                                <th colspan="2">
                                        <h3>
                                                <?php _e( 'Additional Shortcodes', 'bbp-style-pack' ); ?>
                                        </h3>
                                </th>
                        </tr>
                        <tr valign="top">
                                <td colspan="2">
                                        <p>
                                                <tt>[display-topic-count]</tt> 
                                                <?php _e( 'Displays the current users topic count', 'bbp-style-pack' ); ?>
                                        </p>
                                        <p>
                                                <tt>[display-reply-count]</tt>  
                                                <?php _e( 'Displays the current users reply count', 'bbp-style-pack' ); ?>
                                        </p>
                                        <p>
                                                <tt>[display-total-count]</tt>  
                                                <?php _e( 'Displays the current users total topic and reply count', 'bbp-style-pack' ); ?>
                                        </p>
                                        <p>
                                                <tt>[display-top-users]</tt>
                                                <?php _e( 'Displays top x users for total topics and replies', 'bbp-style-pack' ); ?>
                                        </p>
                                        <p>
                                                <?php _e( 'This shortcode has many parameters - these are optional and only add those you need !', 'bbp-style-pack' ); ?>
                                        </p>
                                        <p><h3>
                                                <?php _e( 'display-top-users - additional parameters !', 'bbp-style-pack' ); ?>
                                        </h3></p>
                                        <p>
                                                <tt>[display-top-users avatar-size="25" padding="20" before=" - " after=" topics"  show="6" count="tr" hide-admins="yes" profile-link="no" show-avatar="no" show-name="no" forum="1234"]</tt>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i><b>
                                                <?php _e( 'Note - you only need enter parameters where you want to change the default', 'bbp-style-pack' ); ?>
                                        </i></b></p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'avatar-size', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = '96' - the smaller the number the smaller the avatar", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'padding', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = '50' - The space between the avatar/username and the text to the right of this", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'before', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = blank -  Any characters/text before the count number - eg 'Topics : ", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'after', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = blank - Any characters/text after the count number - eg ' Topics ", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'show', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = '5' - the number of users to show", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'count', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = 'tr' - what to count - put 't' for just topics, 'r' for just replies default is to count the total topics and replies ", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'hide-admins', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = 'no' - if set to 'yes' - then administrators are excluded from display", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'profile-link', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = 'yes' - if set to 'no' - then the avatar and/or name do not have a link to the users profile", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'show-avatar', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = 'yes' -  if set to 'no' - then the avatar will not show", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'show-name', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = 'yes' -  if set to 'no' - then the name will not show", 'bbp-style-pack' ); ?>
                                        </p>

                                        <!-- paragraph break -->
                                        <br/>
                                        <!-- end paragraph break -->

                                        <p><i>
                                                <?php _e( 'forum', 'bbp-style-pack' ); ?>
                                        </i></p>
                                        <p>
                                                <?php _e( "Default = blank - Enter a single forum ID to only count from that forum", 'bbp-style-pack' ); ?>
                                        </p>

                                </td>
                        </tr>
                </table>
                <?php
        }
}
