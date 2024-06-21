<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**

//THIS IS CODE FROM BBPTOOLKIT as that plugin is no longer maintained.
* Add new users to the default forum(s)
*/


function bsptoolkit_insert_forums_new_user( $user_id ) {
	global $bsp_style_settings_sub_management ;
	// Get default forums to add
		$all_forums = bsptoolkit_forum_structure();
		foreach ($all_forums as $myforum) {
			// add subscription for user to default forums except for categories
			if (bbp_is_forum( $myforum['id']) && !bbp_is_forum_category( $myforum['id'])) {
				//now see if user should be subscribed to this forum
				if(!empty($bsp_style_settings_sub_management[$myforum['id']])) {
                                        bbp_add_user_forum_subscription( $user_id, $myforum['id'] ); 
				}
			}
		}
}


add_action ('user_register','bsptoolkit_insert_forums_new_user' ) ;

/**
* Add 'Subscriptions' as action for forum
*/

function bsptoolkit_forum_subscr_action_link($actions, $post) {
	if ( $post->post_type == "forum" ) {
		if ((bbp_current_user_can_publish_forums()) && (!((bbp_is_forum_category($post->ID))))) {
			$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&forum_id=' . $post->ID . '">' . __( 'Subscriptions', 'bbp-style-pack' ) . '</a>';
		}
	}
	return $actions;
}
add_filter('page_row_actions', 'bsptoolkit_forum_subscr_action_link', 10, 2);

/**
* Add 'Subscriptions' as action for topics
*/

function bsptoolkit_topic_subscr_action_link($actions, $post) {
	if ( $post->post_type == "topic" ) {
		if ((bbp_current_user_can_publish_forums()) && (!((bbp_is_forum_category($post->ID))))) {
			$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&topic_id=' . $post->ID . '">' . __( 'Subscriptions', 'bbp-style-pack' ) . '</a>';
		}
	}
	return $actions;
}
add_filter('post_row_actions', 'bsptoolkit_topic_subscr_action_link', 10, 2);

/**
* Add 'Subscriptions' as action for user
*/

function bsptoolkit_user_subscr_action_link($actions, $user_object) {
	if (bbp_current_user_can_publish_forums()) {
		$actions['mng_subscr'] = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&user_id=' . $user_object->ID . '">' . __( 'Subscriptions', 'bbp-style-pack' ) . '</a>';
	}
		return $actions;
}
add_filter('user_row_actions', 'bsptoolkit_user_subscr_action_link', 10, 2);


/**
* Add Subscriptions metabox to forum
*/
function bsptoolkit_forum_subscrip_metabox() {
	echo '<br>';
	if (bbp_is_forum_category(get_the_ID())) {
		_e('No subscriptions for categories', 'bbp-style-pack');
	} else {
		$forum_id = get_the_ID();
		$users_arr = bbp_get_forum_subscribers($forum_id);
		$subscriber_count = count($users_arr);
		
		echo '<a class="preview button" href="' . site_url() . '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&forum_id=' . $forum_id . '">'; _e('Manage Subscriptions', 'bbp-style-pack'); echo ' (' . $subscriber_count . ')</a>';
	}
	echo '<br>';
	echo '<br>';
}
function bsptoolkit_subscrip_attributes_metabox() {
	// Meta data
	add_meta_box(
		'bsptoolkit_forum_subscrip_metabox',
		__( 'Forum Subscriptions', 'bbp-style-pack' ),
		'bsptoolkit_forum_subscrip_metabox',
		'forum',
		'side'
	);
}
add_action('add_meta_boxes', 'bsptoolkit_subscrip_attributes_metabox');

/**
* Add Subscriptions column to forum list
*/

function bsptksub_edit_forum_column( $columns ) {
	$columns['manage_subscriptions'] = __( 'Subscriptions', 'bbp-style-pack' );
	return $columns;
}
add_filter( 'manage_edit-forum_columns', 'bsptksub_edit_forum_column' );

