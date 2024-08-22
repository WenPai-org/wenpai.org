<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//topic/repy styling tab

function bsp_style_settings_t () {
	global $bsp_style_settings_t ;
	global $bsp_bbpress_version ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-t', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_t' );
	//create a style.css on entry and on saving
	generate_style_css();
	//and for this tab, a js delete file if needed
	if (!empty ($bsp_style_settings_t['participant_trash_topic_confirm'])|| !empty ($bsp_style_settings_t['participant_trash_reply_confirm'] ) ){
		generate_delete_js();
	}
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Topic/Reply Display' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to amend the topic/reply section.', 'bbp-style-pack'); ?>
				</p>
				<p>
					<?php _e('You only need to enter those styles and elements within a style that you wish to alter', 'bbp-style-pack'); ?>
				</p>
			</td>
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/topic.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
	

<!--1. Topic/Reply Content background ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic/Reply Content' ;
			$name0 = __('Topic/Reply Content', 'bbp-style-pack') ;
			$name1 = __('Background color - odd numbers', 'bbp-style-pack') ;
			$name2 = __('Background color - even numbers', 'bbp-style-pack') ;
			$area1='Background color - odd numbers' ;
			$area2='Background color - even numbers' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '#fff') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '#fbfbfb') ;
			?>
			<th>
				<?php echo '1. '.$name0 ?>
			</th>
			<td style="vertical-align: top;">
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
					<label class="description">
						<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                                <p>
                                                <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                                <?php _e( '#fff', 'bbp-style-pack' ); ?>
                                                </p>
					</label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td style="vertical-align: top;"> 
				<?php echo $name2 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="bsp-color-picker" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                        <p>
                                        <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                        <?php _e( '#fbfbfb', 'bbp-style-pack' ); ?>
                                        </p>
				</label><br/>
			</td>
		</tr>
			
<!--2. Topic/Reply header background ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic/Reply Header' ;
			$name0 = __('Topic/Reply Header', 'bbp-style-pack') ;
			$name1 = __('Background color', 'bbp-style-pack') ;
			$area1='Background color' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '#f4f4f4') ;
			?>
			<th>
				<?php echo '2. '.$name0 ?>
			</th>
			<td style="vertical-align: top;"> 
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                        <p>
                                        <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                        <?php _e( '#f4f4f4', 'bbp-style-pack' ); ?>
                                        </p>
				</label><br/>
			</td>
		</tr>
		
<!--3. Trash/spam background color ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Trash/Spam Content' ;
			$name0 = __('Trash/Spam Content', 'bbp-style-pack') ;
			$name1 = __('Background color - odd numbers', 'bbp-style-pack') ;
			$name2 = __('Background color - even numbers', 'bbp-style-pack') ;
			$area1='Background color - odd numbers' ;
			$area2='Background color - even numbers' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '#fdd') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '#fee') ;
			?>
			<th>
				<?php echo '3. '.$name0 ?>
			</th>
			<td style="vertical-align: top;"> 
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                        <p>
                                        <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                        <?php _e( '#fdd', 'bbp-style-pack' ); ?>
                                        </p>
				</label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td style="vertical-align: top;"> 
				<?php echo $name2 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="bsp-color-picker" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                        <p>
                                        <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                        <?php _e( '#fee', 'bbp-style-pack' ); ?>
                                        </p>
				</label><br/>
			</td>
		</tr>

<!--4. Closed background color ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = 'Closed Topic Content' ;
			$name0 = __('Closed Topic Content', 'bbp-style-pack') ;
			$name1 = __('Background color', 'bbp-style-pack') ;
			$area1='Background color' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '#fdd') ;
			?>
			<th>
				<?php echo '4. '.$name0 ?>
			</th>
			<td style="vertical-align: top;">
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                        <p>
                                        <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                        <?php _e( '#ccc', 'bbp-style-pack' ); ?>
                                        </p>
				</label><br/>
			</td>
		</tr>
			
