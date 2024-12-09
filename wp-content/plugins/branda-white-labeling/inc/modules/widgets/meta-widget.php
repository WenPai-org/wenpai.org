<?php
/**
 * Branda Meta Widget class.
 *
 * @package Branda
 * @subpackage Widgets
 */
if ( ! class_exists( 'Branda_Meta_Widget' ) ) {
	class Branda_Meta_Widget extends Branda_Helper {

		public function __construct() {
			parent::__construct();
			add_action( 'widgets_init', array( $this, 'register' ) );
			add_filter( 'ultimatebranding_settings_rebranded_meta_widget', array( $this, 'admin_options_page' ) );
		}

		/**
		 * set options
		 *
		 * @since 2.1.0
		 */
		protected function set_options() {
			$img           = add_query_arg(
				'version',
				$this->build,
				branda_files_url( 'modules/widgets/assets/images/meta-widget.png' )
			);
			$options       = array(
				'desc' => array(
					'title'       => __( 'Rebranding', 'ub' ),
					'description' => __( 'Meta widget has been rebranded with your site link instead of the default “WordPress.org” link.', 'ub' ),
					'fields'      => array(
						'html' => array(
							'type'  => 'description',
							'value' => sprintf( '<img src="%s" alt="" />', esc_url( $img ) ),
						),
					),
				),
			);
			$this->options = $options;
		}

		public function register() {
			unregister_widget( 'WP_Widget_Meta' );
			register_widget( 'Branda_WP_Widget_Rebranded_Meta' );
		}
	}
}
new Branda_Meta_Widget();

class Branda_WP_Widget_Rebranded_Meta extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_meta',
			'description' => __( 'Log in/out, admin, feed and powered-by links', 'ub' ),
		);
		parent::__construct( 'meta', __( 'Meta', 'ub' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Meta', 'ub' ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		if ( function_exists( 'get_blog_option' ) && function_exists( 'get_current_site' ) ) {
			$current_site     = get_current_site();
			$blog_id          = ( isset( $current_site->blog_id ) ) ? $current_site->blog_id : UB_MAIN_BLOG_ID;
			$global_site_link = 'http://' . $current_site->domain . $current_site->path;
			$global_site_name = get_blog_option( $blog_id, 'blogname' );
		} else {
			$global_site_link = get_option( 'home' );
			$global_site_name = get_option( 'blogname' );
		}
		?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo( 'rss2_url' ); ?>" title="<?php echo esc_attr( __( 'Syndicate this site using RSS 2.0', 'ub' ) ); ?>"><?php _e( 'Entries <abbr title="Really Simple Syndication">RSS</abbr>', 'ub' ); ?></a></li>
			<li><a href="<?php bloginfo( 'comments_rss2_url' ); ?>" title="<?php echo esc_attr( __( 'The latest comments to all posts in RSS', 'ub' ) ); ?>"><?php _e( 'Comments <abbr title="Really Simple Syndication">RSS</abbr>', 'ub' ); ?></a></li>
			<li><a href="<?php echo $global_site_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Powered by %s', 'ub' ), $global_site_name ) ); ?>"><?php echo esc_attr( $global_site_name ); ?></a></li>
			<?php wp_meta(); ?>
			</ul>
		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = strip_tags( $instance['title'] );
		?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ub' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php
	}
}

