<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//where to show...
$check = true ;
if ( !empty( $attributes['bbpressOnly'] ) ) {
	//then set to false and make below turn to true
	$check = false ;
	//only show on bbpress pages or if in wdigets or page/post edits
	if (is_bbpress()) $check=true ;
	elseif (isset($_REQUEST['context'])&& $_REQUEST['context'] == 'edit') {
		$check = true ;
	}
}
//if check is false, then return ;
if (!$check) return ;
		
		echo '<div class="widget bsp-widget bsp-statistics-container">';
		
		do_action ('bsp_statistics_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
	
			echo '<span class="bsp-stats-title"><h3 class="widget-title bsp-widget-title">' .  $attributes['title']  . '</h3></span>' ;
		} 
		
		$sep = apply_filters ('bsp_stats_seperator', ': ') ;
		
		do_action ('bsp_statistics_after_title') ;

		// Get the statistics
		$stats = bbp_get_statistics(); 
		echo '<strong>' ;
		 esc_html_e( 'Registered Users', 'bbpress' ); 
			echo $sep.esc_html( $stats['user_count'] ).'<br/>';
		
		 esc_html_e( 'Forums', 'bbpress' ); 
		 echo $sep.esc_html( $stats['forum_count'] ).'<br/>';
			
		
		esc_html_e( 'Topics', 'bbpress' ); 
			echo $sep.esc_html( $stats['topic_count'] ).'<br/>';
		
		esc_html_e( 'Replies', 'bbpress' ); 
		
			echo $sep.esc_html( $stats['reply_count'] ).'<br/>';
		
		
		 if ( ! empty( $stats['topic_tag_count'] ) ) : 

		esc_html_e( 'Topic Tags', 'bbpress' ); 
		
			echo $sep.esc_html( $stats['topic_tag_count'] ).'<br/>';
		
		
		 endif; 

		 if ( ! empty( $stats['empty_topic_tag_count'] ) ) : 

			esc_html_e( 'Empty Topic Tags', 'bbpress' ); 
			
				echo $sep.esc_html( $stats['empty_topic_tag_count'] ).'<br/>';
			

		 endif; 

		 if ( ! empty( $stats['topic_count_hidden'] ) ) : 

			esc_html_e( 'Hidden Topics', 'bbpress' ); 
				echo $sep.esc_html( $stats['topic_count_hidden'] ).'<br/>';
		 endif; 

		 if ( ! empty( $stats['reply_count_hidden'] ) ) : 

			esc_html_e( 'Hidden Replies', 'bbpress' ); 
			echo $sep.esc_html( $stats['reply_count_hidden'] ).'<br/>';
			
		 endif; 

		 do_action( 'bbp_after_statistics' ); 

	

	 unset( $stats );

			
		echo '<div>' ;
	
		

