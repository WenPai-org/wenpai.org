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

//see if we have multiple forums
				// Sanitise to make sure it is valid
				$attributes['laParentForum'] = str_replace(' ', '', $attributes['laParentForum']);
				//check that laParentForum only contains numbers or numbers separated by commas
				$re = '/^\d+(?:,\d+)*$/';
				if ( !preg_match($re, $attributes['laParentForum']) ) {
				$attributes['laParentForum'] = 'all';
				}
				//check if it's all and if so set post_parent__in
				if (empty ($attributes['laParentForum'])) $attributes['laParentForum'] = '' ;
				if ($attributes['laParentForum'] == 'all' ) $attributes['post_parent__in'] =''; //to set up a null post parent in 
				//then test if it's not number (either single forum or 0 for root) - if it is, then that's also ok, so don't do further tests
				elseif ( !is_numeric( $attributes['laParentForum'] ) ) {
						//otherwise it is a list of forums (or rubbish!) so we need to create a post_parent_in array
						$attributes['post_parent__in'] = explode(",",$attributes['laParentForum']);
						$attributes['laParentForum'] = '' ; // to nullify it
					}
				//it's a single forum so 
				else $attributes['post_parent__in'] =''; //to set up a null post parent in
				
				//now check if we should actually be excluding instead of including - done this way as $attributes['laExcludeForum'] may be blank, which means we need to do the above to ensure it catches include forums if it is.
				if (!empty ($attributes['laExcludeForum'])) {
					$attributes['post_parent__in'] = '' ; // to get rid of it
					$attributes['laParentForum'] = '' ; // to nullify it
					//we should be excluding, so ...
					// Sanitise to make sure it is valid
					$attributes['laExcludedForum'] = str_replace(' ', '', $attributes['laExcludedForum']);
					//check that laExcludedForum only contains numbers or numbers separated by commas
					if ( !preg_match($re, $attributes['laExcludedForum']) ) {
					$attributes['laExcludedForum'] = '';
					}
					//check if it makes sense !
						if (is_numeric( $attributes['laExcludedForum'] ) ) $attributes['post_parent__not_in'] =  array ($attributes['laExcludedForum']) ;
						if ( !is_numeric( $attributes['laExcludedForum'] ) ) {
								//otherwise it is a list of forums (or rubbish!) so we need to create a post_parent__not_in  array
								$attributes['post_parent__not_in'] = explode(",",$attributes['laExcludedForum']);
						}
						
				}
		
		// How do we want to order our results?
		if (empty ($attributes['laOrderBy'] )) $attributes['laOrderBy']  = '';
		switch ( $attributes['laOrderBy'] ) {

			// Order by most recent replies
			case 'freshness' :
				$topics_query = array(
					'post_type'           => bbp_get_topic_post_type(),
					'post_parent'         => $attributes['laParentForum'],
					'posts_per_page'      => (int) $attributes['laMaxShown'],
					'post_status'         => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'meta_key'            => '_bbp_last_active_time',
					'orderby'             => 'meta_value',
					'order'               => 'DESC',
				);
				break;

			// Order by total number of replies
			case 'popular' :
				$topics_query = array(
					'post_type'           => bbp_get_topic_post_type(),
					'post_parent'         => $attributes['laParentForum'],
					'posts_per_page'      => (int) $attributes['laMaxShown'],
					'post_status'         => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'meta_key'            => '_bbp_reply_count',
					'orderby'             => 'meta_value',
					'order'               => 'DESC'
				);
				break;

			// Order by which topic was created most recently
			case 'newness' :
			default :
				$topics_query = array(
					'post_type'           => bbp_get_topic_post_type(),
					'post_parent'         => $attributes['laParentForum'],
					'posts_per_page'      => (int) $attributes['laMaxShown'],
					'post_status'         => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'order'               => 'DESC'
				);
				break;
		}
		//set size for avatar
		global $bsp_style_settings_la ;
		$avatar_size = (!empty($bsp_style_settings_la['AvatarSize']) ? $bsp_style_settings_la['AvatarSize']  : '14') ;
		
		
		//allow other plugin (eg private groups) to filter this query
		$topics_query = apply_filters( 'bsp_activity_widget', $topics_query ) ;
		
		// The default forum query with allowed forum ids array added
		//reset the max to be shown
		$topics_query['posts_per_page'] =(int) $attributes['laMaxShown'] ;
		
		//add any include/exclude forums ;
		if (!empty ($attributes['post_parent__not_in'])) $topics_query['post_parent__not_in'] = $attributes['post_parent__not_in'] ;
		else $topics_query['post_parent__in']= $attributes['post_parent__in'] ;
		
		// Note: private and hidden forums will be excluded via the
		// bbp_pre_get_posts_normalize_forum_visibility action and function.
		$widget_query = new WP_Query( $topics_query );
				// Bail if no topics are found
		if ( ! $widget_query->have_posts() ) {
			return;
		}
		
		echo '<div class="widget bsp-widget bsp-la-container">';
		
		do_action ('bsp_latest_activity_widget_before_title') ;

		if ( !empty( $attributes['laTitle'] ) ) {
			echo '<span class="bsp-la-title"><h3 class="widget-title bsp-widget-title">' .  $attributes['laTitle']  . '</h3></span>' ;
		} 
		
		do_action ('bsp_latest_activity_widget_after_title') ;
		
		while ( $widget_query->have_posts() ) :
				

				$widget_query->the_post();
				$topic_id    = bbp_get_topic_id( $widget_query->post->ID );
				$author_link = '';
				
				//check if this topic has a reply
				$reply = get_post_meta( $topic_id, '_bbp_last_reply_id',true);
				
				// Maybe get the topic author
				if ( ! empty( $attributes['laShowAuthor'] ) ) {
				//do we display avatar?
					if (!empty ($attributes['laHideAvatar'])) $type='name' ;
					else $type='both' ;
				//if no reply the author
				if (empty ($reply)) $author_link = bbp_get_topic_author_link( array( 'post_id' => $topic_id, 'type' => $type, 'size' => $avatar_size ) );
				//if has a reply then get the author of the reply
				else $author_link = bbp_get_reply_author_link( array( 'post_id' => $reply, 'type' => $type, 'size' => $avatar_size) );
				} ?>
				<ul>
				<li>
				<?php 
				//if no replies set the link to the topic
				if (empty ($reply)) {?>
					<a class="bsp-la-reply-topic-title" href="<?php bbp_topic_permalink( $topic_id ); ?>"><?php bbp_topic_title( $topic_id ); ?></a>
				<?php } 
				//if replies then set link to the latest reply
				else { 
					echo '<a class="bsp-la-reply-topic-title " href="' . esc_url( bbp_get_reply_url( $reply ) ) . '" title="' . esc_attr( bbp_get_reply_excerpt( $reply, 50 ) ) . '">' . bbp_get_reply_topic_title( $reply ) . '</a>';
				} ?>
				
					<?php if ( ! empty( $author_link ) ) : ?>
						<div class = "bsp-activity-author">
						<?php 
						
							if (empty($reply)) {
							echo '<span class="bsp-la-text">' ;
							echo $attributes['laTopicAuthorLabel'].'</span> <span class="bsp-la-topic-author topic-author">' . $author_link . '</span>' ; 
							}
							else {
							echo '<span class="bsp-la-text">' ;
							echo $attributes['laReplyAuthorLabel'].'</span> <span class=" bsp-la-topic-author topic-author">' . $author_link . '</span>' ; 
							} ?>
							
						</div>
						<?php endif; ?>
										
					
					<?php if (bbp_get_topic_post_type() == get_post_type()) {
									$topic = get_the_ID(); ?>
										<span class="bsp-topic-posts">
										<?php //if show author off, then add a <br>
										if (empty ($attributes['laShowAuthor'])) echo '<br>' ;
										if (!empty ($attributes ['laShowReplyCount'])) {
											echo $attributes['laReplyCountLabel'] ; 
											bbp_topic_reply_count($topic); ?>
										</span>
										<?php }
							} ?>
					
					
					

					<?php if ( ! empty( $attributes['laShowFreshness'] ) ) : ?>
					<?php $output = bbp_get_topic_last_active_time( $topic_id ) ; 
						//shorten freshness?
						if ( ! empty( $attributes['laShortenFreshness'] ) ) $output = preg_replace( '/, .*[^ago]/', ' ', $output ); ?>
						<div class = "bsp-activity-freshness"><?php 
						echo '<span class="bsp-la-freshness">'.$output. '</span>'  ;
						//bbp_topic_last_active_time( $topic_id ); ?></div>
					
					<?php endif; ?>
					
					<?php if ( ! empty( $attributes['laShowForum'] ) ) : ?>
					<div class = "bsp-activity-forum">
						<?php
						$forum = bbp_get_topic_forum_id($topic_id);
						if ( ! empty( $attributes['laShowParentForum'] ) && !empty(bbp_get_forum_parent_id($forum))  ) $forum1 = bbp_get_forum_title(bbp_get_forum_parent_id($forum)) . ' - ' . bbp_get_forum_title($forum);
						else $forum1 = bbp_get_forum_title($forum) ;
						$forum2 = esc_url( bbp_get_forum_permalink( $forum )) ;
						echo '<span class="bsp-la-text">' ;
						_e ( 'in ', 'bbp-style-pack' ) ;
						echo '</span>' ; ?>
						<a class="bsp-la-forum-title bbp-forum-title" href="<?php echo $forum2; ?>"><?php echo $forum1 ; ?></a>
					</div>
					<?php endif; ?>
				
						

					

				</li>
				</ul>
				
			<?php endwhile; ?>
			
			<?php do_action( 'bbp_after_latest_activity_widget' ); ?>
			
			
			</div>

<?php 
		
		
		
