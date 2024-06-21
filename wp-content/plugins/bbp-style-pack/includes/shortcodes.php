<?php


// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// NOTE: Updated shortcodes for version 5.6.3
// All y/n and yes/no option values have been converted to true/false.
// Legacy checks included to account for y/n and yes/no to prevent breaking existing shortcode uses on sites.
// true/false now standard for shortcodes.


// shortcodes functions

add_shortcode('bsp-display-topic-index', 'bsp_display_topic_index');  
add_shortcode('bsp-display-newest-users', 'bsp_display_newest_users'); 
add_shortcode('bsp-display-forum-index', 'bsp_display_selected_forum'); 
add_shortcode ('bsp-profile', 'bsp_display_edit_profile_link') ;
add_shortcode ('bsp-forum-subscriber-count', 'bsp_forum_subscriber_count') ;
add_shortcode ('bsp-force-login', 'bsp_forum_login') ;


//add_shortcode ('bsp-display-topic' , 'bsp_display_topic' ) ;

function bsp_display_topic_index($attr, $content = '' ) {

        // Unset globals
        bsp_unset_globals();
        global $show;
        global $forum;
        global $stickies;
        global $display;
        global $noreply;


        if (!empty( $attr['show'])) $show = absint( trim( strtolower( $attr['show'] ) ) );
        if (!empty( $attr['forum'])) $forum = trim( strtolower( $attr['forum'] ) );				
        if (!empty( $attr['show_stickies'])) $stickies = trim( strtolower( $attr['show_stickies'] ) );
        if (!empty( $attr['template'])) $display = trim( strtolower( $attr['template'] ) );
        if (!empty( $attr['noreply'])) $noreply = trim( strtolower( $attr['noreply'] ) );

        // Filter the query
        if ( ! bbp_is_topic_archive() ) {
                add_filter( 'bbp_before_has_topics_parse_args', 'bsp_display_topic_index_query' );
        }

        // Start output buffer
        bsp_start( 'bbp_topic_archive' );

        // Output template
        global $display ;
        if ($display == 'short') {
                ?>
                <div id="bbpress-forums">
                <?php
                if ( bbp_is_topic_tag() ) bbp_topic_tag_description();
                        if ( bbp_has_topics() ) bbp_get_template_part( 'loop',       'topics'    ); 
                        else  bbp_get_template_part( 'feedback',   'no-topics' ); ?>
                </div>
        <?php
        }
        elseif ($display == 'list') {
                if ( bbp_has_topics() ) {
                ?>

                        <ul class="bbp-body">

                                <?php while ( bbp_topics() ) : bbp_the_topic(); ?>

                                        <li class="bsp-list"><a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a></li>

                                <?php endwhile; ?>

                        </ul>
                        <?php
                }


        }
        else bbp_get_template_part( 'content', 'archive-topic' );

        // Return contents of output buffer
        return bsp_end();
}
 
function bsp_start( $query_name = '' ) {

        // Set query name
        bbp_set_query_name( $query_name );

        // Start output buffer
        ob_start();
}
	
function bsp_end() {

        // Unset globals
        bsp_unset_globals();

        // Reset the query name
        bbp_reset_query_name();

        // Return and flush the output buffer
        return ob_get_clean();
}

function bsp_unset_globals() {
        $bbp = bbpress();

        // Unset global queries
        $bbp->forum_query  = new WP_Query();
        $bbp->topic_query  = new WP_Query();
        $bbp->reply_query  = new WP_Query();
        $bbp->search_query = new WP_Query();

        // Unset global ID's
        $bbp->current_view_id      = 0;
        $bbp->current_forum_id     = 0;
        $bbp->current_topic_id     = 0;
        $bbp->current_reply_id     = 0;
        $bbp->current_topic_tag_id = 0;

        // Reset the post data
        wp_reset_postdata();
}

