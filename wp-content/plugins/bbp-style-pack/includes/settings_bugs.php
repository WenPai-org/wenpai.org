<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_settings_bugs() {
 ?>
			
	<h3>
		<?php _e ('bbPress Bug Fixes' , 'bbp-style-pack' ) ; ?>
	</h3>
	<p>
		<?php _e ('This section lets you get over some bbpress bugs - enable as desired' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('They should work for you, but I cannot guarantee' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e ('When I am aware that they have been fixed in bbpress, I will remove them from here' , 'bbp-style-pack' ) ; ?>
	</p>
	
	<?php global $bsp_style_settings_bugs ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-bugs', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_bugs' );
        bsp_clear_cache();
	?>
					
			<table class="form-table">
			
			<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix Threaded Replies Jump', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php _e( 'In bbpress 2.6.x threaded replies only work if the WordPress adminbar is enabled. If it is disabled and you click a reply link of a lower level reply the page is reloaded which is not supposed happen. If you then post the reply, it is added at the end of the forum post and not after the corresponding reply - this fix corrects that. ', 'bbp-style-pack' ); ?>
			</td>
			
		</tr>
		<tr>
			<td>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['activate_threaded_replies'] ) ?  $bsp_style_settings_bugs['activate_threaded_replies'] : '');
				echo '<input name="bsp_style_settings_bugs[activate_threaded_replies]" id="bsp_style_settings_bugs[activate_threaded_replies]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Apply Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		
		<?php $version = get_option('bsp_bbpress_version', '2.5') ;  //set to 2.5 as default if option not set
		?>
			<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix Last Active Time', 'bbp-style-pack'); ?>
			</th>
			<?php
			if (substr($version, 2, 1) == '5' || substr($version, 4, 1) <6)  {
			//add this section
			?>
			<td>
				<?php _e( 'In 2.6.0 - 2.6.5 calculating the last active time does not always work. Update to 2.6.6 to fix this, or if you are unable to do that, try these fixes, if you select both, only the first will apply', 'bbp-style-pack' ); ?>
			</td>
			
		</tr>
		
		<tr>
			<td>
			</td>
			
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['activate_last_active_time'] ) ?  $bsp_style_settings_bugs['activate_last_active_time'] : 0);
				echo '<input name="bsp_style_settings_bugs[activate_last_active_time]" id="bsp_style_settings_bugs[activate_last_active_time]" type="radio" value="1" class="code" ' . checked( 0,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'No fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		<tr>
			<td>
			</td>
			
			<td>
				<?php 
				echo '<input name="bsp_style_settings_bugs[activate_last_active_time]" id="bsp_style_settings_bugs[activate_last_active_time]" type="radio" value="2" class="code" ' . checked( 2,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Try this option first - if it doesn\'t work, try the next ', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
				<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<td >
			</td>
			
					
			<td>
				<?php 
				echo '<input name="bsp_style_settings_bugs[activate_last_active_time]" id="bsp_style_settings_bugs[activate_last_active_time]" type="radio" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Try this option if the above does not work', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<?php
		} // end of if (substr($version, 0, 5) != '2.6.6') {
			else {
				?>
		<td>
				<?php _e( 'This bug was fixed in 2.6.6 and this fix is no longer needed.', 'bbp-style-pack' ); ?>
			</td>
			
		</tr>
		<?php
		} //end of else
			
		?>
		
		
		<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix \'A variable Mismatch has been detected\'', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If other plugins (for instance \'Theme my login\')  register ‘action’ as a public query variable with WP, then on splitting or merging a topic, bbpress gives this error - this fix corrects that.', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['variable_mismatch'] ) ?  $bsp_style_settings_bugs['variable_mismatch'] : '');
				echo '<input name="bsp_style_settings_bugs[variable_mismatch]" id="bsp_style_settings_bugs[variable_mismatch]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Apply Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix \'Restore\' on front end', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you trash a topic or reply, and then clicking \'Restore\', this does not work - this fix corrects that.', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['frontend_restore'] ) ?  $bsp_style_settings_bugs['frontend_restore'] : '');
				echo '<input name="bsp_style_settings_bugs[frontend_restore]" id="bsp_style_settings_bugs[frontend_restore]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Apply Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix private sub forums not displaying', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you have forums or categories with ONLY PRIVATE sub forums, then sub forums will not display on the forums list.  This is automatically fixed using this plugin, but if you need the original code to work, then tick here to exclude this fix.', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['subfourm_fix'] ) ?  $bsp_style_settings_bugs['subfourm_fix'] : '');
				echo '<input name="bsp_style_settings_bugs[subfourm_fix]" id="bsp_style_settings_bugs[subfourm_fix]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Exclude Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		
