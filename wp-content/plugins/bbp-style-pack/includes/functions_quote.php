<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//functions for the quote tab
global $bsp_style_settings_quote;



if ($bsp_style_settings_quote['quote_position'] == 1) {
	add_filter('bbp_topic_admin_links', 'bsp_quote_admin_link');
	add_filter('bbp_reply_admin_links', 'bsp_quote_admin_link');
}
if ($bsp_style_settings_quote['quote_position'] == 2) {
        add_action ('bbp_theme_before_topic_content' , 'bsp_quote_reply_content');
        add_action ('bbp_theme_before_reply_content' , 'bsp_quote_reply_content');
}
if ($bsp_style_settings_quote['quote_position'] == 3) {		
        add_action ('bbp_theme_after_reply_content' , 'bsp_quote_reply_content');
        add_action ('bbp_theme_after_topic_content' , 'bsp_quote_reply_content');
}
	

function bsp_quote_reply_content($content='') {
	global $bsp_style_settings_quote;
	if(bbp_current_user_can_access_create_reply_form()) {
		//if in admin AND using buddyboss
		if ($bsp_style_settings_quote['quote_position'] == 1 && function_exists ('buddyboss_theme'))  {
			echo '<span class="">'.bsp_quote().'</span>';
		}
         else  echo '<div class="bsp-quote-block">'.bsp_quote().'</div>';
	}
}

	
function bsp_quote_admin_link($links) {
        if(bbp_current_user_can_access_create_reply_form()) {
                $links['Quote'] = bsp_quote();
        }
        return $links;
}


function bsp_quote() {
        global $bsp_style_settings_quote;
        $id = bbp_get_reply_id();

        $is_reply = true;
        if ($id == 0) {
                $is_reply = false;
                $id = bbp_get_topic_id();
        }

        if ($is_reply) {
                $url = bbp_get_reply_url($id);
                $ath = bbp_get_reply_author_display_name($id);
        } else {
                $url = get_permalink($id);
                $ath = bbp_get_topic_author_display_name($id);
        }
        $quote_name = (!empty ($bsp_style_settings_quote['quote_name'] ) ? $bsp_style_settings_quote['quote_name'] : 'Quote' );
		//if we are using buddyboss
		if (function_exists ('buddyboss_theme')) {
			//if quote in admin
			if ($bsp_style_settings_quote['quote_position'] == 1) {
				$retval   = '<a data-balloon=" ' . esc_html__( 'Reply', 'buddyboss-theme' ) . ' " data-balloon-pos="up" href="' . esc_url( $r['uri'] ) . '" data-modal-id="bbp-reply-form" class="bbp-reply-to-link"><i class="bb-icon-reply"></i><span class="bb-forum-reply-text">' . esc_html( $r['reply_text'] ) . '</span></a>' . $r['link_after'];
				$retval   = '<a data-balloon=" '.$quote_name.' " data-balloon-pos="up" href="#'.$id.'" data-modal-id="bbp-reply-form" class="bs-dropdown-link bbp-reply-to-link bsp-quote-link">' ;
				$retval.= '<i class="bb-icon-quote bsp-admin-links-border-buddyboss"></i><span class="bb-forum-reply-text">' . $quote_name . '</span></a>' ;
			return $retval ;
			}
			else return '<a href="#'.$id.'" data-modal-id="bbp-reply-form" class="bsp-quote-link">'.$quote_name.'</a>' ;
		}
		else return '<a href="#'.$id.'" bbp-url="'.$url.'" bbp-author="'.$ath.'" class="bsp-quote-link">'.$quote_name.'</a>';
		
}


//amend allowed tags to accept <div class> and <span class> tags
add_filter ('bbp_kses_allowed_tags' , 'bsp_allow_div_tag', 100 );

function bsp_allow_div_tag( $tags ) {
        $tags['div'] = array(
                'class' => true
        );
        $tags['span'] = array(
                'class' => true
        );
        $tags['br'] = array();

        return $tags;
	
}

//ajax functions

//backend
add_action ('wp_ajax_get_status_by_ajax' , 'bsp_function');
//front end
add_action ('wp_ajax_nopriv_get_status_by_ajax' , 'bsp_function');

