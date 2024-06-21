<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $bsp_settings_admin;


//ON FORUMS : sort columns by number of topics or replies
if (!empty ( $bsp_settings_admin['activate_forum_sort'])) {
        add_filter( 'manage_edit-forum_sortable_columns', 'bsp_sortable_forum_counts' );
}

function bsp_sortable_forum_counts( $columns ) {
	//if columns have NOT been set as selectable, then bbp.. will apply
        $columns['bbp_forum_topic_count'] = 'Topics';
        $columns['bbp_forum_reply_count'] = 'Replies';
	//if columns have been set as selectable, then bsp.. will apply
        $columns['bsp_forum_topic_count'] = 'Topics';
        $columns['bsp_forum_reply_count'] = 'Replies';
        return $columns;
}

//ON FORUMS : when clicking on topics counts or replies counts, show the list of topics or replies
if (!empty ( $bsp_settings_admin['activate_forum_links'])) {
        add_filter( 'manage_forum_posts_columns', 'bsp_forum_columns_list', 20, 1 );
        add_action ('manage_forum_posts_custom_column' , 'bsp_forum_display_sort', 10 , 2) ;
}

//takes out the old columns and adds the new topic and replies linked columns
function bsp_forum_columns_list($columns) {
        $new = array();
        foreach($columns as $key => $title) {
		$new[$key] = $title;
		//change the topic column in forums
		if ($key=='bbp_forum_topic_count') {
			unset( $new['bbp_forum_topic_count'] );
			$new['bsp_forum_topic_count']     = 'Topics';
		}
		//change the replies column in topics
		if ($key=='bbp_forum_reply_count') {
			unset( $new['bbp_forum_reply_count'] );
			$new['bsp_forum_reply_count']     = 'Replies';
		}
        }
return $new ;
}

//Forum new columns
function bsp_forum_display_sort ($column, $forum_id) {
	// Populate column data
        switch ( $column ) {
        // forum
                case 'bsp_forum_topic_count' :
                echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=topic&amp;bsp_forum_id='.$forum_id.'&amp;bsp_checkf=1">'.bbp_get_forum_topic_count( $forum_id ).'</a>' ;
                break;
        // replies
                case 'bsp_forum_reply_count' :
                echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=reply&amp;bbp_forum_id='.$forum_id.'&amp;bsp_checkf=1">'.bbp_get_forum_reply_count( $forum_id ).'</a>' ;
                break;

        }
}


//ON TOPICS  : sort column by number of replies
if (!empty ( $bsp_settings_admin['activate_topic_sort'])) {
        add_filter( 'manage_edit-topic_sortable_columns', 'bsp_sortable_reply_count' );
}

function bsp_sortable_reply_count( $columns ) {
	//used if replies not sortable
	$columns['bbp_topic_reply_count'] = 'Replies';
	//used if replies are sortable
        $columns['bsp_topic_reply_count'] = 'Replies';
        return $columns;
}

//ON TOPICS : when clicking on forum, author or replies counts, show the list 
//in dashboard>topics makes the forum, replies and author item display their contents
if (!empty ( $bsp_settings_admin['activate_topic_links'])) {
	add_filter( 'manage_topic_posts_columns', 'bsp_topic_columns_list', 20, 1 );
	add_action ('manage_topic_posts_custom_column' , 'bsp_topic_display_sort', 10 , 2) ;
}

//takes out the old columns and adds the new author and replies linked columns
function bsp_topic_columns_list($columns) {
        $new = array();
        foreach($columns as $key => $title) {
		$new[$key] = $title;
		//change the author column in topics
		if ($key=='bbp_topic_author') {
			unset( $new['bbp_topic_author'] );
			$new['bsp_topic_author']     = 'Author';
		}
		//change the replies column in topics
		if ($key=='bbp_topic_reply_count') {
			unset( $new['bbp_topic_reply_count'] );
			$new['bsp_topic_reply_count']     = 'Replies';
		}
		//change the forum column in topics
		if ($key=='bbp_topic_forum') {
			unset( $new['bbp_topic_forum'] );
			$new['bsp_topic_forum']     = 'Forum';
		}
        }
    
return $new ;
}