<!--5. Font - topic/reply date  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic/Reply Date Font' ;
			$name0 = __('Topic/Reply Date Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '5. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name2 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="bsp-color-picker" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
				</label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
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
			
<!--6. Font - topic/reply text font  ------------------------------------------------------------------->
			
			<tr>
			<?php 
			$name = 'Topic/Reply Text Font' ;
			$name0 = __('Topic/Reply Text Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '6. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name2; ?>
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
			</td>
			<td> 
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?>
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
<!--7. Font - Author name ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Author Name Font' ;
			$name0 = __('Author Name Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '7. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?>
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
<!--8. Font - Reply Permalink ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Reply Link Font' ;
			$name0 = __('Reply Link Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '8. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?> 
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
<!--9. Font - author role ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Author Role' ;
			$name0 = __('Author Role', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '9. '.$name0 ?>
			</th>
			<td colspan=2>
				<?php _e( 'NOTE : You can also style Author roles and it\'s position more fully using the \'Forum Roles\' Tab', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td> 
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
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
			</td>
			<td>
				<?php echo $name3 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?>
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
<!--10. topic header - author and posts ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic Header' ;
			$name0 = __('Topic Header', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '10. '.$name0 ?>
			</th>
			<td> 
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
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
			</td>
			<td>
				<?php echo $name3 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?>
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
		
<!--11. topic admin links ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic Admin links' ;
			$name0 = __('Topic Admin links', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '11. '.$name0 ?>
			</th>
			<td> 
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
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
			</td>
			<td> 
				<?php echo $name3 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?>
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
<!--12. Revisions ------------------------------------------------------------------->		
		<tr>
			<?php
			$name = 'Revisions' ;
			$name0 = __('Revisions', 'bbp-style-pack') ;
			$area1='revisions' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : 'all') ;
			?>
			<th>
				<?php echo '12. '.$name0 ?>
			</th>
			<td colspan = 2>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<p>' ; ?> 
				<label class="description">
					<?php _e( 'Type "all" to show all revisions, "none" to hide all revisions, or a number to show the last n revisions eg "1" to just show the last revision.', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>

<!--13. @mentions ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'mentions' ;
			$name0 = __('@Mentions', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$item2="bsp_style_settings_t[".$name.$area2."]" ;
			$item3="bsp_style_settings_t[".$name.$area3."]" ;
			$item4="bsp_style_settings_t[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_t[$name.$area2]) ? $bsp_style_settings_t[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_t[$name.$area3]) ? $bsp_style_settings_t[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_t[$name.$area4]) ? $bsp_style_settings_t[$name.$area4]  : '') ;
			?>
			<th>
			<?php echo '13. '.$name0 ?>
			</th>
			<!-- checkbox to activate  -->
			<td>
				<?php _e('Activate @mentions Description', 'bbp-style-pack'); ?>
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_t['mentionsactivate'] ) ?  $bsp_style_settings_t['mentionsactivate'] : '');
				echo '<input name="bsp_style_settings_t[mentionsactivate]" id="bsp_style_settings_t[mentionsactivate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ; ?>
			</td>
		</tr>
		
		<tr valign="top">
			<td colspan=3>
				<label class="description" for="bsp_settings_t[mentionsactivate]">
					<?php _e( 'NOTES ', 'bbp-style-pack' ); ?>
					<br/>
					<?php _e( '1. This ONLY adds the @mention description shown above for the user.  @mentions is available with Buddypress or by using the <a href = "https://wordpress.org/plugins/bbp-mentions-email-notifications/" target="_blank"> bbp-mentions-email-notifications</a> plugin, and either will need to be activated for this to be useful !', 'bbp-style-pack' ); ?>
					<br/>
					<?php _e( '2. This will show the user\'s \'nicename\' which may be different from the display name shown above it. Users can amend their display names in their profile settings or the admin may decide this. Whilst for most sites this is not an issue, you will want to consider whether showing their nicename which is formed from their login name may compromise privacy or reveal information. ', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr valign="top">
			<td>
			</td>
			<td><?php _e('Priority', 'bbp-style-pack'); ?>
			</td>
			<td>
				<?php 
				$item_priority = (!empty ($bsp_style_settings_t['mentions_priority'] ) ? $bsp_style_settings_t['mentions_priority']  : '' ) ?>
				<input id="bsp_style_settings_t[mentions_priority]" class="small-text" name="bsp_style_settings_t[mentions_priority]" type="text" value="<?php echo esc_html( $item_priority ) ;?>" /><br/>
					<label class="description" for="bsp_settings_t[mentions_priority]">
						<?php _e( 'Default : 10  Leave blank unless you want to alter. Explanation: If you have multiple items displaying under the author from say other plugins, then you can use this to change the display order - lower numbers equal higher priority', 'bbp-style-pack' ); ?>
						</label>
						<br/>
			</td>
		</tr>
			
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
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
			</td>
			<td> 
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?> 
			</td>
			<td>
				<select name="<?php echo $item4 ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
		
<!--14. anon emails ------------------------------------------------------------------->	
		<tr>
			<?php 
			$name = 'anon_email' ;
			$name0 = __('Show emails for anonymous users', 'bbp-style-pack') ;
			$area1='Show' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1]) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			?>
			<th>
			<?php echo '14. '.$name0 ?>
			</th>
			<!-- checkbox to activate  -->
			<td>
				<?php _e('Show Anonymous Email addresses', 'bbp-style-pack'); ?>
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_t['anon_emailShow'] ) ?  $bsp_style_settings_t['anon_emailShow'] : 0);
				echo '<input name="bsp_style_settings_t[anon_emailShow]" id="bsp_style_settings_t[anon_emailShow]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ; ?>
				<label class="description">
					<?php _e( 'If you allow unregistered users to post, this will show the email address they entered in the topic/reply form to KEYMASTERS only ', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
<!--15. ------------------------------------------------------------------->			
		<tr>
			<th>
				15.<?php _e('Hide Author Name', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_t[hide_name]' ;
				$item1 = (!empty($bsp_style_settings_t['hide_name']) ? $bsp_style_settings_t['hide_name'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to hide name', 'bbp-style-pack');
				?>
			</td>
		</tr>
<!--16.  ------------------------------------------------------------------->			
		<tr>
			<th>
				16.<?php _e('Hide Author Avatar', 'bbp-style-pack'); ?> 
			</th>
			<td>
				<?php
				$item =  'bsp_style_settings_t[hide_avatar]' ;
				$item1 = (!empty($bsp_style_settings_t['hide_avatar']) ? $bsp_style_settings_t['hide_avatar'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Click to hide avatar', 'bbp-style-pack');
				?>
			</td>
		</tr>
<!--17.  ------------------------------------------------------------------->		
		<tr>
			<th>
				17.<?php _e('Participant Close Topics', 'bbp-style-pack'); ?> 
			</th>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_close_topic]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_close_topic']) ? $bsp_style_settings_t['participant_close_topic'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Allow Participants to close their own topics', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		
		
		
		
		
		
<!--18.  ------------------------------------------------------------------->		
		<tr>
			<th>
				18.<?php _e('Participant Trash Topics', 'bbp-style-pack'); ?> 
			</th>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_topic]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_topic']) ? $bsp_style_settings_t['participant_trash_topic'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Allow Participants to trash their own topics', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_topic_confirm]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_topic_confirm']) ? $bsp_style_settings_t['participant_trash_topic_confirm'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Add an \'are you sure?\' confirm message', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_topic_text]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_topic_text']) ? $bsp_style_settings_t['participant_trash_topic_text'] : 'Are you sure you want to delete this topic?');
				echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter the confirm message - Default : Are you sure you want to delete this topic?', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>

<!--19.  ------------------------------------------------------------------->		
		<tr>
			<th>
				19.<?php _e('Participant Trash Replies', 'bbp-style-pack'); ?> 
			</th>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_reply]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_reply']) ? $bsp_style_settings_t['participant_trash_reply'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Allow Participants to trash their own replies', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_reply_confirm]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_reply_confirm']) ? $bsp_style_settings_t['participant_trash_reply_confirm'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Add an \'are you sure?\' confirm message', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[participant_trash_reply_text]' ;
				$item1 = (!empty($bsp_style_settings_t['participant_trash_reply_text']) ? $bsp_style_settings_t['participant_trash_reply_text'] : 'Are you sure you want to delete this reply?');
				echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Enter the confirm message - Default : Are you sure you want to delete this reply?', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
			
<!--20. --Change 'you must be logged in ' message---------------------------------------------------------------------->
		<tr>
			<th>20.. 
				<?php _e ('Change "you must be logged in..." message' , 'bbp-style-pack' ) ; ?>
			</th>
		<?php 
		$name = 'must_be_logged_in' ;
		$item1="bsp_style_settings_t[".$name."]" ;
		$value1 = (!empty($bsp_style_settings_t[$name] ) ? $bsp_style_settings_t[$name]  : '') ;
		?>
		<td colspan=2>
			<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'Default "You must be logged in to reply to this topic." Enter the words you want', 'bbp-style-pack' ); ?></label><br/>
			</td>
			</tr>
			
		
<!--21. Click to add login---------------------------------------------------------------------->
	
		<tr>
			<th>21. 
				<?php _e ('Add login link' , 'bbp-style-pack' ) ; ?>
			</th>
			<?php
			$name = 'add_login' ;
			$name1 = __('Login link description', 'bbp-style-pack') ;
			$area1='_login' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1] ) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			
		
			//put in option if version 2.5
			if ($bsp_bbpress_version == '2.5') {
			?>
			<td>
			<?php
			echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />';
			_e ('Click to activate' , 'bbp-style-pack' ) ;
  			?>
		</td>
		<?php
		}
		elseif ($bsp_bbpress_version == '2.6') {
		?>
		<td colspan=2>
		<?php
			_e ('In bbpress 2.6 users are shown a login form if not logged in, so this option is not available' , 'bbp-style-pack' ) ;
		?>
		</td>
		<?php
		}
		
		?>
		</tr>
		<?php 
		//put in option if version 2.5
			if ($bsp_bbpress_version == '2.5') {
		?>
		
		<tr>
		<?php 
		$name = 'login_page' ;
		$name1 = __('Login Description', 'bbp-style-pack') ;
		$area1='_page' ;
		$item1="bsp_style_settings_t[".$name.$area1."]" ;
		$value1 = (!empty($bsp_style_settings_t[$name.$area1] ) ? $bsp_style_settings_t[$name.$area1]  : '') ;
		$name2 = __('Login page', 'bbp-style-pack') ;
		$area2='_url' ;
		$item2="bsp_style_settings_t[".$name.$area2."]" ;
		$value2 = (!empty($bsp_style_settings_t[$name.$area2] ) ? $bsp_style_settings_t[$name.$area2]  : '') ;
		?>
		
		
		<td>
			<?php echo $name1 ; ?>
		</td>
		
		<td colspan=2>
			<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'Default "Login" Enter the words you want eg "log in", "sign in" etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
			</tr>
			<tr>
			<td>
			<?php echo $name2 ; ?>
		</td>
			
			<td colspan=2>
			<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'You should create a wordpress page with a login shortcode such as [bbp-login] and put the full url in here e.g. http://www.mysite.com/loginpage. If left blank the default wordpress login page will be used.', 'bbp-style-pack' ); ?></label><br/>
		</td>
	</tr>
	<?php
			} //end of if 2.5
		
	?>
	
