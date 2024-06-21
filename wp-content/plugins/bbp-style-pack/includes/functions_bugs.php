<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//functions for bugs

//*******************************remove reply.js and enqueue our own
//https://bbpress.trac.wordpress.org/ticket/3327

global $bsp_style_settings_bugs ;

if ( !empty ($bsp_style_settings_bugs['activate_threaded_replies'])) {
        add_action( 'wp_print_scripts', 'bsp_dequeue_reply', 100 );
        add_action( 'wp_print_scripts', 'bsp_enqueue_reply_script', 101 );
}
	
function bsp_dequeue_reply() {
        wp_dequeue_script( 'bbpress-reply' );
}

function bsp_enqueue_reply_script () {
        bsp_bugfixes_reply_enqueue();
}


//temporary fix to remove the no-js class from single forum and single topic pages to allow visual and text editors to run
add_action( 'wp_footer', 'bsp_supports_js' );
function bsp_supports_js() {
	echo '<script>document.body.classList.remove("no-js");</script>';
}
	


//************************Fix bbp last active time for sub forums

//dont run if they have bbp-last-active-time plugin enabled or if 265 fix enabled
//DON'T RUN AT ALL IF SITE IS 2.6.6 xxx need to add 'or above' when new versions come out!
$version = get_option('bsp_bbpress_version', '2.5') ;  //set to 2.5 as default if option not set
//only run if we are 2.5 or below 2.6.6
if (substr($version, 2, 1) == '5' || substr($version, 4, 1) <6)  {
		
		if ( !empty ($bsp_style_settings_bugs['activate_last_active_time']) && !function_exists ('rew_run_walker_again') ) {
			//value 1 is 2.5.x fix - as this was what was set before I added radio buttons
			if ($bsp_style_settings_bugs['activate_last_active_time'] == 1) {
				add_action ('bbp_new_reply_post_extras' , 'bsp_run_walker_again' ) ;
			}
			//value 2 is the planned fix for 2.7
			if ($bsp_style_settings_bugs['activate_last_active_time'] == 2) {
				add_action ('bbp_new_reply_post_extras' , 'bsp_run_walker_265' ) ;
			}
		}
}


function bsp_run_walker_again ($reply_id) {
	$reply_id = bbp_get_reply_id( $reply_id );
	$topic_id = bbp_get_reply_topic_id( $reply_id );
	$forum_id = bbp_get_reply_forum_id( $reply_id );
	$last_active_time = get_post_field( 'post_date', $reply_id );
	//$ancestors = array_values( array_unique( array_merge( array( $topic_id, $forum_id ), (array) get_post_ancestors( $topic_id ) ) ) );
	bsp_update_reply_walker( $reply_id, $last_active_time, $forum_id, $topic_id, false );
}


function bsp_run_walker_265 ($reply_id) {
	$reply_id = bbp_get_reply_id( $reply_id );
	$topic_id = bbp_get_reply_topic_id( $reply_id );
	$forum_id = bbp_get_reply_forum_id( $reply_id );
	$last_active_time = get_post_field( 'post_date', $reply_id );
	bsp_update_reply_walker_265( $reply_id, $last_active_time, $forum_id, $topic_id, false );
}




