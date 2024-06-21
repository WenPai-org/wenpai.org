<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_css_location() {
global $bsp_css_location ;

$location = $bsp_css_location ['location'] ;
			// if it starts with '/' 
			if (0 === strpos($location, '/')) {
			echo '<p> <strong>WARNING - location should not start with a \'/\' !! </strong> </p>' ;
			}
		// if it doesn't end with a '/' 
		if (substr( $location, strlen($location)-1, strlen($location) ) !== '/') {
			echo '<p> <strong> WARNING - location is missing a \'/\' at the end !! </strong> </p>' ;
		}
		?>

			
						<table class="form-table">
					
					<tr valign="top">
						<th colspan="2">
						
						<h3>
						<?php _e ('CSS Location' , 'bbp-style-pack' ) ; ?>
						</h3>
	<?php
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
			
<p>
<?php _e ('This plugin creates a Cascade Style Sheet (css file) which allows the user\'s browser to know what styling to apply.' , 'bbp-style-pack' ) ; ?>
</p>
<p>
<?php _e ('By default this is set to store this file in the plugins css directory as wp-content/plugins/bbp-style-pack/css/bspstyle.css' , 'bbp-style-pack' ) ; ?>
</p>
<p>
<?php _e ('Some users may have issues with using this directory under permissions on their server, and some advanced users may wish to store this file elsewhere.' , 'bbp-style-pack' ) ; ?>
</p>
<p>
<?php _e ('To allow flexibility, this tab allows you to amend where this file is stored.' , 'bbp-style-pack' ) ; ?>
</p>
<p>
<?php _e ('If you don\'t understand any of the above, just exit from this tab - you should really only change this if you understand or on advice.' , 'bbp-style-pack' ) ; ?>
</p>
<p>
<?php _e ('<i>Default location</i> : ' , 'bbp-style-pack' ) ; ?>
<?php echo plugins_url('css/',dirname(__FILE__) ) ; ?>
</p>

<p>
<?php _e ('Current location : ' , 'bbp-style-pack' ) ; ?>
<?php if (!empty ($bsp_css_location ['activate css location']) && !empty($bsp_css_location ['location'])) {
	$url = home_url();
	echo esc_url( $url ).'/'.$bsp_css_location ['location'] ; 
	}
	else echo plugins_url('css/',dirname(__FILE__) ) ; ?>
</p>



<?php 
global $bsp_css_location ;
	?>
	 <form method="post" action="options.php">
	<?php wp_nonce_field( 'csslocation', 'login-nonce' ) ?>
	<?php settings_fields( 'bsp_css_location' );
	?>	
	
	<table class="form-table">
	
	
<!--Click to add login/logout---------------------------------------------------------------------->
	<tr><td>
	<?php
			$name = '' ;
			$name1 = __('Enter bspstyle.css location', 'bbp-style-pack') ;
			$name2 = __('Location to store bspstyle.css', 'bbp-style-pack') ;
			$area1='activate css location' ;
			$area2='location' ;
			$item1="bsp_css_location[".$area1."]" ;
			$item2="bsp_css_location[".$area2."]" ;
			$value1 = (!empty($bsp_css_location[$area1] ) ? $bsp_css_location[$area1]  : '') ;
			$value2 = (!empty($bsp_css_location[$area2] ) ? $bsp_css_location[$area2]  : '') ;
			_e ('Click to activate' , 'bbp-style-pack' ) ;
			?>
			</td><td>
			<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />';
			
  			?>
	</td></tr>
	<tr>
			<td style="vertical-align:top"> <?php echo $name1 ; ?> </td>
			<td colpsan="2">
			 <?php $url = home_url();
			echo esc_url( $url ).'/'; ?>
			<?php echo '<input id="'.$item2.'" class="regular-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
			</td></tr><tr><td></td><td>
			<label class="description"><?php _e( 'Enter the path to the file', 'bbp-style-pack' ); ?></label><br/>
			<label class="description"><?php _e( 'Don\'t forget to end with a "/" !!', 'bbp-style-pack' ); ?></label><br/>
			<label class="description"><?php _e( 'Don\'t include the bspstyle.css in the location', 'bbp-style-pack' ); ?></label><br/>
			</td>
			</tr>
				
			
				</table>
<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
				</p>
				</form>
		</div><!--end sf-wrap-->
	</div><!--end wrap-->
	
	 
		

<?php
}


