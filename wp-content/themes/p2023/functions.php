<?php
/**
 * P2020 functions and definitions
 *
 * @package p2020
 */

namespace P2020;

/**
 * You're probably not an Automattician but it insists on asking!
 *
 * @return boolean
 */
function is_automattician() : bool {
	return false;
}

/**
 * Are we Automattic? Or are we dancer?
 *
 * @return boolean
 */
function is_automattic() : bool {
	return false;
}

/**
 * Is this an Automattic P2?
 *
 * @return boolean
 */
function is_a8c_p2() : bool {
	return false;
}

/**
 * Automatticians have forgootten about the site_url function.
 *
 * @param string $path the path to use in the full URL.
 *
 * @return string
 */
function get_blog_url( $path = '' ) : string {
	return site_url( $path );
}

/**
 * Load My Team widget
 */
require_once get_template_directory() . '/widgets/my-team/my-team.php';

/**
 * Load partner plugins loader file.
 */
require_once 'thirdparty.php';

/**
 * Global variable for WP core and various plugins to size embeds/images appropriately.
 * https://codex.wordpress.org/Content_Width
 */
global $content_width;
if ( ! isset( $content_width ) ) {
	$content_width = 940;
} /* pixels */

/**
 * Set up social sharing and likes
 */
function social_init() {
	//Disable social media share button
	$sharing_services = get_option( 'sharing-services' );
	if ( ! empty( $sharing_services['visible'] ) ) {
		$sharing_services['visible'] = [];
		update_option( 'sharing-services', $sharing_services );
	}

	// Disable reblog button
	$disabled_reblogs = get_option( 'disabled_reblogs' );
	if ( 1 !== (int) $disabled_reblogs ) {
		update_option( 'disabled_reblogs', 1 );
	}

	// Enable like button
	$disabled_likes = get_option( 'disabled_likes' );
	if ( 1 === (int) $disabled_likes ) {
		update_option( 'disabled_likes', 0 );
	}

	// Show buttons everywhere
	$sharing_options   = get_option( 'sharing-options' );
	$show_in_locations = [ 'index', 'post', 'page', 'attachment' ];
	if ( ! is_array( $sharing_options['global']['show'] ) ||
		count( array_intersect( $sharing_options['global']['show'], $show_in_locations ) ) !== count( $show_in_locations ) ) {
		$sharing_options['global']['show'] = $show_in_locations;
		update_option( 'sharing-options', $sharing_options );
	}
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\social_init' );

/**
 * Disable related posts feature
 */
function disable_related_posts() {
	$jetpack_relatedposts = get_option( 'jetpack_relatedposts' );
	// We need to explicitly set it to false to avoid default behavior
	if ( ! isset( $jetpack_relatedposts['enabled'] ) || 0 !== (int) $jetpack_relatedposts['enabled'] ) {
		// Disable related posts
		$jetpack_relatedposts['enabled'] = 0;
		update_option( 'jetpack_relatedposts', $jetpack_relatedposts );

		// Remove related-posts from jetpack active modules
		$jetpack_active_modules = get_option( 'jetpack_active_modules' );
		if ( is_array( $jetpack_active_modules ) ) {
			update_option( 'jetpack_active_modules', array_diff( $jetpack_active_modules, [ 'related-posts' ] ) );
		}
	}
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\disable_related_posts' );


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function setup() {

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require get_template_directory() . '/inc/extras.php';

	/**
	 * Customizer additions
	 */
	require get_template_directory() . '/inc/customizer/customizer.php';

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on P2, use a find and replace
	 * to change 'p2020' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'p2020', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Enable support for Full-Width Images
	 */
	add_theme_support( 'align-wide' );

	/**
	 * Enable support for Responsive Embeds
	 */
	add_theme_support( 'responsive-embeds' );

	/**
	 * Enable support for themed block editor styles
	 */
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style-editor.css' );

	/**
	 * Enable support for selective refresh of widgets in the Customizer
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus(
		[
			'primary' => __( 'Primary Menu', 'p2020' ),
		]
	);

	/**
	 * Add a menu with Home item, if location is empty.
	 */
	$locations = get_theme_mod( 'nav_menu_locations' );
	$menu_link = [
		'menu-item-title'  => __( 'Learn more about P2', 'p2020' ),
		'menu-item-url'    => 'https://wordpress.com/p2/',
		'menu-item-status' => 'publish',
	];

	if ( empty( $locations['primary'] ) ) {
		// Check if menu object already exists
		$menu = wp_get_nav_menu_object( 'primary' );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( 'primary' );
			wp_update_nav_menu_item( $menu_id, 0, $menu_link );
		} else {
			$menu_id = $menu->term_id;
		}

		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

	} else {
		$menu = wp_get_nav_menu_object( 'primary' );
		if ( $menu ) {
			$menu_items = wp_get_nav_menu_items( $menu );
			if ( $menu_items ) {
				foreach ( $menu_items as $menu_item ) {
					// replace existing Home link with `Learn about P2`
					if ( 'Home' === $menu_item->post_title && '/' === $menu_item->url ) {
						wp_update_nav_menu_item( $menu->term_id, $menu_item->ID, $menu_link );
					}
				}
			}
		}
	}
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\setup' );

/**
 * Register widgetized area
 */
function widget_areas_init() {
	register_sidebar(
		[
			'name'          => __( 'Primary Sidebar', 'p2020' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="p2020-sidebar-padded-container">',
			'after_widget'  => '</div></aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]
	);
}

add_action( 'widgets_init', __NAMESPACE__ . '\widget_areas_init' );

/**
 * Enqueue Google Fonts
 */
function fonts() {
	/**
	 * translators: If there are characters in your language that are not supported
	 * by Inter, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Sans: on or off', 'p2020' ) ) {
		wp_register_style( 'p2020-sans', '#', [], '20200801' );
	}
}

add_action( 'init', __NAMESPACE__ . '\fonts' );

/**
 * Enqueue font styles in custom header admin
 */
function admin_fonts( $hook_suffix ) {
	if ( 'appearance_page_custom-header' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'p2020-sans' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_fonts' );

/**
 * Hide menu pages from wp-admin
 */
function hide_pages_from_admin_menu() {
	remove_menu_page( 'paid-upgrades.php' ); // Plans
	remove_menu_page( 'calypso-plugins' ); // Plugins
	remove_submenu_page( 'themes.php', 'themes.php' ); // Appearance -> Themes
}

//add_action( 'admin_menu', __NAMESPACE__ . '\hide_pages_from_admin_menu', 40 );

// Block direct access to wp-admin/themes.php (Super Admins are exempt)
//add_action( 'load-themes.php', 'wpcom_disable_admin_page' );

/**
 * Remove the Contributor role in wp-admin (if there are no existing Contributors)
 */
function maybe_remove_contributor_role() {
	$contributors = get_users( [ 'role' => 'contributor' ] );

	if ( count( $contributors ) === 0 ) {
		remove_role( 'contributor' );
	}
}

//add_action( 'wp_loaded', __NAMESPACE__ . '\maybe_remove_contributor_role' );

/**
 * Enqueue scripts and styles
 */
function scripts() {
	wp_enqueue_style( 'p2020-style', get_stylesheet_uri(), [], '20200801' );
	wp_style_add_data( 'p2020-style', 'rtl', 'replace' );
	wp_enqueue_style( 'p2020-sans' );

	// Vendor
	wp_enqueue_script( 'p2020-modernizr', get_template_directory_uri() . '/js/vendor/modernizr-custom.js', [], '20200416', true );
	wp_enqueue_script( 'p2020-debounce', get_template_directory_uri() . '/js/vendor/jquery.ba-throttle-debounce.min.js', [], '20200416', true );

	// Main enqueued file
	wp_enqueue_script(
		'p2020-js',
		get_template_directory_uri() . '/js/enqueued-main.js',
		[
			'o2-enquire',
			'p2020-modernizr',
		],
		'20200416',
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'p2020-keyboard-image-navigation', get_template_directory_uri() . '/js/vendor/keyboard-image-navigation.js', [ 'jquery' ], '20120202', true );
	}
}

// Our stylesheets need to be loaded after the O2 stylesheets to take priority
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts', 11 );

function genericons() {
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons.css', [], '20200801' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\genericons', 1 );

function admin_styles() {
	wp_enqueue_style(
		'p2020-admin-common-style',
		get_stylesheet_directory_uri() . '/admin-common-style.css',
		[],
		'20200801'
	);

	if ( is_a8c_p2() ) {
		wp_enqueue_style(
			'p2020-admin-a8c-p2-style',
			get_stylesheet_directory_uri() . '/admin-a8c-p2-style.css',
			[],
			'20200801'
		);
	} else {
		wp_enqueue_style(
			'p2020-admin-non-a8c-p2-style',
			get_stylesheet_directory_uri() . '/admin-non-a8c-p2-style.css',
			[],
			'20200801'
		);
	}
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_styles' );


/**
 * Set Homepage display to latest posts.
 */
function set_homepage_display() {
	$show_on_front = get_option( 'show_on_front' );
	if ( 'posts' !== $show_on_front ) {
		update_option( 'show_on_front', 'posts' );
	}
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\set_homepage_display', 102 );

/**
 * Add recommended widgets to sidebar
 */
function enable_default_widgets() {
	$widget_no = 3;

	$setup_option = get_option( 'p2020_sidebar_setup' );
	if ( empty( $setup_option ) ) {
		return;
	}

	if ( 'reset' === $setup_option ) {
		$sidebars_widgets = [];
	} else {
		$sidebars_widgets = get_option( 'sidebars_widgets' );
	}
	$sidebars_widgets['sidebar-1'] = $sidebars_widgets['sidebar-1'] ?? [];

	// My Team widget (widgets/myteam)
	$widget_instance = "p2020-my-team-widget-{$widget_no}";
	if ( empty( $sidebars_widgets['sidebar-1'] ) || ! in_array( $widget_instance, $sidebars_widgets['sidebar-1'], true ) ) {
		$team_widget_settings = [
			$widget_no => [
				'title' => __( 'Team', 'p2020' ),
				'limit' => 14,
			],
		];
		update_option( 'widget_p2020-my-team-widget', $team_widget_settings );

		// Add to head of sidebar-1 widgets
		array_unshift( $sidebars_widgets['sidebar-1'], "p2020-my-team-widget-{$widget_no}" );
	}

	// Save sidebar updates
	wp_set_sidebars_widgets( $sidebars_widgets );

	// Clear sidebar setup flag afterwards
	update_option( 'p2020_sidebar_setup', false );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\enable_default_widgets' );

/**
 * Hide editor for certain views:
 *     - search results page
 *     - tag archives
 */
function hide_o2_editor( $o2_options ) {
	if ( is_search() || is_tag() ) {
		$o2_options['options']['showFrontSidePostBox'] = false;
	}

	return $o2_options;
}

add_filter( 'o2_options', __NAMESPACE__ . '\hide_o2_editor' );

/**
 * Replace UI strings in O2
 */
function replace_o2_strings( array $o2_options ): array {
	$o2_options['strings']['noPosts']       = __( 'Ready to publish your first post? Simply use the editor above.', 'p2020' );
	$o2_options['strings']['noPostsMobile'] = __( 'Tap the + button in the top right corner to begin writing your first post.', 'p2020' );

	return $o2_options;
}

add_filter( 'o2_options', __NAMESPACE__ . '\replace_o2_strings' );

/**
 * P2tenberg setup
 */
function config_p2tenberg_comment_editor( array $settings ): array {
	$settings['alignWide'] = false;
	return $settings;
}

add_filter( 'p2tenberg_comment_editor', __NAMESPACE__ . '\config_p2tenberg_comment_editor' );

/**
 * Append Contributor block to content on single pages
 */
function append_contributors_block( $content ) {
	if ( ! is_page() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	require_once get_template_directory() . '/inc/contributors.php';

	return $content . get_contributors_block();
}

add_filter( 'the_content', __NAMESPACE__ . '\append_contributors_block' );

/**
 * Hide widgets with P2 replacement versions:
 *  - o2 Filter widget
 *  - Pages widget
 */
function unregister_pages_filter_widgets() {
	// Pages Widget
	if ( ! is_active_widget( false, false, 'pages' ) ) {
		unregister_widget( 'WP_Widget_Pages' );
	}

	// o2 Filter widget
	if ( ! is_active_widget( false, false, 'o2-filter-widget' ) ) {
		unregister_widget( 'o2_Filter_Widget' );
	}
}

add_action( 'widgets_init', __NAMESPACE__ . '\unregister_pages_filter_widgets' );

/**
 * Custom CSS for Customizer > Widgets (admin)
 */
function customizer_widgets_styles( $hook ) {
	if ( 'widgets.php' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'p2020-customizer-widgets', get_template_directory_uri() . '/widgets/customizer.css', [], '20200801' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\customizer_widgets_styles' );

/**
 * Filter: enqueue scripts, hook actions and filters.
 */
function p2020_filter_init() {
	require_once get_template_directory() . '/inc/filter/filter.php';
	\P2020\Filter\enqueue_scripts();
	\P2020\Filter\add_hooks();
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\p2020_filter_init' );

/**
 * Follow: enqueue scripts
 */
function p2020_follow_init() {
	require_once get_template_directory() . '/inc/follow/follow.php';
	\P2020\Follow\enqueue_scripts();
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\p2020_follow_init' );

/**
 * Menu: enqueue scripts, hook actions and filters.
 */
function p2020_menu_init() {
	require_once get_template_directory() . '/inc/menu/menu.php';
	\P2020\Menu\enqueue_scripts();
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\p2020_menu_init' );


