<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//functions for the email tab
global $bsp_style_settings_email ;

add_filter ('bbp_get_do_not_reply_address', 'bsp_no_reply') ;


if (!empty($bsp_style_settings_email['email_activate_email_content'])) {
	add_filter( 'bbp_forum_subscription_mail_title',   'bsp_topic_title'  , 10, 3 );
	add_filter( 'bbp_forum_subscription_mail_message', 'bsp_topic_message' , 10, 3 );
	add_filter( 'bbp_subscription_mail_title',   'bsp_reply_title'  , 10, 3 );
	add_filter( 'bbp_subscription_mail_message', 'bsp_reply_message' , 10, 3 );
	

	if (!empty ($bsp_style_settings_email['email_email_type'])) {
		add_action ('bbp_pre_notify_forum_subscribers', 'bsp_html_email') ;
		add_action ('bbp_post_notify_forum_subscribers', 'bsp_remove_html_email') ;
		add_action ('bbp_pre_notify_subscribers', 'bsp_html_email') ;
		add_action ('bbp_post_notify_subscribers', 'bsp_remove_html_email') ;
	}
}


function bsp_no_reply ($no_reply) {
	global $bsp_style_settings_email ;
	$no_reply = (!empty ($bsp_style_settings_email['email_email_account']) ?  $bsp_style_settings_email['email_email_account'] : $no_reply) ;
        return $no_reply ;
}

if (!empty ($bsp_style_settings_email['email_from_name'])) {
add_filter ('bbp_subscription_mail_headers' , 'bsp_change_email_from_name', 10 , 1 ) ;
}


function bsp_change_email_from_name ($headers) {
	global $bsp_style_settings_email ;
	$name = 'From: ' . get_bloginfo( 'name' ) ;
		foreach ($headers as $header=>$header_name) {	
			if( strpos( $header_name, $name ) !== false)	{
				//replace the site nane with the changed ;
				$new_name = 'From: ' .$bsp_style_settings_email['email_from_name'] ;
				// re Setup the From header
				$headers[$header]  = str_replace($name,$new_name, $header_name);
			}	
		}
return $headers ;		
}


function bsp_html_email () {
        add_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
}

function bsp_remove_html_email () {
        remove_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
}

function bsp_set_html_content_type() {
        return 'text/html';
}



function bsp_topic_title( $title, $topic_id, $forum_id ) {
        global $bsp_style_settings_email ;

        $subject = (!empty($bsp_style_settings_email['email_topic_title']) ? $bsp_style_settings_email['email_topic_title']  : $title) ;

        // Because we're expecting a string from get_option(), let's use is_string()
        // to check for a string and then ensure the string is longer than `0`. If it isn't
        // a string, bail returning the original $title.
        if ( ! is_string( $subject ) && strlen( $subject ) == 0 ) {
                return $title;
        }

        $site_title = get_bloginfo( 'name' );
        $title = strip_tags( bbp_get_topic_title( $topic_id ) );
		$forum_name = bbp_get_forum_title( $forum_id );
		
        $subject = str_replace( '{site_title}',  $site_title,  $subject );
        $subject = str_replace( '{title}',  $title,  $subject );
		$subject = str_replace( '{forum_name}', $forum_name, $subject );		

        return $subject;
}
	
