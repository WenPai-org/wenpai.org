<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/*******************************************
* bbp topic count display
*******************************************/

//Make sure we don't expose any info if called directly
 if ( ! defined( 'ABSPATH' ) ) {
    
        // set values for bbp-topic-count
        if ( basename( __FILE__ ) == 'display.php' ) {
                $tc_textdomain = 'bbp-topic-count'; // bad practice, but still works and is necessary for code portability 
        }
        // set values for bbp-style-pack
        if ( basename( __FILE__ ) == 'functions_topic_count.php' ) {
                $tc_textdomain = 'bbp-style-pack'; // bad practice, but still works and is necessary for code portability - not used after 6.1.1
        }

	esc_html_e( "Hi there!  I'm just a plugin, not much I can do when called directly.", 'bbp-style-pack');
	exit;
        
} else {
    
        // set values for bbp-topic-count
        if ( basename( __FILE__ ) == 'display.php' ) {   
                global $tc_options;
                $tc_options_values = $tc_options;
        }

        // set values for bbp-style-pack
        if ( basename( __FILE__ ) == 'functions_topic_count.php' ) {
                global $bsp_settings_topic_count;
                $tc_options_values = $bsp_settings_topic_count;
        }
		
		
		if ( empty( $tc_options_values['location'] ) ) {
			$priority = apply_filters ('tc_topic_count_priority', 8) ;
			add_action ('bbp_theme_after_reply_author_details', 'tc_display_counts', $priority) ;
			//if lead topic showing, in search results, and pending shortcode...
			add_action ('bbp_theme_after_topic_author_details', 'tc_display_counts',$priority) ;
	
		}

		else {
			add_action ('bbp_theme_before_reply_content', 'tc_display_counts_in_reply') ;
			//if lead topic showing, in search results, and pending shortcode...
			add_action ('bbp_theme_before_topic_content', 'tc_display_counts_in_reply') ;
	
		}

           
        
        //this function hooks to BBpress loop-single-reply.php and adds the counts to the reply display
        if ( ! function_exists( 'tc_render_display_counts' ) ) {
                function tc_render_display_counts( $called_by, $location, $reply_id = 0 ) {

                        // set values for bbp-topic-count
                        if ( $called_by == 'display.php' ) {
                                global $tc_options;
                                $tc_options_values = $tc_options;
                        }

                        // set values for bbp-style-pack
                        if ( $called_by == 'functions_topic_count.php' ) {
                                global $bsp_settings_topic_count;
                                $tc_options_values = $bsp_settings_topic_count;
                        }
        
                        $user_id = bbp_get_reply_author_id( $reply_id );
                        $user_nicename = bbp_get_user_nicename( $user_id );
                        $topic_count = tc_formatted_number( bbp_get_user_topic_count_raw( $user_id ) );
                        $reply_count = tc_formatted_number( bbp_get_user_reply_count_raw( $user_id ) );
                        $post_count = tc_formatted_number( (int) bbp_get_user_topic_count_raw( $user_id ) + bbp_get_user_reply_count_raw( $user_id ) );			
                        $link_counts = ( ! empty( $tc_options_values['link_counts'] ) ? 1 : 0 );

                        if ( (bool) $link_counts ) {
                            $topic_count_string = $topic_count > 0 ? '<a href="'.esc_url( bbp_get_user_topics_created_url( $user_id ) ).'" title="'.esc_attr( $tc_options_values['topic_label'] ).' '.esc_attr( $user_nicename ).'">' . $topic_count . '</a>' : $topic_count;
                            $reply_count_string = $reply_count > 0 ? '<a href="'.esc_url( bbp_get_user_replies_created_url( $user_id ) ).'" title="'.esc_attr( $tc_options_values['reply_label'] ).' '.esc_attr( $user_nicename ).'">' . $reply_count . '</a>' : $reply_count;
                            $post_count_string = $post_count > 0 ? '<a href="'.esc_url( bbp_get_user_engagements_url( $user_id ) ).'" title="'.esc_attr( $tc_options_values['posts_label'] ).' '.esc_attr( $user_nicename ).'">' . $post_count . '</a>' : $post_count;
                        } else {
                            $topic_count_string = $topic_count;
                            $reply_count_string = $reply_count;
                            $post_count_string = $post_count;
                        } 

                        echo '<div class="tc_display">';

                        if ( $location === 'author_details' ) {
                            echo '<ul>';
                        }
                        if ( $location === 'in_reply' ) {
                            echo '<table><tr>';
                        }


        // displays topic count

                        $value = ! empty( $tc_options_values['activate_topics'] ) ? $tc_options_values['activate_topics'] : '';
                        if ( ! empty ( $value ) ) {
                                echo $location === 'author_details' ? '<li>' : '<td>';
                                        if ( empty( $tc_options_values['order'] ) ) { 
                                                echo $label1 = $tc_options_values['topic_label']." ";
                                                echo $topic_count_string;
                                        }
                                        else {
                                                echo $topic_count_string." ";
                                                echo $label1 = $tc_options_values['topic_label'];
                                        }
                                echo $location === 'author_details' ? '</li>' : '</td>';
                        }


        // displays replies count

                        $value = ! empty( $tc_options_values['activate_replies'] ) ? $tc_options_values['activate_replies'] : '';
                        if( ! empty( $value ) ) {
                                echo $location === 'author_details' ? '<li>' : '<td>';
                                        if ( empty( $tc_options_values['order'] ) ) { 
                                                echo $label2 = $tc_options_values['reply_label']." ";
                                                echo $reply_count_string;
                                        }
                                        else {
                                                echo $reply_count_string." ";
                                                echo $label2 = $tc_options_values['reply_label'];
                                        }
                                echo $location === 'author_details' ? '</li>' : '</td>';
                        }


        // displays total posts count

                        $value = ! empty( $tc_options_values['activate_posts'] ) ? $tc_options_values['activate_posts'] : '';
                        if( ! empty( $value ) ) {
                            echo $location === 'author_details' ? '<li>' : '<td>';
                               if ( empty( $tc_options_values['order'] ) ) { 
                                                echo $label3 = $tc_options_values['posts_label']." ";
                                                echo $post_count_string;
                                        }
                                        else {
                                                echo $post_count_string." ";
                                                echo $label3 = $tc_options_values['posts_label'];
                                        }
                                echo $location === 'author_details' ? '</li>' : '</td>';
                        }

        //end of list		
                        if ( $location === 'author_details' ) {
                            echo '</ul>';
                        }
                        if ( $location === 'in_reply' ) {
                            echo '</tr></table>';
                        }

                        echo "</div>";

                }
        }
}


