<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_settings_admin() {
 ?>
			
	<h3>
		<?php _e ('Dashboard Administration' , 'bbp-style-pack' ) ; ?>
	</h3>
	
		
	<?php global $bsp_settings_admin ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-admin', 'style-settings-admin' ) ?>
	<?php settings_fields( 'bsp_settings_admin' );
	?>
					
	<table class="form-table">
<!-- FORUMS     ----->		
		<tr>
			<th >
				<?php _e('Forums', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/forums-admin.png',dirname(__FILE__)  ) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td width="300" >
				1. <?php _e( 'Make topic and reply columns sortable', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_forum_sort'] ) ?  $bsp_settings_admin['activate_forum_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_forum_sort]" id="bsp_settings_admin[activate_forum_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_forum_sort]">
				<?php _e( 'This lets you sort these columns showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				2. <?php _e('Make topic and reply items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_forum_links'] ) ?  $bsp_settings_admin['activate_forum_links'] : '');
				echo '<input name="bsp_settings_admin[activate_forum_links]" id="bsp_settings_admin[activate_forum_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_forum_links]">
					<?php _e( 'When you click an item, it will list all the topics or replies for that forum', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>

		
<!-- TOPICS    ----->		
		<tr>
			<th >
				<?php _e('Topics', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/topics-admin.png',dirname(__FILE__)  ) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td>
				3. <?php _e( 'Make reply column sortable', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty($bsp_settings_admin['activate_topic_sort'] ) ?  $bsp_settings_admin['activate_topic_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_topic_sort]" id="bsp_settings_admin[activate_topic_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_topic_sort]">
				<?php _e( 'This lets you sort this column showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				4. <?php _e('Make Forum, Reply and Author items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_topic_links'] ) ?  $bsp_settings_admin['activate_topic_links'] : '');
				echo '<input name="bsp_settings_admin[activate_topic_links]" id="bsp_settings_admin[activate_topic_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_topics_links]">
					<?php _e( 'When you click an item, it will list the topics for that forum, replies for that topic, or topics by that author', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
<!-- REPLIES    ----->		
		<tr>
			<th >
				<?php _e('Replies', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/replies-admin.png',dirname(__FILE__)  ) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
		<tr>
			<td>
				5. <?php _e('Make Author items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_reply_links'] ) ?  $bsp_settings_admin['activate_reply_links'] : '');
				echo '<input name="bsp_settings_admin[activate_reply_links]" id="bsp_settings_admin[activate_reply_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_reply_links]">
					<?php _e( 'When you click an author, it will list replies by that author', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
<!-- Users    ----->		
		<tr>
			<th >
				<?php _e('Users', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/users-admin.png',dirname(__FILE__)  ) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td>
				6. <?php _e( 'Add New Topic and Reply columns', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_columns'] ) ?  $bsp_settings_admin['activate_user_columns'] : '');
				echo '<input name="bsp_settings_admin[activate_user_columns]" id="bsp_settings_admin[activate_user_columns]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_columns]">
				<?php _e( 'This adds new topic and reply columns', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				7. <?php _e('Make Topics and Replies Columns sortable', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_sort'] ) ?  $bsp_settings_admin['activate_user_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_user_sort]" id="bsp_settings_admin[activate_user_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_sort]">
					<?php _e( 'This lets you sort these columns showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<tr>
			<td>
				8. <?php _e('Make topic and reply items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_links'] ) ?  $bsp_settings_admin['activate_user_links'] : '');
				echo '<input name="bsp_settings_admin[activate_user_links]" id="bsp_settings_admin[activate_user_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_links]">
					<?php _e( 'When you click an item, it will list all the topics or replies for that user', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		
			
		</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save', 'bbp-style-pack' ); ?>" />
		</p>
	</form>
	
<?php
}







