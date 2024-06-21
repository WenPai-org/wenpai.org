<?php
class bspbbPressModToolsPlugin {

	protected $plugin_slug = 'moderation-tools-bbpress';	
	protected $version = '1.2.0';

	public function __construct() {

		add_action( 'init', array( $this, 'on_loaded' ) );

	}

	public function on_loaded() {

		$this->update();

	}

	/**
	 * perform any updates needed where database changes have happened
	 * @since  1.0.0
	 */
	private function update() {
		
		
		$current_version = get_option( $this->plugin_slug . '-version' );

		if ( version_compare( $current_version, '0.1.3' ) == -1 ) {

			// Change moderation options
			$moderation_type = get_option( '_bbp_moderation_type' );

			if( 'links' == $moderation_type || 'users' == $moderation_type ) {

				update_option( '_bbp_moderation_type', 'custom' );
				update_option( '_bbp_moderation_custom', array( $moderation_type ) );

			}

		}

		if ( $current_version >= '1.1.0' && get_option( '_bbp_active_notification_post_held' ) === false ) {

			update_option( '_bbp_active_notification_post_held', 1 );

		}

		if ( get_option( '_bbp_moderation_post_types' ) === false ) {

			update_option( '_bbp_moderation_post_types', array( 'topic', 'reply' ) );

		}

		update_option( $this->plugin_slug . '-version', $this->version );

	}

	/**
	 * Function to check whether user can moderate the current forum. Added ready for bbPress 2.6
	 * @since 1.0.0
	 * @param  $user_id
	 * @param  $forum_id
	 * @return bool
	 */
	protected function user_can_moderate( $user_id = 0, $forum_id = 0 ) {
		
		if ( ! $user_id ) {

			$user_id = get_current_user_ID();

		}

		if ( ! $forum_id ) {

			$forum_id = bbp_get_forum_id();

		}

		$user_can_moderate = user_can( $user_id, 'moderate' );

		if ( function_exists( 'bbp_is_user_forum_moderator' ) && ! $user_can_moderate ) {

			$user_can_moderate = bbp_is_user_forum_moderator( $user_id, $forum_id );

		}

		/**
		 * Filters whether the given user can moderate a specific forum.
		 *
		 * @param bool $user_can_moderate
		 * @param int  $user_id
		 * @param int  $forum_id
		 */
		return apply_filters( 'bbp_modtools_user_can_moderate', $user_can_moderate, $user_id, $forum_id );

	}

}

$bbPressModTools = new bspbbPressModToolsPlugin();