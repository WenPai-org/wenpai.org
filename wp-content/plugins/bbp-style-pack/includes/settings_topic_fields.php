<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

function bsp_settings_topic_fields() {
	global $bsp_style_settings_topic_fields;
	?>
	<form method="post" action="options.php">
	<?php
		wp_nonce_field( 'style-settings_topic_fields', 'style-settings-nonce' );
                settings_fields( 'bsp_style_settings_topic_fields' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
		?>
		
	
	<table class="form-table">
                        <tr valign="top">
                                <th colspan="2">
                                        <h3>
                                                <?php _e('Topic Fields' , 'bbp-style-pack' ); ?>
                                        </h3>
                                </th>
                        </tr>

                        <tr valign="top">
                                <th colspan="2">
                                        <?php _e('This section lets you add additional fields to the Topic Form and dispaly them on the topic' , 'bbp-style-pack' ); ?>
                                </th>
                        </tr>
						<tr valign="top">
						
						<tr>
						<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/topic_fields.png',dirname(__FILE__)  ) . '" width="500px"> '; ?>
			</td>
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/topic_fields2.png',dirname(__FILE__)  ) . '" width="500px"> '; ?>
			</td>
			</tr>
			
		<th colspan="2">
		  <?php _e('There are settings to style the display at the bottom of this tab' , 'bbp-style-pack' ); ?>
		  </th>
	 </table>
	<table class="form-table">
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'bbp-style-pack' ); ?>" />
	</p>
	<tr>
		<td> 
			<?php _e ('Number of fields' , 'bbp-style-pack' ) ; ?>
		</td>
		<?php
		$name="number_of_fields" ;
		$item1="bsp_style_settings_topic_fields[".$name."]" ;
		$top =(!empty( $bsp_style_settings_topic_fields[$name] ) ?  $bsp_style_settings_topic_fields[$name] : '0');
                                       
		?>
	
		<td colspan = "4" style="vertical-align:top">
			<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $top ).'"' ; ?> 
			<label class="description"><?php _e( 'Enter the no. fields you wish to have and press "Save changes" to generate', 'bbp-style-pack' ); ?></label>
		</td>
	</tr>
	
	
	
	<tr>
		<td> 
			<?php _e ('Where to display on Topics' , 'bbp-style-pack' ) ; ?>
		</td>
		<td>
	<?php
		$name = 'show_item_on_topic' ;
		$name_display1 = __('Show fields above the topic', 'bbp-style-pack');
		$name_display2 = __('Show fields above the topic content', 'bbp-style-pack');
		$name_display3 = __('Show fields below the topic content' , 'bbp-style-pack');
		$item='bsp_style_settings_topic_fields['.$name.']' ;
		$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : 1) ;
		echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code" ' . checked( 1,$value, false ) . ' />'. $name_display1.'<br/>';
		echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code" ' . checked( 2,$value, false ) . ' />'. $name_display2.'<br/>';
		echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="3" class="code" ' . checked( 3,$value, false ) . ' />'. $name_display3;
		?>
	</td>
	</tr>
	
	

	<?php $i=1 ; ?>
	<?php //*************START OF FIELD LOOP************************  
	
	
	while($i<= $top)   {
			
			
	?>
				
					<!-------------------------------Label ---------------------------------------->
	<tr valign="top">
		<th colspan="2"><h4>
			<?php 
			_e('Field ', 'bbp-style-pack') ;
			echo $i ; 
			?></h4>
		</th>
	</tr>
					
					
					
	<tr valign="top">
		<?php 
		$name = 'item'.$i.'_label' ;
		$item='bsp_style_settings_topic_fields['.$name.']' ;
		$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
		?>
		<th>
			<?php _e('Name', 'bbp-style-pack'); ?>
		</th>
		<td>
			<?php echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'"' ; ?> 
			<label class="description" for="bsp_settings[item1_label]"><?php _e( 'Enter Field Label ', 'bbp-style-pack' ); ?></label><br/>
		</td>
	</tr>
	
	<?php
	$name = 'field'.$i ;
	$item='bsp_style_settings_topic_fields['.$name.']' ;
	$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : 0) ;
	?>
					
	<tr>
			<th>
				<?php _e ('Field Type' , 'bbp-style-pack' ) ;?>
			</th>
			<td>
				<?php echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="0" class="code"  ' . checked( 0,$value, false ) . ' />' ; ?>
				<label class="description"><?php _e( 'The user can enter any text', 'bbp-style-pack' ); ?></label>
			<br/>
				<?php echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$value, false ) . ' />' ; ?>
				<label class="description"><?php _e( 'The user can only select from a list', 'bbp-style-pack' ); ?></label>
			</td>
	</tr>
	
	<tr>
	<td>
	</td>
			<td>		
				<?php 
				_e ('If a list - one item per line' , 'bbp-style-pack' ) ; 
				echo '<br/>' ;
				$name1 = 'fieldlist'.$i ;
				$item1='bsp_style_settings_topic_fields['.$name1.']' ;
				$value1 = (!empty($bsp_style_settings_topic_fields[$name1]) ? $bsp_style_settings_topic_fields[$name1] : '') ;
				if ($value1==' ') $value1 = '' ;
				echo '<textarea id="'.$item1.'" class="medium-text" name="'.$item1.'" rows="10" cols="35" >'.$value1.'</textarea>' ; ?>
			</td>
		</tr>
		<!-- checkbox to make required -->
	<tr valign="top">  
		<th>
			<?php _e('Make completion of this item required on the topic form', 'bbp-style-pack'); ?></th>
		<td>
			<?php 
			$name = 'itemrequired_item'.$i ;
			$item='bsp_style_settings_topic_fields['.$name.']' ;
			$name_display = __('Make this a required field', 'bbp-style-pack'); 
			$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
			echo '<input name="'.$item.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' /> '.$name_display;
			?>
		</td>
	</tr>
		
					
					<!-- Show -->
	<tr valign="top">  
		<th>
			<?php _e('Hide this item on the topic form', 'bbp-style-pack'); ?>
		</th>
		<td>
		<?php
		$name = 'hide_item_on_form'.$i ;
		$name_display = __('Hide this field on the topic form', 'bbp-style-pack');
		$item='bsp_style_settings_topic_fields['.$name.']' ;
		$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' />'. $name_display;
		?>
		</td>
	</tr>
	
	
	
	<!-- Show -->
	<tr valign="top">  
		<th>
			<?php _e('Hide this item on the topic', 'bbp-style-pack'); ?>
		</th>
		<td>
		<?php
		$name = 'hide_item_on_topic'.$i ;
		$name_display = __('Hide this field on the topic', 'bbp-style-pack');
		$item='bsp_style_settings_topic_fields['.$name.']' ;
		$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' />'. $name_display;
		?>
		</td>
	</tr>
	
	<tr>
	
	<td colspan = 2>
		<?php _e('You can use the above 2 settings to control the item. Normally you would leave these blank, and the item will show on the topic form and the topic display.' , 'bbp-style-pack'); ?>
		<br/>
		<?php _e('But if you cease to use this item, you would set this to hide on the topic form to stop new entries, but for older topics that have already been created using this field, you can decide whether to hide those entries, or display.  ', 'bbp-style-pack'); ?>
	  </td>
	</tr>
							
	<!-- checkbox to display label in topics/replies -->
	<tr valign="top">  
		<th>
			<?php _e('Hide label', 'bbp-style-pack'); ?>
		</th>
		<td>
			<?php
			$name = 'labelhide_item'.$i ;
			$item='bsp_style_settings_topic_fields['.$name.']' ;
			$name_display = __('Hide the label for this item on the topic', 'bbp-style-pack');
			$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
			echo '<input name="'.$item.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' />'.$name_display;
			?>
		</td>
	</tr>
					
	<!-- checkbox to hide label in topics/replies -->
	<tr valign="top">  
		<th>
			<?php _e('Hide Label if no data', 'bbp-style-pack'); ?>
		</th>
		<td>
			<?php
			$name = 'labelhide_label'.$i ;
			$item='bsp_style_settings_topic_fields['.$name.']' ;
			$name_display = __('You can opt to hide the label if a user has not entered data, so that the label only shows if the user has entered information ', 'bbp-style-pack');
			$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
			echo '<input name="'.$item.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' />'.$name_display ;
			?>
		</td>
	</tr>
					
		<tr>
			<?php
			$name = 'forums'.$i ;
			$name_display = __('Forums', 'bbp-style-pack');
			$item='bsp_style_settings_topic_fields['.$name.']' ;
			$value = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
			?>
		
			<th> <?php echo $name_display ; ?> 
			</th>
			<td>
			<?php echo '<input id="'.$item.'" class="large-text" name="'.$item.'" type="text" value="'.esc_html( $value ).'"<br>' ; ?> 
			<label class="description"><?php _e( '<i>Leave blank for all</i>, or enter the forum ID, or forums ID\'s separated by comma\'s,  e.g. <strong>1615</strong> or <strong>1615, 1723, 1852</strong> ', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>			
	
					
	<tr>
		<td colspan=2>
			<hr>
		</td>
	</tr>
					
					
<?php
	//increments $i	
		$i++;	
	} ?>
	<?php //*************END OF LEVEL LOOP************************  ?>
						
					