function bsp_function() {
	//check_ajax_referrer
	//this comes from the variables set in generate_css.php wp_localise_script function
	wp_verify_nonce( $_POST['quote'], 'get_id_content' );
	global $bsp_style_settings_quote;
	$id = absint( filter_var( $_POST['id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
	//set up elements
	$preamble = (!empty ($bsp_style_settings_quote['quote_preamble'] ) ? $bsp_style_settings_quote['quote_preamble'] : 'On' );
	$conclusion = (!empty ($bsp_style_settings_quote['conclusion'] ) ? $bsp_style_settings_quote['conclusion'] : 'said' );
	if (bbp_is_reply ($id)) {
		$content = bbp_get_reply_content($id );
		$author = bbp_get_reply_author_link(array( 'type' => 'name', 'post_id' => $id, ) );
		$date = (!empty ($bsp_style_settings_quote['date'] ) ? '<span class="bbp-reply-post-date">'.bbp_get_reply_post_date($id).'</span>' : '' );
	}
	if (bbp_is_topic ($id)) {
		$content = bbp_get_topic_content($id );
		$author = bbp_get_topic_author_link(array( 'type' => 'name', 'post_id' => $id, ));
		$date = (!empty ($bsp_style_settings_quote['date'] ) ? '<span class="bbp-topic-post-date">'.bbp_get_topic_post_date($id).'</span>' : '' );
	}
        
        // if nested quotes disabled, strip all current quotes from the content
        if ( ! empty( $bsp_style_settings_quote['disable_nested_quotes'] ) ) {
                    
                // first get rid of the actual quoted content title/link/header info for all quotes (single and nested)
                $re = "'<div class=\"bsp-quote-title\">(.*?)</div>'si";
                while( preg_match( $re, $content ) ){
                        $content = preg_replace( $re, '', $content );
                }

                // second, strip all opening/closing blockquote tags and their content from the main content
                // this will handle single quotes, and the inner-most nested quote
                $re = "'<blockquote>(.*?)</blockquote>'si";
                while( preg_match( $re, $content ) ){
                        $content = preg_replace( $re, '', $content );
                }

                // third, handle cases for all other nested quotes where <p> tags/content are used that also need to be cleared
                $re = "'<p>(.*?)</p></blockquote>'si";
                while( preg_match( $re, $content ) ){
                        $content = preg_replace( $re, '', $content );
                }
                        
        }
        
	//set up default order
	if (!empty($bsp_style_settings_quote['date'] ) ? $total_items=4 : $total_items=3 );
	if ($total_items==3) {
                $default_preamble=1;
                $default_author=2;
                $default_conclusion=3;
	}
	if ($total_items==4) {
                $default_preamble=1;
                $default_date = 2;
                $default_author=3;
                $default_conclusion=4;
	}
	//now change if set
	$order = array();
	$i=1;
	//set the limit to $total_items and set up order
		while($i<=$total_items) {
                        if ((!empty($bsp_style_settings_quote["preamble_order"]) ? $bsp_style_settings_quote["preamble_order"] : $default_preamble) == $i) $order[$i] = 'preamble_order';
                        if ((!empty($bsp_style_settings_quote["date_order"]) ? $bsp_style_settings_quote["date_order"] : $default_date) == $i) $order[$i] = 'date_order';
                        if ((!empty($bsp_style_settings_quote["author_order"]) ? $bsp_style_settings_quote["author_order"] : $default_author) == $i) $order[$i] = 'author_order';
                        if ((!empty($bsp_style_settings_quote["conclusion_order"]) ? $bsp_style_settings_quote["conclusion_order"] : $default_conclusion) == $i) $order[$i] = 'conclusion_order';
                        //increments $i	
                        $i++;	
		}	 
		//start output
		echo '<blockquote><div class="bsp-quote-title">';
		$i=1;
		while($i<=$total_items) {
                        //then work out which is active and output
                        if (!empty($order[$i])) {
                                if ($order[$i] == 'preamble_order') echo $preamble;
                                if ($order[$i] == 'date_order') echo $date;
                                if ($order[$i] == 'author_order') echo $author;
                                if ($order[$i] == 'conclusion_order') echo $conclusion;
                        }
                        $i++;
                        echo ' ';
		}
		
	//output
	echo '</div>';
	echo $content;
	echo '</blockquote>';
	echo '<br>'	;
        wp_die();
}


// function to handle whether quotes should link to user profiles or not
function bsp_custom_quote_profile_links( $content ) {
        $has_quotes = strpos( $content, '<div class="bsp-quote-title">' );
        // if we indeed are dealing with a quote, process it based on admin settings
        if ( $has_quotes !== false ) {
                // get admin setting value
                global $bsp_style_settings_quote;
                $profile_link = ( ! empty( $bsp_style_settings_quote["quoted_user_link"] ) ? $bsp_style_settings_quote["quoted_user_link"] : 'everyone' );
                
        // DISABLE PROFILE LINKS
                // if ( setting value is to disable profile links ), or ( setting value is display to logged-in only && user is not logged-in )
                if ( $profile_link === 'no_one' || ( $profile_link === 'logged_in' && ! is_user_logged_in() ) ) {
                        // replace profile links with bbp-author-link class specified in <a href> tag (top-level quotes)
                        $new_content = preg_replace( '#<a href="[^"]*" title="[^"]*" class="bbp-author-link">(.*?)</a>#i', '$1', $content );
                        // replace profile links where no bbp-author-link class is specified in <a href> tag (nested quotes)
                        $new_content = preg_replace( '#<a href="[^"]*" title="[^"]*"><span class="bbp-author-name">(.*?)</span></a>#is', '<span class="bbp-author-name">$1</span>', $new_content );
                        return $new_content;
                }
                
        // ENABLE PROFILE LINKS
                // if ( setting value is everyone ), or ( setting value is display to logged-in only && user is logged-in )
                if ( $profile_link === 'everyone' || ( $profile_link === 'logged_in' && is_user_logged_in() ) ) {
                        // If profile links were previously disabled but have been re-enabled, 
                        // then nested quotes may be quoting another quote that had the profile link previously removed.
                        // For consistency, let's force re-add profile links to quoted usernames
                    
                        // get all quoted usernames (display_names) and add them to an array
                        preg_match_all( "'<span class=\"bbp-author-name\">(.*?)</span>'is", $content, $user_names );
                        if ( is_array( $user_names ) && ! empty( $user_names ) ) {
                                // loop through all quoted usernames in this post
                                foreach ( $user_names[1] as $user_name ) {
                                        // get the user object for this user's display_name
                                        $args= array(
                                                'search' => $user_name, // or login or nicename in this example
                                                'search_fields' => array( 'user_login', 'user_nicename', 'display_name' )
                                        );
                                        $user_query = new WP_User_Query($args);
                                        if ( $user_query && ! is_wp_error( $user_query ) ) {
                                                // get the user info for the first result of the search
                                                $user_info = $user_query->results[0]->data;
                                                $user_nicename = bbp_get_user_nicename( $user_info->ID );

                                                // get the user profile link for the current user_id that was quoted
                                                $link = bbp_get_user_profile_link( $user_info->ID );
                                                
                                                // Format the user profile link to match what a nested quote looks like
                                                // From this: <a href="https://site.com/forums/users/username/">username</a>
                                                // To this: <a href="https://site.com/forums/users/username/" title="View username's profile"><span class="bbp-author-name">username</span></a>
                                                $view_profile_text = sprintf( 
                                                        /* translators: %s is a username */
                                                        __( 'View %s\'s profile'),
                                                        $user_name
                                                );
                                                $new_link = preg_replace( '#">(.*?)</a>#is', '" title="'.$view_profile_text.'"><span class="bbp-author-name">$1</span></a>', $link );

                                                // Note: Top-level quotes will always have the profile link, so we don't have to do anything here with adding profile links
                                                // We're only dealing with profile links for nested quotes here.
                                                
                                                // Note: Some nested quotes may have profile links and other nested quotes may not. It all depends on what was previously set.
                                                // We need to handle both cases here.
                                                // There's no good way to search for usernames that don't have profile links or to bulk-add profile links without duplicating,
                                                // so we just force remove any existing profile links for this user's nested quotes (make it all standardized without profile links), 
                                                // and then force re-add them afterwards (re-standardize with profile links).
                                                
                                                // force-remove nested quote profile links for this user
                                                $content = preg_replace( '#'.$new_link.'#is', '<span class="bbp-author-name">'.$user_name.'</span>', $content );
                                                // force re-add nested quote profile links and/or add them for any that were missing profile links in the first place
                                                $content = preg_replace( '#<span class="bbp-author-name">'.$user_name.'</span>#is', $new_link, $content );      
                                        }
                                }
                        }
                        // return the content, with profile links force-added for any post with quotes
                        return $content;
                }
        } 
        // else, no quotes or no setting to disable profile links so just return the content unaltered
        return $content;
}
add_filter( 'bbp_get_topic_content', 'bsp_custom_quote_profile_links' );
add_filter( 'bbp_get_reply_content', 'bsp_custom_quote_profile_links' );


// we need to force-add 'display_name' as a column to search for getting user_info from bbPress display_name within quotes
function bsp_user_search_columns( $search_columns ){
        if( ! in_array( 'display_name', $search_columns ) ) {
                $search_columns[] = 'display_name';
        }
        return $search_columns;
}
add_filter( 'user_search_columns', 'bsp_user_search_columns', 10, 1 );

//BUDDYBOSS ONLY !
//AMENDED change to show quotes in admin link
//this function sits in \buddyboss-theme\inc\theme\template-functions.php but has an if_exists so we can load a fresh one here
//and add the Quote function 
//- this is called by a filter in \buddyboss-theme\bbpress\loop-single-reply.php


function bb_theme_reply_link_attribute_change( $retval, $r, $args ) {

	    if ( ! function_exists( 'buddypress' ) && ! bp_is_active( 'forums' ) ) {
		    return;
	    }

	    // Get the reply to use it's ID and post_parent
	    $reply = bbp_get_reply( bbp_get_reply_id( (int) $r['id'] ) );

	    // Bail if no reply or user cannot reply
	    if ( empty( $reply ) || ! bbp_current_user_can_access_create_reply_form() )
		    return;

	    // If single user replies page then no need to open a modal for reply to.
	    if ( bbp_is_single_user_replies() ) {
		    return $retval;
	    }

	    // Build the URI and return value
	    $uri = remove_query_arg( array( 'bbp_reply_to' ) );
	    $uri = add_query_arg( array( 'bbp_reply_to' => $reply->ID ), bbp_get_topic_permalink( bbp_get_reply_topic_id( $reply->ID ) ) );
	    $uri = wp_nonce_url( $uri, 'respond_id_' . $reply->ID );
	    $uri = $uri . '#new-post';

	    // Only add onclick if replies are threaded
	    if ( bbp_thread_replies() ) {

		    // Array of classes to pass to moveForm
		    $move_form = array(
			    $r['add_below'] . '-' . $reply->ID,
			    $reply->ID,
			    $r['respond_id'],
			    $reply->post_parent
		    );

		    // Build the onclick
		    $onclick  = ' onclick="return addReply.moveForm(\'' . implode( "','", $move_form ) . '\');"';

		    // No onclick if replies are not threaded
	    } else {
		    $onclick  = '';
	    }

	    $modal = 'data-modal-id-inline="new-reply-'.$reply->post_parent.'"';

	    // Add $uri to the array, to be passed through the filter
	    $r['uri'] = $uri;
	    $reply_link   = $r['link_before'] . '<a data-balloon=" ' . esc_html__( 'Reply', 'buddyboss-theme' ) . ' " data-balloon-pos="up" href="' . esc_url( $r['uri'] ) . '" class="bbp-reply-to-link ' . $reply->ID . ' "' . $modal . $onclick . '><i class="bb-icon-reply"></i><span class="bb-forum-reply-text">' . esc_html( $r['reply_text'] ) . '</span></a>' . $r['link_after'];
		//add quotes link if needed
		global $bsp_style_settings_quote;
		$quote_link = '' ;
		//if in admin links
		if ($bsp_style_settings_quote['quote_position'] == 1) {
		$quote_link = bsp_quote_reply_content() ;
		}
		$retval=$reply_link.$quote_link ;
		return $retval;
}