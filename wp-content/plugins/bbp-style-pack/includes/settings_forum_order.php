<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum order settings page

function bsp_style_settings_forum_order () {
	global $bsp_forum_order ;
	?> 
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'settings-forum-order', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_forum_order' );
	//create a style.css on entry and on saving
	//generate_style_css();
        bsp_clear_cache();
	?>
	
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Forum Order' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<strong>
	<p><?php _e('By default bbpress will display forums in the order set when you edit a forum and as shown in Forum Attributes>Order on the right hand side.  If levels are the same, then they will display in alphabetical order.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('For most sites this is what is wanted, and no changes are needed here.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('However some sites need a different order and this section lets you change the order for forums.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('At the moment this is just to \'freshness\' or \'date created\' order, but if you would like other orders I\'ll add them, raise a <a href="https://wordpress.org/support/plugin/bbp-style-pack">support ticket</a>.', 'bbp-style-pack'); ?> </p>
	</strong>
	<hr>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
<!-- Change order  -->	
		<tr>
			<th>
				1. <?php _e('Change Order', 'bbp-style-pack'); ?>
			</th>
			
			<?php
			$name = ('Order') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$name2 = __('Order', 'bbp-style-pack') ;
						
			$area1='activate' ;
			$area2='order' ;
					
			
			$item1="bsp_forum_order[".$name.$area1."]" ;
			$item2="bsp_forum_order[".$name.$area2."]" ;
			
			
			$value1 = (!empty($bsp_forum_order[$name.$area1]) ? $bsp_forum_order[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_forum_order[$name.$area2]) ? $bsp_forum_order[$name.$area2]  : '1') ;
			
			?>
			
			<td>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code"  ' . checked( 1,$value1, false ) . ' />' ;
				echo $name1 ; ?>
			</td>
		</tr>
		
				
		<tr>	
			<th>
				<?php echo $name2 ; ?> 
			</th>
			<td>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="1" class="code"  ' . checked( 1,$value2, false ) . ' />' ;
				_e ('Default Order' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_forum_description]"><?php _e( '<i>Display forums in the order set in Edit Forum>Forum Attributes>Order and if the same in alphbetical</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="2" class="code"  ' . checked( 2,$value2, false ) . ' />' ;
				_e ('Freshness' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_forum_description]"><i><?php _e( 'Display forums in the order of recent topics/replies so the most recent active forum appears at the top etc.', 'bbp-style-pack' ); ?></i></label><br/>
				<p/>
				<P>
					<label class="description" for="bsp_settings[new_forum_description]"><i><?php _e( 'Note : Any forums with no topics will display at the top, so you might want to post a starter topic in any new forum', 'bbp-style-pack' ); ?></i></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="3" class="code"  ' . checked( 3,$value2, false ) . ' />' ;
				_e ('Date Forum Created - newest at top' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_forum_description]"><i><?php _e( 'Display forums in the order the forum was created, newest at the top.', 'bbp-style-pack' ); ?></i></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="4" class="code"  ' . checked( 4,$value2, false ) . ' />' ;
				_e ('Date Forum Created - oldest at top' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_forum_description]"><i><?php _e( 'Display forums in the order the forum was created, oldest at the top.', 'bbp-style-pack' ); ?></i></label><br/>
				<p/>
								
			</td>		
		</tr>
		
		
					
		
	
	
	</table>
	<hr>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	</form>

<?php
}

	