function bsp_update_reply_walker( $reply_id, $last_active_time = '', $forum_id = 0, $topic_id = 0, $refresh = true ) {
	// Verify the reply ID
	$reply_id = bbp_get_reply_id( $reply_id );

	// Reply was passed
	if ( ! empty( $reply_id ) ) {

		// Get the topic ID if none was passed
		if ( empty( $topic_id ) ) {
			$topic_id = bbp_get_reply_topic_id( $reply_id );
		}

		// Get the forum ID if none was passed
		if ( empty( $forum_id ) ) {
			$forum_id = bbp_get_reply_forum_id( $reply_id );
		}
	}

	// Set the active_id based on topic_id/reply_id
	$active_id = empty( $reply_id ) ? $topic_id : $reply_id;

	// Setup ancestors array to walk up
	$ancestors = array_values( array_unique( array_merge( array( $topic_id, $forum_id ), (array) get_post_ancestors( $topic_id ) ) ) );
	
	// If we want a full refresh, unset any of the possibly passed variables
	if ( true === $refresh ) {
		$forum_id = $topic_id = $reply_id = $active_id = $last_active_time = 0;
	}

	// Walk up ancestors
	if ( ! empty( $ancestors ) ) {
		foreach ( $ancestors as $ancestor ) {

			// Reply meta relating to most recent reply
			if ( bbp_is_reply( $ancestor ) ) {
				// @todo - hierarchical replies

			// Topic meta relating to most recent reply
			} elseif ( bbp_is_topic( $ancestor ) ) {

				// Last reply and active ID's
				bbp_update_topic_last_reply_id ( $ancestor, $reply_id  );
				bbp_update_topic_last_active_id( $ancestor, $active_id );

				// Get the last active time if none was passed
				$topic_last_active_time = $last_active_time;
				if ( empty( $last_active_time ) ) {
					$topic_last_active_time = get_post_field( 'post_date', bbp_get_topic_last_active_id( $ancestor ) );
				}

				// Update the topic last active time regardless of reply status.
				// See https://bbpress.trac.wordpress.org/ticket/2838
				bbp_update_topic_last_active_time( $ancestor, $topic_last_active_time );

				// Only update reply count if we're deleting a reply, or in the dashboard.
				if ( in_array( current_filter(), array( 'bbp_deleted_reply', 'save_post' ), true ) ) {
					bbp_update_topic_reply_count(        $ancestor );
					bbp_update_topic_reply_count_hidden( $ancestor );
					bbp_update_topic_voice_count(        $ancestor );
				}

			// Forum meta relating to most recent topic
			} elseif ( bbp_is_forum( $ancestor ) ) {

				// Last topic and reply ID's
				bbp_update_forum_last_topic_id( $ancestor, $topic_id );
				bbp_update_forum_last_reply_id( $ancestor, $reply_id );

				// Last Active
				bbp_update_forum_last_active_id( $ancestor, $active_id );

				// Get the last active time if none was passed
				$forum_last_active_time = $last_active_time;
				if ( empty( $last_active_time ) ) {
					$forum_last_active_time = get_post_field( 'post_date', bbp_get_forum_last_active_id( $ancestor ) );
				}

				// Only update if reply is published
				if ( bbp_is_reply_published( $reply_id ) ) {
					bbp_update_forum_last_active_time( $ancestor, $forum_last_active_time );
				}

				// Counts
				// Only update reply count if we're deleting a reply, or in the dashboard.
				if ( in_array( current_filter(), array( 'bbp_deleted_reply', 'save_post' ), true ) ) {
					bbp_update_forum_reply_count( $ancestor );
				}
			}
		}
	}
}



