<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//custom css settings page

function bsp_css_settings() {
 ?>
	<table class="form-table">
					
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Custom CSS' , 'bbp-style-pack' ) ; ?>
				</h3>
			</th>
		</tr>	

		<tr>
			<td>
			<?php _e ('You can add any custom css here' , 'bbp-style-pack' ) ; ?>
			</td>
		</tr>	
				
		<?php global $bsp_css ; ?>
	<form method="post" action="options.php">
		<?php wp_nonce_field( 'css', 'css-nonce' ) ?>
		<?php settings_fields( 'bsp_css' );
		//create a style.css on entry and on saving
		generate_style_css();
                bsp_clear_cache();
		?>	
	
		
	<table class="form-table">
				
<!--add custom css---------------------------------------------------------------------->			
		<tr>
			<td>		
				<?php 
				$name = __('css', 'bbp-style-pack') ;
				$item1="bsp_css[".$name."]" ;
				$value1 = (!empty($bsp_css[$name]) ? $bsp_css[$name] : '');
				echo '<textarea id="'.$item1.'" class="large-text" name="'.$item1.'" rows="20" cols="40" >' ; 
				echo $value1 ; ?> 
				</textarea>
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