// the function triggers the rendering of counts based on location in the author details
if ( ! function_exists( 'tc_display_counts' ) ) {
        function tc_display_counts( $reply_id = 0 ) {
                $called_by = basename( __FILE__ );
                tc_render_display_counts( $called_by, 'author_details', $reply_id );
        }
}

// the function triggers the rendering of counts based on location within replies
if ( ! function_exists( 'tc_display_counts_in_reply' ) ) {
        function tc_display_counts_in_reply( $reply_id = 0 ) {   
                $called_by = basename( __FILE__ );
                tc_render_display_counts( $called_by, 'in_reply', $reply_id );
        }
}



/*******************************************
* bbp topic count shortcodes
*******************************************/


/**********************  SHORTCODES  **********/

if ( ! shortcode_exists( 'display-topic-count' ) ) add_shortcode( 'display-topic-count', 'tc_display_topic_count' );  
if ( ! shortcode_exists( 'display-reply-count' ) ) add_shortcode( 'display-reply-count', 'tc_display_reply_count' );  
if ( ! shortcode_exists( 'display-total-count' ) ) add_shortcode( 'display-total-count', 'tc_display_total_count' );  
if ( ! shortcode_exists( 'display-top-users' ) ) add_shortcode( 'display-top-users', 'tc_display_top_users' );

if ( ! function_exists( 'tc_display_topic_count' ) ) {

        function tc_display_topic_count() {
            
                $called_by = basename( __FILE__ );
                
                // set values for bbp-topic-count
                if ( $called_by == 'shortcodes.php' ) {
                        global $tc_options;
                        $tc_options_values = $tc_options;
                }

                // set values for bbp-style-pack
                if ( $called_by == 'functions_topic_count.php' ) {
                        global $bsp_settings_topic_count;
                        $tc_options_values = $bsp_settings_topic_count;
                }
                
                $user_id = bbp_get_current_user_id();
                $topic_count = tc_formatted_number( bbp_get_user_topic_count_raw( $user_id) );
                $link_counts = ( ! empty( $tc_options_values['link_counts'] ) ? 1 : 0 );
                if ( (bool) $link_counts ) {
                        $user_profile_link = bbp_get_user_topics_created_url( $user_id );
                        $user_nicename = bbp_get_user_nicename($user_id);
                        return $topic_count > 0 ? '<a href="'.esc_url( $user_profile_link ).'" title="'.esc_attr( $tc_options_values['topic_label'] ).': '.esc_attr( $user_nicename ).'">' . $topic_count . '</a>' : $topic_count;
                }
                return $topic_count;
        }
}

