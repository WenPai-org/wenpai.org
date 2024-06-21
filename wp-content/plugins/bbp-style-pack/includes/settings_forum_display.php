<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum display settings page

function bsp_forum_display () {
	global $bsp_forum_display ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'forum-display', 'forum-display-nonce' ) ?>
	<?php settings_fields( 'bsp_forum_display' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>	
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Forum Display' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table class="form-table">
		<tr valign="top">
			<th colspan="3">
				<p>
					<?php _e('This section allows you to amend the way the forums display.', 'bbp-style-pack'); ?>
				</p>
			</th>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
		<tr>
			<td>
			</td>
			<th style="text-align:left">
				<?php _e('CHANGE', 'bbp-style-pack'); ?> 
			</th>
			<th style="text-align:center">
				<?php _e('FROM', 'bbp-style-pack'); ?>
			</th>
			<th style="text-align:center">
				<?php _e('TO', 'bbp-style-pack'); ?> 
			</th>
			<th style="text-align:left">
				<?php _e('Click to activate', 'bbp-style-pack'); ?> 
			</th>
	
<!--forum list vertical ------------------------------------------------------------------->
		<tr>
			
			<th width="10%">
				<?php 
				$name = __('Forum List', 'bbp-style-pack') ;
				$desc = __('Alter the list from horizontal to vertical', 'bbp-style-pack') ;
				?>
				<?php echo '1.<br>'.$name.'</p><i>'.$desc.' </i>' ?>
			</th>
			
			<td width="15%">
				<?php
				$item =  'bsp_forum_display[forum_list]' ;
				$item1 = (!empty($bsp_forum_display['forum_list']) ? $bsp_forum_display['forum_list'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Change to column list','bbp-style-pack');
				?>
			</td>
			
			<td width="35%">
				<?php echo '<img src="' . plugins_url( 'images/forum1.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="35%">
				<?php echo '<img src="' . plugins_url( 'images/forum2.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
<!--hide forum counts ------------------------------------------------------------------->
		<tr>
		
			<th width="10%">
				<?php $name = __('Hide sub-forum counts','bbp-style-pack')  ; ?>
				<?php echo '2.<br>'.$name ; ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[hide_counts]' ;
				$item1 = (!empty($bsp_forum_display['hide_counts']) ? $bsp_forum_display['hide_counts'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Hide counts','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum1.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum3.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
			</tr>
			
<!--Move subscribe to right ------------------------------------------------------------------->
			<tr>
				<?php 
				$name = __('Move subscribe','bbp-style-pack')  ; 
				$desc = __('Stop subscribe resting against the breadcrumb', 'bbp-style-pack') ;
				?>
				<th width="10%">
					<?php echo '3.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
				</th>
				<td width="16%">
				<?php
				$item =  'bsp_forum_display[move_subscribe]' ;
				$item1 = (!empty($bsp_forum_display['move_subscribe']) ? $bsp_forum_display['move_subscribe'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Move subscribe to right','bbp-style-pack') ;
				?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum4.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum5.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
			
<!--Remove Private title ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = __('Remove "Private" prefix','bbp-style-pack')  ; 
			$desc = __('Remove private prefix for forums and topics', 'bbp-style-pack') ;
			?>
			<th width="10%">
				<?php echo '4.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[remove_private]' ;
				$item1 = (!empty($bsp_forum_display['remove_private']) ? $bsp_forum_display['remove_private'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Remove private prefix','bbp-style-pack') ;
				?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum6.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum7.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
<!--create new topic ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = __('Add "Create New Topic" link','bbp-style-pack')  ; 
			$desc = __('Adds a "Create New Topic" for individual forums', 'bbp-style-pack') ;
			?>
			<th width="10%">
				<?php echo '5.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[create_new_topic]' ;
				$item1 = (!empty($bsp_forum_display['create_new_topic']) ? $bsp_forum_display['create_new_topic'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
				_e('Add New topic link','bbp-style-pack') ;
				?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum7.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum8.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
<!--create new topic name ---------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Create New Topic Description','bbp-style-pack')  ; 
			$area1='Create New Topic Description' ;
			$item1="bsp_forum_display[".$area1."]" ;
                        $value = isset( $bsp_forum_display[$area1] ) ? esc_html( $bsp_forum_display[$area1] ) : '';
			?>
			<th>
			</th>
			<td>
				<?php echo $name ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.$value.'"<br>' ; ?> 
				<label class="description">
					<?php _e( 'By default this will be "Create New Topic".<p> Enter any alternate wording e.g. "Start new Topic", "Begin a debate" etc.', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
<!--add forum description ------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Add Forum Description', 'bbp-style-pack')  ; 
			$desc = __('Adds description to the display', 'bbp-style-pack') ;
			?>
			
			<th width="10%">
				<?php echo '6.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[add_forum_description]' ;
				$item1 = (!empty($bsp_forum_display['add_forum_description']) ? $bsp_forum_display['add_forum_description'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Add Forum Description','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum7.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum9.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
			
<!--add forum description to forum lists------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Add Sub Forum Description to forum lists','bbp-style-pack')  ; 
			$desc = __('Adds description to the Sub forum lists' , 'bbp-style-pack') ;
			?>
			
			<th width="10%">
				<?php echo '7.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[add_subforum_list_description]' ;
				$item1 = (!empty($bsp_forum_display['add_subforum_list_description']) ? $bsp_forum_display['add_subforum_list_description'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Add Forum List Description','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/sub-forum-desc1.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/sub-forum-desc2.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>			
<!--add rounded corners ------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Add Rounded Corners','bbp-style-pack')  ; 
			$desc = __('Adds rounded corners', 'bbp-style-pack') ;
			?>
			
			<th width="10%">
				<?php echo '8.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[rounded_corners]' ;
				$item1 = (!empty($bsp_forum_display['rounded_corners']) ? $bsp_forum_display['rounded_corners'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Add Rounded Corners','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/Corners1.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/Corners2.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
	
<!--Remove Forum Description ------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Remove Forum/Topic Description','bbp-style-pack')  ; 
			$desc = __('This will also remove it from topics as well', 'bbp-style-pack') ;
			?>
			
			<th width="10%">
				<?php echo '9.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[forum-description]' ;
				$item1 = (!empty($bsp_forum_display['forum-description']) ? $bsp_forum_display['forum-description'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Remove Forum/Topic Description','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum-description1.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/forum-description2.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
			
		</tr>
			
<!--Add thumbnails to forum lists ------------------------------------------------------------------->
		<tr>
			<?php
			$name = __('Add Thumbnails to forum lists','bbp-style-pack')  ; 
			$desc = __('Add a picture before the forum name in the forum lists', 'bbp-style-pack') ;
			?>
			
			<th width="10%">
				<?php echo '10.<br>'.$name.'</p><i>'.$desc.'</i>' ?>
			</th>
			<td width="16%">
				<?php
				$item =  'bsp_forum_display[thumbnail]' ;
				$item1 = (!empty($bsp_forum_display['thumbnail']) ? $bsp_forum_display['thumbnail'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
				_e('Add Thumbnails to forum lists','bbp-style-pack') ;
				?>
			</td>
			
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/thumbnail1.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td width="37%">
				<?php echo '<img src="' . plugins_url( 'images/thumbnail2.JPG',dirname(__FILE__)  ) . '" > '; ?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td colspan="3">
			<?php _e ('NOTE : once activated go to each forum, and you will see the ability to add an image and size options' , 'bbp-style-pack') ; ?>
			</td>
		</tr>
			
			
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
				<?php _e ('Where to display any forum description' , 'bbp-style-pack' ) ;?>	
			</td>
		</tr>
			
			
			<?php
			$name = 'forum_description' ;
			$area0 = 'location' ;
			$item0 = "bsp_forum_display[".$name.$area0."]" ;
			$value0 = (!empty($bsp_forum_display[$name.$area0]) ? $bsp_forum_display[$name.$area0] : 0) ;
			?>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
				_e ('Description under thumbnail' , 'bbp-style-pack' ) ;?>
			</td>
			
			<td style="vertical-align:top">
				<?php echo '<input name="'.$item0.'" id="'.$value0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
				_e ('Description beside thumbnail' , 'bbp-style-pack' ) ;?>
				
			</td>
			
		</tr>	
			
		<!-------------------------------11. no forums per page---------------------------------------->
		<tr valign="top">
			<th>
				11. <?php _e('No. Forums Per Page', 'bbp-style-pack'); ?>
			</th>
			<td colspan="2">
				<?php 
				$item1 = (!empty ($bsp_forum_display['number_forums'] ) ? $bsp_forum_display['number_forums']  : '' ) ?>
				<input id="bsp_forum_display[number_forums]" class="small-text" name="bsp_forum_display[number_forums]" type="text" value="<?php echo esc_html( $item1 ) ;?>" /><br/>
				<label class="description" for="bsp_forum_display[number_forums]"><?php _e( 'Default : 50', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<!-------------------------------12. freshness calculation---------------------------------------->
		<tr valign="top">
			<th>
				12. <?php _e('Sub Forum Freshness', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php 
				$item =  'bsp_forum_display[forum_freshness]' ;
				$item1 = (!empty($bsp_forum_display['forum_freshness']) ? $bsp_forum_display['forum_freshness'] : '');
				echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />'; 
				_e ('Recalculate Freshness for forums' , 'bbp-style-pack' ) ;?>
				</td>
				<td colspan="2">
				<label class="description" for="bsp_forum_display[forum_freshness]"><?php _e( 'A parent forum or category can get the wrong last active topic if a topic in a sub forum is marked as spam or deleted. By selecting this box, the correct last active topic is displayed, but this can slow the site if you have a lot of sub forums', 'bbp-style-pack' ); ?></label><br/>
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
		
		


	
