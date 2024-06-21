<?php
class bspbbPressModToolsPlugin_Admin extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();

		// Add admin pending notification counter to topics and replies
		add_action( 'admin_init', array( $self, 'admin_pending_counter' ) );

	}

	/**
	 * Add pending counter to topics and replies in admin
	 *
	 * @since  0.1.0
	 *
	 */
	public function admin_pending_counter() {
		
		global $menu, $wpdb;

		// Are there any pending items
		$sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'topic' AND post_status = 'pending'";
		$topic_count = $wpdb->get_var($sql);
		$sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'reply' AND post_status = 'pending'";
		$reply_count = $wpdb->get_var($sql);

		if ( $reply_count or $topic_count ) {

			// Add awaiting posts count next to the topic/reply menu item
			// Use in_array to find the edit post type to identify the right menu item
			if ( ! empty( $menu ) ) {
				foreach ( $menu as $key => $item ) {

					if ( $topic_count && ! empty( $item ) && in_array( 'edit.php?post_type=topic', $item ) ) {

						$bubble = '<span class="awaiting-mod count-'.$topic_count.'"><span class="pending-count">'.number_format_i18n($topic_count) .'</span></span>';
						$menu[$key][0] .= $bubble;

					}

					if ( $reply_count && ! empty( $item ) && in_array( 'edit.php?post_type=reply', $item ) ) {

						$bubble = '<span class="awaiting-mod count-'.$reply_count.'"><span class="pending-count">'.number_format_i18n($reply_count) .'</span></span>';
						$menu[$key][0] .= $bubble;

					}

				}
			}

		}

	}

}

bspbbPressModToolsPlugin_Admin::init();