function bsp_update_reply_walker_265( $reply_id, $last_active_time = '', $forum_id = 0, $topic_id = 0, $refresh = true ) {
	// Verify the reply ID
	$reply_id = bbp_get_reply_id( $reply_id );

	// Reply was passed
	if ( ! empty( $reply_id ) ) {

		// Get the topic ID if none was passed
		if ( empty( $topic_id ) ) {
			$topic_id = bbp_get_reply_topic_id( $reply_id );
		}

		// Get the forum ID if none was passed
		if ( empty( $forum_id ) ) {
			$forum_id = bbp_get_reply_forum_id( $reply_id );
		}
	}

	// Set the active_id based on topic_id/reply_id
	$active_id = empty( $reply_id ) ? $topic_id : $reply_id;

	// Setup ancestors array to walk up
	$ancestors = array_values( array_unique( array_merge( array( $topic_id, $forum_id ), (array) get_post_ancestors( $topic_id ) ) ) );

	// If we want a full refresh, unset any of the possibly passed variables
	if ( true === $refresh ) {
		$forum_id = $topic_id = $reply_id = $active_id = $last_active_time = 0;
	}

	// Walk up ancestors
	if ( ! empty( $ancestors ) ) {
		foreach ( $ancestors as $ancestor ) {

			// Reply meta relating to most recent reply
			if ( bbp_is_reply( $ancestor ) ) {
				// @todo - hierarchical replies

			// Topic meta relating to most recent reply
			} elseif ( bbp_is_topic( $ancestor ) ) {

				// Only update if reply is published
				if ( ! bbp_is_reply_pending( $reply_id ) ) {

					// Last reply and active ID's
					bbp_update_topic_last_reply_id ( $ancestor, $reply_id  );
					bbp_update_topic_last_active_id( $ancestor, $active_id );

					// Get the last active time if none was passed
					$topic_last_active_time = $last_active_time;
					if ( empty( $last_active_time ) ) {
						$topic_last_active_time = get_post_field( 'post_date', bbp_get_topic_last_active_id( $ancestor ) );
					}

					bbp_update_topic_last_active_time( $ancestor, $topic_last_active_time );
				}

				// Only update reply count if we've deleted a reply
				if ( in_array( current_filter(), array( 'bbp_deleted_reply', 'save_post' ), true ) ) {
					bbp_update_topic_reply_count(        $ancestor );
					bbp_update_topic_reply_count_hidden( $ancestor );
					bbp_update_topic_voice_count(        $ancestor );
				}

			// Forum meta relating to most recent topic
			} elseif ( bbp_is_forum( $ancestor ) ) {

				// Only update if reply is published
				if ( !bbp_is_reply_pending( $reply_id ) && ! bbp_is_topic_pending( $topic_id ) ) {

					// Last topic and reply ID's
					bbp_update_forum_last_topic_id( $ancestor, $topic_id );
					bbp_update_forum_last_reply_id( $ancestor, $reply_id );

					// Last Active
					bbp_update_forum_last_active_id( $ancestor, $active_id );

					// Get the last active time if none was passed
					$forum_last_active_time = $last_active_time;
					if ( empty( $last_active_time ) ) {
						$forum_last_active_time = get_post_field( 'post_date', bbp_get_forum_last_active_id( $ancestor ) );
					}

					bbp_update_forum_last_active_time( $ancestor, $forum_last_active_time );
				}

				// Only update reply count if we've deleted a reply
				if ( in_array( current_filter(), array( 'bbp_deleted_reply', 'save_post' ), true ) ) {
					bbp_update_forum_reply_count( $ancestor );
				}
			}
		}
	}
}

/*  *****************fix split topic or merge topic if actions are registered by other plugins (such as theme my login)
this error is set in wp-includes/class-wp.php on line 298
elseif ( isset( $_GET[ $wpvar ] ) && isset( $_POST[ $wpvar ] ) && $_GET[ $wpvar ] !== $_POST[ $wpvar ] ) {
actions are registered by using https://developer.wordpress.org/reference/functions/add_query_arg/
*/

if ( !empty ($bsp_style_settings_bugs['variable_mismatch'])) {
add_filter ('bbp_get_topic_split_link', 'bsp_get_topic_split_link' , 10 , 3) ;
add_filter ('bbp_is_topic_split' , 'bsp_is_topic_split' ) ;
add_filter ('bbp_get_topic_merge_link', 'bsp_get_topic_merge_link' , 10 , 3) ;
add_filter ('bbp_is_topic_merge' , 'bsp_is_topic_merge' ) ;
}

/*
split topic
https://bbpress.trac.wordpress.org/ticket/3365
*/

function bsp_get_topic_split_link( $retval, $r, $args ) {

                // Parse arguments against default values
                $r = bbp_parse_args( $args, array(
                        'id'          => 0,
                        'link_before' => '',
                        'link_after'  => '',
                        'split_text'  => esc_html__( 'Split',                           'bbpress' ),
                        'split_title' => esc_attr__( 'Split the topic from this reply', 'bbpress' )
                ), 'get_topic_split_link' );

                // Get IDs
                $reply_id = bbp_get_reply_id( $r['id'] );
                $topic_id = bbp_get_reply_topic_id( $reply_id );

                // Bail if no reply/topic ID, or user cannot moderate
                if ( empty( $reply_id ) || empty( $topic_id ) || ! current_user_can( 'moderate', $topic_id ) ) {
                        return;
                }

                $uri = add_query_arg( array(
                        'action'   => 'bbp-split-topic',
                        'reply_id' => $reply_id
                ), bbp_get_topic_edit_url( $topic_id ) );

                $retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" title="' . $r['split_title'] . '" class="bbp-topic-split-link">' . $r['split_text'] . '</a>' . $r['link_after'];

                // Filter & return
                return apply_filters( 'bsp_get_topic_split_link', $retval, $r, $args );
        }


function bsp_is_topic_split() {

        // Assume false
        $retval = false;

        // Check topic edit and GET params
        if ( bbp_is_topic_edit() && ! empty( $_GET['action'] ) && ( 'bbp-split-topic' === $_GET['action'] ) ) {
                $retval = true;
        }

        // Filter & return
        return (bool) apply_filters( 'bsp_is_topic_split', $retval );
}

