<?php
class bspbbPressModToolsPlugin_Report extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();

		add_filter( 'bbp_get_reply_content', array( $self, 'render_content_reports' ), 10, 2 );

		// Intercept the admin bar and add approve post
		add_filter( 'bbp_topic_admin_links', array( $self, 'add_report_link' ), 15, 2 );
		add_filter( 'bbp_reply_admin_links', array( $self, 'add_report_link' ), 15, 2 );

		// Report Post
		add_action( 'wp_ajax_bbp_report_post', array( $self, 'handle_report_post' ) );
		add_action( 'wp_ajax_nopriv_bbp_report_post', array( $self, 'handle_report_post' ) );

		// Trigger action processing
		add_action( 'init', array( $self, 'handle_clear_report_action' ) );

	}

	/**
	 * Add reported content message to topics and replies
	 *
	 *	@since  1.2.0
	 *
	 * @param  string $content
	 * @param  int $post_id
	 *
	 * @return string $content
	 */
	public function render_content_reports( $content, $post_id ) {

		if ( $this->is_reported( $post_id ) && $this->user_can_manage_reports() ) {

			$notices = '';
			$report_types = array();
			$reports = get_post_meta( $post_id, '_bbp_modtools_post_report' );

			foreach ( $reports as $report ) {

				$report_types[$report['type']] = isset( $report_types[$report['type']] ) ? $report_types[$report['type']] = $report_types[$report['type']] + 1 : 1;

			}

			uasort( $report_types, function( $a, $b ) {

				if ( $a == $b )
					return 0;

				return ( $a > $b ) ? -1 : 1;

			} );

			foreach ( $report_types as $type => $count ) {

				$notices .= '<div class="bbp-mt-template-notice">' ; 
				$notices .= $count . esc_html__( ' users reported this as ', 'bbp-style-pack') . $type . '.</div>';

			}

			$content = $notices . '<br>' . $content;

		}

		return $content;

	}

	/**
	 * Add report content link for forum users
	 *
	 * @since  1.2.0
	 *
	 * @param  array $links
	 * @param  int $r_id
	 *
	 * @return string $links
	 */
	public function add_report_link( $links, $post_id ) {

		if ( is_user_logged_in() && get_option( '_bbp_report_post' ) ) {

			array_push( $links,
				'<a href="#" class="bbp-report-link bbp-report-link-' . $post_id . '" data-post-id="' . $post_id . '">' . __( 'Report', 'bbp-style-pack' ) . '</a>
				<span class="bbp-report-type">
					<select class="bbp-report-select" data-post-id="' . $post_id . '">
					<option>' . __( 'Report reason', 'bbp-style-pack' ) . '</option>
					<option>' . __( 'Spam', 'bbp-style-pack' ) . '</option>
					<option>' . __( 'Advertising', 'bbp-style-pack' ) . '</option>
					<option>' . __( 'Harassment', 'bbp-style-pack' ) . '</option>
					<option>' . __( 'Inappropriate content', 'bbp-style-pack' ) . '</option>
					</select>
				</span>'
			);

		}

		if ( $this->is_reported( $post_id ) && $this->user_can_manage_reports() ) {

			$url = add_query_arg( array(
				'post_id' => $post_id,
				'action' => $this->plugin_slug . '-clear_reports',
			));

			$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->plugin_slug . '-wp_nonce' );
			array_push( $links, '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Clear reports', 'bbp-style-pack' ) . '</a>' );

		}

		return $links;

	}

	/**
	 * Has the post been reported?
	 *
	 *	@since  1.2.0
	 *
	 * @param  int $post_id
	 *
	 * @return bool
	 */
	private function is_reported( $post_id ) {

		$reported_count = (int) get_post_meta( $post_id, '_bbp_modtools_post_report_count', TRUE );

		if ( $reported_count > 0 )
			return TRUE;

		return FALSE;

	}

	/**
	 * Can user view/manage reported content
	 *
	 *	@since  1.2.0
	 *
	 * @param  int $user_id
	 *
	 * @return bool
	 */
	private function user_can_manage_reports( $user_id = 0 ) {

		if ( ! $user_id )
			$user_id = get_current_user_id();

		// For now, if user can moderate, they can manage reports
		$user_can_moderate = user_can( $user_id, 'moderate' );

		return $user_can_moderate;

	}

	/**
	 * Can user report content
	 *
	 *	@since  1.2.0
	 *
	 * @param  int $user_id
	 *
	 * @return bool
	 */
	private function user_can_report( $post_id = 0 ) {

		$user_can_report = FALSE;
		$user_id = get_current_user_id();

		if ( $user_id )
			$user_can_report = TRUE;

		/**
		 * Filters who can report posts
		 *
		 * @param bool $user_can_report
		 * @param string $user_id
		 * @param int    $post_id
		 */
		return apply_filters( 'bbp_modtools_user_can_report', $user_can_report, $user_id, $post_id );

	}

	/**
	* Handle report post
	*
	*	@since  1.1.0
	*/
	public function handle_report_post() {

		check_ajax_referer( 'report-post-nonce', 'nonce' );

		$post_id = absint( filter_var( $_POST['post_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) );
		$type = sanitize_text_field( $_POST['type'] );
                if ( $type !== 'Report reason' ) {
                        $this->report_post( $post_id, $type );

                        _e( 'Your report will be reviewed by our moderation team.', 'bbp-style-pack' );
                }

		die();

	}
	
	
		

	/**
	* Report post
	*
	*	@since  1.1.0
	*/
	private function report_post( $post_id, $type ) {

		$report = array(
			'date' => date( 'Y-m-d H:i:s' ),
			'user_id' => get_current_user_id(),
			'type' => $type,
		);

		$meta_id = add_post_meta( $post_id, '_bbp_modtools_post_report', $report );

		$this->increase_post_reported_count( $post_id );

		do_action( 'bbp_mod_tools_report_post', $meta_id, $post_id, $report );

	}

	/**
	* Increase post reported count
	*
	*	@since  1.1.0
	*/
	private function increase_post_reported_count( $post_id ) {

		$current = (int)get_post_meta( $post_id, '_bbp_modtools_post_report_count', TRUE ) + 1;
		update_post_meta( $post_id, '_bbp_modtools_post_report_count', $current );

	}

	/**
	 * Handle clear report action
	 *
	 *	@since  1.2.0
	 *
	 */
	public function handle_clear_report_action() {

		if ( ! isset( $_GET[$this->plugin_slug . '-wp_nonce'] ) or ! wp_verify_nonce( $_GET[$this->plugin_slug . '-wp_nonce'], 'moderator_action' ) )
			return;

		if ( ! isset( $_GET['action'] ) && $_GET['action'] != 'clear_reports' )
			return;

		if ( ! $this->user_can_manage_reports() )
			return;
		
		if ( isset( $_GET['post_id'] ) ) $this->clear_reports( absint( filter_var( $_GET['post_id'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH ) ) );

	}

	/**
	 * Clear reports
	 *
	 *	@since  1.2.0
	 *
	 */
	private function clear_reports( $post_id ) {

		delete_post_meta( $post_id, '_bbp_modtools_post_report_count' );
		delete_post_meta( $post_id, '_bbp_modtools_post_report' );

	}

}

bspbbPressModToolsPlugin_Report::init();