function bsp_topic_message( $message, $topic_id, $forum_id ) {
        global $bsp_style_settings_email ;
        //strip tags if content is plain text
        if (empty ($bsp_style_settings_email['email_email_type'])) $topic_content = strip_tags( bbp_get_topic_content( $topic_id ) );
        else $topic_content 	= bbp_get_topic_content( $topic_id ) ;

        $excerpt_length = (!empty($bsp_style_settings_email['email_length']) ? $bsp_style_settings_email['email_length']  : 100) ;
        $excerpt_type = (!empty($bsp_style_settings_email['email_excerpt_type']) ? $bsp_style_settings_email['email_excerpt_type']  : 'char') ;
        $topic_excerpt = bsp_get_topic_excerpt($topic_id, $excerpt_length, $excerpt_type ) ;

        $topic_url     	= bbp_get_topic_permalink( $topic_id );
		$topic_url = apply_filters ('bsp_topic_message_url' , $topic_url ) ;
        $topic_author	= bbp_get_topic_author_display_name( $topic_id );
        $forum_name     = bbp_get_forum_title( $forum_id );
        $title = strip_tags( bbp_get_topic_title( $topic_id ) );
        $site_title = get_bloginfo( 'name' );

        //check which email message to load - if !empty = HTML
        if (!empty ($bsp_style_settings_email['email_email_type'])) $type='email_topic_body_h' ;
        else $type='email_topic_body_p' ;

        //old $message = (!empty($bsp_style_settings_email[$type]) ? $bsp_style_settings_email[$type]  : $message) ;
		//amended to allow wpml to translate
		$message = (!empty($bsp_style_settings_email[$type]) ? __($bsp_style_settings_email[$type],'bbp-style-pack') : $message) ;
				
        $message = str_replace( '{author}',  $topic_author,  $message );
        $message = str_replace( '{content}', $topic_content, $message );
        $message = str_replace( '{excerpt}', $topic_excerpt, $message );
        $message = str_replace( '{url}',     $topic_url,     $message );
        $message = str_replace( '{forum_name}', $forum_name, $message );
        $message = str_replace( '{title}', $title, $message );
        $message = str_replace( '{site_title}', $site_title, $message );

        //add html text if HTML
        if (!empty ($bsp_style_settings_email['email_email_type'])) {
                //replace cr with <br> as html doesnlt seem to recognise cr
				$message = str_replace( "\r\n",  '<br>', $message );
                $message = str_replace( "\r",  '<br>', $message );
				$message = str_replace( "\n",  '<br>', $message );
				
                $message = '<html><head></head><body>'.$message.'</body></html>' ;
        }
        return $message;
}
	

function bsp_reply_title( $title, $reply_id, $topic_id) {
        global $bsp_style_settings_email ;

        $subject = (!empty($bsp_style_settings_email['email_reply_title']) ? $bsp_style_settings_email['email_reply_title']  : $title) ;

        // Because we're expecting a string from get_option(), let's use is_string()
        // to check for a string and then ensure the string is longer than `0`. If it isn't a string, bail returning the original $title.
        if ( ! is_string( $subject ) && strlen( $subject ) == 0 ) {

                return $title;
        }

        $site_title = get_bloginfo( 'name' );
        $title = strip_tags( bbp_get_topic_title( $topic_id ) );
		$forum_name = bbp_get_forum_title( $forum_id );
        $subject = str_replace( '{site_title}',  $site_title,  $subject );
        $subject = str_replace( '{title}',  $title,  $subject );
		$subject = str_replace( '{forum_name}', $forum_name, $subject );		


        return $subject;
}

function bsp_reply_message($message, $reply_id, $topic_id ) {
        global $bsp_style_settings_email ;
        //strip tags if content is plain text
        if (empty ($bsp_style_settings_email['email_email_type'])) $reply_content = strip_tags( bbp_get_reply_content( $reply_id ) );
        else $reply_content 	= bbp_get_reply_content( $reply_id ) ;

        $excerpt_length = (!empty($bsp_style_settings_email['email_length']) ? $bsp_style_settings_email['email_length']  : 100) ;
        $excerpt_type = (!empty($bsp_style_settings_email['email_excerpt_type']) ? $bsp_style_settings_email['email_excerpt_type']  : 'char') ;
        $reply_excerpt = bsp_get_reply_excerpt( $reply_id, $excerpt_length, $excerpt_type ) ;

        $reply_url     = bbp_get_reply_url( $reply_id );
		$reply_url = apply_filters ('bsp_reply_message_url' , $reply_url, $reply_id) ;
		
        // Poster name
        $reply_author_name = bbp_get_reply_author_display_name( $reply_id );
        $forum_id 		= bbp_get_topic_forum_id ($topic_id) ;
        $forum_name     = bbp_get_forum_title( $forum_id );
        $title = strip_tags( bbp_get_topic_title( $topic_id ) );
        $site_title = get_bloginfo( 'name' );

        //check which email message to load - if !empty = HTML
        if (!empty ($bsp_style_settings_email['email_email_type'])) $type='email_reply_body_h' ;
        else $type='email_reply_body_p' ;

        //old $message = (!empty($bsp_style_settings_email[$type]) ? $bsp_style_settings_email[$type]  : $message) ;
		//allow wpml to translate
		$message = (!empty($bsp_style_settings_email[$type]) ? __($bsp_style_settings_email[$type],'bbp-style-pack') : $message) ;
		 
		 
        $message = str_replace( '{author}',  $reply_author_name,  $message );
        $message = str_replace( '{content}', $reply_content, $message );
        $message = str_replace( '{excerpt}', $reply_excerpt, $message );
        $message = str_replace( '{url}',     $reply_url,     $message );
        $message = str_replace( '{forum_name}', $forum_name, $message );
        $message = str_replace( '{title}', $title, $message );
        $message = str_replace( '{site_title}', $site_title, $message );

        //add html text if HTML
        if (!empty ($bsp_style_settings_email['email_email_type'])) {
                //replace cr with <br> as html doesnlt seem to recognise cr
				$message = str_replace( "\r\n",  '<br>', $message );
                $message = str_replace( "\r",  '<br>', $message );
				$message = str_replace( "\n",  '<br>', $message );
				
				
                $message = '<html><head></head><body>'.$message.'</body></html>' ;
        }
        return $message;
}


