<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum style settings page

function bsp_style_settings_email () {
	global $bsp_style_settings_email;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-email', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_email' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	?>
	<table class="form-table">
		<tr valign="top">
				<h3>
					<?php esc_html_e('Subscription emails' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
                <tr>
                	<p> <?php esc_html_e('This section allows you to amend the subscription emails sent on topic and reply creation.', 'bbp-style-pack'); ?> </p>
                </tr>
	</table>	
	
	
	<!-- save the options -->
        <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
        </p>
	<hr>
	<table class="form-table">
                <?php
                $sitename = strtolower( $_SERVER['SERVER_NAME'] );
                if ( substr( $sitename, 0, 4 ) === 'www.' ) {
                        $sitename = substr( $sitename, 4 );
                }
                $default_email = 'noreply@' . $sitename ;
                $name = 'email' ;
                $name1 = esc_html__('Email account', 'bbp-style-pack') ;
                $area1='_email_account' ;
                $item1="bsp_style_settings_email[".$name.$area1."]" ;
                $value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : $default_email) ;
				$area2='_from_name' ;
                $item2="bsp_style_settings_email[".$name.$area2."]" ;
                $value2 = (!empty($bsp_style_settings_email[$name.$area2]) ? $bsp_style_settings_email[$name.$area2]  : '') ;
                ?>
                <tr>

                        <th>
                                1. <?php echo esc_html($name1) ; ?>
                        </th>
						</tr>
						<tr>
                        <td valign="top">
						<?php esc_html_e('Email Address' , 'bbp-style-pack' ) ;?>
						</td>
						<td>
                                        <?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"> <br>' ; ?> 
                                        <label class="description"><?php esc_html_e( 'By default bbpress sends an email using noreply@yoursite as both the sending and receiving email address.  Each subscriber is then blind copied in (bcc\'d ).				', 'bbp-style-pack' ); ?></label><br/>
                                        <label class="description"><?php esc_html_e( 'This means on topic or reply creation a single email is sent, which makes for fast processing', 'bbp-style-pack' ); ?></label><br/>
                                        <label class="description"><?php esc_html_e( 'You may wish to change this address in some cases, for instance if you are on a sub domain ' , 'bbp-style-pack' ); ?></label><br/>
                                <label class="description"><?php esc_html_e( 'Many email sytems will reject emails which don\'t come from the site\'s domain or real email addresses so amend with care !' , 'bbp-style-pack' ); ?></label><br/>

                        </td>
                </tr>
				<tr>
                        <td valign="top">
						<?php _e ('From Name' , 'bbp-style-pack' ) ;?>
						</td>
						<td>
                                        <?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"> <br>' ; ?> 
                                        <label class="description"><?php esc_html_e( 'By default bbpress sends an email using your site title ie ', 'bbp-style-pack' ).bloginfo( 'name' ); ?></label><br/>
                                        <label class="description"><?php esc_html_e( 'Leave blank, unless you wish to change this', 'bbp-style-pack' ); ?></label><br/>
                                        <label class="description"><?php esc_html_e( 'Email sytems may reject emails which appear to be spoofing another site so amend with care !' , 'bbp-style-pack' ); ?></label><br/>

                        </td>
                </tr>


                <tr>
                        <?php $name1 = esc_html__('Auto Login', 'bbp-style-pack') ; ?>	
			<th>
				2. <?php echo esc_html($name1) ; ?> 
			</th>
		
			<td>
				<?php
				$area1 =  '_activate_auto_login' ;
				$item1="bsp_style_settings_email[".$name.$area1."]" ;
				$value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : '') ;
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,esc_html($value1), false ) . ' />' ; ?>
				<label class="description"><?php esc_html_e( 'Activate Auto Login', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'When users receive an email, it contains a link to the topic/reply.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'If the forum is private and they are not logged in, they will get the site 404 not found error.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'This item instead lets you select a wordpress login, bbress login or other login, which once completed continues to the topic/reply.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'If you select bbPress Login, you must have the [bbp-login] shortcode in a page, and put the full url in below.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'Some themes or plugins also add a login page that users use to login, in this case select "bbPress Login or login using a specific page" and put the full url in below.  Whether this works will depend on the theme or plugin being used, so I cannot guarantee that this will work', 'bbp-style-pack' ); ?></label><br/>
				


				
			</td>
		</tr>
		<tr>
                        <td>
                        </td>
                        <td colspan = '2'>
				
				<?php
		
                                $name1 = esc_html__('Login users to private forum links', 'bbp-style-pack') ;
                                $area1='_private_login_type' ;
                                $item1="bsp_style_settings_email[".$name.$area1."]" ;
                                $value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : 0) ;
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="0" class="code"  ' . checked( 0,esc_html($value1), false ) . ' />' ;
				_e ('Wordpress Login' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="1" class="code"  ' . checked( 1,esc_html($value1), false ) . ' />' ;
				_e ('bbPress Login or login using a specific page' , 'bbp-style-pack' ) ;?>
				<p/>
															
			</td>		
		</tr>
		
        <tr>
		<td></td>
        </tr>
	
	<tr>
		<td></td>
		<td>
		<?php
                        $item =  'email_bbpress_login_url' ;
                        $item1="bsp_style_settings_email[".$item."]" ;
                        $value1 = (!empty($bsp_style_settings_email[$item]) ? $bsp_style_settings_email[$item]  : '') ;
                        echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"> <br>' ; ?> 
                        <label class="description"><b><?php esc_html_e( 'If you are using bbPress login ', 'bbp-style-pack' ) ; ?>
						</b>
						<?php esc_html_e('you will have or need a wordpress page with the [bbp-login] shortcode in it.', 'bbp-style-pack' ); ?></label><br/>
                        <label class="description"><b><?php esc_html_e( 'Some themes or plugins also add a login page that users use to login', 'bbp-style-pack' ); ?></b></label><br/>

                        <label class="description"><?php esc_html_e( 'Enter the full URL of this page here.', 'bbp-style-pack' ); ?></label><br/>
		</td>
        </tr>
		
        
        <tr>
		<th>
                        3. <?php esc_html_e('Roles To Email', 'bbp-style-pack'); ?>
		</th>
		<td>
                        <?php esc_html_e( 'Specify which roles you want to receive subscription emails.', 'bbp-style-pack' ); ?>
						<p>
						<?php esc_html_e( 'If you deselect a role here, users in that role will not receive emails even if they have previously subscribed to forums or topics.', 'bbp-style-pack' ); ?>
						<p>
						<?php esc_html_e( 'For roles that are not selected, users in that role will no longer see subscription options for forums or topics.', 'bbp-style-pack' ); ?>
		
		</td>
	</tr>
	
	<tr>
                <td>
                </td>
		<td>
			<?php
			$item =  'email_roles' ;
			$item1 = "bsp_style_settings_email[".$item."]";
                        $value1 = ( ! empty( $bsp_style_settings_email['email_roles'] ) ? maybe_unserialize( $bsp_style_settings_email['email_roles'] ) : bsp_default_roles_to_email() );
                        $bbp_roles = bbp_get_dynamic_roles();
                        ksort( $bbp_roles );

                        // loop through bbp roles
                        foreach ( $bbp_roles as $role => $role_values ) {
                                // is this role in the current set roles to email? if so,, set to checked
                                $checked = in_array( $role, $value1 ) ? true : false;
                                // display input checkbox  for for role
                                echo '<input name="' .esc_html($item1). '[' . $role . ']" id="' .esc_html($item1). '[' . $role . ']" type="checkbox" value="' . $role . '" class="code" ' . checked( true, $checked, false ) . ' /> ' . $role_values['name'];
                                echo '<br/>';
                        }
                        // include a hidden value so that we can preserve not sending emails for any role if site admin unchecked all and saved
                        echo '<input name="' .esc_html($item1). '[placeholder]" id="' .esc_html($item1). '[placeholder]" type="hidden" value="placeholder" class="code" />';
			?>
		</td>
	</tr>
        
        
	<tr>
		<?php $name1 = esc_html__('Change Email Content', 'bbp-style-pack') ; ?>	
			<th>
				4. <?php echo esc_html($name1) ; ?> 
			</th>
			<td>
				<?php
				$area1 =  '_activate_email_content' ;
				$item1="bsp_style_settings_email[".$name.$area1."]" ;
				$value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : '') ;
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,esc_html($value1), false ) . ' />' ; ?>
				<label class="description"><?php esc_html_e( 'If you want to alter the topic and/or reply emails then click here and items 5 to 10 will take effect', 'bbp-style-pack' ); ?></label><br/>
                        </td>
        </tr>
		
	<?php
		
			$name1 = esc_html__('Email Type', 'bbp-style-pack') ;
			$area1='_email_type' ;
			$item1="bsp_style_settings_email[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : 0) ;
		?>
		<tr>	
			<th>
				5. <?php echo esc_html($name1) ; ?> 
			</th>
				<td colspan = '2'>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="0" class="code"  ' . checked( 0,esc_html($value1), false ) . ' />' ;
				_e ('Plain Text' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="1" class="code"  ' . checked( 1,esc_html($value1), false ) . ' />' ;
				_e ('HTML Text' , 'bbp-style-pack' ) ;?>
				<p/>
				<label class="description"><?php esc_html_e( 'By default bbpress uses plain text to send emails.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><b><?php esc_html_e( 'If you want HTML text, then click HTML text, and press the \'save\' button, and the wordpress text editor will appear on the refreshed screen', 'bbp-style-pack' ); ?></b></label><br/>
				<label class="description"><?php esc_html_e( 'Almost all email services can process HTML text, and you may want to make your subscription emails look better by using this feature.', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'However if you do, then any email system that is only capable of using plain text may look strange as the HTML text code may also show.', 'bbp-style-pack' ); ?></label><br/>
																
			</td>		
		</tr>
	
	</table>
	
	<table class="form-table">
	<?php
	
	$default_topic_title = '[' . get_option( 'blogname' ) . '] {title}';
	$default_reply_title = '[' . get_option( 'blogname' ) . '] {title}';
	
	$default_topic_body_p = '{author} wrote:

{content}

Post Link: {url}

-----------

You are receiving this email because you subscribed to the {forum_name} forum.

Login and visit the forum to unsubscribe from these emails.';
$default_topic_body_h = '{author} wrote:<p>{content}</p><p>Post Link: {url}</p><hr><p>You are receiving this email because you subscribed to the {forum_name} forum.</p>
<p>Login and visit the forum to unsubscribe from these emails.</p>';

$default_reply_body_p = '{author} wrote:

{content}

Post Link: {url}

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic to unsubscribe from these emails.'; 

$default_reply_body_h = '{author} wrote:<p>{content}</p><p>Post Link: {url}</p><p><hr><p>You are receiving this email because you subscribed to a forum topic.</p>
<p>Login and visit the topic to unsubscribe from these emails.</p>';


			$name = 'email' ;
			$name1 = esc_html__('New Topic Email Title', 'bbp-style-pack') ;
			$name2= esc_html__('New Topic Email Body', 'bbp-style-pack') ;
			$name3 = esc_html__('New Reply Email Title', 'bbp-style-pack') ;
			$name4 = esc_html__('New Reply Email Body', 'bbp-style-pack') ;
			$area1='_topic_title' ;
			$area2p='_topic_body_p' ;
			$area2h='_topic_body_h' ;
			$area3='_reply_title' ;
			$area4p='_reply_body_p' ;
			$area4h='_reply_body_h' ;
			$item1="bsp_style_settings_email[".$name.$area1."]" ;
			$item2p="bsp_style_settings_email[".$name.$area2p."]" ;
			$item2h="bsp_style_settings_email[".$name.$area2h."]" ;
			$item3="bsp_style_settings_email[".$name.$area3."]" ;
			$item4p="bsp_style_settings_email[".$name.$area4p."]" ;
			$item4h="bsp_style_settings_email[".$name.$area4h."]" ;
			$value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : $default_topic_title) ;
			$value2p = (!empty($bsp_style_settings_email[$name.$area2p]) ? $bsp_style_settings_email[$name.$area2p]  : $default_topic_body_p) ;
			$value2h = (!empty($bsp_style_settings_email[$name.$area2h]) ? $bsp_style_settings_email[$name.$area2h]  : $default_topic_body_h) ;
			$value3 = (!empty($bsp_style_settings_email[$name.$area3]) ? $bsp_style_settings_email[$name.$area3]  : $default_reply_title) ;
			$value4p = (!empty($bsp_style_settings_email[$name.$area4p]) ? $bsp_style_settings_email[$name.$area4p]  : $default_reply_body_p) ;
			$value4h = (!empty($bsp_style_settings_email[$name.$area4h]) ? $bsp_style_settings_email[$name.$area4h]  : $default_reply_body_h) ;
	
	

		?>
		
		<tr>
			<th>
				6. <?php echo esc_html($name1) ;?>
			</th>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"> <br>' ; ?> 
				<label class="description"><?php esc_html_e( 'The subject of the notification email', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'Allowable codes - {site_title} {title} {forum_name}', 'bbp-style-pack' ); ?></label><br/>
			</td>

		<tr>
			<th>
				7. <?php echo esc_html($name2) ;?>
			</th>
			<td colspan=2>
			<?php if (empty ($bsp_style_settings_email['email_email_type'])) {
				echo '<textarea id="'.$item2p.'" class="large-text" name="'.$item2p.'" type="text" rows="15">'.esc_html($value2p).'</textarea>' ; 
				echo '<input type="hidden" id="'.$item2h.'" name="'.$item2h.'" value="'.$value2h.'">' ; 
			} 
			else {
			wp_editor( $value2h, 'bsp_style_topic', array(
				'textarea_name' => $item2h,
				'textarea_rows' => '15',
				// TRUE to output the minimal editor config, such as the Comment editor.
				'teeny'         => false,
			) );
			echo '<input type="hidden" id="'.$item2p.'" name="'.$item2p.'" value="'.esc_html($value2p).'">' ;
			} ?>

					
					<label class="description"><?php esc_html_e( 'Email message sent to forum subscribers when a new topic is posted', 'bbp-style-pack' ); ?></label><br/>
					<label class="description"><?php esc_html_e( 'Allowable codes - {site_title} {title} {author} {content} {excerpt} {url} {forum_name}', 'bbp-style-pack' ); ?></label><br/>
					
			</td>
		</tr>
		
		<tr>
			<th>
				8. <?php echo esc_html($name3) ;?>
			</th>
			<td>
				<?php echo '<input id="'.esc_html($item3).'" class="large-text" name="'.esc_html($item3).'" type="text" value="'.esc_html( $value3 ).'"> <br>' ; ?> 
				<label class="description"><?php esc_html_e( 'The subject of the notification email', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'Allowable codes - {site_title} {title} {forum_name}', 'bbp-style-pack' ); ?></label><br/>
			</td>

		<tr>
			<th>
				9. <?php echo esc_html($name4) ;?>
			</th>
			<td colspan=2>
			<?php if (empty ($bsp_style_settings_email['email_email_type'])) {
				echo '<textarea id="'.esc_html($item4p).'" class="large-text" name="'.esc_html($item4p).'" type="text" rows="15">'.esc_html($value4p).'</textarea>' ; 
				echo '<input type="hidden" id="'.esc_html($item4h).'" name="'.esc_html($item4h).'" value="'.esc_html($value4h).'">' ; 
			} 
			else {
			wp_editor( $value4h, 'bsp_style_reply', array(
				'textarea_name' => $item4h,
				'textarea_rows' => '15',
				// TRUE to output the minimal editor config, such as the Comment editor.
				'teeny'         => false,
			) );
			echo '<input type="hidden" id="'.esc_html($item4p).'" name="'.esc_html($item4p).'" value="'.esc_html($value4p).'">' ;
			} ?>

					
					<label class="description"><?php esc_html_e( 'Email message sent to forum subscribers when a new topic is posted', 'bbp-style-pack' ); ?></label><br/>
					<label class="description"><?php esc_html_e( 'Allowable codes - {site_title} {title} {author} {content} {excerpt} {url} {forum_name}', 'bbp-style-pack' ); ?></label><br/>
					
			</td>
		</tr>
		
		<?php
		$name1 = esc_html__('Excerpt type and length', 'bbp-style-pack') ;
		$name2 = esc_html__('Excerpt length', 'bbp-style-pack') ;
		$area1='_length' ;
		$value1 = (!empty($bsp_style_settings_email[$name.$area1]) ? $bsp_style_settings_email[$name.$area1]  : 100) ;
		$item1="bsp_style_settings_email[".$name.$area1."]" ;
		$area2='_excerpt_type' ;
		$value2 = (!empty($bsp_style_settings_email[$name.$area2]) ? $bsp_style_settings_email[$name.$area2]  : 'char') ;
		$item2="bsp_style_settings_email[".$name.$area2."]" ;
			
		?>
		<tr>	
			<th>
				10. <?php echo esc_html($name1) ; ?> 
			</th>
			<td>
				<label class="description"><b><?php esc_html_e( 'If you are not using {excerpt} in the email body then you can ignore these settings.', 'bbp-style-pack' ); ?></b></label><br/>
				<label class="description"><?php esc_html_e( 'If you are using {excerpt} then the excerpt type and length can be set here ', 'bbp-style-pack' ); ?></label><br/>
			<td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name2) ; ?> 
				<?php echo '<input id="'.esc_html($item1).'" class="small-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'">' ; ?> 
			</td>
		</tr>
		<tr>
		<td>
		</td>
		<td>
			<?php
				echo '<input name="'.esc_html($item2).'" id="'.esc_html($item2).'" type="radio" value="char" class="code"  ' . checked( 'char',$value2, false ) . ' />' ;
				_e ('Characters' , 'bbp-style-pack' ) ;
			?>
				<p/>
			<?php
				echo '<input name="'.esc_html($item2).'" id="'.esc_html($item2).'" type="radio" value="words" class="code"  ' . checked( 'words',$value2, false ) . ' />' ;
				_e ('Words' , 'bbp-style-pack' ) ;
			?>
				<p/>
														
			</td>		
		</tr>
		
		<!-- checkbox to activate  -->
	<tr>
		<th>
                        11. <?php esc_html_e('Send test email', 'bbp-style-pack'); ?>
		</th>
		<td>
			<b>
			<?php esc_html_e('Save changes before sending test emails', 'bbp-style-pack'); ?>
			</b>
		</td>
	</tr>
	
	<tr>
                <td>
                </td>
		<td>
			<?php
			$item =  'test_topic_email' ;
			$item="bsp_style_settings_email[".$item."]" ;
			$item1 = '' ;
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item).'" type="checkbox" value="1" class="code" ' . checked( 1,esc_html($item1), false ) . ' />' ;
				esc_html_e('Send a test topic email', 'bbp-style-pack');
			?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php
			$item =  'test_reply_email' ;
			$item="bsp_style_settings_email[".$item."]" ;
			$item1 = '' ;
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item).'" type="checkbox" value="1" class="code" ' . checked( 1,esc_html($item1), false ) . ' />' ;
				esc_html_e('Send a test reply email', 'bbp-style-pack');
			?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php
			$item =  'test_email_address' ;
			$item1="bsp_style_settings_email[".$item."]" ;
			$value1 = (!empty($bsp_style_settings_email[$item]) ? $bsp_style_settings_email[$item]  : get_bloginfo('admin_email') ) ;
			echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"> <br>' ; ?> 
					<label class="description"><?php esc_html_e( 'The email recipient address of the test email - Default - your site admin email address', 'bbp-style-pack' ); ?></label><br/>
		</td>
	</tr>
		
	<tr>
		<td>
		</td>
		<td>
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Send test email(s)', 'bbp-style-pack' ); ?>" />
		</td>
	</tr>
		
		
	
		
	
	
		
		</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
		</p>
</form>
	 
<?php
}




	