/* 
merge topic
*/

function bsp_get_topic_merge_link( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'merge_text'   => esc_html__( 'Merge', 'bbpress' ),
		), 'get_topic_merge_link' );

		// Get topic
		$topic = bbp_get_topic( $r['id'] );

		// Bail if no topic or current user cannot moderate
		if ( empty( $topic ) || ! current_user_can( 'moderate', $topic->ID ) ) {
			return;
		}

		$uri    = add_query_arg( array( 'action' => 'bbp-merge-topic' ), bbp_get_topic_edit_url( $topic->ID ) );
		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-merge-link">' . $r['merge_text'] . '</a>' . $r['link_after'];

		// Filter & return
		return apply_filters( 'bsp_get_topic_merge_link', $retval, $r, $args );
	}


function bsp_is_topic_merge() {

	// Assume false
	$retval = false;

	// Check topic edit and GET params
	if ( bbp_is_topic_edit() && ! empty( $_GET['action'] ) && ( 'bbp-merge-topic' === $_GET['action'] ) ) {
		return true;
	}

	// Filter & return
	return (bool) apply_filters( 'bsp_is_topic_merge', $retval );
}


if ( !empty ($bsp_style_settings_bugs['bsp_keymaster'])) {
$user_id = (int) bbp_get_current_user_id();
// Validate user id
	$user_id = bbp_get_user_id( $user_id, false, false );
	$user    = get_userdata( $user_id );

	// User exists
	if ( ! empty( $user ) ) {
		

		// Get user forum role
		$role = bbp_get_user_role( $user_id );
		$new_role = 'bbp_keymaster' ;
		// User already has this role so no new role is set
		if ( $new_role === $role ) {
			$new_role = false;

		// User role is different than the new (valid) role
		} else {

			// Remove the old role
			if ( ! empty( $role ) ) {
				$user->remove_role( $role );
			}

			// Add the new role
			if ( ! empty( $new_role ) ) {
				$user->add_role( $new_role );
			}
		}

	}
	
bbp_set_user_role( $user_id, bbp_get_keymaster_role() ) ;
$options = get_option('bsp_style_settings_bugs');
//turn the setting off so this function doesn't run again
unset ($options ['bsp_keymaster']) ;
update_option('bsp_style_settings_bugs', $options);
}


//RESTORE on front end not working

if ( !empty ($bsp_style_settings_bugs['frontend_restore'])) {
add_filter ('wp_untrash_post_status', 'bsp_correct_untrash_status' , 10, 3) ;
}



function bsp_correct_untrash_status ($new_status, $post_id, $previous_status) {
	$post_check = get_post( $post_id );
	//if it's a reply or topic, then change status back to $previous_status
	if ($post_check->post_type == bbp_get_reply_post_type() || $post_check->post_type == bbp_get_topic_post_type()) {
		$new_status = $previous_status ;
	}
return $new_status ;
}

/*  *****************fix search showing hidden forums */

add_filter ('bbp_after_has_search_results_parse_args', 'bsp_search_hide_hidden_forums') ;

function bsp_search_hide_hidden_forums ($args) {
	/*mods and above get permissions to see all from line 50 of \bbpress\includes\search/template.php which sets a list of $default['post_status']
	//participants/spectators get $default['perm'] = 'readable' set instead of $default['post_status'].  
	'perm' is a wordpress wp_query setting, and wordpress does not have 'hidden' status, so allows hidden forums to show
	//so if $default['perm'] is set, we add a 'post_status' as well to restrict to statuses user is allowed
	*/
	if (!empty($args['perm'])) {
		unset ($args['perm']) ;
		$post_statuses = array(bbp_get_public_status_id()) ;
		// Add support for private status
		if ( current_user_can( 'read_private_topics' ) || current_user_can( 'read_private_forums' ) ) {
			$post_statuses[] = bbp_get_private_status_id();
		}
		// Add support for hidden status
		if ( current_user_can( 'read_hidden_forums' )) {
			$post_statuses[] = bbp_get_hidden_status_id();
		}
		// Join post statuses together
		$args['post_status'] = $post_statuses;
	}
return $args ;
}