<!--22. Click to add register---------------------------------------------------------------------->
		

		<tr>
			<th>22. 
				<?php _e ('Add register link' , 'bbp-style-pack' ) ; ?>
			</th>
			<?php
			$name = 'add_register' ;
			$name1 = __('register link description', 'bbp-style-pack') ;
			$area1='_register' ;
			$item1="bsp_style_settings_t[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_t[$name.$area1] ) ? $bsp_style_settings_t[$name.$area1]  : '') ;
			
			//put in option if version 2.5
			if ($bsp_bbpress_version == '2.5') {
			?>
			<td>
			<?php
			echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />';
			_e ('Click to activate' , 'bbp-style-pack' ) ;
  			?>
		</td>
		<?php
		}
		elseif ($bsp_bbpress_version == '2.6') {
		?>
		<td colspan = 2>
		<?php
			_e ('In bbpress 2.6 users are shown a login form to which you can add a register link.  You can set a register option in ' , 'bbp-style-pack' ) ;
			echo '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=topic_index_styling">' ;
			_e ('Topics Index Styling - item 19' , 'bbp-style-pack' ) ;
			echo '</a>' ;

		?>
		</td>
		<?php
		}
		?>
		
		
		
		
		</tr>
		<?php 
		//put in option if version 2.5
			if ($bsp_bbpress_version == '2.5') {
		?>
		<tr>
		<?php 
		$name = 'register_page' ;
		$name1 = __('Register Description', 'bbp-style-pack') ;
		$area1='_page' ;
		$item1="bsp_style_settings_t[".$name.$area1."]" ;
		$value1 = (!empty($bsp_style_settings_t[$name.$area1] ) ? $bsp_style_settings_t[$name.$area1]  : '') ;
		$name2 = __('Register page', 'bbp-style-pack') ;
		$area2='_url' ;
		$item2="bsp_style_settings_t[".$name.$area2."]" ;
		$value2 = (!empty($bsp_style_settings_t[$name.$area2] ) ? $bsp_style_settings_t[$name.$area2]  : '') ;
		?>
		
		
		<td>
			<?php echo $name1 ; ?>
		</td>
		
		<td colspan=2>
			<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'Default "register" Enter the words you want eg "log in", "sign in" etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
			</tr>
			<tr>
			<td>
			<?php echo $name2 ; ?>
		</td>
			
			<td colspan=2>
			<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'You should create a wordpress page with a register shortcode such as [bbp-register] and put the full url in here e.g. http://www.mysite.com/registerpage. If left blank the default wordpress login page will be used.', 'bbp-style-pack' ); ?></label><br/>
		</td>
	</tr>
	<?php
			} //end of if 2.5
		
	?>