function bsptksub_manage_forum_column($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
                case 'manage_subscriptions':
                        if (bbp_is_forum_category($id)) {
                                echo '';
                        } else {
                                if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
                                        $url = $_SERVER['REQUEST_URI'] . '&page=forum_subscriptions&forum_id=' . $id;
                                } else {
                                        $url = $_SERVER['REQUEST_URI'] . '?page=forum_subscriptions&forum_id=' . $id;
                                }
                                $users_arr = bbp_get_forum_subscribers($id);
                                $subscriber_count = count($users_arr);
                                echo '<a href="' . $url . '">' . $subscriber_count .' '.__('subscriber(s)', 'bbp-style-pack' ).'</a>';
                        }
                        break;
                default:
                        break;
	} // end switch
}  
add_action('manage_forum_posts_custom_column', 'bsptksub_manage_forum_column', 10, 2);

/**
* Add Subscriptions column to topic list
*/

function bsptksub_edit_topic_column( $columns ) {
	$columns['manage_subscriptions'] = __( 'Subscriptions', 'bbp-style-pack' );
	return $columns;
}
add_filter( 'manage_edit-topic_columns', 'bsptksub_edit_topic_column' );

function bsptksub_manage_topic_column($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
                case 'manage_subscriptions':
                        $url = site_url( '/wp-admin/edit.php?post_type=forum&page=forum_subscriptions&topic_id=' . $id );
                        $users_arr = bbp_get_topic_subscribers($id);
                        $subscriber_count = count($users_arr);
                        echo '<a href="' . $url . '">' . $subscriber_count .' '.__('subscriber(s)', 'bbp-style-pack' ).'</a>';
                        break;
                default:
                        break;
	} // end switch
}  
add_action('manage_topic_posts_custom_column', 'bsptksub_manage_topic_column', 10, 2);

/**
* Add Subscriptions menu item under forums - This was changed in 5.7.1 to put back the submenu page parameter 'edit.php?post_type=forum' which was put as null, but caused a strpos null error
*/
add_action('admin_menu', 'bsptoolkit_subscr_submenu');
function bsptoolkit_subscr_submenu(){
	$confHook = add_submenu_page('edit.php?post_type=forum', 'Subscriptions', 'Subscriptions', 'edit_forums', 'forum_subscriptions', 'bsp_forum_subscriptions_page');
	add_action("admin_head-".$confHook, 'bsptksub_admin_header');
}

function bsptksub_admin_header() {
	echo '<script type=\'text/javascript\'>';
	echo 'function bsptksubtoggleall(master,group) {';
	echo '	var cbarray = document.getElementsByClassName(group);';
	echo '	for(var i = 0; i < cbarray.length; i++){';
	echo '		var cb = document.getElementById(cbarray[i].id);';
	echo '		cb.checked = master.checked;';
	echo '	}';
	echo '}';
	echo '</script>';
	echo '<style type="text/css">';
	echo '.wp-list-table .subscr-yes-button {
	  -webkit-border-radius: 28;
	  -moz-border-radius: 28;
	  border-radius: 28px;
	  font-family: Arial;
	  color: #ffffff;
	  font-size: 12px;
	  background: #3498db;
	  padding: 3px 7px 3px 7px;
	  text-decoration: none;
	  }
	.wp-list-table .subscr-no-button {
	  -webkit-border-radius: 28;
	  -moz-border-radius: 28;
	  border-radius: 28px;
	  font-family: Arial;
	  color: #ffffff;
	  font-size: 10px;
	  background: #D8D8D8;
	  padding: 3px 7px 3px 7px;
	  text-decoration: none;
	  }';
	echo '</style>';
}

