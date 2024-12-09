<?php
/**
 * Branda Author Box class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Author_Box' ) ) {

	/**
	 * Author Box Widget
	 *
	 * @since 2.0.0
	 */
	include_once dirname( __FILE__ ) . '/class-author-box-widget.php';

	class Branda_Author_Box extends Branda_Helper {
		protected $option_name = 'ub_author_box';
		private $current_sites = array();

		/**
		 * Constructor
		 *
		 * @since 1.9.7
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'author-box';
			/**
			 * Admin area
			 */
			add_filter( 'ultimatebranding_settings_author_box', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_author_box_process', array( $this, 'update' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'widgets_init', array( $this, 'widgets' ) );
			/**
			 * Front end
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_head', array( $this, 'print_css' ) );
			add_filter( 'the_content', array( $this, 'author_box' ) );
			add_filter( 'get_the_excerpt', array( $this, 'remove_the_content_filter' ), 9 );
			add_filter( 'wp_trim_excerpt', array( $this, 'restore_the_content_filter' ) );
			add_filter( 'author_box', array( $this, 'widget' ) );
			/**
			 * user profile
			 */
			add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ) );
			add_action( 'edit_user_profile', array( $this, 'add_social_media' ) );
			add_action( 'personal_options_update', array( $this, 'save_user_profile' ) );
			add_action( 'show_user_profile', array( $this, 'add_social_media' ) );
			/**
			 * Change settings: Design: Social Media
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_get_options_fields_design_social', array( $this, 'add_design_social_position' ), 10, 3 );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
		}

		/**
		 * Don't apply the_content filter for excerpts
		 */
		public function remove_the_content_filter( $post_excerpt ) {
			remove_filter( 'the_content', array( $this, 'author_box' ) );

			return $post_excerpt;
		}

		public function restore_the_content_filter( $text ) {
			add_filter( 'the_content', array( $this, 'author_box' ) );

			return $text;
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			/**
			 * Check we have plugin_version in saved data
			 */
			if ( isset( $value['plugin_version'] ) ) {
				/**
				 * do not run again big upgrade if config was saved by Branda
				 */
				$version_compare = version_compare( $value['plugin_version'], '3.0.0' );
				if ( -1 < $version_compare ) {
					return;
				}
				return;
			}
			$data = array(
				'show'   => array(),
				'design' => array(),
				'colors' => array(),
			);
			/**
			 * Common migration
			 */
			$data = $this->common_upgrade_options( $data, $value );
			/**
			 * show
			 */
			if ( isset( $value['show'] ) ) {
				$v = $value['show'];
				if ( isset( $v['mode'] ) ) {
					$data['show']['mode'] = $v['mode'];
				}
				if (
					isset( $v['post_type'] )
					&& is_array( $v['post_type'] )
				) {
					$data['show']['post_type'] = array();
					foreach ( $v['post_type'] as $post_type ) {
						$data['show']['post_type'][ $post_type ] = true;
					}
				}
				if ( isset( $v['display_name'] ) ) {
					$data['design']['name_and_bio_show_name'] = $v['display_name'];
				}
				if ( isset( $v['description'] ) ) {
					$data['design']['name_and_bio_show_description'] = $v['description'];
				}
				if ( isset( $v['avatar'] ) ) {
					$data['design']['avatar_show'] = $v['avatar'];
				}
				if ( isset( $v['social_media'] ) ) {
					$data['design']['social_media_show'] = $v['social_media'];
				}
				if ( isset( $v['entries'] ) ) {
					$data['design']['latests_entries_show'] = $v['entries'];
				}
			}
			/**
			 * box
			 */
			if ( isset( $value['box'] ) ) {
				$v = $value['box'];
				if ( isset( $v['border_width'] ) ) {
					$data['design']['container_border_width'] = $v['border_width'];
				}
				if ( isset( $v['border_color'] ) ) {
					$data['colors']['container_border_color'] = $v['border_color'];
				}
				if ( isset( $v['border_style'] ) ) {
					$data['design']['container_border_style'] = $v['border_style'];
				}
				if ( isset( $v['border_radius'] ) ) {
					$data['design']['container_border_radius'] = $v['border_radius'];
				}
				if ( isset( $v['background_color'] ) ) {
					$data['colors']['container_background_color'] = $v['background_color'];
				}
			}
			/**
			 * name_options
			 */
			if ( isset( $value['name_options'] ) ) {
				$v = $value['name_options'];
				if ( isset( $v['link'] ) ) {
					$data['design']['name_and_bio_link_name'] = $v['link'];
				}
				if ( isset( $v['counter'] ) ) {
					$data['design']['name_and_bio_show_counter'] = $v['counter'];
				}
			}
			/**
			 * avatar
			 */
			if ( isset( $value['avatar'] ) ) {
				$v = $value['avatar'];
				if ( isset( $v['size'] ) ) {
					$data['design']['avatar_size'] = $v['size'];
				}
				if ( isset( $v['rounded'] ) ) {
					$data['design']['avatar_border_radius'] = $v['rounded'];
				}
				if ( isset( $v['border'] ) ) {
					$data['design']['avatar_border_width'] = $v['border'];
				}
				if ( isset( $v['border_color'] ) ) {
					$data['colors']['avatar_border_color'] = $v['border_color'];
				}
			}
			/**
			 * name_options
			 */
			if ( isset( $value['entries_settings'] ) ) {
				$v = $value['entries_settings'];
				if ( isset( $v['the_same_type'] ) ) {
					$data['design']['latests_entries_type'] = $v['the_same_type'];
				}
				if ( isset( $v['limit'] ) ) {
					$data['design']['latests_entries_limit'] = $v['limit'];
				}
				if ( isset( $v['link_in_new_tab'] ) ) {
					$data['design']['latests_entries_link_in_new_tab'] = $v['link_in_new_tab'];
				}
				if ( isset( $v['title_show'] ) ) {
					$data['design']['latests_entries_show'] = $v['title_show'];
				}
				if ( isset( $v['title'] ) ) {
					$data['design']['latests_entries_title'] = $v['title'];
				}
			}
			/**
			 * Fix social media
			 */
			if (
				isset( $data['content'] )
				&& is_array( $data['content'] )
			) {
				$profiles = array();
				foreach ( $data['content'] as $key => $value ) {
					if ( preg_match( '/^social_media_(.+)$/', $key, $matches ) ) {
						$profiles[] = $matches[1];
					}
				}
				$data['profile'] = array(
					'content' => $profiles,
				);
				unset( $data['content'] );
			}
			$this->update_value( $data );
		}

		/**
		 * Add sovial media position into Design -> Social Media
		 *
		 * @since 3.0.0
		 */
		public function add_design_social_position( $data, $defaults, $module ) {
			if ( $this->module !== $module ) {
				return $data;
			}
			unset( $data['social_media_colors']['accordion'] );
			$data['social_media_position'] = array(
				'type'      => 'sui-tab',
				'label'     => __( 'Position', 'ub' ),
				'options'   => array(
					'bottom'       => __( 'Bottom', 'ub' ),
					'top'          => __( 'Top', 'ub' ),
					'under-avatar' => __( 'Under avatar', 'ub' ),
				),
				'default'   => esc_attr( isset( $defaults['social_media_position'] ) ? $defaults['social_media_position'] : 'bottom' ),
				'accordion' => array(
					'end' => true,
				),
			);
			return $data;
		}

		/**
		 * Register Widgets
		 *
		 * @since 2.0.0
		 */
		public function widgets() {
			register_widget( 'Author_Box_Widget' );
		}

		/**
		 * Set options for module
		 *
		 * @since 1.9.7
		 */
		protected function set_options() {
			$post_types = array();
			$p          = get_post_types( array( 'public' => true ), 'objects' );
			foreach ( $p as $key => $data ) {
				$post_types[ $key ] = $data->label;
			}
			$value = $this->get_value( 'show', 'display_name_link', 'on' );
			if ( 'off' === $value ) {
				$this->set_value( 'name_options', 'link', $value );
			}
			/**
			 * options
			 */
			$options = array(
				'show'    => array(
					'title'       => __( 'Configure', 'ub' ),
					'description' => __( 'Configure the display option and visibility of the author box.', 'ub' ),
					'fields'      => array(
						'widget'    => array(
							'type'  => 'description',
							'label' => __( 'Author Box Widget', 'ub' ),
							'value' => sprintf(
								__( 'Go to <b>Appearance &gt; <a href="%s">Widgets</a></b> to add the author box widget into your theme\'s sidebars. All customization done in this module will affect the author box widget as well as the inline content author box.', 'ub' ),
								admin_url( 'widgets.php' )
							),
						),
						'mode'      => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Inline Content', 'ub' ),
							'options'     => array(
								'on'  => __( 'Enable', 'ub' ),
								'off' => __( 'Disable', 'ub' ),
							),
							'default'     => 'on',
							'description' => __( 'You can add author box inline in the content of posts, pages, etc. Disable this if wish to use the author box widget only.', 'ub' ),
						),
						'post_type' => array(
							'type'        => 'checkboxes',
							'label'       => __( 'Visibility', 'ub' ),
							'options'     => $post_types,
							'multiple'    => true,
							'description' => __( 'Choose the post types for which author box should be displayed. This options will affect the visibility of author box widget as well as inline author box.', 'ub' ),
							'default'     => array( 'post' ),
						),
					),
				),
				'profile' => array(
					'title'       => __( 'Author Profile', 'ub' ),
					'description' => __( 'Profiles will be available on user edit and user profile page.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'content', array( 'social', 'reset' ) ),
					'accordion'   => array(
						'sufix' => 'builder',
					),
				),
				'design'  => array(
					'title'       => __( 'Design', 'ub' ),
					'description' => __( 'Customize the design of the author box.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'design', array( 'name_and_bio', 'avatar', 'latests_entries', 'social', 'container', 'reset' ) ),
				),
				'colors'  => array(
					'title'       => __( 'Colors', 'ub' ),
					'description' => __( 'Adjust the default colour combinations of author box.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'colors', array( 'name_and_bio', 'avatar', 'latest_entries', 'social', 'container', 'reset' ) ),
				),
				'css'     => $this->get_custom_css_array(
					array(
						'extra_description' => __( 'This will be added to the header of every Login page.', 'ub' ),
						'ace_selectors'     => $this->get_ace_selectors(),
					)
				),
			);
			/**
			 * return options/
			 */
			$this->options = $options;
		}

		/**
		 * Enqueue needed scripts.
		 *
		 * @since 1.9.7
		 */
		public function enqueue_scripts() {
			/**
			 * load on admin
			 */
			if ( is_admin() ) {
				$screen = get_current_screen();
				$this->load_social_logos_css();
				return;
			}
			/**
			 * Load on frontend
			 */
			$is_allowed_post_type = $this->check_post_type();
			if ( $is_allowed_post_type ) {
				$this->load_social_logos_css( $this->module );
			}
		}

		/**
		 * Add social media links to author box.
		 *
		 * @since 1.9.7
		 */
		public function add_social_media( $profileuser ) {
			$data = $this->get_value( 'profile', 'content' );
			$show = isset( $data ) && is_array( $data ) && ! empty( $data );
			if ( ! $show ) {
				return;
			}
			$value        = get_user_meta( $profileuser->ID, $this->option_name, true );
			$social_media = $this->get_options_social_media();
			$fields       = array();
			foreach ( $data as $key ) {
				if ( ! isset( $social_media[ $key ] ) ) {
					continue;
				}
				$label          = $social_media[ $key ]['label'];
				$fields[ $key ] = array(
					'id'          => $this->get_name( $key ),
					'label'       => $label,
					'value'       => isset( $value[ $key ] ) ? $value[ $key ] : '',
					'option_name' => $this->option_name,
					'placeholder' => sprintf( esc_attr__( 'Enter %s URL here…', 'ub' ), $label ),
				);
			}
			if ( empty( $fields ) ) {
				return;
			}
			$args     = array(
				'fields' => $fields,
			);
			$template = $this->get_template_name( 'profile' );
			$this->render( $template, $args );
		}

		/**
		 * Save user profile
		 *
		 * @since 1.9.7
		 */
		public function save_user_profile( $user_id ) {
			if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST[ $this->option_name ] ) ) {
				$value   = array_filter( $_POST[ $this->option_name ] );
				$current = get_user_meta( $user_id, $this->option_name, true );
				$value   = wp_parse_args( $value, $current );
				foreach ( $value as $key => $v ) {
					$is_url = filter_var( $v, FILTER_VALIDATE_URL );
					if ( ! $is_url ) {
						unset( $value[ $key ] );
					}
				}
				if ( empty( $value ) && ! is_multisite() ) {
					delete_user_meta( $user_id, $this->option_name );
					return;
				}
				$result = add_user_meta( $user_id, $this->option_name, $value, true );
				if ( false === $result ) {
					update_user_meta( $user_id, $this->option_name, $value );
				}
			}
		}

		/**
		 * Handle entry content
		 *
		 * @since 1.9.7
		 */
		public function author_box( $content ) {
			$value = $this->get_value( 'show', 'mode' );
			if ( 'off' === $value ) {
				return $content;
			}
			return $this->box( $content );
		}

		/**
		 * handle filter "author_box".
		 *
		 * @since 2.0.0
		 */
		public function widget() {
			return $this->box();
		}

		/**
		 * add author box
		 *
		 * @since 1.9.7
		 */
		private function box( $content = '' ) {
			/**
			 * Check allowed post types.
			 */
			$value = $this->check_post_type();
			if ( ! $value ) {
				return $content;
			}
			$user_id     = get_the_author_meta( 'ID' );
			$box_content = '';
			/**
			 * social media
			 */
			$social_media          = $this->get_social_media_content();
			$social_media_position = $this->get_value( 'design', 'social_media_position', 'bottom' );
			/**
			 * Gravatar
			 */
			$show = $this->get_value( 'design', 'avatar_show', false );
			if ( 'on' == $show ) {
				$size         = $this->get_value( 'design', 'avatar_size', 96 );
				$box_content .= sprintf( '<div class="branda-author-box-avatar" style="min-width: %dpx;">', $size );
				$box_content .= get_avatar( $user_id, $size );
				if ( 'under-avatar' === $social_media_position ) {
					$box_content .= $social_media;
				}
				$box_content .= '</div>';
			}
			/**
			 * name
			 */
			$part = '';
			$show = $this->get_value( 'design', 'name_and_bio_show_name', false );
			if ( 'on' == $show ) {
				$value = get_the_author_meta( 'display_name' );
				$link  = $this->get_value( 'design', 'name_and_bio_link_name', 'on' );
				if ( 'on' == $link ) {
					$value = sprintf(
						'<a href="%s">%s</a>',
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						$value
					);
				}
				$show = $this->get_value( 'design', 'name_and_bio_show_counter', false );
				if ( 'on' === $show ) {
					$args = array(
						'author'      => get_the_author_meta( 'ID' ),
						'post_type'   => get_post_type(),
						'fields'      => 'ids',
						'nopaging'    => true,
						'post_status' => array( 'publish', 'inherit' ),
					);
					$type = $this->get_value( 'design', 'latests_entries_type', 'on' );
					if ( 'off' === $type ) {
						$args['post_type'] = 'any';
					}
					$the_query = new WP_Query( $args );
					$number    = count( $the_query->posts );
					$value    .= sprintf( ' (%d)', number_format_i18n( $number ) );
				}
				$part .= sprintf( '<h4>%s</h4>', $value );
			}
			/**
			 * description
			 */
			$show = $this->get_value( 'design', 'name_and_bio_show_description', false );
			if ( 'on' == $show ) {
				$description = get_the_author_meta( 'user_description' );
				if ( $description ) {
					$part .= sprintf( '<div class="description">%s</div>', wpautop( $description ) );
				}
			}
			/**
			 * last entries
			 */
			$show = $this->get_value( 'design', 'latests_entries_show', false );
			if ( 'on' === $show ) {
				$args = array(
					'post__not_in'   => array( get_the_ID() ),
					'author'         => get_the_author_meta( 'ID' ),
					'posts_per_page' => $this->get_value( 'design', 'latests_entries_limit', 5 ),
					'post_type'      => get_post_type(),
					'post_status'    => array( 'publish', 'inherit' ), // for attachements
				);
				$type = $this->get_value( 'design', 'latests_entries_type', 'on' );
				if ( 'off' === $type ) {
					$args['post_type'] = 'any';
				}
				$entries   = '';
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) {
					$target = $this->get_value( 'design', 'latests_entries_link_in_new_tab', false );
					$target = ( 'on' === $target ) ? ' target="_blank"' : '';
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$title = get_the_title();
						if ( empty( $title ) ) {
							$title = sprintf(
								'<small>[%s]</small>',
								esc_html__( 'entry has no title', 'ub' )
							);
						}
						$entries .= sprintf(
							'<li><a href="%s"%s>%s</a></li>',
							get_the_permalink(),
							$target,
							$title
						);
					}
					wp_reset_postdata();
				}
				wp_reset_query();
				if ( ! empty( $entries ) ) {
					$part .= '<div class="branda-author-box-more">';
					$title = $this->get_value( 'design', 'latests_entries_title', '' );
					if ( ! empty( $title ) ) {
						$part .= sprintf( '<h4>%s</h4>', $title );
					}
					$part .= sprintf( '<ul>%s</ul>', $entries );
					$part .= '</div>';
				}
			}
			/**
			 * wrap description
			 */
			if ( ! empty( $part ) ) {
				$box_content .= sprintf( '<div class="branda-author-box-desc">%s</div>', $part );
			}
			/**
			 * wrap box content
			 */
			if ( ! empty( $box_content ) ) {
				$box_content = sprintf( '<div class="branda-author-box-content">%s</div>', $box_content );
			}
			/**
			 * social_media
			 */
			if ( ! empty( $social_media ) ) {
				switch ( $social_media_position ) {
					case 'top':
						$box_content = $social_media . $box_content;
						break;
					case 'bottom':
						$box_content .= $social_media;
						break;
					default:
						break;
				}
			}
			/**
			 * wrap all
			 */
			if ( ! empty( $box_content ) ) {
				$content .= sprintf( '<div class="branda-author-box">%s</div>', $box_content );
			}
			return $content;
		}

		/**
		 * Social media helper
		 *
		 * @since 1.9.9
		 */
		private function get_social_media_content() {
			$content = '';
			$value   = $this->get_value( 'design', 'social_media_show', 'off' );
			if ( 'off' === $value ) {
				return $content;
			}
			$profiles = get_the_author_meta( $this->option_name );
			if ( empty( $profiles ) ) {
				return $content;
			}
			/**
			 * open link target
			 */
			$target = $this->get_value( 'design', 'social_media_target', false );
			$target = ( '_blank' === $target ) ? ' target="_blank"' : '';
			/**
			 * process
			 */
			$social                         = $this->get_options_social_media();
			$profiles['wp-profile-website'] = get_the_author_meta( 'user_url' );
			$profiles['mail']               = get_the_author_meta( 'user_email' );
			/**
			 * class pattern
			 */
			$pattern = 'social-logo social-logo-%s';
			$allowed = $this->get_value( 'profile', 'content', array() );
			foreach ( $allowed as $key ) {
				if ( ! isset( $social[ $key ] ) ) {
					continue;
				}
				if ( ! isset( $profiles[ $key ] ) ) {
					continue;
				}
				$value = trim( $profiles[ $key ] );
				if ( $value ) {
					$class = sprintf( $pattern, $key );
					switch ( $key ) {
						case 'wp-profile-website':
							$class          = sprintf( $pattern, 'share' );
							$social[ $key ] = array( 'label' => __( 'Website', 'ub' ) );
							break;
						case 'mail':
							$value = 'mailto:' . $value;
							break;
						default:
							break;
					}
					$content .= sprintf(
						'<li class="ub-social-%s"><a href="%s"%s><span class="%s" title="%s"></span></a></li>',
						esc_attr( $key ),
						esc_url( $value ),
						$target,
						esc_attr( $class ),
						esc_attr( $social[ $key ]['label'] )
					);
				}
			}
			if ( $content ) {
				$classes = 'social-media';
				$show    = $this->get_value( 'design', 'social_media_colors', false );
				if ( 'color' == $show ) {
					$classes .= ' use-color';
				}
				$content = sprintf( '<ul class="%s">%s</ul>', esc_attr( $classes ), $content );
			}
			return $content;
		}

		/**
		 * Print custom CSS
		 *
		 * @since 1.9.7
		 */
		public function print_css() {
			/**
			 * Check allowed post types.
			 */
			$is_allowed_post_type = $this->check_post_type();
			if ( ! $is_allowed_post_type ) {
				return;
			}
			/**
			 * social media radius
			 */
			$social_media_radius = null;
			$value               = intval( $this->get_value( 'design', 'container_border_radius', 0 ) );
			if ( 0 < $value ) {
				$position = $this->get_value( 'design', 'social_media_position', false );
				switch ( $position ) {
					case 'top':
						$social_media_radius = sprintf( '%1$spx %1$spx 0 0', $value );
						break;
					case 'bottom':
						$social_media_radius = sprintf( '0 0 %1$spx %1$spx', $value );
						break;
					default:
						break;
				}
			}
			/**
			 * Social Media Color
			 */
			$social_media_color = null;
			$value              = $this->get_value( 'design', 'social_media_colors', 'color' );
			if ( 'monochrome' === $value ) {
				$social_media_color = $this->get_value( 'colors', 'social_monochrome', false );
			}
			/**
			 * template
			 */
			$template = sprintf( '/front-end/modules/%s/css', $this->module );
			$args     = array(
				'id'                         => $this->get_name( 'css' ),
				/**
				 * Container
				 */
				'container_border_width'     => intval( $this->get_value( 'design', 'container_border_width', 0 ) ),
				'container_border_style'     => $this->get_value( 'design', 'container_border_style', false ),
				'container_border_radius'    => intval( $this->get_value( 'design', 'container_border_radius', 0 ) ),
				'container_border_color'     => $this->get_value( 'colors', 'container_border_color', false ),
				'container_background_color' => $this->get_value( 'colors', 'container_background_color', false ),
				/**
				 * Avatar
				 */
				'avatar_size'                => intval( $this->get_value( 'design', 'avatar_size', 0 ) ),
				'avatar_border_width'        => intval( $this->get_value( 'design', 'avatar_border_width', 0 ) ),
				'avatar_border_style'        => $this->get_value( 'design', 'avatar_border_style', 'solid' ),
				'avatar_border_radius'       => intval( $this->get_value( 'design', 'avatar_border_radius', 0 ) ),
				'avatar_border_color'        => $this->get_value( 'colors', 'avatar_border_color', false ),
				/**
				 * latests_entries
				 */
				'latest_entries_entry_color' => $this->get_value( 'colors', 'latest_entries_entry_color', false ),
				'latest_entries_title_color' => $this->get_value( 'colors', 'latest_entries_title_color', false ),
				/**
				 * social media
				 */
				'social_background_color'    => $this->get_value( 'colors', 'social_background_color', false ),
				'social_media_radius'        => $social_media_radius,
				'social_media_color'         => $social_media_color,
				/**
				 * Colors: author title
				 */
				'name_and_bio_name_color'    => $this->get_value( 'colors', 'name_and_bio_name_color', false ),
				'name_and_bio_bio_color'     => $this->get_value( 'colors', 'name_and_bio_bio_color', false ),
				/**
				 * Custom
				 */
				'custom'                     => $this->get_value( 'css', 'css', '' ),
			);
			$this->render( $template, $args );
		}

		/**
		 * modify option name
		 *
		 * @since 1.9.7
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && $this->module == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		/**
		 * Check allowed post types.
		 *
		 * @since 1.9.7
		 */
		private function check_post_type() {
			if ( is_admin() ) {
				return false;
			}
			if ( is_singular() ) {
				$allowed_post_types = $this->get_value( 'show', 'post_type', false );
				if ( empty( $allowed_post_types ) ) {
					return false;
				}
				$post_type = get_post_type();
				return array_key_exists( $post_type, $allowed_post_types );
			}
			return false;
		}

		/**
		 * Design -> Name & Bio
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_name_and_bio( $defaults ) {
			$data = array(
				'name_and_bio_link_name'        => array(
					'type'         => 'sui-tab',
					'label'        => __( 'Link name', 'ub' ),
					'description'  => array(
						'content'  => __( 'Enable this to link author’s name to author archive.', 'ub' ),
						'position' => 'bottom',
					),
					'options'      => array(
						'off' => __( 'Disable', 'ub' ),
						'on'  => __( 'Enable', 'ub' ),
					),
					'default'      => 'on',
					'master'       => $this->get_name( 'display-name' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Name &amp; Bio', 'ub' ),
					),
					'group'        => array(
						'begin' => true,
					),
				),
				'name_and_bio_show_counter'     => array(
					'type'         => 'sui-tab',
					'label'        => __( 'Number of posts', 'ub' ),
					'description'  => array(
						'content'  => __( 'This will show the number of posts of author along with the name.', 'ub' ),
						'position' => 'bottom',
					),
					'options'      => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'      => 'on',
					'master'       => $this->get_name( 'display-name' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'name_and_bio_show_name'        => array(
					'label'       => __( 'Author’s name', 'ub' ),
					'type'        => 'sui-tab',
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'on',
					'slave-class' => $this->get_name( 'display-name' ),
				),
				'name_and_bio_show_description' => array(
					'type'      => 'sui-tab',
					'label'     => __( 'Author’s bio', 'ub' ),
					'options'   => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'   => 'on',
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Design -> Avatar
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_avatar( $defaults ) {
			$data = array(
				'avatar_size'          => array(
					'type'         => 'number',
					'label'        => __( 'Size', 'ub' ),
					'after_label'  => __( 'px', 'ub' ),
					'attributes'   => array( 'placeholder' => '20' ),
					'default'      => 96,
					'min'          => 0,
					'master'       => $this->get_name( 'avatar' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Avatar', 'ub' ),
					),
					'group'        => array(
						'begin' => true,
					),
					'before_field' => '<div class="sui-row"><div class="sui-col">',
					'after_field'  => '</div>',
				),
				'avatar_border_radius' => array(
					'type'         => 'number',
					'label'        => __( 'Corner radius', 'ub' ),
					'attributes'   => array( 'placeholder' => '10' ),
					'default'      => 0,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'master'       => $this->get_name( 'avatar' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'before_field' => '<div class="sui-col">',
					'after_field'  => '</div></div>',
				),
				'avatar_border_width'  => array(
					'type'         => 'number',
					'label'        => __( 'Thickness', 'ub' ),
					'attributes'   => array( 'placeholder' => '1' ),
					'default'      => 0,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'master'       => $this->get_name( 'avatar' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'before_field' => sprintf(
						'<span class="sui-label">%s</span><div class="sui-border-frame branda-author-box-avatar-border"><div class="sui-row"><div class="sui-col">',
						esc_html__( 'Border', 'ub' )
					),
					'after_field'  => '</div>',
				),
				'avatar_border_style'  => array(
					'type'         => 'select',
					'label'        => __( 'Style', 'ub' ),
					'default'      => 'solid',
					'options'      => $this->css_border_options(),
					'master'       => $this->get_name( 'avatar' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'before_field' => '<div class="sui-col">',
					'after_field'  => sprintf(
						'</div></div><span class="sui-description">%s</span></div>',
						esc_html__( 'Color of the border in the Colors Scheme dropdown.', 'ub' )
					),
				),
				'avatar_show'          => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Visibility', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'on',
					'slave-class' => $this->get_name( 'avatar' ),
					'accordion'   => array(
						'end' => true,
					),
					'group'       => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Design -> Latest Entries
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_latests_entries( $defaults ) {
			$data = array(
				'latests_entries_type'            => array(
					'type'         => 'sui-tab',
					'label'        => __( 'Entries type', 'ub' ),
					'description'  => array(
						'content'  => __( 'If a viewer is on a post, only the posts from the author will be shown. Pages and Media type will not be included.', 'ub' ),
						'position' => 'bottom',
					),
					'options'      => array(
						'on'  => __( 'Same as the current post type', 'ub' ),
						'off' => __( 'All', 'ub' ),
					),
					'default'      => 'on',
					'master'       => $this->get_name( 'entries' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Latest Entries', 'ub' ),
					),
					'group'        => array(
						'begin' => true,
					),
				),
				'latests_entries_limit'           => array(
					'type'         => 'number',
					'label'        => __( 'Number of entries', 'ub' ),
					'default'      => 5,
					'min'          => 1,
					'master'       => $this->get_name( 'entries' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'latests_entries_link_in_new_tab' => $this->get_options_link_in_new_tab(
					array(
						'master'       => $this->get_name( 'entries' ),
						'master-value' => 'on',
						'display'      => 'sui-tab-content',
						'default'      => 'on',
					)
				),
				'latests_entries_title'           => array(
					'label'        => __( 'Entries title (optional)', 'ub' ),
					'description'  => array(
						'content'  => __( 'Leave this field blank if you don’t want to show any title.', 'ub' ),
						'position' => 'bottom',
					),
					'default'      => __( 'Read more from the same author:', 'ub' ),
					'master'       => $this->get_name( 'entries' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
				),
				'latests_entries_show'            => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Visibility', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'off',
					'slave-class' => $this->get_name( 'entries' ),
					'accordion'   => array(
						'end' => true,
					),
					'group'       => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * ->
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_design_container( $defaults ) {
			$data = array(
				'container_border_radius' => array(
					'type'        => 'number',
					'label'       => __( 'Corner radius', 'ub' ),
					'after_label' => __( 'px', 'ub' ),
					'attributes'  => array( 'placeholder' => '20' ),
					'default'     => 0,
					'min'         => 0,
					'after_label' => __( 'px', 'ub' ),
					'accordion'   => array(
						'begin' => true,
						'title' => __( 'Container', 'ub' ),
					),
					'group'       => array(
						'begin' => true,
					),
				),
				'container_border_width'  => array(
					'type'         => 'number',
					'label'        => __( 'Thickness', 'ub' ),
					'attributes'   => array( 'placeholder' => '1' ),
					'default'      => 1,
					'min'          => 0,
					'after_label'  => __( 'px', 'ub' ),
					'before_field' => sprintf(
						'<span class="sui-label">%s</span><div class="sui-border-frame branda-author-box-avatar-border"><div class="sui-row"><div class="sui-col">',
						esc_html__( 'Border', 'ub' )
					),
					'after_field'  => '</div>',
				),
				'container_border_style'  => array(
					'type'         => 'select',
					'label'        => __( 'Style', 'ub' ),
					'default'      => 'solid',
					'options'      => $this->css_border_options(),
					'accordion'    => array(
						'end' => true,
					),
					'group'        => array(
						'end' => true,
					),
					'before_field' => '<div class="sui-col">',
					'after_field'  => sprintf(
						'</div></div><span class="sui-description">%s</span></div>',
						esc_html__( 'Color of the border in the Colors Scheme dropdown.', 'ub' )
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Colors -> Name & Bio
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_name_and_bio( $defaults ) {
			$data = array(
				'name_and_bio_name_color' => array(
					'type'      => 'color',
					'label'     => __( 'Name', 'ub' ),
					'default'   => '#222',
					'group'     => array(
						'begin' => true,
					),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Name &amp; Bio', 'ub' ),
					),
				),
				'name_and_bio_bio_color'  => array(
					'type'      => 'color',
					'label'     => __( 'Bio', 'ub' ),
					'default'   => '#333',
					'group'     => array(
						'end' => true,
					),
					'accordion' => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Colors -> avatar
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_avatar( $defaults ) {
			$data = array(
				'avatar_border_color' => array(
					'type'      => 'color',
					'label'     => __( 'Border', 'ub' ),
					'default'   => '#ddd',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Avatar', 'ub' ),
						'end'   => true,
					),
					'group'     => array(
						'begin' => true,
						'end'   => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Colors -> Latest Entries
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_latest_entries( $defaults ) {
			$data = array(
				'latest_entries_title_color' => array(
					'type'      => 'color',
					'label'     => __( 'Title', 'ub' ),
					'default'   => '#222',
					'group'     => array(
						'begin' => true,
					),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Latest Entries', 'ub' ),
					),
				),
				'latest_entries_entry_color' => array(
					'type'      => 'color',
					'label'     => __( 'Entry', 'ub' ),
					'default'   => '#333',
					'group'     => array(
						'end' => true,
					),
					'accordion' => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Colors -> social
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_social( $defaults ) {
			$data = array(
				'social_background_color' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'default'   => '#ddd',
					'data'      => array(
						'alpha' => true,
					),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Social Accounts', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'social_monochrome'       => array(
					'type'        => 'color',
					'label'       => __( 'Monochrome', 'ub' ),
					'description' => array(
						'content'  => __( 'This color will be used ONLY when icons are monochrome.', 'ub' ),
						'position' => 'bottom',
					),
					'default'     => '#222',
					'data'        => array(
						'alpha' => true,
					),
					'group'       => array(
						'end' => true,
					),
					'accordion'   => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Colors -> container
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_colors_container( $defaults ) {
			$data = array(
				'container_border_color'     => array(
					'type'      => 'color',
					'label'     => __( 'Border', 'ub' ),
					'default'   => '#ddd',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Container', 'ub' ),
					),
					'group'     => array(
						'begin' => true,
					),
				),
				'container_background_color' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'default'   => '#fff',
					'data'      => array(
						'alpha' => true,
					),
					'accordion' => array(
						'end' => true,
					),
					'group'     => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Content Social Media options fields
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_social( $defaults = array() ) {
			$uba              = branda_get_uba_object();
			$this->uba        = $uba;
			$social_media     = $this->get_options_social_media( $defaults );
			$value            = $this->get_value( 'profile', 'content', array() );
			$social_media_new = array();
			$data             = array();
			$template         = sprintf( '/admin/modules/%s/row-social-media', $this->module );
			if ( ! empty( $value ) && is_array( $value ) ) {
				foreach ( $value as $key ) {
					/**
					 * Check is available - it can be wrong, when we saved
					 * data before 3.1.0 with G+ profile, which was removed in
					 * 3.1.0
					 */
					if ( ! isset( $social_media[ $key ] ) ) {
						continue;
					}
					$one                      = $social_media[ $key ];
					$one['type']              = 'raw';
					$args                     = array(
						'id'    => $key,
						'label' => $one['label'],
					);
					$one['content']           = $this->render( $template, $args, true );
					$one['container-classes'] = array( 'sui-can-move' );
					unset( $one['label'] );
					$data[ $key ]                   = $one;
					$social_media[ $key ]['hidden'] = true;
				}
			}
			$data['add-new-button'] = array(
				'type'      => 'button',
				'value'     => __( 'Add Accounts', 'ub' ),
				'icon'      => 'plus',
				'sui'       => 'ghost',
				'after'     => '',
				'accordion' => array(
					'end' => true,
				),
				'classes'   => array(
					'branda-social-logo-add-dialog-button',
					'modal-mask' => 'true',
				),
				'data'      => array(
					'modal-open' => $this->get_nonce_action( 'social', 'media', 'add' ),
				),
			);
			/**
			 * Add open accordion
			 */
			$keys                               = array_keys( $data );
			$key                                = $keys[0];
			$data[ $key ]['accordion']['begin'] = true;
			$data[ $key ]['accordion']['title'] = __( 'Social Accounts', 'ub' );
			$data[ $key ]['accordion']['sufix'] = 'builder';
			if ( 1 > count( $data ) ) {
				$key = 'add-new-button';
			}
			$data['add-new-button']['before_field']  = '';
			$data[ $key ]['before_field']            = '<div class="sui-box-builder-fields social-logo-color branda-social-logos-main-container">';
			$data['add-new-button']['before_field'] .= '</div>';
			/**
			 * dialog
			 */
			$content  = sprintf(
				'<p class="sui-description">%s</p>',
				esc_html__( 'Choose the platforms to insert into your social sharing module.', 'ub' )
			);
			$content .= '<div class="sui-box-selectors">';
			$content .= '<ul>';
			foreach ( $social_media as $k => $value ) {
				$id       = $this->get_nonce_action( 'social-media', $k );
				$content .= sprintf(
					'<li class="branda-social-logo-li-%s%s" data-id="%s"><label for="%s" class="sui-box-selector">',
					esc_attr( $k ),
					isset( $value['hidden'] ) && $value['hidden'] ? ' hidden' : '',
					esc_attr( $k ),
					esc_attr( $id )
				);
				$content .= sprintf(
					'<input type="checkbox" name="%s" id="%s" value="%s" data-label="%s" />',
					esc_attr( $id ),
					esc_attr( $id ),
					esc_attr( $k ),
					esc_attr( $value['label'] )
				);
				$content .= '<span class="branda-social-logo-container">';
				$content .= sprintf(
					'<span class="social-logo social-logo-%s"></span><span class="social-media-title">%s</span>',
					esc_attr( $k ),
					esc_html( $value['label'] )
				);
				$content .= '</span>';
				$content .= '</label>';
				$content .= '</li>';
			}
			$content .= '</ul>';
			$content .= '</div>';
			/**
			 * footer
			 */
			$footer  = '';
			$args    = array(
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
				'data' => array(
					'modal-close' => '',
				),
			);
			$footer .= $this->button( $args );
			$args    = array(
				'text'  => __( 'Add Accounts', 'ub' ),
				'sui'   => '',
				'class' => 'branda-social-logo-add-accounts ' . $this->get_name( 'add' ),
				'data'  => array(
					'nonce'    => wp_create_nonce( $this->get_nonce_action( 'social', 'media', 'add' ) ),
					'dialog'   => $this->get_nonce_action( 'social', 'media', 'add' ),
					'template' => $this->get_name( 'social-media-item' ),
				),
			);
			$footer .= $this->button( $args );
			/**
			 * Dialog
			 */
			$args                             = array(
				'id'           => $this->get_nonce_action( 'social', 'media', 'add' ),
				'content'      => $content,
				'title'        => __( 'Add Social Account', 'ub' ),
				'classes'      => array( 'branda-social-logo-add-dialog', 'social-logo-color', 'sui-modal-lg' ),
				'confirm_type' => false,
				'data'         => array(
					'template' => $this->get_name( 'social-media-item' ),
				),
				'footer'       => array(
					'content' => $footer,
					'classes' => array( 'sui-space-between' ),
				),
			);
			$data['add-new-button']['after'] .= $this->sui_dialog( $args );
			/**
			 * template
			 */
			$args                             = array(
				'id'    => '{{{data.id}}}',
				'label' => '{{{data.label}}}',
			);
			$template                         = '/admin/modules/author-box/row-social-media';
			$content                          = '<div class="sui-form-field simple-option simple-option-raw sui-can-move ui-sortable-handle">';
			$content                         .= $this->render( $template, $args, true );
			$content                         .= '</div>';
			$data['add-new-button']['after'] .= sprintf(
				'<script type="text/html" id="tmpl-%s">%s</script>',
				$this->get_name( 'social-media-item' ),
				$content
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Profile -> Reset
		 *
		 * @since 3.0.0
		 */
		protected function get_options_fields_content_reset( $defaults = array() ) {
			return $this->get_options_fields_reset( 'profile', $defaults );
		}

		/**
		 * Get ACE editor buttons
		 *
		 * @since 3.1.0
		 */
		private function get_ace_selectors() {
			$selectors = array(
				'general' => array(
					'selectors' => array(
						'.branda-author-box-more'          => __( 'Box', 'ub' ),
						'.branda-author-box-desc h4'       => __( 'Author Name', 'ub' ),
						'.branda-author-box-content'       => __( 'Box content', 'ub' ),
						'.branda-author-box-more'          => __( 'List', 'ub' ),
						'.branda-author-box-avatar'        => __( 'Avatar', 'ub' ),
						'.branda-author-box .social-media' => __( 'Social Media', 'ub' ),
					),
				),
			);
			return $selectors;
		}
	}
}
new Branda_Author_Box();