<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix "Uncaught TypeError: register_shutdown_function()" error', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you have converted other forums to bbpress, part of the login process checks if passwwords need to converted. PHP 8.x does not like
					a function used.  The simplest solution is to exclude this step, which at worst would mean that some users created in the conversion will
					need to reset their passwords.', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['register_shutdown'] ) ?  $bsp_style_settings_bugs['register_shutdown'] : '');
				echo '<input name="bsp_style_settings_bugs[register_shutdown]" id="bsp_style_settings_bugs[register_shutdown]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Apply Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>	

<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix subscription in dashboard>topics being lost after update ', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you go into dashboard>topics and edit a topic, then if you update only one subscription is saved.  This fix ensures that all are saved.  You cannot amend subscriptions within this metabox, use the \'Subsciptions Management\' tab to manage subscriptions on the backend.  You can exclude this fix if you wish', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['subscriptions_fix'] ) ?  $bsp_style_settings_bugs['subscriptions_fix'] : '');
				echo '<input name="bsp_style_settings_bugs[subscriptions_fix]" id="bsp_style_settings_bugs[subscriptions_fix]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Exclude Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>		
		
		<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix new topic errors not showing', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you do not complete the required fields for a new topic in the form at the bottom of the forum topic list, then the topic will not post, but you can be redirected to the top of the page with no explanation. I\'ve added a fix to show the errors at the top of the page as well.  You can exclude this fix if you wish', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['new_topics_error_fix'] ) ?  $bsp_style_settings_bugs['new_topics_error_fix'] : '');
				echo '<input name="bsp_style_settings_bugs[new_topics_error_fix]" id="bsp_style_settings_bugs[new_topics_error_fix]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Exclude Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>		
		
<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
<?php if (class_exists ('Akismet')) { ?>
		<tr valign="top">  
			<th >
				<?php _e('Fix Akismet not correctly updating latest activity ', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you are using Akismet, if it detects a post as spam, it still includes it as the latest activity, so this shows the wrong information.  This fix corrects that.', 'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['akismet_fix'] ) ?  $bsp_style_settings_bugs['akismet_fix'] : '');
				echo '<input name="bsp_style_settings_bugs[akismet_fix]" id="bsp_style_settings_bugs[akismet_fix]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Include Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>

<?php } //end of if askimt class exists ?>		

<!-- ACTIVATE  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th >
				<?php _e('Fix Forum Count in [bbp-stats] ', 'bbp-style-pack'); ?>
			</th>
			<td>
					<?php _e( 'If you have private forums, the count does not show these, this fixes that' ,'bbp-style-pack' ); ?>
				</td>
			
		</tr>
		<tr>
			<td>
			</td>
			
					
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_bugs['forum_count_fix'] ) ?  $bsp_style_settings_bugs['forum_count_fix'] : '');
				echo '<input name="bsp_style_settings_bugs[forum_count_fix]" id="bsp_style_settings_bugs[forum_count_fix]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_settings[new_topic_description]">
					<?php _e( 'Include Fix', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>		
						
				
<?php
$keymasters = get_users( [ 'role__in' => [ 'bbp_keymaster'] ] );
if (empty ($keymasters)) {
	?>
	<tr>
	<th>
	<?php esc_html_e( 'No Keymasters exist', 'bbp-style-pack'  ); ?>
	</th>
	<td>
					
					<?php	echo '<input name="bsp_style_settings_bugs[bsp_keymaster]" id="bsp_style_settings_bugs[bsp_keymaster]" type="checkbox" value="1" class="code" />' ; ?>
					<label for="bbp-forums-role"><?php esc_html_e( 'Set yourself as Keymaster ?', 'bbp-style-pack'  ); ?></label><br>
					<label for="bbp-forums-role"><?php esc_html_e( 'bbPress required at least one Keymaster, and only keymasters can set other keymasters. ', 'bbp-style-pack'  ); ?></label><br>
					<label for="bbp-forums-role"><?php esc_html_e( 'However bbPress does allow you to change the last keymaster to another role without warning - leaving no keymasters, and no ability to set a keymaster.', 'bbp-style-pack'  ); ?></label><br>
					<label for="bbp-forums-role"><?php esc_html_e( 'This is the case on this site, and you, or someone who has access to this area, needs to set themself as Keymaster.', 'bbp-style-pack'  ); ?></label><br>
					
					
					</td>
				</tr>
				<?php
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







