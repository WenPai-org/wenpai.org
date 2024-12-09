<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//was class bspunreadInformation {};
//the extends stdClass{} has been added to prevent deprecation errors for php 8.2 
//'Deprecated: Creation of dynamic property bspunreadInformation::$unread '

class bspunreadInformation extends stdClass{};

//this function adds a class of bsp-topic-unread
add_filter( 'bbp_get_topic_class', 'bsp_add_unread_class' , 30 , 3) ;

if (is_user_logged_in ()) {
		global $bsp_style_settings_unread ;
		if ($bsp_style_settings_unread['optinout'] == 1) {
			//all users are opted in
			bsp_add_unread_actions () ;
			return ;
		}
		if ($bsp_style_settings_unread['optinout'] == 2 ) {
			bsp_unread_profile_actions() ;
			//then users must opt in, so only show if they have opted in
			$user = wp_get_current_user();
			$user_id = $user->ID ;
			$optinout = (!empty (get_user_meta($user_id, 'bsp_unread_optinout', true)) ? get_user_meta($user_id, 'bsp_unread_optinout', true) :'') ;
			if ($optinout==1) {
				//user has opted in so display
				bsp_add_unread_actions () ;
				return ;
			}
		}
		if ($bsp_style_settings_unread['optinout'] == 3 ) {
			bsp_unread_profile_actions() ;
			//then users must opt out, so show unless they have opted out
			$user = wp_get_current_user();
			$user_id = $user->ID ;
			$optinout = (!empty (get_user_meta($user_id, 'bsp_unread_optinout', true)) ? get_user_meta($user_id, 'bsp_unread_optinout', true) :'') ; 
			//if they have not opted out (!=2) then $optinout must equal 1 or be blank
			if ($optinout!=2) {
				//user has opted in so display
				bsp_add_unread_actions () ;
				return ;
			}
			//otherwise don't display
		} 
}
	
	
function bsp_add_unread_actions () {
	global $bsp_style_settings_unread ;
	add_action ( "bbp_theme_before_topic_title", "bsp_ur_icon_wrapper_begin" );
	//marks topic as read on visit to topic
	add_action ( "bbp_template_after_single_topic", "bsp_ur_on_topic_visit" );
	
	//display the unread/read icon before each forum title on forum index, and mark as read if needed
	add_action ( 'bbp_theme_before_forum_title', 'bsp_unread_forum_icons' );
	if (empty ($bsp_style_settings_unread['hide_on_index'])) {
	//display button only if we are at the forums index/root
	add_action ( 'bbp_template_before_forums_index', 'bsp_unread_button' );
	}
	//test if we need to mark all topics read in this forum - if user selects mark as read in single forum, the page redisplays and bsp_ur_is_mark_all_topics_as_read_requested returns true
	add_action ( 'bbp_template_before_single_forum', 'bsp_test_mark_as_read' ) ;
}

//only show if users need to opt-in or opt-out
function bsp_unread_profile_actions () {
	//edit in another user
	add_action( 'edit_user_profile', 'bsp_unread_profile_information', 50 , 2) ;
	//show in your own profile
	add_action( 'show_user_profile', 'bsp_unread_profile_information') ;
	
	//save if editing anothers profile
	add_action( 'edit_user_profile_update', 'bsp_update_unread_profile_information' );
	//add_action( 'edit_user_profile_update', 'bsp_unread_profile_information' );
	
	//saves if editing own profile
	add_action( 'personal_options_update',         'bsp_update_unread_profile_information' );
}