//Topic new columns
function bsp_topic_display_sort ($column, $topic_id) {
	// Populate column data
        switch ( $column ) {
	// Author
                case 'bsp_topic_author' :
                $author_id = bbp_get_topic_author_id($topic_id );
                        echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=topic&amp;author='.$author_id.'">'.bbp_get_topic_author_display_name( $topic_id ).'</a>' ;
                        break;
		// replies
                case 'bsp_topic_reply_count' :
                        echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=reply&amp;bbp_topic_id='.$topic_id.'&amp;bsp_checkt=1">'.bbp_get_topic_reply_count( $topic_id ).'</a>' ;
                        break;
		// forum
                case 'bsp_topic_forum' :
			$forum_id = bbp_get_topic_forum_id () ;
			echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=topic&amp;bbp_forum_id='.$forum_id.'">'.bbp_get_forum_title( $forum_id ).'</a>' ;
                        break;
        }
}


//ON REPLIES  when clicking on author, show the list
if (!empty ( $bsp_settings_admin['activate_reply_links'])) {	
	add_filter( 'manage_reply_posts_columns', 'bsp_reply_columns_list', 20, 1 );
	add_action ('manage_reply_posts_custom_column' , 'bsp_reply_display_author_sort', 10 , 2) ;
}
	
function bsp_reply_columns_list($columns) {
        $new = array();
        foreach($columns as $key => $title) {
                    $new[$key] = $title;
                    //change the column
                    if ($key=='bbp_reply_author') {
                            unset( $new['bbp_reply_author'] );
                            $new['bsp_reply_author']     = 'Author';
                    }
        }

        return $new ;
}

function bsp_reply_display_author_sort ($column, $reply_id) {
	// Populate column data
        switch ( $column ) {
	// Author
                case 'bsp_reply_author' :
                $author_id = bbp_get_reply_author_id($reply_id );
                        echo '<a href="' . site_url() . '/wp-admin/edit.php?post_type=reply&amp;author='.$author_id.'">'.bbp_get_reply_author_display_name( $reply_id ).'</a>' ;
                        break;
        }
}
	

//ON USERS : sort columns by number of topics or replies or posts
//make users topics and replies sortable
if (!empty ( $bsp_settings_admin['activate_user_sort'])) {
	add_filter( 'manage_users_sortable_columns', 'bsp_sortable_users_counts' );
}
	
function bsp_sortable_users_counts( $columns ) {
        $columns['topic_count'] = 'Topics';
        $columns['reply_count'] = 'Replies';
	$columns['posts'] = 'Posts';
        return $columns;
}


//ON USERS : add user columns if needed
if (!empty ( $bsp_settings_admin['activate_user_columns'])) {
	add_filter( 'manage_users_columns', 'bsp_add_user_forum_counts', 20, 1);
        if (empty ( $bsp_settings_admin['activate_user_links'])) {
                //add unsorted row if sorting not selected
                add_filter( 'manage_users_custom_column','bsp_add_user_row', 20, 3 );
        }
}

function bsp_add_user_forum_counts($columns)  {
	$new = array();
        foreach($columns as $key => $title) {
                $new[$key] = $title;
               //add the 2 columns after the forum role column
                if ($key=='bbp_user_role') {
                      $new['topic_count'] = 'Topics';
                      $new['reply_count'] = 'Replies';
                }

        }
        return $new;
}

function bsp_add_user_row($retval = '', $column_name = '', $user_id = 0) {
	if ($column_name == 'topic_count') {
		$retval = bbp_get_user_topic_count ($user_id);
	}
	if ($column_name == 'reply_count') {
		$retval =  bbp_get_user_reply_count ($user_id);
	}
        return $retval ;
}

//ON USERS : when clicking on topic or replies counts, show the list for that user
if (!empty ( $bsp_settings_admin['activate_user_links'])) {
        add_filter( 'manage_users_custom_column','bsp_add_user_row_sortable', 20, 3 );
}

function bsp_add_user_row_sortable($retval = '', $column_name = '', $user_id = 0) {
	if ($column_name == 'topic_count') {
		$retval = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=topic&amp;author='.$user_id.'">'.bbp_get_user_topic_count ($user_id).'</a>' ;
	}
	if ($column_name == 'reply_count') {
		$retval = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=reply&amp;author='.$user_id.'">'.bbp_get_user_reply_count ($user_id).'</a>' ;
	}
        return $retval ;
}

//ON USERS : add the sort items
add_action( 'pre_get_users', 'bsp_sort_topics_replies' );

