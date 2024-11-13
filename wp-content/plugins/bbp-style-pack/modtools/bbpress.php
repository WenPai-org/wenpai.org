<?php
class bspbbPressModToolsPlugin_bbPress extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();
		
		// Show a notice to users
		add_action( 'bbp_template_before_single_forum', array( $self, 'anon_pending_notice' ) );
		add_action( 'bbp_template_before_single_topic', array( $self, 'anon_pending_notice' ) );

		// Add blocked link to user details and profile
		$priority = apply_filters ('tc_moderation_priority', 10) ;
		add_action( 'bbp_theme_after_reply_author_details', array( $self, 'user_admin_links' ),$priority );
        add_action( 'bbp_theme_after_topic_author_details', array( $self, 'user_admin_links' ), $priority );
		add_action( 'bbp_template_before_user_profile', array( $self, 'user_admin_links' ), $priority );

		// Do additional actions on approval of topics
		add_action( 'bbp_approved_topic', array( $self, 'moderation_approve_action' ) );
		add_action( 'bbp_approved_reply', array( $self, 'moderation_approve_action' ) );

		remove_action( 'bbp_template_redirect', 'bbp_forum_enforce_blocked', 1  );
		add_action( 'bbp_template_redirect', array( $self, 'bbp_forum_enforce_blocked' ), 1  );

		// Redirect anon users back to parent forum
		add_filter( 'bbp_new_topic_redirect_to', array( $self, 'redirect_pending_anon' ), 20, 3 );
		add_filter( 'bbp_new_reply_redirect_to', array( $self, 'redirect_pending_anon' ), 20, 3 );

		// Include pending posts into replies and topic lists
		add_filter( 'bbp_has_topics_query', array( $self, 'pending_query' ) );
		add_filter( 'bbp_has_replies_query', array( $self, 'pending_query' ) );

		// Intercept the content and show awaiting moderation message
		add_filter( 'bbp_get_reply_content', array( $self, 'moderate_content' ), 10, 2 );
                add_filter( 'bbp_get_topic_content', array( $self, 'moderate_content' ), 10, 2 );

		// Intercept the post title and add awaiting moderation notice
		add_filter( 'bbp_get_topic_title', array( $self, 'pending_title' ), 10, 2 );

		// Disable ability to reply if topic is awaiting moderation.
		add_filter( 'bbp_current_user_can_publish_replies', array( $self, 'pending_topic_reply_check' ) );

		// Intercept the admin bar and add approve post
		add_filter( 'bbp_topic_admin_links', array( $self, 'add_moderation_links'), 10, 2 );
		add_filter( 'bbp_reply_admin_links', array( $self, 'add_moderation_links'), 10, 2 );

	}

	/**
	 * Redirect users back to parent with pending variable to display notice for anon users
	 *
	 *	@since  1.0.0
	 *
	 * @param  $redirect_url
	 * @param  $redirect_to
	 * @param  $post_id
	 *
	 * @return $redirect_url
	 *
	 */
	public function redirect_pending_anon( $redirect_url, $redirect_to, $post_id ) {

		$post = get_post( $post_id );

		if ( in_array( $post->post_type, array( 'topic', 'reply' ) ) && 'pending' == $post->post_status && $post->post_author == 0 ) {
			$redirect_url = add_query_arg(
				array( 'moderation_pending' => $post_id ),
				get_permalink( $post->post_parent )
			);
		}

		return $redirect_url;

	}

	/**
	 * Add the post status to the topic and reply query
	 *
	 *	@since  0.1.0
	 *
	 * @param  array $bbp
	 *
	 * @return array $bbp
	 */
	public function pending_query( $bbp ) {

		$user = wp_get_current_user();

		if ( ! $user->ID ) {
			return $bbp;
		}

		$bbp['post_status'] = 'pending,publish,closed,private,hidden,reported';

		$user_can_moderate = $this->user_can_moderate( $user->ID, bbp_get_forum_id() );

		if ( ( isset( $_GET['view'] ) && $_GET['view'] == 'all' ) && $user_can_moderate ) {

			$bbp['post_status'] .= ',spam,trash';

		}

		if ( ! $user_can_moderate ) {

			add_filter( 'posts_where', array( $this, 'posts_where' ) );

		}

		return $bbp;

	}

	/**
	 * Posts where...
	 *
	 *	@since 0.1.0
	 *
	 * @param str $where
	 *
	 * @return str $where
	 */
	public function posts_where( $where = '' ) {

		global $wpdb;

		$user = wp_get_current_user();

		$where = str_ireplace( $wpdb->prefix . "posts.post_status = 'pending'", "(" . $wpdb->prefix . "posts.post_status = 'pending' AND " . $wpdb->prefix . "posts.post_author = " . $user->ID . ")", $where );

		return $where;

	}

	/**
	 * Replace the content with an awaiting moderation message
	 *
	 *	@since  0.1.0
	 *
	 * @param  string $content
	 * @param  int $post_id
	 *
	 * @return string $content
	 */
	public function moderate_content( $content, $post_id ) {

		$post = get_post( $post_id );

		// Why would it be empty? no one knows, but better safe than sorry!
		if ( empty( $post ) ) {
			return $content;
		}

		if ( 'pending' == $post->post_status ) {

			$notice = '<div class="bbp-mt-template-notice">' . __( 'This post is awaiting moderation.', 'bbp-style-pack' ) . '</div>';

			if ( $this->user_can_moderate( get_current_user_ID(), bbp_get_forum_id() ) ) {

				$content = $notice . '<br>' . $content;

			} else {

				$content = $notice;

			}

		}

		return $content;

	}

	/**
	 * Add 'Block user' link into the author profile and post author
	 * @since 1.0.0
	 */
	public function user_admin_links() {
		
		global $post;
		$user_role = strtolower( bbp_get_user_display_role( get_current_user_id() ) );

		if ( $this->user_can_moderate( get_current_user_id() ) ) {
			
				$post_id = bbp_get_reply_id() ;
				$author_id = bbp_get_reply_author_id ($post_id) ;
					
			$role = bbp_get_user_display_role( $author_id );
			//if ($author_id  == 1362) update_option ('rew' , '1362 '.$role ) ;
			if ( ! in_array( $role, array( 'Blocked', 'Keymaster', 'Moderator', 'Senior Moderator' ) ) && $author_id > 0 ) {
				$action = 'block';
			} elseif ( in_array( $role, array( 'Blocked' ) ) ) {
				if ( $user_role == 'keymaster' or $user_role == 'senior moderator' ) {
					$action = 'unblock';
				}
			}

			$output = '<ul class="moderationlinks">';

			if ( isset( $action ) ) {
				$url = add_query_arg( array(
					'author_id' => $author_id,
					'action' => $this->plugin_slug . '-' . $action . '_user',
				));
				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->plugin_slug . '-wp_nonce' );
				//$confirm = '" onclick="return confirm(\'' . esc_js( esc_html__( 'Confirm you wish to block this user', 'cd' ) ). '\' );"' ;
				$confirm =  esc_html__( 'Confirm you wish to ', 'cd' ). ucfirst( $action ) . esc_html__(' User', 'bbp-style-pack') ;
				$confirm = esc_js( $confirm ) ; // '\' ) ;
				$confirm = '" onclick="return confirm(\'' . $confirm. '\' );"' ;
				$output .= '<li class="moderationlinks-block"><a href="' . $nonce_url . '" class="bbp-reply-edit-link"'.$confirm.'>' . ucfirst( $action ) . esc_html__(' User', 'bbp-style-pack') . '</a></li>';
			}

			// Add a user moderation flag link
			if ( ! in_array( $role, array( 'Keymaster', 'Moderator', 'Senior Moderator' ) ) && $author_id > 0 && ! get_user_meta( $author_id, '_bbp_moderation_flagged', true ) ) {
				$lockdown_action = 'add_moderation_flag';
				$lockdown_action_text = __( 'Add User Moderation Flag',  'bbp-style-pack' );
			} elseif ( get_user_meta( $author_id, '_bbp_moderation_flagged', true ) ) {
				$lockdown_action = 'remove_moderation_flag';
				$lockdown_action_text = __( 'Remove User Moderation Flag',  'bbp-style-pack' );
			}

			if ( isset( $lockdown_action ) ) {
				$url = add_query_arg( array(
					'author_id' => $author_id,
					'action' => $this->plugin_slug . '-' . $lockdown_action,
				));
				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->plugin_slug . '-wp_nonce' );
				$confirm =  esc_html__( 'Confirm you wish to ', 'bbp-style-pack' ).$lockdown_action_text ;
				$confirm = esc_js( $confirm ) ; // '\' ) ;
				$confirm = '" onclick="return confirm(\'' . $confirm. '\' );"' ;
				$output .= '<li class="moderationlinks-flag"><a href="' . $nonce_url . '" class="bbp-reply-edit-link"'.$confirm.'>' . $lockdown_action_text . '</a></li>';
			}

			$output .= '</ul>';
			$output = apply_filters ('bbp_user_admin_links' ,$output ) ;
			echo $output ;
		}

	}

	/**
	 * Action to add flags to users on topic/reply approval
	 *
	 * @since 1.0.0
	 *
	 * @param  $post_id
	 */
	public function moderation_approve_action( $post_id ) {

		if ( 'custom' != get_option( '_bbp_moderation_type' ) ) {
			return;
		}

		$post_author = get_post_field( 'post_author', $post_id );

		if ( 0 == $post_author ) {
			return;
		}

		$custom_moderation_options = get_option( '_bbp_moderation_custom' );

		if ( ! empty( $custom_moderation_options ) && in_array( 'links', $custom_moderation_options ) && get_post_meta( $post_id, '_bbp_moderation_link_found', true ) ) {
			update_user_meta( $post_author, '_link_moderation_approved', TRUE );
		}

		if ( ! empty( $custom_moderation_options ) && in_array( 'ascii_unnaproved', $custom_moderation_options ) && get_post_meta( $post_id, '_bbp_moderation_ascii_found', true ) ) {
			update_user_meta( $post_author, '_ascii_moderation_approved', TRUE );
		}

	}

	/**
	 * Add pending indicator to message title
	 *
	 *	@since  0.1.0
	 *
	 * @param string $title
	 * @param int $post_id
	 *
	 * @return string $title
	 *
	 */
	public function pending_title( $title, $post_id ) {

		$post = get_post( $post_id );

		// Why would it be empty? no one knows, but better safe than sorry!
		if ( empty( $post ) ) {
			return $title;
		}

		if ( 'pending' == $post->post_status ) {
			return $title . ' ('. __( 'Awaiting moderation', 'bbp-style-pack' ) . ')';
		}

		if ( $this->user_can_moderate() ) {
			$args = array(
				'post_parent' => $post_id,
				'post_type'   => 'reply',
				'numberposts' => -1,
				'post_status' => 'pending'
			);
			$children = get_children( $args );
			$child_count = count ( $children );

			if ( $child_count > 1 ) {
				return $title . ' ('. $child_count . ' ' . __( 'Replies awaiting moderation', 'bbp-style-pack' ) . ')';
			} else if ( $child_count == 1 ) {
				return $title . ' ('. $child_count . ' ' . __( 'Reply awaiting moderation', 'bbp-style-pack' ) . ')';
			}
		}

		return $title;

	}

	/**
	 * Check if the topic is waiting moderation. Disable ability to reply if it is
	 *
	 *	@since 0.1.0
	 *
	 * @param boolean $retval
	 *
	 * @return boolean - true can reply
	 */
	public function pending_topic_reply_check( $retval ) {

		if ( ! $retval ) {
			return $retval;
		}

		$topic_id = bbp_get_topic_id();
		return ( 'publish' == bbp_get_topic_status( $topic_id ) );

	}

	/**
	 * Display pending notice to anon users when submitted post is pending
	 * @since 1.0.0
	 */
	public function anon_pending_notice() {

		if ( ! empty( $_GET['moderation_pending'] ) ) {
			$post_id = absint( filter_var( $_GET['moderation_pending'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
			$post = get_post( $post_id );

			if ( in_array( $post->post_type, array( 'topic', 'reply' ) ) && 0 == $post->post_author ) {
				switch( $post->post_type ) {
					case 'topic':
						$message = __('Your topic has been submitted and is pending further moderation', 'bbp-style-pack' );
					break;
					case 'reply':
						$message = __('Your reply has been submitted and is pending further moderation', 'bbp-style-pack' );
					break;
				}

				echo '<div class="bbp-template-notice"><p>' . $message . '</p></div>';
			}
		}

	}

	/**
	 * Check if a user is blocked, or cannot spectate the forums.
	 *
	 * @since 1.0.0
	 *
	 * @uses is_user_logged_in() To check if user is logged in
	 * @uses bbp_is_user_keymaster() To check if user is a keymaster
	 * @uses current_user_can() To check if the current user can spectate
	 * @uses is_bbpress() To check if in a bbPress section of the site
	 * @uses bbp_set_404() To set a 404 status
	 */
	public function bbp_forum_enforce_blocked() {

		if ( ! is_user_logged_in() || bbp_is_user_keymaster() )
			return;

		// Redirect to custom block page or Set 404 if in bbPress and user cannot spectate
		if ( is_bbpress() && ! current_user_can( 'spectate' ) ) {

			if ( $page_id = get_option( '_bbp_blocked_page_id' ) ) {

				wp_redirect( get_permalink( $page_id ) );
				exit;

			} else {

				bbp_set_404();

			}

		}

	}

	/**
	 * Add 'approve' or 'pending' link into the mod links
	 *
	 * @since  0.1.0
	 * @since  1.0.0 Added bbPress version check to remove links being added to unapprove/approve posts
	 * @since  1.2.0 Updated to use bbp_topic_admin_links and bbp_reply_admin_links hook
	 *
	 * @param  array $links
	 * @param  int $post_id
	 *
	 * @return array $links
	 */
	public function add_moderation_links( $links, $post_id ) {

		if ( $this->user_can_moderate() ) {

			$post_status = bbp_get_topic_status( $post_id );
			$post_type = get_post_type( $post_id );
			$bbpress_version = bbp_get_version();
			$bbpress_version = explode( '-', $bbpress_version );
			$bbpress_version = reset( $bbpress_version );
			$is_pre_bbpress_2_6 = ( version_compare( '2.6', $bbpress_version ) <= 0 ) ? true : false;

			if ( 'spam' != $post_status && 'pending' == $post_status && ! $is_pre_bbpress_2_6 ) {

				$url = add_query_arg( array(
					$post_type . '_id' => $post_id,
					'action' => $this->plugin_slug . '-approve',
				));

				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->plugin_slug . '-wp_nonce' );
				array_push( $links, '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Approve', 'bbp-style-pack' ) . '</a>' );

			} else if ( 'spam' != $post_status && 'pending' != $post_status && ! $is_pre_bbpress_2_6 ) {

				$url = add_query_arg( array(
					$post_type . '_id' => $post_id,
					'action' => $this->plugin_slug . '-remove',
				));

				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->plugin_slug . '-wp_nonce' );
				array_push( $links, '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Unapprove', 'bbp-style-pack' ) . '</a>' );

			}

		}
		
		return $links;

	}

}

bspbbPressModToolsPlugin_bbPress::init();