//Mark as read all topics in current forum
function bsp_test_mark_as_read () {
	if (bsp_ur_is_mark_all_topics_as_read_requested ()) {
		$forum_id = absint( filter_var( $_POST["bsp_ur_mark_id"], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		bsp_forum_read($forum_id) ; 

	}
}


function bsp_get_icon ($is_unread_topic) {
	global $bsp_style_settings_unread;
	//sets the path into the appropriate icon
	if (!empty($is_unread_topic)) {
		//unread topic
		$unread_icon = $bsp_style_settings_unread['unread_icon'];
		if ($unread_icon == 1) {
			//display default
			$path =plugins_url ( "images/folder_new.png" ,dirname(__FILE__));
		}
		if ($unread_icon == 2) {
			$path = $bsp_style_settings_unread['unread_url'] ;
		}
		if ($unread_icon == 3) {
			$path = 'blank' ;
		}
		
	}
	else {
		//read topic
		$read_icon = $bsp_style_settings_unread['read_icon'];
		if ($read_icon == 1) {
			//display default
			$path =plugins_url ( "images/folder.png" ,dirname(__FILE__));
		}
		if ($read_icon == 2) {
			$path = $bsp_style_settings_unread['read_url'] ;
		}
		if ($read_icon == 3) {
			$path = 'blank' ;
		}
	}
	
return $path ;
	
}

function bsp_display_icon ($path, $topic_id, $amount_div='') {
	if ($path !='blank') {
		//new code to take you to the last read item.  
		//get last visit ID to this topic, if blank then no visit to this topic since this update, so use of method of getting latest reply
		$last_id = get_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key_id (), true );
		if (!empty ($last_id))	$url = bbp_get_reply_url ($last_id) ;
		else $url = bbp_get_topic_last_reply_url ( $topic_id ) ;
		echo '
			<div class="bbpresss_unread_posts_icon">
				<a href="' . $url . '">
					<img src="' . $path . '"/>
				</a>
				'. $amount_div. '
			</div>
			
		';
	}
}

function bsp_ur_icon_wrapper_begin(){
	$topic_id = bsp_ur_get_current_looped_topic_id ();
	$is_unread_topic = bsp_is_topic_unread ( $topic_id );
	//we return with $is_unread_topic = true if the topic date is newer than the last visit date
	$path = bsp_get_icon ($is_unread_topic) ;
	//show the icon
	bsp_display_icon ($path, $topic_id) ;
}


function bsp_ur_get_current_looped_topic_id(){
	//check if set - not set on a 'search' for instance!
	if (isset( bbpress()->topic_query->post->ID ) ) return bbpress()->topic_query->post->ID;
	else return '' ;
}

function bsp_ur_on_topic_visit(){
	$topic_id = bbpress ()->reply_query->query ["post_parent"];
	bsp_ur_update_last_topic_visit ( $topic_id );
}

function bsp_ur_update_last_topic_visit($topic_id){
	update_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key (), current_time ( 'timestamp' ) );
	$last = get_post_meta ( $topic_id, '_bbp_last_active_id', true ) ;
	update_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key_id (), $last);
}

function bsp_ur_get_last_visit_meta_key(){
	 $current_user = wp_get_current_user();
	 
	return "bbpress_unread_posts_last_visit_" . $current_user->ID ;
}

function bsp_ur_get_last_visit_meta_key_id(){
	 $current_user = wp_get_current_user();
	 
	return "bbpress_unread_posts_last_visit_id_" . $current_user->ID ;
}



function bsp_ur_is_mark_all_topics_as_read_requested(){
	return isset ( $_POST ["bsp_ur_mark_all_topic_as_read"] );
}


function bsp_is_topic_unread($topic_id){
	//this is the last time that this topic was active - either the topic was created/amended or a reply was created/amended
	$topic_last_active_time = bbp_convert_date ( get_post_meta ( $topic_id, '_bbp_last_active_time', true ) );
	//this is the last visit by this user to this topic
	$last_visit_time = get_post_meta ( $topic_id, bsp_ur_get_last_visit_meta_key (), true );
	//this returns a true/false - true if the topic is newer than last visit time, false otherwise
	return $topic_last_active_time > $last_visit_time;
}