function bsp_sort_topics_replies( $query ) {
        //these entries in the user meta use a wp function 'get_user_option' which adds the blog prefix (eg 'wp_') to the meta table entry, so we need to prefix it
        global $wpdb ;
        $prefix = $wpdb->get_blog_prefix();
	if ( 'Topics' === $query->get( 'orderby') ) {
                $query->set ('meta_query', array(
                        'relation' => 'OR', // make sure it's OR
                        // Include posts that have the meta.
                        array(
                        'key' => $prefix.'_bbp_topic_count',
                        'compare' => 'EXISTS',
                        ),
                        // Include posts that don't have the meta.
                        array(
                                'key' => 'wp__bbp_topic_count',
                                'compare' => 'NOT EXISTS',
                        ),
		)) ;
		$query->set( 'orderby', 'meta_value_num' );
        }
	if ( 'Replies' === $query->get( 'orderby') ) {
		$query->set ('meta_query', array(
                        'relation' => 'OR', // make sure it's OR
                        // Include posts that have the meta.
                        array(
                                'key' => $prefix.'_bbp_reply_count',
                                'compare' => 'EXISTS',
                        ),
                        // Include posts that don't have the meta.
                        array(
                                'key' => 'wp__bbp_reply_count',
                                'compare' => 'NOT EXISTS',
                        ),
		)) ;
		$query->set( 'orderby', 'meta_value_num' );
        }

}


//ON ALL ITEMS : Add a new filter to the main options to filter the lists to show only the topics (or replies) that have been created by this user
add_action('restrict_manage_posts', 'bsp_filter_by_the_author');

function bsp_filter_by_the_author() {
	$params = array(
		'name' => 'author', // this is the "name" attribute for filter <select>
		'show_option_all' => 'All authors' // label for all authors (display posts without filter)
	);
 
	if ( isset($_GET['user']) )
		$params['selected'] = filter_var( $_GET['user'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ); // choose selected user by $_GET variable
 
	wp_dropdown_users( $params ); // print the ready author list
}


//ON ALL ITEMS
	// filter to catch the request from topics and display the filterd reply in replies	
add_filter( 'bbp_request', 'bsp_filter_admin_rows' ) ;
	
		
//this catches the link from forums, topics replies and users, and adds the filter
function bsp_filter_admin_rows( $query_vars ) {
		//the $_GET['bsp_checkxxx'] is just here to make sure this is only run from forum or topics column - otherwise errors thrown
		//on dashboard>topics - replies 
		// Add post_parent query_var if one is present
                if ( ! empty( $_GET['bbp_topic_id']) && !empty( $_GET['bsp_checkt']  )) {
                        $query_vars ['post_parent'] = absint( filter_var( $_GET['bbp_topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		// on dashboard>topics - forum 
		if ( ! empty( $_GET['bbp_forum_id']) && !empty( $_GET['bsp_checkt']  )) {
			$query_vars ['post_parent'] = absint( filter_var( $_GET['bbp_topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}
		
		// on dashboard>forum - topics
		if ( ! empty( $_GET['bsp_forum_id']) && !empty( $_GET['bsp_checkf']  )) {
			$query_vars['meta_key']   = '_bbp_forum_id';
			$query_vars['meta_value'] = absint( filter_var( $_GET['bsp_forum_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
			
		}
	
		// on on dashboard>forums  - filter topics column 
		if ( ! empty( $_GET['orderby']) && $_GET['orderby'] == 'Topics') {
			$query_vars['meta_key']   = '_bbp_total_topic_count';
			$query_vars['orderby']  = 'meta_value_num';
		}
		// on on dashboard>forums  - filter replies column  **** Note we use $_SERVER to understand that this is a call in dashboard>forums so use '_bbp_total_reply_count'
		if ( strpos($_SERVER['REQUEST_URI'], '?post_type=forum') == true ) {
			if ( ! empty( $_GET['orderby']) && $_GET['orderby'] == 'Replies' ) {
				$query_vars['meta_key']   = '_bbp_total_reply_count';
				$query_vars['orderby']  = 'meta_value_num';
			}
		}
		
		// on dashboard>topics - filter replies column **** Note we use $_SERVER to understand that this is a call in dashboard>topics so use '_bbp_reply_count'
		if ( strpos($_SERVER['REQUEST_URI'], '?post_type=topic') == true ) {
			if ( ! empty( $_GET['orderby']) && $_GET['orderby'] == 'Replies') {
				$query_vars['meta_key']   = '_bbp_reply_count';
				$query_vars['orderby']  = 'meta_value_num';
			}
		}
				
		// Return manipulated query_vars
		return $query_vars;
}

