<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//unread options settings page

function bsp_style_settings_unread () {
	global $bsp_style_settings_unread ;
	?> 
	<form method="post" action="options.php">
		<?php wp_nonce_field( 'style-settings_unread', 'style-settings-nonce' ) ?>
		<?php settings_fields( 'bsp_style_settings_unread' );
		//create a style.css on entry and on saving
		generate_style_css();
                bsp_clear_cache();
		?>
		
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Unread Posts' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to add \'unread\' and/or \'read\' icons to topics and forums to allow users to quickly find new content.', 'bbp-style-pack'); ?> 
				</p>
				<p>
					<?php _e('It also adds a class of either \'bsp-topic-read\' or \'bsp-topic-unread\' to the title on topic in each forum topic list, allowing you to style the title.', 'bbp-style-pack'); ?> 
				</p>
				<p>
					<?php _e('It contains much of the code of the plugin \'bbp unread posts\' and \'bbp unread posts V2\' which are no longer being maintained.', 'bbp-style-pack'); ?> 
				</p>
			</td>
			
			<td>	
				<?php
				//show style image
				echo '<img src="' . plugins_url( 'images/unread_forum.JPG',dirname(__FILE__)  ) . '" > '; 
				echo '<img src="' . plugins_url( 'images/unread_topics.JPG',dirname(__FILE__)  ) . '" > '; 
				echo '<img src="' . plugins_url( 'images/unread_profile.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>

	<hr>
	
	<table class="form-table">
	<!-- ACTIVATE UNREAD  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th>
				<?php _e('Activate unread option', 'bbp-style-pack'); ?>
			</th>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_unread['unread_activate'] ) ?  $bsp_style_settings_unread['unread_activate'] : '');
				echo '<input name="bsp_style_settings_unread[unread_activate]" id="bsp_style_settings_unread[unread_activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
			</td>
		</tr>
		
		<?php
		if (function_exists ('bbp_unread_forum_icons')) {#
		?>
		<tr>
			<td colspan = 2>
			<?php _e('<b>INFORMATION :</b> It looks like you are running the Unread Posts V2 plugin. It will look very confused if you run both, so it is suggested that you deactivate Unread Posts v2 plugin before activating this section', 'bbp-style-pack'); ?>
			</td>
		</tr>
		<?php		
		}
		?>
						
		
					
	<!-- checkbox to activate  -->
					
		<tr valign="top">  
			<th>
				1. <?php _e('Unread Icon', 'bbp-style-pack'); ?>
			</th>
		</tr>
			<tr>
			<td colspan=2>
				<?php _e('Before a topic has been read, this icon will display', 'bbp-style-pack'); ?>
			</td>
		</tr>
			
		<tr>
			<td colspan=2>
				<?php _e('You can choose the default icon, to use an image, or to have no icon', 'bbp-style-pack'); ?>
			</td>
		</tr>
			<?php
			$item =  'bsp_style_settings_unread[unread_icon]' ;
			$item1 = (!empty($bsp_style_settings_unread['unread_icon']) ? $bsp_style_settings_unread['unread_icon'] : 1);
			$realpath =plugins_url ( "images/folder_new.png" ,dirname(__FILE__));			
			?>
		<tr>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$item1, false ) . ' />' ;
				_e ('Use default Icon' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php echo '<i>' ;
					_e( 'This Icon will be displayed:', 'bbp-style-pack') ; 
					echo '<img src="' . $realpath . '"/></i>' ; ?>
				</label>
			</td>
			
			<td width="200" style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2,$item1, false ) . ' />' ;
				_e ('Use Image' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>Enter the full url to the image below</i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="3" class="code"  ' . checked( 3,$item1, false ) . ' />' ;
				_e ('Do not display Icon' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>No icon will be displayed</i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
			
	<tr valign="top">
			<th>
				<?php _e('Image url', 'bbp-style-pack'); ?>
			</th>
			
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_unread['unread_url'] ) ? $bsp_style_settings_unread['unread_url']  : 1 ) ?>
				<input id="bsp_style_settings_unread[unread_url]" class="large-text" name="bsp_style_settings_unread[unread_url]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_settings[subscribe_button_description]">
					<?php _e( 'If you are using an image, then enter its full url above', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
			
			
		<tr valign="top">  
			<th>
				2. <?php _e('Read Icon', 'bbp-style-pack'); ?>
			</th>
		</tr>
		
		<tr>
			<td colspan=2>
				<?php _e('Once a topic has been read, this icon will display', 'bbp-style-pack'); ?>
			</td>
		</tr>
			
		<tr>
			<td colspan=2>
				<?php _e('You can choose the dafault icon, to use an image, or to have no icon', 'bbp-style-pack'); ?>
			</td>
		</tr>
			<?php
			$item =  'bsp_style_settings_unread[read_icon]' ;
			$item1 = (!empty($bsp_style_settings_unread['read_icon']) ? $bsp_style_settings_unread['read_icon'] : 1);
			$realpath =plugins_url ( "images/folder.png" ,dirname(__FILE__));			
			?>
		<tr>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$item1, false ) . ' />' ;
				_e ('Use default Icon' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
				<?php echo '<i>' ;
					_e( 'This Icon will be displayed:', 'bbp-style-pack') ; 
					echo '<img src="' . $realpath . '"/></i>' ; ?>
				</label>
			</td>
			
			<td width="200" style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2,$item1, false ) . ' />' ;
				_e ('Use Image' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>Enter the full url to the image below</i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="3" class="code"  ' . checked( 3,$item1, false ) . ' />' ;
				_e ('Do not display Icon' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>No icon will be displayed</i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
			
	<tr valign="top">
			<th>
				<?php _e('Image url', 'bbp-style-pack'); ?>
			</th>
			
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_unread['read_url'] ) ? $bsp_style_settings_unread['read_url']  : '' ) ?>
				<input id="bsp_style_settings_unread[read_url]" class="large-text" name="bsp_style_settings_unread[read_url]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description">
					<?php _e( 'If you are using an image, then enter its full url above', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<!--Show unread totals  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				3. <?php _e('Show unread total on forums?', 'bbp-style-pack'); ?>
			</th>
			
			<td colspan=2>
				<?php 
				$item = (!empty( $bsp_style_settings_unread['unread_amount'] ) ?  $bsp_style_settings_unread['unread_amount'] : '');
				echo '<input name="bsp_style_settings_unread[unread_amount]" id="bsp_style_settings_unread[unread_amount]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description">
					<?php _e( 'On forum lists, this will show the number of unread topics in each forum', 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
		
		<tr valign="top">
			<th>
				4. <?php _e('Mark as Read Description', 'bbp-style-pack'); ?>
			</th>
			
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_style_settings_unread['unread_description'] ) ? $bsp_style_settings_unread['unread_description']  : '' ) ?>
				<input id="bsp_style_settings_unread[unread_description]" class="large-text" name="bsp_style_settings_unread[unread_description]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description">
					<?php _e( 'Default : \'Mark all topics as read\'. Enter new text if you wish ', 'bbp-style-pack' ); ?>
				</label>
				<br>
				<label class="description">
					<?php _e( 'If you want to style this button, then visit the Buttons tab ', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<!--Hide mark as read on main index-->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				5. <?php _e('Hide the \'Mark as Read\' Button <p>on the main index page</p>', 'bbp-style-pack'); ?>
			</th>
			
			<td colspan=2>
				<?php 
				$item = (!empty( $bsp_style_settings_unread['hide_on_index'] ) ?  $bsp_style_settings_unread['hide_on_index'] : '');
				echo '<input name="bsp_style_settings_unread[hide_on_index]" id="bsp_style_settings_unread[hide_on_index]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description">
					<?php _e( 'You can hide the button on the main index - on large sites, marking all topics as read may take some time, and you may want to restrict this to individual forums only', 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
		
			<tr valign="top">  
			<th>
				6. <?php _e('Opt-in or Opt-out options', 'bbp-style-pack'); ?>
			</th>
		</tr>
		
		<tr>
			<td colspan=3>
				<?php _e('You can allow users to opt-in or opt-out of seeing the unread icons if you wish. They do this via their profile, and you may need to expain to them how to do this!', 'bbp-style-pack'); ?>
			</td>
		</tr>
		
			
		<?php
			$item =  'bsp_style_settings_unread[optinout]' ;
			$item1 = (!empty($bsp_style_settings_unread['optinout']) ? $bsp_style_settings_unread['optinout'] : 1);
			$realpath =plugins_url ( "images/folder.png" ,dirname(__FILE__));			
			?>
		<tr>
			<td style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$item1, false ) . ' />' ;
				_e ('No opt-in opt-out options' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>Users will always see the unread icons and buttons' , 'bbp-style-pack' ); ?>
				</label>
			</td>
			
			<td width="200" style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2,$item1, false ) . ' />' ;
				_e ('Allow users to opt-in' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>Users will have to opt-in via their profile to see the icons and use the buttons  </i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
			<td width="200" style="vertical-align:top">
				<?php
				echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="3" class="code"  ' . checked( 3,$item1, false ) . ' />' ;
				_e ('Allow users to opt-out' , 'bbp-style-pack' ) ;?>
				<br>
				<label class="description">
					<?php _e( '<i>Users will have to opt-out via their profile to see the icons and use the buttons </i>' , 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
		</table>
		<table>
		
		<tr valign="top">
			<td>
			<?php _e( 'Description for opt-in in user profile' , 'bbp-style-pack' ); ?>
			</td>
		
			<td width="400" style="vertical-align:top">
				<?php 
				$item1 = (!empty ($bsp_style_settings_unread['optin_desc'] ) ? $bsp_style_settings_unread['optin_desc']  : '' ) ?>
				<input id="bsp_style_settings_unread[optin_desc]" class="large-text" name="bsp_style_settings_unread[optin_desc]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description">
					<?php _e( 'Default : "Display unread icons"', 'bbp-style-pack' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
		<td>
		
			<?php _e( 'Description for opt-out in user profile' , 'bbp-style-pack' ); ?>
			</td>
			<td width="400" style="vertical-align:top">
				<?php 
				$item1 = (!empty ($bsp_style_settings_unread['optout_desc'] ) ? $bsp_style_settings_unread['optout_desc']  : '' ) ?>
				<input id="bsp_style_settings_unread[optout_desc]" class="large-text" name="bsp_style_settings_unread[optout_desc]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description">
					<?php _e( 'Default : "Do not display unread icons"', 'bbp-style-pack' ); ?>
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
