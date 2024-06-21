<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//bail if not in single topic
		if (!bbp_is_single_topic()) return ;
		
		echo '<div class="widget bsp-widget bsp-st-container">';
		
		do_action ('bsp_single_topic_widget_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
			echo '<span class="bsp-st-title"><h3 class="widget-title bsp-widget-title">' .  $attributes['title']  . '</h3></span>' ;
		} 
		
		do_action ('bsp_single_topic_widget_after_title') ;
		
		// Validate topic_id
		$topic_id = bbp_get_topic_id();

		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Build the topic description
		$voice_count = bbp_get_topic_voice_count( $topic_id, true );
		//$reply_count = bbp_get_topic_replies_link  ( $topic_id );
		$time_since  = bbp_get_topic_freshness_link( $topic_id );

		// Singular/Plural
		$voice_count = (bbp_number_format( $voice_count )>1 ? bbp_number_format( $voice_count ).' '.$attributes['participants'] : bbp_number_format( $voice_count ).' '.$attributes['participant'] ) ;
		$reply_count = (bbp_get_topic_reply_count( $topic_id)>1 ? bbp_get_topic_reply_count( $topic_id).' '.$attributes['replies'] : bbp_get_topic_reply_count( $topic_id).' '.$attributes['reply'] ) ;
		
		$last_reply  = bbp_get_topic_last_active_id( $topic_id );
		
		$show_iconf = (!empty ($attributes['show_icons']) ? 'show-iconf' : '' ) ;
		$show_iconr = (!empty ($attributes['show_icons']) ? 'show-iconr' : '' ) ;
		$show_iconv = (!empty ($attributes['show_icons']) ? 'show-iconv' : '' ) ;
		$show_iconlr = (!empty ($attributes['show_icons']) ? 'show-iconlr' : '' ) ;
		$show_iconla = (!empty ($attributes['show_icons']) ? 'show-iconla' : '' ) ;
		$show_iconfa = (!empty ($attributes['show_icons']) ? 'show-iconfa' : '' ) ;
		$show_iconsu = (!empty ($attributes['show_icons']) ? 'show-iconsu' : '' ) ;
		//then stop list style bullet points form showing if we are showing icons
		$list_style = (!empty ($attributes['show_icons']) ? 'hide-list-style' : '' ) ;
		
		echo '<ul class="bsp-st-info-list '.$list_style.'">';
		?>
		<li class="topic-forum <?php echo $show_iconf ; ?> ">
		<?php
			/* translators: %s: forum title */
			echo $attributes['in'];
			printf( '<a href="%s">%s</a>',
					esc_url( bbp_get_forum_permalink( bbp_get_topic_forum_id() ) ),
					bbp_get_topic_forum_title()
				) ;
			
		?></li>
		<?php if ( !empty( $reply_count ) ) : ?>
			<li class="reply-count <?php echo $show_iconr ; ?> ">
		<?php echo $reply_count; ?></li>
		<?php endif; ?>
		
		<?php if ( !empty( $voice_count ) ) : ?>
			<li class="voice-count <?php echo $show_iconv ; ?> ">
			
		<?php echo $voice_count; ?></li>
		<?php endif; ?>
		
		<?php if ( !empty( $last_reply  ) ) : ?>
			<li class="topic-freshness-author <?php echo $show_iconlr ; ?> ">
			<?php
				echo $attributes['last_reply'];
				echo bbp_get_author_link( array( 'type' => 'name', 'post_id' => $last_reply, 'size' => '15' ) );
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
			<?php $_topic_id = bbp_is_reply_edit() ? bbp_get_reply_topic_id() : $topic_id; ?>
			<?php // we add a 'widget' into the array, so that the subscribe link doesn't add button styling
		?>
			<li class="topic-subscribe <?php echo $show_iconfa ; ?>"><?php bbp_topic_subscription_link( array( 'before' => '', 'topic_id' => $_topic_id, 'widget' => 'yes' ) ); ?></li>
			<li class="topic-favorite <?php echo $show_iconsu ; ?>"><?php bbp_topic_favorite_link( array( 'topic_id' => $_topic_id ) ); ?></li>
			
		<?php endif;
		echo '</ul>' ; //end of '<ul class="bsp-st-info-list">'; 
		
		do_action( 'bbp_single_topic_information_widget' ); 
		echo '</div>'; //end of'<div class="bsp-st-container">';
