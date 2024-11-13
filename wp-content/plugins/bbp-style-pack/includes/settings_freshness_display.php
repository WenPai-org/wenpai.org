<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//freshness display style settings page

function bsp_style_settings_freshness () {
	global $bsp_style_settings_freshness ;
	global $bsp_bbpress_version ;
	if (get_locale() == 'en_GB' || get_locale() == 'en_US') $lang='seng' ;
	//test if this is the first time accessing the settings, and if so set $check = 1
	$check = (!empty($bsp_style_settings_freshness) ? 0 : 1);
	?> 
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings_freshness', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_freshness' );
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Freshness Display' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to amend freshness', 'bbp-style-pack'); ?>
				</p>
			</td>
			<td>	
				<?php echo '<img src="' . plugins_url( 'images/freshness_display.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
			
	<table class="form-table">
	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th>
				<?php _e('Activate Freshness display', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php $item = (!empty( $bsp_style_settings_freshness['activate'] ) ?  $bsp_style_settings_freshness['activate'] : '');
				echo '<input name="bsp_style_settings_freshness[activate]" id="bsp_style_settings_freshness[activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
			</td>
		</tr>
<!-------------------------------Heading---------------------------------------->
		<tr valign="top">
			<th>
				1. <?php _e('Heading Name', 'bbp-style-pack'); ?>
			</th>
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_freshness['heading_name'] ) ? $bsp_style_settings_freshness['heading_name']  : '' ) ?>
				<input id="bsp_style_settings_freshness[heading_name]" class="large-text" name="bsp_style_settings_freshness[heading_name]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_settings[heading_label]"><?php _e( 'Default : Freshness - if you wish to change enter the heading description eg "Last Post", "Last updated", "Freshness" "Last activity" etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>

		<tr>
			<th style="width:250px">
				2. <?php _e('Topic Title', 'bbp-style-pack'); ?> 
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_freshness[show_title]' ;
				$item1 = (!empty($bsp_style_settings_freshness['show_title']) ? $bsp_style_settings_freshness['show_title'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to show Title on forums pages', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<th>
				3. <?php _e('Topic Freshness', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_freshness[show_date]' ;
				$item1 = (!empty($bsp_style_settings_freshness['show_date']) ? $bsp_style_settings_freshness['show_date'] : '');
				if ($check == 1) $item1 = 1 ;
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to show Freshness (see 6. below for format)', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<th>
				4.<?php _e('Topic Author name', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_freshness[show_name]' ;
				$item1 = (!empty($bsp_style_settings_freshness['show_name']) ? $bsp_style_settings_freshness['show_name'] : '');
				if ($check == 1) $item1 = 1 ;
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to show Author name', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<th>
				5.<?php _e('Topic Author avatar', 'bbp-style-pack'); ?> 
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_freshness[show_avatar]' ;
				$item1 = (!empty($bsp_style_settings_freshness['show_avatar']) ? $bsp_style_settings_freshness['show_avatar'] : '');
				if ($check == 1) $item1 = 1 ;
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to show author avatar', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<th>
				6. <?php _e('Freshness format', 'bbp-style-pack'); ?> 
			</th>
			<td>
				<code> 4 days, 5 hours ago</code>
			</td>
			<td>
				<code> 18 February 2021 at 4:49 pm</code>
			</td>
			<td>
				<code> 4 days, 5 hours ago</code>
				<br/>
				<?php _e('then after x days', 'bbp-style-pack'); ?>
				<br/>
				<code> 18 February 2021 at 4:49 pm</code>
			</td>
		</tr>
		
		<tr>	
			<td>
			</td>
			<td style="width:250px;vertical-align:top">
				<?php
				$item0='bsp_style_settings_freshness[date_format]' ;
				$value0 = (!empty($bsp_style_settings_freshness['date_format']) ? $bsp_style_settings_freshness['date_format'] : 1) ; 
				echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Click to show time since last post' , 'bbp-style-pack' ) ;?>
				<br/>
					<label class="description">
						<i>
						<?php _e( 'Default' , 'bbp-style-pack' ); ?>
						</i>
					</label>
			
			</td>
			<td style="width:250px;vertical-align:top">
				<?php
				echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />' ;
				_e ('Click to show date of last post' , 'bbp-style-pack' ) ;?>
			</td>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="3" class="code"  ' . checked( 3,$value0, false ) . ' />' ;
				_e ('Click to show Hybrid' , 'bbp-style-pack' ) ;?>
			</td>
		</tr>
		
		<tr>
		<td></td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td style="width:250px;vertical-align:top">
				<?php
				$item_freshness='bsp_style_settings_freshness[freshness_format]' ;
				$value_freshness = (!empty($bsp_style_settings_freshness['freshness_format']) ? $bsp_style_settings_freshness['freshness_format'] : 0) ; 
				echo '<input name="'.$item_freshness.'" id="'.$value_freshness.'" type="radio" value="0" class="code"  ' . checked( 0,$value_freshness, false ) . ' />' ;
				_e ('Click to show full Freshness' , 'bbp-style-pack' ) ;?>
				<br/>
					<label class="description">
						<i>
						<?php _e( 'Default' , 'bbp-style-pack' ); ?>
						</i>
						<?php _e( 'e.g' , 'bbp-style-pack' ); ?>
						<code> 4 days, 5 hours ago</code>
					</label>
			
			</td>
			<td>
				<?php
				$name =  'bsp_style_settings_freshness[date_order]' ;
				$item = (!empty($bsp_style_settings_freshness['date_order']) ? $bsp_style_settings_freshness['date_order'] : '0');
				echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="0" class="code"  ' . checked( 0,$item, false ) . ' />' ;
				_e ('Date First' , 'bbp-style-pack' ) ;?>
			
			</td>
			<td>
				<?php 
				_e('Number of days back to change to date format', 'bbp-style-pack'); 
				$item1 = (!empty ($bsp_style_settings_freshness['hybrid_days_back'] ) ? $bsp_style_settings_freshness['hybrid_days_back']  : '7' ) ?>
				<input id="bsp_style_settings_freshness[hybrid_days_back]" class="small-text" name="bsp_style_settings_freshness[hybrid_days_back]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
					<label class="description" for="bsp_style_settings_freshness[date_separator]">
						<?php _e( 'Enter the number of days', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td style="width:250px;vertical-align:top">
				<?php
				
				echo '<input name="'.$item_freshness.'" id="'.$value_freshness.'" type="radio" value="1" class="code"  ' . checked( 1,$value_freshness, false ) . ' />' ;
				_e ('Click to show shortened Freshness' , 'bbp-style-pack' ) ;?>
				<br/>
				<?php _e( 'e.g' , 'bbp-style-pack' ); ?>
				<code> 4 days ago </code>
			</td>
			<td>
				<?php
				echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$item, false ) . ' />' ;
				_e ('Time First' , 'bbp-style-pack' ) ;?>
			</td>
			<td>
			<?php _e ('The freshness and date and time display will be as set in the previous two columns' , 'bbp-style-pack' ) ;?>
			</td>
		</tr>
		
		<tr valign="top">
			<td>
			</td>
			<td>
			</td>
			<td>
				<?php 
				_e('Separator', 'bbp-style-pack'); 
				$item1 = (!empty ($bsp_style_settings_freshness['date_separator'] ) ? $bsp_style_settings_freshness['date_separator']  : '' ) ?>
				<input id="bsp_style_settings_freshness[date_separator]" class="large-text" name="bsp_style_settings_freshness[date_separator]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
					<label class="description" for="bsp_style_settings_freshness[date_separator]">
						<?php _e( 'eg " at " "," ":" - do not forget to include any spaces needed', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			</td>
			<td>
			<th scope="row">
				<?php _e('Date Format', 'bbp-style-pack') ?>
			</th>
			<td>
			</td>
		</tr>
		
		<tr>
			<fieldset>
				<legend class="screen-reader-text">
					<span>
						<?php _e('Date Format' , 'bbp-style-pack') ?>
					</span>
				</legend>
				
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
				<?php
				//Filters the default date formats.
				$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
				$custom = true;
	
				$date = (!empty($bsp_style_settings_freshness['bsp_date_format']) ? $bsp_style_settings_freshness['bsp_date_format'] : '');
				$name =  'bsp_style_settings_freshness[bsp_date_format]' ;
				if ($date == 'custom')  {
					$date = (!empty($bsp_style_settings_freshness['bsp_date_format_custom']) ? $bsp_style_settings_freshness['bsp_date_format_custom'] : '');
				}
				foreach ( $date_formats as $format ) {
					echo "\t<label><input type='radio' name=".$name." value='" . esc_attr( $format ) . "'";
						if ( $date == esc_attr($format) ) { // checked() uses "==" rather than "==="
							echo " checked='checked'";
							$custom = false;
						}
					echo ' /><span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";
				}
				
				echo '<label><input type="radio" name="bsp_style_settings_freshness[bsp_date_format]" id="date_format_custom_radio" value="custom"';
				if ($custom == true) echo " checked='checked'";
					echo '/> <span class="date-time-text date-time-custom-text">' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom date format in the following field' ) . '</span></label>' .
					'<label for="date_format_custom" class="screen-reader-text">' . __( 'Custom date format:' ) . '</label>' .
					'<input type="text" name="bsp_style_settings_freshness[bsp_date_format_custom]" id="date_format_custom" value="' . $date . '" class="medium-text" /></span>' .
					"<span class='spinner'></span>\n";
				?>
			</fieldset>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			</td>
			<td>
			<th scope="row">
				<?php _e('Time Format', 'bbp-style-pack') ?>
			</th>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span>
							<?php _e('Time Format') ?>
						</span>
					</legend>
				<?php
				//Filters the default time formats.
				$time_formats = array_unique( apply_filters( 'time_formats', array( __( 'g:i a' ), 'g:i A', 'H:i' ) ) );

				$custom = true;
	
				$time = (!empty($bsp_style_settings_freshness['bsp_time_format']) ? $bsp_style_settings_freshness['bsp_time_format'] : '');
				$name =  'bsp_style_settings_freshness[bsp_time_format]' ;
				if ($time == 'custom')  {
					$time = (!empty($bsp_style_settings_freshness['bsp_time_format_custom']) ? $bsp_style_settings_freshness['bsp_time_format_custom'] : '');
				}

				foreach ( $time_formats as $format ) {
					echo "\t<label><input type='radio' name=".$name." value='" . esc_attr( $format ) . "'";
						if ( $time === $format ) { // checked() uses "==" rather than "==="
							echo " checked='checked'";
							$custom = false;
						}
					echo ' /><span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";
				}

				echo '<label><input type="radio" name="bsp_style_settings_freshness[bsp_time_format]" id="time_format_custom_radio" value="custom"';
				if ($custom == true) echo " checked='checked'";
				echo '/> <span class="date-time-text date-time-custom-text">' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom time format in the following field' ) . '</span></label>' .
				'<label for="time_format_custom" class="screen-reader-text">' . __( 'Custom time format:' ) . '</label>' .
				'<input type="text" name="bsp_style_settings_freshness[bsp_time_format_custom]" value="' . $time . '" class="medium-text" /></span>' .
				"<span class='spinner'></span>\n";

				echo "\t<p class='date-time-doc'>" . __('<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">');
				_e('Documentation on date and time formatting', 'bbp-style-pack');
				echo '</a>.'.'</p>';
				?>
			</fieldset>
			</td>
		</tr>
		<?php if ( (get_locale() != 'en_GB' && get_locale() != 'en_US') && $bsp_bbpress_version == '2.6' ) { ?>
		<tr>
			<th>
				7.<?php _e('Freshness Translation', 'bbp-style-pack'); ?> 
			</th>
			</tr>
			<tr>
			<td colspan=2>
			<?php 	_e ('In bbpress 2.6.x the date freshness format has changed.' , 'bbp-style-pack') ;
				_e ('<br>If your translation has not been updated this will still display in English' , 'bbp-style-pack') ;
				_e ('<br>You can enter your language translations here', 'bbp-style-pack') ; 
			?>
			</td>
			
		</tr>
		<tr>
		<td>
		<?php echo 'year' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['year'] ) ? $bsp_style_settings_freshness['year']  : '' ) ?>
				<input id="bsp_style_settings_freshness[year]" class="large-text" name="bsp_style_settings_freshness[year]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'years' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['years'] ) ? $bsp_style_settings_freshness['years']  : '' ) ?>
				<input id="bsp_style_settings_freshness[years]" class="large-text" name="bsp_style_settings_freshness[years]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'month' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['month'] ) ? $bsp_style_settings_freshness['month']  : '' ) ?>
				<input id="bsp_style_settings_freshness[month]" class="large-text" name="bsp_style_settings_freshness[month]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'months' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['months'] ) ? $bsp_style_settings_freshness['months']  : '' ) ?>
				<input id="bsp_style_settings_freshness[months]" class="large-text" name="bsp_style_settings_freshness[months]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'week' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['week'] ) ? $bsp_style_settings_freshness['week']  : '' ) ?>
				<input id="bsp_style_settings_freshness[week]" class="large-text" name="bsp_style_settings_freshness[week]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'weeks' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['weeks'] ) ? $bsp_style_settings_freshness['weeks']  : '' ) ?>
				<input id="bsp_style_settings_freshness[weeks]" class="large-text" name="bsp_style_settings_freshness[weeks]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'day' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['day'] ) ? $bsp_style_settings_freshness['day']  : '' ) ?>
				<input id="bsp_style_settings_freshness[day]" class="large-text" name="bsp_style_settings_freshness[day]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'days' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['days'] ) ? $bsp_style_settings_freshness['days']  : '' ) ?>
				<input id="bsp_style_settings_freshness[days]" class="large-text" name="bsp_style_settings_freshness[days]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'hour' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['hour'] ) ? $bsp_style_settings_freshness['hour']  : '' ) ?>
				<input id="bsp_style_settings_freshness[hour]" class="large-text" name="bsp_style_settings_freshness[hour]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'hours' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['hours'] ) ? $bsp_style_settings_freshness['hours']  : '' ) ?>
				<input id="bsp_style_settings_freshness[hours]" class="large-text" name="bsp_style_settings_freshness[hours]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'minute' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['minute'] ) ? $bsp_style_settings_freshness['minute']  : '' ) ?>
				<input id="bsp_style_settings_freshness[minute]" class="large-text" name="bsp_style_settings_freshness[minute]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'minutes' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['minutes'] ) ? $bsp_style_settings_freshness['minutes']  : '' ) ?>
				<input id="bsp_style_settings_freshness[minutes]" class="large-text" name="bsp_style_settings_freshness[minutes]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'second' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['second'] ) ? $bsp_style_settings_freshness['second']  : '' ) ?>
				<input id="bsp_style_settings_freshness[second]" class="large-text" name="bsp_style_settings_freshness[second]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo 'seconds' ; ?>
		</td>
		<td>
			<?php 
				$item = (!empty ($bsp_style_settings_freshness['seconds'] ) ? $bsp_style_settings_freshness['seconds']  : '' ) ?>
				<input id="bsp_style_settings_freshness[seconds]" class="large-text" name="bsp_style_settings_freshness[seconds]" type="text" value="<?php echo esc_html( $item ) ;?>" /><br/>
		</td>
		</tr>
		<?php }  ?>
	
	</table>
					
<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
</form>
	 
<?php
}
		

	