function bsp_display_topic_index_query( $args = array() ) {
        global $forum;
        global $stickies;
        global $show;
        global $noreply;

        if (!empty( $forum)) {
        $forums = explode(',', $forum);
                        $args['post_parent__in'] = $forums;
                        $args['post_parent'] = '' ;
        }

        if ( !empty( $stickies ) ) {
                if ($stickies == 'true' ) $args['show_stickies'] = true;
                if ($stickies == 'false' ) $args['show_stickies'] = false;
        }
        else $args['show_stickies'] = false;

        if ( !empty( $noreply ) ) {
                if ( $noreply == 'true' ) {
                        $args['meta_query']  = array(
                        array(
                                'key'       => '_bbp_reply_count',
                                'value'     => '0',
                                'compare'   => '=',
                                'type'      => 'NUMERIC',
                                ),
                        );
                }
        }

        if ( !empty ( $show ) ) {
                $args['posts_per_page'] = $show;
                $args['max_num_pages'] = 1;
                $args['paged'] = 1;

        }
        else {
                $args['posts_per_page'] = bbp_get_topics_per_page() ;
                $args['max_num_pages'] = false;
        }

        $args['author']        = 0;
        $args['order']         = 'DESC';

        //allow private groups and/or other plugins to filter this query
        return apply_filters( 'bsp_display_topic_index_query', $args );

}
	
	

// adds a shortcode that displays the latest wordpress registered
	
	
function bsp_display_newest_users ($attr) {
        if ( is_numeric( $attr['show'] )) $number = absint( $attr['show'] );
        else $number = 5;
	$users = get_users( array( 'orderby' => 'registered', 'order' => 'desc', 'number' => $number ) ); 
	$heading1= __('Newest users','bbp-style-pack'); 
	$heading2= __('Date joined','bbp-style-pack'); 
	echo '<table><th align=left>'.$heading1.'</th><th align=left>'.$heading2.'</th>';
	
	foreach ( $users as $user ) {
		$date=date_i18n("jS M Y", strtotime( $user->user_registered ) ); 
		echo '<tr><td>' . esc_html( $user->display_name ).'</td>';
		echo '<td>'.$date.'</td>';
		echo '</tr>';
	}
	echo '</table>';
}


//adds a shortcode to display the index from a single forum
function bsp_display_selected_forum($attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['forum'] )  ) )
                //$content = 'no forum(s) set ' ;
                return $content;

        // Unset globals
        bsp_unset_globals();
        global $forum;
        if ( !empty( $attr['forum'] ) ) $forum = trim( strtolower( $attr['forum'] ) );

        if ( !empty( $attr['breadcrumb'] ) ) {
                if ( trim( strtolower( $attr['breadcrumb'] ) ) == 'no' || trim( strtolower( $attr['breadcrumb'] ) ) == 'false' ) {
                        add_filter ('bbp_no_breadcrumb', '__return_false');
                }
        }	

        if ( !empty( $attr['search'] ) ) { 
                if ( trim( strtolower( $attr['search'] ) ) == 'no' || trim( strtolower( $attr['search'] ) ) == 'false' ) {
                        add_filter ('bbp_allow_search', '__return_false');
                }
        }			


        // Filter the query
        if ( ! bbp_is_forum_archive() ) {
                add_filter( 'bbp_before_has_forums_parse_args', 'bsp_display_forum_query' );
        }

        if ( !empty( $attr['title'] ) ) {
                global $title;
                $title =  $attr['title'];
                add_filter( 'gettext', 'bsp_change_category_text', 20, 3 );
        }		

        // Start output buffer
        bsp_start( 'bbp_forum_archive' );

        // Output template
        bbp_get_template_part( 'content', 'archive-forum' );

        // Return contents of output buffer
        return bsp_end();
}
	
function bsp_display_forum_query( $args ) {
        global $forum;

        // split the string into pieces
        $forums = explode( ',', trim( $forum ) );

        $args['post__in'] = $forums;
        $args['post_parent'] = '';
        $args['orderby'] = 'post__in';

        //allow private groups and/or other plugins to filter this query
        return apply_filters( 'bsp_display_forum_query', $args );
		
}



function bsp_no_search () {
        return false;
}

function bsp_display_edit_profile_link ($atts) {
	global $bsp_login;
	if ( !is_user_logged_in() ) {
		return;
        }
        else {
		if ( empty ($atts) ) $atts = array();
                $edit = false;
                if ( !empty($atts['edit'] ) ) {
                        if ( trim( strtolower( $atts['edit'] ) ) == 'y' || trim( strtolower( $atts['edit'] ) ) == 'true' ) $edit = true;
                }
		if ( isset($atts['label'] ) ) {
			$profile = $atts['label'];
                }
                else $profile = __( 'My Profile', 'bbp-style-pack' );
		$user_id = get_current_user_id();
		$link = bbp_get_user_profile_url( $user_id );
		if ( $edit ) $link.= 'edit';
                $profilelink = '<a href="'.$link.'">'.$profile.'</a></li>';
                return $profilelink;
	}
}