if ( ! function_exists( 'tc_display_reply_count' ) ) {
        function tc_display_reply_count() {
            
                $called_by = basename( __FILE__ );
                
                // set values for bbp-topic-count
                if ( $called_by == 'shortcodes.php' ) {
                        global $tc_options;
                        $tc_options_values = $tc_options;
                }

                // set values for bbp-style-pack
                if ( $called_by == 'functions_topic_count.php' ) {
                        global $bsp_settings_topic_count;
                        $tc_options_values = $bsp_settings_topic_count;
                }
                
                $user_id = bbp_get_current_user_id();
                $reply_count = tc_formatted_number( bbp_get_user_reply_count_raw( $user_id ) );
                $link_counts = ( ! empty( $tc_options_values['link_counts'] ) ? 1 : 0 );
                if ( (bool) $link_counts ) {
                        $user_profile_link = bbp_get_user_replies_created_url( $user_id );
                        $user_nicename = bbp_get_user_nicename($user_id);
                        return $reply_count > 0 ? '<a href="'.esc_url( $user_profile_link ).'" title="'.esc_attr( $tc_options_values['reply_label'] ).': '.esc_attr( $user_nicename ).'">' . $reply_count . '</a>' : $reply_count;
                }
                return $reply_count;
        }
}

if ( ! function_exists( 'tc_display_total_count' ) ) {
        function tc_display_total_count() {
            
                $called_by = basename( __FILE__ );
                
                // set values for bbp-topic-count
                if ( $called_by == 'shortcodes.php' ) {
                        global $tc_options;
                        $tc_options_values = $tc_options;
                }

                // set values for bbp-style-pack
                if ( $called_by == 'functions_topic_count.php' ) {
                        global $bsp_settings_topic_count;
                        $tc_options_values = $bsp_settings_topic_count;
                }
                
                $user_id = bbp_get_current_user_id();
                $topic_count = bbp_get_user_topic_count_raw( $user_id );
                $reply_count = bbp_get_user_reply_count_raw( $user_id );
                $post_count = tc_formatted_number( (int) $topic_count + $reply_count );
                $link_counts = ( ! empty( $tc_options_values['link_counts'] ) ? 1 : 0 );
                if ( (bool) $link_counts ) {
                        $user_profile_link = bbp_get_user_engagements_url( $user_id );
                        $user_nicename = bbp_get_user_nicename( $user_id );
                        return $post_count > 0 ? '<a href="'.esc_url( $user_profile_link ).'" title="'.esc_attr( $tc_options_values['posts_label'] ).': '.esc_attr( $user_nicename ).'">' . $post_count . '</a>' : $post_count;
                }
                return $post_count;
        }
}

