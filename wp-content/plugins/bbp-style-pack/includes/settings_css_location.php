<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//css location settings page

function bsp_css_location() {
	global $bsp_css_location;
	$css_location = isset( $bsp_css_location ['location'] ) ? $bsp_css_location ['location'] : '';
        $js_location = isset( $bsp_css_location ['js_location'] ) ? $bsp_css_location ['js_location'] : '';
        $home_url = esc_url( home_url() ).'/';
	?>

	<h3>
		<?php _e ('CSS/JS Location' , 'bbp-style-pack' ) ; ?>
	</h3>
	<p>
		<?php _e ('This plugin creates/loads several Cascade Style Sheets (CSS) files and Javascript (JS) files which allow the user\'s browser to know what styling to apply, and also adds some client-side functionality.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('By default these files are served from the plugin\'s css and js directories.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('Some users may have issues with using these directories under permissions on their server, and some advanced users may wish to serve these files from elsewhere.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('To allow flexibility, this tab allows you to amend where these files are stored and served from.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('If you don\'t understand any of the above, just exit from this tab - you should really only change this if you understand what you\'re or specifically advised to change these values.' , 'bbp-style-pack' ) ; ?>
	</p>


	<form method="post" action="options.php">
	<?php 
        wp_nonce_field( 'csslocation', 'login-nonce' );
	settings_fields( 'bsp_css_location' );
        //create generated files, and move default files appropriately on entry and on saving
        copy_to_custom_dirs();
	generate_style_css();
        bsp_clear_cache();
	?>	
            
                <hr />
        <h4>
                <?php _e ('CSS File Location' , 'bbp-style-pack' ) ; ?>
        </h4>
        <p>
		<?php _e ('You can change the CSS file location with the settings below.', 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php echo '<strong>' . __('Default CSS location' , 'bbp-style-pack' ) . ' : </strong>'; ?>
		<?php echo esc_url( bsp_default_full_location( $type = 'url', $file_type = 'css' ) ); ?>
	</p>
	<p>
		<?php
		echo '<strong>' . __('Current CSS location' , 'bbp-style-pack' ) . ' : </strong>'; 
		if ( bsp_use_custom_location( 'css', false ) ) {
			echo esc_url( bsp_sanitize_full_custom_url( $css_location ) );
		}
		else echo esc_url( bsp_default_full_location( $type = 'url', $file_type = 'css' ) );
                if ( !empty( $bsp_css_location ['activate css location'] ) ) {
                        $location_errors = bsp_custom_file_location_errors( 'css' );
                        if ( ! empty( $location_errors ) ) {
                                foreach ( $location_errors as $error ) {
                                        echo '<p style="border:1px solid red;"><strong>' . $error . '</strong></p>';
                                }
                        }
                }                
		?>
	</p>
	
	<table class="form-table">
		<tr>
			<td>
				<?php
				$name = '';
				$name1 = __('Enter CSS file location', 'bbp-style-pack');
				$name2 = __('Location to store plugin CSS files', 'bbp-style-pack');
				$area1 = 'activate css location';
				$area2 = 'location';
				$item1 = "bsp_css_location[".$area1."]";
				$item2 = "bsp_css_location[".$area2."]";
				$value1 = (!empty($bsp_css_location[$area1] ) ? $bsp_css_location[$area1]  : '');
				$value2 = $css_location;
				_e( 'Click to activate' , 'bbp-style-pack' );
				?>
			</td>
			<td>
                                <?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />'; ?>
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align:top">
				<?php echo $name1; ?>
			</td>
			
			<td colpsan="2">
				<?php echo $home_url; ?>
				<?php echo '<input id="'.$item2.'" class="regular-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<label class="description">
					<?php _e( 'Enter the desired path to the CSS files', 'bbp-style-pack' ); ?>
				</label>
				<br/>
				<label class="description">
					<?php _e( 'Don\'t forget to end with a "/" !!', 'bbp-style-pack' ); ?>
				</label>
				<br/>
                                <br/>
				<label class="description">
					<?php _e( 'If the directory does not currently exist, Style Pack will try to create the directory for you', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
	</table>
            
        <hr />
        
        <h4>
                <?php _e ('JS File Location' , 'bbp-style-pack' ) ; ?>
        </h4>
        <p>
		<?php _e ('You can change the JS file location with the settings below.', 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php echo '<strong>' . __('Default JS location' , 'bbp-style-pack' ) . ' : </strong>'; ?>
		<?php echo esc_url( bsp_default_full_location( $type = 'url', $file_type = 'js' ) ); ?>
	</p>
	<p>
		<?php
		echo '<strong>' . __('Current JS location' , 'bbp-style-pack' ) . ' : </strong>'; 
		if ( bsp_use_custom_location( 'js', false ) ) {
			echo esc_url( bsp_sanitize_full_custom_url( $js_location ) );
		}
		else echo esc_url( bsp_default_full_location( $type = 'url', $file_type = 'js' ) );
                if ( !empty( $bsp_css_location ['activate js location'] ) ) {
                        $location_errors = bsp_custom_file_location_errors( 'js' );
                        if ( ! empty( $location_errors ) ) {
                                foreach ( $location_errors as $error ) {
                                        echo '<p style="border:1px solid red;"><strong>' . $error . '</strong></p>';
                                }
                        }
                }                
		?>
	</p>
        
        <table class="form-table">
		<tr>
			<td>
				<?php
				$name = '';
				$name1 = __( 'Enter JS file location', 'bbp-style-pack' );
				$name2 = __( 'Location to store plugin JS files', 'bbp-style-pack' );
				$area1 = 'activate js location';
				$area2 = 'js_location';
				$item1 = "bsp_css_location[".$area1."]";
				$item2 = "bsp_css_location[".$area2."]";
				$value1 = ( !empty($bsp_css_location[$area1] ) ? $bsp_css_location[$area1]  : '' );
				$value2 = $js_location;
				_e( 'Click to activate' , 'bbp-style-pack' );
				?>
			</td>
			<td>
                                <?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />'; ?>
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align:top">
				<?php echo $name1; ?>
			</td>
			
			<td colpsan="2">
				<?php echo $home_url; ?>
				<?php echo '<input id="'.$item2.'" class="regular-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<label class="description">
					<?php _e( 'Enter the desired path to the JS files', 'bbp-style-pack' ); ?>
				</label>
				<br/>
				<label class="description">
					<?php _e( 'Don\'t forget to end with a "/" !!', 'bbp-style-pack' ); ?>
				</label>
				<br/>
                                <label class="description">
					<?php _e( 'If the directory does not currently exist, Style Pack will try to create the directory for you', 'bbp-style-pack' ); ?>
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