function bsp_test_email ($input) {
	//remember to return $input at end, as otherwise settings don't get saved !
	global $bsp_style_settings_email ;
//TOPIC see if we need to send a test topic email
	if (!empty ($input['test_topic_email'] )) {
	//set up the header
		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_subscription_from_email', $no_reply );
		$name = (!empty($bsp_style_settings_email['email_from_name']) ? $bsp_style_settings_email['email_from_name']  : get_bloginfo( 'name' )) ;
		// Setup "From" email address
		$headers = array( 'From: ' . $name . ' <' . $from_email . '>' );
		// Get email address of test user
		$header_recip = (!empty($bsp_style_settings_email['test_email_address']) ? $bsp_style_settings_email['test_email_address']  : get_bloginfo('admin_email')) ;
		$headers[] = 'Bcc: '.$header_recip ;
		
		
		//set up the title
			$title = '[' . get_option( 'blogname' ) . '] {title}';
			$subject = (!empty($bsp_style_settings_email['email_topic_title']) ? $bsp_style_settings_email['email_topic_title']  : $title) ;
			$site_title = get_bloginfo( 'name' );
			$title = 'Test Topic Title' ;
			$forum_name = 'Test Forum Name' ;
			$subject = str_replace( '{site_title}',  $site_title,  $subject );
			$subject = str_replace( '{title}',  $title,  $subject );
			$subject = str_replace( '{forum_name}', $forum_name, $subject );
			$title = $subject ;
						

		//set up the body	
			
			$topic_content 	= 'This is a sample of the content' ;
			$topic_excerpt 	= 'This is a sample of the content excerpt' ;
			
			$topic_url     	= get_home_url().'/test_content/' ;
			$topic_author	= 'Fred Jones' ;
							
			
			$message = '' ;
			//check which email message to load - if !empty = HTML
			if (!empty ($bsp_style_settings_email['email_email_type'])) $type='email_topic_body_h' ;
			else $type='email_topic_body_p' ;

			//old $message = (!empty($bsp_style_settings_email[$type]) ? $bsp_style_settings_email[$type]  : $message) ;
			//amended to allow wpml to translate
			$message = (!empty($bsp_style_settings_email[$type]) ? __($bsp_style_settings_email[$type],'bbp-style-pack') : $message) ;

			
			$message = str_replace( '{author}',  $topic_author,  $message );
			$message = str_replace( '{content}', $topic_content, $message );
			$message = str_replace( '{excerpt}', $topic_excerpt, $message );
			$message = str_replace( '{url}',     $topic_url,     $message );
			$message = str_replace( '{forum_name}', $forum_name, $message );
			$message = str_replace( '{title}', $title, $message );
			$message = str_replace( '{site_title}', $site_title, $message );
			//add html text if HTML
			if (!empty ($bsp_style_settings_email['email_email_type'])) {
				 //replace cr with <br> as html doesnlt seem to recognise cr
				$message = str_replace( "\r\n",  '<br>', $message );
                $message = str_replace( "\r",  '<br>', $message );
				$message = str_replace( "\n",  '<br>', $message );
				
				$message = '<html><head></head><body>'.$message.'</body></html>' ;
			}
			
		// Send notification email
			$to_email = $bsp_style_settings_email['email_email_account'] ;
			if (!empty ($bsp_style_settings_email['email_email_type'])) add_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
			wp_mail( $to_email, $title, $message, $headers );
			if (!empty ($bsp_style_settings_email['email_email_type'])) remove_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
		

	}
	
//REPLY see if we need to send a test reply email
	if (!empty ($input['test_reply_email'] )) {
		
	
		//set up the header
		$no_reply   = bbp_get_do_not_reply_address();

		// Setup "From" email address
		$from_email = apply_filters( 'bbp_subscription_from_email', $no_reply );
		$name = (!empty($bsp_style_settings_email['email_from_name']) ? $bsp_style_settings_email['email_from_name']  : get_bloginfo( 'name' )) ;
		// Setup "From" email address
		$headers = array( 'From: ' . $name . ' <' . $from_email . '>' );
		// Get email address of test user
		$header_recip = (!empty($bsp_style_settings_email['test_email_address']) ? $bsp_style_settings_email['test_email_address']  : get_bloginfo('admin_email')) ;
		$headers[] = 'Bcc: '.$header_recip ;
		
			//set up the title
			$title = '[' . get_option( 'blogname' ) . '] {title}';
			$subject = (!empty($bsp_style_settings_email['email_reply_title']) ? $bsp_style_settings_email['email_reply_title']  : $title) ;
			$site_title = get_bloginfo( 'name' );
			$title = 'Test Reply Title' ;
			$forum_name = 'Test Forum Name' ;
			$subject = str_replace( '{site_title}',  $site_title,  $subject );
			$subject = str_replace( '{title}',  $title,  $subject );
			$subject = str_replace( '{forum_name}', $forum_name, $subject );
			$title = $subject ;
						

		//set up the body	
			
			$reply_content 	= 'This is a sample of the content' ;
			$reply_excerpt 	= 'This is a sample of the content excerpt' ;
			$topic_url     	= get_home_url().'/test_content/' ;
			$topic_author	= 'Fred Jones' ;
			$forum_name     = 'General';
			$message = '' ;
			if (!empty ($bsp_style_settings_email['email_email_type'])) {
			$message = (!empty($bsp_style_settings_email['email_reply_body_h']) ? $bsp_style_settings_email['email_reply_body_h']  : $message) ;
			}
			else {
			$message = (!empty($bsp_style_settings_email['email_reply_body_p']) ? $bsp_style_settings_email['email_reply_body_p']  : $message) ;
			}

			
			$message = str_replace( '{author}',  $topic_author,  $message );
			$message = str_replace( '{content}', $reply_content, $message );
			$message = str_replace( '{excerpt}', $reply_excerpt, $message );
			$message = str_replace( '{url}',     $topic_url,     $message );
			$message = str_replace( '{forum_name}', $forum_name, $message );
			//add html text if HTML
			if (!empty ($bsp_style_settings_email['email_email_type'])) {
				//replace cr with <br> as html doesnlt seem to recognise cr
				$message = str_replace( "\r\n",  '<br>', $message );
                $message = str_replace( "\r",  '<br>', $message );
				$message = str_replace( "\n",  '<br>', $message );
				
				$message = '<html><head></head><body>'.$message.'</body></html>' ;
			}
			
			// Send notification email
			$to_email = $bsp_style_settings_email['email_email_account'] ;
			if (!empty ($bsp_style_settings_email['email_email_type'])) add_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
			wp_mail( $to_email, $title, $message, $headers );
			if (!empty ($bsp_style_settings_email['email_email_type'])) remove_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );

	}
	
        return $input;
}