function bsp_change_category_text( $translated_text, $text, $domain ) {
	global $title ;
	if ( $text == 'Forum' ) {
                $translated_text = $title ;
	}
	return $translated_text;
}


function bsp_forum_subscriber_count ($attr, $content = '') {
	//bail if no forum set
	if ( empty ( $attr['forum'] ) ) return;
	if ( empty ( $attr['before'] ) ) $attr['before'] = '';
	if ( empty ( $attr['after'] ) ) $attr['after'] = '';

	$count = 0;
	$users= get_users();
	if ( $users ) :
		foreach ( $users as $user ) {
			$user_id = $user->ID;
			$subscriptions = bbp_get_user_subscribed_forum_ids( $user_id );
			if ( in_array ( $attr['forum'], $subscriptions ) )  $count++;
		}
	endif;
	
	echo $attr['before'].$count.$attr['after'];
}


//adds a single topic without the reply form
function bsp_display_topic( $attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
                return $content;

        // Unset globals
        bsp_unset_globals();

        // Set passed attribute to $forum_id for clarity
        $topic_id = bbpress()->current_topic_id = $attr['id'];
        $forum_id = bbp_get_topic_forum_id( $topic_id );

        // Bail if ID passed is not a topic
        if ( !bbp_is_topic( $topic_id ) )
                return $content;

        // Reset the queries if not in theme compat
        if ( !bbp_is_theme_compat_active() ) {

                $bbp = bbpress();

                // Reset necessary forum_query attributes for topics loop to function
                $bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
                $bbp->forum_query->in_the_loop             = true;
                $bbp->forum_query->post                    = get_post( $forum_id );

                // Reset necessary topic_query attributes for topics loop to function
                $bbp->topic_query->query_vars['post_type'] = bbp_get_topic_post_type();
                $bbp->topic_query->in_the_loop             = true;
                $bbp->topic_query->post                    = get_post( $topic_id );
        }

        // Start output buffer
        bsp_start( 'bbp_single_topic' );

        // Check forum caps
        if ( bbp_user_can_view_forum( array( 'forum_id' => $forum_id ) ) ) {

                ?>
                <div id="bbpress-forums">

                <?php bbp_breadcrumb(); ?>

                <?php do_action( 'bbp_template_before_single_topic' ); ?>

                <?php if ( post_password_required() ) : ?>

                        <?php bbp_get_template_part( 'form', 'protected' ); ?>

                <?php else : ?>

                        <?php bbp_topic_tag_list(); ?>

                        <?php bbp_single_topic_description(); ?>

                        <?php if ( bbp_show_lead_topic() ) : ?>

                                <?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

                        <?php endif; ?>

                        <?php if ( bbp_has_replies() ) : ?>

                                <?php bbp_get_template_part( 'pagination', 'replies' ); ?>

                                <?php bbp_get_template_part( 'loop',       'replies' ); ?>

                                <?php bbp_get_template_part( 'pagination', 'replies' ); ?>

                        <?php endif; ?>


                <?php endif; ?>

                <?php do_action( 'bbp_template_after_single_topic' ); ?>

                <?php
        // Forum is private and user does not have caps
        } elseif ( bbp_is_forum_private( $forum_id, false ) ) {
                bbp_get_template_part( 'feedback', 'no-access'    );
        }

        // Return contents of output buffer
        return bsp_end();
}
	
	
	
function bsp_forum_login ($atts) {

        // Unset globals
        bsp_unset_globals();

        // Start output buffer
        bsp_start( 'bbp_login' );

        // Output templates
        if ( ! is_user_logged_in() ) {
                if (!empty($atts['message'])) {
                        echo '<p>'.($atts['message']).'</p>' ;
                }
                bbp_get_template_part( 'form',     'user-login' );
        } else {
                bbp_get_template_part( 'content', 'archive-forum' );
        }

        // Return contents of output buffer
        return bsp_end();
}