<!--23. links in new window---------------------------------------------------------------------->
	<tr>
			<th>23. 
				<?php _e ('Open Links in new Window' , 'bbp-style-pack' ) ; ?>
			</th>
	<?php	
		$item =  'bsp_style_settings_t[window_links]' ;
		$item1 = (!empty($bsp_style_settings_t['window_links']) ? $bsp_style_settings_t['window_links'] : 0);
	?>
	<td colspan=2>
				<?php
				echo '<input name="'.$item.'" id="'.$item1.'" type="radio" value="0" class="code" ' . checked( 0,$item1, false ) . ' /> ';
				_e ('Default' , 'bbp-style-pack' ) ; ?>
				<br>
				<label class="description">
					<?php _e( '<i>Whether the links open in a new window will depend on how the author added the link</i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td colspan=2>
				<?php
				echo '<input name="'.$item.'" id="'.$item1.'" type="radio" value="1" class="code" ' . checked( 1,$item1, false ) . ' /> ';
				_e ('All Links to open in new window' , 'bbp-style-pack' ) ; ?>
				<br>
				<label class="description">
					<?php _e( '<i>This should make all links open in a new window</i>' , 'bbp-style-pack' ); ?>
				</label>
				
			</td>
		</tr>
	
<!--24.  ------------------------------------------------------------------->		
		<tr>
			<th>
				24. <?php _e('Show Reply more/less', 'bbp-style-pack'); ?> 
			</th>
			
			<td colspan=2>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/reply-more.png',dirname(__FILE__)  ) . '"  width="50%"> '; ?>
			</td>
			</tr>
			<tr>
			<td></td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[more_less]' ;
				$item1 = (!empty($bsp_style_settings_t['more_less']) ? $bsp_style_settings_t['more_less'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Show only part of a reply when the reply is long, along with a more link to show all the reply', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[more_less_length]' ;
				$item1 = (!empty($bsp_style_settings_t['more_less_length']) ? $bsp_style_settings_t['more_less_length'] : '200');
				echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Length of text before "more", Default 200 characters', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[more_text]' ;
				$item1 = (!empty($bsp_style_settings_t['more_text']) ? $bsp_style_settings_t['more_text'] : 'More...');
				echo '<input id="'.$item.'" class="medium-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'More... text', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[less_text]' ;
				$item1 = (!empty($bsp_style_settings_t['less_text']) ? $bsp_style_settings_t['less_text'] : 'Less...');
				echo '<input id="'.$item.'" class="medium-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Less... text', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>
		
<!--25. Create New Reply Button ------------------------------------------------------------------->		
		<tr>
			<th>
				25. <?php _e('Show Create New Reply button', 'bbp-style-pack'); ?> 
			</th>
			
			<td colspan=2>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/reply_button.png',dirname(__FILE__)  ) . '"  width="50%"> '; ?>
			</td>
			</tr>
			<tr>
			<td></td>
			<td colspan=2>
			<?php
			_e('This button will take any Button style settings in the \'Forum Buttons\' Tab', 'bbp-style-pack');
			?>
			</td>
			</tr>
			<tr>
			<td></td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[new_reply_activate]' ;
				$item1 = (!empty($bsp_style_settings_t['new_reply_activate']) ? $bsp_style_settings_t['new_reply_activate'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Activate Button', 'bbp-style-pack');
				?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td colspan=2>
				<?php
				$item =  'bsp_style_settings_t[new_reply_description]' ;
				$item1 = (!empty($bsp_style_settings_t['new_reply_description']) ? $bsp_style_settings_t['new_reply_description'] : 'Create New Reply');
				echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Text for button.  Allowable codes - {topic_name} {forum_name}', 'bbp-style-pack' ); ?>
					</label>
					<br/>
			</td>
		</tr>

<!--26. Topic Button Styling ------------------------------------------------------------------->                
                        <tr>	
                                <th>
                                        26. <?php _e('Topic Buttons Style', 'bbp-style-pack'); ?> 
                                </th>
                                <td colspan="2">
                                        <?php _e('Topic favorite/subscribe links can be styled separately here. You can keep them as default bbPress links (no styling added), use the button styling specified in the "Forum Buttons" tab, or create a button class (see help further down)', 'bbp-style-pack'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td style="vertical-align:top;">
                                        <?php
                                        $item = 'bsp_style_settings_t[topic_button_type]';
                                        $item1 = (!empty($bsp_style_settings_t['topic_button_type']) ? $bsp_style_settings_t['topic_button_type'] : 1); 
                                        echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1, $item1, false ) . ' />';
                                        _e('No style' , 'bbp-style-pack' );
                                        ?>
                                        <br>
                                        <label class="description">
                                                <i><?php _e( '(Use the default bbPress link.)' , 'bbp-style-pack' ); ?></i>
                                        </label>
                                </td>
                                <td style="vertical-align:top;">
                                        <?php
                                        echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2, $item1, false ) . ' />';
                                        _e('Use "Forum Buttons" style' , 'bbp-style-pack' );
                                        ?>
                                        <br>
                                        <label class="description">
                                                <i><?php _e( '(Use the forum button style values from "Forum Buttons".)' , 'bbp-style-pack' ); ?></i>
                                        </label>
                                </td>
                                <td style="vertical-align:top;">
                                        <?php
                                        echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="3" class="code"  ' . checked( 3, $item1, false ) . ' />';
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
                        <tr>
                                <td style="vertical-align:top;">
                                        <?php 
                                        $name = 'Class';
                                        $item = 'bsp_style_settings_t[TopicButtonclass]';
                                        $item1 = (!empty($bsp_style_settings_t['TopicButtonclass']) ? $bsp_style_settings_t['TopicButtonclass'] : ''); 
                                        echo $name; ?>
                                </td>
                                <td colspan="2">
                                        <?php echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'" /><br>'; ?> 
                                        <label class="description">
                                                <?php _e( 'If you have selected "Use Class" above,  then enter the class.', 'bbp-style-pack' ); ?><br/></br>
                                                <?php _e( 'See "Custom CSS Suggestions" at the bottom of this page for additional help with applying your custom class.', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                

<!--27. Subscribe Button Separator ------------------------------------------------------------------->                
                        <tr valign="top">	
                                <th>
                                        27. <?php _e('Subscribe Button Separator', 'bbp-style-pack'); ?> 
                                </th>
                                <td colspan="2">
                                        <p>
                                                <?php _e( 'bbPress adds a prefix to the topic subscribe link/button to separate it from the favorites link/button. You can change that prefix here.', 'bbp-style-pack' ); ?>
                                        </p>
                                        <br/>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_t['activate_topic_subscribe_button_prefix'] ) ?  $bsp_style_settings_t['activate_topic_subscribe_button_prefix'] : '');
                                        echo '<input name="bsp_style_settings_t[activate_topic_subscribe_button_prefix]" id="bsp_style_settings_t[activate_topic_subscribe_button_prefix]" type="checkbox" value="1" class="code" ' . checked( 1, $item, false ) . ' />';
                                        ?>
                                        <label class="description" for="bsp_style_settings_t[activate_topic_subscribe_button_prefix]">
                                                <?php _e( 'Check to customize the topic subscribe button prefix (specify below).', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td></td>
                                <td colspan="2">
                                        <?php 
                                        $item1 = ( ! empty ($bsp_style_settings_t['topic_subscribe_button_prefix'] ) ? htmlspecialchars( $bsp_style_settings_t['topic_subscribe_button_prefix'] ) : '' ); 
                                        ?>
                                        <input id="bsp_style_settings_t[topic_subscribe_button_prefix]" class="large-text" name="bsp_style_settings_t[topic_subscribe_button_prefix]" type="text" value="<?php echo $item1; ?>" /><br/>
                                        <p>
                                                <?php 
                                                        echo sprintf(
                                                                /* translators: %1$s and %2$s are opening/closing <a href> HTML tags */
                                                                __( 'You can use any keyboard character(s), or any %1$sASCII HTML Name or HTML Number%2$s code(s).', 'bbp-style-pack' ),
                                                                '<a href="https://www.ascii-code.com/" target="_blank">',
                                                                '</a>'
                                                        );
                                                ?>
                                        </p>
                                        <label class="description" for="bsp_style_settings_t[topic_subscribe_button_prefix]">
                                                <?php 
                                                        echo sprintf(
                                                                /* translators: %1$s is a string of ASCII characters and keyboard characters, %2$s shows the rendering of those characters */
                                                                __( 'Default : %1$s (which get rendered on the front-end as "%2$s")', 'bbp-style-pack' ),
                                                                htmlspecialchars( '&nbsp;|&nbsp;' ),
                                                                ' | '
                                                        );
                                                ?>
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
        <br/>
        <p>
            <b><?php _e( 'Custom CSS Suggestions:', 'bbp-style-pack' ); ?></b><br/><br/>
                <?php _e( 'When writing your own custom styling for the custom botton class specified above in #26, it is highly recommended to target specific selectors to make sure bbPress defaults do not override your custom styling code.', 'bbp-style-pack' ); ?><br/><br/>
                <?php 
                        if ( !empty( $bsp_style_settings_t['TopicButtonclass'] ) ) {
                                _e( 'Suggested selectors to target include:', 'bbp-style-pack' );
                                echo '<br/><br/>';
                                echo '&nbsp;&nbsp;&nbsp;&nbsp; .' . esc_html( trim( $bsp_style_settings_t['TopicButtonclass'] ) ) . '<br/>';
                                echo '&nbsp;&nbsp;&nbsp;&nbsp; #bbpress-forums a.' . esc_html( trim( $bsp_style_settings_t['TopicButtonclass'] ) ) . '<br/>';
                                echo '&nbsp;&nbsp;&nbsp;&nbsp; #bbpress-forums a.' . esc_html( trim( $bsp_style_settings_t['TopicButtonclass'] ) ) . ':visited<br/>';
                                echo '&nbsp;&nbsp;&nbsp;&nbsp; #bbpress-forums a.' . esc_html( trim( $bsp_style_settings_t['TopicButtonclass'] ) ) . ':hover<br/>';
                        }
                        else {
                                _e( 'Add your custom class name above to see some suggestions for CSS selectors to target.', 'bbp-style-pack' ); 
                        }
                ?>
        </p>    
	
<?php
}
