<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// new widget to show topics, but with latest author

function register_la_widget() {
    register_widget("bsp_Activity_Widget");

}

add_action('widgets_init', 'register_la_widget');


//latest activity widget
class bsp_Activity_Widget extends WP_Widget {

	/**
	 * bbPress Topic Widget
	 *
	 * Registers the topic widget
	 *
	 * @since bbPress (r2653)
	 *
	 * @uses apply_filters() Calls 'bbp_topics_widget_options' with the
	 *                        widget options
	 */
	public function __construct() {
		$widget_ops = apply_filters( 'bsp_topics_widget_options', array(
			'classname'   => 'widget_display_topics',
			'description' => __( 'A list of recent topics, sorted by popularity or freshness with latest author.', 'bbp-style-pack' )
		) );

		parent::__construct( false, __( '(Style Pack) Latest Activity', 'bbp-style-pack' ), $widget_ops );
	}

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		register_widget( 'bsp_Activity_Widget' );
	}

	/**
	 * Displays the output, the topic list
	 *
	 * @since bbPress (r2653)
	 *
	 * @param mixed $args
	 * @param array $instance
	 * @uses apply_filters() Calls 'bbp_topic_widget_title' with the title
	 * @uses bbp_topic_permalink() To display the topic permalink
	 * @uses bbp_topic_title() To display the topic title
	 * @uses bbp_get_topic_last_active_time() To get the topic last active
	 *                                         time
	 * @uses bbp_get_topic_id() To get the topic id
	 */
	public function widget( $args = array(), $instance = array() ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance );
		
		if (!isset ($settings['topic_author_label'])) {
			$settings['topic_author_label'] =  'topic by';
			$settings['reply_author_label'] =  'reply by';
		} 

		// Typical WordPress filter
		$settings['title'] = apply_filters( 'widget_title',           $settings['title'], $instance, $this->id_base );

		// bbPress filter
		$settings['title'] = apply_filters( 'bsp_latest_activity_widget_title', $settings['title'], $instance, $this->id_base );
		
		//set default for exclude
		
		//see if we have multiple forums
				//check if it's any and if so set post_parent__in
				if ($settings['parent_forum'] == 'any' ) $settings['post_parent__in'] =''; //to set up a null post parent in 
				//then test if it's not number (either single forum or 0 for root) - if it is, then that's also ok, so don't do further tests
				elseif ( !is_numeric( $settings['parent_forum'] ) ) {
						//otherwise it is a list of forums (or rubbish!) so we need to create a post_parent_in array
						$settings['post_parent__in'] = explode(",",$settings['parent_forum']);
						$settings['parent_forum'] = '' ; // to nullify it
					}
				//it's a single forum so 
				else $settings['post_parent__in'] =''; //to set up a null post parent in
				
				//now check if we should actually be excluding instead of including - done this way as $settings['exclude_forum'] may be blank, which means we need to do the above to ensure it catches include forums if it is.
				if (!empty ($settings['exclude_forum'])) {
					$settings['post_parent__in'] = '' ; // to get rid of it
					$settings['parent_forum'] = '' ; // to nullify it
					//we should be excluding, so ...
					//check if it makes sense !
						if (is_numeric( $settings['excluded_forum'] ) ) $settings['post_parent__not_in'] =  array ($settings['excluded_forum']) ;
						if ( !is_numeric( $settings['excluded_forum'] ) ) {
								//otherwise it is a list of forums (or rubbish!) so we need to create a post_parent__not_in  array
								$settings['post_parent__not_in'] = explode(",",$settings['excluded_forum']);
						}
						
				}
		
		// How do we want to order our results?
		switch ( $settings['order_by'] ) {

			// Order by most recent replies
			case 'freshness' :
				$topics_query = array(
					'post_type'           => bbp_get_topic_post_type(),
					'post_parent'         => $settings['parent_forum'],
					'posts_per_page'      => (int) $settings['max_shown'],
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
					'post_parent'         => $settings['parent_forum'],
					'posts_per_page'      => (int) $settings['max_shown'],
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
					'post_parent'         => $settings['parent_forum'],
					'posts_per_page'      => (int) $settings['max_shown'],
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
		$topics_query['posts_per_page'] =(int) $settings['max_shown'] ;
		
		//add any include/exclude forums ;
		if (!empty ($settings['post_parent__not_in'])) $topics_query['post_parent__not_in'] = $settings['post_parent__not_in'] ;
		else $topics_query['post_parent__in']= $settings['post_parent__in'] ;
		
		// Note: private and hidden forums will be excluded via the
		// bbp_pre_get_posts_normalize_forum_visibility action and function.
		$widget_query = new WP_Query( $topics_query );
				// Bail if no topics are found
		if ( ! $widget_query->have_posts() ) {
			return;
		}
		
		

		echo $args['before_widget'];

		if ( !empty( $settings['title'] ) ) {
			echo '<span class="bsp-la-title">' . $args['before_title'] .  $settings['title'] . $args['after_title'] . '</span>' ;
		} ?>
		
		

			<?php while ( $widget_query->have_posts() ) :
				

				$widget_query->the_post();
				$topic_id    = bbp_get_topic_id( $widget_query->post->ID );
				$author_link = '';
				
				//check if this topic has a reply
				$reply = get_post_meta( $topic_id, '_bbp_last_reply_id',true);
				
				// Maybe get the topic author
				if ( ! empty( $settings['show_user'] ) ) {
				//do we display avatar?
					if (!empty ($settings['hide_avatar'])) $type='name' ;
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
							//printf( _x( 'b', 'widgets', 'bbp-style-pack' ), '</span> <span class="bsp-la-topic-author topic-author">' . $author_link . '</span>' ); 
							echo $settings['topic_author_label'].'</span> <span class="bsp-la-topic-author topic-author">' . $author_link . '</span>' ; 
							}
							else {
							echo '<span class="bsp-la-text">' ;
							//printf( _x( 'reply by %1$s', 'widgets', 'bbp-style-pack' ), '</span> <span class=" bsp-la-topic-author topic-author">' . $author_link . '</span>' ); 
							echo $settings['reply_author_label'].'</span> <span class=" bsp-la-topic-author topic-author">' . $author_link . '</span>' ; 
							} ?>
							
						</div>
						<?php endif; ?>
										
					
					<?php if ( ! empty( $settings['show_count'] ) && bbp_get_topic_post_type() == get_post_type()) {
									$topic = get_the_ID(); ?>
										<span class="bsp-topic-posts">
											<?php if ( ! empty( $settings['reply_count_label'] )) echo $settings['reply_count_label'] ; ?>
											<?php bbp_topic_reply_count($topic); ?>
										</span>
					<?php } ?>
					
					
					

					<?php if ( ! empty( $settings['show_freshness'] ) ) : ?>
					<?php $output = bbp_get_topic_last_active_time( $topic_id ) ; 
						//shorten freshness?
						if ( ! empty( $settings['shorten_freshness'] ) ) $output = preg_replace( '/, .*[^ago]/', ' ', $output ); ?>
						<div class = "bsp-activity-freshness"><?php 
						echo '<span class="bsp-la-freshness">'.$output. '</span>'  ;
						//bbp_topic_last_active_time( $topic_id ); ?></div>
					
					<?php endif; ?>
					
					<?php if ( ! empty( $settings['show_forum'] ) ) : ?>
					<div class = "bsp-activity-forum">
						<?php
						$forum = bbp_get_topic_forum_id($topic_id);
						if ( ! empty( $settings['show_parent_forum'] ) && !empty(bbp_get_forum_parent_id($forum))  ) $forum1 = bbp_get_forum_title(bbp_get_forum_parent_id($forum)) . ' - ' . bbp_get_forum_title($forum);
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

		

		<?php echo $args['after_widget'];

		// Reset the $post global
		wp_reset_postdata();
	}

	/**
	 * Update the topic widget options
	 *
	 * @since bbPress (r2653)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	public function update( $new_instance = array(), $old_instance = array() ) {
		$instance                 = $old_instance;
		$instance['title']        = (!empty ($new_instance['title']) ? strip_tags( $new_instance['title']) : '' );
		$instance['order_by']     = (!empty ($new_instance['order_by']) ? strip_tags( $new_instance['order_by']) : '' ); 
		$instance['exclude_forum']   = (!empty ($new_instance['exclude_forum']) ? (bool)( $new_instance['exclude_forum']) : '' );  
		$instance['excluded_forum']     =(!empty ($new_instance['excluded_forum']) ? sanitize_text_field( $new_instance['excluded_forum']) : '' ); 
		$instance['parent_forum'] = (!empty ($new_instance['parent_forum']) ? sanitize_text_field( $new_instance['parent_forum']) : '' );
		$instance['show_freshness']    = (!empty ($new_instance['show_freshness']) ? (bool)( $new_instance['show_freshness']) : '' );
		$instance['show_user']    = (!empty ($new_instance['show_user']) ? (bool)( $new_instance['show_user']) : '' );
		$instance['topic_author_label']    = (!empty ($new_instance['topic_author_label']) ?  $new_instance['topic_author_label'] : '' );
		$instance['reply_author_label']    = (!empty ($new_instance['reply_author_label']) ? $new_instance['reply_author_label'] : '' );
		$instance['show_forum']    = (!empty ($new_instance['show_forum']) ? (bool)( $new_instance['show_forum']) : '' );
		$instance['show_parent_forum']    = (!empty ($new_instance['show_parent_forum']) ? (bool)( $new_instance['show_parent_forum']) : '' );
		$instance['show_count']    = (!empty ($new_instance['show_count']) ? (bool)( $new_instance['show_count']) : '' );
		$instance['reply_count_label']    = (!empty ($new_instance['reply_count_label']) ? strip_tags( $new_instance['reply_count_label']) : '' );
		$instance['max_shown']    = (!empty ($new_instance['max_shown']) ? (int)( $new_instance['max_shown']) : '' );
		$instance['shorten_freshness']    = (!empty ($new_instance['shorten_freshness']) ? (int)( $new_instance['shorten_freshness']) : '' );
		$instance['hide_avatar']    = (!empty ($new_instance['hide_avatar']) ? (int)( $new_instance['hide_avatar']) : '' );

		
		
		
		//strip spaces
		$instance['parent_forum'] = str_replace(' ', '', $instance['parent_forum']);
		//check that parent_forum only contains numbers or numbers separated by commas
		$re = '/^\d+(?:,\d+)*$/';
		if ( !preg_match($re, $instance['parent_forum']) ) {
    	$instance['parent_forum'] = 'any';
		}
		
		$instance['excluded_forum'] = str_replace(' ', '', $instance['excluded_forum']);
		//check that parent_forum only contains numbers or numbers separated by commas
		if ( !preg_match($re, $instance['excluded_forum']) ) {
    	$instance['excluded_forum'] = '';
		}
		
		return $instance;
	}

	/**
	 * Output the topic widget options form
	 *
	 * @since bbPress (r2653)
	 *
	 * @param $instance Instance
	 * @uses BBP_Topics_Widget::get_field_id() To output the field id
	 * @uses BBP_Topics_Widget::get_field_name() To output the field name
	 */
	public function form( $instance = array() ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance ); ?>
		
		<p><label for="<?php echo $this->get_field_id( 'title'     ); ?>"><?php _e( 'Title:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title'     ); ?>" name="<?php echo $this->get_field_name( 'title'     ); ?>" type="text" value="<?php echo esc_attr( $settings['title']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'max_shown' ); ?>"><?php _e( 'Maximum topics to show:', 'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_shown' ); ?>" name="<?php echo $this->get_field_name( 'max_shown' ); ?>" type="text" value="<?php echo esc_attr( $settings['max_shown'] ); ?>" /></label></p>
		<hr>
		<p>
		<label for="<?php echo $this->get_field_id( 'exclude_forum' ); ?>"><input type="radio" id="<?php echo $this->get_field_id( 'exclude_forum' ); ?>" name="<?php echo $this->get_field_name( 'exclude_forum' ); ?>" <?php checked( false, $settings['exclude_forum'] ); ?> value="0" /></label>
			
			<label for="<?php echo $this->get_field_id( 'parent_forum' ); ?>"><?php _e( 'From Forum ID(s):', 'bbp-style-pack' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'parent_forum' ); ?>" name="<?php echo $this->get_field_name( 'parent_forum' ); ?>" type="text" value="<?php echo esc_attr( $settings['parent_forum'] ); ?>" />
			</label>

			<br />

			<small><?php _e( '"0" to show only root - "any" to show all - ', 'bbp-style-pack' ); ?></small>
			<small><br /><?php _e( 'a single forum eg "2921"  - or forums separated by commas eg "2921,2922"', 'bbp-style-pack' ); ?></small>
			<small><br /><?php _e( 'See dashboard>forums>all forums to find the ID of a forum', 'bbp-style-pack' ); ?></small>
			
		</p>
		<?php _e( 'OR', 'bbp-style-pack' ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_forum' ); ?>"><input type="radio" id="<?php echo $this->get_field_id( 'exclude_forum' ); ?>" name="<?php echo $this->get_field_name( 'exclude_forum' ); ?>" <?php checked( true, $settings['exclude_forum'] ); ?> value="1" /></label>
			
			<label for="<?php echo $this->get_field_id( 'excluded_forum' ); ?>"><?php _e( 'Exclude Forum ID(s):', 'bbp-style-pack' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'excluded_forum' ); ?>" name="<?php echo $this->get_field_name( 'excluded_forum' ); ?>" type="text" value="<?php echo esc_attr( $settings['excluded_forum'] ); ?>" />
			</label>

			<br />

			<small><br /><?php _e( 'a single forum eg "2921"  - or forums separated by commas eg "2921,2922"', 'bbp-style-pack' ); ?></small>
						
		</p>
		<hr>
		<p><label for="<?php echo $this->get_field_id( 'show_freshness' ); ?>"><?php _e( 'Show Freshness:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_freshness' ); ?>" name="<?php echo $this->get_field_name( 'show_freshness' ); ?>" <?php checked( true, $settings['show_freshness'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'shorten_freshness' ); ?>"><?php _e( 'Shorten freshness:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'shorten_freshness' ); ?>" name="<?php echo $this->get_field_name( 'shorten_freshness' ); ?>" <?php checked( true, $settings['shorten_freshness'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_user' ); ?>"><?php _e( 'Show topic author:', 'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_user' ); ?>" name="<?php echo $this->get_field_name( 'show_user' ); ?>" <?php checked( true, $settings['show_user'] ); ?> value="1" /></label></p>
		<label for="<?php echo $this->get_field_id( 'topic_author_label' ); ?>"><?php _e( 'Topic by Label:', 'bbp-style-pack' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'topic_author_label' ); ?>" name="<?php echo $this->get_field_name( 'topic_author_label' ); ?>" type="text" value="<?php echo $settings['topic_author_label']; ?>" />
			</label>
			<br />

			<small><?php _e( 'eg topic by, Topic Author: - etc', 'bbp-style-pack' ); ?></small>
		<p>
		<label for="<?php echo $this->get_field_id( 'reply_author_label' ); ?>"><?php _e( 'Reply by Label:', 'bbp-style-pack' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'topic_author_label' ); ?>" name="<?php echo $this->get_field_name( 'reply_author_label' ); ?>" type="text" value="<?php echo $settings['reply_author_label']; ?>" />
			</label>
			<br />

			<small><?php _e( 'eg reply by, Reply Author: - etc', 'bbp-style-pack' ); ?></small>
		<p>
		
		<p><label for="<?php echo $this->get_field_id( 'hide_avatar' ); ?>"><?php _e( 'Hide Avatar',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_avatar' ); ?>" name="<?php echo $this->get_field_name( 'hide_avatar' ); ?>" <?php checked( true, $settings['hide_avatar'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_forum' ); ?>"><?php _e( 'Show Forum:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_forum' ); ?>" name="<?php echo $this->get_field_name( 'show_forum' ); ?>" <?php checked( true, $settings['show_forum'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_parent_forum' ); ?>"><?php _e( 'Show Parent Forum:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_parent_forum' ); ?>" name="<?php echo $this->get_field_name( 'show_parent_forum' ); ?>" <?php checked( true, $settings['show_parent_forum'] ); ?> value="1" /></label> <?php _e('only shows if Show Forum is ticked','bbp-style-pack') ;?> </p>
		<p><label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Show reply count:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" <?php checked( true, $settings['show_count'] ); ?> value="1" /></label></p>
		<label for="<?php echo $this->get_field_id( 'reply_count_label' ); ?>"><?php _e( 'Reply Count Label:', 'bbp-style-pack' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'reply_count_label' ); ?>" name="<?php echo $this->get_field_name( 'reply_count_label' ); ?>" type="text" value="<?php echo $settings['reply_count_label']; ?>" />
			</label>
			<br />

			<small><?php _e( 'eg Replies:, No. Replies - etc', 'bbp-style-pack' ); ?></small>
		<p>
			<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order By:',        'bbp-style-pack' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'order_by' ); ?>" id="<?php echo $this->get_field_name( 'order_by' ); ?>">
				<option <?php selected( $settings['order_by'], 'freshness' ); ?> value="freshness"><?php _e( 'Topics With Recent Replies', 'bbp-style-pack' ); ?></option>
				<option <?php selected( $settings['order_by'], 'newness' );   ?> value="newness"><?php _e( 'Newest Topics',                'bbp-style-pack' ); ?></option>
				<option <?php selected( $settings['order_by'], 'popular' );   ?> value="popular"><?php _e( 'Popular Topics',               'bbp-style-pack' ); ?></option>
				
			</select>
		</p>

		<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since bbPress (r4802)
	 *
	 * @param $instance Instance
	 * @uses bbp_parse_args() To merge widget options into defaults
	 */
	public function parse_settings( $instance = array() ) {
		return bbp_parse_args( $instance, array(
			'title'        => __( 'Latest Activity', 'bbp-style-pack' ),
			'max_shown'    => 5,
			'show_date'    => false,
			'show_user'    => false,
			'topic_author_label' => 'topic by',
			'reply_author_label' => 'reply by',
			'exclude_forum' => false,
			'excluded_forum' => '',
			'parent_forum' => 'any',
			'show_parent_forum' => false,
			'show_freshness' => false,
			'shorten_freshness' => false,
			'hide_avatar' => false,
			'show_forum' => false,
			'show_count' => false,
			'reply_count_label' => false,
			'order_by'     => false
		), 'latest_activity_widget_settings' );
	}
}


//single topic widget

function register_single_topic_widget() {
    register_widget("bsp_Single_Topic_Widget");

}

add_action('widgets_init', 'register_single_topic_widget');



class bsp_Single_Topic_Widget extends WP_Widget {
	
	public function __construct() {
		$widget_ops = apply_filters( 'bsp_single_topic_widget_options', array(
			'classname'   => 'widget_display_single_topic',
			'description' => __( 'Display single topic widget', 'bbp-style-pack' )
		) );

		parent::__construct( false, __( '(Style Pack) Single Topic Information', 'bbp-style-pack' ), $widget_ops );
	}



	public function widget( $args = array(), $instance = array() ) { 
		//bail if not in single topic
		if (!bbp_is_single_topic()) return ;
	// Get widget settings
		$settings = $this->parse_settings( $instance );
		
		// Typical WordPress filter
		$settings['title'] = apply_filters( 'widget_title',           $settings['title'], $instance, $this->id_base );

		// bbPress filter
		$settings['title'] = apply_filters( 'bsp_single_topic_widget_title', $settings['title'], $instance, $this->id_base );
		echo '<div class="widget bsp-st-title-container">';
		
		if ( !empty( $settings['title'] ) ) {
			echo '<span class="bsp-st-title">' . $args['before_title'] .  $settings['title'] . $args['after_title'] . '</span>' ;
		} 
		// Validate topic_id
		$topic_id = bbp_get_topic_id();

		

		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Build the topic description
		$voice_count = bbp_get_topic_voice_count   ( $topic_id, true );
		//$reply_count = bbp_get_topic_replies_link  ( $topic_id );
		$time_since  = bbp_get_topic_freshness_link( $topic_id );

		// Singular/Plural
		$voice_count = (bbp_number_format( $voice_count )>1 ? bbp_number_format( $voice_count ).' '.$settings['participants'] : bbp_number_format( $voice_count ).' '.$settings['participant'] ) ;
		$reply_count = (bbp_get_topic_reply_count( $topic_id)>1 ? bbp_get_topic_reply_count( $topic_id).' '.$settings['replies'] : bbp_get_topic_reply_count( $topic_id).' '.$settings['reply'] ) ;
		
		$last_reply  = bbp_get_topic_last_active_id( $topic_id );
		
		$show_iconf = (!empty ($settings['show_icons']) ? 'show-iconf' : '' ) ;
		$show_iconr = (!empty ($settings['show_icons']) ? 'show-iconr' : '' ) ;
		$show_iconv = (!empty ($settings['show_icons']) ? 'show-iconv' : '' ) ;
		$show_iconlr = (!empty ($settings['show_icons']) ? 'show-iconlr' : '' ) ;
		$show_iconla = (!empty ($settings['show_icons']) ? 'show-iconla' : '' ) ;
		$show_iconfa = (!empty ($settings['show_icons']) ? 'show-iconfa' : '' ) ;
		$show_iconsu = (!empty ($settings['show_icons']) ? 'show-iconsu' : '' ) ;
		//then stop list style bullet points form showing if we are showing icons
		$list_style = (!empty ($settings['show_icons']) ? 'hide-list-style' : '' ) ;
		
		echo '<ul class="bsp-st-info-list '.$list_style.'">';
		?>
		<li class="topic-forum <?php echo $show_iconf ; ?> ">
		<?php
			/* translators: %s: forum title */
			echo $settings['in'];
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
				echo $settings['last_reply'];
				echo bbp_get_author_link( array( 'type' => 'name', 'post_id' => $last_reply, 'size' => '15' ) );
			?></li>
		<?php endif; ?>
		<?php if ( !empty( $time_since  ) ) : ?>
			<li class="topic-freshness-time <?php echo $show_iconla ; ?> ">
			<?php
				echo $settings['last_activity'];
				echo $time_since ;
			?></li>
		<?php endif; ?>
		
		<?php if ( is_user_logged_in() ) : ?>
			<?php $_topic_id = bbp_is_reply_edit() ? bbp_get_reply_topic_id() : $topic_id; ?>
			
			<li class="topic-subscribe <?php echo $show_iconfa ; ?>"><?php bbp_topic_subscription_link( array( 'before' => '', 'topic_id' => $_topic_id ) ); ?></li>
			<li class="topic-favorite <?php echo $show_iconsu ; ?>"><?php bbp_topic_favorite_link( array( 'topic_id' => $_topic_id ) ); ?></li>
			
		<?php endif;
		echo '</ul>' ; //end of '<ul class="bsp-st-info-list">'; 
		echo '</div>'; //end of'<div class="bsp-st-title-container">';
	}
	
	public function update( $new_instance = array(), $old_instance = array() ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['show_icons']    = (bool) $new_instance['show_icons'];
		$instance['in']        = strip_tags( $new_instance['in'] );
		$instance['reply']        = strip_tags( $new_instance['reply'] );
		$instance['replies']        = strip_tags( $new_instance['replies'] );
		$instance['participant']        = strip_tags( $new_instance['participant'] );
		$instance['participants']        = strip_tags( $new_instance['participants'] );
		$instance['last_reply']        = strip_tags( $new_instance['last_reply'] );
		$instance['last_activity']        = strip_tags( $new_instance['last_activity'] );
		
		return $instance;
		}
		
	public function form( $instance = array() ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance ); ?>
		<p> <?php _e('Note : This widget will only show on single topic pages' , 'bbp-style-pack' ) ; ?> </p>
		<p><label for="<?php echo $this->get_field_id( 'title'     ); ?>"><?php _e( 'Title:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title'     ); ?>" name="<?php echo $this->get_field_name( 'title'     ); ?>" type="text" value="<?php echo esc_attr( $settings['title']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_icons' ); ?>"><?php _e( 'Show Icons:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_icons' ); ?>" name="<?php echo $this->get_field_name( 'show_icons' ); ?>" <?php checked( true, $settings['show_icons'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'in'     ); ?>"><?php _e( 'In:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'in'     ); ?>" name="<?php echo $this->get_field_name( 'in'     ); ?>" type="text" value="<?php echo esc_attr( $settings['in']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'reply'     ); ?>"><?php _e( 'Reply:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'reply'     ); ?>" name="<?php echo $this->get_field_name( 'reply'     ); ?>" type="text" value="<?php echo esc_attr( $settings['reply']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'replies'     ); ?>"><?php _e( 'Replies:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'replies'     ); ?>" name="<?php echo $this->get_field_name( 'replies'     ); ?>" type="text" value="<?php echo esc_attr( $settings['replies']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'participant'     ); ?>"><?php _e( 'Participant:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'participant'     ); ?>" name="<?php echo $this->get_field_name( 'participant'     ); ?>" type="text" value="<?php echo esc_attr( $settings['participant']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'participants'     ); ?>"><?php _e( 'Participants:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'participants'     ); ?>" name="<?php echo $this->get_field_name( 'participants'     ); ?>" type="text" value="<?php echo esc_attr( $settings['participants']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'last_reply'     ); ?>"><?php _e( 'Last reply:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'last_reply'     ); ?>" name="<?php echo $this->get_field_name( 'last_reply'     ); ?>" type="text" value="<?php echo esc_attr( $settings['last_reply']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'last_activity'     ); ?>"><?php _e( 'Last activity:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'last_activity'     ); ?>" name="<?php echo $this->get_field_name( 'last_activity'     ); ?>" type="text" value="<?php echo esc_attr( $settings['last_activity']     ); ?>" /></label></p>
		<?php
	}
	
	public function parse_settings( $instance = array() ) {
		return bbp_parse_args( $instance, array(
			'title'        => __( 'Topic Information', 'bbp-style-pack' ),
			'show_icons'    => false,
			'in'        => __( 'In:', 'bbp-style-pack' ),
			'reply'        => __( 'Reply', 'bbp-style-pack' ),
			'replies'        => __( 'Replies', 'bbp-style-pack' ),
			'participant'        => __( 'Participant', 'bbp-style-pack' ),
			'participants'        => __( 'Participants', 'bbp-style-pack' ),
			'last_reply'        => __( 'Last Reply:', 'bbp-style-pack' ),
			'last_activity'        => __( 'Last Activity:', 'bbp-style-pack' ),
			
		), 'single_topic_information_widget_settings' );
	}
	
}

//single forum widget

function register_single_forum_widget() {
    register_widget("bsp_Single_Forum_Widget");

}

add_action('widgets_init', 'register_single_forum_widget');



class bsp_Single_Forum_Widget extends WP_Widget {
	
	public function __construct() {
		$widget_ops = apply_filters( 'bsp_single_forum_widget_options', array(
			'classname'   => 'widget_display_single_forum',
			'description' => __( 'Display single forum widget', 'bbp-style-pack' )
		) );

		parent::__construct( false, __( '(Style Pack) Single Forum Information', 'bbp-style-pack' ), $widget_ops );
	}


	public function widget( $args = array(), $instance = array() ) { 
	if (!bbp_is_single_forum()) return ;
		// Validate forum_id
		$forum_id = bbp_get_forum_id();
		// Get widget settings
		$settings = $this->parse_settings( $instance );
		
			
		// Typical WordPress filter
		$settings['title'] = apply_filters( 'widget_title',           $settings['title'], $instance, $this->id_base );

		// bbPress filter
		$settings['title'] = apply_filters( 'bsp_single_forum_widget_title', $settings['title'], $instance, $this->id_base );
		
		echo '<div class="widget bsp-sf-title-container">';
		if ( !empty( $settings['title'] ) ) {
	
			echo '<div class="bsp-sf-title">' . $args['before_title'] .  $settings['title'] . $args['after_title'] .'</div>' ;
		} 
		
		echo '<ul class="bsp-sf-info-list">';
		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_forum_permalink', 'bbp_add_view_all' );

		// Get some forum data
		$topic_count = bbp_get_forum_topic_count( $forum_id, true, true );
		$reply_count = bbp_get_forum_reply_count( $forum_id, true, true );
		$last_active = bbp_get_forum_last_active_id( $forum_id );

		// Has replies
		if ( !empty( $reply_count ) ) {
			
			$topic_count = ($topic_count>1 ? $topic_count.' '.$settings['topics'] : $topic_count.' '.$settings['topic'] ) ;
			$reply_count = ($reply_count>1 ? $reply_count.' '.$settings['replies'] : $reply_count.' '.$settings['reply'] ) ;
			
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
	
		
		$show_iconf = (!empty ($settings['show_icons']) ? 'show-iconf' : '' ) ;
		$show_icont = (!empty ($settings['show_icons']) ? 'show-icont' : '' ) ;
		$show_iconr = (!empty ($settings['show_icons']) ? 'show-iconr' : '' ) ;
		$show_iconlr = (!empty ($settings['show_icons']) ? 'show-iconlr' : '' ) ;
		$show_iconla = (!empty ($settings['show_icons']) ? 'show-iconla' : '' ) ;

		if ( bbp_get_forum_parent_id() ) : ?>
			<li class="topic-parent <?php echo $show_iconf ; ?> ">
			<?php echo $settings['in'];
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
				echo $settings['last_reply'];
				echo bbp_get_author_link( array( 'type' => 'name', 'post_id' => $last_active ) ) ;
			?></li>
		<?php endif; ?>
		<?php if ( !empty( $time_since  ) ) : ?>
		<li class="topic-freshness-time <?php echo $show_iconla ; ?> ">
			<?php
				echo $settings['last_activity'];
				echo $time_since ;
			?></li>
			
		
		<?php endif; ?>
		
		<?php if ( is_user_logged_in() ) : ?>
		<?php // we add a 'button' into the array, so that this link doesn't get taken out by /includes/functions function bsp_remove_forum_subscribe_link 
		?>
			<li class="forum-subscribe"><?php bbp_forum_subscription_link( array( 'forum_id' => $forum_id, 'buton' => 'yes') ); ?></li>
		<?php endif;
		echo '</ul>' ;
		echo '</div>'; // end of  '<div class="bsp-st-title-container">'; 
		
	}
	
	public function update( $new_instance = array(), $old_instance = array() ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['show_icons']    = (bool) $new_instance['show_icons'];
		$instance['in']        = strip_tags( $new_instance['in'] );
		$instance['reply']        = strip_tags( $new_instance['reply'] );
		$instance['replies']        = strip_tags( $new_instance['replies'] );
		$instance['topic']        = strip_tags( $new_instance['topic'] );
		$instance['topics']        = strip_tags( $new_instance['topics'] );
		$instance['last_reply']        = strip_tags( $new_instance['last_reply'] );
		$instance['last_activity']        = strip_tags( $new_instance['last_activity'] );
		
		return $instance;
		}
		
	public function form( $instance = array() ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance ); ?>
		<p> <?php _e('Note : This widget will only show on single forum pages' , 'bbp-style-pack' ) ; ?> </p>
		<p><label for="<?php echo $this->get_field_id( 'title'     ); ?>"><?php _e( 'Title:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title'     ); ?>" name="<?php echo $this->get_field_name( 'title'     ); ?>" type="text" value="<?php echo esc_attr( $settings['title']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_icons' ); ?>"><?php _e( 'Show Icons:',    'bbp-style-pack' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_icons' ); ?>" name="<?php echo $this->get_field_name( 'show_icons' ); ?>" <?php checked( true, $settings['show_icons'] ); ?> value="1" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'in'     ); ?>"><?php _e( 'In:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'in'     ); ?>" name="<?php echo $this->get_field_name( 'in'     ); ?>" type="text" value="<?php echo esc_attr( $settings['in']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'topic'     ); ?>"><?php _e( 'Topic:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'topic'     ); ?>" name="<?php echo $this->get_field_name( 'topic'     ); ?>" type="text" value="<?php echo esc_attr( $settings['topic']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'topics'     ); ?>"><?php _e( 'Topics:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'topics'     ); ?>" name="<?php echo $this->get_field_name( 'topics'     ); ?>" type="text" value="<?php echo esc_attr( $settings['topics']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'reply'     ); ?>"><?php _e( 'Reply:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'reply'     ); ?>" name="<?php echo $this->get_field_name( 'reply'     ); ?>" type="text" value="<?php echo esc_attr( $settings['reply']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'replies'     ); ?>"><?php _e( 'Replies:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'replies'     ); ?>" name="<?php echo $this->get_field_name( 'replies'     ); ?>" type="text" value="<?php echo esc_attr( $settings['replies']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'last_reply'     ); ?>"><?php _e( 'Last reply:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'last_reply'     ); ?>" name="<?php echo $this->get_field_name( 'last_reply'     ); ?>" type="text" value="<?php echo esc_attr( $settings['last_reply']     ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'last_activity'     ); ?>"><?php _e( 'Last activity:',                  'bbp-style-pack' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'last_activity'     ); ?>" name="<?php echo $this->get_field_name( 'last_activity'     ); ?>" type="text" value="<?php echo esc_attr( $settings['last_activity']     ); ?>" /></label></p>
		<?php
	}
	
	public function parse_settings( $instance = array() ) {
		return bbp_parse_args( $instance, array(
			'title'        => __( 'Topic Information', 'bbp-style-pack' ),
			'show_icons'    => false,
			'in'        => __( 'In:', 'bbp-style-pack' ),
			'reply'        => __( 'Reply', 'bbp-style-pack' ),
			'replies'        => __( 'Replies', 'bbp-style-pack' ),
			'topic'        => __( 'Topic', 'bbp-style-pack' ),
			'topics'        => __( 'Topics', 'bbp-style-pack' ),
			'last_reply'        => __( 'Last Reply:', 'bbp-style-pack' ),
			'last_activity'        => __( 'Last Activity:', 'bbp-style-pack' ),
			
		), 'single_forum_information_widget_settings' );
	}
}

//forums list widget

function register_forum_lists_widget() {
    register_widget("bsp_Forum_Lists_Widget");

}

add_action('widgets_init', 'register_forum_lists_widget');



class bsp_Forum_Lists_Widget extends WP_Widget {

	
	public function __construct() {
		$widget_ops = apply_filters( 'bbp_forums_widget_options', array(
			'classname'                   => 'bsp-widget-display-forums',
			'description'                 => esc_html__( 'A list of forums with an option to set the parent.', 'bbpress' ),
			'customize_selective_refresh' => true
		) );

		parent::__construct( false, esc_html__( '(Style Pack) Forums List', 'bbpress' ), $widget_ops );
	}

	
	public function widget( $args, $instance ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance );

		// Typical WordPress filter
		$settings['title'] = apply_filters( 'widget_title',           $settings['title'], $instance, $this->id_base );

		// bbPress filter
		$settings['title'] = apply_filters( 'bbp_forum_widget_title', $settings['title'], $instance, $this->id_base );

		// Note: private and hidden forums will be excluded via the
		// bbp_pre_get_posts_normalize_forum_visibility action and function.
		$widget_query = new WP_Query( array(

			// What and how
			'post_type'      => bbp_get_forum_post_type(),
			'post_status'    => bbp_get_public_status_id(),
			'post_parent'    => $settings['parent_forum'],
			'posts_per_page' => (int) get_option( '_bbp_forums_per_page', 50 ),

			// Order
			'orderby' => 'menu_order title',
			'order'   => 'ASC',

			// Performance
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false
		) );

		// Bail if no posts
		if ( ! $widget_query->have_posts() ) {
			return;
		}
		echo '<div class="widget bsp-sf-title-container">';
		echo $args['before_widget'];

		if ( ! empty( $settings['title'] ) ) {
			echo $args['before_title'] . $settings['title'] . $args['after_title'];
		} ?>
		<?php
		/*
		<li class="bbp-forum-info bsp-forum-info"><?php _e( 'Forum', 'bbpress' ); ?></li>
	    <li class="bsp-forum-topic-count bsp-forum-info"><?php _e( 'Posts', 'bbpress' ); ?></li>
		*/
		?>
		
		 
		<table>
		
		<tr>
			<td class="bbp-forum-info bsp-forum-info">
			<?php _e( 'Forum', 'bbpress' ); ?>
			</td>
			<td class="bsp-forum-topic-count bsp-forum-info">
			<?php _e( 'Posts', 'bbpress' ); ?>
			</td>
		</tr>
			
		<?php while ( $widget_query->have_posts() ) : $widget_query->the_post(); ?>
		<?php
		/*
				<li class="bbp-forum-info">
					<a class="bbp-forum-title" href="<?php bbp_forum_permalink($widget_query->post->ID ); ?>" title="<?php bbp_forum_title( $widget_query->post->ID ); ?>"><?php bbp_forum_title( $widget_query->post->ID ); ?></a>
				</li>
				<li class="bsp-forum-topic-count"><?php bbp_forum_post_count($widget_query->post->ID); ?>
				</li>
		*/
		?>
		
			<tr>
			<td>
			<a class="bbp-forum-title" href="<?php bbp_forum_permalink($widget_query->post->ID ); ?>" title="<?php bbp_forum_title( $widget_query->post->ID ); ?>"><?php bbp_forum_title( $widget_query->post->ID ); ?></a>
			</td>
			<td class="bsp-forum-topic-count">
			<?php bbp_forum_post_count($widget_query->post->ID); ?>
			</td>
			</tr>
			
			
		<?php endwhile; ?>
		</table>
		<br/>

	
		<?php echo $args['after_widget']; ?>
		
		
		<?php // Reset the $post global
		wp_reset_postdata();
	}

	/**
	 * Update the forum widget options
	 *
	 * @since 2.0.0 bbPress (r2653)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['parent_forum'] = sanitize_text_field( $new_instance['parent_forum'] );

		// Force to any
		if ( ! empty( $instance['parent_forum'] ) && ! is_numeric( $instance['parent_forum'] ) ) {
			$instance['parent_forum'] = 'any';
		}

		return $instance;
	}

	/**
	 * Output the forum widget options form
	 *
	 * @since 2.0.0 bbPress (r2653)
	 *
	 * @param $instance Instance
	 */
	public function form( $instance ) {

		// Get widget settings
		$settings = $this->parse_settings( $instance ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'bbpress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'parent_forum' ); ?>"><?php esc_html_e( 'Parent Forum ID:', 'bbpress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'parent_forum' ); ?>" name="<?php echo $this->get_field_name( 'parent_forum' ); ?>" type="text" value="<?php echo esc_attr( $settings['parent_forum'] ); ?>" />
			</label>

			<br />

			<small><?php esc_html_e( '"0" to show only root - "any" to show all', 'bbpress' ); ?></small>
		</p>

		<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since 2.3.0 bbPress (r4802)
	 *
	 * @param $instance Instance
	 */
	public function parse_settings( $instance = array() ) {
		return bbp_parse_args( $instance, array(
			'title'        => esc_html__( 'Forums List', 'bbp-style-pack' ),
			'parent_forum' => 0
		), 'bsp_forum_widget_settings' );
	}
}