</table>

<table>

<tr>
		<th width = "200px"> 
			<?php _e ('Separator' , 'bbp-style-pack' ) ; ?>
		</th>
		<?php
		$name="separator" ;
		$item1="bsp_style_settings_topic_fields[".$name."]" ;
		$value =(!empty( $bsp_style_settings_topic_fields[$name] ) ?  $bsp_style_settings_topic_fields[$name] : '');
                                       
		?>
		<td>
		</td>
		<td colspan = "4" style="vertical-align:top">
			<?php echo '<input id="'.$item1.'" class="small-text" name="'.$item1.'" type="text" value="'.esc_html( $value ).'"' ; ?> 
			<label class="description"><?php _e( 'What to have between the label and the data eg a space or " - " Default ": "', 'bbp-style-pack' ); ?></label>
		</td>
	</tr>
	
<!-- Label Styling  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = ('label font') ;
			$name0 = __('Label Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_topic_fields[".$name.$area1."]" ;
			$item2="bsp_style_settings_topic_fields[".$name.$area2."]" ;
			$item3="bsp_style_settings_topic_fields[".$name.$area3."]" ;
			$item4="bsp_style_settings_topic_fields[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_topic_fields[$name.$area1]) ? $bsp_style_settings_topic_fields[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_topic_fields[$name.$area2]) ? $bsp_style_settings_topic_fields[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_topic_fields[$name.$area3]) ? $bsp_style_settings_topic_fields[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_topic_fields[$name.$area4]) ? $bsp_style_settings_topic_fields[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo $name0 ?>
			</th>
			<td width = "50px">
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
		
		<!-- Item Styling  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name = ('item font') ;
			$name0 = __('Label Font', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_topic_fields[".$name.$area1."]" ;
			$item2="bsp_style_settings_topic_fields[".$name.$area2."]" ;
			$item3="bsp_style_settings_topic_fields[".$name.$area3."]" ;
			$item4="bsp_style_settings_topic_fields[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_topic_fields[$name.$area1]) ? $bsp_style_settings_topic_fields[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_topic_fields[$name.$area2]) ? $bsp_style_settings_topic_fields[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_topic_fields[$name.$area3]) ? $bsp_style_settings_topic_fields[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_topic_fields[$name.$area4]) ? $bsp_style_settings_topic_fields[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo $name0 ?>
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
</table>
				
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'bbp-style-pack' ); ?>" />
	</p>
								
	</form>
	
			
	<?php
}

