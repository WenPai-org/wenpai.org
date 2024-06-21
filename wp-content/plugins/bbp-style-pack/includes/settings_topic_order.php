<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//topic order settings page

function bsp_style_settings_topic_order () {
	global $bsp_topic_order ;
	?> 
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'settings-topic-order', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_topic_order' );
	//create a style.css on entry and on saving
	//generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Topic/Reply Order' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<strong>
	<p><?php _e('Topics:', 'bbp-style-pack'); ?> </p>
	<p><?php _e('bbpress will display topics in the latest reply order, with the latest topic at the top.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('However some sites need a different order and this section lets you change both the default order, and the order for specific forums.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('Replies:', 'bbp-style-pack'); ?> </p>
	<p><?php _e('bbpress will display replies in date order starting with the oldest.', 'bbp-style-pack'); ?> </p>
	<p><?php _e('However if you want to display the latest reply first then you can change this below ', 'bbp-style-pack'); ?> </p>
	
	
	</strong>
	
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	
	<hr>
<!-- TOPICS  -->	
	<h2><?php _e('Topics', 'bbp-style-pack'); ?></h2>
	<table class="form-table">
<!-- Change Default order  -->	
		<tr>
			<th>
				1. <?php _e('Change Topic Default Order', 'bbp-style-pack'); ?>
			</th>
			
			<?php
			$name = ('Default_Order') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$name2 = __('Display Order', 'bbp-style-pack') ;
			$name3 = __('Ascending/Descending', 'bbp-style-pack') ;
			$name4 = __('Forums', 'bbp-style-pack') ;
			
			$area1='Activate' ;
			$area2='Order' ;
			$area3='Asc' ;
			$area4='Forums' ;
			
			
			$item1="bsp_topic_order[".$name.$area1."]" ;
			$item2="bsp_topic_order[".$name.$area2."]" ;
			$item3="bsp_topic_order[".$name.$area3."]" ;
			$item4="bsp_topic_order[".$name.$area4."]" ;
			
			$value1 = (!empty($bsp_topic_order[$name.$area1]) ? $bsp_topic_order[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_topic_order[$name.$area2]) ? $bsp_topic_order[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_topic_order[$name.$area3]) ? $bsp_topic_order[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_topic_order[$name.$area4]) ? $bsp_topic_order[$name.$area4]  : '') ;
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
				_e ('Latest Reply date' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in the order of the latest reply</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="2" class="code"  ' . checked( 2,$value2, false ) . ' />' ;
				_e ('Topic date' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in the order of the original topic date</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="3" class="code"  ' . checked( 3,$value2, false ) . ' />' ;
				_e ('Title' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in alphbetical Title order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="4" class="code"  ' . checked( 4,$value2, false ) . ' />' ;
				_e ('Author' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in alphbetical Author order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
			</td>		
		</tr>
		
		<tr>	
			<th>
				<?php echo $name3; ?> 
			</th>
		
			<td>
				<?php
				echo '<input name="'.$item3.'" id="'.$item3.'" type="radio" value="1" class="code"  ' . checked( 1,$value3, false ) . ' />' ;
				_e ('Ascending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Earliest Date at top or Alphabetical order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item3.'" id="'.$item3.'" type="radio" value="2" class="code"  ' . checked( 2,$value3, false ) . ' />' ;
				_e ('Descending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Latest Date at top or reverse Alphabetical order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
			</td>		
		</tr>						
		

		
		
<!-- Change specific forum order  -->	
					
		<tr valign="top">  
			<td colspan="2">
			<p><?php _e('Alternately (or additionally) you may want one or more forums to show in a different order', 'bbp-style-pack'); ?> </p>
			</td>
		</tr>
		
		<tr>
			<th>
				2. <?php _e('Change Topic Order in specific forums', 'bbp-style-pack'); ?>
			</th>
			
			<?php
			$name = ('Forum_Order1') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$name2 = __('Display Order', 'bbp-style-pack') ;
			$name3 = __('Ascending/Descending', 'bbp-style-pack') ;
			$name4 = __('Forums', 'bbp-style-pack') ;
			
			$area1='Activate' ;
			$area2='Order' ;
			$area3='Asc' ;
			$area4='Forums' ;
			
			
			$item1="bsp_topic_order[".$name.$area1."]" ;
			$item2="bsp_topic_order[".$name.$area2."]" ;
			$item3="bsp_topic_order[".$name.$area3."]" ;
			$item4="bsp_topic_order[".$name.$area4."]" ;
			
			$value1 = (!empty($bsp_topic_order[$name.$area1]) ? $bsp_topic_order[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_topic_order[$name.$area2]) ? $bsp_topic_order[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_topic_order[$name.$area3]) ? $bsp_topic_order[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_topic_order[$name.$area4]) ? $bsp_topic_order[$name.$area4]  : '') ;
			?>
		
			<td>
				<?php echo '<input name="'.$item1.'" id="'.$item1.'" type="checkbox" value="1" class="code"  ' . checked( 1,$value1, false ) . ' />' ;
				echo $name1 ; ?>
			</td>
			
		<tr>
		
			<th> <?php echo $name4 ; ?> 
			</th>
			<td>
			<?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4 ).'"<br>' ; ?> 
			<label class="description"><?php _e( 'Enter the forum ID, or forums ID\'s separated by comma\'s,  e.g. <strong>1615</strong> or <strong>1615, 1723, 1852</strong> ', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
				
		<tr>	
			<th>
				<?php echo $name2 ; ?> 
			</th>
			<td>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="1" class="code"  ' . checked( 1,$value2, false ) . ' />' ;
				_e ('Latest Reply date' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in the order of the latest reply</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="2" class="code"  ' . checked( 2,$value2, false ) . ' />' ;
				_e ('Topic date' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in the order of the original topic date</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="3" class="code"  ' . checked( 3,$value2, false ) . ' />' ;
				_e ('Title' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in alphbetical Title order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item2.'" id="'.$item2.'" type="radio" value="4" class="code"  ' . checked( 4,$value2, false ) . ' />' ;
				_e ('Author' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Display topics in alphbetical Author order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
			</td>		
		</tr>
		
		<tr>	
			<th>
				<?php echo $name3; ?> 
			</th>
		
			<td>
				<?php
				echo '<input name="'.$item3.'" id="'.$item3.'" type="radio" value="1" class="code"  ' . checked( 1,$value3, false ) . ' />' ;
				_e ('Ascending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Earliest Date at top or Alphabetical order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item3.'" id="'.$item3.'" type="radio" value="2" class="code"  ' . checked( 2,$value3, false ) . ' />' ;
				_e ('Descending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Latest Date at top or reverse Alphabetical order</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
			</td>		
		</tr>					
					
		
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
	
<!-- REPLIES  -->
	
		<tr>
			<td colspan="2">
				<h2><?php _e('Replies', 'bbp-style-pack'); ?></h2>
			</td>
		</tr>
		<?php
				$name = ('reply_') ;
				$area1='order' ;
				$item1="bsp_topic_order[".$name.$area1."]" ;
				$value1 = (!empty($bsp_topic_order[$name.$area1]) ? $bsp_topic_order[$name.$area1]  : 1) ;
			?>
		<tr>
			<th>
				<?php echo $name3; ?> 
			</th>
			<td>
				<?php
				echo '<input name="'.$item1.'" id="'.$item1.'" type="radio" value="1" class="code"  ' . checked( 1,$value1, false ) . ' />' ;
				_e ('Ascending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Earliest Date at top</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item1.'" id="'.$item1.'" type="radio" value="2" class="code"  ' . checked( 2,$value1, false ) . ' />' ;
				_e ('Descending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Latest Date at top, show topic at top of each page</i>', 'bbp-style-pack' ); ?></label><br/>
				<p/>
				<?php
				echo '<input name="'.$item1.'" id="'.$item1.'" type="radio" value="3" class="code"  ' . checked( 3,$value1, false ) . ' />' ;
				_e ('Descending' , 'bbp-style-pack' ) ;?>
				<P>
					<label class="description" for="bsp_settings[new_topic_description]"><?php _e( '<i>Latest Date at top, show topic at bottom on last page</i>', 'bbp-style-pack' ); ?></label><br/>
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
