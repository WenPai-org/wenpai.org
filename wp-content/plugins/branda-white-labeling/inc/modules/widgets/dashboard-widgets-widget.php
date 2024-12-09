<?php
/**
 * Branda Dashboard Widgets class.
 *
 * @package Branda
 * @subpackage Widgets
 */
if ( ! class_exists( 'Branda_Dashboard_Widgets_Widget' ) ) {
	class Branda_Dashboard_Widgets_Widget {
		var $widget_id;
		var $widget_options;

		public function init( $options_set = '', $options = array() ) {
			if ( empty( $options_set ) ) {
				return; }
			if ( empty( $options ) ) {
				return; }
			if ( strlen( $options_set ) ) {
				$this->widget_id   = $options['branda_id'];
				$options['number'] = $options_set;
			}
			$this->widget_options = $options;
			wp_add_dashboard_widget(
				$this->widget_id,
				stripslashes( $this->widget_options['title'] ),
				array( $this, 'wp_dashboard_widget_display' )
			);
		}

		public function wp_dashboard_widget_display() {
			$content = $this->widget_options['content'];
			if ( isset( $this->widget_options['content_meta'] ) ) {
				$content = $this->widget_options['content_meta'];
			}
			printf( '<div class="branda-widget">%s</div>', wp_kses_post( stripslashes( $content ) ) );
		}

		public function wp_dashboard_widget_controls() {
			wp_widget_rss_form( $this->widget_options );
		}
	}
}