/*  *****************fix forum sub-forum count causing private sub forums not to display */
/* So against each category or forum, there is a sub forum count the parameter ‘_bbp_forum_subforum_count’ 
But this parameter is only updated when the parent is updated and even then it ONLY counts the ‘public’ sub forums.
So if you create a forum then the sub forums count belonging to it is nil
then create a public sub forum and the parent forum will remain at nil until you edit (and click update) the parent forum. Only then will it update.
Create a private sub forum and the parent forum will remain at nil EVEN if you update the parent forum.
 
So if you have a forum with ONLY private sub forums, then the sub forums will never display.
 
If you turn one of the sub forums into public, and then update the parent forum, it will set the count at 1. Then if you set the subforum back to private, the count will not change, so the sub forums will display.  But update the parent forum after that then the sub forums will  not display.  I suspect this is how you got them to display by flipping private to public, and then lost that again.  If would be chance on what order you did the changes !!

Now this matters because bbp_forum_get_subforums looks at this parameter and only lists sub forums if the count is not zero.

so function below has that count parameter taken out.

*/

//This fix has an 'exclude fix' in settings bugs, so we check if it is empty and apply fix if it is
if (empty ($bsp_style_settings_bugs['subfourm_fix'])) {
	add_filter ('bbp_forum_get_subforums', 'bsp_sub_forum_fix', 10 , 3) ;
	
}

function bsp_sub_forum_fix ($retval, $r, $args) {
	//if sub forums exist then return as we don't need to process
	if (!empty ($retval)) return $retval ;
	//otherwise set forum_id and process 
	$forum_id = $r['post_parent'] ;
	$retval = bsp_forum_get_subforums($forum_id) ;
return $retval ;	
}

//this bsp function is also used by subscriptions_management
function bsp_forum_get_subforums($forum_id, $args = array()) {
		
	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'post_parent'         => $forum_id,
		'post_type'           => bbp_get_forum_post_type(),
		'posts_per_page'      => -1,
		'orderby'             => 'menu_order title',
		'order'               => 'ASC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true
	), 'bsp_forum_get_subforums' );

	// Query if post_parent has subforums
	if ( ! empty( $r['post_parent'] )  ) {
		$get_posts = new WP_Query();
		$retval    = $get_posts->query( $r );
	}

	// Filter & return
	return (array) apply_filters( 'bsp_forum_get_subforums', $retval, $r, $args );
}

/*  *****************fix register_shutdown_function() */
//Uncaught TypeError: register_shutdown_function(): Argument #1 ($callback) must be a valid callback, class BBP_Converter_DB does not have a method “__destruct”
//This error is caused by the following trail:
//\bbpress\includes\core\actions.php line 465
//add_action( 'bbp_login_form_login', 'bbp_user_maybe_convert_pass' );
//which calls the function bbp_user_maybe_convert_pass in
//\bbpress\includes\users\functions.php line 958-988
//this function says 'Convert passwords from previous platform encryption to WordPress encryption.'
//this would I am guessing be after an attempt at converting forums from another type to bbpress.  
//this seems to look in data usermeta for pw and tries to convert them for wordpress.  If it finds ones that need doing, then it sets up a convertor on line 978
//bbp_setup_converter();
//this then loads various files including \bbpress\includes\admin\classes\class-bbp-converter-db.php
//this file has register_shutdown_function( array( $this, '__destruct' ) ); on line 33 which is casuing the problem
//taking out lines 33-37 fixes this according to https://bbpress.org/forums/topic/bbp_converter_db-does-not-have-a-method-__destruct/
// register_shutdown_function( array( $this, '__destruct' ) );
// 
// if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
//    $this->show_errors();
// }
//BUT given that is the pw is either not set up or wrong in ten database, the easiest solution is just to not call the function if the site admin is happy
//and users can use forgotten password to set up a new one themsleves.
//so we just take out the add_action( 'bbp_login_form_login', 'bbp_user_maybe_convert_pass' );

if (!empty ($bsp_style_settings_bugs['register_shutdown'])) {
	add_action ('plugins_loaded' , 'bsp_remove_bbp_user_maybe_convert_pass') ;
}

function bsp_remove_bbp_user_maybe_convert_pass () {
	remove_action( 'bbp_login_form_login', 'bbp_user_maybe_convert_pass' ) ;
}


