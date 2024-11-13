<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum style settings page

function bsp_style_settings_block_widgets () {
	global $bsp_style_settings_block_widgets ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings_block', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_block_widgets' );
	//create a style.css on entry and on saving
        generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Block Widgets' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	
	<p>
		<?php _e('Blocks widgets are now available for bbPress.', 'bbp-style-pack'); ?>
		<?php _e('These can be used instead of the legacy widgets, and in blocks in pages and posts.  For FSE themes,  legacy widgets are not available so block widgets are a necessity', 'bbp-style-pack'); ?>
	</p>
	<p>	
		<?php _e('The following widgets have been added', 'bbp-style-pack'); ?>
	</p>
	<table>
		<tr>
			<td style="width:400px ; vertical-align:top">	
				<b><?php _e('Style Pack block widgets', 'bbp-style-pack'); ?> </b>
			</td>
			
			<td>
				 <?php _e('Latest Activity', 'bbp-style-pack'); ?> 
				
				<p>
				<?php _e('Single Forum Information', 'bbp-style-pack'); ?>
				</p>
				<p>
				<?php _e('Single Topic Information', 'bbp-style-pack'); ?>
				</p>
				
			</td>
		</tr>
		<tr>
			<td style="width:400px ; vertical-align:top">	
				<b><?php _e('Style Pack block versions of bbPress Widgets', 'bbp-style-pack'); ?> </b>
			</td>
			
			<td>
				<?php _e('Login', 'bbp-style-pack'); ?> 
				<p>
				 <?php _e('Forums List', 'bbp-style-pack'); ?> 
				</p>
				<p>
				<?php _e('Search', 'bbp-style-pack'); ?>
				</p>
				<p>
				<?php _e('Statistics', 'bbp-style-pack'); ?>
				</p>
				<p>
				<?php _e('Topic Views', 'bbp-style-pack'); ?>
				</p>
				
			</td>
		</tr>
	</table>
	
	<p>
		<?php _e('If you are not familiar with blocks that have settings, initially it can be confusing to work out where these are.', 'bbp-style-pack'); ?>
		
	</p>
	<p>
		<?php _e('Therefore I have added text that says \'Click here for settings on right hand side\'.  This is simply to get you to click inside the block to show the settings', 'bbp-style-pack'); ?>
	</p>
	<p>
		<?php _e('You will also need to have the settings sidebar enabled to see these, so ensure that the settings icon in the top right is black.', 'bbp-style-pack'); ?>
	</p>
	
	<p>
		<?php echo '<img src="' . plugins_url( 'images/block-widgets.png',dirname(__FILE__)  ) . '"  > '; ?>
	</p>

	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
	
	<p><b>
		<?php _e('How the title in widgets displays can depend on your theme.  The following lets you amend this if you wish to.', 'bbp-style-pack'); ?>
	</p></b>
	
	<p><i>
		<?php _e('You can style the Latest Activity widget elements in the ', 'bbp-style-pack');
				echo '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=la_widget">' ;
				_e('Latest Activity Widget Styling tab', 'bbp-style-pack');
				echo '</a>' ;
		?>
	</p></i>
	
	<!--Font - Widget title  ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Widget Title' ;
			$name0 = __('Widget Title', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_block_widgets[".$name.$area1."]" ;
			$item2="bsp_style_settings_block_widgets[".$name.$area2."]" ;
			$item3="bsp_style_settings_block_widgets[".$name.$area3."]" ;
			$item4="bsp_style_settings_block_widgets[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_block_widgets[$name.$area1]) ? $bsp_style_settings_block_widgets[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_block_widgets[$name.$area2]) ? $bsp_style_settings_block_widgets[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_block_widgets[$name.$area3]) ? $bsp_style_settings_block_widgets[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_block_widgets[$name.$area4]) ? $bsp_style_settings_block_widgets[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '1. '.$name0 ?>
			</th>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
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
			
			
	
		
		
		
		
			
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
</form>
	 
<?php
}
		

	
