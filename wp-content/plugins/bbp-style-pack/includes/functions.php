<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//functions 
global $bsp_forum_display;
global $bsp_login;
global $bsp_breadcrumb;
global $bsp_profile;
global $bsp_style_settings_freshness;
global $bsp_style_settings_form;
global $bsp_style_settings_ti;
global $bsp_style_settings_t;
global $bsp_style_settings_buttons;
global $bsp_roles;
global $bsp_topic_order;
global $bsp_forum_order;
global $bsp_style_settings_search;
global $bsp_style_settings_email;
global $bsp_style_settings_translation;
global $bsp_bbpress_version;
global $bsp_style_settings_f;
global $bsp_login_fail;
global $bsp_style_settings_topic_preview;

if(!function_exists('rclog')){

	function rclog( $data ) {
		if ( is_array( $data ) ) {
			foreach ($data as $key=>$item ) {
			$data = $key.': '.$item;
			echo "<script>console.log('" . $data . "');</script>";
			}
		}
		else {
			$output = "<script>console.log('" . $data . "');</script>";
			echo $output;
		}
	}
}


/**********forum list create vertical list************/
function bsp_sub_forum_list($args) {
        $args['separator'] = '<br>';
        return $args;
}

if ( !empty ($bsp_forum_display['forum_list'] )) {
        add_filter('bbp_before_list_forums_parse_args', 'bsp_sub_forum_list' );
        add_filter('bbp_before_bsp_list_forums_parse_args', 'bsp_sub_forum_list' );
}

/**********remove counts*********************/
function bsp_remove_counts($args) {
        $args['show_topic_count'] = false;
        $args['show_reply_count'] = false;
        $args['count_sep'] = '';
        return $args;
}

if ( !empty ($bsp_forum_display['hide_counts'] )) {
        add_filter('bbp_before_list_forums_parse_args', 'bsp_remove_counts' );
        add_filter('bbp_before_bsp_list_forums_parse_args', 'bsp_remove_counts' );
}



/**********removes 'private' and protected prefix for forums ********************/
// we need to remove only for forums, so posts and pages still show private, so we execute the add_filter('private_title_format'... in a filter for bbp_get_forum_title

function bsp_remove_private_title ($title, $forum_id ) {
	$forum_id = bbp_get_forum_id( $forum_id );
	add_filter('private_title_format', 'bsp_remove_private_titleb');
	$title = get_the_title( $forum_id );
	return apply_filters( 'bsp_remove_private_title', $title, $forum_id );
}

function bsp_remove_private_titleb($title) {
	return '%s';
}


if ( !empty ($bsp_forum_display['remove_private'] )) {
        add_filter('bbp_get_forum_title', 'bsp_remove_private_title', 10, 2);
}



/**********BUTTONS ********/

//quicker to just add the # in all cases

add_action( 'bbp_theme_before_topic_form', 'bsp_create_new_topicb' );

//First find out if we are using a link or a button for create new topic
if (empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && !empty($bsp_forum_display['create_new_topic'] ) ) {
	//it is just a link so	
	add_action ( 'bbp_template_before_single_forum', 'bsp_create_new_topica' );
}


//then find out if we are using a link or a button for forum subscribe
if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) ) {
        //then it is a button so set button and add action for name link
        //add_filter to take out current subscribe link
	add_filter ('bbp_get_forum_subscribe_link' , 'bsp_remove_forum_subscribe_link', 10 , 2 );
} 

//then we check 

function  bsp_remove_forum_subscribe_link ( $retval, $r) {
	//if we have a button, then in the function below we add a variable to $r called 'button'
	//so if we don't have that then we know to blank the return
	if (!empty ($r['widget']) ) return $retval ;
	if (empty ($r['button']) ) 	return '' ;
	else 
		return apply_filters( 'bsp_remove_forum_subscribe_link', $retval, $r );
}
	
	
//add this action so that if buttons are active we show them
add_action ( 'bbp_template_before_single_forum', 'bsp_display_buttons' );