/**
* MAIN PAGE
*/
function bsp_forum_subscriptions_page() {
	global $wpdb;
	
	// Security check: only if user can publish_forums (standard Moderators and Keymasters)
	if (!bbp_current_user_can_publish_forums()) {
		echo __('Sorry, you do not have enough permissions', 'bbp-style-pack');
		return;
	}
	
	if (!(isset($_GET['forum_id']) || isset($_GET['user_id']) || isset($_GET['topic_id']))) {
		// Get forum list here to start
	//This is shown in dashboard>forums>subscriptions
		echo '<h1>'.__('Manage Subscriptions', 'bbp-style-pack').'</h1>';
		
		echo '<p>'.__('To manage subscriptions of a forum', 'bbp-style-pack').', <a href="' . site_url() . '/wp-admin/edit.php?post_type=forum' . '">'.__('edit the forums', 'bbp-style-pack').'</a>'.__(' and click on "Subscriptions" as an action of the forum, or edit the forum and find the "Manage Subscriptions" button (somewhere below the Forum Attributes)', 'bbp-style-pack').'</p>';
		echo '<p>'.__('To manage subscriptions of topics', 'bbp-style-pack').', <a href="' . site_url() . '/wp-admin/edit.php?post_type=topic' . '">'.__('edit the topics', 'bbp-style-pack').'</a>'.__(' and find "Subscriptions" as an action for each topic.', 'bbp-style-pack').'</p>';
		echo '<p>'.__('To manage subscriptions for a user', 'bbp-style-pack').', <a href="' . site_url() . '/wp-admin/users.php' . '">'.__('edit the users', 'bbp-style-pack').'</a>'.__(' and find "Subscriptions" as an action for each user.', 'bbp-style-pack').'</p>';
		return;
	}
	
	
	if (isset($_GET['forum_id'])) {
		bsp_manage_forum_subscriptions() ;
	}
	
	if (isset($_GET['topic_id'])) {
		bsp_manage_topic_subscriptions() ;
	}
	
	if (isset($_GET['user_id'])) {
		bsp_manage_user_subscriptions() ;
	}
}

function bsptoolkit_removehtml_and_cutwords($scontent, $limitwords) {
	// Remove HTML tags
	$scontent = wp_strip_all_tags($scontent);
	// Cut after $limitwords words
	$scontent = preg_replace('/(?<=\S,)(?=\S)/', ' ', $scontent);
	$scontent = str_replace("\n", " ", $scontent);
	$contentarray = explode(" ", $scontent);
	if (count($contentarray)>$limitwords) {
		array_splice($contentarray, $limitwords);
		$scontent = implode(" ", $contentarray)." ...";
	} 
	return $scontent;
}

function bsp_get_user_details ($user) {
	$user_det_arr = array();
        if ( is_object( $user ) ) { 
                if ($user->display_name) $user_det_arr[] = $user->display_name;
                if (($user->user_login) && ($user->user_login != $user->display_name)) $user_det_arr[] = $user->user_login;
                if (($user->user_nicename) && ($user->user_nicename!= $user->display_name) && $user->user_nicename!= $user->user_login) $user_det_arr[] = $user->user_nicename;
        }
        $user_det = implode(" - ", $user_det_arr);
        return $user_det ;
}

function bsptoolkit_forum_structure() {
	$all_forums_data = array();
	$i = 0;
	if ( bbp_has_forums() ) {
		while ( bbp_forums() ) {
			bbp_the_forum();
			$forum_id = bbp_get_forum_id();
			$all_forums_data[$i]['id'] = $forum_id;
			$all_forums_data[$i]['title'] = bbp_get_forum_title($forum_id);
			// Check for subforums (first level only)
			if ($sublist = bsp_forum_get_subforums($forum_id)) {
				$all_subforums = array();
				foreach ( $sublist as $sub_forum ) {
					$i++;
					$all_forums_data[$i]['id'] = $sub_forum->ID;
					$all_forums_data[$i]['title'] = '- ' . bbp_get_forum_title($sub_forum->ID);
				}
			}					
			$i++;
		} // while()
	} // if()
	return $all_forums_data;
}

