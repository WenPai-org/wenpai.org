<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Branda_Admin_Menu_Builder {
	private $menu;
	private $markup = '';

	private $self;
	private $parent_file;
	private $typenow;
	private $submenu_file;
	private $plugin_page;

	public function __construct( $menu, $self, $parent_file, $submenu_file, $plugin_page, $typenow ) {
		$this->menu = $menu;

		$this->self         = $self;
		$this->parent_file  = $parent_file;
		$this->typenow      = $typenow;
		$this->submenu_file = $submenu_file;
		$this->plugin_page  = $plugin_page;
	}

	public function build() {
		$first = true;

		ob_start();
		$this->print_menu_start();
		foreach ( $this->menu as $menu_item_id => $menu_item_settings ) {
			if ( $this->is_separator( $menu_item_id ) ) {
				$this->print_separator();
			} else {
				$this->print_menu_item_markup( $menu_item_id, $menu_item_settings, $first );
			}
			$first = false;
		}
		$this->print_collapse_button();

		$this->markup .= ob_get_clean();
	}

	public function render() {
		echo $this->markup;
	}

	private function print_menu_item_markup( $menu_item_id, $menu_item_settings, $first ) {
		if ( $this->is_hidden( $menu_item_settings ) ) {
			return;
		}

		$menu_item_slug = $this->get_slug( $menu_item_id, $menu_item_settings );

		$class           = $this->get_menu_item_class_attribute( $menu_item_id, $menu_item_settings, $first );
		$id_attribute    = $this->get_menu_item_setting( $menu_item_settings, 'id_attribute' );
		$url             = $this->get_url( $menu_item_settings );
		$aria_attributes = $this->get_menu_item_aria_attributes( $menu_item_id, $menu_item_settings );
		$image           = $this->get_menu_item_image( $menu_item_settings );
		$title           = wptexturize( $this->get_menu_item_setting( $menu_item_settings, 'title' ) );
		$link_target     = $this->get_menu_item_setting( $menu_item_settings, 'link_target' );
		$submenu         = $this->get_submenu( $menu_item_settings );

		$notification = $this->get_menu_item_setting( $menu_item_settings, 'notification' );
		if ( $notification ) {
			$title .= " {$notification}";
		}

		?>
		<li <?php echo $class; ?> id="<?php echo esc_attr( $id_attribute ); ?>">

			<a href="<?php echo esc_attr( $url ); ?>" <?php echo $class; ?> <?php echo $aria_attributes; ?>
			   target="<?php echo esc_attr( $link_target ); ?>">
				<div class="wp-menu-arrow">
					<div></div>
				</div>
				<?php echo $image; ?>
				<div class="wp-menu-name"><?php echo wp_kses_post( $title ); ?></div>
			</a>

			<?php if ( $this->is_submenu_visible( $submenu ) ) : ?>
				<ul class="wp-submenu wp-submenu-wrap">
					<li class="wp-submenu-head" aria-hidden="true"><?php echo wp_kses_post( $title ); ?></li>

					<?php
					$first_submenu_item = true;
					foreach ( $submenu as $submenu_item_id => $submenu_item_settings ) {
						$this->print_submenu_item_markup(
							$submenu_item_id,
							$submenu_item_settings,
							$menu_item_slug,
							$first_submenu_item
						);
						$first_submenu_item = false;
					}
					?>
				</ul>
			<?php endif; ?>
		</li>
		<?php
	}

	private function print_submenu_item_markup( $submenu_item_id, $submenu_item_settings, $menu_item_slug, $first ) {
		if ( $this->is_hidden( $submenu_item_settings ) ) {
			return;
		}

		$submenu_item_slug = $this->get_slug( $submenu_item_id, $submenu_item_settings );

		$image           = $this->get_submenu_item_image( $submenu_item_settings );
		$class           = $this->get_submenu_class_attribute( $submenu_item_id, $submenu_item_settings, $menu_item_slug, $first, (bool) $image );
		$id_attribute    = $this->get_menu_item_setting( $submenu_item_settings, 'id_attribute' );
		$aria_attributes = $this->get_submenu_item_aria_attributes( $submenu_item_slug, $menu_item_slug );
		$title           = wptexturize( $this->get_menu_item_setting( $submenu_item_settings, 'title' ) );
		$link_target     = $this->get_menu_item_setting( $submenu_item_settings, 'link_target' );
		$url             = $this->get_url( $submenu_item_settings );

		$notification = $this->get_menu_item_setting( $submenu_item_settings, 'notification' );
		if ( $notification ) {
			$title .= " {$notification}";
		}

		add_filter( 'wp_kses_allowed_html', array( 'Branda_Helper', 'kses_allow_style_tag' ) );

		?>
		<li <?php echo $class; ?>>

			<a id="<?php echo esc_attr( $id_attribute ); ?>"
				href="<?php echo esc_attr( $url ); ?>" <?php echo $class; ?> <?php echo $aria_attributes; ?>
				target="<?php echo esc_attr( $link_target ); ?>">
				<?php echo $image; ?>
				<?php echo wp_kses_post( $title ); ?>
			</a>
		</li>
		<?php

		remove_filter( 'wp_kses_allowed_html', array( 'Branda_Helper', 'kses_allow_style_tag' ) );
	}

	private function print_collapse_button() {
		?>
		<li id="collapse-menu" class="hide-if-no-js">
			<button type="button" id="collapse-button" aria-label="<?php echo esc_attr__( 'Collapse Main menu', 'ub' ); ?>"
					aria-expanded="true">
				<span class="collapse-button-icon" aria-hidden="true"></span>
				<span class="collapse-button-label"><?php echo __( 'Collapse menu', 'ub' ); ?></span>
			</button>
		</li>
		<?php
	}

	private function get_menu_item_setting( $item_settings, $key ) {
		if ( ! empty( $item_settings[ $key ] ) ) {
			return $item_settings[ $key ];
		}

		$default_value = $this->get_menu_item_default( $item_settings, $key );
		if ( ! empty( $default_value ) ) {
			return $default_value;
		}

		return '';
	}

	private function get_menu_item_default( $item_settings, $key ) {
		$default_key = $key . '_default';
		if ( ! empty( $item_settings[ $default_key ] ) ) {
			return $item_settings[ $default_key ];
		}

		return '';
	}

	private function is_current_page_menu_item( $slug ) {
		return ( $this->parent_file && $slug == $this->parent_file )
			   || ( empty( $this->typenow ) && $this->self == $slug );
	}

	private function is_current_page_submenu_item( $sub_item_slug, $main_item_slug ) {
		$self_type = ! empty( $this->typenow ) ? $this->self . '?post_type=' . $this->typenow : 'nothing';
		$menu_file = $this->get_menu_file( $main_item_slug );

		if ( isset( $this->submenu_file ) ) {
			return $this->submenu_file == $sub_item_slug;
		} elseif (
			( ! isset( $this->plugin_page ) && $this->self == $sub_item_slug )
			|| (
				isset( $this->plugin_page )
				&& $this->plugin_page == $sub_item_slug
				&& (
					$main_item_slug == $self_type
					|| $main_item_slug == $this->self
					|| file_exists( $menu_file ) === false
				)
			)
		) {
			return true;
		}

		return false;
	}

	private function get_menu_file( $menu_slug ) {
		$menu_file = $menu_slug;
		if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
			$menu_file = substr( $menu_file, 0, $pos );
		}

		return $menu_file;
	}

	private function get_slug( $item_id, $item_settings ) {
		$is_native = $this->is_native( $item_settings );

		return $is_native
			? $this->get_menu_item_setting( $item_settings, 'slug' )
			: $item_id;
	}

	private function is_native( $item_settings ) {
		return (bool) $this->get_menu_item_setting( $item_settings, 'is_native' );
	}

	private function has_submenu( $settings ) {
		return ! empty( $this->get_submenu( $settings ) );
	}

	/**
	 * @param $settings
	 *
	 * @return array
	 */
	private function get_submenu( $settings ) {
		$submenu = $this->get_menu_item_setting( $settings, 'submenu' );
		return ! empty( $submenu ) && is_array( $submenu )
			? $submenu
			: array();
	}

	/**
	 * @param $menu_item_id
	 * @param $menu_item_settings
	 * @param $is_first
	 *
	 * @return string
	 */
	private function get_menu_item_class_attribute( $menu_item_id, $menu_item_settings, $is_first ) {
		$class          = array();
		$menu_item_slug = $this->get_slug( $menu_item_id, $menu_item_settings );

		if ( ! $this->is_native( $menu_item_settings ) ) {
			$class[] = 'menu-top';
		}

		if ( $is_first ) {
			$class[] = 'wp-first-item';
		}

		$has_submenu = $this->has_submenu( $menu_item_settings );
		if ( $has_submenu ) {
			$class[] = 'wp-has-submenu';
		}

		if ( $this->is_current_page_menu_item( $menu_item_slug ) ) {
			if ( $has_submenu ) {
				$class[] = 'wp-has-current-submenu wp-menu-open';
			} else {
				$class[] = 'current';
			}
		} else {
			$class[] = 'wp-not-current-submenu';
		}

		$custom_classes = branda_get_array_value( $menu_item_settings, 'css_classes' );
		if ( ! empty( $custom_classes ) ) {
			$class[] = esc_attr( $custom_classes );
		}
		$default_classes = $this->get_menu_item_default( $menu_item_settings, 'css_classes' );
		if ( ! empty( $default_classes ) ) {
			$class[] = esc_attr( $default_classes );
		}

		return $class
			? 'class="' . join( ' ', $class ) . '"'
			: '';
	}

	private function get_submenu_class_attribute( $submenu_item_id, $submenu_item_settings, $main_item_slug, $is_first, $has_icon ) {
		$class             = array();
		$submenu_item_slug = $this->get_slug( $submenu_item_id, $submenu_item_settings );

		if ( $is_first ) {
			$class[] = 'wp-first-item';
		}

		if ( $has_icon ) {
			$class[] = 'branda-sub-menu-with-icon';
		}

		if ( $this->is_current_page_submenu_item( $submenu_item_slug, $main_item_slug ) ) {
			$class[] = 'current';
		}

		$custom_classes = branda_get_array_value( $submenu_item_settings, 'css_classes' );
		if ( ! empty( $custom_classes ) ) {
			$class[] = esc_attr( $custom_classes );
		}
		$default_classes = $this->get_menu_item_default( $submenu_item_settings, 'css_classes' );
		if ( ! empty( $default_classes ) ) {
			$class[] = esc_attr( $default_classes );
		}

		return $class
			? 'class="' . join( ' ', $class ) . '"'
			: '';
	}

	private function get_menu_item_aria_attributes( $menu_item_id, $menu_item_settings ) {
		$menu_item_slug  = $this->get_slug( $menu_item_id, $menu_item_settings );
		$aria_attributes = '';
		$has_submenu     = $this->has_submenu( $menu_item_settings );

		if ( $this->is_current_page_menu_item( $menu_item_slug ) && ! $has_submenu ) {
			$aria_attributes .= 'aria-current="page"';
		} elseif ( $has_submenu ) {
			$aria_attributes .= 'aria-haspopup="true"';
		}

		return $aria_attributes;
	}

	private function get_submenu_item_aria_attributes( $submenu_item_slug, $main_item_slug ) {
		$is_current = $this->is_current_page_submenu_item( $submenu_item_slug, $main_item_slug );
		if ( $is_current ) {
			return 'aria-current="page"';
		}

		return '';
	}

	private function get_menu_item_image( $menu_item_settings ) {
		return $this->get_image(
			$menu_item_settings,
			'wp-menu-image',
			'style="background-image:url(\'%s\') !important"',
			false
		);
	}

	private function get_submenu_item_image( $menu_item_settings ) {
		return $this->get_image(
			$menu_item_settings,
			'wp-menu-image',
			'style="background-image:url(\'%s\') !important"',
			true
		);
	}

	private function get_image( $menu_item_settings, $container_class, $svg_style, $default_value_empty ) {
		$icon_type    = $this->get_menu_item_setting( $menu_item_settings, 'icon_type' );
		$image_style  = '';
		$image_class  = '';
		$image        = '<br>';
		$image_exists = false;

		if ( $icon_type === 'none' ) {
			$image_class = 'dashicons-before';
		} elseif ( $icon_type === 'svg' ) {
			$svg = $this->get_menu_item_setting( $menu_item_settings, 'icon_svg' );

			if ( $svg ) {
				$image_exists = true;
				$image_class  = 'svg';
				$image_style  = sprintf( $svg_style, esc_attr( $svg ) );
			}
		} elseif ( $icon_type === 'url' ) {
			$icon_url = $this->get_menu_item_setting( $menu_item_settings, 'icon_url' );

			if ( $icon_url ) {
				$image_exists = true;
				$image        = '<img src="' . $icon_url . '" alt="" />';
				$image_class  = 'dashicons-before';
			}
		} elseif ( $icon_type === 'upload' ) {
			$icon_image_id = $this->get_menu_item_setting( $menu_item_settings, 'icon_image_id' );

			if ( $icon_image_id ) {
				global $branda_network;
				// if icon isn't available on subsites
				if ( ! image_downsize( $icon_image_id ) && $branda_network && ! is_main_site() ) {
					$main_site_id = get_network()->site_id;
					switch_to_blog( $main_site_id );
					$return_to_current_blog = true;
				}
				$icon_url = wp_get_attachment_image_src( $icon_image_id, 'thumbnail', true );
				// maybe return to current blog
				if ( ! empty( $return_to_current_blog ) ) {
					restore_current_blog();
				}

				if ( ! empty( $icon_url ) ) {
					$image_exists = true;
					$image        = '<img src="' . $icon_url[0] . '" alt="" />';
					$image_class  = 'dashicons-before';
				}
			}
		} elseif ( $icon_type === 'dashicon' ) {
			$dashicon = $this->get_menu_item_setting( $menu_item_settings, 'dashicon' );

			if ( $dashicon ) {
				$image_exists = true;
				$image_class  = 'dashicons-before ' . sanitize_html_class( $dashicon );
			}
		}

		return ! $image_exists && $default_value_empty
			? ''
			: sprintf( '<div class="%1$s %2$s" %3$s>%4$s</div>', $container_class, $image_class, $image_style, $image );
	}

	private function get_url( $settings ) {
		$link_type = $this->get_menu_item_setting( $settings, 'link_type' );
		$url       = '#';

		if ( $link_type === 'frontend' ) {
			$url = home_url();
		} elseif ( $link_type === 'admin' ) {
			$url = admin_url();
		} elseif ( $link_type === 'custom' ) {
			$url = $this->get_menu_item_setting( $settings, 'custom_url' );
		}

		return $url;
	}

	private function print_menu_start() {
		?>
	<div id="adminmenumain" role="navigation" aria-label="<?php esc_attr_e( 'Main menu', 'ub' ); ?>">
		<a href="#wpbody-content" class="screen-reader-shortcut"><?php _e( 'Skip to main content', 'ub' ); ?></a>
		<a href="#wp-toolbar" class="screen-reader-shortcut"><?php _e( 'Skip to toolbar', 'ub' ); ?></a>
		<div id="adminmenuback"></div>
		<div id="adminmenuwrap">
			<ul id="adminmenu">
		<?php
	}

	private function is_separator( $menu_item_id ) {
		return strpos( $menu_item_id, 'separator_' ) === 0;
	}

	private function print_separator() {
		?>
		<li class="wp-not-current-submenu wp-menu-separator" aria-hidden="true">
			<div class="separator"></div>
		</li>
		<?php
	}

	private function is_submenu_visible( $submenu ) {
		return is_array( $submenu )
			   && ! empty( $submenu )
			   && ! empty( array_filter( $submenu, array( $this, 'is_visible' ) ) );
	}

	private function is_visible( $menu_item_settings ) {
		return ! $this->is_hidden( $menu_item_settings );
	}

	private function is_hidden( $menu_item_settings ) {
		return $this->get_menu_item_setting( $menu_item_settings, 'is_hidden' );
	}
}
