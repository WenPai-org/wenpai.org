<?php
class bspbbPressModToolsPlugin_Notifications extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();

		// Notify Admin when a new topic or reply is posted that needs moderating.
		add_action( 'bbp_new_topic', array( $self, 'new_topic' ), 10, 1 );
		add_action( 'bbp_new_reply', array( $self, 'new_reply' ), 10, 1 );

		// Notify when a post is reported
		add_action( 'bbp_mod_tools_report_post', array( $self, 'handle_report_post_notification' ), 10, 3 );

	}

	/**
	 * Notify admin of new topic with pending status
	 * @since  0.1.0
	 *
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param boolean $anonymous_data
	 * @param int $reply_author
	 */
	public function new_topic( $topic_id = 0 ) {

		$topic_id = bbp_get_topic_id( $topic_id );
		$status = bbp_get_topic_status( $topic_id );

		if ( 'pending' == $status ) {

			$this->handle_post_moderation_notification( $topic_id );

		}

	}

	/**
	 * Notify admin of new reply with pending status
	 *
	 *	@since  0.1.0
	 *
	 * @param int $reply_id
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param boolean $anonymous_data
	 * @param int $reply_author
	 */
	public function new_reply( $reply_id = 0 ) {

		$reply_id = bbp_get_reply_id( $reply_id );
		$status = bbp_get_reply_status( $reply_id );

		if ( 'pending' == $status ) {

			$this->handle_post_moderation_notification( $reply_id );

		}

	}

	/**
	 * Send notification
	 *
	 * @since  1.1.0
	 */
	private function send_notification( $message, $type, $post_id ) {

		$blog_name = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
		$subject = sprintf( 
                                /* translators: %1$s is the string name of the blog and %2$s is the string name for the notification type */
                                __( '%1$s Moderation: %2$s', 'bbp-style-pack' ), 
                                $blog_name, 
                                $type 
                            );
		$recipients = [];

		// Check if notify moderators is on
		if ( get_option( '_bbp_notify_moderator ') ) {
			// Get list of moderators
			$moderators = get_users( array( 'role' => 'bbp_moderator' ) );
			foreach ( $moderators as $user ) {
				$recipients[] = $user->user_email;
			}

			if ( function_exists( 'bbp_get_moderator_ids' ) ) {
				$forums_moderators_ids = bbp_get_moderator_ids( bbp_get_forum_id() );
				if ( ! empty( $forums_moderators_ids ) ) {
					$forum_moderators = get_users( array( 'include' => $forums_moderators_ids ) );
					foreach ( $forum_moderators as $user ) {
						$recipients[] = $user->user_email;
					}
				}
			}
		}

		// Check if notify keymasters is on
		if ( get_option( '_bbp_notify_keymaster' ) ) {
			// Get list of keymasters
			$keymasters = get_users( array( 'role' => 'bbp_keymaster' ) );
			foreach ( $keymasters as $user ) {
				$recipients[] = $user->user_email;
			}
		}

		// Check if any custom email addresses are to be notified
		if ( get_option( '_bbp_notify_email' ) ) {
			// List of emails should be comma or semi colon separated, and a valid email address
			$emails = get_option( '_bbp_notify_email' );
			foreach ( preg_split( "/( |\;|\,)/", $emails ) as $email ) {
				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$recipients[] =  $email;
				}
			}
		}

		/**
		 * Filters the list of emails that a moderation notification will be sent to.
		 *
		 * @param array $recipients
		 * @param int   $post_id
		 */
		$recipients = apply_filters( 'bbp_modtools_notification_recipients', $recipients, $post_id );

		if ( ! empty( $recipients ) && ! empty( $message ) ) {

			add_filter( 'wp_mail_content_type', 'bsp_set_html_content_type' );
			wp_mail( $recipients, $subject, $message );

		}

	}
	
	 
	/**
	 * Notification when a post is held for moderation
	 *
	 * @since  1.1.0
	 */
	private function handle_post_moderation_notification( $post_id ) {

		if ( ! get_option( '_bbp_active_notification_post_held' ) )
			return;

		$post = get_post( $post_id );

		if ( ! $post )
			return;

		$post_link = get_permalink( $post_id );
		$post_link = apply_filters ('handle_post_moderation_notification' , $post_link ) ;

		$message = '';
		$message .= sprintf( 
                                /* translators: %1$s is string for post type (topic/reply) and %2$s is HREF URL link that post */
                                __( 'A new %1$s has been flagged for moderation: %2$s', 'bbp-style-pack' ) . "<br><br>", 
                                $post->post_type, 
                                '<a href="' . $post_link . '">' . $post_link . '</a>' 
                            );
		$message .= sprintf( 
                                /* translators: %s is string for the username */
                                __( 'User: %s', 'bbp-style-pack' ) . "<br>", 
                                $this->get_author_name( $post )
                            );
		$message .= sprintf( 
                                /* translators: %s is string for the topic title */
                                __( 'Topic: %s', 'bbp-style-pack' ) . "<br>", 
                                $this->get_title( $post )
                            );
		$message .= sprintf( 
                                /* translators: %s is string for the post content */
                                __( 'Content: %s', 'bbp-style-pack' ) . "<br>", 
                                nl2br( $post->post_content )
                            );

		/**
		 * Filters the content of the moderation email.
		 *
		 * @param string $message
		 * @param int    $post_id
		 */
		$message = apply_filters( 'bbp_modtools_notification_message', $message, $post_id );

		$this->send_notification( $message, __( 'Flagged ', 'bbp-style-pack' ) . $post->post_type, $post_id );

	}

	/**
	 * Notification for user post report
	 *
	 *	@since  1.1.0
	 *
	 * @param int $meta_id
	 * @param int $post_id
	 * @param array $report
	 */
	public function handle_report_post_notification( $meta_id, $post_id, $report ) {

		if ( ! get_option( '_bbp_active_notification_report_post' ) )
			return;

		$post = get_post( $post_id );

		if ( ! $post )
			return;

		$post_link = get_permalink( $post_id );

		if ( $report['user_id'] ) {

			$user = get_user_by( 'ID', $report['user_id'] );
			$reported_by = $user->display_name . ' - ' . $user->user_email;

		}

		$message = '';
		$message .= sprintf(
                                /* translators: %1$s is string for post type (topic/reply), %2$s is string for report reason, and %3$s is HREF URL to the post */
                                __( 'A %1$s has been reported as %2$s: %3$s', 'bbp-style-pack' ) . "<br><br>",
				$post->post_type,
				$report['type'],
				'<a href="' . $post_link . '">' . $post_link . '</a>'
                            );
		$message .= sprintf( 
                                /* translators: %s is string for username who reported the post */
                                __( 'Reported by: %s', 'bbp-style-pack' ) . "<br>", 
                                $reported_by
                            );
		$message .= sprintf( 
                                /* translators: %s is string for date post ws reported */
                                __( 'Date: %s', 'bbp-style-pack' ) . "<br>", 
                                $report['date']
                            );
		$message .= sprintf( 
                                /* translators: %s is string for the author name of the reported post */
                                __( 'OP: %s', 'bbp-style-pack' ) . "<br>", 
                                $this->get_author_name( $post )
                            );
		$message .= sprintf( 
                                __( 'Topic: %s', 'bbp-style-pack' ) . "<br>", 
                                $this->get_title( $post )
                            );
		$message .= sprintf( 
                                __( 'Content: %s', 'bbp-style-pack' ) . "<br>", 
                                nl2br( $post->post_content )
                            );

		/**
		 * Filters the content of the report post moderation email.
		 *
		 * @param string $message
		 * @param int    $post_id
		 */
		$message = apply_filters( 'bbp_modtools_report_notification_message', $message, $post_id );

		$this->send_notification( $message, __( 'Reported ', 'bbp-style-pack' ) . $post->post_type, $post_id );

	}

	/**
	 * get_author_name
	 *
	 *	@since  1.1.0
	 */
	private function get_author_name( $post ) {

		if ( $author = get_userdata( $post->post_author ) ) {
			return $author->display_name;
		}

		return __( 'Anonymous', 'bbp-style-pack' );

	}

	/**
	 * get_title
	 *
	 *	@since  1.1.0
	 */
	private function get_title( $post ) {

		$title = $post->post_title;

		// If no post title, check if it's a reply and get parent title
		if ( ! $title && 'reply' == $post->post_type ) {

			$parent_post = get_post( $post->post_parent );
			$title = 'RE: ' . $parent_post->post_title;

		}

		return $title;

	}

}

bspbbPressModToolsPlugin_Notifications::init();
