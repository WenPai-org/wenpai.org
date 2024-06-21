<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//topic preview style settings page

function bsp_style_settings_topic_preview () {
	global $bsp_style_settings_topic_preview ;
	?> 
	<form method="post" action="options.php">
		<?php wp_nonce_field( 'style-settings_topic_preview', 'style-settings-nonce' ) ?>
		<?php settings_fields( 'bsp_style_settings_topic_preview' );
		//create a style.css on entry and on saving
		generate_style_css();
                bsp_clear_cache();
		?>
		<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Topic Preview' , 'bbp-style-pack' ) ; ?>
				</h3>
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
					<?php _e('This section allows you to create a topic preview - when you hover on a topic in a forum list, the first x words of the topic are displayed.', 'bbp-style-pack'); ?> 
				</p>
				<p>
					<?php _e('This saves your community needing to open each topic to see what it is about.', 'bbp-style-pack'); ?> 
				</p>
			</td>
			
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/topic_preview.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>

	<hr>
	
	<table class="form-table">
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th>
				1. <?php _e('Activate Topic Previews', 'bbp-style-pack'); ?>
			</th>
			
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_topic_preview['activate'] ) ?  $bsp_style_settings_topic_preview['activate'] : '');
				echo '<input name="bsp_style_settings_topic_preview[activate]" id="bsp_style_settings_topic_preview[activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
			</td>
		</tr>
						
		
			
	<!-- TOPIC PREVIEW STYLING -->					
		<tr>	
			<th>
				<?php _e('Style', 'bbp-style-pack'); ?>
			</th>
		</tr>
		
		<tr>
			<td colspan=2>
				<?php _e('You can style the preview below' , 'bbp-style-pack'); ?>
			</td>
			<?php
			$item =  'bsp_style_settings_topic_preview[button_type]' ;
			$item1 = (!empty($bsp_style_settings_topic_preview['button_type']) ? $bsp_style_settings_topic_preview['button_type'] : 1); 
			?>
				
			<?php 
			$name = ('preview') ;
			$name0 = __('Preview', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$name5 = __('Background Color', 'bbp-style-pack') ;
			$name6 = __('Width of Preview box', 'bbp-style-pack') ;
			$name7 = __('Height of Preview box', 'bbp-style-pack') ;
			$name8= __('Number of characters', 'bbp-style-pack') ;
			$name9 = __('Screen Width', 'bbp-style-pack') ;
			$name10 = __('Width of Preview box', 'bbp-style-pack') ;
			$name11= __('Height of Preview box', 'bbp-style-pack') ;
			$name12= __('Number of characters', 'bbp-style-pack') ;
			
						
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='FontStyle';
			$area5='background color';
			$area6='width';
			$area7='height';
			$area8='chars';
			$area9='mscreen';
			$area10='mwidth';
			$area11='mheight';
			$area12='mchars';
		
						
			$item1="bsp_style_settings_topic_preview[".$name.$area1."]" ;
			$item2="bsp_style_settings_topic_preview[".$name.$area2."]" ;
			$item3="bsp_style_settings_topic_preview[".$name.$area3."]" ;
			$item4="bsp_style_settings_topic_preview[".$name.$area4."]" ;
			$item5="bsp_style_settings_topic_preview[".$name.$area5."]" ;
			$item6="bsp_style_settings_topic_preview[".$name.$area6."]" ;
			$item7="bsp_style_settings_topic_preview[".$name.$area7."]" ;
			$item8="bsp_style_settings_topic_preview[".$name.$area8."]" ;
			$item9="bsp_style_settings_topic_preview[".$name.$area9."]" ;
			$item10="bsp_style_settings_topic_preview[".$name.$area10."]" ;
			$item11="bsp_style_settings_topic_preview[".$name.$area11."]" ;
			$item12="bsp_style_settings_topic_preview[".$name.$area12."]" ;
			
			
			
			$value1 = (!empty($bsp_style_settings_topic_preview[$name.$area1]) ? $bsp_style_settings_topic_preview[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_topic_preview[$name.$area2]) ? $bsp_style_settings_topic_preview[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_topic_preview[$name.$area3]) ? $bsp_style_settings_topic_preview[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_topic_preview[$name.$area4]) ? $bsp_style_settings_topic_preview[$name.$area4]  : 'Normal') ;
			$value5 = (!empty($bsp_style_settings_topic_preview[$name.$area5]) ? $bsp_style_settings_topic_preview[$name.$area5]  : '') ;
			$value6 = (!empty($bsp_style_settings_topic_preview[$name.$area6]) ? $bsp_style_settings_topic_preview[$name.$area6]  : '') ;
			$value7 = (!empty($bsp_style_settings_topic_preview[$name.$area7]) ? $bsp_style_settings_topic_preview[$name.$area7]  : '') ;
			$value8 = (!empty($bsp_style_settings_topic_preview[$name.$area8]) ? $bsp_style_settings_topic_preview[$name.$area8]  : '') ;
			$value9 = (!empty($bsp_style_settings_topic_preview[$name.$area9]) ? $bsp_style_settings_topic_preview[$name.$area9]  : '') ;
			$value10 = (!empty($bsp_style_settings_topic_preview[$name.$area10]) ? $bsp_style_settings_topic_preview[$name.$area10]  : '') ;
			$value11 = (!empty($bsp_style_settings_topic_preview[$name.$area11]) ? $bsp_style_settings_topic_preview[$name.$area11]  : '') ;
			$value12 = (!empty($bsp_style_settings_topic_preview[$name.$area12]) ? $bsp_style_settings_topic_preview[$name.$area12]  : '') ;
			
			
			
			?>
			
		<tr>
			<td>
				<?php echo $name1 ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 10px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php echo $name2 ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.$item2.'" class="bsp-color-picker" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php echo $name3 ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
			
		<tr>
			<td> 
				<?php echo $name4 ; ?>
			</td>
			
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
		<tr>
			<td>
				<?php echo $name5 ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.$item5.'" class="bsp-color-picker" name="'.$item5.'" type="text" value="'.esc_html( $value5 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td colspan=2>
				<b><p>
					<?php _e('By default, the preview will show the content of the topic, but without images or links.', 'bbp-style-pack'); ?> 
				</p>
				<p>
					<?php _e('The defaults below work for many sites, <i>so you may not need to set anything below </i>, but you may want to adjust them for your site - to say limit what is shown.', 'bbp-style-pack'); ?> 
				</p>
				</b>
			</td>
		</tr>
		
		<tr>
			<td colspan=2>
				<p><b>
					<?php _e('Standard display', 'bbp-style-pack'); ?> 
				</b></p>
				
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name6 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item6.'" class="medium-text" name="'.$item6.'" type="text" value="'.esc_html( $value6 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Width of preview - default 400px', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name7 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item7.'" class="medium-text" name="'.$item7.'" type="text" value="'.esc_html( $value7 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Height of preview - by default this will adjust for the amount of content, but you can set a fixed size instead', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name8 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item8.'" class="medium-text" name="'.$item8.'" type="text" value="'.esc_html( $value8 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Number of characters to show in Preview - default will show all the content', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td colspan=2>
				<p>
					<b>
						<?php _e('Smaller display', 'bbp-style-pack'); ?> 
					</b>
				</p>
				
			</td>
		</tr>
		
		<tr>
			<td colspan=2>
				<p>
					<?php _e('You may want to alter the parameters for smaller screens - eg mobiles etc. <i>You do not need to set these unless you want a different display. </i> ', 'bbp-style-pack'); ?>
				</p>
				<p>
					<?php _e('Typical widths are 320px — 480px for mobile devices, and 481px — 768px for iPads, Tablets etc.', 'bbp-style-pack'); ?>
				</p>	
					<p>
					<?php _e('Set the maximum width and screens below this width will use the settings below.', 'bbp-style-pack'); ?>
				</p>	
					
					
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name9 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item9.'" class="medium-text" name="'.$item9.'" type="text" value="'.esc_html( $value9 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Screen width - below which these settings take effect', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name10 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item10.'" class="medium-text" name="'.$item10.'" type="text" value="'.esc_html( $value10 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Width of preview - default 400px', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td> 
				<?php echo $name11 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item11.'" class="medium-text" name="'.$item11.'" type="text" value="'.esc_html( $value11 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Height of preview - by default this will adjust for the amount of content, but you can set a fixed size instead', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		
		<tr>
			<td> 
				<?php echo $name12 ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.$item12.'" class="medium-text" name="'.$item12.'" type="text" value="'.esc_html( $value12 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Number of characters to show in Preview - default will show all the content', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
			
		
		
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	</form>

<?php
}