function bsp_manage_forum_subscriptions () {
	global $wpdb;
	$forum_id = absint( filter_var( $_GET['forum_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		if (bbp_is_forum_category($forum_id)) {
			echo '<h1>'.__('Manage Subscriptions', 'bbp-style-pack').'</h1>';
			_e('Categories do not have subscriptions...', 'bbp-style-pack');
			return;
		}

		if (isset($_POST["bsptksubsubmit"])) {
			$apply = __('Apply', 'bbp-style-pack') ;
			if ($_POST["bsptksubsubmit"] == $apply) {
				// POST form received for FORUM
				if ($_POST["action"] == 'Subscribe') {
					foreach ($_POST["bsptksubcb"] as $user_id) {
						if (!bbp_is_forum_category( absint( $_POST['forum_id'] ) )) {
							bbp_add_user_subscription( absint( $user_id ), absint( $_POST['forum_id'] ) );
						}
					}
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bsptksubcb"] as $user_id) {
						bbp_remove_user_subscription( absint( $user_id ), absint( $_POST['forum_id'] ) );
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = absint( filter_var( $_GET['paged'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}


		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = absint( filter_var( $_GET['number'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		
		// Get users
		$args = array(
			'orderby' => 'display_name',
			'order' => 'ASC',
			'number' => $number,
			'paged' => $paged,
		);
		$all_users = get_users( $args );
		
		//echo '<h1><b>Manage subscriptions</b> for forum: ' . bbp_get_forum_title($forum_id) . '</h1>'; 
		echo '<h1><b>'.__('Manage Subscriptions', 'bbp-style-pack').'</b> '.__('for forum: ', 'bbp-style-pack');
		echo bbp_get_forum_title($forum_id) . '</h1>';
		echo '<h3>' . bbp_get_forum_content($forum_id) . '</h3>'; 
		echo '<form action="" method="post" id="bsptksubform">';
		echo '<select name="action">';
		echo '<option value="">'.__('Select Option' , 'bbp-style-pack').'</option>';
		echo '<option value="Subscribe">'.__('Subscribe' , 'bbp-style-pack').'</option>';
		echo '<option value="Unsubscribe">'.__('Unsubscribe','bbp-style-pack').'</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bsptksubsubmit" value="'; 
		_e('Apply', 'bbp-style-pack'); 
		echo '" />';
		echo '<br><br>';
		
		echo '<table id="bsptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bsptksubcbgroup_master" onchange="bsptksubtoggleall(this,\'bsptksubcbgroup\')" /></th><th><b>ID</b></th><th><b>'.__('Name', 'bbp-style-pack').'</b></th><th><b>'.__('Subscriptions', 'bbp-style-pack').'</b></th><th><b>'.__('Roles' , 'bbp-style-pack').'</b></th></tr>';
		$i = 0;
		$cap_with_prefix = $wpdb->prefix . 'capabilities';
		foreach ( $all_users as $user ) {
			$user_id = $user->ID;
			// Check subscription
			$is_subscribed = bbp_is_user_subscribed_to_forum($user_id, $forum_id);
			if ($is_subscribed) {
				$is_subscribed = '<button disabled class="subscr-yes-button">'.__('Subscribed', 'bbp-style-pack').'</button>';
			} else {
				$is_subscribed = '&nbsp;&nbsp;<button disabled class="subscr-no-button">'.__('Unsubscribed', 'bbp-style-pack').'</button>';
			}
			// Get roles
			$caps = get_user_meta($user_id, $cap_with_prefix, true);
			$roles = array_keys((array)$caps);
			$roles = str_replace ('bbp_', '', $roles) ;
			// User details
			$user_det = bsp_get_user_details ($user) ;
			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bsptksubcbgroup" id="bsptksubcb_'.$user_id.'" name="bsptksubcb[]" value="' . $user_id . '"></td>';
			echo '<td>' . $user->ID . '</td><td>' . $user_det ;
			bsp_not_allowed_subscription_emails ($user_id) ;	
			echo '</td><td>' . $is_subscribed . '</td>';
			echo '<td>' . implode(", ", $roles) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="forum_id" value="' . $forum_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
                foreach ($query as $key=>$value) {
                        $query[$key] = ( ( is_string( $value ) || is_integer( $value ) ) ? htmlspecialchars($query[$key]) : $query[$key] );
                }
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text">'.__('Prev Page', 'bbp-style-pack').' </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo __('Page ', 'bbp-style-pack'). $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text">'.__('Next Page', 'bbp-style-pack').'</span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
}




function bsp_manage_topic_subscriptions() {
	global $wpdb;
	$topic_id = absint( filter_var( $_GET['topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		if (isset($_POST["bsptksubsubmit"])) {
			$apply = __('Apply', 'bbp-style-pack') ;
			if ($_POST["bsptksubsubmit"] == $apply && !empty ($_POST["bsptksubcb"])) {
				// POST form received for TOPIC
				if ($_POST["action"] == 'Subscribe') {
					foreach ($_POST["bsptksubcb"] as $user_id) {
						bbp_add_user_subscription( absint( filter_var( $user_id, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ), absint( filter_var( $_POST['topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );
					}
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bsptksubcb"] as $user_id) {
						bbp_remove_user_subscription( absint( filter_var( $user_id, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ), absint( filter_var( $_POST['topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = absint( filter_var( $_GET['paged'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}


		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = absint( filter_var( $_GET['number'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		
		// Get users
		$args = array(
			'orderby' => 'display_name',
			'order' => 'ASC',
			'number' => $number,
			'paged' => $paged,
		);
		$all_users = get_users( $args );
		
		
		echo '<h1><b>'.__('Manage Subscriptions', 'bbp-style-pack').'</b> '.__('for topic: ', 'bbp-style-pack');
		echo bbp_get_topic_title($topic_id) . '</h1>';

		
		echo '<h3>' . bbp_get_topic_content($topic_id) . '</h3>'; 
		echo '<form action="" method="post" id="bsptksubform">';
		echo '<select name="action">';
		echo '<option value="">'.__('Select Option' , 'bbp-style-pack').'</option>';
		echo '<option value="Subscribe">'.__('Subscribe Selected Users' , 'bbp-style-pack').'</option>';
		echo '<option value="Unsubscribe">'.__('Unsubscribe Selected Users','bbp-style-pack').'</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bsptksubsubmit" value="'; 
		_e('Apply', 'bbp-style-pack'); 
		echo '" />';
		echo '<br><br>';
		
		echo '<table id="bsptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bsptksubcbgroup_master" onchange="bsptksubtoggleall(this,\'bsptksubcbgroup\')" /></th><th><b>ID</b></th><th><b>'.__('Name', 'bbp-style-pack').'</b></th><th><b>'.__('Subscriptions', 'bbp-style-pack').'</b></th><th><b>'.__('Roles' , 'bbp-style-pack').'</b></th></tr>';
		
		
		$i = 0;
		$cap_with_prefix = $wpdb->prefix . 'capabilities';
		foreach ( $all_users as $user ) {
			$user_id = $user->ID;
			// Check subscription
			$is_subscribed = bbp_is_user_subscribed_to_topic($user_id, $topic_id);
			if ($is_subscribed) {
				$is_subscribed = '<button disabled class="subscr-yes-button">'.__('Subscribed', 'bbp-style-pack').'</button>';
			} else {
				$is_subscribed = '&nbsp;&nbsp;<button disabled class="subscr-no-button">'.__('Unsubscribed', 'bbp-style-pack').'</button>';
			}
			
			// Get roles
			$caps = get_user_meta($user_id, $cap_with_prefix, true);
			$roles = array_keys((array)$caps);
			$gen_roles = str_replace ('bbp_', '', $roles) ;
			// User details
			$user_det = bsp_get_user_details ($user) ;
			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bsptksubcbgroup" id="bsptksubcb_'.$user_id.'" name="bsptksubcb[]" value="' . $user_id . '"></td>';
			echo '<td>' ;
			echo $user->ID ;
			echo '</td><td>' ;
			echo $user_det ;
			bsp_not_allowed_subscription_emails ($user_id) ;	
			echo '</td><td>' . $is_subscribed . '</td>';
			echo '<td>' . implode(", ", $gen_roles) ;
			
			echo '</td>' ;
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="topic_id" value="' . $topic_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
                foreach ($query as $key=>$value) {
                        $query[$key] = ( ( is_string( $value ) || is_integer( $value ) ) ? htmlspecialchars($query[$key]) : $query[$key] );
                }
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text">'.__('Prev Page', 'bbp-style-pack').' </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo __('Page ', 'bbp-style-pack'). $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text">'.__('Next Page', 'bbp-style-pack').'</span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
}

function bsp_manage_user_subscriptions() {
	global $wpdb;
	$user_id = absint( filter_var( $_GET['user_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );

		if (isset($_POST["bsptkfrsubsubmit"])) {
			$apply = __('Apply', 'bbp-style-pack') ;
			if ($_POST["bsptkfrsubsubmit"] == $apply && !empty ($_POST["bsptksubcb"])) {
				// POST form received for USER
				if ($_POST["action"] == 'Subscribe') {
					foreach ($_POST["bsptksubcb"] as $forum_id) {
					bbp_add_user_forum_subscription( $user_id, absint( filter_var( $forum_id, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );
					}
				}
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bsptksubcb"] as $forum_id) {
						bbp_remove_user_forum_subscription( $user_id, absint( filter_var( $forum_id, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );
					}
				}		
			}
		}
		
		if (isset($_POST["bsptksubsubmit"])) {
			$apply = __('Apply', 'bbp-style-pack') ;
			if ($_POST["bsptksubsubmit"] == $apply && !empty ($_POST["bsptksubtopcb"])) {
				// POST form received for TOPICS
				if ($_POST["action"] == 'Unsubscribe') {
					foreach ($_POST["bsptksubtopcb"] as $forum_id) {
						bbp_remove_user_topic_subscription( $user_id, absint( filter_var( $forum_id, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );
					}
				}		
			}
		}
		
		// Check page number from URL for forums
		$paged = 1;
		if (isset($_GET['paged'])) {
			$paged = absint( filter_var( $_GET['paged'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		
		// Check number of records per page from URL
		$number = 20;
		if (isset($_GET['number'])) {
			$number = absint( filter_var( $_GET['number'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		// User details
		$user = get_userdata($user_id);
		$user_det = bsp_get_user_details ($user) ;
		echo '<h1><b>'.__('Manage Subscriptions', 'bbp-style-pack').'</b> '.__('for user: ', 'bbp-style-pack');
		echo bsp_get_user_details ($user). '</h1>';
		echo '<h2>Forum subscriptions</h2>';
		echo '<form action="" method="post" id="bsptksubform">';
		echo '<select name="action">';
		echo '<option value="">'.__('Select Option' , 'bbp-style-pack').'</option>';
		echo '<option value="Subscribe">'.__('Subscribe to Selected Forums' , 'bbp-style-pack').'</option>';
		echo '<option value="Unsubscribe">'.__('Unsubscribe from Selected Forums','bbp-style-pack').'</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bsptkfrsubsubmit" value="';
		_e('Apply', 'bbp-style-pack'); 
		echo '" />';
		echo '&nbsp;';
		bsp_not_allowed_subscription_emails ($user_id, true) ;
		echo '<table id="bsptksub-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bsptksubcbgroup_master" onchange="bsptksubtoggleall(this,\'bsptksubcbgroup\')" /></th><th><b>'.__('Forum Name', 'bbp-style-pack').' </b></th></tr>';
		$i = 0;
		//
		?>
		<tr>
		<td >
		<?php
		$all_forums = bsptoolkit_forum_structure();
		foreach ($all_forums as $myforum) {
			echo '<tr><td>' ;
			echo '<input type="checkbox" class="bsptksubcbgroup" id="bsptksubcb_'.$myforum['id'].'" name="bsptksubcb[]" value="'.$myforum['id'].'" ';
			echo '>' ; //end of input type
			echo '</td><td>'.$myforum['title'].'</td>';
			echo '<td>' ;		
			// Check subscription
			$is_subscribed = bbp_is_user_subscribed_to_forum($user_id, $myforum['id']);
			if ($is_subscribed) {
				$is_subscribed = '<button disabled class="subscr-yes-button">'.__('Subscribed', 'bbp-style-pack').'</button>';
			} else {
				$is_subscribed = '&nbsp;&nbsp;<button disabled class="subscr-no-button">'.__('Unsubscribed', 'bbp-style-pack').'</button>';
			}
			echo $is_subscribed.'</td></tr>' ;
		}	
		?>
		
		</td>
		</tr>
		</table>
		<?php
		echo '<input type="hidden" name="user_id" value="' . $user_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging
		$query = $_GET;
		$query['paged'] = $paged + 1;
                foreach ($query as $key=>$value) {
                        $query[$key] = ( ( is_string( $value ) || is_integer( $value ) ) ? htmlspecialchars($query[$key]) : $query[$key] );
                }
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged > 1) {
			$query['paged'] = $paged - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text">'.__('Prev Page', 'bbp-style-pack').' </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo __('Page ', 'bbp-style-pack'). $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text">'.__('Next Page', 'bbp-style-pack').'</span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
	
		
		echo '<br><br>';
		echo '<h2>'.__('Remove Topic subscriptions', 'bbp-style-pack').'</h2>';
		echo '<form action="" method="post" id="bsptksubtopform">';
		echo '<select name="action">';
		echo '<option value="">'.__('Select Option' , 'bbp-style-pack').'</option>';
		echo '<option value="Unsubscribe">'.__('Unsubscribe from Selected Topics','bbp-style-pack').'</option>';
		echo '</select>&nbsp;&nbsp;';
		echo '<input type="submit" name="bsptksubsubmit" value="'; 
		_e('Apply', 'bbp-style-pack'); 
		echo '" />';
		echo '&nbsp;';
		bsp_not_allowed_subscription_emails ($user_id, true) ;	
		
		// Table for TOPIC unsubscribe
		$topic_ids = bbp_get_user_subscribed_topic_ids($user_id);
		echo '<table id="bsptksubtop-table" class="wp-list-table widefat striped">';
		echo '<tr><th class="check-column"><input type="checkbox" id="bsptksubtopcbgroup_master" onchange="bsptksubtoggleall(this,\'bsptksubtopcbgroup\')" /></th><th><b>ID</b></th><th><b>Topic Name</b></th><th><b>Topic Extract</b></th></tr>';
		$i = 0;
		foreach($topic_ids as $topic_id) {
			//Show table
			echo '<tr>';
			echo '<td><input type="checkbox" class="bsptksubtopcbgroup" id="bsptksubtopcb_'.$topic_id.'" name="bsptksubtopcb[]" value="' . $topic_id . '"></td>';
			echo '<td>' . $topic_id . '</td><td>' . bbp_get_topic_title($topic_id) . '</td><td>' . bsptoolkit_removehtml_and_cutwords(bbp_get_topic_content($topic_id) , 50) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo '<input type="hidden" name="user_id" value="' . $user_id . '" />';
		echo '</form>';
		echo '<br>';
		// Paging

		// Check page number from URL for topics
		$paged_topics = 1;
		if (isset($_GET['paged_topics'])) {
			$paged_topics = absint( filter_var( $_GET['paged_topics'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}

		$query = $_GET;
		$query['paged_topics'] = $paged_topics + 1;
                foreach ($query as $key=>$value) {
                        $query[$key] = ( ( is_string( $value ) || is_integer( $value ) ) ? htmlspecialchars($query[$key]) : $query[$key] );
                }
		$next_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
		if ($paged_topics > 1) {
			$query['paged_topics'] = $paged_topics - 1;
			$prev_page = site_url() . '/wp-admin/edit.php?' . http_build_query($query);
			$prev_page_sign = '&lsaquo;';
		} else {
			$prev_page = '';
			$prev_page_sign = '&nbsp;';
		}
		echo '<a class="next-page" href="' . $prev_page . '"><span class="screen-reader-text">'.__('Prev Page', 'bbp-style-pack').' </span><span class="tablenav-pages-navspan" aria-hidden="true">' . $prev_page_sign . '</span></a>';
		echo __('Page ', 'bbp-style-pack'). $paged . ' ';
		echo '<a class="next-page" href="' . $next_page . '"><span class="screen-reader-text">'.__('Next Page', 'bbp-style-pack').'</span><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></a>';
		echo '<br><br>';
}

function bsp_not_allowed_subscription_emails ($user_id, $user_management= false) {
	global $bsp_style_settings_email ;
	$allowable_roles = (!empty ($bsp_style_settings_email['email_roles']) ? $bsp_style_settings_email['email_roles'] : array());
	if (!empty ($allowable_roles) && is_array ($allowable_roles)) {
		$role = bbp_get_user_role( $user_id );
		if (!in_array ($role, $allowable_roles)) { 
			echo '<p><b>' ;
			$link = '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=email">' ;
			$link2 = '</a>' ;
			if (!empty ($user_management)) {
				echo '<h2>' ;
				printf( esc_html__( ' WARNING: The setting in item 3 in %1s settings %2s overrides these user settings. ', 'bbp-style-pack' ), $link, $link2) ; 
				printf( esc_html__('The %1s role this user has is set to not send subscription emails', 'bbp-style-pack' ), bbp_get_user_display_role($user_id) );
				echo '</h2>' ;
			}
			else {
				printf( esc_html__( ' WARNING: The setting in item 3 in %1s settings %2s overrides this individual setting. ', 'bbp-style-pack' ), $link, $link2) ; 
				printf( esc_html__('The %1s role is set to not send subscription emails', 'bbp-style-pack' ), bbp_get_user_display_role($user_id) );
			}
			echo '</b></p>' ;
		}
	}
}

