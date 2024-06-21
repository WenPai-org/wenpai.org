<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum style settings page

function bsp_style_settings_la () {
	global $bsp_style_settings_la ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings_la', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_la' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Latest Activity Widget styling' , 'bbp-style-pack' ) ; ?>
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
				echo '<img src="' . plugins_url( 'images/la-widget.PNG',dirname(__FILE__)  ) . '" > '; 
				?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
	
	<!--Font - Widget title  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = __('Widget Title') ;
			$name0 = __('Widget Title', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item2="bsp_style_settings_la[".$name.$area2."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_la[$name.$area2]) ? $bsp_style_settings_la[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '1. '.$name0 ?>
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
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
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
			
			
	<!--Font - Topic/reply title  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = __('Topic Title') ;
			$name0 = __('Topic/Reply Title', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
			?>
			
			<th>
				<?php echo '2. '.$name0 ?>
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
			
			
	<!--Font - Text  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Text font' ;
			$name0 = __('Text Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item2="bsp_style_settings_la[".$name.$area2."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_la[$name.$area2]) ? $bsp_style_settings_la[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
			?>
			
			<th>
				<?php echo '3. '.$name0 ?>
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
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
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
			
	<!--Font - Topic author  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic author Font' ;
			$name0 = __('Topic/reply Author Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
			?>
			
			<th>
				<?php echo '4. '.$name0 ?>
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
			
	<!--Font - Freshness  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Freshness Font' ;
			$name0 = __('Freshness Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item2="bsp_style_settings_la[".$name.$area2."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_la[$name.$area2]) ? $bsp_style_settings_la[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
			?>
			
			<th>
				<?php echo '5. '.$name0 ?>
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
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
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
			
	<!--Font - Forum ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Forum Font' ;
			$name0 = __('Forum Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$item4="bsp_style_settings_la[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_la[$name.$area4]) ? $bsp_style_settings_la[$name.$area4]  : '') ;
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
					
	<!--Font - links   ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Topic-reply links' ;
			$name0 = __('Topic/reply links, Author links, and forum link colors' , 'bbp-style-pack') ;
			$name1 = __('Link Color', 'bbp-style-pack') ;
			$name2 = __('Active Color', 'bbp-style-pack') ;
			$name3 = __('Hover Color', 'bbp-style-pack') ;
			$area1='Link Color' ;
			$area2='Visited Color' ;
			$area3='Hover Color' ;
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$item2="bsp_style_settings_la[".$name.$area2."]" ;
			$item3="bsp_style_settings_la[".$name.$area3."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_la[$name.$area2]) ? $bsp_style_settings_la[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_la[$name.$area3]) ? $bsp_style_settings_la[$name.$area3]  : '') ;
			?>
			
			<th>
				<?php echo '7. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
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
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
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
				<?php echo '<input id="'.$item3.'" class="bsp-color-picker" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'Click to set color - You can select from palette or enter hex value- see help for further info', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<!--avatar Size ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Avatar' ;
			$name0 = __('Avatar', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$area1='Size' ;
			$item1="bsp_style_settings_la[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_la[$name.$area1]) ? $bsp_style_settings_la[$name.$area1]  : '') ;
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
					<?php _e( 'Default 14px ', 'bbp-style-pack' ); ?>
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
		

	
