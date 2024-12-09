<?php
/**
 * Branda Dashboard Feeds Widget class.
 *
 * @package Branda
 * @subpackage Widgets
 */
if ( ! class_exists( 'Branda_Dashboard_Feeds_Widget' ) ) {

	class Branda_Dashboard_Feeds_Widget {
		var $widget_id;
		var $widget_options;

		public function __construct() {
		}

		public function init( $options_set = '', $options = array() ) {
			if ( empty( $options_set ) ) {
				return;
			}
			if ( empty( $options ) ) {
				return;
			}
			if ( strlen( $options_set ) ) {
				$this->widget_id   = 'wpmudev_dashboard_item_' . $options_set;
				$options['number'] = $options_set;
			}
			foreach ( $options as $key => $value ) {
				if ( preg_match( '/^(on|show|full)$/', $value ) ) {
					$options[ $key ] = true;
				}
				if ( preg_match( '/^(off|hide|except)$/', $value ) ) {
					$options[ $key ] = false;
				}
			}
			$this->widget_options = $options;
			/**
			 * setup widget title if is not defined
			 */
			$title = $this->widget_options['title'];
			if ( empty( $title ) ) {
				if ( ! empty( $this->widget_options['link'] ) ) {
					$title = $this->widget_options['link'];
				}
				if ( empty( $title ) ) {
					$title = $this->widget_options['url'];
				}
				$title = preg_replace( '/^[^\/]+\/\//', '', $title );
				$title = preg_replace( '/\/.*/', '', $title );
			}
			if ( empty( $title ) ) {
				$title = __( '[no title]', 'ub' );
			}
			/**
			 * Decode url
			 */
			$this->widget_options['url'] = htmlspecialchars_decode( $this->widget_options['url'] );
			wp_add_dashboard_widget(
				$this->widget_id,
				$title,
				array( $this, 'wp_dashboard_widget_display' )
			);
		}

		public function wp_dashboard_widget_display() {
			$rss = @fetch_feed( $this->widget_options['url'] );
			if ( is_wp_error( $rss ) ) {
				if ( is_admin() || current_user_can( 'manage_options' ) ) {
					echo '<div class="rss-widget"><p>';
					printf( __( '<strong>Feed Error</strong>: %s', 'ub' ), $rss->get_error_message() );
					echo '</p></div>';
				}
			} elseif ( ! $rss->get_item_quantity() ) {
				$rss->__destruct();
				unset( $rss );
				return false;

			} else {
				echo '<div class="rss-widget">';
				wp_widget_rss_output( $rss, $this->widget_options );
				echo '</div>';
				$rss->__destruct();
				unset( $rss );
			}
		}

		public function wp_dashboard_widget_controls() {
			wp_widget_rss_form( $this->widget_options );
		}
	}
}