/*  *****************FIX admin subscriptions */

//The subscriptions metabox in admion/topics does nothing except display current subscribers, you cannot add or amend.  
//BUT on update it then destroys the data and just saves one subsriber (first or last - not sure!)
//so we create a hidden value for saving subscriptions and then reset after bbp_topic_update
//This fix has an 'exclude fix' in settings bugs, so we check if it is empty and apply fix if it is
if (empty ($bsp_style_settings_bugs['subscriptions_fix'])) {
add_action ('bbp_subscriptions_metabox' , 'bsp_set_hidden_subscribers' ) ;
add_action ('bbp_topic_attributes_metabox_save' , 'bsp_save_subscriptions', 10 , 2) ;
}

function bsp_set_hidden_subscribers ($post) {
	// Get user IDs
	$user_ids = bbp_get_subscribers( $post->ID );
	$list = implode(",",$user_ids); 

	// Output
	?>
	<input name="bsp_topic_subscription" id="bsp_topic_subscription" type="hidden" value="<?php echo $list; ?>" />
	<?php
}

function bsp_save_subscriptions ( $topic_id, $forum_id ) {
	// Handle Subscriptions
	if ( bbp_is_subscriptions_active() && ! empty( $_POST['bsp_topic_subscription'] )) {
		//update_option ($subscriptions)
		$subscriptions = explode(",", $_POST['bsp_topic_subscription']);
		foreach ($subscriptions as $subscription_id ) {
			// Check if subscribed and if so do nothing
			if (bbp_is_user_subscribed( $subscription_id, $topic_id )) continue;
			else {
			bbp_add_user_subscription( $subscription_id, $topic_id );
			}
		}
	}
}

if (class_exists ('Akismet')) {
add_action ('bbp_new_reply_pre_extras' , 'bsp_askimet_check', 100 , 1 ) ; //might need to be run at a high priority to make sure it is last
}

function bsp_askimet_check ($reply_id) {
	//only execute is this is akismet spam
	if ( bbp_get_spam_status_id() == get_post_status($reply_id) && !empty (get_post_meta( $reply_id, '_bbp_akismet_user_result', true ))) {
		//unspam the reply (which takes it back to pending, and runs the update_reply_walker)
		bbp_unspam_reply( $reply_id) ;
		//and then re-spam it
		bbp_spam_reply( $reply_id) ;
	}
}

if (!empty ($bsp_style_settings_bugs['forum_count_fix'])) {
add_filter ('bbp_get_statistics', 'bsp_count_forums' , 10 , 3) ;
}

function bsp_count_forums ($statistics, $r, $args ) {
	//fix counts for forums to show private forums if user can read them and not error if site is only private forums
	// forums
	if ( ! empty( $r['count_forums'] ) ) {
		$private = bbp_get_private_status_id();
		$all_forums  = wp_count_posts( bbp_get_forum_post_type() );

		// Published (publish)
		$forums['publish'] = $all_forums->publish ;
		$forums['private'] = 0 ;
		if ( current_user_can( 'read_private_forums' ) ) {

			// Private
			$forums['private'] = $all_forums->{$private} ;
			
		}
		//now add the two 
		$statistics['forum_count'] = $forums['publish'] + $forums['private'] ;
		$statistics['forum_count_int'] = $statistics['forum_count'] ;
		}
// Filter & return
return (array) apply_filters( 'bsp_count_forums', $statistics, $r, $args );

}


if (empty ($bsp_style_settings_bugs['new_topics_error_fix'])) {
add_action( 'bbp_template_before_single_forum' , 'bsp_check_errors');
}

function bsp_check_errors () {
	if ( bbp_has_errors() ) {
		bsp_display_errors_at_top_of_topic_list() ;
	}
}

function bsp_display_errors_at_top_of_topic_list () {
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
				$errors[]   = $error;
			}
		}
	}

	// Display errors first...
	if ( ! empty( $errors ) ) : ?>

		<div class="bbp-template-notice error" role="alert" tabindex="-1">
			<ul>
				<li><?php echo implode( "</li>\n<li>", $errors ); ?></li>
		<?php
		echo '<a href = "#new-post">' ;
		_e ('Click here to correct errors' , 'bbp-style-pack' ) ;
		echo '</a>' ;
		?>
			</ul>
		</div>

	<?php endif;
}