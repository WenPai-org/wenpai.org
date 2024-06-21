<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum roles settings page

function bsp_roles() {

 ?>
			
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
					<h3>
						<?php _e ('Forum Roles' , 'bbp-style-pack' ) ; ?>
					</h3>
					<p>
						<?php _e ('By default bbpress displays the forum role eg Participant, Moderator, Keymaster etc. against the username on topics and replies.' , 'bbp-style-pack' ) ; ?>
					</p>					
					<p>
						<?php _e ('This tab lets you decide what roles will be displayed, where they are displayed, what names they are called, and how they are styled', 'bbp-style-pack' ) ; ?>
					</p>
					<p>
						<?php _e ('For instance you may not want to display roles at all, or only display moderator.  You might also want to change "keymaster" to say "Admin"' , 'bbp-style-pack' ) ; ?>
					</p>
			</th>
		</tr>
	</table>
					
	<?php global $bsp_roles ;?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'roles', 'roles-nonce' ) ?>
	<?php settings_fields( 'bsp_roles' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>	
	<hr>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
		</p>
	<table>
		<tr> 
			<th style="text-align:left ; vertical-align:top ; width : 200px">
				<?php _e('Where to show roles','bbp-style-pack'); ?>
			</th>
		</tr>
				
		<?php 
		$name = 'all_roles' ;
		$area0='where_to_display' ;
		$item0="bsp_roles[".$name.$area0."]" ;
		// set default value to 0 - display after display name which is what bbpress does by default
		$value0 = (!empty($bsp_roles[$name.$area0]) ? $bsp_roles[$name.$area0] : 0) ;
		?>
		<tr>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Click to hide roles' , 'bbp-style-pack' ) ;?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />' ;
				_e ('Click to display before user display name' , 'bbp-style-pack' ) ;?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
				_e ('Click to display after user display name' , 'bbp-style-pack' ) ;?>
			</td>
			
		</tr>
		
		<tr>
		
			<td>
			</td>
			
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/all_roles1.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/all_roles2.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			
			
		</tr>
	</table>
	
	<hr>
	
	<table>
		<tr>
		
		<tr>
		<th style="text-align:left ; vertical-align:top" colspan = 2>
				<?php _e('If you have selected before user display name','bbp-style-pack'); ?>
			</th>
		</tr>
		
		
		<?php 
		$area0='before_username_left' ;
		$item0="bsp_roles[".$name.$area0."]" ;
		// set default value to 0 - display after display name which is what bbpress does by default
		$value0 = (!empty($bsp_roles[$name.$area0]) ? $bsp_roles[$name.$area0] : 0) ;
		?>
		<tr>
			<td style="width : 200px">
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
				_e ('Click to display centered' , 'bbp-style-pack' ) ;?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Click to display left' , 'bbp-style-pack' ) ;?>
			</td>
			
			
			
		</tr>
		
		<tr>
			<td>
			</td>
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/all_roles1.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/all_roles3.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			
			
		</tr>
	</table>	
	
	<hr>
	 
	<table>
		<tr>
		
		<tr>
		<th style="text-align:left ; vertical-align:top" colspan = 2>
				<?php _e('If you have selected after user display name','bbp-style-pack'); ?>
			</th>
		</tr>
		<tr>
		
		<?php
			$item =  'bsp_roles[removeline]' ;
			$item1 = (!empty($bsp_roles['removeline']) ? $bsp_roles['removeline'] : '');
			
			?>
			<td style="width : 200px">
				<?php echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ; ?>
				<?php _e('Remove Line','bbp-style-pack'); ?>
				<p>
				<?php _e ('By default bbpress puts a line between the display name and the role. You can opt to remove this selecting', 'bbp-style-pack' ) ; ?>
				</p>
			</td> 
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/roles5.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			<td style="width : 200px">
				<?php echo '<img src="' . plugins_url( 'images/roles6.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			
			
		</tr>
	</table>
	
		
	<hr>	
	<p><strong>
	<?php _e ('Roles', 'bbp-style-pack' ) ; ?>
	</p>
	</strong>
	
	
	<table>
		<tr>
			<td style="width : 250px">
			</td>
			<td style="width : 250px">
				<?php echo '<img src="' . plugins_url( 'images/roles1.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			<td style="width : 250px">
				<?php echo '<img src="' . plugins_url( 'images/roles2.JPG',dirname(__FILE__)  ) . '" height=200px > '; ?>
			</td>
			<td style="width : 250px">
				<?php echo '<img src="' . plugins_url( 'images/roles3.JPG',dirname(__FILE__)  ) . '" height=200px> '; ?>
			</td>
			<td style="width : 250px">
				<?php echo '<img src="' . plugins_url( 'images/roles4.JPG',dirname(__FILE__)  ) . '" height=200px> '; ?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<th style="text-align:left"> Image</th>
			<th style="text-align:left"> Name </th>
			<th style="text-align:left"> Name superimposed<br/> on image </th>
			<th style="text-align:left"> Name below image </th>
		</tr>
		
				
		<?php 
		$area0='type' ;
		$area1='name' ;
		$area2='image' ;
		$area3='image_height' ;
		$area4='image_width' ;
		$area5='font_color' ;
		$area6='font' ;
		$area7='font_size' ;
		$area8='background_color' ;
		$area9='font_style' ;
	
		$roles = bbp_get_dynamic_roles () ;
		
		foreach ( $roles as $key=>$role ) {
			?>
		<tr>
			<td colspan=5>
				<hr>
			</td>
		</tr>
		<?php
			$name = $key ;
			$display = bbp_translate_user_role( $role['name']) ;
			?>	
		<tr>
			<th style="text-align:left ; vertical-align:top">
				<?php echo $display ; ?>
			</th>
		<?php
		$item0="bsp_roles[".$name.$area0."]" ;
		$item1="bsp_roles[".$name.$area1."]" ;
		$item2="bsp_roles[".$name.$area2."]" ;
		$item3="bsp_roles[".$name.$area3."]" ;
		$item4="bsp_roles[".$name.$area4."]" ;
		$item5="bsp_roles[".$name.$area5."]" ;
		$item6="bsp_roles[".$name.$area6."]" ;
		$item7="bsp_roles[".$name.$area7."]" ;
		$item8="bsp_roles[".$name.$area8."]" ;
		$item9="bsp_roles[".$name.$area9."]" ;
	
	
		$value0 = (!empty($bsp_roles[$name.$area0]) ? $bsp_roles[$name.$area0] : 2) ;
		$value1 = (!empty($bsp_roles[$name.$area1]) ? $bsp_roles[$name.$area1] : '') ;
		$value2 = (!empty($bsp_roles[$name.$area2]) ? $bsp_roles[$name.$area2] : '') ;
		$value3 = (!empty($bsp_roles[$name.$area3]) ? $bsp_roles[$name.$area3] : '') ;
		$value4 = (!empty($bsp_roles[$name.$area4]) ? $bsp_roles[$name.$area4] : '') ;
		$value5 = (!empty($bsp_roles[$name.$area5]) ? $bsp_roles[$name.$area5] : '') ;
		$value6 = (!empty($bsp_roles[$name.$area6]) ? $bsp_roles[$name.$area6] : '') ;
		$value7 = (!empty($bsp_roles[$name.$area7]) ? $bsp_roles[$name.$area7] : '') ;
		$value8 = (!empty($bsp_roles[$name.$area8]) ? $bsp_roles[$name.$area8] : '') ;
		$value9 = (!empty($bsp_roles[$name.$area9]) ? $bsp_roles[$name.$area9] : '') ;
		$i = 1 ;
		?>
	
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"/>' ; 
				_e ('Role Name' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<td colspan = 2>
				<label class="description">
					<?php _e( '<i>If you want to change the name - enter it here</i>' , 'bbp-style-pack' ); ?>
				</label>
				</td>
			<?php } ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="5" class="code"  ' . checked( 5,$value0, false ) . ' />' ;
				_e ('Click to hide role' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<br/>
				<label class="description">
					<?php _e( '<i>(Do not display this role)</i>' , 'bbp-style-pack' ); ?>
				</label>
			<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Click to display image' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<br/>
				<label class="description">
					<?php _e( '<i>(Use just an image that you have uploaded)</i>' , 'bbp-style-pack' ); ?>
				</label>
			<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />' ;
				_e ('Click to display name' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<br/>
				<label class="description">
					<?php _e( '<i>(Use just the role name - with a background color if desired )</i>' , 'bbp-style-pack' ); ?>
				</label>
			<?php } ?>
			</td>
	
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="3" class="code"  ' . checked( 3,$value0, false ) . ' />' ;
				_e ('Click to display name on top of image' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<br/>
				<label class="description">
					<?php _e( '<i>(Use the role name superimposed on an image)</i>' , 'bbp-style-pack' ); ?>
				</label>
			<?php } ?>
			</td>
	
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="4" class="code"  ' . checked( 4,$value0, false ) . ' />' ;
				_e ('Click to display name below image' , 'bbp-style-pack' ) ;?>
				<?php if ($name == 'bbp_keymaster') { ?>
				<br/>
				<label class="description">
					<?php _e( '<i>(Use the role name below an image)</i>' , 'bbp-style-pack' ); ?>
				</label>
			<?php } ?>
			</td>
		</tr>
	
		<tr>
			<td>
				<br/>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'">' ; ?>
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
				<label class="description"><?php _e( 'Enter the FULL url of the image to use', 'bbp-style-pack' ); ?>
				</label>
			<?php }
				else { ?>
				<label class="description"><?php _e( 'url', 'bbp-style-pack' ); ?></label></br>	
				<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'">' ; ?> 
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
				<label class="description">
					<?php _e( 'Image height if required eg 50px etc. ', 'bbp-style-pack' ); ?>
				</label>
			<?php }
			else { ?>
					<label class="description">
					<?php _e( 'Height', 'bbp-style-pack' ); ?>
				</label>
				<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4 ).'">' ; ?>
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
				<label class="description"><?php _e( 'Image width if required eg 50px etc.', 'bbp-style-pack' ); ?></label>
			<?php }
			else { ?>
			<label class="description">
				<?php _e( 'Width', 'bbp-style-pack' ); ?>
			</label>
			<?php } ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item5.'" class="bsp-color-picker" name="'.$item5.'" type="text" value="'.esc_html( $value5 ).'">' ; ?>
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
					
					<label class="description">
						<?php _e( 'Click to select the font color if required', 'bbp-style-pack' ); ?>
					</label>
				<?php }
				else { ?>
					<label class="description">
					<?php _e( 'Font color', 'bbp-style-pack' ); ?>
					</label>
				<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item6.'" class="large-text" name="'.$item6.'" type="text" value="'.esc_html( $value6 ).'">' ; ?> 
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
					<label class="description">
						<?php _e( 'Enter the font to use - default theme font', 'bbp-style-pack' ); ?>
					</label>
				<?php }
				else { ?>
					<label class="description">
						<?php _e( 'Font', 'bbp-style-pack' ); ?>
					</label>
				<?php } ?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item7.'" class="large-text" name="'.$item7.'" type="text" value="'.esc_html( $value7 ).'">' ; ?> 
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
					<label class="description">
						<?php _e( 'Enter the font size - Default 12px', 'bbp-style-pack' ); ?>
					</label>
				<?php }
				else { ?>
					<label class="description">
						<?php _e( 'Font size', 'bbp-style-pack' ); ?>
					</label>
				<?php } ?>
			</td>
			<td style="vertical-align:top">
				<select name="<?php echo $item9 ; ?>">
					<?php echo '<option value="'.esc_html( $value9).'">'.esc_html( $value9 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			<br/>
			<?php _e ('Font style' , 'bbp-style-pack' ) ;?>	
			</td>
		</tr>
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		<tr>
		
			<td>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item8.'" class="bsp-color-picker" name="'.$item8.'" type="text" value="'.esc_html( $value8 ).'">' ; ?>
				<br/>
				<?php if ($name == 'bbp_keymaster') { ?>
					<label class="description">
						<?php _e( 'Click to select the background color if required', 'bbp-style-pack' ); ?>
					</label>
				<?php }
				else { ?>
					<label class="description">
						<?php _e( 'Background color', 'bbp-style-pack' ); ?>
					</label>
				<?php } ?>
			</td>
				
			
	</tr>
	
	<?php
	//increments $i	
		$i++;	
	} ?>
	<?php //*************END OF roles LOOP************************ 

	// ADD topic author	?>

	<tr>
			<td colspan=5>
				<hr>
			</td>
		</tr>
		<?php
			$name = 'topic_author' ;
			$display = __('Topic Author', 'bbp-style-pack') ;
		?>	
		<tr>
			<th style="text-align:left ; vertical-align:top">
				<?php echo $display ; ?>
			</th>
			<td colspan = 3>
				<?php _e ('Displays this label if the reply is from the original topic author' , 'bbp-style-pack' ) ;?>	
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
		
			
			<?php
			$item0="bsp_roles[".$name.$area0."]" ;
			$item1="bsp_roles[".$name.$area1."]" ;
			$item2="bsp_roles[".$name.$area2."]" ;
			$item3="bsp_roles[".$name.$area3."]" ;
			$item4="bsp_roles[".$name.$area4."]" ;
			$item5="bsp_roles[".$name.$area5."]" ;
			$item6="bsp_roles[".$name.$area6."]" ;
			$item7="bsp_roles[".$name.$area7."]" ;
			$item8="bsp_roles[".$name.$area8."]" ;
			$item9="bsp_roles[".$name.$area9."]" ;
		
		
			$value0 = (!empty($bsp_roles[$name.$area0]) ? $bsp_roles[$name.$area0] : 0) ;
			$value1 = (!empty($bsp_roles[$name.$area1]) ? $bsp_roles[$name.$area1] : '') ;
			$value2 = (!empty($bsp_roles[$name.$area2]) ? $bsp_roles[$name.$area2] : '') ;
			$value3 = (!empty($bsp_roles[$name.$area3]) ? $bsp_roles[$name.$area3] : '') ;
			$value4 = (!empty($bsp_roles[$name.$area4]) ? $bsp_roles[$name.$area4] : '') ;
			$value5 = (!empty($bsp_roles[$name.$area5]) ? $bsp_roles[$name.$area5] : '') ;
			$value6 = (!empty($bsp_roles[$name.$area6]) ? $bsp_roles[$name.$area6] : '') ;
			$value7 = (!empty($bsp_roles[$name.$area7]) ? $bsp_roles[$name.$area7] : '') ;
			$value8 = (!empty($bsp_roles[$name.$area8]) ? $bsp_roles[$name.$area8] : '') ;
			$value9 = (!empty($bsp_roles[$name.$area9]) ? $bsp_roles[$name.$area9] : '') ;
			?>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"/>' ; 
				_e ('Topic Author' , 'bbp-style-pack' ) ;?>
			</td>
		</tr>
		
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
				_e ('Click to hide role' , 'bbp-style-pack' ) ;?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Click to display image' , 'bbp-style-pack' ) ;?>
				
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />' ;
				_e ('Click to display name' , 'bbp-style-pack' ) ;?>
				
			</td>
	
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="3" class="code"  ' . checked( 3,$value0, false ) . ' />' ;
				_e ('Click to display name on top of image' , 'bbp-style-pack' ) ;?>
				
			</td>
	
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="4" class="code"  ' . checked( 4,$value0, false ) . ' />' ;
				_e ('Click to display name below image' , 'bbp-style-pack' ) ;?>
				
			</td>
		</tr>
		
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'">' ; ?>
				<br/>
					<label class="description">
						<?php _e( 'url', 'bbp-style-pack' ); ?>
					</label>
				</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'">' ; ?> 
				<br/>
				<label class="description">
					<?php _e( 'Height', 'bbp-style-pack' ); ?>
				</label>
				
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4 ).'">' ; ?> 
				<br/>
					<label class="description">
						<?php _e( 'Width', 'bbp-style-pack' ); ?>
					</label>

			</td>
		</tr>
		
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item5.'" class="bsp-color-picker" name="'.$item5.'" type="text" value="'.esc_html( $value5 ).'">' ; ?>
				<br/>
				<label class="description">
					<?php _e( 'Font color', 'bbp-style-pack' ); ?>
				</label>	
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item6.'" class="large-text" name="'.$item6.'" type="text" value="'.esc_html( $value6 ).'">' ; ?> 
				<br/>
				<label class="description">
					<?php _e( 'Font', 'bbp-style-pack' ); ?>
				</label>	
				
			</td>
			<br/>
			<td style="vertical-align:top">
				<?php echo '<input id="'.$item7.'" class="large-text" name="'.$item7.'" type="text" value="'.esc_html( $value7 ).'">' ; ?> 
				<br/>
				<label class="description">
					<?php _e( 'Font size', 'bbp-style-pack' ); ?>
				</label>	
				
			</td>
			<td style="vertical-align:top">
			<select name="<?php echo $item9 ; ?>">
				<?php echo '<option value="'.esc_html( $value9).'">'.esc_html( $value9 ) ; ?> 
				<option value="Normal">Normal</option>
				<option value="Italic">Italic</option>
				<option value="Bold">Bold</option>
				<option value="Bold and Italic">Bold and Italic</option>
				</select>
				<br/>
				<?php _e ('Font style' , 'bbp-style-pack' ) ;?>	
			</td>
			
		</tr>
		<tr>
			<td>
				<br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td style="vertical-align:top">
			<?php echo '<input id="'.$item8.'" class="bsp-color-picker" name="'.$item8.'" type="text" value="'.esc_html( $value8 ).'">' ; ?>
				<br/>
				<label class="description">
					<?php _e( 'Background color', 'bbp-style-pack' ); ?>
				</label>
				
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




