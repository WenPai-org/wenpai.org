<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_login_fail() {
 ?>
			
	<h3>
		<?php _e ('Login Failure options' , 'bbp-style-pack' ) ; ?>
	</h3>
	<p>
		<?php _e ('If you are using either the <b> [bbp-login]</b> shortcode or the <b>bbpress login widget</b>, if users mis-enter login information, they are taken to the wordpress login and see error messages there.  This tab allows you to keep them in the relevant area and display error messages as you wish.', 'bbp-style-pack' ) ; ?>
	</p>
	<p/>
	
	<?php 
	global $bsp_login_fail;
?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'login_fail', 'login-fail-once' ) ?>
	<?php settings_fields( 'bsp_login_fail' ); 
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
		
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
	
	
<!--Click to add login/logout---------------------------------------------------------------------->
		<tr>
			<th colspan="2">1. 
				<?php _e ('Redirect failed login back to login shortcode and/or widget' , 'bbp-style-pack' ) ; ?>
			</th>
			<?php
			$name = 'fail' ;
			$name1 = __('Invalid Username', 'bbp-style-pack') ;
			$name2 = __('Incorrect Password', 'bbp-style-pack') ;
			$name3 = __('Empty Username', 'bbp-style-pack') ;
			$name4 = __('Empty Password', 'bbp-style-pack') ;
			$name5 = __('Nothing entered', 'bbp-style-pack') ;
			$area1='_invalid_username' ;
			$area2='_incorrect_password' ;
			$area3='_empty_username' ;
			$area4='_empty_password' ;
			$area5='_nothing_entered' ;
			$item1="bsp_login_fail[".$name.$area1."]" ;
			$item2="bsp_login_fail[".$name.$area2."]" ;
			$item3="bsp_login_fail[".$name.$area3."]" ;
			$item4="bsp_login_fail[".$name.$area4."]" ;
			$item5="bsp_login_fail[".$name.$area5."]" ;
			$value1 = (!empty($bsp_login_fail[$name.$area1] ) ? $bsp_login_fail[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_login_fail[$name.$area2] ) ? $bsp_login_fail[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_login_fail[$name.$area3] ) ? $bsp_login_fail[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_login_fail[$name.$area4] ) ? $bsp_login_fail[$name.$area4]  : '') ;
			$value5 = (!empty($bsp_login_fail[$name.$area5] ) ? $bsp_login_fail[$name.$area5]  : '') ;
			$item =  'bsp_login_fail[activate_failed_login]' ;
			$value = (!empty($bsp_login_fail['activate_failed_login']) ? $bsp_login_fail['activate_failed_login'] : '') ;
			?>
		
			<td>
			<?php
			echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$value, false ) . ' />';
			_e ('Click to activate' , 'bbp-style-pack' ) ;
  			?>
		</td>
		</tr>
		
		<tr>
			<th>2. 
				<?php _e ('Enter the error text' , 'bbp-style-pack' ) ;?>
			</th>
		
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default: "ERROR: Unknown username. Check again or try your email address" ', 'bbp-style-pack' ); ?></label><br/>
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
				<label class="description"><?php _e( 'Default: "ERROR: The password you entered was incorrect" ', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name2 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default: "ERROR: The username field was empty" ', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td> 
				<?php echo $name4 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default: "ERROR: The password field was empty" ', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td> 
				<?php echo $name5 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item5.'" class="large-text" name="'.$item5.'" type="text" value="'.esc_html( $value5).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default: "ERROR: Nothing was entered" ', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
		<!--3. style error message  ------------------------------------------------------------------->
			<tr>
			<?php 
			$name0 = __('Style the error messages', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_login_fail[".$name.$area1."]" ;
			$item2="bsp_login_fail[".$name.$area2."]" ;
			$item3="bsp_login_fail[".$name.$area3."]" ;
			$item4="bsp_login_fail[".$name.$area4."]" ;
			$value1 = (!empty($bsp_login_fail[$name.$area1]) ? $bsp_login_fail[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_login_fail[$name.$area2]) ? $bsp_login_fail[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_login_fail[$name.$area3]) ? $bsp_login_fail[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_login_fail[$name.$area4]) ? $bsp_login_fail[$name.$area4]  : '') ;
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
	
	

			
	</table>
<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
</form>

<?php
}