if ( ! function_exists( 'tc_display_top_users' ) ) {
        function tc_display_top_users( $attr ){
                if ( empty( $attr ) ) $attr = array();
                //set defaults
                if ( empty( $attr['show'] ) ) $attr['show'] = 5;
                if ( empty( $attr['count'] ) ) $attr['count'] = 'tr';
                if ( empty( $attr['avatar-size'] ) ) $attr['avatar-size'] = 96; 
                if ( empty( $attr['padding'] ) ) $attr['padding'] = 50;
				
				//sanitise/make sure avatar size and padding are just numeric
				if ( ! empty( $attr['avatar-size'] ) && !is_numeric( $attr['avatar-size'] ) )  $attr['avatar-size'] = 96;
				if ( ! empty( $attr['padding'] ) && !is_numeric( $attr['padding'] ) )  $attr['padding'] = 50;
				
				
				if ( ! empty( $attr['forum'] ) && is_numeric( $attr['forum'] ) ) $forum = $attr['forum'];

                //blank remainder so they exist!
                if ( empty( $attr['show-avatar'] ) ) $attr['show-avatar'] = '';
                if ( empty( $attr['show-name'] ) ) $attr['show-name'] = '';
                if ( empty( $attr['before'] ) ) $attr['before'] = '';
                if ( empty( $attr['after'] ) ) $attr['after'] = '';
                if ( empty( $attr['remove-styling'] ) ) $attr['remove-styling'] = '';
                if ( empty( $attr['hide-admins'] ) ) $attr['hide-admins'] = '';
                if ( empty( $attr['profile-link'] ) ) $attr['profile-link'] = '';

					
				
                $count = array();

                $users= get_users();
                if ( $users ) :
                        foreach ( $users as $user ) {
                                $topic_count = $reply_count = 0;
                                $user_id = $user->ID;
                                if ( $attr['hide-admins'] == 'yes' && user_can( $user_id, 'administrator' ) ) continue;
                                if ( empty ( $attr['forum'] ) ) {
                                        if ( strpos ( $attr['count'], "t" ) !== false ) $topic_count = bbp_get_user_topic_count_raw( $user_id );
                                        if ( strpos ( $attr['count'], "r" ) !== false ) $reply_count = bbp_get_user_reply_count_raw( $user_id );
                                }
                                else {  //we have an individual forum
                                        if ( strpos ( $attr['count'], "t" ) !== false) $topic_count = tc_topic_count_by_forum( $user_id, $forum );
                                        if ( strpos ( $attr['count'], "r" ) !== false ) $reply_count = tc_reply_count_by_forum( $user_id, $forum );
                                }
                                $count[$user_id] = (int) $topic_count + $reply_count;	
                }
                endif;

                //re-sort into descending order
                arsort($count);

                //set up in-line styling
                if ( $attr['remove-styling'] !="yes" ) {
                        $css1 = 'style="float:left;';
                        $css2 = 'style="padding-left:'.$attr['padding'].'px;height:'.$attr['avatar-size'].'px"';
                        $css3 = 'style="padding-left:'.$attr['padding'].'px; top: 50%;transform: translateY(-50%);position: relative;"';
                }
                else
                        $css1 = $css2 = $css3 = '';


                $i=0;
                $output='';
                foreach( $count as $user_id => $value ) {
                        $i++; 

                        //stop if we have users with 0
                        if ( $value == 0 ) break;
                        $output.= '<div class="tc-user">';
                        if ( $attr['show-avatar'] !='no' ) {
                                $output.= '<div class="tc-avatar"'.$css1.'">';
                                if ( $attr['profile-link'] == 'no' ) $output.= get_avatar( $user_id, $attr['avatar-size'] ); 
                                else $output.= '<a class="tc-profile-link" href="' . esc_url( bbp_get_user_profile_url( $user_id ) ) . '">' . get_avatar( $user_id, $attr['avatar-size']) . '</a>';
                                $output.= '</div>';
                        }
                        $output.= '<div class="tc-wrapper"'.$css2.'>';
                        $output.= '<div class="tc-content"'.$css3.'>';
                        if ( $attr['show-name'] !='no' ) {
                                if ( $attr['profile-link'] == 'no' ) $output.= get_the_author_meta ( 'display_name', $user_id );
                                else $output.= '<a class="tc-profile-link" href="' . esc_url( bbp_get_user_profile_url( $user_id ) ) . '">' . get_the_author_meta ( 'display_name', $user_id ) . '</a>';
                        }
                        //the content!!
                        $output.= $attr['before'].tc_formatted_number( $value ).$attr['after'] ;
                        $output.= "</div></div><br>";
                        $output.= '</div>';
                if ( $i == $attr['show'] ) break;
                }
                return $output;
        }
}

if ( ! function_exists( 'tc_topic_count_by_forum' ) ) {
        function tc_topic_count_by_forum( $user_id, $forum ) {
                global $wpdb;
                $where = get_posts_by_author_sql( bbp_get_topic_post_type(), true, $user_id );
                $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} $where AND post_parent = '$forum' " );

                //$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} $where post_parent = '$forum' AND post_type = '$forumt' AND post_author = '$user_id' " );
                return $count;
        }
}

if ( ! function_exists( 'tc_topic_count_by_forum' ) ) {
        function tc_reply_count_by_forum( $user_id, $forum ) {
                global $wpdb;
                $type = bbp_get_reply_post_type();
                //$where = get_posts_by_author_sql( bbp_get_reply_post_type(), true, $user_id );
                $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts, $wpdb->postmeta	
                WHERE $wpdb->posts.post_type = '$type'
                AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' )
                AND $wpdb->posts.post_author = '$user_id'
                AND $wpdb->posts.ID = $wpdb->postmeta.post_id 
                AND $wpdb->postmeta.meta_key = '_bbp_forum_id'
                AND $wpdb->postmeta.meta_value = '$forum' " );
                return $count;
        }
}
