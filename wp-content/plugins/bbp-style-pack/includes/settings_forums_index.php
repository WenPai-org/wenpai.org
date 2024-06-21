<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum index style settings page

function bsp_style_settings_f () {
	global $bsp_style_settings_f ;
	?>
	 <form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings_f', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_f' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Forums Index Styling' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to amend styles.', 'bbp-style-pack'); ?>
				</p>
				<p>
					<?php _e('You only need to enter those styles and elements within a style that you wish to alter', 'bbp-style-pack'); ?>
				</p>
			</td>
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/forums-list.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>

	<table class="form-table">
	
	<!--1. Forum Content background ---------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Forum Content' ;
			$name0 = __('Forum Content', 'bbp-style-pack') ;
			$name1 = __('Background Color - odd numbers', 'bbp-style-pack') ;
			$name2 = __('Background Color - even numbers', 'bbp-style-pack') ;
			$name3 = __('Background Image - odd numbers', 'bbp-style-pack') ;
			$name4 = __('Background Image - even numbers', 'bbp-style-pack') ;
			$area1='Background color - odd numbers' ;
			$area2='Background color - even numbers' ;
			$area3='Background image - odd numbers' ;
			$area4='Background image - even numbers' ;
			
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			
			
			?>
			<th>
				<?php echo '1. '.$name0 ?>
			</th>
			<td style="vertical-align: top;">
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html($value1).'"<br>' ; ?> 
				<label class="description">
                                    <?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                    <p>
                                    <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                    <?php _e( '#fbfbfb', 'bbp-style-pack' ); ?>
                                    </p>
				</label>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php _e('Or', 'bbp-style-pack') ; ; ?> 
			</td>
		</tr>
			
		
		<tr valign='top'>
			<td>
			</td>
			<td> 
				<?php echo $name3 ; ?> 
			</td>
			<td>
				<?php echo get_bloginfo('url').'/<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3).'"' ; ?> 
				<br/>
				<label class="description"><?php _e( 'Enter path of image', 'bbp-style-pack') ; ?>
				</label>
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
                                    <?php _e( '#fff', 'bbp-style-pack' ); ?>
                                    </p>
                                </label>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php _e('Or', 'bbp-style-pack') ; ; ?> 
			</td>
		</tr>
			
		
		<tr valign='top'>
			<td>
			</td>
			<td> 
				<?php echo $name4 ; ?> 
			</td>
			<td>
				<?php echo get_bloginfo('url').'/<input id="'.$item4.'" class="medium-text" name="'.$item4.'" type="text" value="'.esc_html( $value4).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter path of image', 'bbp-style-pack') ; ?>
				</label><br/>
			</td>
		</tr>
	
	<!--2. Forum/Topic/Reply headers/footers background ------------------------------------------------------------------->
		
			<?php 
			$name = 'Forum/Topic Headers/Footers' ;
			$name0 = __('Forum/Topic Headers/Footers', 'bbp-style-pack') ;
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$name2 = __('Background Image', 'bbp-style-pack') ;
			$area1='Background Color' ;
			$area2='Background Image' ;			
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			?>
		<tr valign='top'>
			<th>
				<?php echo '2. '.$name0 ?>
			</th>
			<td style="vertical-align: top;">
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1).'"<br>' ; ?> 
				<label class="description">
                                    <?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
                                    <p>
                                    <?php _e( ' bbPress Default: ', 'bbp-style-pack' ); ?>
                                    <?php _e( '#f4f4f4', 'bbp-style-pack' ); ?>
                                    </p>
                                </label>
			</td>
		</tr>
		
		
		<tr>
			<td>
			</td>
			<td>
				<?php _e('Or', 'bbp-style-pack') ; ; ?> 
			</td>
		</tr>
			
		
		<tr valign='top'>
			<td>
			</td>
			<td> 
				<?php echo $name2 ; ?> 
			</td>
			<td>
				<?php echo get_bloginfo('url').'/<input id="'.$item2.'" class="medium-text" name="'.$item2.'" type="text" value="'.esc_html( $value2).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter path of image', 'bbp-style-pack') ; ?>
				</label><br/>
			</td>
		</tr>
	
	<!--3. Font - Forum headings  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = ('Forum Headings Font') ;
			$name0 = __('Forum Headings Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '3. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
	<!--4. Font - breadcrumb font  ------------------------------------------------------------------->
						
		<tr>
			<?php 
			$name = 'Breadcrumb Font' ;
			$name0 = __('Breadcrumb Font','bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '4. '.$name0 ?>
			</th>
			<td> 
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
					
	<!--5. Font - links   ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Links' ;
			$name0 = __('Links' , 'bbp-style-pack') ;
			$name1 = __('Link Color', 'bbp-style-pack') ;
			$name2 = __('Active/Visited Color', 'bbp-style-pack') ;
			$name3 = __('Hover Color', 'bbp-style-pack') ;
			$area1='Link Color' ;
			$area2='Visited Color' ;
			$area3='Hover Color' ;
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			?>
			<th>
				<?php echo '5. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="bsp-color-picker" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
				</label><br/>
			</td>
		</tr>
			
			
	<!--6. Font - forum and category lists   ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Forum and Category List Font' ;
			$name0 = __('Forum and Category List Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '6. '.$name0 ?>
			</th>
			<td> 
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
			
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
			
	<!--7. Font - sub forums   ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Sub Forum List Font' ;
			$name0 = __('Sub Forum List Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '7. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
	<!--8. Font - forum description  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Forum Description Font' ;
			$name0 = __('Forum Description Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '8. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
		
	<!--9. Font - freshness  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Freshness Font' ;
			$name0 = __('Freshness Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '9. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
	<!--10. Font - freshness author  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Freshness Author Font' ;
			$name0 = __('Freshness Author Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '10. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
		
		
<!--11. freshness avatar size ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Freshness Avatar' ;
			$name0 = __('Freshness Avatar Size', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$area1='Size' ;
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '11. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 14px - enter a size ', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
				
				
	<!--12. Forum Border  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Forum Border' ;
			$name0 = __('Forum Border', 'bbp-style-pack') ;
			$name1 = __('Line width', 'bbp-style-pack') ;
			$name3 = __('Line style', 'bbp-style-pack') ;
			$name4 = __('Color', 'bbp-style-pack') ;
			$area1='Line width' ;
			$area3='Line style' ;
			$area4='Color';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '12. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 1px  - Set to 0px to hide border', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default solid - solid, dashed, dotted are common values - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name4 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item4.'" class="bsp-color-picker" name="'.$item4.'" type="text" value="'.esc_html( $value4 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
				</label><br/>
			</td>
		</tr>
		
	<!--13. Font - topic count  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic Count Font' ;
			$name0 = __('Topic Count Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '13. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
		
	<!--14. Font - Post Count  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Post Count Font' ;
			$name0 = __('Post Count Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_f[".$name.$area1."]" ;
			$item2="bsp_style_settings_f[".$name.$area2."]" ;
			$item3="bsp_style_settings_f[".$name.$area3."]" ;
			$item4="bsp_style_settings_f[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_f[$name.$area4]) ? $bsp_style_settings_f[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '14. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack') ; ?>
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
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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

<!--15. show icons ------------------------------------------------------------------->

		<tr valign="top">
			<th>
				15. <?php _e('Show Dashicons', 'bbp-style-pack'); ?>
			</th>
			<?php 
			$name = 'forum' ;
			$name0 = __('Show Dashicons instead of text', 'bbp-style-pack') ;
			$name2 = __('Topics Dashicon', 'bbp-style-pack') ;
			$name3 = __('Posts Dashicon', 'bbp-style-pack') ;
			$help = __('See this link for a ful list of Dashicons', 'bbp-style-pack') ;
			$area1 = '_icons' ;
			$item1 =  "bsp_style_settings_f[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1] : '');
			$area2 = '_topics' ;
			$item2 =  "bsp_style_settings_f[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_f[$name.$area2]) ? $bsp_style_settings_f[$name.$area2] : '');
			$area3 = '_posts' ;
			$item3 =  "bsp_style_settings_f[".$name.$area3."]" ;
			$value3 = (!empty($bsp_style_settings_f[$name.$area3]) ? $bsp_style_settings_f[$name.$area3] : '');
			?>
			<td>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				echo $name0 ;
				?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name2 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="medium-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default f325','bbp-style-pack' ); ?>
				<span class="dashicons bsp-topics-icon"></span>
				<?php _e ('- leave blank to show default', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="medium-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default f125', 'bbp-style-pack' ); ?>
				<span class="dashicons bsp-posts-icon"></span>
				<?php _e ('- leave blank to show default', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		<tr>
		<td></td>
		<td colspan=2>
		<?php echo $help ;
		echo '<a href= "https://azuliadesigns.com/wordpress/wordpress-dashicons-cheat-sheet/" target="_blank"> https://azuliadesigns.com/wordpress/wordpress-dashicons-cheat-sheet/</a>' ;
		?>
		</td>
		</tr>
		


	
<!--16. oh bother message ------------------------------------------------------------------->
		<tr valign="top">
			<th>
				16. <?php _e('Change empty forum index message', 'bbp-style-pack'); ?>
			</th>
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_f['empty_index'] ) ? $bsp_style_settings_f['empty_index']  : '' ) ?>
				<input id="bsp_style_settings_f[empty_index]" class="large-text" name="bsp_style_settings_f[empty_index]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_settings[empty_forum]"><?php _e( 'Default : Oh bother! No forums were found here!', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<?php 
			$name = 'empty_index' ;
			$name0 = __('Don\'t show empty forum index message', 'bbp-style-pack') ;
			$area1 = 'Activate' ;
			$item1 =  "bsp_style_settings_f[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1] : '');
			?>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				_e('Don\'t show this message','bbp-style-pack');
				?>
			</td>
		</tr>
<!--17. oh bother message ------------------------------------------------------------------->			
		<tr valign="top">
			<?php
			
			$name0 = __('Search', 'bbp-style-pack') ;
			
			
			?>
			<th>
				<?php echo '17. '.$name0 ?>
			</th>
			<td colspan = '2'>
				<label class="description"><?php _e( 'You can style the search widget via the tab \'Search Styling\' ', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		
<!--18. wordpress search  ONLY ALLOW IF PRIVATE GROUPS NOT ACTIVATED------------------------------------------------------------------->
	
		<tr valign="top">
			<th>
				18. <?php _e('Allow main site search to access bbpress topics and replies', 'bbp-style-pack'); ?>
			</th>
		<?php if( ! function_exists('bbp_private_groups_init') ) { ?>
			<?php 
			$name = 'wordpress_search' ;
			$area1 = 'Activate' ;
			$item1 =  "bsp_style_settings_f[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_f[$name.$area1]) ? $bsp_style_settings_f[$name.$area1] : '');
			?>
			<td colspan=2>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				_e('Add topics and replies to main site search','bbp-style-pack');?>
				<br/><label class="description"><?php _e( 'By default most themes provide a site search.  This will not usually include topics and replies.  Activating adds these to the site main search.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		
		<?php } // end of if private groups exists
			else { ?>
				
				<td colspan=2>
				<br/><label class="description"><?php _e( 'As you are running bbp Private Groups Plugin, this option is not permitted.', 'bbp-style-pack' ); ?></label><br/>
			</td>
			<?php } ?>
				
			
		</tr>
			
	</table>
	
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	</form>
	
<?php
}
		

	