//used by forums index to show for each forum and mark as read for all forums if requested
function bsp_unread_forum_icons(){
	global $bsp_style_settings_unread ;
	//again not sure the if statement is needed as this function is only called from a forum page
	if ('forum' == get_post_type ()) {
		$unread_info = null;
		//check if we need to mark all items as read
		if (bsp_ur_is_mark_all_topics_as_read_requested ()) {
			$forum_id = bbp_get_forum_id ();
			bsp_all_forum_read ( $forum_id );
			$unread = false;
		} else {
			$forum_id = bbp_get_forum_id ();
			$unread_info = bsp_is_forum_unread_amount ( $forum_id );
			$unread = $unread_info->unread;
		}
		$amount_div = '';
		if(!empty($bsp_style_settings_unread['unread_amount']) && !empty($unread_info->amount) ){
			$amount_div = '<span class="bbpresss_unread_posts_amount">'. $unread_info->amount .'</span>';
		}
		$path = bsp_get_icon ($unread) ;
		//get the last active topic for a link
		$topic_id = get_post_meta( $forum_id , '_bbp_last_active_id', true );
		//show the icon
		bsp_display_icon($path, $topic_id,$amount_div) ;
        }
}

function bsp_is_forum_unread_amount($forum_id){
	//this returns the number of unread topics in a forum
	if (!empty ( $forum_id )) {
		$unread_amount = get_option ( 'bbp_unread_post_amount', false );
		$unr = new bspunreadInformation();
		$unr->unread = false;
		$unr->amount = 0;
		
		$childs = bsp_get_all_child_ids ( $forum_id, bbp_get_topic_post_type () );
		//offset starts at zero, so we end at one less
		$max = count ( $childs )-1;
		for($i = 0; $i <= $max; $i++) {
			$topic_id = $childs[$i];
			if (bsp_is_topic_unread ( $topic_id )) {
				$unr->unread = true;
				$unr->amount = $unr->amount + 1;
				if(! $unread_amount){
					return $unr;
				}
			}
		}
		$childs = bsp_get_all_child_ids ( $forum_id, bbp_get_forum_post_type () );
		//offset starts at zero, so we end at one less
		$max = count ( $childs )-1;
		$subforum_id;
		for($i = 0; $i <= $max; $i ++) {
			$subforum_id = $childs [$i];
			$subforum_info = bsp_is_forum_unread_amount( $subforum_id );
			if (! empty ( $subforum_id ) && $subforum_info->unread) {
				$unr->unread = true;
				$unr->amount = $subforum_info->amount + $unr->amount;
				if(! $unread_amount){
					return $unr;
				}
				
			}
		}
	}
	return $unr;
}
//this marks all topics in a forum as read
function bsp_forum_read($forum_id){
	$current_user = wp_get_current_user();
	$key =  "bbpress_unread_posts_last_visit_" . $current_user->ID ;
	//not sure this if statement is needed, can't see this is ever called without a valid forum id
	if ($forum_id != null && ! empty ( $forum_id )) {
		
		//mark all topics in this forum as read
		$childs = bsp_get_all_child_ids ( $forum_id, bbp_get_topic_post_type () );
		//offset starts at zero, so we end at one less
		$max = count ( $childs )-1;
		$topic_id;
		for($i = 0; $i <= $max; $i ++) {
			$topic_id = $childs [$i];	
			update_post_meta ( $topic_id, $key, current_time ( 'timestamp' ) );;
		}
	}
}

//this marks all topics in all forums as read - called by forum index as each forum is displayed and does child forums as well.
function bsp_all_forum_read($forum_id){
	//not sure this if statement is needed, can't see this is ever called without a valid forum id
	if ($forum_id != null && ! empty ( $forum_id )) {
		bsp_forum_read($forum_id) ;
		//then mark all topics in sub forums
		$childs = bsp_get_all_child_ids ( $forum_id, bbp_get_forum_post_type () );
		if (!empty ($childs)) {
			//offset starts at zero, so we end at one less
			$max = count ( $childs )-1;
			$subforum_id;
			for($i = 0; $i <= $max; $i ++) {
				$subforum_id = $childs [$i];
				bsp_forum_read ( $subforum_id );
			}
		}
	}
}



