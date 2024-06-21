<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//bail if not in single forum
		if (!bbp_is_single_forum()) return ;
		// Validate forum_id
		$forum_id = bbp_get_forum_id();
			
			
		echo '<div class="widget bsp-widget bsp-sf-container">';
		
		do_action ('bsp_single_forum_widget_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
	
			echo '<span class="bsp-sf-title"><h3 class="widget-title bsp-widget-title">' .  $attributes['title']  . '</h3></span>' ;
		} 
		
		do_action ('bsp_single_forum_widget_after_title') ;
		
		echo '<ul class="bsp-sf-info-list">';
		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_forum_permalink', 'bbp_add_view_all' );

		// Get some forum data
		$topic_count = bbp_get_forum_topic_count( $forum_id, true, true );
		$reply_count = bbp_get_forum_reply_count( $forum_id, true, true );
		$last_active = bbp_get_forum_last_active_id( $forum_id );

		// Has replies
		if ( !empty( $reply_count ) ) {
			
			$topic_count = ($topic_count>1 ? $topic_count.' '.$attributes['topics'] : $topic_count.' '.$attributes['topic'] ) ;
			$reply_count = ($reply_count>1 ? $reply_count.' '.$attributes['replies'] : $reply_count.' '.$attributes['reply'] ) ;
			
		}

		// Forum has active data
		if ( !empty( $last_active ) ) {
			$topic_text      = bbp_get_forum_topics_link( $forum_id );
			$time_since      = bbp_get_forum_freshness_link( $forum_id );

		// Forum has no last active data
		} else {
			$topic_text      = sprintf(
                                                /* translators: %s is topic count number formated as a string */
                                                _n( '%s topic', '%s topics', $topic_count, 'bbp-style-pack' ), 
                                                bbp_number_format( $topic_count ) 
                                            );
		}
	
		
		$show_iconf = (!empty ($attributes['show_icons']) ? 'show-iconf' : '' ) ;
		$show_icont = (!empty ($attributes['show_icons']) ? 'show-icont' : '' ) ;
		$show_iconr = (!empty ($attributes['show_icons']) ? 'show-iconr' : '' ) ;
		$show_iconlr = (!empty ($attributes['show_icons']) ? 'show-iconlr' : '' ) ;
		$show_iconla = (!empty ($attributes['show_icons']) ? 'show-iconla' : '' ) ;

		if ( bbp_get_forum_parent_id() ) : ?>
			<li class="topic-parent <?php echo $show_iconf ; ?> ">
			<?php echo $attributes['in'];
				printf( '<a href="%s">%s</a>',
						esc_url( bbp_get_forum_permalink( bbp_get_forum_parent_id() ) ),
						bbp_get_forum_title( bbp_get_forum_parent_id() )) ;
					?></li>
		<?php endif; ?>
		<?php if ( !empty( $topic_count ) ) : ?>
			<li class="topic-count <?php echo $show_icont ; ?> ">
			<?php echo $topic_count;  ?></li>
		<?php endif; ?>
		<?php if ( !empty( $reply_count ) ) : ?>
		<li class="reply-count <?php echo $show_iconr ; ?> ">
		<?php echo $reply_count; ?></li>
		<?php endif; ?>
		<?php if ( !empty( $last_active  ) ) : ?>
			<li class="topic-freshness-author <?php echo $show_iconlr ; ?> ">
			<?php
				echo $attributes['last_reply'];
				echo bbp_get_author_link( array( 'type' => 'name', 'post_id' => $last_active ) ) ;
			?></li>
		<?php endif; ?>
		<?php if ( !empty( $time_since  ) ) : ?>
		<li class="topic-freshness-time <?php echo $show_iconla ; ?> ">
			<?php
				echo $attributes['last_activity'];
				echo $time_since ;
			?></li>
			
		
		<?php endif; ?>
		
		<?php if ( is_user_logged_in() ) : ?>
		<?php // we add a 'button' into the array, so that this link doesn't get taken out by /includes/functions function bsp_remove_forum_subscribe_link 
		?>
			<li class="forum-subscribe"><?php bbp_forum_subscription_link( array( 'forum_id' => $forum_id, 'widget' => 'yes' ) ); ?></li>
		<?php endif;
		echo '</ul>' ;
		echo '</div>'; // end of  '<div class="bsp-st-container">'; 
