<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_translation_settings() {
 ?>
			
	<h3>
		<?php _e ('Translations' , 'bbp-style-pack' ) ; ?>
	</h3>
	<p>
		<?php _e ('bbPress is already translated into many languages, and for most sites this is fine.' , 'bbp-style-pack' ) ; ?>
		<?php _e ('Additionally you can use the <a href = "https://wordpress.org/plugins/loco-translate/">Loco Translate</a> plugin to amend words and phrases.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('However you may want to amend either a translation, or correct a mis-translation, or just change a word or phrase on your own site.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('This section lets you amend this, simply put in the original word or phrase in the first column, and the word or phrase you want it replaced with in the second column.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('Caution : This method is slower than using the installed translations, because each time a bbpress word or phrase is encountered a function must run.  Whilst in most cases this will not result in any perceivable slowing of the site, the more phrases entered the more likely that you may see a speed change.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('You will need to enter the word of phrase exactly as shown on your site, and including capitalisation. So if you want to change both \'forum\' and \'Forum\', you will need an entry for each.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p> 
		<?php _e ("You may need experiment to get this to work. Some phrases may have full stops at the end.  You may need to enter single and plural words as separate entries." , 'bbp-style-pack' ) ; ?>
		
		<p> 
		<?php
                    /* translators: %1\$s, %2\$s, %3\$s, and %4\$s are displayed in plain text as an example complex translation strings. */
                    _e ("Finally some phrases use format specifiers or placeholders for instance 'This forum has %1\$s, %2\$s, and was last updated %3\$s by %4\$s.' so may need some skill to amend." , 'bbp-style-pack' ) ; 
                ?>
	</p>
	<p> <b>
		<?php _e ("I only plan to provide very limited help for this section as it is not my task to translate bbpress into your language :-) " , 'bbp-style-pack' ) ; ?>
	</b></p>
	</p>
	<p>
	</p>
	<?php global $bsp_style_settings_translation ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-translation', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_translation' );
        bsp_clear_cache();
	?>
					
			<table class="form-table">
			
			<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Activate translation option', 'bbp-style-pack'); ?>
			</th>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_translation['activate'] ) ?  $bsp_style_settings_translation['activate'] : '');
				echo '<input name="bsp_style_settings_translation[activate]" id="bsp_style_settings_translation[activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
			</td>
		</tr>
		
					
				<tr>
					<td width="50%">
					<b>
						<?php _e('Original word or phrase' , 'bbp-style-packs' ) ; ?>
					</b>
					</td>
					
					<td >
					<b>
						<?php _e('New word or phrase' , 'bbp-style-packs' ) ; ?>
					</b>
					</td>
				</tr>
				
				<?php 
				$count= (!empty ($bsp_style_settings_translation['count']) ? $bsp_style_settings_translation['count'] : 1) ;
				for ($i = 1 ; $i <= $count ; $i++) {
					$name="translation".$i ;
					$itema="bsp_style_settings_translation[".$name."a]" ;
					$itemb="bsp_style_settings_translation[".$name."b]" ;
					$valuea = (!empty ($bsp_style_settings_translation[$name.'a']) ? $bsp_style_settings_translation[$name.'a'] : '' ) ;
					$valueb = (!empty ($bsp_style_settings_translation[$name.'b']) ? $bsp_style_settings_translation[$name.'b'] : '' ) ;
					?>
					<tr valign="top">
						<td >
							<?php echo '<input id="'.$itema.'" class="large-text" name="'.$itema.'" type="text" value="'.esc_html( $valuea ).'"<br>' ; ?>
						</td>
					
						<td  >
							<?php echo '<input id="'.$itemb.'" class="large-text" name="'.$itemb.'" type="text" value="'.esc_html( $valueb).'"<br>' ; ?>
						
						</td>
					</tr>
				<?php 
				}					 			
				//now add a new one if needed
				if (!empty ($valuea)) {
				$name="translation".$i ;
				$itema="bsp_style_settings_translation[".$name."a]" ;
					$itemb="bsp_style_settings_translation[".$name."b]" ;
					$valuea = (!empty ($bsp_style_settings_translation[$name.'a']) ? $bsp_style_settings_translation[$name.'a'] : '' ) ;
					$valueb = (!empty ($bsp_style_settings_translation[$name.'b']) ? $bsp_style_settings_translation[$name.'b'] : '' ) ;
					?>
				<tr valign="top">
						<td >
							<?php echo '<input id="'.$itema.'" class="large-text" name="'.$itema.'" type="text" value="'.esc_html( $valuea ).'"<br>' ; ?>
						</td>
					
						<td  >
							<?php echo '<input id="'.$itemb.'" class="large-text" name="'.$itemb.'" type="text" value="'.esc_html( $valueb).'"<br>' ; ?>
							<?php echo '<input type="hidden" id="bsp_style_settings_translation[count]" name="bsp_style_settings_translation[count]" value="'.$i.'">' ; ?>
						</td>
					</tr>
				<?php
				}
				else {
				$i-- ;
				echo '<input type="hidden" id="bsp_style_settings_translation[count]" name="bsp_style_settings_translation[count]" value="'.$i.'">' ;
					
				}
				
				?>
				
				
					
					
		</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save', 'bbp-style-pack' ); ?>" />
		</p>
	</form>

<?php
}
