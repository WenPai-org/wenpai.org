<?php
class bspbbPressModToolsPlugin_Moderation extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();

		// Moderate the post and mark as awaiting approval
		add_filter( 'bbp_new_topic_pre_insert', array( $self, 'moderate_post' ) );
		add_filter( 'bbp_edit_topic_pre_insert', array( $self, 'moderate_post' ) );
		add_filter( 'bbp_new_reply_pre_insert', array( $self, 'moderate_post' ) );
		add_filter( 'bbp_edit_reply_pre_insert', array( $self, 'moderate_post' ) );

		// Trigger action processing
		add_action( 'init', array( $self, 'handle_actions' ) );

	}

	/**
	 * Mark topics and replies as awaiting moderation
	 *
	 *	@since  0.1.0
	 *	@since  1.0.0 Added english detection and expanded logic to allow multiple rules
	 *
	 * @param  INT $post_id
	 */
	public function moderate_post( $post ) {

		global $wpdb;
		$pending = false;

		// Check if the topic is a reply or topic
		if ( 'reply' != $post['post_type'] && 'topic' != $post['post_type'] ) {
			return $post;
		}

		// Skip moderation if the post is marked as spam
		if ( 'spam' == $post['post_status'] ) {
			return $post;
		}

		// Skip moderation if the user has moderation power
		if ( $this->user_can_moderate() ) {
			return $post;
		}

		// Check if any moderation type is set, if not or is off return the post as is.
		$moderation_type = get_option( '_bbp_moderation_type' );
		if ( empty( $moderation_type ) || 'off' == $moderation_type ) {
			return $post;
		}

		// Check if we should be moderating the topic or reply
		$moderation_post_types = get_option( '_bbp_moderation_post_types' );
		if ( ! is_array( $moderation_post_types ) or ! in_array( $post['post_type'], $moderation_post_types ) ) {
			return $post;
		}

		// If the moderation type is set to 'all', set post status to pending and return $post
		if ( ! empty( $moderation_type ) && 'all' == $moderation_type ) {
			$pending = true;
		}

		// If user is flagged for moderation
		if ( get_user_meta( $post['post_author'], '_bbp_moderation_flagged', true ) ) {
			$pending = true;
		}

		// If moderation type is custom, run valid checks
		if ( ! empty( $moderation_type ) && 'custom' == $moderation_type ) {
			$test_content = htmlspecialchars_decode( stripslashes( $post['post_content'] ) );
			$test_title = htmlspecialchars_decode( stripslashes( $post['post_title'] ) );
			$custom_moderation_options = get_option( '_bbp_moderation_custom' );

			// Check if anon/guest posts are allowed and if the user is a guest
			if ( ! empty( $custom_moderation_options ) && in_array( 'anon', $custom_moderation_options ) ) {
				if ( $post['post_author'] === 0 ) {
					$pending = true;
				}
			}

			// Run the ascii english detection check
			if ( ! empty( $custom_moderation_options ) && ( in_array( 'ascii', $custom_moderation_options ) || in_array( 'ascii_unnaproved', $custom_moderation_options ) ) ) {
				$ascii_approved = get_user_meta( $post['post_author'], '_ascii_moderation_approved', true );

				if ( ! $ascii_approved ) {
                                        $english_arr = array();
                                        $non_english_arr = array();
                                        
					$len = strlen( $test_content );
					for ($i = 0; $i < $len; $i++) {
						$ord = ord( $test_content[$i] );
						if ( $ord == 10 || $ord == 32 || $ord == 194 || $ord == 163 ) {

						} else if ( $ord > 127 ) {
							$non_english_arr[] = $ord;
						} else {
							$english_arr[] =  $ord;
						}
					}

					$len = strlen( $test_title );
					for ($i = 0; $i < $len; $i++) {
						$ord = ord( $test_title[$i] );
						if ( $ord == 10 || $ord == 32 || $ord == 194 || $ord == 163 ) {

						} else if ( $ord > 127 ) {
							$non_english_arr[] = $ord;
						} else {
							$english_arr[] =  $ord;
						}
					}

					$english_percent = ( count( $english_arr ) / ( count( $non_english_arr ) + count( $english_arr ) ) ) * 100 ;
					$bbp_moderation_english_threshold = get_option( '_bbp_moderation_english_threshold');
					$english_threshold = ! empty( $bbp_moderation_english_threshold ) ? $bbp_moderation_english_threshold : 70;

					if ( (int) $english_percent < (int) $english_threshold ) {
						$pending = true;
					}

				}

			}

			// Moderate posts with links
			if ( ! empty( $custom_moderation_options ) && in_array( 'links', $custom_moderation_options ) ) {
				// Check if user has not had a previous post with a link approved
				if ( ! get_user_meta( $post['post_author'], '_link_moderation_approved', TRUE ) ) {
					// Check for a link in the post content
					$pattern = '#(https?\://)?(www\.)?[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(/\S*)?#';
					if ( preg_match( $pattern, $post['post_content'] ) or preg_match( $pattern, $post['post_title'] ) ) {
						$pending = true;
					}
				}
			}

			// Moderate first post if user is logged in
			if ( $post['post_author'] > 0 ) {
				if ( ! empty( $custom_moderation_options ) && in_array( 'users', $custom_moderation_options ) ) {
					// Check if user has any published posts
					$sql = $wpdb->prepare( "SELECT COUNT( ID ) FROM {$wpdb->posts} WHERE post_author = %d AND post_type IN ('topic','reply') AND post_status = 'publish'", $post['post_author'] );
					$count = $wpdb->get_var( $sql );
					if ( $count < 1 ) {
						$pending = true;
					}
				}
			}
		}

		$pending = apply_filters( 'bbp_modtools_moderate_post', $pending, $post );

		if ( $pending ) {
			$post['post_status'] = 'pending';
		}

		return $post;

	}

	/**
	 * Handle actions
	 *
	 *	@since  1.1.0
	 *
	 */
	public function handle_actions() {

		if ( ! isset( $_GET[$this->plugin_slug . '-wp_nonce'] ) or ! wp_verify_nonce( $_GET[$this->plugin_slug . '-wp_nonce'], 'moderator_action' ) )
			return;

		if ( ! isset( $_GET['action'] ) )
			return;

		switch ( $_GET['action'] ) {

			case $this->plugin_slug . '-approve':
			case $this->plugin_slug . '-remove':
				$this->handle_moderation_action( $_GET['action'] );
				break;

			case $this->plugin_slug . '-block_user':
			case $this->plugin_slug . '-unblock_user':
				$this->handle_block_action( $_GET['action'] );
				break;

			case $this->plugin_slug . '-add_moderation_flag':
			case $this->plugin_slug . '-remove_moderation_flag':
				$this->handle_moderation_flag_action( $_GET['action'] );
				break;

		}

	}

	/**
	 * Handle moderator actions
	 *
	 *	@since  0.1.0
	 *	@since  1.0.0 moved flag setting and added actions to handle flag in preperation for bbPress 2.6
	 *
	 */
	public function handle_moderation_action( $action ) {

		if ( ! $this->user_can_moderate() ) {
			return;
		}

		if ( isset( $_GET['topic_id'] ) ) {
			$post_id = absint( filter_var( $_GET['topic_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		} else if ( isset( $_GET['reply_id'] ) ) {
			$post_id = absint( filter_var( $_GET['reply_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		}

		$post = get_post( $post_id );

		if ( empty( $post ) ) {
			return;
		}

		if ( $action == $this->plugin_slug . '-approve' ) {
			// Execute pre pending code
			if ( 'topic' == $post->post_type ) {
				do_action( 'bbp_approve_topic', $post->ID );
			} else if ( 'reply' == $post->post_type ) {
				do_action( 'bbp_approve_reply', $post->ID );
			}

			wp_update_post( array(
				'ID' => $post->ID,
				'post_status' => 'publish',
			) );

			// Execute post pending code
			if ( 'topic' == $post->post_type ) {
				$forum_id = bbp_get_topic_forum_id( $post->ID );

				do_action( 'bbp_approved_topic', $post->ID );
				do_action( 'bbp_new_topic', $post->ID, $forum_id, 0, $post->post_author );
			} else if ( 'reply' == $post->post_type ) {
				$topic_id = bbp_get_reply_topic_id( $post->ID );
				$forum_id = bbp_get_topic_forum_id( $topic_id );
				$reply_to = (int) get_post_meta( $post->ID, '_bbp_reply_to', true );

				$bbp_reply_count = get_post_meta( $topic_id, '_bbp_reply_count', true );
				$bbp_last_reply_id = get_post_meta( $topic_id, '_bbp_last_reply_id', true );
				$bbp_last_active_id = get_post_meta( $topic_id, '_bbp_last_active_id', true );
				$bbp_last_active_time = get_post_meta( $topic_id, '_bbp_last_active_time', true );

				do_action( 'bbp_approved_reply', $post->ID );
				do_action( 'bbp_new_reply', $post->ID, $topic_id, $forum_id, 0, $post->post_author, true, $reply_to );

				// If this isn't the last reply, reset topic freshness to previous state
				if ( $post->menu_order != $bbp_reply_count ) {
					update_post_meta( $topic_id, '_bbp_last_reply_id', $bbp_last_reply_id );
					update_post_meta( $topic_id, '_bbp_last_active_id', $bbp_last_active_id );
					update_post_meta( $topic_id, '_bbp_last_active_time', $bbp_last_active_time );
				}
			}

		} elseif ( $action == $this->plugin_slug . '-remove' ) {
			wp_update_post( array(
				'ID' => $post->ID,
				'post_status' => 'pending',
			));
		}

		if ( $post->post_type == 'reply' ) {
			wp_redirect( remove_query_arg( array( 'reply_id', 'topic_id', 'action', $this->plugin_slug . '-wp_nonce' ), $_SERVER['REQUEST_URI'] ) );
		} else {
			wp_redirect( site_url( '?post_type=' . $post->post_type . '&p=' . $post->ID ) );
		}

		exit;

	}

	/**
	 * Handle block actions
	 *
	 *	@since  1.1.0
	 *
	 */
	public function handle_block_action( $action ) {

		if ( ! isset( $_GET['author_id'] ) ) {
			return;
		}

		$author_id = absint( filter_var( $_GET['author_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );

		if ( $action == $this->plugin_slug . '-block_user' ) {
			bbp_set_user_role( $author_id, 'bbp_blocked' );
		}

		if ( $action == $this->plugin_slug . '-unblock_user' ) {
			bbp_set_user_role( $author_id, 'bbp_participant' );
		}

	}

	/**
	 * Handle moderation flag actions
	 *
	 *	@since  1.2.0
	 *
	 */
	public function handle_moderation_flag_action( $action ) {

		if ( ! isset( $_GET['author_id'] ) ) {
			return;
		}

		$author_id = absint( filter_var( $_GET['author_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );

		if ( $action == $this->plugin_slug . '-add_moderation_flag' ) {
			update_user_meta( $author_id, '_bbp_moderation_flagged', 1 );
		}

		if ( $action == $this->plugin_slug . '-remove_moderation_flag' ) {
			delete_user_meta( $author_id, '_bbp_moderation_flagged' );
		}

	}

}

bspbbPressModToolsPlugin_Moderation::init();