//revised bbp_get_all_child_ids to take out trashed and spam topics
function bsp_get_all_child_ids( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id  = 'bbp_parent_all_' . $parent_id . '_type_' . $post_type . '_child_ids';

	// Check for cache and set if needed
	$child_ids = wp_cache_get( $cache_id, 'bbpress_posts' );
	if ( false === $child_ids ) {
		$post_status = array( bbp_get_public_status_id() );

		// Extra post statuses based on post type
		switch ( $post_type ) {

			// Forum
			case bbp_get_forum_post_type() :
				$post_status[] = bbp_get_private_status_id();
				$post_status[] = bbp_get_hidden_status_id();
				break;

			// Topic
			case bbp_get_topic_post_type() :
				$post_status[] = bbp_get_closed_status_id();
				break;

			// Reply
			case bbp_get_reply_post_type() :
				$post_status[] = bbp_get_trash_status_id();
				$post_status[] = bbp_get_spam_status_id();
				break;
		}

		// Join post statuses together
		$post_status = "'" . implode( "', '", $post_status ) . "'";

		$child_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_ids, 'bbpress_posts' );
	}

	// Filter and return
	return apply_filters( 'bsp_get_all_child_ids', $child_ids, (int) $parent_id, $post_type );
}

//user edit profile options

function bsp_unread_profile_information()  {
//This function hooks to form-user-edit to add user ability to edit items on both wordpress user profile and bbp user profile
			global $bsp_style_settings_unread ;
			global $current_user;
			if (isset($_REQUEST['user_id'])) {
                                $user_id = absint( filter_var( $_REQUEST['user_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
			} else {
                                $user_id = $current_user->ID;
			}
			//exit if users are not allowed to change
			if ($bsp_style_settings_unread['optinout'] == 1) return ;
			//opt in - user not yet set
			$optinout = (!empty (get_user_meta($user_id, 'bsp_unread_optinout', true)) ? get_user_meta($user_id, 'bsp_unread_optinout', true) :'') ; 
			//then set default based on optinout value if user has not yet set something			
			if (empty ($optinout) && $bsp_style_settings_unread['optinout'] == 2) {
                                //user must opt in, so set to opt out
                                $optinout = 2 ;
			}
			if (empty ($optinout) && $bsp_style_settings_unread['optinout'] == 3) {
                                //user must opt out, so set to opt in
                                $optinout = 1 ;
			}
			
			$label1 = (!empty ($bsp_style_settings_unread['optin_desc']) ? $bsp_style_settings_unread['optin_desc']: 'Display unread icons')  ;
			$label2 = (!empty ($bsp_style_settings_unread['optout_desc']) ? $bsp_style_settings_unread['optout_desc']: 'Do not display unread icons')  ;
			echo '<div id= "bsp-unread">' ;	
			?>	
			<table>
				<tr>			
					<td style="text-align:left">
						<?php
						$item =  'bsp_unread_optinout' ;
						echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$optinout, false ) . ' />' ;
						echo $label1 ;?>
						<br/>
						<?php
						echo '<input name="'.$item.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2,$optinout, false ) . ' />' ;
						echo $label2 ;?>
					</td>
				</tr>
			</table>
			<?php echo '</div>' ;
}

//this function adds the updated items info to the usermeta database
function bsp_update_unread_profile_information( $user_id ) {
	$update = filter_var( $_POST['bsp_unread_optinout'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH );
        update_user_meta( $user_id, 'bsp_unread_optinout', $update);
}

function bsp_add_unread_class ($post_classes, $topic_id, $classes ) {
	if (!is_user_logged_in()) return $post_classes;
	$topic_id = bsp_ur_get_current_looped_topic_id ();
	if (bsp_is_topic_unread ( $topic_id )) {
		array_push ($post_classes, 'bsp-topic-unread') ;
	}
	else {
		array_push ($post_classes, 'bsp-topic-read') ;
	}
        return $post_classes ;
}
?>