function bsp_get_reply_excerpt( $reply_id = 0, $length = 100, $excerpt_type='char' ) {
        $reply_id = bbp_get_reply_id( $reply_id );
        $length   = (int) $length;
        $excerpt  = get_post_field( 'post_excerpt', $reply_id );

        if ( empty( $excerpt ) ) {
                $excerpt = bbp_get_reply_content( $reply_id );
        }

        $excerpt = trim ( strip_tags( $excerpt ) );

        if ($excerpt_type=='char') {

                // Multibyte support
                if ( function_exists( 'mb_strlen' ) ) {
                        $excerpt_length = mb_strlen( $excerpt );
                } else {
                        $excerpt_length = strlen( $excerpt );
                }

                if ( ! empty( $length ) && ( $excerpt_length > $length ) ) {
                        $excerpt  = mb_substr( $excerpt, 0, $length );
                }

        }
        elseif ($excerpt_type=='words')  {
                $excerpt = wp_trim_words( $excerpt, $length, '');
        }

        // Filter & return
        return apply_filters( 'bsp_get_reply_excerpt', $excerpt, $reply_id, $length, $excerpt_type );
}
	
function bsp_get_topic_excerpt( $topic_id = 0, $length = 100, $excerpt_type='char' ) {
        $topic_id = bbp_get_topic_id( $topic_id );
        $length   = (int) $length;
        $excerpt  = get_post_field( 'post_excerpt', $topic_id );

        if ( empty( $excerpt ) ) {
                $excerpt = bbp_get_topic_content( $topic_id );
        }

        $excerpt = trim( strip_tags( $excerpt ) );

        if ($excerpt_type=='char') {

                // Multibyte support
                if ( function_exists( 'mb_strlen' ) ) {
                        $excerpt_length = mb_strlen( $excerpt );
                } else {
                        $excerpt_length = strlen( $excerpt );
                }

                if ( ! empty( $length ) && ( $excerpt_length > $length ) ) {
                        $excerpt  = mb_substr( $excerpt, 0, $length );
                }
        }

        elseif ($excerpt_type=='words')  {
                $excerpt = wp_trim_words( $excerpt, $length, '');
        }


        // Filter & return
        return apply_filters( 'bsp_get_topic_excerpt', $excerpt, $topic_id, $length, $excerpt_type );
}


