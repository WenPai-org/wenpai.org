<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//buttons style settings page

function bsp_style_settings_buttons() {
	global $bsp_style_settings_buttons;
	?> 
	<form method="post" action="options.php">
                <?php 
                wp_nonce_field( 'style-settings_buttons', 'style-settings-nonce' );
                settings_fields( 'bsp_style_settings_buttons' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
		?>
		<table class="form-table">
                        <tr valign="top">
                                <th colspan="2">
                                        <h3>
                                                <?php _e('Forum Buttons' , 'bbp-style-pack' ); ?>
                                        </h3>
                                </th>
                        </tr>

                        <tr valign="top">
                                <th colspan="2">
                                        <?php _e('This section lets you add various buttons to the forum display.' , 'bbp-style-pack' ); ?>
                                </th>
                        </tr>

                        <tr valign="top">
                                <th colspan="2">
                                        <?php _e('There are also buttons for the topic display, these are set in the \'Topic/Reply\' Tab.' , 'bbp-style-pack' ); ?>
                                </th>
                        </tr>
                </table>
            
	<!-- save the options -->
                <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
                </p>
	
                <table>
                        <tr>
                                <td>
                                        <p>
                                                <?php _e('This section allows you to add up to 4 buttons - a create topic button, a subscribe button and a profile button, and if activated a button for unread topics.  You can then style and display them in any order', 'bbp-style-pack'); ?> 
                                        </p>
                                </td>
                                <td>	
                                        <?php
                                        //show style image
                                        echo '<img src="' . plugins_url( 'images/buttons.JPG',dirname(__FILE__)  ) . '" > '; 
                                        ?>
                                </td>
                        </tr>
                </table>

                <hr>
	
                <table class="form-table">
                <!-- CREATE TOPIC BUTTON  -->	
                <!-- checkbox to activate  -->
                        <tr valign="top">  
                                <th>
                                        1. <?php _e('Activate Create Topic Button', 'bbp-style-pack'); ?>
                                </th>
                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_buttons['Create Topic Buttonactivate'] ) ?  $bsp_style_settings_buttons['Create Topic Buttonactivate'] : '');
                                        echo '<input name="bsp_style_settings_buttons[Create Topic Buttonactivate]" id="bsp_style_settings_buttons[Create Topic Buttonactivate]" type="checkbox" value="1" class="code" ' . checked( 1, $item, false ) . ' />';
                                        ?>
                                </td>
                        </tr>
                        <tr valign="top">
                                <th>
                                        <?php _e('Create new topic Description', 'bbp-style-pack'); ?>
                                </th>
                                <td colspan="2">
                                        <?php					
                                        $item1 = (!empty ($bsp_style_settings_buttons['new_topic_description'] ) ? $bsp_style_settings_buttons['new_topic_description']  : '' ) 
                                        ?>
                                        <input id="bsp_style_settings_buttons[new_topic_description]" class="large-text" name="bsp_style_settings_buttons[new_topic_description]" type="text" value="<?php echo esc_html( $item1 ); ?>" /><br/>
                                        <label class="description" for="bsp_settings[new_topic_description]">
                                                <?php _e( 'Default : Create New Topic', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <hr>
                                </td>
                        </tr>

	
<!-- SUBSCRIBE BUTTON  -->		
	<!-- checkbox to activate  -->
					
                        <tr valign="top">  
                                <th>
                                        2. <?php _e('Activate Subscribe Button', 'bbp-style-pack'); ?>
                                </th>
                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_buttons['Subscribe Buttonactivate'] ) ?  $bsp_style_settings_buttons['Subscribe Buttonactivate'] : '');
                                        echo '<input name="bsp_style_settings_buttons[Subscribe Buttonactivate]" id="bsp_style_settings_buttons[Subscribe Buttonactivate]" type="checkbox" value="1" class="code" ' . checked( 1, $item, false ) . ' />';
                                        ?>
                                </td>
                        </tr>
                        <tr valign="top">
                                <th>
                                        <?php _e('Subscribe Description', 'bbp-style-pack'); ?>
                                </th>
                                <td colspan="2">
                                        <?php 
                                        $item1 = (!empty ($bsp_style_settings_buttons['subscribe_button_description'] ) ? $bsp_style_settings_buttons['subscribe_button_description']  : '' ); 
                                        ?>
                                        <input id="bsp_style_settings_buttons[subscribe_button_description]" class="large-text" name="bsp_style_settings_buttons[subscribe_button_description]" type="text" value="<?php echo esc_html( $item1 ); ?>" /><br/>
                                        <label class="description" for="bsp_settings[subscribe_button_description]">
                                                <?php _e( 'Default : Subscribe', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr valign="top">
                                <th>
                                        <?php _e('Unsubscribe Description', 'bbp-style-pack'); ?>
                                </th>
                                <td colspan="2">
                                        <?php 
                                        $item1 = (!empty ($bsp_style_settings_buttons['unsubscribe_button_description'] ) ? $bsp_style_settings_buttons['unsubscribe_button_description']  : '' ); 
                                        ?>
                                        <input id="bsp_style_settings_buttons[unsubscribe_button_description]" class="large-text" name="bsp_style_settings_buttons[unsubscribe_button_description]" type="text" value="<?php echo esc_html( $item1 ); ?>" /><br/>
                                        <label class="description" for="bsp_settings[unsubscribe_button_description]">
                                                <?php _e( 'Default : Unsubscribe', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <hr>
                                </td>
                        </tr>
					
<!-- PROFILE BUTTON  -->	
	<!-- checkbox to activate  -->
                        <tr valign="top">  
                                <th>
                                        3. <?php _e('Activate Profile Button', 'bbp-style-pack'); ?>
                                </th>
                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_buttons['Profile Buttonactivate'] ) ?  $bsp_style_settings_buttons['Profile Buttonactivate'] : '');
                                        echo '<input name="bsp_style_settings_buttons[Profile Buttonactivate]" id="bsp_style_settings_buttons[Profile Buttonactivate]" type="checkbox" value="1" class="code" ' . checked( 1, $item, false ) . ' />';
                                        ?>
                                </td>
                        </tr>
                        <tr valign="top">
                                <th>
                                        <?php _e('Profile Description', 'bbp-style-pack'); ?>
                                </th>
                                <td colspan="2">
                                        <?php 
                                        $item1 = (!empty ($bsp_style_settings_buttons['profile_description'] ) ? $bsp_style_settings_buttons['profile_description']  : '' ); 
                                        ?>
                                        <input id="bsp_style_settings_buttonsprofile[profile_description]" class="large-text" name="bsp_style_settings_buttons[profile_description]" type="text" value="<?php echo esc_html( $item1 ); ?>" /><br/>
                                        <label class="description" for="bsp_settings[profile_description]">
                                                <?php _e( 'Default : Profile', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <hr>
                                </td>
                        </tr>
			
	<!-- BUTTON STYLING -->					
                        <tr>	
                                <th>
                                        <?php _e('Button Style', 'bbp-style-pack'); ?>
                                </th>
                        </tr>
                        <tr>
                                <td colspan="2">
                                        <?php _e('You can style the button(s) below, use an existing class from your theme, or create a button class (see help further down)', 'bbp-style-pack'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td style="vertical-align:top;">
                                        <?php
                                        $item = 'bsp_style_settings_buttons[button_type]';
                                        $item1 = (!empty($bsp_style_settings_buttons['button_type']) ? $bsp_style_settings_buttons['button_type'] : 1); 
                                        echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1, $item1, false ) . ' />';
                                        _e('Use style below' , 'bbp-style-pack' );
                                        ?>
                                        <br>
                                        <label class="description">
                                                <i><?php _e( '(You can style the button below)' , 'bbp-style-pack' ); ?></i>
                                        </label>
                                </td>
                                <td style="vertical-align:top;">
                                        <?php
                                        echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2, $item1, false ) . ' />';
                                        _e('Use Class' , 'bbp-style-pack' );
                                        ?>
                                        <br>
                                        <label class="description">
                                                <i><?php _e( '(Use a class from your theme - the button will use the style from the class below.)' , 'bbp-style-pack' ); ?></i>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <hr>
                                </td>
                        </tr>

                        <?php 
                        $name = ('Button');
                        $name0 = __('Button', 'bbp-style-pack');
                        $name1 = __('Size', 'bbp-style-pack');
                        $name2 = __('Color', 'bbp-style-pack');
                        $name3 = __('Font', 'bbp-style-pack');
                        $name4 = __('Style', 'bbp-style-pack');
                        $name5 = __('Background Color', 'bbp-style-pack');
                        $name6 = __('Hover Background Color', 'bbp-style-pack');
                        $name7 = __('Class', 'bbp-style-pack');

                        $area1='Size';
                        $area2='Color';
                        $area3='Font';
                        $area4='Font Style';
                        $area5='background color';
                        $area6='hover color';
                        $area7='class';

                        $item1="bsp_style_settings_buttons[".$name.$area1."]";
                        $item2="bsp_style_settings_buttons[".$name.$area2."]";
                        $item3="bsp_style_settings_buttons[".$name.$area3."]";
                        $item4="bsp_style_settings_buttons[".$name.$area4."]";
                        $item5="bsp_style_settings_buttons[".$name.$area5."]";
                        $item6="bsp_style_settings_buttons[".$name.$area6."]";
                        $item7="bsp_style_settings_buttons[".$name.$area7."]";

                        $value1 = (!empty($bsp_style_settings_buttons[$name.$area1]) ? $bsp_style_settings_buttons[$name.$area1]  : '');
                        $value2 = (!empty($bsp_style_settings_buttons[$name.$area2]) ? $bsp_style_settings_buttons[$name.$area2]  : '#ffffff');
                        $value3 = (!empty($bsp_style_settings_buttons[$name.$area3]) ? $bsp_style_settings_buttons[$name.$area3]  : '');
                        $value4 = (!empty($bsp_style_settings_buttons[$name.$area4]) ? $bsp_style_settings_buttons[$name.$area4]  : 'Normal');
                        $value5 = (!empty($bsp_style_settings_buttons[$name.$area5]) ? $bsp_style_settings_buttons[$name.$area5]  : '#3498db');
                        $value6 = (!empty($bsp_style_settings_buttons[$name.$area6]) ? $bsp_style_settings_buttons[$name.$area6]  : '#3cb0fd');
                        $value7 = (!empty($bsp_style_settings_buttons[$name.$area7]) ? $bsp_style_settings_buttons[$name.$area7]  : '');
                        ?>

                        <tr>
                                <td>
                                        <?php echo $name1; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'Default 10px - see help for further info', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo $name2; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item2.'" class="bsp-color-picker" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack'); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo $name3; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'Default : Arial - Enter Font eg Arial - see help for further info ', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td> 
                                        <?php echo $name4; ?>
                                </td>
                                <td>
                                        <select name="<?php echo $item4; ?>">
                                                <?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4); ?> 
                                                <option value="Normal">Normal</option>
                                                <option value="Italic">Italic</option>
                                                <option value="Bold">Bold</option>
                                                <option value="Bold and Italic">Bold and Italic</option>
                                        </select>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo $name5; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item5.'" class="bsp-color-picker" name="'.$item5.'" type="text" value="'.esc_html( $value5 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack'); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td> 
                                        <?php echo $name6; ?> 
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item6.'" class="bsp-color-picker" name="'.$item6.'" type="text" value="'.esc_html( $value6 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack'); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <hr>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo $name7; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item7.'" class="large-text" name="'.$item7.'" type="text" value="'.esc_html( $value7 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'If you have selected "Use Class" above,  then enter the class.', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        <tr>
                                <td></td>
                        </tr>
                        <tr>
                                <td></td>
                        </tr>
                        <tr>
                                <td></td>
                        </tr>
		
                        <?php
                        //work out how many buttons to display in order
                        global $bsp_style_settings_buttons;
                        global $bsp_style_settings_unread;
                        $topic_button = $subscribe_button = $profile_button = $unread_button = 0;
                        if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) )  $topic_button=1;
                        if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) )    $subscribe_button=1;	
                        if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) )  $profile_button=1;
                        if (!empty ($bsp_style_settings_unread['unread_activate']))	 $unread_button=1;
                        $total_buttons = $topic_button + $subscribe_button + $profile_button + $unread_button;
                        ?>
	
                        <tr>
                                <td>
                                        <?php _e('DISPLAY ORDER' , 'bbp-style-pack' ); ?>
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2">
                                        <?php _e('If you are displaying more than 1 button, you can change the default order ' , 'bbp-style-pack' ); ?>
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2">
                                        <?php _e('Click \'Save changes\' to update this section if you have activated/deactivated buttons' , 'bbp-style-pack' ); ?>
                                </td>
                        </tr>
                        
                        <?php 
                        if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) ) {
                                ?>
                                <tr>
                                        <td style="vertical-align:top;">
                                                <?php _e('Create Topic' , 'bbp-style-pack' ); ?>
                                        </td>
                                        <td style="vertical-align:top;">
                                                <?php $item='bsp_style_settings_buttons[topic_order]'; ?>
                                                <?php $value = (!empty($bsp_style_settings_buttons["topic_order"]) ? $bsp_style_settings_buttons["topic_order"] : ''); ?>
                                                <?php echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'" /><br>'; ?> 
                                                <label class="description">
                                                        <?php _e( 'Enter the order ie a number from 1 to', 'bbp-style-pack' ); ?>
                                                        <?php echo $total_buttons; ?>
                                                </label>
                                                </br>
                                        </td>
                                </tr>
                                <?php
                        } 
                        if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) )  {
                                ?>
                                <tr>
                                        <td style="vertical-align:top;">
                                                <?php _e('Subscribe' , 'bbp-style-pack' ); ?>
                                        </td>
                                        <td style="vertical-align:top;">
                                                <?php $item='bsp_style_settings_buttons[subscribe_order]'; ?>
                                                <?php $value = (!empty($bsp_style_settings_buttons["subscribe_order"]) ? $bsp_style_settings_buttons["subscribe_order"] : ''); ?>
                                                <?php echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'" /><br>'; ?> 
                                                <label class="description">
                                                        <?php _e( 'Enter the order ie a number from 1 to', 'bbp-style-pack' ); ?>
                                                        <?php echo $total_buttons; ?>
                                                </label>
                                                </br>
                                        </td>
                                </tr>
                                <?php
                        }
                        if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) ) {
                                ?>
                                <tr>
                                        <td style="vertical-align:top;">
                                                <?php _e('Profile' , 'bbp-style-pack' ); ?>
                                        </td>
                                        <td style="vertical-align:top;">
                                                <?php $item='bsp_style_settings_buttons[profile_order]'; ?>
                                                <?php $value = (!empty($bsp_style_settings_buttons["profile_order"]) ? $bsp_style_settings_buttons["profile_order"] : ''); ?>
                                                <?php echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'" /><br>'; ?> 
                                                <label class="description">
                                                        <?php _e( 'Enter the order ie a number from 1 to', 'bbp-style-pack' ); ?>
                                                        <?php echo $total_buttons; ?>
                                                </label>
                                                </br>
                                        </td>
                                </tr>
                                <?php
                        }

                        global $bsp_style_settings_unread;
                        if (!empty ($bsp_style_settings_unread['unread_activate'])) {
                                ?>
                                <tr>
                                        <td style="vertical-align:top;">
                                                <?php _e('Mark as read' , 'bbp-style-pack' ); ?>
                                        </td>
                                        <td style="vertical-align:top;">
                                                <?php $item='bsp_style_settings_buttons[unread_order]'; ?>
                                                <?php $value = (!empty($bsp_style_settings_buttons["unread_order"]) ? $bsp_style_settings_buttons["unread_order"] : ''); ?>
                                                <?php echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'" /><br>'; ?> 
                                                <label class="description">
                                                        <?php _e( 'Enter the order ie a number from 1 to', 'bbp-style-pack' ); ?>
                                                        <?php echo $total_buttons; ?>
                                                </label>
                                                </br>
                                        </td>
                                </tr>
                                <?php
                        } // end of if unread...
                        ?>

                </table>
                
                <hr>
                
	<!-- save the options -->
                <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
                </p>
                
	</form>

	<p>
                <b><?php _e( 'Further Help', 'bbp-style-pack' ); ?> </b>
	</p>
	<p>
		<?php _e( 'If your theme has a button style, then you can use this by entering it in the class sections above - of course if you know how to find the class !', 'bbp-style-pack' ); ?>
	</p>
	<p>
		<?php _e( 'There are also many button styling websites available which will let you create a style for your buttons - just google \'button generator CSS\'.  These create CSS code and a class.  You would put the CSS code into either your theme or into the \'custom CSS\' tab of this plugin.', 'bbp-style-pack' ); ?>
	</p>
	<p>
		<?php
                        echo sprintf(
                                /* translators: %s is a URL */
                                __( 'So for instance if you go to %s you can create a button.  This will generate code for a class of \'btn\'.  Copy this code to the \'custom CSS\' tab of this plugin, then select \'Use Class\' above and put \'btn\' in the class box above. ', 'bbp-style-pack' ),
                                '<a href="http://css3buttongenerator.com/" target="_blank">http://css3buttongenerator.com/</a>'
                        ); 
                ?>
        </p>

        <?php
}