function bsp_display_buttons() {
	global $bsp_style_settings_buttons;
	global $bsp_style_settings_unread;
	$topic_button = $subscribe_button = $profile_button = $unread_button = 0;
	if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) ) $topic_button=1;
	if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) ) $subscribe_button=1;	
	if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) ) $profile_button=1;
	if (!empty ($bsp_style_settings_unread['unread_activate']))	 $unread_button=1;
	$total_buttons = $topic_button + $subscribe_button + $profile_button + $unread_button;
	if ($total_buttons == 0) return;
	//first set a new div
	echo '<div style="clear:both;"></div>';
	//now display in order
	//if we have 4 buttons, then this is the default order
	$default_topic= 1;
	$default_subscribe= 2;
	$default_profile = 3;
	$default_unread = 4;
	//we need to sort out a default order if buttons are 3
	if ($total_buttons == 3) {
		if (empty ($topic_button)) {
			$default_subscribe= 1;
			$default_profile = 2;
			$default_unread = 3;
		}
		if (empty ($subscribe_button)) {
			$default_topic= 1;
			$default_profile = 2;
			$default_unread = 3;
		}
		if (empty ($profile_button)) {
			$default_topic= 1;
			$default_subscribe = 2;
			$default_unread = 3;
		}
		if (empty ($unread_button)) {
			$default_topic= 1;
			$default_subscribe = 2;
			$default_profile = 3;
		}
	}
	//we need to sort out a default order if buttons are 2
	if ($total_buttons == 2) {
		$button_pos = 1;
		if (!empty ($topic_button)) {
			$default_topic = $button_pos;
			$button_pos = 2;
		}
		if (!empty ($subscribe_button)) {
			$default_subscribe = $button_pos;
			$button_pos = 2;
		}
		if (!empty ($profile_button)) {
			$default_profile = $button_pos;
			$button_pos = 2;
		}
		if (!empty ($unread_button)) {
			$default_unread = $button_pos;
		}
	
	}
	$order = array();
	$i=1;
	//set the limit to $total_buttons
		while($i<=$total_buttons) {
		if ((!empty($bsp_style_settings_buttons["topic_order"]) ? $bsp_style_settings_buttons["topic_order"] : $default_topic) == $i) $order[$i] = 'topic_order';
		if ((!empty($bsp_style_settings_buttons["subscribe_order"]) ? $bsp_style_settings_buttons["subscribe_order"] : $default_subscribe) == $i) $order[$i] = 'subscribe_order';
		if ((!empty($bsp_style_settings_buttons["profile_order"]) ? $bsp_style_settings_buttons["profile_order"] : $default_profile) == $i) $order[$i] = 'profile_order';
		if ((!empty($bsp_style_settings_buttons["unread_order"]) ? $bsp_style_settings_buttons["unread_order"] : $default_unread) == $i) $order[$i] = 'unread_order';
                        //increments $i	
                        $i++;	
		}
		if ($total_buttons == 1) {
                        //then just one
                        echo '<div class="bsp-center">';
                        //then work out which is active and call
                        if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) ) bsp_new_topic_button();
                        if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) ) bsp_subscribe_button();
                        if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) ) bsp_profile_link();
                        if (!empty($bsp_style_settings_unread['unread_activate'] ) ) bsp_unread_button();
                        echo '</div>';
		}
		if ($total_buttons == 2) {
                        echo '<div class="bsp-center bsp-one-half">';
                        //then work out which is active and call in order
                        if (!empty($order['1'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['1'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['1'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['1'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['1'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';
                        echo '<div class="bsp-center bsp-one-half">';
                        //then work out which is active and call
                        if (!empty($order['2'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['2'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['2'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['2'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['2'] == 'unread_order')) bsp_unread_button();
                        }		
                        echo '</div>';		
		}
		if ($total_buttons == 3) {
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['1'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['1'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['1'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['1'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['1'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['2'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['2'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['2'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['2'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['2'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['3'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['3'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['3'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['3'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['3'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';		
		}
		if ($total_buttons == 4) {
                        echo '<div class="bsp-center">';
                        //then work out which is active and call
                        if (!empty($order['1'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['1'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['1'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['1'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['1'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['2'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['2'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['2'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['2'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['2'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['3'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['3'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['3'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['3'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['3'] == 'unread_order')) bsp_unread_button();
                        }
                        echo '</div>';		
                        echo '<div class="bsp-center bsp-one-third">';
                        //then work out which is active and call
                        if (!empty($order['4'])) {
                                if (!empty($bsp_style_settings_buttons['Create Topic Buttonactivate'] ) && ($order['4'] == 'topic_order')) bsp_new_topic_button();
                                if (!empty($bsp_style_settings_buttons['Subscribe Buttonactivate'] ) && ($order['4'] == 'subscribe_order')) bsp_subscribe_button();
                                if (!empty($bsp_style_settings_buttons['Profile Buttonactivate'] ) && ($order['4'] == 'profile_order')) bsp_profile_link();
                                if (!empty($bsp_style_settings_unread['unread_activate'] ) && ($order['4'] == 'unread_order')) bsp_unread_button();
                        }
		echo '</div>';		
		}
		
	
}

function bsp_new_topic_button() {
	global $bsp_style_settings_buttons;
	if ( ! empty( $bsp_style_settings_buttons['new_topic_description'] ) ) $text = $bsp_style_settings_buttons['new_topic_description'];
	else $text = __( 'Create New Topic', 'bbp-style-pack' );
	$class = 'bsp_button1';
	if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] ) ) {
                if ( $bsp_style_settings_buttons['button_type'] == 2 ) $class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
	}
	if ( bbp_current_user_can_access_create_topic_form() && !bbp_is_forum_category() ) {
                $href = apply_filters ('bsp_new_topic_button' , '#new-post' );
                echo '<a class="' . $class . '" href ="' . $href . '">' . $text . '</a>';
	}
}

function bsp_unread_button() {
	global $bsp_style_settings_unread;
	global $bsp_style_settings_buttons;
	if ( ! empty( $bsp_style_settings_unread['unread_description'] ) ) $text = $bsp_style_settings_unread['unread_description'];
	else $text = __( 'Mark all topics as read', 'bbp-style-pack' );
	$class = 'bsp_button1';
	if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] ) ) {
                if ( $bsp_style_settings_buttons['button_type'] == 2 ) $class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
	}
	$forum_id = bbp_get_forum_id();
	$html = '
                <form action="" method="post" >
                        <input type="hidden" name="bsp_ur_mark_all_topic_as_read" value="1"/>
                        <input type="hidden" name="bsp_ur_mark_id" value="' . $forum_id . '"/>
                        <input class="' . $class . '"type="submit" value="' . $text . '"/>
                </form>
			';
	echo $html;	
}

// Actually display/render the subscription button
function bsp_subscribe_button() {
 
	global $bsp_style_settings_buttons;
 
        // setup the button with custom description if set
        if ( ! empty( $bsp_style_settings_buttons['subscribe_button_description'] ) ) $args['subscribe'] = esc_attr( $bsp_style_settings_buttons['subscribe_button_description'] );
        if ( ! empty( $bsp_style_settings_buttons['unsubscribe_button_description'] ) ) $args['unsubscribe'] = esc_attr( $bsp_style_settings_buttons['unsubscribe_button_description'] );

        // setup remaining required arguments
        $args['user_id'] = bbp_get_user_id( get_current_user_id(), true, true );
        $args['object_id'] = bbp_get_forum_id();
        $args['object_type'] = 'post';

        // render the button with arguments
        bbp_user_subscribe_link( $args );

}


// Apply filter to the subscription button to make sure the class and descriptions are applied
function bsp_subscribe_button_filter( $html, $args ) {
        global $bsp_style_settings_buttons;

        // change "Subscribe" text within the html only if needed
        if ( ! empty( $bsp_style_settings_buttons['subscribe_button_description'] ) ) {
                if ( $args['subscribe'] !== $bsp_style_settings_buttons['subscribe_button_description'] ) {
                        $html = preg_replace('/'.$args['subscribe'].'/', esc_attr( $bsp_style_settings_buttons['subscribe_button_description'] ), $html);
                }
        }

        // change "Unsubscribe" text within the html only if needed
        if ( ! empty( $bsp_style_settings_buttons['unsubscribe_button_description'] ) ) {
                if ( $args['unsubscribe'] !== $bsp_style_settings_buttons['unsubscribe_button_description'] ) {
                        $html = preg_replace('/'.$args['unsubscribe'].'/', esc_attr( $bsp_style_settings_buttons['unsubscribe_button_description'] ), $html);
                }
        }
 
        //if we are not in the single topic information widget, then add the button class...
	if (empty ( $args['widget'])) {

                // set the class according to forum button values
                $class = 'bsp_button1';
                if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] )   ) {
                        if ( $bsp_style_settings_buttons['button_type'] == 2 ) $class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
                }
		
                // if a class is set, let's apply that to the buttons
                if ( $class ) {
                        // apply the class to the link html
                        $pattern = '/class="subscription-toggle"/' ;
                        $replace = 'class="subscription-toggle '.$class.'"';
                        $html = preg_replace( $pattern, $replace, $html );
                }
	}
    
        // return the customized link html
        return $html;

}
add_filter( 'bbp_get_user_subscribe_link', 'bsp_subscribe_button_filter', 10, 2 );


// function to customize surrounding HTML of the topic subscribe button
function bsp_topic_subscribe_filter( $html ) {
        global $bsp_style_settings_t;
        $prefix = false;
        $class = false;
        $forum_class = 'bsp_button1';
        
        if ( ! empty( $bsp_style_settings_t['activate_topic_subscribe_button_prefix'] ) ) {
                $prefix = ( ! empty( $bsp_style_settings_t['topic_subscribe_button_prefix'] ) ) ? $bsp_style_settings_t['topic_subscribe_button_prefix'] : '';
        }
    
        // change the prefix, but only if admin has enabled custom prefix
        if ( $prefix !== false ) {
                $pattern = '/&nbsp;\|&nbsp;/';
                if (!empty ($html)) $html = preg_replace( $pattern, $prefix, $html );
        }
        
        // topic subscribe button class
        if ( ! empty( $bsp_style_settings_t['topic_button_type'] )   ) {
                // 1 = bbPress defaults, 2 = 'bsp_button1', 3 = custom class 
                if ( $bsp_style_settings_t['topic_button_type'] == 2 ) $class = 'bsp_button1';
                if ( $bsp_style_settings_t['topic_button_type'] == 3 && ! empty( $bsp_style_settings_t['TopicButtonclass'] ) ) $class = esc_attr( $bsp_style_settings_t['TopicButtonclass'] );
        }

        // get current subscribe class from forum buttons so we can adjust accordingly for topics
        global $bsp_style_settings_buttons;
        if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] )   ) {
                if ( $bsp_style_settings_buttons['button_type'] == 2 ) $forum_class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
        }

        // apply the class to the topic subscribe link html
        $pattern = '/class="subscription-toggle '.$forum_class.'"/' ;
        if ( $class == false ) $replace = 'class="subscription-toggle"';
        else $replace = 'class="subscription-toggle '.$class.'"';
        if (!empty ($html)) $html = preg_replace( $pattern, $replace, $html );
        
        // return the customized button html
        return $html;
}    
add_filter( 'bbp_get_topic_subscribe_link', 'bsp_topic_subscribe_filter', 10, 1 );


// function to customize HTML of the topic favorite button
function bsp_topic_favorite_filter( $html ) {
        global $bsp_style_settings_t;
        $class = false;
        
        if ( ! empty( $bsp_style_settings_t['topic_button_type'] ) ) {
                // 1 = bbPress defaults, 2 = 'bsp_button1', 3 = custom class 
                if ( $bsp_style_settings_t['topic_button_type'] == 2 ) $class = 'bsp_button1';
                if ( $bsp_style_settings_t['topic_button_type'] == 3 && ! empty( $bsp_style_settings_t['TopicButtonclass'] ) ) $class = esc_attr( $bsp_style_settings_t['TopicButtonclass'] );
        }
        
        // if a class is set, let's apply that to the buttons
        if ( $class ) {
                // apply the class to the favorite link html
                $pattern = '/class="favorite-toggle"/' ;
                $replace = 'class="favorite-toggle '.$class.'"';
                $html = preg_replace( $pattern, $replace, $html );
        }
        
        // return the customized button html
        return $html;
}
add_filter( 'bbp_get_topic_favorite_link', 'bsp_topic_favorite_filter', 10, 1 );
add_filter( 'bbp_get_user_favorites_link', 'bsp_topic_favorite_filter', 10, 1 ); // added to account for engagements AJAX response


//TOPIC BUTTONS 

//Reply button

if (!empty ($bsp_style_settings_t['new_reply_activate'] )) {
	add_action ( 'bbp_template_before_single_topic', 'bsp_display_reply_button' );
}


function bsp_display_reply_button() {
	echo '<div style="clear:both;"></div>';
	echo '<div class="bsp-center">';
	global $bsp_style_settings_t;
	global $bsp_style_settings_buttons;
	if ( ! empty( $bsp_style_settings_t['new_reply_description'] ) ) $text = $bsp_style_settings_t['new_reply_description'];
	else $text = __( 'Create New Reply', 'bbp-style-pack' );
	$forum_title = bbp_get_topic_forum_title () ;
	$topic_title = bbp_get_topic_title() ;
	$text = str_replace( '{topic_name}',  $topic_title,  $text );
	$text = str_replace( '{forum_name}',  $forum_title,  $text );
	$text = apply_filters ('bsp_display_reply_button_text' , $text ) ;
	$class = 'bsp_button1';
	if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] ) ) {
                if ( $bsp_style_settings_buttons['button_type'] == 2 ) $class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
	}
	if ( bbp_current_user_can_access_create_reply_form() ) {
                $href = apply_filters ( 'bsp_new_reply_button' , '#new-post' );
                echo '<a class="' . $class . '" href ="' . $href . '">' . $text . '</a>';
	}
        echo '</div>';
	
}
	
//for those using the 'show topics by freshness' rather than 'show forums' in the bbPress setup, then modify the create topic button in forum display
if (get_option('_bbp_show_on_root') == 'topics') {
	global $bsp_forum_display;
	if (!empty ($bsp_forum_display['create_new_topic'] )) {
		add_action ( 'bbp_template_before_topics_index', 'bsp_create_new_topica' );
		add_action ('bbp_template_after_topics_index' , 'bsp_add_new_topic_form' );
	}
}
	
	
	
function bsp_create_new_topica() {
	global $bsp_forum_display;
	if (!empty ($bsp_forum_display['Create New Topic Description'])) $text=$bsp_forum_display['Create New Topic Description'];
	else $text=__('Create New Topic', 'bbp-style-pack');
	if ( bbp_current_user_can_access_create_topic_form() && !bbp_is_forum_category() ) {
                $href = apply_filters ('bsp_create_new_topica' , '#new-post' );
                echo '<div class="bsp-new-topic"> <a href ="'.$href.'">'.$text.'</a></div>';
	}
}
	
function bsp_create_new_topicb() {
	echo '<div><a class="bsptopic" name="bsptopic"></a></div>';
}
	
	
function bsp_add_new_topic_form() {
	echo '<div><a class="bsptopic" name="bsptopic"></a></div>';
	//adds the new topic form to the end of the topics list
	bbp_get_template_part( 'form', 'topic' ); 
}

function bsp_profile_link() {
	if ( ! is_user_logged_in() ) return;
	global $bsp_style_settings_buttons;
	if ( ! empty($bsp_style_settings_buttons['profile_description'] ) ) $text = $bsp_style_settings_buttons['profile_description'];
	else $text = __( 'Profile', 'bbp-style-pack' );
	$class = 'bsp_button1';
	if ( ! empty( $bsp_style_settings_buttons['button_type'] ) && ! empty( $bsp_style_settings_buttons['Buttonclass'] ) ) {
                if ( $bsp_style_settings_buttons['button_type'] == 2 ) $class = esc_attr( $bsp_style_settings_buttons['Buttonclass'] );
	}
	$current_user = wp_get_current_user();
	$user = $current_user->ID;
	echo '<a class="' . $class . '" href="' . esc_url( bbp_get_user_profile_url( $user ) ) . '">' . $text . '</a>';
}
	

/**********Add forum description ********/

/** filter to add description after forums titles on forum index */
function bsp_add_display_forum_description() {
        echo '<div class="bsp-forum-content">';
        bbp_forum_content();
        echo '</div>';
}
	
	

if ( !empty($bsp_forum_display['add_forum_description'] ) ) {
        //if ($bsp_forum_display['add_forum_description'] == true ) {
        add_action( 'bbp_template_before_single_forum' , 'bsp_add_display_forum_description' );
}




/**********BSP LOGIN*******************/
		
/**********adds login/logout to menu*******************/
if (!empty ($bsp_login['add_login'] )) {
        add_filter( 'wp_nav_menu_items', 'bsp_nav_menu_login_link' , 10, 2);
}

function bsp_get_menu_from_args ($args) {
	//get menu name from $args (a bit convoluted!)
	$me = $args->menu;
	$me2 = json_encode($me);
	$pos1 = strpos ($me2 , '"name":"');
	$pos2 = strpos ($me2 , '","slug":"');
	$menu_name = substr($me2, ($pos1+8), ($pos2-$pos1-8));
        return $menu_name;	
}

function bsp_nav_menu_login_link($menu, $args) {
	global $bsp_login;
	$menu_name = bsp_get_menu_from_args($args); 
	//if menu not set then not this menu, so return
	if (empty ($bsp_login['login_'.$menu_name]))
		return $menu;
	//othewise... 
	if (!empty ($bsp_login['only_bbpress'] )) {
		if(is_bbpress()) {
                        $loginlink = bsp_login();
		}
		else {
                        $loginlink="";
		}
	}
	else {
                $loginlink = bsp_login();
	}
        $menu = $menu . $loginlink;
	return apply_filters( 'bsp_nav_menu_login_link', $menu );
}

function bsp_login() {
        global $bsp_login;
        if (is_user_logged_in()) {
                if (!empty($bsp_login['Login/logoutLogout page'] )) {
                        $url=$bsp_login['Login/logoutLogout page'];
                }
                else {
                        $url=site_url();
                }		
                $url2=wp_logout_url($url);
                //add menu item name
                $link = (!empty($bsp_login['Add login/logout to menu itemslogout']) ? $bsp_login['Add login/logout to menu itemslogout'] : 'Logout');
                //if we have a logout class add it here
                $start = (!empty($bsp_login['Add login/logout to menu itemslogoutcss']) ? '<li class="'.$bsp_login['Add login/logout to menu itemslogoutcss'].'">' :'<li>');
                //$end = (!empty($bsp_login['Add login/logout to menu itemslogoutcss']) ? '</span>' :'');
                $loginlink = $start.'<a href="'.$url2.'">'.$link.'</a></li>';
                return $loginlink;
        }
        else {
                if (!empty($bsp_login['Login/logoutLogin page'] )) {
                        $url = $bsp_login['Login/logoutLogin page'];
                }
                else {
                        $url=site_url().'/wp-login.php';
                }
                //add menu item name
                $link = (!empty($bsp_login['Add login/logout to menu itemslogin']) ? $bsp_login['Add login/logout to menu itemslogin'] : 'Login');
                //if we have a login class add it here
                $start = (!empty($bsp_login['Add login/logout to menu itemslogincss']) ? '<li class="'.$bsp_login['Add login/logout to menu itemslogincss'].'">' :'<li>');
                //$end = (!empty($bsp_login['Add login/logout to menu itemslogincss']) ? '</span>' :'');
                $loginlink = $start.'<a href="'.$url.'">'.$link.'</a></li>';
                return $loginlink;
        }

}


if (!empty ($bsp_login['edit_profile'] )) {
        add_filter( 'wp_nav_menu_items', 'bsp_edit_profile', 10,2 );
}

function bsp_edit_profile ($menu, $args) { 
	global $bsp_login;		
	if (!is_user_logged_in()) return $menu;
	$menu_name = bsp_get_menu_from_args($args); 
	//if menu not set then not this menu, so return
	if (empty ($bsp_login['profile_'.$menu_name])) return $menu;
	//else if it's set to bbPress only and it's not bbPress - then return
	if(!empty($bsp_login['profile_only_bbpress'] ) && (!is_bbpress())) {
		return $menu;	
	}
	else {
		$current_user = wp_get_current_user();
		$user=$current_user->user_nicename ;
		$user_slug = get_option( '_bbp_user_slug' );
                if (get_option( '_bbp_include_root' ) == true ) {	
                        $forum_slug = get_option( '_bbp_root_slug' );
                        $slug = $forum_slug.'/'.$user_slug.'/';
                }
                else {
                        $slug=$user_slug . '/';
                }
                if (!empty($bsp_login['edit profileMenu Item Description'] )) {
                        $edit_profile=$bsp_login['edit profileMenu Item Description'];
                }
                else $edit_profile = __('Edit Profile', 'bbp-style-pack');
                //see if we are linking to edit or main profile
                if (empty($bsp_login['profile_not_edit'] )) {
                        $edit = '/edit';
                }
                else $edit = '';
                //get url
                $url = get_home_url(); 
                $start = (!empty($bsp_login['edit profilecss']) ? '<li class="'.$bsp_login['edit profilecss'].'">' :'<li>');
                $profilelink = $start.'<a href="'. $url .'/' .$slug. $user . $edit . '">'.$edit_profile.'</a></li>';

		$menu = $menu . $profilelink;
	}
        return apply_filters( 'bsp_edit_profile', $menu );
}


if (!empty ($bsp_login['register'] ) ) {
        add_filter( 'wp_nav_menu_items', 'bsp_register', 10,2 );
}

function bsp_register ($menu, $args) { 
        global $bsp_login;	
        if (is_user_logged_in())
                return $menu;
        $menu_name = bsp_get_menu_from_args($args); 
        //if menu not set then not this menu, so return
        if (empty ($bsp_login['register_'.$menu_name]))
                return $menu;
        //else if it's set to bbPress only and it's not bbPress - then return
        if(!empty($bsp_login['register_only_bbpress'] ) && (!is_bbpress())) {
                return $menu;	
        }
        else
                $url = $bsp_login['Register PageRegister page'];
        if (!empty($bsp_login['Register PageMenu Item Description'] )) {
                $desc=$bsp_login['Register PageMenu Item Description'];
        }
        else $desc=__('Register', 'bbp-style-pack');
        $start = (!empty($bsp_login['Register Pagecss']) ? '<li class="'.$bsp_login['Register Pagecss'].'">' :'<li>');
        $registerlink = $start.'<a href="'.$url.'">'.$desc.'</a></li>';
        $menu = $menu . $registerlink;
        return apply_filters( 'bsp_register', $menu );
	
}

function bsp_login_redirect ($redirect) {
	//quit if it is a redirect
	if (strpos($_SERVER['REQUEST_URI'], '?redirect_to=') == true ) return $redirect;
	global $bsp_login;	
	//find out whether we need to do a redirect
	$login_page = $bsp_login['Login/logoutLogin page'];
	$login_redirect = $bsp_login['Login/logoutLogged in redirect']; 
	
	$length1 = strlen ( site_url() );
	$length2 = strlen ( $login_page );
	$loginslug = substr( $login_page, $length1, $length2 );
	//put a '/' on the end if not there !
	if (substr($loginslug, -1) != '/') $loginslug.='/';
 
	//if the page that we're on ($_SERVER['REQUEST_URI']) is the one that is used for login ($loginslug) then we know that it is a redirect from our login not a widget redirect, so can do our redirect
        if ($_SERVER['REQUEST_URI'] == $loginslug) {
                $redirect_to = $login_redirect;
                return $redirect_to;
        }
}


if (!empty ($bsp_login['Login/logoutLogged in redirect'] )) {	
        add_filter ('bbp_user_login_redirect_to' , 'bsp_login_redirect');
}


/**********breadcrumbs ********/



if ( !empty( $bsp_breadcrumb['no_breadcrumb'] ) ) {
        add_filter ('bbp_no_breadcrumb', 'bsp_no_breadcrumb');
}

//no breadcrumbs
function bsp_no_breadcrumb ($param) { 
        return true;
}

//add the filter - if no args set then this does nothing
add_filter('bbp_before_get_breadcrumb_parse_args', 'bsp_breadcrumbs');

function bsp_breadcrumbs ($args) {
	global $bsp_breadcrumb;
	if ( !empty( $bsp_breadcrumb['no_home_breadcrumb'] ) ) $args['include_home'] = false;
	if ( !empty( $bsp_breadcrumb['no_root_breadcrumb'] ) ) $args['include_root'] = false;
	if ( !empty( $bsp_breadcrumb['no_current_breadcrumb'] ) ) $args['include_current'] = false;
	if (class_exists ('polylang')) {
		$current = pll_current_language();
		if (!empty ($bsp_breadcrumb['Breadcrumb HomeText'.$current] )) $args['home_text'] = $bsp_breadcrumb['Breadcrumb HomeText'.$current];
		elseif (!empty ($bsp_breadcrumb['Breadcrumb HomeText'] )) $args['home_text'] = $bsp_breadcrumb['Breadcrumb HomeText'];
		if (!empty ($bsp_breadcrumb['Breadcrumb RootText'.$current] )) $args['root_text'] = $bsp_breadcrumb['Breadcrumb RootText'.$current];
		elseif (!empty ($bsp_breadcrumb['Breadcrumb RootText'] )) $args['root_text'] = $bsp_breadcrumb['Breadcrumb RootText'];
		if (!empty ($bsp_breadcrumb['Breadcrumb CurrentText'.$current] )) $args['current_text'] = $bsp_breadcrumb['Breadcrumb CurrentText'.$current];
		elseif (!empty ($bsp_breadcrumb['Breadcrumb CurrentText'] )) $args['current_text'] = $bsp_breadcrumb['Breadcrumb CurrentText'];
	}
	else {
                if (!empty ($bsp_breadcrumb['Breadcrumb HomeText'] )) $args['home_text'] = $bsp_breadcrumb['Breadcrumb HomeText'];
                if (!empty ($bsp_breadcrumb['Breadcrumb RootText'] )) $args['root_text'] = $bsp_breadcrumb['Breadcrumb RootText'];
                if (!empty ($bsp_breadcrumb['Breadcrumb CurrentText'] )) $args['current_text'] = $bsp_breadcrumb['Breadcrumb CurrentText'];
	}
	//but set home icon if this is set
	if (!empty ($bsp_breadcrumb['home_icon'] )) $args['home_text'] = '<span class="bsp-home-icon"></span>';
	return $args;

}

//change breadcrumb urls if set

add_filter ('bbp_breadcrumbs', 'bsp_breadcrumb_urls');

function bsp_breadcrumb_urls ($crumbs ) {
	global $bsp_breadcrumb;
	$pattern = '/(?<=href\=")[^]]+?(?=")/';
	//home is $crumbs[0] root is $crumbs[1];
	//check if polylang is set
	if (class_exists ('polylang')) {
		$current = pll_current_language();
		$home = (!empty($bsp_breadcrumb['Breadcrumb HomeURL'.$current]) ? $bsp_breadcrumb['Breadcrumb HomeURL'.$current] : '');
		//allow backward compatibility
		if (empty ($home)) $home = (!empty($bsp_breadcrumb['Breadcrumb HomeURL']) ? $bsp_breadcrumb['Breadcrumb HomeURL'] : '');
		$root = (!empty($bsp_breadcrumb['Breadcrumb RootURL'.$current]) ? $bsp_breadcrumb['Breadcrumb RootURL'.$current] : '');
 //allow backward compatibility
		if (empty ($root)) $root = (!empty($bsp_breadcrumb['Breadcrumb RootURL']) ? $bsp_breadcrumb['Breadcrumb RootURL'] : '');
	}
	else {
                $home = (!empty($bsp_breadcrumb['Breadcrumb HomeURL']) ? $bsp_breadcrumb['Breadcrumb HomeURL'] : '');
                $root = (!empty($bsp_breadcrumb['Breadcrumb RootURL']) ? $bsp_breadcrumb['Breadcrumb RootURL'] : '');
	}
	if (!empty ($home)) {
		$crumbs[0] = preg_replace($pattern, $home, $crumbs[0]);
	}
	if (!empty ($root) && !empty ($crumbs[1])) {
		$crumbs[1] = preg_replace($pattern, $root, $crumbs[1]);
	}
        return $crumbs;	
}


//if there is no root breadcrumb (because we are in the root), bbPress displays the current breadcrumb name instead of the root - fine unless user has entered a root name, so check and amend
//in 2.6.x this filter has four arguments, in 2.5.x only 3, so since we don't need the 4th, we only bring through the first 3 - $args (4th argument) is also available on 2.6.x
add_filter ('bbp_get_breadcrumb' , 'bsp_amend_root_name', 10 , 3);

function bsp_amend_root_name ($trail, $crumbs, $r ) {
	global $bsp_breadcrumb;
	//if no root text but current text, but we are s'posed to be showing root text, then we need to show current text as root text
	if (bbp_is_forum_archive() && empty ($r['include_root']) && !empty ($r['include_current'])) {
		if ( empty( $bsp_breadcrumb['no_root_breadcrumb'] ) ) {
			$stringold = '<span class="bbp-breadcrumb-current">'.$r['current_text'].'</span>';
			$stringnew = '<span class="bbp-breadcrumb-current">'.$r['root_text'].'</span>';
			$trail = str_replace ($stringold , $stringnew , $trail);
                }
	}
	return apply_filters( 'bsp_amend_root_name', $trail, $crumbs, $r );
}


//This function changes the text wherever it is quoted
function bsp_change_text( $translated_text, $text, $domain ) {
 global $bsp_login;
	if ( $text == 'You are already logged in.' ) {
                $translated_text = $bsp_login['Login/logoutLogged in text'];
	}
	return $translated_text;
}

if (!empty ($bsp_login['Login/logoutLogged in text'] )) add_filter( 'gettext', 'bsp_change_text', 20, 3 );


//this function adds the gravatar thingy to the profile page
if (!empty ($bsp_profile['gravatar'] )) {
        add_action( 'bbp_user_edit_after_name', 'bsp_mention_gravatar' );
}


function bsp_mention_gravatar() {
        global $bsp_profile;
        $label = (!empty($bsp_profile['ProfileGravatar Label']) ? $bsp_profile['ProfileGravatar Label'] : '');
        $gdesc = (!empty($bsp_profile['ProfileItem Description']) ? $bsp_profile['ProfileItem Description'] : '');
        $gurl = (!empty($bsp_profile['ProfilePage URL']) ? esc_html ($bsp_profile['ProfilePage URL']) : '');
        $gurl = '<a href="'.$gurl.'" title="Gravatar">';
        $gurldesc = (!empty($bsp_profile['ProfileURL Description']) ? esc_html ($bsp_profile['ProfileURL Description']) : '');
        ?>
        <div>
                <label for="bbp-gravatar-notice"><?php echo $label ?></label>
                <fieldset style="width: 60%;">
                        <span style="margin-left: 0; width: 100%;" name="bbp-gravatar-notice" class="description"><?php echo $gdesc ?> <?php echo $gurl?> <?php echo $gurldesc ?></a>.</span>
                </fieldset>
        </div>

        <?php
}

///////////////////////////////////////////////////FORUM ROLES FUNCTION

add_filter( 'bbp_get_reply_author_role', 'bsp_get_reply_author_role', 10,2); 
add_filter( 'bbp_get_topic_author_role', 'bsp_get_reply_author_role', 10,2); 


function bsp_get_reply_author_role( $author_role, $r ) {
        global $bsp_roles;
        $roles_show = (!empty($bsp_roles['all_roleswhere_to_display']) ? $bsp_roles['all_roleswhere_to_display'] : '');
        if ($roles_show == 2 ) return; //2 = show at top, so hide here
        $author_role = bsp_author_role ($r);
	return apply_filters( 'bsp_get_reply_author_role', $author_role, $r );
}

$roles_show = (!empty($bsp_roles['all_roleswhere_to_display']) ? $bsp_roles['all_roleswhere_to_display'] : '');

//if roles showing above - add this filter
if ($roles_show == 2 ) {
	add_action ('bbp_theme_before_reply_author_details' , 'bsp_display_reply_role' );
	add_action ('bbp_theme_before_topic_author_details' , 'bsp_display_topic_role' );
}



function bsp_display_reply_role( $args = array() ) {
	// Parse arguments against default values
        $r = bbp_parse_args( $args, array(
                'reply_id' => 0,
                'class' => 'bbp-author-role',
                'before' => '',
                'after' => ''
        ), 'get_reply_author_role' );
        $r['reply_id'] = bbp_get_reply_id( $r['reply_id'] );
        $author_role = bsp_author_role ($r);
        echo $author_role;
}

function bsp_display_topic_role( $args = array() ) {
	// Parse arguments against default values
        $r = bbp_parse_args( $args, array(
                'topic_id' => 0,
                'class' => 'bbp-author-role',
                'before' => '',
                'after' => ''
        ), 'get_reply_author_role' );
	$r['topic_id'] = bbp_get_topic_id( $r['topic_id'] );
	$author_role = bsp_author_role ($r);
	echo $author_role;
}

//added function to allow others to call the role
function bsp_get_user_display_role( $user_id = 0 ) {
	if (empty ($user_id) ) $user_id = bbp_get_user_id( $user_id );
	$r['profile_id'] = $user_id;
	$r['before'] = '';
	$r['after'] = '<br>';
	
	$author_role = bsp_author_role ($r);
	return $author_role;
}

	
function bsp_author_role ($r) {
	global $bsp_roles;
	//check if we are displaying roles at all or if we are not displaying after display name, and bail if appropriate
        $roles_show = (!empty($bsp_roles['all_roleswhere_to_display']) ? $bsp_roles['all_roleswhere_to_display'] : '');
        if ($roles_show == 1 ) return; //1 = hide
                //if reply set up reply variables
                if (!empty($r['reply_id'] )) {
                        $item_id = $r['reply_id'];
                        $role = bbp_get_user_role( bbp_get_reply_author_id( $item_id ) );
                        $roledisplay = bbp_get_user_display_role( bbp_get_reply_author_id( $item_id ) );	
                }
                //if topic set up topic variables
                if (!empty($r['topic_id'] )) {
                        $item_id = $r['topic_id'];
                        $role = bbp_get_user_role( bbp_get_topic_author_id( $item_id ) );
                        $roledisplay = bbp_get_user_display_role( bbp_get_topic_author_id( $item_id ) );	
                }

                //if profile ...
                if (!empty($r['profile_id'] )) {
                        $role = bbp_get_user_role( $r['profile_id']);
                        $roledisplay = bbp_get_user_display_role($r['profile_id']);
                }
	
                //added in 3.7.5 to get around a case where $role isn't set and the rest of this errors - further work to understand why needed if/when i can replicate
                if (!empty ($role)) {
                        //now check if we should display this role, and if not just return
                        $type = $role.'type';
                        //bail if doesn't exist (anymore! - may be an old role that's been deleted)
                        if (empty($bsp_roles[$type]) ) {
                                $author_role = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), esc_html( $roledisplay ), $r['after'] );
                                return apply_filters( 'bsp_get_reply_author_role', $author_role, $r );
                        }	

                        if ($bsp_roles[$type] == 5) return;

                        $r['class'] = 'bsp-author-'.$role;
                        //get which display we are showing
                        //if image then...
                        if ($bsp_roles[$type] == 1) {
                                $image = (!empty($bsp_roles[$role.'image']) ? $bsp_roles[$role.'image'] : '');
                                $image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
                                $image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
                                $role = '<img src = "'.$image.'" height="'.$image_height.'" width="'.$image_width.'" >';
                                $author_role = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), $role , $r['after'] );		
                        }

                        //if name then...(with either background color if specified or image - styles.php checks which is required)
                        if ($bsp_roles[$type] == 2 || $bsp_roles[$type] == 3 ) {
                                $roledisplay = (!empty($bsp_roles[$role.'name']) ? $bsp_roles[$role.'name'] : $roledisplay);
                                $author_role = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), esc_html( $roledisplay ), $r['after'] );
                        }

                        //if name under image
                        if ($bsp_roles[$type] == 4) {
                                $image = (!empty($bsp_roles[$role.'image']) ? $bsp_roles[$role.'image'] : '');
                                $image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
                                $image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
                                $role1 = '<img src = "'.$image.'" height="'.$image_height.'" width="'.$image_width.'" >';
                                $role2 = (!empty($bsp_roles[$role.'name']) ? $bsp_roles[$role.'name'] : $roledisplay);
                                $author_role = sprintf( '%1$s<div class="%2$s"><ul><li>%3$s</li><li>%4$s</li></ul></div>%5$s', $r['before'], esc_attr( $r['class'] ), $role1, $role2 , $r['after'] );	;
                        }

                        //now add topic author
                        $author_show = (!empty($bsp_roles['topic_authortype']) ? $bsp_roles['topic_authortype'] : '');

                        //if this is profile display - bail here
                        if (!empty($r['profile_id'] )) {
                                return apply_filters( 'bsp_get_reply_author_role', $author_role );	
                        }
                        
/* ROBIN - CHECK THIS "BAIL" LINE - SEEMS INCORRECT/INCOMPLETE */
/* REW commenetd oiut in 5.5.0 - delete if this causes no problems
/***********************************************************************/
                        //bail at this point if hide is active or not set
                        //if (empty ($author_show) ) 
/***********************************************************************/

                        //if this is a topic... (this id matches the topic), then don't display topic author - just bail here
                        //either we are using topic_id, so just quit here
                        if (!empty($r['topic_id'] )) return apply_filters( 'bsp_get_reply_author_role', $author_role, '' );
                        //the line above did read as follows, but this errored in search results
                        //if (!empty($r['topic_id'] )) return apply_filters( 'bsp_get_reply_author_role', $reply_id );

                        //or (this id matches the topic), then don't display topic author - just quit here
                        if (!empty($r['reply_id'] )) {
                                $topic_id = bbp_get_reply_topic_id( $r['reply_id'] );
                                if ($topic_id == $r['reply_id'] ) return apply_filters( 'bsp_get_reply_author_role_2', $author_role, $r['reply_id'] );
                        }

                        //needed if show lead topic = true !
                        //or this is the topic !!
                        if (empty($r['reply_id'] )) {
                                return apply_filters( 'bsp_get_reply_author_role_3', $author_role,'' );
                        }

                        //now check if it is the topic author
                        $author_topic = bbp_get_reply_author_id( $topic_id );
                        $author_reply = bbp_get_reply_author_id( $r['reply_id'] );

                        //then bail if they don't match
                        if ($author_topic != $author_reply ) return apply_filters( 'bsp_get_reply_author_role_4', $author_role, $r['reply_id'] );

                        //and if it is ...
                        if (empty ($author_role) ) $author_role = ''; //allow for no role above being shown
                        $r['class'] = 'bsp-author-topic_author';
                        $role = 'topic_author';
                        $type = $role.'type';
                        //if image then...
                        if ($bsp_roles[$type] == 1) {
                                $image = (!empty($bsp_roles[$role.'image']) ? $bsp_roles[$role.'image'] : '');
                                $image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
                                $image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
                                $role = '<img src = "'.$image.'" height="'.$image_height.'" width="'.$image_width.'" >';
                                $author_role .= sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), $role , $r['after'] );		
                        }
                        //if name then...(with either background color if specified or image - styles.php checks which is required)
                        if ($bsp_roles[$type] == 2 || $bsp_roles[$type] == 3 ) {
                                $roledisplay = (!empty($bsp_roles[$role.'name']) ? $bsp_roles[$role.'name'] : $roledisplay);
                                $author_role .= sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), esc_html( $roledisplay ), $r['after'] );
                        }
                        //if name under image
                        if ($bsp_roles[$type] == 4) {
                                $image = (!empty($bsp_roles[$role.'image']) ? $bsp_roles[$role.'image'] : '');
                                $image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
                                $image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
                                $role1 = '<img src = "'.$image.'" height="'.$image_height.'" width="'.$image_width.'" >';
                                $role2 = (!empty($bsp_roles[$role.'name']) ? $bsp_roles[$role.'name'] : $roledisplay);

                                $author_role .= sprintf( '%1$s<div class="%2$s"><ul><li>%3$s</li><li>%4$s</li></ul></div>%5$s', $r['before'], esc_attr( $r['class'] ), $role1, $role2 , $r['after'] );	;
                        }
		
                return apply_filters( 'bsp_get_reply_author_role', $author_role, $r['reply_id'] );
                }
        return; //failsafe if $role is blank
}





//////////////remove space after the name and before the role


function bsp_break_remove ($author_link) {
        $pattern = '#<br /><div class="bsp-author#';
        $replacement = '<div class="bsp-author';
        $author_link = preg_replace($pattern, $replacement, $author_link);
        return $author_link;
}


if (!empty ($bsp_roles['removeline'] )) {
	add_filter ('bbp_get_reply_author_link' , 'bsp_break_remove' );
}



////////////////////////////////////////////////////////FRESHNESS DISPLAY

//filter to correctly return last active ID for sub forums
//note : a parent forum or category can get the wrong last active ID if a topic in a sub forum is marked as spam or deleted. This filter ignores the parent and works out the correct sub forum

//don't add if pg filter exists as this will have done it already
if (!function_exists ('private_groups_get_permitted_subforums')) {
	global $bsp_forum_display;
	if (!empty ($bsp_forum_display['forum_freshness'])) {	
                add_filter ('bbp_get_forum_last_active_id' , 'bsp_get_forum_last_active_id', 10 , 2 );
	}
}

function bsp_get_forum_last_active_id ($active_id, $forum_id) {
	$sub_forums = bbp_forum_get_subforums($forum_id);
	if ( !empty( $sub_forums ) ) {
		$active_id = 0;
		$show = array();
		//find the latest permissible 
		foreach ( $sub_forums as $sub_forum ) {
			$sub_forum_id = $sub_forum->ID;
			$active_id = get_post_meta( $sub_forum_id , '_bbp_last_active_id', true );
			$last_active = get_post_meta( $sub_forum_id, '_bbp_last_active_time', true );
			if ( empty( $active_id ) ) { // not replies, maybe topics ?
				$active_id = bbp_get_forum_last_topic_id( $sub_forum_id );
				if ( !empty( $active_id ) ) {
					$last_active = bbp_get_topic_last_active_time( $active_id );
				}
			}
			if ( !empty( $active_id ) ) {
				$curdate = strtotime($last_active);
				$show[$curdate] = $active_id;
			}
		}
		//then add the forum itself in case it has the latest
                $active_id = get_post_meta( $forum_id , '_bbp_last_active_id', true );
                $last_active = get_post_meta( $sub_forum_id, '_bbp_last_active_time', true );
                if ( empty( $active_id ) ) { // not replies, maybe topics ?
                        $active_id = bbp_get_forum_last_topic_id( $forum_id );
                        if ( !empty( $active_id ) ) {
                                $last_active = bbp_get_topic_last_active_time( $active_id );
                        }
                }
                if ( !empty( $active_id ) ) {
                        $curdate = strtotime($last_active);
                        $show[$curdate] = $active_id;
                }
		$mostRecent= 0;
		foreach($show as $date=>$value){
			if ($date > $mostRecent) {
				 $mostRecent = $date;
			}
		}
		if ($mostRecent != 0) {
			$active_id = $show[$mostRecent];
		} else {
			$active_id = 0;
		}
	}
	return apply_filters( 'bsp_get_forum_last_active_id', $active_id, $forum_id );
}




//Check they are activated, and add filters if they are
if (!empty ($bsp_style_settings_freshness ['activate'] )) {
	//heading name
	if (!empty ($bsp_style_settings_freshness ['heading_name'] )) {
		add_filter( 'gettext', 'bsp_change_translate_text', 20, 3 );		
	}
	//show title
	if (!empty ($bsp_style_settings_freshness ['show_title'] )) {
		add_action( 'bbp_theme_before_forum_freshness_link', 'bsp_freshness_display_title');
	}
	//show (hide!) date
	if (!empty ($bsp_style_settings_freshness) && empty($bsp_style_settings_freshness ['show_date'] )) {
		add_filter('bbp_get_forum_freshness_link', 'bsp_hide_freshness_link' );
		add_filter('bbp_get_topic_freshness_link', 'bsp_hide_freshness_link' );
	}
	else {
		//if we are showing freshness link, then ensure correct last active ID from sub forum shown if needed
		// & don't add if pg filter exists as this will have done it already
		if (!function_exists ('pg_get_forum_freshness_link')) {
			add_filter('bbp_get_forum_freshness_link', 'bsp_get_forum_freshness_link' , 10 ,2);
		}
	}
	
	//show avatar/name combination as appropriate
	
	if (!empty($bsp_style_settings_freshness)) {
		//firstly filtered if PG is active to ensure we show the correct author
		if (function_exists ('rpg_get_last_active_author')) {
			add_filter ('bbp_before_get_author_link_parse_args' , 'rpg_get_last_active_author' );
		}
		add_filter('bbp_before_get_author_link_parse_args', 'bsp_author_freshness_link' );
	}
	
	
	//change date format if needed
	if (!empty ($bsp_style_settings_freshness ['date_format'] ) && $bsp_style_settings_freshness ['date_format'] == 2) {
		add_filter( 'bbp_get_forum_last_active', 'bsp_change_freshness_forum', 10, 2 );
		add_filter( 'bbp_get_topic_last_active', 'bsp_change_freshness_topic', 10, 2 );
	}	
	
	//change date format if needed - hybrid
	if (!empty ($bsp_style_settings_freshness ['date_format'] ) && $bsp_style_settings_freshness ['date_format'] == 3) {
		add_filter( 'bbp_get_forum_last_active', 'bsp_change_forum_freshness_hybrid', 10, 2 );
		add_filter( 'bbp_get_topic_last_active', 'bsp_change_topic_freshness_hybrid', 10, 2 );
	}
	
        function bsp_change_forum_freshness_hybrid ($active_time, $forum_id) {
                global $bsp_style_settings_freshness;
                $forum_id = bbp_get_forum_id( $forum_id );
                $last_active = get_post_meta( $forum_id, '_bbp_last_active_time', true );
                if ( empty( $last_active ) ) {
                        $reply_id = bbp_get_forum_last_reply_id( $forum_id );
                        if ( !empty( $reply_id ) ) {
                                $last_active = get_post_field( 'post_date', $reply_id );
                        } else {
                                $topic_id = bbp_get_forum_last_topic_id( $forum_id );
                                if ( !empty( $topic_id ) ) {
                                        $last_active = bbp_get_topic_last_active_time( $topic_id );
                                }
                        }
                }
                $last_active_unix = strtotime ($last_active);
                $days_back = (!empty ($bsp_style_settings_freshness['hybrid_days_back'] ) ? $bsp_style_settings_freshness['hybrid_days_back'] : '7' );
                $seconds_back = $days_back * 86400;
                $test = current_time ('timestamp') - $seconds_back;
                if ($last_active_unix < $test) {
                        $active_time = bsp_change_freshness_forum ($active_time, $forum_id );
                }
                return $active_time;
        }

        function bsp_change_topic_freshness_hybrid ($active_time, $topic_id) {
                global $bsp_style_settings_freshness;
                $topic_id = bbp_get_topic_id( $topic_id );

                // Try to get the most accurate freshness time possible
                $last_active = get_post_meta( $topic_id, '_bbp_last_active_time', true );
                if ( empty( $last_active ) ) {
                        $reply_id = bbp_get_topic_last_reply_id( $topic_id );
                        if ( !empty( $reply_id ) ) {
                                $last_active = get_post_field( 'post_date', $reply_id );
                        } else {
                                $last_active = get_post_field( 'post_date', $topic_id );
                        }
                }

                $last_active_unix = strtotime ($last_active);
                $days_back = (!empty ($bsp_style_settings_freshness['hybrid_days_back'] ) ? $bsp_style_settings_freshness['hybrid_days_back'] : '7' );
                $seconds_back = $days_back * 86400;
                $test = current_time ('timestamp') - $seconds_back;
                if ($last_active_unix < $test) {
                        $active_time = bsp_change_freshness_topic ($last_active, $topic_id);
                }
                return $active_time;
        }


        //amend freshness if not english
        if ( (get_locale() != 'en_GB' && get_locale() != 'en_US') && $bsp_bbpress_version == '2.6' ) {
                add_filter ('bbp_get_time_since' , 'bsp_time_since_translate' );
        }		

        function bsp_time_since_translate ($output) {
                global $bsp_style_settings_freshness;
                if (!empty ($bsp_style_settings_freshness['years'])) $output = preg_replace('/years/', $bsp_style_settings_freshness['years'], $output);
                if (!empty ($bsp_style_settings_freshness['year']))$output = preg_replace('/year/', $bsp_style_settings_freshness['year'], $output);
                if (!empty ($bsp_style_settings_freshness['months']))$output = preg_replace('/months/', $bsp_style_settings_freshness['months'], $output);
                if (!empty ($bsp_style_settings_freshness['month']))$output = preg_replace('/month/', $bsp_style_settings_freshness['month'], $output);
                if (!empty ($bsp_style_settings_freshness['weeks']))$output = preg_replace('/weeks/', $bsp_style_settings_freshness['weeks'], $output);
                if (!empty ($bsp_style_settings_freshness['week']))$output = preg_replace('/week/', $bsp_style_settings_freshness['week'], $output);
                if (!empty ($bsp_style_settings_freshness['days']))$output = preg_replace('/days/', $bsp_style_settings_freshness['days'], $output);
                if (!empty ($bsp_style_settings_freshness['day']))$output = preg_replace('/day/', $bsp_style_settings_freshness['day'], $output);
                if (!empty ($bsp_style_settings_freshness['hours']))$output = preg_replace('/hours/', $bsp_style_settings_freshness['hours'], $output);
                if (!empty ($bsp_style_settings_freshness['hour']))$output = preg_replace('/hour/', $bsp_style_settings_freshness['hour'], $output);
                if (!empty ($bsp_style_settings_freshness['minutes']))$output = preg_replace('/minutes/', $bsp_style_settings_freshness['minutes'], $output);
                if (!empty ($bsp_style_settings_freshness['minute']))$output = preg_replace('/minute/', $bsp_style_settings_freshness['minute'], $output);
                if (!empty ($bsp_style_settings_freshness['seconds']))$output = preg_replace('/seconds/', $bsp_style_settings_freshness['seconds'], $output);
                if (!empty ($bsp_style_settings_freshness['second']))$output = preg_replace('/second/', $bsp_style_settings_freshness['second'], $output);
                return $output;
        }
}

function bsp_freshness_display_title ($forum_id = 0) {
	//use pg function if private groups plugin active
	if (function_exists ('pg_get_forum_freshness_title')) {
		$anchor = pg_get_forum_freshness_title();		
	} 
	else {	
                // Verify forum and get last active meta
                $forum_id = bbp_get_forum_id( $forum_id );
                $active_id = bbp_get_forum_last_active_id( $forum_id );
                $link_url = $title = '';

                if ( empty( $active_id ) )
                $active_id = bbp_get_forum_last_reply_id( $forum_id );

                if ( empty( $active_id ) )
                $active_id = bbp_get_forum_last_topic_id( $forum_id );

                if ( bbp_is_topic( $active_id ) ) {
                        //then reset forum_id to the forum of the active topic in case it is a sub forum
                        $forum_id = bbp_get_topic_forum_id($active_id);
                        $link_url = bbp_get_forum_last_topic_permalink( $forum_id );
                        $title = bbp_get_forum_last_topic_title( $forum_id );
                } elseif ( bbp_is_reply( $active_id ) ) {
                        //then reset forum_id to the forum of the active topic in case it is a sub forum
                        $forum_id = bbp_get_reply_forum_id($active_id);
                        $link_url = bbp_get_forum_last_reply_url( $forum_id );
                        $title = bbp_get_forum_last_reply_title( $forum_id );
                }

                $anchor = '<a class="bsp_freshness_display_title" href="' . esc_url( $link_url ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $title ) . '</a>';
	}
        echo $anchor.'<p/>';
}

function bsp_get_forum_freshness_link ($anchor, $forum_id) {
        //amended to reset the forum_id as commented below
        global $rpg_settingsf;
        $forum_id = bbp_get_forum_id( $forum_id );
        $active_id = bbp_get_forum_last_active_id( $forum_id );

        if ( empty( $active_id ) )
        $active_id = bbp_get_forum_last_reply_id( $forum_id );

        if ( empty( $active_id ) )
        $active_id = bbp_get_forum_last_topic_id( $forum_id );

        $link_url = $title = '';

        if ( bbp_is_topic( $active_id ) ) {
                //then reset forum_id to the forum of the active topic in case it is a sub forum
                $forum_id = bbp_get_topic_forum_id($active_id);
                $link_url = bbp_get_forum_last_topic_permalink( $forum_id );
                $title = bbp_get_forum_last_topic_title( $forum_id );

        } elseif ( bbp_is_reply( $active_id ) ) {
                //then reset forum_id to the forum of the active topic in case it is a sub forum
                $forum_id = bbp_get_reply_forum_id($active_id);
                $link_url = bbp_get_forum_last_reply_url( $forum_id );
                $title = bbp_get_forum_last_reply_title( $forum_id );

        }

        $time_since = bbp_get_forum_last_active_time( $forum_id );

        if ( !empty( $time_since ) && !empty( $link_url ) )
                $anchor = '<a href="' . esc_url( $link_url ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $time_since ) . '</a>';
        else
                $anchor = esc_html__( 'No Topics', 'bbpress' );

        return apply_filters( 'bsp_get_forum_freshness_link', $anchor, $forum_id, $time_since, $link_url, $title, $active_id );
}

function bsp_hide_freshness_link() {
	$anchor = '<b></b>';
	return $anchor;
}

function bsp_author_freshness_link ($args) {
	global $bsp_style_settings_freshness;
	if (!empty($bsp_style_settings_freshness ['show_name']) && !empty($bsp_style_settings_freshness ['show_avatar'] )) $args ['type'] = 'both';
	if (empty($bsp_style_settings_freshness ['show_name']) && !empty($bsp_style_settings_freshness ['show_avatar'] )) $args ['type'] = 'avatar';
	if (!empty($bsp_style_settings_freshness ['show_name']) && empty($bsp_style_settings_freshness ['show_avatar'] )) $args ['type'] = 'name';
	if (empty($bsp_style_settings_freshness ['show_name']) && empty($bsp_style_settings_freshness ['show_avatar'] )) $args['post_id'] = '';
	return $args;
}

//this function changes the bbp freshness data (time since) into a last post date for forums
function bsp_change_freshness_forum ($active_time, $forum_id ) {
	global $bsp_style_settings_freshness;

        // Verify forum and get last active meta
        $forum_id = bbp_get_forum_id( $forum_id );
        $last_active = get_post_meta( $forum_id, '_bbp_last_active_time', true );

        if ( empty( $last_active ) ) {
                $reply_id = bbp_get_forum_last_reply_id( $forum_id );
                if ( !empty( $reply_id ) ) {
                        $last_active = get_post_field( 'post_date', $reply_id );
                } else {
                        $topic_id = bbp_get_forum_last_topic_id( $forum_id );
                        if ( !empty( $topic_id ) ) {
                                $last_active = bbp_get_topic_last_active_time( $topic_id );
                        }
                }
        }

        $last_active = bbp_convert_date( $last_active );
        $date_format = (!empty ( $bsp_style_settings_freshness['bsp_date_format'] ) ? $bsp_style_settings_freshness['bsp_date_format'] : '' );
        $time_format = (!empty ( $bsp_style_settings_freshness['bsp_time_format'] ) ? $bsp_style_settings_freshness['bsp_time_format'] : '' );
        if ($date_format == 'custom' ) $date_format = $bsp_style_settings_freshness['bsp_date_format_custom'];
        if ($time_format == 'custom' ) $time_format = $bsp_style_settings_freshness['bsp_time_format_custom'];
        $date= date_i18n( "{$date_format}", $last_active );
        $time=date_i18n( "{$time_format}", $last_active );
        //check the order
        if (!empty ($bsp_style_settings_freshness['date_order'])) {
                $first = $time;
                $second = $date;
        }
        else {
                $first = $date;
                $second = $time;
        }
        $separator = (!empty ($bsp_style_settings_freshness['date_separator'] ) ? $bsp_style_settings_freshness['date_separator'] : '' );
        $active_time = $first.$separator.$second;
        return apply_filters ('bsp_change_freshness_forum' , $active_time);
}


//this function changes the bbp freshness data (time since) into a last post date for topics
function bsp_change_freshness_topic ($last_active, $topic_id) {
	global $bsp_style_settings_freshness;
	$topic_id = bbp_get_topic_id( $topic_id );

        // Try to get the most accurate freshness time possible
        $last_active = get_post_meta( $topic_id, '_bbp_last_active_time', true );
        if ( empty( $last_active ) ) {
                $reply_id = bbp_get_topic_last_reply_id( $topic_id );
                if ( !empty( $reply_id ) ) {
                        $last_active = get_post_field( 'post_date', $reply_id );
                } else {
                        $last_active = get_post_field( 'post_date', $topic_id );
                }
        }

        $last_active = bbp_convert_date( $last_active );
        $date_format = (!empty ( $bsp_style_settings_freshness['bsp_date_format'] ) ? $bsp_style_settings_freshness['bsp_date_format'] :'' );
        $time_format = (!empty ( $bsp_style_settings_freshness['bsp_time_format'] ) ? $bsp_style_settings_freshness['bsp_time_format'] : '');
        if ($date_format == 'custom' ) {
                // use default format if empty, custom formatting if specified
                $date_format = ( ! empty( $bsp_style_settings_freshness['bsp_date_format_custom'] ) ? $bsp_style_settings_freshness['bsp_date_format_custom'] : 'F j, Y' );
        } 
        if ($time_format == 'custom' ) {
                // use default format if empty, custom formatting if specified
                $time_format = ( ! empty( $bsp_style_settings_freshness['bsp_time_format_custom'] ) ? $bsp_style_settings_freshness['bsp_time_format_custom'] : 'g:i a' );            
        }         
        $date = date_i18n( "{$date_format}", $last_active );
        $time = date_i18n( "{$time_format}", $last_active );
        //check the order
        if (!empty ($bsp_style_settings_freshness['date_order'])) {
                $first = $time;
                $second = $date;
        }
        else {
                $first = $date;
                $second = $time;
        }
        $separator = (!empty ($bsp_style_settings_freshness['date_separator'] ) ? $bsp_style_settings_freshness['date_separator'] : '' );
        $active_time = $first.$separator.$second;
        return apply_filters ('bsp_change_freshness_topic' , $active_time);
}
		
//This function changes the heading "Freshness" to the name created in Settings
function bsp_change_translate_text( $translated_text, $text, $domain ) {
	global $bsp_style_settings_freshness;
	if (empty ($bsp_style_settings_freshness ['heading_name'] )) return $translated_text;
        $testtext = 'Freshness';
        $testdomain = 'bbpress';
        if ( ($text == $testtext) && ($domain == $testdomain) ) {
                $translated_text = $bsp_style_settings_freshness ['heading_name'];
        }	
	return $translated_text;
}


//shorten Freshness
function bsp_short_freshness_time( $output) {
        $output = preg_replace( '/, .*[^ago]/', ' ', $output );
        return $output;
}

if (!empty ($bsp_style_settings_freshness['freshness_format'])) {
	add_filter( 'bbp_get_time_since', 'bsp_short_freshness_time' );
	add_filter('bp_core_time_since', 'bsp_short_freshness_time');
}

////////////////////////////////Submitting and spinner
//new version_compare
if (!empty ( $bsp_style_settings_form['SubmittingActivate'])) {
	add_action ('bbp_theme_before_topic_form_submit_button' , 'bsp_load_spinner_topic' );
	add_action ('bbp_theme_before_reply_form_submit_button' , 'bsp_load_spinner_reply' );
}

function bsp_load_spinner_topic() {
	global $bsp_style_settings_form;
	//preload spinner so it is ready - CSS hides this
	echo '<div id="bsp-spinner-load"></div>';
	//add button - hidden by CSS
	echo '<button type="submit" id="bsp_topic_submit" name="bbp_topic_submit" class="button submit">';
	//leave as is if field is blanked (user may just want spinner)
	$value = (!empty($bsp_style_settings_form['SubmittingSubmitting']) ? $bsp_style_settings_form['SubmittingSubmitting'] : '');
	echo $value;
	//then add spinner if activated
	if (!empty( $bsp_style_settings_form['SubmittingSpinner'])) echo '<span class="bsp-spinner"></span>';
	//then finish button
	echo '</button>';
}
	
	
function bsp_load_spinner_reply() {
	global $bsp_style_settings_form;
	//preload spinner so it is ready - CSS hides this
	echo '<div id="bsp-spinner-load"></div>';
	//add button - hidden by CSS
	echo '<button type="submit" id="bsp_reply_submit" name="bbp_reply_submit" class="button submit">';
	//leave as is if field is blanked (user may just want spinner)
	$value = (!empty($bsp_style_settings_form['SubmittingSubmitting']) ? $bsp_style_settings_form['SubmittingSubmitting'] : '');
	echo $value;
	//then add spinner if activated
	if (!empty ( $bsp_style_settings_form['SubmittingSpinner'])) echo '<span class="bsp-spinner"></span>';
	//then finish button
	echo '</button>';
}
	


	
///////////////////////////// REPLY SUBSCRIBED
//Add reply subscribed
function bsp_default_reply_subscribed() {
	
	// Default value
        $topic_subscribed = true;

        // Get _POST data IE is this a first post of a topic?
        if ( bbp_is_topic_form_post_request() && isset( $_POST['bbp_topic_subscription'] ) ) {
                $topic_subscribed = (bool) filter_var( $_POST['bbp_topic_subscription'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH );

        // Get edit data IE either the author or someone else is editing a topic or reply
        } elseif ( bbp_is_topic_edit() || bbp_is_reply_edit() ) {
                $post_author = (int) bbp_get_global_post_field( 'post_author', 'raw' );
                $topic_subscribed = bbp_is_user_subscribed( $post_author, bbp_get_topic_id() );

        // Get current status
        } elseif ( bbp_is_single_topic() ) {
                //the user is writing a new reply ?
                $topic_subscribed = true;
        //the next line is what it used to say instead of true
        //bbp_is_user_subscribed( bbp_get_current_user_id(), bbp_get_topic_id() );
        }

        // Get checked output
        $checked = checked( $topic_subscribed, true, false );

        // Filter & return
        return apply_filters( 'bsp_default_reply_subscribed', $checked, $topic_subscribed );
}
	
if (!empty ($bsp_style_settings_form ['NotifyActivate'] )) {
        add_filter ('bbp_get_form_topic_subscribed', 'bsp_default_reply_subscribed');
}

////////////////////////////// ADD FORUM ID column to admin
if (!function_exists ('rpg_ID_column_add')) {
        add_action("manage_edit-forum_columns", 'bsp_column_add');
        add_filter("manage_forum_posts_custom_column", 'bsp_column_value', 10, 3);
}

function bsp_column_add($columns) {
	$new = array();
        foreach($columns as $key => $title) {
        if ($key=='bbp_forum_topic_count') // Put the forum ID column before the Topics column
                $new['bsp_id'] = 'Forum ID';
                $new[$key] = $title;
        }
        return $new;
}
	
function bsp_column_value($column_name, $id) {
        if ($column_name == 'bsp_id') echo $id;
}
			
			
			
///////////////////////////REVISIONS

add_filter( 'bbp_get_reply_revisions', 'bsp_trim_revision_log', 20, 1 );
add_filter( 'bbp_get_topic_revisions', 'bsp_trim_revision_log', 20, 1 );

// Only return one entry for revision log otherwise it gets cluttered
function bsp_trim_revision_log( $r='' ) {
	global $bsp_style_settings_t;
	//if not set up or 'all' then just return
	$rev = (!empty($bsp_style_settings_t['Revisionsrevisions']) ? $bsp_style_settings_t['Revisionsrevisions'] : 'all' );
	if ($rev== 'all') return $r;
        //if 0, then return none
	if ($rev == 'none') return;
	else {
                //show only the last n revisions
                $arr = array_slice($r, -$rev);
                return $arr;
        }
}
 


///////////////////////////PROFILE
// take out profile links for all or non logged in, or just let users see their own
//annoyingly bbPress puts a filter on bbp_get_author_link using bbp_suppress_private_author_link so you cannot apply filters to the first !!
//so we have to remove the filter AND then let the suppress run after in the return line



//logged in users only
if (!empty ($bsp_profile['profile']) && ($bsp_profile['profile'] == 1)) {
	bsp_profile_filters();
	//and make all @mentions unclickable for non logged in
	if (!is_user_logged_in()) bsp_remove_mentions_clickable();
}

//Users own profile only 
if (!empty ($bsp_profile['profile']) && ($bsp_profile['profile'] == 2)) {
	bsp_profile_filters();
	//and make all @mentions unclickable
	bsp_remove_mentions_clickable();
}
	
//no profile for all users
if (!empty ($bsp_profile['profile']) && ($bsp_profile['profile'] == 3)) {
	bsp_profile_filters();
	//and make all @mentions unclickable
	bsp_remove_mentions_clickable();
}

function bsp_profile_filters() {
	//take out the bbPress filters, and run against mine.
        remove_filter( 'bbp_get_author_link', 'bbp_suppress_private_author_link', 10, 2 );
        remove_filter( 'bbp_get_topic_author_link', 'bbp_suppress_private_author_link', 10, 2 );
        remove_filter( 'bbp_get_reply_author_link', 'bbp_suppress_private_author_link', 10, 2 );
        add_filter( 'bbp_get_author_link', 'bsp_no_profile', 10, 2 );
        add_filter( 'bbp_get_topic_author_link', 'bsp_no_profile', 10, 2 );
        add_filter( 'bbp_get_reply_author_link', 'bsp_no_profile', 10, 2 );
}

function bsp_no_profile ($author_link, $r ) {
	global $bsp_profile;
	//keymasters can see all
	$current_user = wp_get_current_user()->ID;
	if ( bbp_is_user_keymaster($current_user)) return $author_link;
	//and check if moderators are allowed to see
	$role = bbp_get_user_role( $current_user );
	if ($role == 'bbp_moderator' && (!empty ($bsp_profile['moderator'])) ) return $author_link;
	//just logged in
	if ($bsp_profile['profile'] == 1 && (!is_user_logged_in())) {
                $author_link = strip_tags ($author_link, '<img><br><div>' );
	}
	//just own profile
	elseif ($bsp_profile['profile'] == 2) {
		//next line needed for bbp_get_topic_author_link 
		if (empty ($r['post_id']) ) $r['post_id'] = 0;
		$current_profile = get_post_field( 'post_author', $r['post_id'] );
		$current_user = wp_get_current_user()->ID;
		//if not current user...
		if ($current_profile != $current_user) {
			$author_link = strip_tags ($author_link, '<img><br><div>' );
                }
	}
	// no users see..
	elseif ($bsp_profile['profile'] == 3) {
                $author_link = strip_tags ($author_link, '<img><br><div>' );
	}
	//then call the suppress function to add it back
	return bbp_suppress_private_author_link( $author_link, $r) ;
	
}


function bsp_remove_mentions_clickable() {
	//keymasters can see all
	$current_user = wp_get_current_user()->ID;
	if ( bbp_is_user_keymaster($current_user)) return;
	//bbPress automatically adds a profile link to @mentions - this removes it
	remove_filter( 'bbp_make_clickable', 'bbp_make_mentions_clickable', 8 );
}


//now do some code that works out if url is one we want to filter out
function bsp_supress_profile_pages() {
	global $bsp_profile;
	$current_user = wp_get_current_user()->ID;
	$test = false;
        //if only logged in set $test
        if ($bsp_profile['profile'] == 1 && is_user_logged_in() ) $test = true;
        //if only users own profile
        if ($bsp_profile['profile'] == 2 && is_user_logged_in() ) {
                //see if username is in the url - ie matches
                $current_url = $_SERVER['REQUEST_URI'];
                $current_user_id = wp_get_current_user()->ID;
                $current_username = bbp_get_user_nicename( $current_user_id );
                if (strpos($current_url, $current_username)==true) $test=true;
        }
        //if turn off all profiles then set in all cases...
        if ($bsp_profile['profile'] == 3 ) $test = false;
        //then set true for keymaster
        if ( bbp_is_user_keymaster($current_user)) $test = true;
        //and check if moderators are allowed to see
        $role = bbp_get_user_role( $current_user );
        if ($role == 'bbp_moderator' && (!empty ($bsp_profile['moderator'])) )	$test = true;

        if ( $test == false && ( bbp_is_favorites() || bbp_is_subscriptions() || bbp_is_single_user_topics() || bbp_is_single_user_replies() || bbp_is_single_user_edit() || bbp_is_single_user_profile() ) )
        {
                $redirect_url = (!empty($bsp_profile['profile-redirect']) ? $bsp_profile['profile-redirect'] : site_url());
                header( 'Location: ' . $redirect_url );	
                die();			
        }
}
		

global $bsp_profile;
if (!empty ($bsp_profile['profile'] ) ) {
        if ($bsp_profile['profile'] == 1 || $bsp_profile['profile'] == 2 || $bsp_profile['profile'] == 3 ){
                add_action('wp','bsp_supress_profile_pages');
        } 
}	


///////////////////////////// Add thumbnail support

if (!empty ($bsp_forum_display ['thumbnail'])) {
//Add featured image box, and custom sizes
add_action('do_meta_boxes', 'bsp_add_featured_image_boxes'); 
//saves the data
add_action( 'save_post', 'bsp_forum_save_meta', 1, 2 );
//displays the thumbnail
//if below thumbnail
$forum_location =(!empty($bsp_forum_display['forum_descriptionlocation']) ? $bsp_forum_display['forum_descriptionlocation'] : '');
	if (empty ($forum_location )) {
		add_action ('bbp_theme_before_forum_title' , 'bsp_forum_display_thumbnail');
		add_action ('bbp_theme_after_forum_title' , 'bsp_forum_display_thumbnail_end1');
	}
	if ($forum_location == 1) {
		add_action ('bbp_theme_before_forum_title' , 'bsp_forum_display_thumbnail');
		add_action ('bbp_theme_before_forum_title' , 'bsp_forum_display_thumbnail_start');
		add_action ('bbp_theme_after_forum_title' , 'bsp_forum_display_description_middle');
		add_action ('bbp_theme_after_forum_description' , 'bsp_forum_display_thumbnail_end2');
		//add_filter ('bbp_list_forums' , 'bsp_sub_forum_image', 10 , 2);
	}
}


function bsp_sub_forum_image( $output , $r ) {

	// Define used variables
	$sub_forums = $topic_count = $reply_count = $counts = '';
	$i = 0;
	$count = array();

	// Loop through forums and create a list
	$sub_forums = bbp_forum_get_subforums( $r['forum_id'] );
	if ( !empty( $sub_forums ) ) {

		// Total count (for separator)
		$total_subs = count( $sub_forums );
		foreach ( $sub_forums as $sub_forum ) {
			$i++; // Separator count

			// Get forum details
			$count = array();
			$show_sep = $total_subs > $i ? $r['separator'] : '';
			$permalink = bbp_get_forum_permalink( $sub_forum->ID );
			$title = bbp_get_forum_title( $sub_forum->ID );
			//added image
			$image = bsp_get_forum_display_thumbnail ($sub_forum->ID);
			// Show topic count
			if ( !empty( $r['show_topic_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
				$count['topic'] = bbp_get_forum_topic_count( $sub_forum->ID );
			}

			// Show reply count
			if ( !empty( $r['show_reply_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
				$count['reply'] = bbp_get_forum_reply_count( $sub_forum->ID );
			}

			// Counts to show
			if ( !empty( $count ) ) {
				$counts = $r['count_before'] . implode( $r['count_sep'], $count ) . $r['count_after'];
			}

			// Build this sub forums link
			$output .= $r['link_before'] .$image. '<a href="' . esc_url( $permalink ) . '" class="bbp-forum-link">' . $title . $counts . '</a>' . $show_sep . $r['link_after'];
		}

		// Output the list
		echo apply_filters( 'bsp_sub_forum_image', $r['before'] . $output . $r['after'], $r );
	}
}

function bsp_sub_forum_imagea ($args, $forum_id = '') {
	//$forum_id = $args['forum_id'];
	$image = bsp_get_forum_display_thumbnail ($forum_id);
	if (!empty ($image)) 
	$args['link_before'] = '<li class="bbp-forum">'.$image.'</li></ul>';
	return $args;
}

function bsp_forum_display_thumbnail(){
	echo bsp_get_forum_display_thumbnail();
}

function bsp_get_forum_display_thumbnail ($forum_id=0) {
	global $bsp_forum_display;
	if (empty ($forum_id)) 
		$id = get_the_ID();
	else 	$id = $forum_id;
	$output = '';
	if ( has_post_thumbnail() ) {
		
		$meta = get_post_meta( $id , 'bsp_forum_thumbnail', true );
		$metawidth = get_post_meta( $id, 'bsp_forum_thumbnailwidth', true );
		$metaheight = get_post_meta( $id, 'bsp_forum_thumbnailheight', true );
		$item = (!empty($meta) ? $meta : '');
		//default to thumbnail
		$itemsize = 'thumbnail';
		// What size?
                switch ( $item) {

                        case 1 :
                                $itemsize = 'thumbnail';
                                break;

                        case 2 :
                                $itemsize = 'medium';
                                break;

                        case 3 :
                                $itemsize = 'large';
                                break;

                        case 4 :
                                $itemsize = 'full';
                                break;

                        case 5 :
                                $itemsize = 'custom';
                                break;	
                }

		$itemwidth = (!empty($metawidth) ? $metawidth : '');
		$itemheight = (!empty($metaheight) ? $metaheight : '');
		$output = '';
		if ($itemsize == 'custom') {
			$itemsize = 'array ('.$itemwidth. ', '.$itemheight.')';
			//start by creating a div we can style
			$output .= '<div class = "bsp_thumbnail">';
			$output .= '<a href="'.get_permalink().'">';
			$output .= get_the_post_thumbnail( $id, array ($itemwidth, $itemheight));
			$output .= '</a>';
                }
		else {
                        $output .= '<div class = "bsp_thumbnail">';
                        $output .= '<a href="'.get_permalink().'">';
                        $output .= get_the_post_thumbnail( $id, $itemsize );
                        $output .= '</a>'; 
		}

	}
        rclog ($output);
        return $output;
}

function bsp_forum_display_thumbnail_end1() {
	if ( has_post_thumbnail() ) {
                //close the div
                echo '</div>';
	}
}

function bsp_forum_display_thumbnail_end2() {
	if ( has_post_thumbnail() ) {
                //close the div
                echo '</li></ul></div>';
	}
}

function bsp_forum_display_description_middle() {
	//ends and starts a new li
	if ( has_post_thumbnail() ) {
                echo '</li><li style="padding-left: 10px;">';
	}
}

function bsp_forum_display_thumbnail_start() {
	if ( has_post_thumbnail() ) {
                echo '<ul><li>';
	}
}

//add @mentions
if (!empty( $bsp_style_settings_t['mentionsactivate'] ) ) {
	$priority = (!empty ($bsp_style_settings_t['mentions_priority'] ) ? $bsp_style_settings_t['mentions_priority'] : 10 );
	add_action ('bbp_theme_after_reply_author_details', 'bsp_mentions', $priority ,1);
	add_action ('bbp_theme_after_topic_author_details', 'bsp_mentions', $priority ,1); 
}

function bsp_mentions() {
	$user_id = bbp_get_reply_author_id();
	$user_info = get_userdata( $user_id ); 
	//echo '<div class="bsp-mentions">@'. $user_info->user_login .'</div>';
	if (!empty ($user_info)) echo '<div class="bsp-mentions">@'. $user_info->user_nicename .'</div>';
}


//adds sub forum description
/**********forum list create vertical list************/
function bsp_list_forums( $args = '' ) {

	// Define used variables
	$output = $sub_forums = $topic_count = $reply_count = $counts = '';
	$i = 0;
	$count = array();

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'before' => '<ul class="bbp-forums-list">',
		'after' => '</ul>',
		'link_before' => '<li class="bbp-forum">',
		'link_after' => '</li>',
		'count_before' => ' (',
		'count_after' => ')',
		'count_sep' => ', ',
		'separator' => ', ',
		'forum_id' => '',
		'show_topic_count' => true,
		'show_reply_count' => true,
	), 'bsp_list_forums' );

	// Loop through forums and create a list
	$sub_forums = bbp_forum_get_subforums( $r['forum_id'] );
	if ( !empty( $sub_forums ) ) {

		// Total count (for separator)
		$total_subs = count( $sub_forums );
		foreach ( $sub_forums as $sub_forum ) {
			$i++; // Separator count

			// Get forum details
			$count = array();
			$show_sep = $total_subs > $i ? $r['separator'] : '';
			$permalink = bbp_get_forum_permalink( $sub_forum->ID );
			$title = bbp_get_forum_title( $sub_forum->ID );
			

			// Show topic count
			if ( !empty( $r['show_topic_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
				$count['topic'] = bbp_get_forum_topic_count( $sub_forum->ID );
			}

			// Show reply count
			if ( !empty( $r['show_reply_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
				$count['reply'] = bbp_get_forum_reply_count( $sub_forum->ID );
			}

			// Counts to show
			if ( !empty( $count ) ) {
				$counts = $r['count_before'] . implode( $r['count_sep'], $count ) . $r['count_after'];
			}
			
			// Build this sub forums link
			//AMENDED to add sub forum descriptions
			$content = bbp_get_forum_content($sub_forum->ID);
			$output .= $r['link_before'] . '<a href="' . esc_url( $permalink ) . '" class="bbp-forum-link">' . $title . $counts . '</a>' . $r['separator'] .$content . $r['separator'] .$r['link_after'];
		}

		// Output the list
		echo apply_filters( 'bsp_list_forums', $r['before'] . $output . $r['after'], $r );
	}
}



if ( !empty ($bsp_forum_display['add_subforum_list_description'] )) {
	//check if private groups exists, and if so it takes priority to ensure correct filtering and use of PG settings to enable
	if( ! function_exists('private_groups_list_forums') ) add_filter('bbp_list_forums', 'bsp_list_forums' );
}



///////////////////////////// TOPIC ORDER

function bsp_date_topic_order( $args ) {
	global $bsp_topic_order;
	//default order
	if (!empty($bsp_topic_order['Default_OrderActivate'])) {
		$orderby = $bsp_topic_order['Default_OrderOrder'];
		switch ($orderby) {
			case "1":
				//latest reply
				$args['orderby']='meta_value';
				$args['meta_key']='_bbp_last_active_time';
				break;
			case "2":
				//topic date
				$args['orderby']='date';
				break;
			case "3":
				//title
				$args['orderby']='title';
				break;
			case "4":
				//author
				$args['orderby']='author';
				break;
		}
		$order = $bsp_topic_order['Default_OrderAsc'];
		switch ($order) {
			case "1":
				$args['order']='ASC';
				break;
			case "2":
				$args['order']='DESC';
				break;
		}
	}
	if (!empty($bsp_topic_order['Forum_Order1Activate']) ) {
		$include = explode (",",($bsp_topic_order['Forum_Order1Forums']));
		if (in_array (bbp_get_forum_id(), $include ) ) {
                        $orderby = $bsp_topic_order['Forum_Order1Order'];
                        switch ($orderby) {
                                case "1":
                                        //latest reply
                                        $args['orderby']='meta_value';
                                        $args['meta_key']='_bbp_last_active_time';
                                        break;
                                case "2":
                                        //topic date
                                        $args['orderby']='date';
                                        break;
                                case "3":
                                        //title
                                        $args['orderby']='title';
                                        break;
                                case "4":
                                        //author
                                        $args['orderby']='author';
                                        break;
                        }
                        $order = $bsp_topic_order['Forum_Order1Asc'];
                        switch ($order) {
                                case "1":
                                        $args['order']='ASC';
                                        break;
                                case "2":
                                        $args['order']='DESC';
                                        break;
                        }
		}
	}
	return $args;
}

//add filter if either apply
if (!empty($bsp_topic_order['Default_OrderActivate']) || !empty($bsp_topic_order['Forum_Order1Activate'])) {
	add_filter('bbp_before_has_topics_parse_args','bsp_date_topic_order');
}

//add topic rules
if (!empty($bsp_style_settings_form['topic_posting_rulesactivate_for_topics'])) add_action( 'bbp_theme_before_topic_form_notices', 'bsp_topic_rules') ;

if (!empty($bsp_style_settings_form['topic_posting_rulesactivate_for_replies'])) add_action( 'bbp_theme_before_reply_form_notices', 'bsp_reply_rules') ;

function bsp_topic_rules() {
        global $bsp_style_settings_form; 
        $content = $bsp_style_settings_form['topic_rules_text'];
        echo '<div class="bsp-topic-rules">'.$content.'</div>';
}

function bsp_reply_rules() {
        global $bsp_style_settings_form; 
        $content = $bsp_style_settings_form['reply_rules_text'];
        echo '<div class="bsp-topic-rules">'.$content.'</div>';
}

//This function changes the text wherever it is quoted
function bsp_change_text2( $translated_text, $text, $domain ) {
	global $bsp_style_settings_ti;
	if ( $text == 'Oh bother! No topics were found here!' || $text == 'Oh, bother! No topics were found here.') {
                $translated_text = $bsp_style_settings_ti['empty_forum'];
	}
	return $translated_text;
}

if (!empty ($bsp_style_settings_ti['empty_forum'] )) add_filter( 'gettext', 'bsp_change_text2', 20, 3 );

//This function changes the text wherever it is quoted
function bsp_change_text4( $translated_text, $text, $domain ) {
	global $bsp_style_settings_f;
	if ( $text == 'Oh bother! No forums were found here!' || $text == 'Oh, bother! No forums were found here.' ) {
                $translated_text = $bsp_style_settings_f['empty_index'];
	}
	return $translated_text;
}

if (!empty ($bsp_style_settings_f['empty_index'] )) add_filter( 'gettext', 'bsp_change_text4', 20, 3 );

//This function changes the text wherever it is quoted
function bsp_change_text3( $translated_text, $text, $domain ) {
	global $bsp_style_settings_search;
	if ( $text == 'Oh bother! No search results were found here!' || $text == 'Oh, bother! No search results were found here.' ) {
                $translated_text = $bsp_style_settings_search['empty_search'];
	}
	return $translated_text;
}

if (!empty ($bsp_style_settings_search['empty_search'] )) add_filter( 'gettext', 'bsp_change_text3', 20, 3 );


//make sure scheduled stickies don't show


//change forum order if activated
if ( !empty ($bsp_forum_order['Orderactivate'] )) {
	if ($bsp_forum_order['Orderorder'] == 2) add_filter('bbp_before_has_forums_parse_args', 'bsp_forum_order_by_freshness');
	if ($bsp_forum_order['Orderorder'] == 3) add_filter('bbp_before_has_forums_parse_args', 'bsp_forum_order_by_newness_newtop');
	if ($bsp_forum_order['Orderorder'] == 4) add_filter('bbp_before_has_forums_parse_args', 'bsp_forum_order_by_newness_oldtop');
	
}

function bsp_forum_order_by_freshness ($args) {
	$args['meta_key'] = '_bbp_last_active_time';
	$args['orderby'] = 'meta_value';
	$args['order'] = 'DESC';
	return $args;
}

function bsp_forum_order_by_newness_oldtop ($args) {
	$args['orderby'] = 'date';
	$args['order'] = 'ASC';
	return $args;
}

function bsp_forum_order_by_newness_newtop ($args) {
	$args['orderby'] = 'date';
	$args['order'] = 'DESC';
	return $args;
}

if (!empty($bsp_style_settings_form['Show_editorsactivate'])) {
	add_filter( 'bbp_after_get_the_content_parse_args', 'bsp_enable_visual_editor' );
	add_filter( 'bbp_get_tiny_mce_plugins', 'bsp_tinymce_paste_plain_text' );
}

//editor bbpress
function bsp_enable_visual_editor( $args = array() ) {
	global $bsp_style_settings_form;
	$args['tinymce'] = true;
	$args = apply_filters('bsp_enable_visual_editor' , $args) ;
	//if full visual
		if ($bsp_style_settings_form['Show_editorsactivate'] == 3 || $bsp_style_settings_form['Show_editorsactivate'] == 4) {
		$args ['teeny'] = false ;
		}
	//if not showing editor as well
		if ($bsp_style_settings_form['Show_editorsactivate'] == 1 || $bsp_style_settings_form['Show_editorsactivate'] == 3) $args['quicktags'] = false;
return $args;
}


//clean html when copy and paste into forum
function bsp_tinymce_paste_plain_text( $plugins = array() ) {
        $plugins[] = 'paste';
        return $plugins;
}


//////////This is from Pascal's toolkit, so only execute if needed (ie pascals function doesn't exist)
// Blocked users should NOT get an email to subscribed topics
function bsp_fltr_get_forum_subscribers( $user_ids ) {
        if (!empty( $user_ids ) ) {
                $new_user_ids = array();
                foreach ($user_ids as $uid) {
                        if (bbp_get_user_role($uid) != bbp_get_blocked_role()) {
                                $new_user_ids[] = $uid;
                        }
                }
                return $new_user_ids;
        } else {
                return $user_ids;
        } 
} 
// add the filter

if(!function_exists('bbptoolkit_fltr_get_forum_subscribers')){
        add_filter( 'bbp_forum_subscription_user_ids', 'bsp_fltr_get_forum_subscribers', 10, 1 );
}

//not in toolkit - so add
add_filter( 'bbp_topic_subscription_user_ids', 'bsp_fltr_get_forum_subscribers', 10, 1 );


if (!empty($bsp_style_settings_t['anon_emailShow'])) {
	$current_user = wp_get_current_user()->ID;
	if ( bbp_is_user_keymaster($current_user)) {
		add_action ('bbp_theme_after_reply_author_admin_details', 'bsp_add_email');
	}
}

function bsp_add_email() {
	$email = get_post_meta ( bbp_get_reply_id() , '_bbp_anonymous_email', true );
	if (!empty ($email))
		echo '<p>'.$email;
}

//decide what author links to show on replies
//show nothing
if (!empty($bsp_style_settings_t['hide_avatar']) && !empty($bsp_style_settings_t['hide_name'])) {
	add_filter ( 'bbp_get_reply_author_link' , 'bsp_hide_author_link', 10 , 2 );
}
elseif (!empty($bsp_style_settings_t['hide_avatar'])) {
	add_filter( 'bbp_before_get_reply_author_link_parse_args', 'bsp_hide_avatar' );
}
elseif (!empty($bsp_style_settings_t['hide_name'])) {
	add_filter( 'bbp_before_get_reply_author_link_parse_args', 'bsp_hide_name' );
	
}


function bsp_hide_author_link ($author_link, $r ) {
	$author_link = '';
 return $author_link;	
}

function bsp_hide_avatar ($args = array()) {
        $args['type'] = 'name';
        return $args;	
}

function bsp_hide_name ($args = array()) {
        $args['type'] = 'avatar';
        return $args;	
}


//This function changes search text 

if (!empty ($bsp_style_settings_search['search_text'] )) add_filter( 'gettext', 'bsp_change_search', 20, 3 );

function bsp_change_search( $translated_text, $text, $domain ) {
 global $bsp_style_settings_search;
	if ( $text == 'Search' && $domain == 'bbpress') {
                $translated_text = $bsp_style_settings_search['search_text'];
	}
	return $translated_text;
}



//Number of forums per page
if (!empty ($bsp_forum_display['number_forums'] )) add_filter ('bbp_before_has_forums_parse_args', 'bsp_number_of_forums');#

function bsp_number_of_forums ($args) {
	global $bsp_forum_display;
	$args['posts_per_page'] = $bsp_forum_display['number_forums'];
        return $args;
}

//allow user to close their own topics
if (!empty($bsp_style_settings_t['participant_close_topic'])) 
        add_action ('init' , 'bsp_add_close_topic_capability');
	
	
function bsp_add_close_topic_capability(){
	if (is_user_logged_in()) {
                add_action ( "bbp_theme_before_reply_admin_links", "bsp_topic_close_link" );
	}
}

function bsp_topic_close_link( $args = '' ) {
	echo bsp_get_topic_close_link( $args );
}

function bsp_get_topic_close_link( $args = '' ) {

        // Parse arguments against default values
        $r = bbp_parse_args( $args, array(
                'id' => 0,
                'link_before' => '',
                'link_after' => '',
                'sep' => ' | ',
                'close_text' => _x( 'Close', 'Topic Status', 'bbpress' ),
                'open_text' => _x( 'Open', 'Topic Status', 'bbpress' )
        ), 'get_topic_close_link' );

        $topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

        if ( empty( $topic ))
                return;

        //if not a topic, then return
        if (bbp_get_reply_id() != bbp_get_topic_id( (int) $r['id'] ) ) return ;

        //if participant and not their own topic, then return
        $role = bbp_get_user_role( get_current_user_id() );
        if ($role == 'bbp_participant' ) {

                //find out if it is their own topic
                $author_id = bbp_get_topic_author_id( $topic->ID);
                if ($author_id == get_current_user_id()) {

                        $display = bbp_is_topic_open( $topic->ID ) ? $r['close_text'] : $r['open_text'];
                        $uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_close', 'topic_id' => $topic->ID ) );
                        $uri = wp_nonce_url( $uri, 'close-topic_' . $topic->ID );
                        $retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-close-link">' . $display . '</a>' . $r['link_after'];
                        $retval = '<span class="bbp-admin-links">'.$r['sep'].$retval.'</span>';

                        return apply_filters( 'bbp_get_topic_close_link', $retval, $r );
                }
        }
        else return;
}
	

//ability to add login and register to You must be logged in to create new topics.
//This function changes the text wherever it is quoted
function bsp_change_must_be_text( $translated_text, $text, $domain ) {
 global $bsp_style_settings_ti;
	if ( $text == 'You must be logged in to create new topics.' ) {
		$translated_text = 'You must be logged in to create new topics.';
		if (!empty ($bsp_style_settings_ti['must_be_logged_in'] )) $translated_text = $bsp_style_settings_ti['must_be_logged_in'];
		//find out what version we are in
		$version = get_option('bsp_bbpress_version', '2.5'); //set to 2.5 as default if option not set
		//set up tabel start in case needed
		$table = '<table><tr><td>';
		if (!empty ($bsp_style_settings_ti['add_login_login'])) {
			//put in a table if version 2.5
			if (substr($version, 0, 3) == '2.5') {
                                $url = (!empty ($bsp_style_settings_ti['login_page_url']) ? $bsp_style_settings_ti['login_page_url'] : site_url().'/wp-login.php');
                                $check=1;
                                $desc = (!empty ($bsp_style_settings_ti['login_page_page']) ? $bsp_style_settings_ti['login_page_page'] : 'Login');
                                $translated_text = $table.$translated_text.'</td><td><a href="'.$url.'"> '.$desc.'</a></td>';
			}
			elseif (substr($version, 0, 3) == '2.6') {
				//do nothing as login is displayed after this in the new template
			}
			
		}
		if (!empty ($bsp_style_settings_ti['add_register_register'])) {
			$desc = (!empty ($bsp_style_settings_ti['register_page_page']) ? $bsp_style_settings_ti['register_page_page'] : 'Register');
			$url = (!empty ($bsp_style_settings_ti['register_page_url']) ? $bsp_style_settings_ti['register_page_url'] : site_url().'/wp-login.php');
			//put in a table if version 2.5
			if (substr($version, 0, 3) == '2.5') {
				if (empty($check)) {
					$check=1;
					$translated_text = $table.$translated_text.'</td><td><a href="'.$url.'"> '.$desc.'</a></td>';
				}
				else {
					$table= $translated_text.'<td>';
					$translated_text = $table.'<a href="'.$url.'"> '.$desc.'</a></td>';
				}
			}
			elseif (substr($version, 0, 3) == '2.6') {
				//we don't do anything here, BUT we add the register to the login form that 2.6 displays using do_action( 'login_form' );
			}
			
		}
	}
	if (!empty($check )) $translated_text.= '</tr></table>';
	return $translated_text;
}

if (!empty ($bsp_style_settings_ti['must_be_logged_in'] ) || !empty ($bsp_style_settings_ti['add_login_login'] ) || !empty ($bsp_style_settings_t['add_register_register'] ) ) {
	add_filter( 'gettext', 'bsp_change_must_be_text', 20, 3 );
}

//if register is set and on version 2.6, then add register to the login form - 2.6 check is done in the function
//check topics index and topic/reply display tabs
if (!empty ($bsp_style_settings_ti['add_register_register']) || !empty ($bsp_style_settings_ti['add_register_register'] ) ) {
        add_action ('login_form' , 'bsp_add_register' );
}

function bsp_add_register() {
	global $bsp_style_settings_ti;
	$version = get_option('bsp_bbpress_version', '2.5'); //set to 2.5 as default if option not set
	if (substr($version, 0, 3) == '2.6') {
		$desc = (!empty ($bsp_style_settings_ti['register_page_page']) ? $bsp_style_settings_ti['register_page_page'] : 'Register');
		$url = (!empty ($bsp_style_settings_ti['register_page_url']) ? $bsp_style_settings_ti['register_page_url'] : site_url().'/wp-login.php');
		echo '<div class="bbp-submit-wrapper"><a class="button bsp-register" href="'.$url.'"> '.$desc.'</a></div>';
	}
}

//ability to add login and register to You must be logged in to reply to topics.
//This function changes the text wherever it is quoted
function bsp_change_must_be_text2( $translated_text, $text, $domain ) {
 global $bsp_style_settings_t;
	if ( $text == 'You must be logged in to reply to this topic.' ) {
		$translated_text = 'You must be logged in to reply to this topic.';
		if (!empty ($bsp_style_settings_t['must_be_logged_in'] )) $translated_text = $bsp_style_settings_t['must_be_logged_in'];
		//find out what version we are in
		$version = get_option('bsp_bbpress_version', '2.5'); //set to 2.5 as default if option not set
		//set up tabel start in case needed
		$table = '<table><tr><td>';
		if (!empty ($bsp_style_settings_t['add_login_login'])) {
			//put in a table if version 2.5
			if (substr($version, 0, 3) == '2.5') {
				$url = (!empty ($bsp_style_settings_t['login_page_url']) ? $bsp_style_settings_t['login_page_url'] : site_url().'/wp-login.php');
				$check=1;
				$desc = (!empty ($bsp_style_settings_t['login_page_page']) ? $bsp_style_settings_t['login_page_page'] : 'Login');
				$translated_text = $table.$translated_text.'</td><td><a href="'.$url.'"> '.$desc.'</a></td>';
			}
			elseif (substr($version, 0, 3) == '2.6') {
				//do nothing as login is displayed after this in the new template
			}
			
		}
		if (!empty ($bsp_style_settings_t['add_register_register'])) {
			//put in a table if version 2.5
			if (substr($version, 0, 3) == '2.5') {
				$desc = (!empty ($bsp_style_settings_t['register_page_page']) ? $bsp_style_settings_t['register_page_page'] : 'Register');
				$url = (!empty ($bsp_style_settings_t['register_page_url']) ? $bsp_style_settings_t['register_page_url'] : site_url().'/wp-login.php');
				if (empty($check)) {
					$check=1;
					$translated_text = $table.$translated_text.'</td><td><a href="'.$url.'"> '.$desc.'</a></td>';
					}
				else {
					$table= $translated_text.'<td>';
					$translated_text = $table.'<a href="'.$url.'"> '.$desc.'</a></td>';
				}
			}
			elseif (substr($version, 0, 3) == '2.6') {
				//we don't do anything here, BUT we add the register to the login form that 2.6 displays using do_action( 'login_form' );
			}
		}
	}
	if (!empty($check )) $translated_text.= '</tr></table>';
	return $translated_text;
}

if (!empty ($bsp_style_settings_t['must_be_logged_in'] ) || !empty ($bsp_style_settings_t['add_login_login'] ) || !empty ($bsp_style_settings_t['add_register_register'] ) ) {
	add_filter( 'gettext', 'bsp_change_must_be_text2', 20, 3 );
	
}


if (!empty ($bsp_style_settings_form['redirect_topicActivate'] )) add_filter( 'bbp_new_topic_redirect_to', 'bsp_forum_redirect_topic' , 10 ,3 );
if (!empty ($bsp_style_settings_form['redirect_replyActivate'] )) add_filter( 'bbp_new_reply_redirect_to', 'bsp_forum_redirect_reply' , 10 ,3 );



function bsp_forum_redirect_topic ($redirect_url, $redirect_to, $topic_id ){
	$forum_id = bbp_get_topic_forum_id($topic_id);
	$redirect_url = bbp_get_forum_permalink( $forum_id );
        return $redirect_url;
}

function bsp_forum_redirect_reply ($redirect_url, $redirect_to, $reply_id ){
	$forum_id = bbp_get_reply_forum_id($reply_id);
	$redirect_url = bbp_get_forum_permalink( $forum_id );
        return $redirect_url;
}

if (!empty ($bsp_breadcrumb['repeat'] )) {
        add_action( 'bbp_template_after_forums_index' , 'bsp_add_breadcrumb'); 
        add_action( 'bbp_template_after_single_forum' , 'bsp_add_breadcrumb'); 
        add_action( 'bbp_template_after_single_topic' , 'bsp_add_breadcrumb'); 
}

function bsp_add_breadcrumb() {
	bbp_breadcrumb(); 
	
}


//************auto login (and see line 3643 for moderation redirect)

//add private forum check
global $bsp_style_settings_email;
if (!empty ($bsp_style_settings_email['email_activate_auto_login'] )) {
        add_action( 'bbp_template_redirect', 'bsp_access_if_logged_out');
}

function bsp_access_if_logged_out(){
	global $bsp_style_settings_email;
	//bail if user is already logged in
	if (is_user_logged_in()) return ;
	$topic_slug = get_option( '_bbp_topic_slug');
	//quick check if we need to do this function, if no topic slug, then quit
	if (strpos($_SERVER['REQUEST_URI'], $topic_slug) == FALSE) return;
	//and bail if already in a redirect (eg using [bbp_login] shortcode)
	if (strpos($_SERVER['REQUEST_URI'], '?redirect_to') == TRUE) return;
	$links = explode('/', $_SERVER['REQUEST_URI']);
	//so now we need to work out which parts to use.
	//so we might have a /forum/ slug, BUT we might also have another preceding part, so we go through the $link until we find the topic slug
	foreach ($links as $link=>$name) {
		if ($name == $topic_slug) {
			//then the topic name will be the next slug, so add 1 to the $link number and quit
			$link++ ;
			break ;
		}
	}
	$post = bsp_get_page_by_slug( $links[$link], OBJECT, bbp_get_topic_post_type() );
	if (!empty ($post)) $topic_id = $post->ID;
	//now we need to check if the topic belongs to a private forum, so can't be seen
	if (!empty ($topic_id)) {
		$forum_id = bbp_get_topic_forum_id($topic_id);
				//if forum is private...and WordPress login
				if (bbp_get_forum_visibility( $forum_id ) == 'private' && empty($bsp_style_settings_email['email_private_login_type']) ) {
                        $redirect = site_url() . '/wp-login.php?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] );
						if (!empty($_REQUEST['bsp_reply_id']) && is_numeric($_REQUEST['bsp_reply_id'])) {
							$redirect.='%23post-'.$_REQUEST['bsp_reply_id'] ;
						}
						wp_redirect( $redirect );
                        exit;
                }
                elseif (bbp_get_forum_visibility( $forum_id ) == 'private' && !empty($bsp_style_settings_email['email_private_login_type']) && !empty($bsp_style_settings_email['email_bbpress_login_url']) ) {
                        $redirect = $bsp_style_settings_email['email_bbpress_login_url'];
                        $redirect = $redirect.'?redirect_to=' .urlencode( $_SERVER['REQUEST_URI'] );
						if (!empty($_REQUEST['bsp_reply_id']) && is_numeric($_REQUEST['bsp_reply_id'])) {
							$redirect.='%23post-'.$_REQUEST['bsp_reply_id'] ;
						}
                        wp_redirect( $redirect );
                        exit;
                }
	}
			
}


function bsp_login_redirect_url ($redirect='') {
	//$redirect is always blank as nothing is set in the sending function
	//strip the site_url from the saved database login path
	global $bsp_style_settings_email;
	$login_url_page = '' ;
	if (!empty ($bsp_style_settings_email['email_bbpress_login_url'])) $login_url_page = str_replace (site_url() , '', $bsp_style_settings_email['email_bbpress_login_url'] );
	//take out login page from request_url
	$redirect = str_replace ($login_url_page , '', $_SERVER['REQUEST_URI'] );
	//strip the ?redirect...
	$redirect = str_replace ('?redirect_to=' , '', $redirect );
	//alter the %2F to / - shouldn;t need to do this, but it does work without it !!
	$redirect = str_replace ('%2F', '/', $redirect );
	$redirect = str_replace ('//', '/', $redirect );
	//if we've come from an auto login, with a reply, we need to change other code
	//change ?
	$redirect = str_replace ('%3F', '?', $redirect );
	//change =
	$redirect = str_replace ('%3D', '=', $redirect );
	//change #
	$redirect = str_replace ('%23', '#', $redirect );
	
	return $redirect;
}



//change auto reply message to add ?bsp_reply
//add filter if we are doing auto_login, the function is in functions.php around line 2500
global $bsp_style_settings_email ;
if (!empty ($bsp_style_settings_email['email_activate_auto_login'] )) {
	//set at priority 5, so it runs before any login redirect set by bsp_login_redirect
	add_filter ('bbp_user_login_redirect_to' , 'bsp_login_redirect_url', 5 , 1);
	if (!empty ($bsp_style_settings_email['email_activate_email_content'])) {
		add_filter('bsp_reply_message_url' , 'bsp_auto_login_reply_redirect', 10 , 2) ;
	}
	else{
		add_filter ('bbp_subscription_mail_message', 'bsp_add_reply_to_url' , 10 , 3 ) ;
	}
}

function bsp_add_reply_to_url ($message, $reply_id, $topic_id) {
	//we have to repeat code from bbpress bbp_notify_topic_subscribers function as this is quicker
	$reply_author_name = bbp_get_reply_author_display_name( $reply_id );
	$reply_author_name = wp_specialchars_decode( strip_tags( $reply_author_name ), ENT_QUOTES );
	$reply_content     = wp_specialchars_decode( strip_tags( bbp_get_reply_content( $reply_id ) ), ENT_QUOTES );
	$reply_url         = bbp_get_reply_url( $reply_id );
	$reply_url = str_replace ('#post' , '?bsp_reply_id='.$reply_id.'#post', $reply_url ) ;

	// For plugins to filter messages per reply/topic/user
	$message = sprintf( esc_html__( '%1$s wrote:

%2$s

Post Link: %3$s

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic to unsubscribe from these emails.', 'bbpress' ),

		$reply_author_name,
		$reply_content,
		$reply_url
	);
	
return $message ;
	
	
}

function bsp_auto_login_reply_redirect ($reply_url, $reply_id) {
	//reply_url starts as topics/topic-name/#post-33603 so we need to replace #post with the ?bsp_reply_id etc. before it
	$reply_url = str_replace ('#post' , '?bsp_reply_id='.$reply_id.'#post', $reply_url ) ;
return $reply_url ;
}

function bsp_get_page_by_slug($page_slug, $output = OBJECT, $post_type = 'page', $status = 'publish' ) { 
        global $wpdb; 
        $page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $page_slug, $post_type) ); 
        if ( $page ) 
                return get_post($page, $output); 
        return null; 
 }
 
 
 
///********allow users to trash own topics/replies


if (!empty($bsp_style_settings_t['participant_trash_topic']) && is_user_logged_in()) { 
	/*Customize the BBPress roles to allow Participants to trash topics*/
	add_filter( 'bbp_get_caps_for_role', 'bsp_add_topic_caps_filter', 10, 2 );
	/*then only allow participants to trash their own topics*/
	add_filter( 'bbp_map_topic_meta_caps', 'bsp_tweak_trash_topic_caps', 11, 4 );
	//then redirect to the forum after trashing topic
	add_action('bbp_template_redirect', 'bsp_trash_topic_check', 8);
}

if (!empty($bsp_style_settings_t['participant_trash_reply']) && is_user_logged_in()) { 
	/*Customize the BBPress roles to allow Participants to trash replies*/
	add_filter( 'bbp_get_caps_for_role', 'bsp_add_reply_caps_filter', 10, 2 );
	/*then only allow participants to trash their own replies*/
	add_filter( 'bbp_map_reply_meta_caps', 'bsp_tweak_trash_reply_caps', 11, 4 );
}

function bsp_add_topic_caps_filter( $caps, $role ){
        // Only filter for roles we are interested in!
        if( $role == bbp_get_participant_role() ) {
		//only change delete topics 
		$caps ['delete_topics']= true;
	}
	return $caps;
}

function bsp_add_reply_caps_filter( $caps, $role ){
        // Only filter for roles we are interested in!
        if( $role == bbp_get_participant_role() ) {
		//only change delete topics 
		$caps ['delete_replies']= true;
	}
	return $caps;
}

function bsp_tweak_trash_topic_caps( $caps, $cap, $user_id, $args ){
	// apply only to delete_topic
	if ( $cap == "delete_topic" ){
		// Get the post
		$_post = get_post( $args[0] );
		if ( !empty( $_post ) ) {

                        // Get caps for post type object
                        $post_type = get_post_type_object( $_post->post_type );

                        // Add 'do_not_allow' cap if user is spam or deleted
                        if ( bbp_is_user_inactive( $user_id ) ) {
                                $caps[] = 'do_not_allow';

                        // Moderators can always edit forum content
                        } elseif ( user_can( $user_id, 'moderate' ) ) {
                                $caps[] = 'moderate';

                        // User is author so allow edit if not in admin
                        } elseif ( user_can( $user_id, 'participate' ) && ( (int) $user_id === (int) $_post->post_author ) ) {
                                $caps = array();

                        // Unknown so do not allow
                        } else {
                                $caps[] = 'do_not_allow';
                        }
		}
	}	
	// return the capabilities
	return $caps;
}


function bsp_tweak_trash_reply_caps( $caps, $cap, $user_id, $args ){
	// apply only to delete_reply
	if ( $cap == "delete_reply" ){
		// Get the post
		$_post = get_post( $args[0] );
		if ( !empty( $_post ) ) {

			// Get caps for post type object
			$post_type = get_post_type_object( $_post->post_type );
			
			// Add 'do_not_allow' cap if user is spam or deleted
			if ( bbp_is_user_inactive( $user_id ) ) {
				$caps[] = 'do_not_allow';

			// Moderators can always edit forum content
			} elseif ( user_can( $user_id, 'moderate' ) ) {
				$caps[] = 'moderate';

			// User is author so allow edit if not in admin
                        } elseif ( user_can( $user_id, 'participate' ) && ( (int) $user_id === (int) $_post->post_author ) ) {
                                $caps = array();
				
			// Unknown so do not allow
			} else {
				$caps[] = 'do_not_allow';
			}
		}
	}	
	// return the capabilities
	return $caps;
}

//check if topic has been trashed by author and show forum if it has
function bsp_trash_topic_check() {
	$topic_slug = get_option( '_bbp_topic_slug');
	//quick check if we need to do this function, so bail if not a topic
	if (strpos($_SERVER['REQUEST_URI'], $topic_slug) == FALSE) return;
	$forum_slug = bbp_get_root_slug();
	//if check is set (ie we prefix forums with the forum slug) then part 1 will be forum slug and part 2 will be topic slug, if not part 1 will be topic slug
	$check = bbp_include_root_slug();
	$link = explode('/', $_SERVER['REQUEST_URI']);
	//next we need the topic id (post id) of the topic so we need to check if it is a topic and if so, find the topic id
	if ($check && $link[1] == $forum_slug && $link[2] == $topic_slug ) {
		$post = bsp_get_page_by_slug( $link[3], OBJECT, 'topic' );
		if (!empty ($post)) $login_check=1;
        } 
	elseif (empty($check) && $link[1] === $topic_slug) {
		$post = bsp_get_page_by_slug( $link[2], OBJECT, 'topic' );
		if (!empty ($post)) $login_check=1;
	}
	//now we need to check if the topic has been trashed by author
	if (!empty ($login_check) && $post->post_status == 'trash' && $post->post_author == get_current_user_id() ) {
		$topic_id = $post->ID;
		//then redirect to the forum we came from
		$forum = bbp_get_forum_permalink (bbp_get_topic_forum_id ( $topic_id ));
		wp_redirect ($forum);
		exit;
	}
	else return;
}

//amend the WordPress toolbar edit profile to go to bbPress profile edit
if (!empty ($bsp_login['toolbar_profile'] )) {
        add_action('wp_before_admin_bar_render', 'bsp_admin_bar_remove_wp_profile', 0);
        add_action('admin_bar_menu', 'bsp_add_bbp_profile', 999);
}



function bsp_admin_bar_remove_wp_profile() {
        global $wp_admin_bar;
	$wp_admin_bar->remove_menu('edit-profile');
}



function bsp_add_bbp_profile($wp_admin_bar) {
	global $bsp_login;
	if (!empty($bsp_login['edit profileMenu Item Description'] )) {
		$edit_profile=$bsp_login['edit profileMenu Item Description'];
	}
	else $edit_profile = __('Edit Profile', 'bbp-style-pack');
	$current_user = wp_get_current_user();
	$user=$current_user->user_nicename ;
	$user_slug = get_option( '_bbp_user_slug' );
	if (get_option( '_bbp_include_root' ) == true ) {	
		$forum_slug = get_option( '_bbp_root_slug' );
		$slug = $forum_slug.'/'.$user_slug.'/';
	}
	else {
		$slug=$user_slug . '/';
	}
			
	$profilelink = '/' .$slug. $user . '/edit';
			
	$wp_admin_bar->add_node( array(
		'parent' => 'user-actions',
		'id'		=> 'bbp-edit-profile',
		'title' => $edit_profile ,
		'href' => $profilelink,
	) );

}

//code that does translations

if (!empty ($bsp_style_settings_translation['activate'] )) {

        $count= (!empty ($bsp_style_settings_translation['count']) ? $bsp_style_settings_translation['count'] : 1);
        $translations = array();
	for ($i = 1; $i <= $count; $i++) {
		$name="translation".$i;
		$itema="bsp_style_settings_translation[".$name."a]";
		$itemb="bsp_style_settings_translation[".$name."b]";
		$valuea = (!empty ($bsp_style_settings_translation[$name.'a']) ? $bsp_style_settings_translation[$name.'a'] : '' );
		$valueb = (!empty ($bsp_style_settings_translation[$name.'b']) ? $bsp_style_settings_translation[$name.'b'] : '' );
		$translations [$valuea] = $valueb;
	
	}

        update_option ('bsp_translations' , $translations);

        add_filter( 'gettext', 'bsp_translations', 20 , 3 );
        add_filter( 'ngettext', 'bsp_translations' , 20 , 3 );
        //and filter time since translations
        add_filter ('bbp_get_time_since' , 'bsp_translations2' , 20 , 1);
}


function bsp_translations( $translated, $text, $domain ) {
	if ($domain != 'bbpress' && $domain != 'bbp-style-pack') return $translated;
	$bsp_translations = get_option( 'bsp_translations' );
	if (!empty ($bsp_translations)) {
                $translated = str_replace( array_keys($bsp_translations), $bsp_translations, $translated );
	}
        return $translated;
}

function bsp_translations2( $translated) {
	$bsp_translations = get_option( 'bsp_translations' );
	if (!empty ($bsp_translations)) {
                $translated = str_replace( array_keys($bsp_translations), $bsp_translations, $translated );
	}
        return $translated;
}

//not used
function bsp_get_time_since_translate ($output, $older_date, $newer_date) {
	$bsp_translations = get_option( 'bsp_translations' );
	if(strpos($output, 'months') !== false){
	}
	if(strpos($output, 'month') !== false){
	}
	if(strpos($output, 'weeks') !== false){
	}
	if(strpos($output, 'week') !== false){
	}
	if(strpos($output, 'days') !== false){
	}
	if(strpos($output, 'day') !== false){
	}
	if(strpos($output, 'hours') !== false){
	}
	if(strpos($output, 'minutes') !== false){
	}
	if(strpos($output, 'minute') !== false){
	}
	if(strpos($output, 'seconds') !== false){
	}
	if(strpos($output, 'second') !== false){
	}
}


//placeholder text
if (!empty ($bsp_style_settings_form['placeholder_reply'] )) {
	add_filter( 'bbp_get_the_content', 'bsp_placeholder_reply', 10, 3);
}

function bsp_placeholder_reply ($output, $args, $post_content) {
	global $bsp_style_settings_form;
	$placeholder_text = $bsp_style_settings_form['placeholder_reply'];
        if ($args['context'] == 'reply' && $post_content == '') $output=str_replace('></textarea>', 'placeholder="'.$placeholder_text.'" ></textarea>', $output);
        return $output;
}

if (!empty ($bsp_style_settings_form['placeholder_topic'] )) {
	add_filter( 'bbp_get_the_content', 'bsp_placeholder_topic', 10, 3);
}

function bsp_placeholder_topic ($output, $args, $post_content) {
	global $bsp_style_settings_form;
	$placeholder_text = $bsp_style_settings_form['placeholder_topic'];
        if ($args['context'] == 'topic' && $post_content == '') $output=str_replace('></textarea>', 'placeholder="'.$placeholder_text.'" ></textarea>', $output);
        return $output;
}


//login failed functions

//there is a pluggable function 'wp_authenticate' that we overwrite in bbp-style-pack.php as well to get the two empty fields that get captured by this code

if (!empty($bsp_login_fail['activate_failed_login'])) {
	// wp_login_failed is the actioin hook in 'wp_authenticate' which we have a new version of in bbp-style-pack.php as it is a pluggable function
	add_action( 'wp_login_failed', 'bsp_login_failed' , 10 , 2); // hook failed login
	//'login_form' is the bbPress action hook in forum-user-login.php which letss us add extra fields to the login form
	//this adds the referring url as a hidden filed, which we then pick up for the redirect
	add_action ('login_form' , 'bsp_login_form_url');
	//this displays the failed message	
	add_action ('login_form' , 'bsp_login_failed_message');
}


function bsp_login_failed( $username, $error ) {
	//$errors = reset($error);  reset  is now deprecated !!  The reset() function moves the internal pointer to the first element of the array.
	$failed = ''; //just in case nothing is passed !
	if (!empty($errors['invalid_username'])) $failed = 'invalid_username'; 
	if (!empty($errors['incorrect_password'])) $failed = 'incorrect_password'; 
	if (!empty($errors['empty_username'])) $failed = 'empty_username'; 
	if (!empty($errors['empty_password'])) $failed = 'empty_password'; 
	if (!empty($errors['empty_password']) && !empty($errors['empty_username'] )) $failed = 'nothing_entered'; 
	if (!empty ($_POST['bsp_login_referrer'])) $referrer = filter_var( $_POST['bsp_login_referrer'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ); // where did the post submission come from?
	// if there's a valid referrer, and it's not the default log-in screen
        if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
                //then strip any old referrer requests
                if (strpos( $referrer, '?') !== false) {
                        $referrer = substr ($referrer, 0 , strpos ($referrer , '?'));
                }
                add_query_arg( 'login', $failed );
                wp_redirect( $referrer . '?login='.$failed ); // let's append some information (login=failed) to the URL for this plugin to use
                //wp_redirect( $referrer);
                exit;
        }
}

// Get the full URL of the current page
function bsp_current_page_url(){
        $page_url = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
                $page_url .= 's';
        }
        return $page_url.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}



function bsp_login_form_url() {
	$url = bsp_current_page_url();
	echo '<input type="hidden" name="bsp_login_referrer" value="'.$url.'"/>';
}


function bsp_login_failed_message() {
	global $bsp_login_fail;
	if (isset($_REQUEST['login'])) {
		//set a default in case !
		$text = 'Unknown Error, please try again';
		$error = $_REQUEST['login'];
		$referrer = $_SERVER['QUERY_STRING'];
		if (strpos($referrer, 'loggedout=true') !== false) $logout=true;
		//if they repeat the form wrongly we only want the last error, so work out that one
		if (strpos($error, '?') !== false ) {
                        $error = substr ($_REQUEST['login'], 0 , strpos ($_REQUEST['login'] , '?'));
		}
		if (empty($logout)) {
			if ($error == 'invalid_username' ) $text= (!empty ($bsp_login_fail ['fail_invalid_username']) ? $bsp_login_fail ['fail_invalid_username'] :'ERROR: Unknown username. Check again or try your email address');
			if ($error == 'incorrect_password' ) $text= (!empty ($bsp_login_fail ['fail_incorrect_password']) ? $bsp_login_fail ['fail_incorrect_password'] : 'ERROR: The password you entered was incorrect');
			if ($error == 'empty_username' ) $text= (!empty ($bsp_login_fail ['fail_empty_username']) ? $bsp_login_fail ['fail_empty_username'] :'ERROR: The username field was empty');
			if ($error == 'empty_password' ) $text= (!empty ($bsp_login_fail ['fail_empty_password']) ? $bsp_login_fail ['fail_empty_password'] :'ERROR: The password field was empty');
			if ($error == 'nothing_entered' ) $text= (!empty ($bsp_login_fail ['fail_nothing_entered']) ? $bsp_login_fail ['fail_nothing_entered'] :'ERROR: Nothing was entered');
			echo '<div id="bsp-login-error">'.$text.'</div>';
		}
	}
}


add_action ('bbp_author_metabox' , 'bsp_author_help_text' );

function bsp_author_help_text() {
	_e('To amend author - type ID,' , 'bbp-style-pack' );
        echo '<br/>';
        _e('or start to type username or email.' , 'bbp-style-pack' );
}

//topic preview functions

if (!empty ($bsp_style_settings_topic_preview['activate'])) {
        add_action ('bbp_theme_before_topic_title' , 'bsp_topic_preview');
        add_action ('bbp_theme_after_topic_title' , 'bsp_topic_preview2');
}

function bsp_topic_preview() {
	echo '<div class="bsp-preview">';
}

function bsp_topic_preview2() {
	global $bsp_style_settings_topic_preview;
	echo '<span class="bsp-previewtext">';
	$post_id = bbp_get_reply_id();
	$content = strip_tags(get_post_field('post_content', $post_id));
	if (!empty($bsp_style_settings_topic_preview['previewchars'])) {
		$chars = $bsp_style_settings_topic_preview['previewchars'];
		$extract = mb_substr($content,0, $chars );
		if (mb_strlen($content ) > mb_strlen($extract) ) $extract.='...';
		$content = $extract;
	}
	echo $content;
	echo '</span>';
	//and set for mobile screens if set
	if (!empty ($bsp_style_settings_topic_preview['previewmscreen'])) {
		echo '<span class="bsp-previewtextm">';
		if (!empty($bsp_style_settings_topic_preview['previewmchars'])) {
			$chars = $bsp_style_settings_topic_preview['previewmchars'];
			$extract = mb_substr($content,0, $chars );
			if (mb_strlen($content ) > mb_strlen($extract) ) $extract.='...';
			$content = $extract;
		}
		echo $content;
                echo '</span>';
                echo '</div>';
	}
}

//template error notices 

if (!empty ($bsp_style_settings_form['errormsgActivate'])) {
        add_action ('bbp_template_before_single_forum' , 'bsp_template_error_notices');
        add_action ('bbp_template_before_single_topic' , 'bsp_template_error_notices');
}

function bsp_template_error_notices() {
	global $bsp_style_settings_form;
	// Bail if no notices or errors
	if ( ! bbp_has_errors() ) {
		return;
	}

	// Define local variable(s)
	$errors = $messages = array();

	// Get bbPress
	$bbp = bbpress();

	// Loop through notices
	foreach ( $bbp->errors->get_error_codes() as $code ) {

		// Get notice severity
		$severity = $bbp->errors->get_error_data( $code );

		// Loop through notices and separate errors from messages
		foreach ( $bbp->errors->get_error_messages( $code ) as $error ) {
			if ( 'message' === $severity ) {
				$messages[] = $error;
			} else {
				$errors[] = $error;
			}
		}
	}

	// Display errors 
	if ( ! empty( $errors ) ) : ?>
		<div class="bbp-template-notice error" role="alert" tabindex="-1">
			<ul>
				<li>
					<?php echo implode( "</li>\n<li>", $errors ); ?>
					<?php if (!empty($bsp_style_settings_form['errormsgActivateLink'])) { 
                                                $message = (!empty($bsp_style_settings_form['errormsgMessage']) ? $bsp_style_settings_form['errormsgMessage'] : 'Click here to correct'); 
                                                echo '<a href="#new-post">'.$message.'</a>';
					} ?>
				</li>
			</ul>
		</div>

	<?php endif;

}



//anonymous posting function

add_filter ('bbp_before_filter_anonymous_post_data_parse_args' , 'bsp_anonymous_posting' ) ;

function bsp_anonymous_posting ($args) {
	global $bsp_style_settings_form ;
	if (!empty ($bsp_style_settings_form['no_anon_nameActivate'])) {
		$value = (!empty($bsp_style_settings_form['no_anon_namename']) ? $bsp_style_settings_form['no_anon_namename']  : 'Anonymous') ;
		$args ['bbp_anonymous_name'] = $value ;
	}
	if (!empty ($bsp_style_settings_form['no_anon_nameActivate'])) {
		//set to xx@xx.com to pass email format filters and then use post submit to remove
		$args ['bbp_anonymous_email'] = 'xx@xx.com' ;
	}
return $args ;
}

add_action( 'bbp_new_topic_post_extras', 'bsp_check_anon_email') ;
add_action( 'bbp_new_reply_post_extras', 'bsp_check_anon_email') ;

function bsp_check_anon_email ($post_id) {
	//blank email address if anon
	$value = get_post_meta ($post_id, '_bbp_anonymous_email' , true) ;
	if ($value == 'xx@xx.com') update_post_meta ($post_id, '_bbp_anonymous_email', '') ;
}



//Add register topics and replies to main site search if activated in forums index as long as private groups not running !
if (!empty ($bsp_style_settings_f['wordpress_searchActivate']) && !function_exists('bbp_private_groups_init') ){
        add_filter( 'bbp_register_topic_post_type', 'bsp_bbp_topic_cpt_search' );
        add_filter( 'bbp_register_reply_post_type', 'bsp_bbp_reply_cpt_search' );
}

function bsp_bbp_topic_cpt_search( $topic_search ) {
	$topic_search['exclude_from_search'] = false;
	return $topic_search;
}

function bsp_bbp_reply_cpt_search( $reply_search ) {
	$reply_search['exclude_from_search'] = false;
	return $reply_search;
}


//change avatar sizes
//freshness
if (!empty ($bsp_style_settings_f['Freshness AvatarSize'])) {
	add_filter ('bbp_after_get_author_link_parse_args' , 'bsp_change_freshness_avatar_size', 20 ,1 );
}

function bsp_change_freshness_avatar_size ($args) {
	global $bsp_style_settings_f;
	$args['size'] = $bsp_style_settings_f['Freshness AvatarSize'];
        return $args;
}



function bsp_target_blank_link_content($content, $id=0){
	global $bsp_style_settings_t;
	//add in target for all cases
	$content = str_replace('<a', '<a target="_blank"', $content);
	
	//if we have more/less set, then we need to take out blank the more/less functions
	if (!empty ( $bsp_style_settings_t['more_less'])) {
		$content = str_replace('<a target="_blank" href="javascript:BspMore', '<a href="javascript:BspMore', $content);
		$content = str_replace('<a target="_blank" href="javascript:BspLess', '<a href="javascript:BspLess', $content);
	}
	
	//now take out for home url if set (value=2)
	if ($bsp_style_settings_t['window_links']== 2) {
		$uri = get_home_url();
		$search = '<a target="_blank" href="'.$uri;
		$replace = '<a href="'.$uri;
		$content = str_replace($search, $replace, $content);
	}
return $content;
}


if (!empty ($bsp_style_settings_t['window_links'])) {
	add_filter( 'bbp_get_topic_content', 'bsp_target_blank_link_content', 60, 2);
	add_filter( 'bbp_get_reply_content', 'bsp_target_blank_link_content', 60, 2 );
}

//called from form topic and form reply;
function bsp_topic_tags ($topic_id=0) { 	
	//get list of tags
	$tags = get_tags(array(
		'taxonomy' => 'topic-tag',
		'orderby' => 'name',
		'hide_empty' => false,
	));
	//get any tags for this topic
	$tag_list = get_the_term_list( $topic_id, bbp_get_topic_tag_tax_id(),'',', ');
	//add commas to start and finish so we can search it
	$tag_list = ','.$tag_list.',';
	
	// Start an output buffer
        ob_start();
	?>
	<label for="bbp_topic_tags">
	<?php esc_html_e( 'Topic Tags:', 'bbpress' ); ?></label><br />
	</label>
						
	<select multiple="multiple" name="bbp_topic_tags[]"
		style="min-width:300px;"
		class="bsp-topic-tag-multiple"
		data-placeholder="<?php _e('Choose Topic Tags', 'bsp-style-pack'); ?>">
		<?php
		//echo '<p>'.$tag_list;
		foreach ($tags as $tag=>$name) {	
			$item = $name->name;
			$selected = '';
			if (strpos($tag_list, $item) !== false) {
				$selected = 'selected';
			}
			?>
			<option value="<?php echo $item; ?>" <?php echo $selected; ?>><?php echo $item; ?></option>
                <?php
                }
                ?>
	</select>
	<?php
	// Output the current buffer
        echo ob_get_clean();
						
}

if (!empty ($bsp_style_settings_form['topic_tag_list'])) {
	add_action ('bbp_new_topic_post_extras' , 'bsp_topic_tags_process' );
	add_action ('bbp_edit_topic_post_extras' , 'bsp_topic_tags_process' );
	add_action( 'bbp_new_reply_post_extras', 'bsp_reply_tags_process' );
	add_action( 'bbp_edit_reply_post_extras', 'bsp_reply_tags_process' );
}


//handle topic tags via new/edit topic submit
function bsp_topic_tags_process ($topic_id) {
	if ( bbp_allow_topic_tags() && ! empty( $_POST['bbp_topic_tags'] ) ) {
		
                foreach( $_POST['bbp_topic_tags'] as $key => $val ) {
                        $terms[] = filter_var( $val, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH );
                }
                
		// Add topic tag ID as main key. Strip indexes and use only tag values for values array.
		$terms = array( bbp_get_topic_tag_tax_id() => array_values( $terms ) );
		
		$topic_data = array(
                        'ID' => $topic_id,
                        'tax_input' => $terms,
		);
		$topic_id = wp_update_post( $topic_data );

	}
}


//handle topic tags via new/edit reply submit
function bsp_reply_tags_process ($reply_id) {
	if ( bbp_allow_topic_tags() && ! empty( $_POST['bbp_topic_tags'] ) ) {
		$topic_id = bbp_get_reply_topic_id ($reply_id);
		
                foreach( $_POST['bbp_topic_tags'] as $key => $val ) {
                        $terms[] = filter_var( $val, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH );
                }
                
		// Add topic tag ID as main key
		$terms = array( bbp_get_topic_tag_tax_id() => array_values( $terms ) );
                
		$topic_data = array(
                        'ID' => $topic_id,
                        'tax_input' => $terms,
		);
		$topic_id = wp_update_post( $topic_data );
	}
}


if (!empty ( $bsp_style_settings_t['more_less'])) add_filter ('bbp_get_reply_content', 'bsp_limit_display', 10 , 2);

function bsp_limit_display ($content, $reply_id) {
	global $bsp_style_settings_t;
	$length = $bsp_style_settings_t['more_less_length'];
	$more = (!empty($bsp_style_settings_t['more_text']) ? $bsp_style_settings_t['more_text'] : 'More...');
	$less = (!empty($bsp_style_settings_t['less_text']) ? $bsp_style_settings_t['less_text'] : 'Less...');
	
	//ignore if not long
	if (strlen ($content)<$length) return $content;
	//create shortened content
        $content_short = '<div id="bsp-short'.$reply_id.'">';
        $content_short.= substr($content,0, $length);
        $content_short.= '</div>';
        $div_s = substr_count($content_short, '<div>');
        $div_f = substr_count($content_short, '</div');
        if ($div_s != $div_f) $content_short.= '</div>';
		
        $content_long = '<div id="bsp-long'.$reply_id.'" style="display:none;">';
        $content_long.= $content;
        $content_long.= '</div>';
        $click1 = '<div id = "bsp-more-button'.$reply_id.
        '"><a href="javascript:BspMore(\'bsp-short'.$reply_id.
        '\' , \'bsp-long'.$reply_id.
        '\' , \'bsp-more-button'.$reply_id.
        '\' , \'bsp-less-button'.$reply_id.
        '\')">'.$more.'</a></div>';
        $click2 = '<div id = "bsp-less-button'.$reply_id.'" style="display:none;">
        <a href="javascript:BspLess(
        \'bsp-short'.$reply_id.'\' ,
        \'bsp-long'.$reply_id.'\' ,
        \'bsp-more-button'.$reply_id.'\',
        \'bsp-less-button'.$reply_id.'\'
        )"
        >'.$less.'</a></div>';
		return $content_short.$content_long.$click1.$click2;
}


// check custom CSS location for validitiy
function bsp_custom_file_location_errors( $type ) {
        global $bsp_css_location;
        switch( strtolower( $type ) ) {
                case 'css' : 
                        $active = ( !empty ( $bsp_css_location ['activate css location'] ) ? true : false );
                        $location = isset( $bsp_css_location ['location'] ) ? $bsp_css_location ['location'] : '';
                        break;
                case 'js' :
                        $active = ( !empty ( $bsp_css_location ['activate js location'] ) ? true : false );
                        $location = isset( $bsp_css_location ['js_location'] ) ? $bsp_css_location ['js_location'] : '';
                        break;
                default :
                        $location = '';
                        break;
        }
        
	
 
        $messages = array();

        if ( $active ) {
                // if it starts with '/' 
                if (0 === strpos($location, '/')) {
                        $messages[] = __( 'WARNING - location should not start with a \'/\' !!', 'bbp-style-pack');
                }
                // if it doesn't end with a '/' 
                if (substr( $location, strlen($location)-1, strlen($location) ) !== '/') {
                        $messages[] = __( 'WARNING - location is missing a \'/\' at the end !!', 'bbp-style-pack');
                }
                // does the dir exist
                if ( ! is_dir( ABSPATH . $location ) ) {
                        $messages[] = __( 'WARNING - location directory does not exist !!', 'bbp-style-pack');
                }
                // is the dir writable
                if ( ! is_writable( ABSPATH . $location ) ) {
                        $messages[] = __( 'WARNING - location directory does not have write permissions !!', 'bbp-style-pack');
                }
                // is the dir readable
                if ( ! is_readable( ABSPATH . $location ) ) {
                        $messages[] = __( 'WARNING - location directory does not have read permissions !!', 'bbp-style-pack');
                }
        }

        return $messages;

}


// check custom files for validitiy
function bsp_custom_file_errors( $file ) {

        $messages = array();

        if ( $file ) {

                // if it ends with a '/' 
                if (substr( $file, strlen($file)-1, strlen($file) ) == '/') {
                        $messages[] = __( 'WARNING - file should not end with a \'/\' !!', 'bbp-style-pack');
                }
                // does the file exist
                if ( ! is_file( $file ) ) {
                        $messages[] = __( 'WARNING - file does not exist !!', 'bbp-style-pack');
                }
                // is the file readable
                if ( ! is_readable( $file ) ) {
                        $messages[] = __( 'WARNING - file does not have read permissions !!', 'bbp-style-pack');
                }
        }

        return $messages;

}


// convert timestamps to server local time zome human friendly date/times
function bsp_timetamp_to_human_local_dtg( $timestamp, $format = 'Y-m-d H:i:s' ) {

        // local site timezone object
        $tz = wp_timezone();

        // adjust it and format it for the site's timezone 
        $tz_date = new DateTime( date( "Y-m-d H:i:s", $timestamp ), $tz);
        $tz_date_offset = $tz->getOffset($tz_date);
        return date( $format, ( $timestamp + $tz_date_offset ) );
}


// get human readable filesize of a file (like debug log)
function bsp_get_friendly_filesize( $file, $dec = 2 ) {
        // make sure we got a valid file
        if ( empty( bsp_custom_file_errors( $file ) ) ) {
                $bytes = filesize( $file );
                $size = array( 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
                $factor = floor( ( strlen( $bytes ) - 1 ) / 3 );
                if ( $factor == 0 ) $dec = 0;

                return sprintf( "%.{$dec}f %s", $bytes / ( 1024 ** $factor ), $size[$factor] );
        }
        return '0B';
}

// count lines in a file (like debug log file)
// done one line at a time to prevent out of memory errors
function bsp_count_lines_in_file( $file ) {
        // set count as zero
        $line_count = 0;
        // if there are no errors with this file
        if ( empty( bsp_custom_file_errors( $file ) ) ) {
                // open the debug log file
                $fp = fopen( $file, "r" );
                // make sure we have contents
                if ( $fp ) {
                        // loop until the end of the file
                        while( ! feof( $fp ) ) {
                                // load one single line
                                $line = fgets( $fp );
                                // make sure it's not a blank line
                                if ( $line ) {
                                        // increment counter
                                        $line_count++;
                                }
                        }
                }
                // close the file
                fclose( $fp );
        }
        // return line count
        return number_format($line_count);
}


// parse out debug log file lines
// done one line at a time to prevent out of memory errors
function bsp_parse_debug_log( $file, $line_number, $error_levels ) {
        // set entries as empty array
        $entries = array();
        foreach ( $error_levels as $error_level ) {
                $entries[$error_level] = array();
        }
        // if there are no errors with this file, and we got error levels to get
        if ( empty( bsp_custom_file_errors( $file ) ) && ! empty( $error_levels ) ) {
                // open the debug log file
                $fp = fopen( $file, "r" );
                // make sure we have contents
                if ( $fp ) {
                        // loop until the end of the file
                        while( ! feof( $fp ) ) {
                                // load one single line
                                $line = fgets( $fp );
                                // make sure it's not a blank line
                                if ( $line ) {
                                        // loop through the error levels we're reporting
                                        foreach ( $error_levels as $error_level ) {
                                                // does this line contain the specified error level?
                                                if ( strpos( $line, $error_level ) !== false ) {
                                                        // push line onto an array
                                                        array_push( $entries[$error_level], $line );
                                                        // shift the array to only contain the last X lines specified
                                                        if ( count( $entries[$error_level] ) > $line_number ) {
                                                                array_shift( $entries[$error_level] );
                                                        }
                                                }
                                        }
                                }
                        }
                }
                // close the file
                fclose( $fp );
        }
        // return array of last X lines for each error_level as a combined array
        // filter empty entries
        return array_filter( $entries );
}


// parse out BSP release versions and notes
function bsp_parse_changelog() {
        // parse out changelog info from the readme.txt file
        $lines = file( plugin_dir_path( __DIR__ ) . 'readme.txt' );
        //echo plugin_dir_path( __DIR__ ) . 'readme.txt';
        $changelog = false;
        $releases = array();
        foreach( $lines as $line ) {

                // only after we hit the changelog line, then we want to process the changelog releases
                if ( $changelog ) {
                        // if the line is a release version
                        if ( strpos( trim( $line ), '=' ) === 0 ) {
                                $release = trim( str_replace( '=', '', $line ) );
                                //$releases[] = $release;
                        }
                        // if the line is a release note
                        if ( strpos( trim( $line ), '*' ) === 0 ) {
                                $note = trim( str_replace( '*', '', $line ) );
                                $releases[$release][] = $note;
                        }
                }

                // don't do anything until we hit the chagelog line
                // intentionally handled after the release processing above so that this line is not included in the array
                if ( strpos( trim( $line ), '== Changelog ==' ) === 0) $changelog = true;
        }

        return $releases;
}


// helper function for formatting topic/reply/post counts
if ( ! function_exists( 'tc_formatted_number' ) ) {
        function tc_formatted_number( $number ) {

                // set values for bbp-topic-count
                if ( basename( __FILE__ ) == 'bbp-topic-count.php' ) {
                        global $tc_options;
                        $tc_options_values = $tc_options;
                }

                // set values for bbp-style-pack
                if ( basename( __FILE__ ) == 'bbp-style-pack.php' ) {
                        global $bsp_settings_topic_count;
                        $tc_options_values = $bsp_settings_topic_count;
                }

                $sep = ( ! empty( $tc_options_values['sep'] ) ? $tc_options_values['sep'] : 0 );

                if ( $sep == 1 ) {
                        return number_format( $number );
                }
                if ( $sep == 2 ) {
                        return number_format( $topic_count, 0, '', ' ' );
                }
                if ( $sep == 3 ) {
                        if ( $number>1000 ) {
                                $number = $number/1000;
                                return number_format( $number, 2 ).'k';
                        }
                }
                if ( $sep == 4 ) {
                        if ( $number>1000 ) {
                                $number = $number/1000;
                                return number_format( $number, 2,",", "," ).'k';
                        }
                }

                // return unformatted number by default if no sep value set
                return number_format( $number );

        }
}

//reply order function
if ( is_array( $bsp_topic_order ) && array_key_exists( 'reply_order', $bsp_topic_order ) ) {
        if ( $bsp_topic_order['reply_order'] == 2 || $bsp_topic_order['reply_order'] == 3 ) {
                add_filter ('bbp_before_has_replies_parse_args', 'bsp_reply_change_order') ;
				add_filter ('bbp_get_reply_position', 'bsp_get_reply_position_reverse', 10 , 3) ;
				
        }
		if ( $bsp_topic_order['reply_order'] == 2 ) {
		 add_filter ('bbp_show_lead_topic', 'bsp_true') ;
		}
}


function bsp_true () {
	return true ;
}

function bsp_reply_change_order ($args) {
	$args['order'] = 'DESC' ;
return $args ;
}

//reverses the reply position for situations where the replies are newest first, and we have more than one page
//This re-does the reply position so that the paginatioin works correctly when a single reply is called, eg in 'latest activity' or the reply number is clicked or in url
//this is based on bbp_get_reply_position_raw
function bsp_get_reply_position_reverse ($reply_position, $reply_id, $topic_id) {
	global $bsp_topic_order ;
	//start by counting the replies
	$reply_count = bbp_get_topic_reply_count( $topic_id, false );
		// Make sure the topic has replies before running another query
		$reply_count = bbp_get_topic_reply_count( $topic_id, false );
		if ( ! empty( $reply_count ) ) {

			// Get reply id's
			$topic_replies = bbp_get_all_child_ids( $topic_id, bbp_get_reply_post_type() );
			if ( ! empty( $topic_replies ) ) {
				//Reverse replies array and search for current reply position
				// The original code has this next line - so we don't do this to get the newest on top order !
				//$topic_replies  = array_reverse( $topic_replies );
				$reply_position = (int) array_search( (string) $reply_id, $topic_replies );

				// Bump the position to compensate for the lead topic post if set
				if ( is_array( $bsp_topic_order ) && array_key_exists( 'reply_order', $bsp_topic_order ) ) {	
					if ( $bsp_topic_order['reply_order'] == 2) $reply_position++;
				}
			}
		}
	return (int) $reply_position;
}


add_action( 'after_setup_theme', 'bsp_admin_notices' );

function bsp_admin_notices () {
	if (!empty ($_REQUEST['page']) && $_REQUEST['page'] == 'bbp-style-pack') {
		remove_all_actions( 'admin_notices' );
	}
}


// function to clear cache any time after files generated or settings values changed
function bsp_clear_cache() {

        /*
         * Purge Plugin Caches
         */
    
        // When plugins have a simple method, add them to the array:
        // ( 'Plugin Name' => 'function_name' ) or 
        // ( 'Plugin Name' => array( 'class_name/object_name' => 'method_name' ) )
        // Concept and starter code borrowed from the WP-Optimize plugin
        $caching_plugins = array(
                'WP Super Cache' => 'wp_cache_clear_cache',
                'W3 Total Cache' => 'w3tc_pgcache_flush',
                'WP Fastest Cache' => 'wpfc_clear_all_cache',
                'WP Rocket' => 'rocket_clean_domain',
                'Cachify' => 'cachify_flush_cache',
                'Comet Cache' => array( 'comet_cache', 'clear' ),
                'SG Optimizer' => 'sg_cachepress_purge_cache',
                'Pantheon' => 'pantheon_wp_clear_edge_all',
                'Zen Cache' => array( 'zencache', 'clear' ),
                'Breeze' => array( 'Breeze_PurgeCache', 'breeze_cache_flush' ),
                'Swift Performance' => array( 'Swift_Performance_Cache', 'clear_all_cache' ),
                'WP Optimize' => 'wpo_cache_flush',
                'AutOptimize' => array( 'autoptimizeCache', 'clearall' ),
        );

        foreach ($caching_plugins as $plugin => $method) {
                if ( is_callable( $method ) ) {
                        call_user_func($method);
                }
        }
        
        // Purge LiteSpeed Cache
        if ( is_callable( array( 'LiteSpeed_Cache_Tags', 'add_purge_tag' ) ) ) {
                LiteSpeed_Cache_Tags::add_purge_tag( '*' );
        }

        // Purge Hyper Cache
        if ( class_exists( 'HyperCache' ) ) {
                do_action( 'autoptimize_action_cachepurged' );
        }
        
        // purge cache enabler
        if ( has_action( 'ce_clear_cache' ) ) {
                do_action( 'ce_clear_cache' );
        }
        
        
        /*
         * Purge Hosting Provider Caches
         */

        // Purge Godaddy Managed WordPress Hosting (Varnish + APC)
        if ( class_exists( 'WPaaS\Plugin' ) ) {
		$url  = empty( $url ) ? home_url() : $url;
		$host = parse_url( $url, PHP_URL_HOST );
		$url  = set_url_scheme( str_replace( $host, WPaas\Plugin::vip(), $url ), 'http');
		if ( function_exists( 'wp_cache_flush' ) ) wp_cache_flush();
		update_option( 'gd_system_last_cache_flush', time() ); // purge apc
		wp_remote_request( esc_url_raw( $url ), array( 'method' => 'BAN', 'blocking' => false, 'headers' => array( 'Host' => $host ) ) );
        }

        // Purge WP Engine
        if ( class_exists( "WpeCommon" ) ) {
                if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
                        WpeCommon::purge_memcached();
                }
                if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
                        WpeCommon::clear_maxcdn_cache();
                }
                if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
                        WpeCommon::purge_varnish_cache();
                }
        }

        // Purge Kinsta
        global $kinsta_cache;
        if ( isset( $kinsta_cache ) && class_exists( '\\Kinsta\\CDN_Enabler' ) ) {
                if ( !empty( $kinsta_cache->kinsta_cache_purge ) && is_callable( array( $kinsta_cache->kinsta_cache_purge, 'purge_complete_caches' ) ) ) {
                        $kinsta_cache->kinsta_cache_purge->purge_complete_caches();
                }
        }

        // Purge Pagely
        if ( class_exists( 'PagelyCachePurge' ) ) {
                $purge_pagely = new PagelyCachePurge();
                if ( is_callable( array( $purge_pagely, 'purgeAll' ) ) ) {
                        $purge_pagely->purgeAll();
                }
        }

        // Purge Pressidum
        if ( defined( 'WP_NINUKIS_WP_NAME' ) && class_exists( 'Ninukis_Plugin' ) && is_callable( array(' Ninukis_Plugin', 'get_instance' ) ) ) {
                $purge_pressidum = Ninukis_Plugin::get_instance();
                if ( is_callable( array( $purge_pressidum, 'purgeAllCaches' ) ) ) {
                        $purge_pressidum->purgeAllCaches();
                }
        }

        // Purge Savvii
        if ( defined( '\Savvii\CacheFlusherPlugin::NAME_DOMAINFLUSH_NOW' ) ) {
                $purge_savvii = new \Savvii\CacheFlusherPlugin();
                if ( is_callable( array( $purge_savvii, 'domainflush' ) ) ) {
                        $purge_savvii->domainflush();
                }
        }
        
        // Purge WP cache
        if ( function_exists( 'wp_cache_flush' ) ) wp_cache_flush();
        
}


//Decide where topic title in a single forum display goes to topic or latest reply etc. 
//only called if $bsp_style_settings_ti['topic_title_link'] is not empty (ie is set to something other than topic)

function bsp_topic_permalink( $topic_id = 0, $redirect_to = '' ) {
	echo esc_url( bsp_get_topic_permalink( $topic_id, $redirect_to ) );
}

function bsp_get_topic_permalink( $topic_id = 0, $redirect_to = '' ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	//set a default just in case !
	$topic_permalink = get_permalink( $topic_id );
	global $bsp_style_settings_unread ;
	global $bsp_style_settings_ti ;
	$value1 = (!empty($bsp_style_settings_ti['topic_title_link']) ? $bsp_style_settings_ti['topic_title_link']  : 0) ;
	//bail if not set
	if (empty ($value1)) return $topic_permalink ;
		//revise what is shown if topics unread was set when choice was made in topics index - ie if unread posts is not active and choice was to send to latest unread or title, send to title etc.
		if (empty ($bsp_style_settings_unread['unread_activate']) && ($value1 == 2)) $value1 = 0 ;
		if (empty ($bsp_style_settings_unread['unread_activate']) && ($value1 == 3)) $value1 = 1 ;
		
		//now send as required
			//send to lastest reply
			if ($value1 == 1) {
				$reply_id = bbp_get_topic_last_reply_id( $topic_id );
				if ( ! empty( $reply_id ) && ( $reply_id !== $topic_id ) ) {
					$topic_permalink = bbp_get_reply_url( $reply_id );
				} 
			}
			//send to the last unread or to topic if empty
			if ($value1 == 2) {
				$latest = bbp_get_topic_last_reply_id( $topic_id ) ;
				$last_id = get_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key_id (), true );
				//if they are the same the user is up to date, so go to the topic
				if ($latest == $last_id) {
					//do nothing as it will return the topic
				}
				elseif (!empty ($last_id)) {
					$topic_permalink = bbp_get_reply_url ($last_id);
				}
				else {
					//do nothing as no replies and it will return the topic
				}
			}				

			//send to the last unread or to last reply if empty
			if ($value1 == 3) {
				$last_id = get_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key_id (), true );
				if (!empty ($last_id))	$topic_permalink = bbp_get_reply_url ($last_id) ;
				else $topic_permalink = bbp_get_topic_last_reply_url ( $topic_id ) ;
			}
			
			
			
return $topic_permalink ;
}
	
	
	
function bsp_topic_title_link ($topic_permalink, $topic_id) {
	global $bsp_style_settings_unread ;
	global $bsp_style_settings_ti ;
	$value1 = (!empty($bsp_style_settings_ti['topic_title_link']) ? $bsp_style_settings_ti['topic_title_link']  : 0) ;
	//bail if not set
	if (empty ($value1)) return $topic_permalink ;
		//revise what is shown if topics unread was set when choice was made in topics index - ie if unread posts is not active and choice was to send to latest unread or title, send to title etc.
		if (empty ($bsp_style_settings_unread['unread_activate']) && ($value1 == 2)) $value1 = 0 ;
		if (empty ($bsp_style_settings_unread['unread_activate']) && ($value1 == 3)) $value1 = 1 ;
		
		//now send as required
			//send to lastest reply
			if ($value1 == 1) {
				$reply_id = bbp_get_topic_last_reply_id( $topic_id );
				if ( ! empty( $reply_id ) && ( $reply_id !== $topic_id ) ) {
					$reply_url = bbp_get_reply_url( $reply_id );
				} else {
					//do nothing as we are going to the topic link 
				}
			}

return $topic_permalink ;
} 

//add moderation redirect if set
if (!empty (get_option('_bbp_email_login' ))) {
	add_action( 'bbp_template_redirect', 'bsp_access_moderation_if_logged_out');
}

function bsp_access_moderation_if_logged_out(){
	$login_type = get_option( '_bbp_email_login_type') ;
	$login_url = get_option( '_bbp_login_url' ) ;
	$topic_slug = bbp_get_topic_post_type() ;
	$reply_slug = bbp_get_reply_post_type() ;
	//quick check if we need to do this function, ie is it the right url, and if so only do if is user not logged in
	if (strpos($_SERVER['REQUEST_URI'], '?post_type='.$topic_slug) == FALSE && strpos($_SERVER['REQUEST_URI'], '?post_type='.$reply_slug) == FALSE ) return;
	if (is_user_logged_in()) return ;
	//if forum WordPress login
    if (empty($login_type)) {
        $redirect = site_url() . '/wp-login.php?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] );;
        wp_redirect( $redirect );
        exit;
    }
	
    elseif (!empty($login_type) && !empty($login_url) ) {
       $redirect = $login_url;
       $redirect = $redirect.'?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] );
       wp_redirect( $redirect );
       exit;
    }	
}

//*******************    ADDS forums to the bulk topoic edit
add_action( 'bulk_edit_custom_box',  'bsp_quick_edit_fields', 10, 2 );

function bsp_quick_edit_fields( $column_name, $post_type ) {
	switch( $column_name ) {
		case 'bsp_topic_forum': {
			echo bsp_bulk_edit_forums () ;
			break;
		}
		case 'bbp_topic_forum': {
			echo bsp_bulk_edit_forums () ;
			break;
		}
	}
}

function bsp_bulk_edit_forums () {
	// Start an output buffer
		ob_start();
		
	?>
				<fieldset class="inline-edit-col-left">
					<div class="inline-edit-col">
						<p>
							<label for="bbp_forum_id"><?php esc_html_e( 'Forum:', 'bbpress' ); ?></label><br />
							<?php
                                                                $no_forum_str = 
                                                                        /* translators: &mdash; is encoded long dash (-) */
                                                                        esc_html__( '&mdash; No forum &mdash;', 'bbpress' );
								bbp_dropdown( array(
									'show_none' => $no_forum_str,
									'selected'  => bbp_get_form_topic_forum()
								) );
							?>
						</p>
					</div>
				<?php
			// Output the current buffer
		$output =  ob_get_clean();
	return $output ;
}

add_action( 'save_post', 'bsp_bulk_edit_save' );

function bsp_bulk_edit_save( $post_id ){

	// check bulk edit nonce
	if ( empty( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'bulk-posts' ) )) {
		return;
	}

	// update the forum
	$forum_id = ! empty( $_REQUEST[ 'bbp_forum_id' ] ) ? absint( $_REQUEST[ 'bbp_forum_id' ] ) : 0;
	remove_action( 'save_post', 'bsp_bulk_edit_save' );

        // update the post, which calls save_post again.
        wp_update_post( array( 'ID' => $post_id, 'post_parent' => $forum_id ) );

        // re-hook this function.
        add_action( 'save_post', 'bsp_bulk_edit_save' );
}