// set default roles for subscription emails
// used by functions_email.php and settings_email.php
function bsp_default_roles_to_email() {
    return array( 'bbp_keymaster', 'bbp_moderator', 'bbp_participant', 'bbp_senior_moderator' );
}


// add the filter to limit subscription emails to specific roles (active subscriptions) as set in subscription emails tab item 3
add_filter( 'bbp_forum_subscription_user_ids', 'bsp_get_active_subscribers_to_email', 10, 1 );
add_filter( 'bbp_topic_subscription_user_ids', 'bsp_get_active_subscribers_to_email', 10, 1 );

// build active subscriptions user IDs(roles emailed)
// this function is used to set only active subscriptions for subscription emails sent, for custom Subscription Management, and as default for all bbPress usage of subscription user IDs
function bsp_get_active_subscribers_to_email( $user_ids ) {
        global $bsp_style_settings_email;
        if ( ! empty( $user_ids ) ) {
                $new_user_ids = array();
                $roles = ( ! empty( $bsp_style_settings_email['email_roles'] ) ? maybe_unserialize( $bsp_style_settings_email['email_roles'] ) : bsp_default_roles_to_email() );
                $roles ['time'] = time() ;
				foreach ( $user_ids as $uid ) {
                        $urole = bbp_get_user_role( $uid );
                        if ( in_array( $urole, $roles ) && bbp_is_valid_role( $urole ) ) {
                                $new_user_ids[] = $uid;
                        }
                }
                return $new_user_ids;
        } else {
                return $user_ids;
        } 
}; 


// build inactive subscriptions user IDs (roles not emailed)
function bsp_get_inactive_subscribers( $object_id ) {
        remove_filter( 'bbp_forum_subscription_user_ids', 'bsp_get_forum_subscribers_to_email', 10 );
        $user_ids = bbp_get_subscribers( $object_id );
        global $bsp_style_settings_email;
        if ( ! empty( $user_ids ) ) {
                $new_user_ids = array();
                $roles = ( ! empty( $bsp_style_settings_email['email_roles'] ) ? maybe_unserialize( $bsp_style_settings_email['email_roles'] ) : bsp_default_roles_to_email() );
                foreach ( $user_ids as $uid ) {
                        $urole = bbp_get_user_role( $uid );
                        if ( ! in_array( $urole, $roles ) && bbp_is_valid_role( $urole ) ) {
                                $new_user_ids[] = $uid;
                        }
                }
                return $new_user_ids;
        } else {
                return $user_ids;
        } 
};

//check if we should be showing subscriptions to this user 
add_filter ('bbp_is_subscriptions_active' , 'bsp_check_user_role_subscriptions_active' ) ;

function  bsp_check_user_role_subscriptions_active ($check) {
	//bail if subscriptions are not turned on
	if ($check != true) return false ;
	//otherwise check if role is allowed subscriptions
	global $bsp_style_settings_email ;
	//get a list of allowable roles
	$roles = ( ! empty( $bsp_style_settings_email['email_roles'] ) ? maybe_unserialize( $bsp_style_settings_email['email_roles'] ) : bsp_default_roles_to_email() );
    $user_id = get_current_user_id() ;
	$user_role = bbp_get_user_role( $user_id);
	if (!in_array( $user_role, $roles ) && bbp_is_valid_role( $user_role ) ) {
		$check = false ;
	}
return $check ;
}
