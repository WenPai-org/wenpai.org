<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum index style settings page

function bsp_style_settings_search () {
	global $bsp_style_settings_search ;
	?>
        <form method="post" action="options.php">
	<?php wp_nonce_field( 'style_settings_search', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_search' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Search Styling' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to amend the search styling.', 'bbp-style-pack'); ?>
				</p>
				<p>
					<b>
					<?php _e('This will change the styling both on the main forum and in the search widget', 'bbp-style-pack'); ?>
					</b>
				</p>
				<p>
					<?php _e('You only need to enter those styles and elements within a style that you wish to alter', 'bbp-style-pack'); ?>
				</p>
			</td>
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/search_styling.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>

	<table class="form-table">
	
	<!--1. search Content background ---------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'search_content' ;
			$name0 = __('Search Content', 'bbp-style-pack') ;
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$name2 = __('Line Height', 'bbp-style-pack') ;
			
			$area1='background_color' ;
			$area2='line_height' ;
			
			$item1="bsp_style_settings_search[".$name.$area1."]" ;
			$item2="bsp_style_settings_search[".$name.$area2."]" ;
			
			$value1 = (!empty($bsp_style_settings_search[$name.$area1]) ? $bsp_style_settings_search[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_search[$name.$area2]) ? $bsp_style_settings_search[$name.$area2]  : '') ;
					
			
			?>
			<th>
				<?php echo '1. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="bsp-color-picker" name="'.$item1.'" type="text" value="'.esc_html($value1).'"<br>' ; ?> 
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
				<?php echo '<input id="'.$item2.'" class="small-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 24px - This will let you set the height of the box', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		
		
		
	
	<!--2. Search Content Text  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = ('search_content_text') ;
			$name0 = __('Search Content Text', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_search[".$name.$area1."]" ;
			$item2="bsp_style_settings_search[".$name.$area2."]" ;
			$item3="bsp_style_settings_search[".$name.$area3."]" ;
			$item4="bsp_style_settings_search[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_search[$name.$area1]) ? $bsp_style_settings_search[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_search[$name.$area2]) ? $bsp_style_settings_search[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_search[$name.$area3]) ? $bsp_style_settings_search[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_search[$name.$area4]) ? $bsp_style_settings_search[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '2. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 15px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
			
<!--1. search box background ---------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'search_box' ;
			$name0 = __('Search Box', 'bbp-style-pack') ;
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$name2 = __('Line Height', 'bbp-style-pack') ;
			
			$area1='background_color' ;
			$area2='line_height' ;
			
			$item1="bsp_style_settings_search[".$name.$area1."]" ;
			$item2="bsp_style_settings_search[".$name.$area2."]" ;
			
			$value1 = (!empty($bsp_style_settings_search[$name.$area1]) ? $bsp_style_settings_search[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_search[$name.$area2]) ? $bsp_style_settings_search[$name.$area2]  : '') ;
					
			
			?>
			<th>
				<?php echo '3. '.$name0 ?>
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
				<?php echo '<input id="'.$item2.'" class="small-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 24px - This will let you set the height of the box', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		
		
		
	
	<!--4. Search box Text  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = ('search_box_text') ;
			$name0 = __('Search Box Text', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_search[".$name.$area1."]" ;
			$item2="bsp_style_settings_search[".$name.$area2."]" ;
			$item3="bsp_style_settings_search[".$name.$area3."]" ;
			$item4="bsp_style_settings_search[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_search[$name.$area1]) ? $bsp_style_settings_search[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_search[$name.$area2]) ? $bsp_style_settings_search[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_search[$name.$area3]) ? $bsp_style_settings_search[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_search[$name.$area4]) ? $bsp_style_settings_search[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '4. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default 15px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Default 24px - This will let you set the height of the box', 'bbp-style-pack' ); ?></label><br/>
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
		
		<!-------------------------------5. Search Text---------------------------------------->
		<tr valign="top">
			<th>
				5. <?php _e('Search Text', 'bbp-style-pack'); ?>
			</th>
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_search['search_text'] ) ? $bsp_style_settings_search['search_text']  : '' ) ?>
				<input id="bsp_style_settings_search[search_text]" class="large-text" name="bsp_style_settings_search[search_text]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_settings[search_text]"><?php _e( 'Default : Search - if you wish to change enter the description eg "Search Forums", etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		
		
		
		
			
	<!--6. Searching spinner  ------------------------------------------------------------------->
			
		<tr valign="top">
			<?php
			$name='Searching' ;
			$name0 = __('Search', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_search[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_search[$name.$area1]) ? $bsp_style_settings_search[$name.$area1]  : '') ;
			$name2 = __('Searching Message', 'bbp-style-pack') ;
			$area2='Searching';
			$item2="bsp_style_settings_search[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_search[$name.$area2]) ? $bsp_style_settings_search[$name.$area2]  : 'Searching') ;
			$name3 = __('Spinner', 'bbp-style-pack') ;
			$area3='Spinner';
			$item3="bsp_style_settings_search[".$name.$area3."]" ;
			$value3 = (!empty($bsp_style_settings_search[$name.$area3]) ? $bsp_style_settings_search[$name.$area3]  : '') ;
			
			
			?>
			<th>
				<?php echo '6. '.$name0 ?>
			</th>
			<td colspan = '2'>
				<label class="description"><?php _e( 'You can set the search to display a different message once it is pressed eg "Searching". This will let the user know that they have sucessfully clicked the search.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				_e ('Click to activate', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php echo $name2 ?>
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="medium-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"' ; ?> 
			</td>
			<td>
				<label class="description"><?php _e( 'eg Searching, Processing, Search in progress etc', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php _e( 'If you just want the spinner below (ie no text), then put a space character in this section and activate the spinner below', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			<td>
			</td>
			<td colspan='2'>
				<label class="description"><?php _e( 'You can also select to display a spinner in addition to the above. This may rotate dependant on both client PC and server performance', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/submit.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			<td>
				<?php echo '<input name="'.$item3.'" id="'.$item3.'" type="checkbox" value="1" class="code" ' . checked( 1,$value3, false ) . ' />' ;
				_e ('Click to activate spinner', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		
		<!-------------------------------oh bother message---------------------------------------->
		<tr valign="top">
			<th>
				7. <?php _e('Change empty search message', 'bbp-style-pack'); ?>
			</th>
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_search['empty_search'] ) ? $bsp_style_settings_search['empty_search']  : '' ) ?>
				<input id="bsp_style_settings_search[empty_search]" class="large-text" name="bsp_style_settings_search[empty_search]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_style_settings_search[empty_search]"><?php _e( 'Default : Oh bother! No search results were found here!', 'bbp-style-pack' ); ?></label><br/>
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
		