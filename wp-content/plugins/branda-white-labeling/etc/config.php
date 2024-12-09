<?php
// If we are on a campus install then we should be hiding some of the modules
if ( ! defined( 'UB_ON_CAMPUS' ) ) {
	define( 'UB_ON_CAMPUS', false ); }
// Allows the branding admin menus to be hidden on a single site install
if ( ! defined( 'UB_HIDE_ADMIN_MENU' ) ) {
	define( 'UB_HIDE_ADMIN_MENU', false ); }
// Allows the main blog to be changed from the default with an id of 1
if ( ! defined( 'UB_MAIN_BLOG_ID' ) ) {
	define( 'UB_MAIN_BLOG_ID', 1 ); }

/**
 * Group list
 *
 * @since 3.0.0
 */
function branda_get_groups_list() {
	$groups = array(
		'admin'     => array(
			'icon'                  => 'dashicons-dashboard',
			'title'                 => __( 'Admin Area', 'ub' ),
			'documentation_chapter' => 'admin-area',
			'description'           => __( 'Customize the different parts of your WordPress Admin Area.', 'ub' ),
		),
		'widgets'   => array(
			'icon'                  => 'thumbnails',
			'title'                 => __( 'Widgets', 'ub' ),
			'documentation_chapter' => 'widgets',
			'description'           => __( 'Customize the existing widgets or add custom feeds to the WordPress Dashboard. Also, update the Meta widget shown on the front-end to match your branding.', 'ub' ),
		),
		'emails'    => array(
			'icon'                  => 'mail',
			'title'                 => __( 'Emails', 'ub' ),
			'documentation_chapter' => 'emails',
			'description'           => __( 'Completely customize the design, content and headers of outgoing emails from your WordPress website or setup a SMTP server.', 'ub' ),
		),
		'front-end' => array(
			'icon'                  => 'monitor',
			'title'                 => __( 'Front-end', 'ub' ),
			'documentation_chapter' => 'front-end',
			'description'           => __( 'Customize every bit of the front-end of your WordPress website to match your website’s theme.', 'ub' ),
		),
		'data'      => array(
			'icon'                  => 'cloud-migration',
			'title'                 => __( 'Settings', 'ub' ),
			'documentation_chapter' => 'settings',
			'description'           => __( 'Import existing configurations to setup Branda within few seconds or export this installation’s configurations for other websites.', 'ub' ),
			'menu-position'         => 'bottom',
		),
		'utilities' => array(
			'icon'                  => 'wrench-tool',
			'title'                 => __( 'Utilities', 'ub' ),
			'documentation_chapter' => 'utilities',
			'description'           => __( 'Utilities are additional components of your website that can be customized to your liking.', 'ub' ),
		),
	);
	return $groups;
}

/**
 * Modules list
 *
 * @since 1.9.4
 */
function branda_get_modules_list( $mode = 'full' ) {
	global $wp_version, $branda_network, $branda_modules_list;

	if ( 'keys' == $mode && ! empty( $branda_modules_list ) ) {
		return $branda_modules_list;
	}
	$modules = array(
		/**
		 * data
		 *
		 * @since 1.8.6
		 */
		'utilities/data.php'                    => array(
			'module'                 => 'data',
			'name'                   => __( 'Data', 'ub' ),
			'description'            => __( 'Control what to do with your settings and data.', 'ub' ),
			'group'                  => 'data',
			'instant'                => 'on',
			'hide-on-dashboard'      => true,
			'add-bottom-save-button' => true,
			'options'                => array( 'ub_data' ),
			'allow-override'         => 'allow',
			'allow-override-message' => 'hide',
		),
		/**
		 * Permissions
		 * URGENT: it must be first loaded module!
		 * DO NOT move it from begining of this array.
		 *
		 * @since 3.1.0
		 */
		'utilities/permissions.php'             => array(
			'module'                 => 'permissions',
			'name'                   => __( 'Permissions', 'ub' ),
			// 'description' => __( 'Use this tool to allow modules in subsites to override the Branda network configurations.', 'ub' ),
			'group'                  => 'data',
			'instant'                => 'on',
			'add-bottom-save-button' => true,
			'public'                 => true,
			'options'                => array(
				'ub_permissions',
			),
			'allow-override'         => 'no',
			'hide-on-dashboard'      => true,
		),
		'admin/bar.php'                         => array(
			'module'         => 'admin-bar',
			'name'           => __( 'Admin Bar', 'ub' ),
			'description'    => __( 'Customize the admin bar with the ability to change the admin bar logo, control the visibility of menu items, add custom menu items or reorder the existing ones.', 'ub' ),
			'public'         => true,
			'group'          => 'admin',
			'options'        => array(
				'ub_admin_bar',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'wdcab',
				'admin_bar_logo',
				'ub_admin_bar_menus',
				'ub_admin_bar_order',
				'ub_admin_bar_style',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		'admin/footer.php'                      => array(
			'module'         => 'admin-footer-text',
			'name'           => __( 'Admin Footer', 'ub' ),
			'description'    => __( 'Display a custom text in the footer of every admin page. ', 'ub' ),
			'group'          => 'admin',
			'options'        => array(
				'ub_admin_footer',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'admin_footer_text',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Admin menu
		 *
		 * @since 3.0.0
		 */
		'admin/menu.php'                        => array(
			'module'         => 'admin_menu',
			'since'          => '3.0.0',
			'name'           => __( 'Admin Menu', 'ub' ),
			'description'    => __( 'This module allows you to fully customize the admin menu by user role or by custom user.  You can add, hide and reorder the menu items as needed. You can enable the Link Manager or the "Dashboard" link from the admin panel for users without a site (in WP Multisite).', 'ub' ),
			'group'          => 'admin',
			'public'         => true,
			'wp'             => '3.5',
			'options'        => array(
				'ub_admin_menu',
				'ub_custom_admin_menu',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Admin Message
		 */
		'admin/message.php'                     => array(
			'module'         => 'admin-message',
			'name'           => __( 'Admin Message', 'ub' ),
			'description'    => __( 'Display a custom message in the WordPress admin pages.', 'ub' ),
			'group'          => 'admin',
			'options'        => array(
				'ub_admin_message',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'admin_message',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Color Schemes
		 */
		'admin/color-schemes.php'               => array(
			'module'         => 'color-schemes',
			'name'           => __( 'Color Schemes', 'ub' ),
			'description'    => __( 'Choose which color schemes will be available within the user profile, force color scheme for every user across website/network or set the default color scheme for newly registered users.', 'ub' ),
			'group'          => 'admin',
			'public'         => true,
			'options'        => array(
				'ub_color_schemes',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'ucs_admin_active_plugin_border_color',
				'ucs_admin_active_theme_actions_background_color',
				'ucs_admin_active_theme_background_color',
				'ucs_admin_active_theme_details_background_color',
				'ucs_admin_bar_background_color',
				'ucs_admin_bar_icon_color',
				'ucs_admin_bar_item_hover_background_color',
				'ucs_admin_bar_item_hover_focus_background',
				'ucs_admin_bar_item_hover_focus_color',
				'ucs_admin_bar_item_hover_text_color',
				'ucs_admin_bar_submenu_icon_color',
				'ucs_admin_bar_text_color',
				'ucs_admin_media_progress_bar_color',
				'ucs_admin_media_selected_attachment_color',
				'ucs_admin_menu_background_color',
				'ucs_admin_menu_bubble_background_color',
				'ucs_admin_menu_bubble_text_color',
				'ucs_admin_menu_current_background_color',
				'ucs_admin_menu_current_icons_color',
				'ucs_admin_menu_current_link_color',
				'ucs_admin_menu_current_link_hover_color',
				'ucs_admin_menu_icons_color',
				'ucs_admin_menu_link_color',
				'ucs_admin_menu_link_hover_background_color',
				'ucs_admin_menu_link_hover_color',
				'ucs_admin_menu_submenu_background_color',
				'ucs_admin_menu_submenu_link_color',
				'ucs_admin_menu_submenu_link_hover_color',
				'ucs_background_color',
				'ucs_checkbox_radio_color',
				'ucs_color_scheme_name',
				'ucs_default_color_scheme',
				'ucs_default_link_hover_color',
				'ucs_delete_trash_spam_link_color',
				'ucs_delete_trash_spam_link_hover_color',
				'ucs_disabled_button_background_color',
				'ucs_disabled_button_text_color',
				'ucs_force_color_scheme',
				'ucs_inactive_plugins_color',
				'ucs_primary_button_background_color',
				'ucs_primary_button_hover_background_color',
				'ucs_primary_button_hover_text_color',
				'ucs_primary_button_text_color',
				'ucs_primary_button_text_color_shadow',
				'ucs_primary_button_text_color_shadow_hover',
				'ucs_table_alternate_row_color',
				'ucs_table_list_hover_color',
				'ucs_table_post_comment_icon_color',
				'ucs_table_post_comment_strong_icon_color',
				'ucs_table_view_switch_icon_color',
				'ucs_table_view_switch_icon_hover_color',
				'ucs_visible_color_schemes',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Admin Custom CSS
		 */
		'admin/custom-css.php'                  => array(
			'module'         => 'custom-admin-css',
			'name'           => __( 'Custom CSS', 'ub' ),
			'description'    => $branda_network ? __( 'Add custom CSS which will be added to the header of every admin page for every site.', 'ub' ) : __( 'Add custom CSS which will be added to the header of every admin page.', 'ub' ),
			'group'          => 'admin',
			'options'        => array(
				'ub_admin_css',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'global_admin_css',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		'admin/help-content.php'                => array(
			'module'         => 'admin-help-content',
			'name'           => __( 'Help Content', 'ub' ),
			'description'    => __( 'Change the existing help content, add new help item or add a help sidebar. ', 'ub' ),
			'group'          => 'admin',
			'options'        => array(
				'ub_admin_help_items',
				'ub_admin_help',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'admin_help_content',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Images
		 *
		 * @since 3.0.0
		 */
		'utilities/images.php'                  => array(
			'module'         => 'images',
			'since'          => '3.0.0',
			'name'           => __( 'Images', 'ub' ),
			'description'    => __( 'Add a Favicon and also override the default image filesize limit of WordPress based on different user roles.', 'ub' ),
			'group'          => 'utilities',
			'public'         => true,
			'wp'             => '4.3',
			'options'        => array(
				'ub_images',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'ub_img_upload_filesize_Administrator',
				'ub_img_upload_filesize_Author',
				'ub_img_upload_filesize_Editor',
				'ub_img_upload_filesize_Contributor',
				'ub_img_upload_filesize_Subscriber',
				'ub_img_upload_filesize_administrator',
				'ub_img_upload_filesize_author',
				'ub_img_upload_filesize_editor',
				'ub_img_upload_filesize_contributor',
				'ub_img_upload_filesize_subscriber',
				'ub_favicons',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Site Generator
		 */
		'utilities/site-generator.php'          => array(
			'module'         => 'site-generator',
			'name'           => __( 'Site Generator', 'ub' ),
			'description'    => __( 'Change the “generator information” and “generator link” from WordPress to something you prefer.', 'ub' ),
			'public'         => true,
			'group'          => 'utilities',
			'options'        => array(
				'ub_site_generator_replacement',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'site_generator_replacement',
				'site_generator_replacement_link',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Text Replacement
		 */
		'utilities/text-replacement.php'        => array(
			'module'           => 'text-replacement',
			'name'             => __( 'Text Replacement', 'ub' ),
			'description'      => __( 'Replace any text from your admin pages and/or front-end pages with an easy to use interface. For example, you can use this to replace the word “WordPress” with your own website name.', 'ub' ),
			'public'           => true,
			'group'            => 'utilities',
			'options'          => array(
				'ub_text_replacement',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'translation_table',
			),
			'status-indicator' => 'hide',
			'allow-override'   => 'allow',
			'has-help'         => true,
		),
		/**
		 * Website Mode
		 *
		 * @since 1.9.1
		 */
		'utilities/maintenance.php'             => array(
			'module'         => 'maintenance',
			'name'           => __( 'Website Mode', 'ub' ),
			'wp'             => '4.6',
			'since'          => '1.9.1',
			'description'    => __( 'Enable the Maintenance Mode or the Coming Soon mode for your website and create a custom page that your visitors will see.', 'ub' ),
			'public'         => true,
			'group'          => 'utilities',
			'options'        => array( 'ub_maintenance' ),
			'allow-override' => 'allow',
		),
		/**
		 * Comments Control
		 *
		 * @since 1.8.6
		 */
		'utilities/comments-control.php'        => array(
			'module'         => 'comments-control',
			'name'           => __( 'Comments Control', 'ub' ),
			'description'    => __( 'Disable the comments on the posts, pages or on your entire website. Advanced options such as Whitelisting IPs is also available.', 'ub' ),
			'wp'             => '3.9',
			'public'         => true,
			'group'          => 'utilities',
			'options'        => array(
				'ub_comments_control',
				'ub_comments_control_cpt',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'limit_comments_allowed_ips',
				'limit_comments_denied_ips',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Tracking codes
		 *
		 * @since 2.3.0
		 */
		'utilities/tracking-codes.php'          => array(
			'module'           => 'tracking-codes',
			'since'            => '2.3.0',
			'name'             => __( 'Tracking Codes', 'ub' ),
			'description'      => __( 'Activate this module to insert the tracking code into your website. You can insert the code at different locations such as within the &lt;head&gt;, after &lt;body&gt; or before &lt;/body&gt;. There is also an option to insert the code on the whole website or insert it conditionally.', 'ub' ),
			'group'            => 'utilities',
			'public'           => true,
			'options'          => array( 'ub_tracking_codes' ),
			'status-indicator' => 'hide',
			'allow-override'   => 'allow',
		),
		'emails/headers.php'                    => array(
			'module'         => 'emails-header',
			'name'           => __( 'From Headers', 'ub' ),
			'description'    => __( 'Set a default sender name and sender email for your outgoing WordPress emails.', 'ub' ),
			'public'         => true,
			'group'          => 'emails',
			'options'        => array(
				'ub_emails_headers',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'ub_from_email',
				'ub_from_name',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Email Template
		 *
		 * @since 1.8.4
		 */
		'emails/template.php'                   => array(
			'module'         => 'email-template',
			'name'           => __( 'Email Template', 'ub' ),
			'description'    => __( 'Stop sending “text only” emails from your website. Either select from our pre-designed email templates or bring your own HTML template. This plugin will wrap every WordPress e-mail sent within the HTML template.', 'ub' ),
			'public'         => true,
			'group'          => 'emails',
			'options'        => array(
				'ub_email_template',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'html_template',
			),
			'allow-override' => 'allow',
		),
		/**
		 * Custom Login Screen
		 *
		 * @since 1.8.5
		 */
		'login-screen/login-screen.php'         => array(
			'module'         => 'login-screen',
			'menu_title'     => __( 'Login Screen', 'ub' ),
			'wp'             => '4.6',
			'name'           => __( 'Customize Login Screen', 'ub' ),
			'description'    => __( 'Customize the default login screen with this module. You can either start with one of our pre-designed templates or start from scratch.', 'ub' ),
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array(
				'ub_login_screen',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'global_login_css',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Blog creation: signup code
		 *
		 * @since 2.3.0
		 */
		'login-screen/signup-code.php'          => array(
			'module'         => 'signup-code',
			'name'           => __( 'Signup Screen', 'ub' ),
			'description'    => __( 'Customize the default signup functionality with this module. With the Signup Code feature, you can restrict the user and blog registrations to the user with a specific signup code.', 'ub' ),
			'since'          => '2.2.1',
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array( 'ub_signup_codes' ),
			'allow-override' => 'no', // Global only
		),
		/**
		 * Gmail API using Google OAuth2
		 *
		 * @since 3.3.0
		 */
		// Postponed for 3.5.0
		 /*'emails/google-oauth.php' => array(
			'module' => 'google-oauth',
			'since' => '3.3.0',
			'name' => __( 'Google OAuth', 'ub' ),
			'name_alt' => __( 'Google OAuth', 'ub' ),
			'description' => __( 'Send Email using Gmail API.', 'ub' ),
			'public' => true,
			'group' => 'emails',
			'options' => array( 'ub_google_oauth' ),
			'allow-override' => 'allow',
		),
		*/
		/**
		 * db-error-page
		 *
		 * @since 2.0.0
		 */
		'front-end/db-error-page.php'           => array(
			'module'         => 'db-error-page',
			'main-blog-only' => true,
			'since'          => '2.0.0',
			'name'           => __( 'DB Error Page', 'ub' ),
			'description'    => __( 'Create a custom database error page so next time your visitors don’t just see the “Error Establishing a Database Connection” text error.', 'ub' ),
			'group'          => 'front-end',
			'options'        => array( 'ub_db_error_page' ),
			'allow-override' => 'no', // Global only
		),
		/**
		 * ms-site-check
		 *
		 * @since 2.0.0
		 */
		'front-end/site-status-page.php'        => array(
			'module'         => 'ms-site-check',
			'network-only'   => true,
			'main-blog-only' => true,
			'since'          => '2.0.0',
			'name'           => __( 'Site Status Pages', 'ub' ),
			'description'    => __( 'Create custom pages for deleted, inactive, archived, or spammed blogs.', 'ub' ),
			'group'          => 'front-end',
			'options'        => array( 'ub_ms_site_check' ),
			'allow-override' => 'no', // Global only
		),
		'content/header.php'                    => array(
			'module'         => 'content-header',
			'name'           => __( 'Header Content', 'ub' ),
			'description'    => __( 'Insert any content that you like into the header of every page of your website. For example, you can put some news/notification for your visitors on top of the regular website header.', 'ub' ),
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array(
				'ub_content_header',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'global_header_content',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		'content/footer.php'                    => array(
			'module'         => 'content-footer',
			'name'           => __( 'Footer Content', 'ub' ),
			'description'    => __( 'Insert any content that you like into the footer of every blog or site in your network. For example, You can add embeds,  terms of service links etc.', 'ub' ),
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array( 'ub_global_footer_content' ),
			'allow-override' => 'allow',
		),
		/**
		 * Cookie Notice
		 *
		 * @since 2.2.0
		 */
		'front-end/cookie-notice.php'           => array(
			'module'         => 'cookie-notice',
			'since'          => '2.2.0',
			'name'           => __( 'Cookie Notice', 'ub' ),
			'description'    => __( 'Cookie Notice allows you to elegantly inform users that your site uses cookies and to comply with the EU cookie law GDPR regulations.', 'ub' ),
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array( 'ub_cookie_notice' ),
			'allow-override' => 'allow',
		),
		/**
		 * Author Box
		 *
		 * @since 1.9.1
		 */
		'front-end/author-box.php'              => array(
			'module'         => 'author-box',
			'name'           => __( 'Author Box', 'ub' ),
			'description'    => __( 'Adds a responsive author box at the end of your posts, showing the author name, author gravatar and author description and social profiles.', 'ub' ),
			'public'         => true,
			'group'          => 'front-end',
			'options'        => array( 'ub_author_box' ),
			'allow-override' => 'allow',
		),
		/**
		 * Custom MS email content
		 *
		 * @since 1.8.6
		 */
		'emails/registration.php'               => array(
			'module'         => 'registration-emails',
			'network-only'   => true,
			'main-blog-only' => true,
			'menu_title'     => __( 'Registration Email', 'ub' ),
			'name'           => __( 'MultiSite Registration Emails', 'ub' ),
			'description'    => __( 'Customize the content of new blog notification email, new user signup email or the welcome email sent after site activation in your multisite network.', 'ub' ),
			'public'         => true,
			'group'          => 'emails',
			'options'        => array(
				'ub_registration_emails',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'global_ms_register_mails',
			),
			'allow-override' => 'no', // Global functionality.
		),
		/**
		 * Accessibility settings.
		 *
		 * @since 3.0.0
		 */
		'utilities/accessibility.php'           => array(
			'module'                 => 'accessibility',
			'name'                   => __( 'Accessibility', 'ub' ),
			'description'            => __( 'Enable support for any available accessibility enhancements.', 'ub' ),
			'group'                  => 'data',
			'instant'                => 'on',
			'options'                => array( 'ub_accessibility' ),
			'add-bottom-save-button' => true,
			'hide-on-dashboard'      => true,
			'allow-override'         => 'allow',
			'allow-override-message' => 'hide',
		),
		/**
		 * Export
		 *
		 * @since 1.8.6
		 */
		'utilities/export.php'                  => array(
			'module'                 => 'export',
			'name'                   => __( 'Export', 'ub' ),
			'description'            => __( 'Use this tool to export the Branda configurations.', 'ub' ),
			'group'                  => 'data',
			'instant'                => 'on',
			'allow-override'         => 'allow',
			'allow-override-message' => 'hide',
		),
		/**
		 * Import
		 *
		 * @since 1.8.6
		 */
		'utilities/import.php'                  => array(
			'module'                 => 'import',
			'name'                   => __( 'Import', 'ub' ),
			'description'            => __( 'Use this tool to import the Branda configurations.', 'ub' ),
			'group'                  => 'data',
			'instant'                => 'on',
			'allow-override'         => 'allow',
			'allow-override-message' => 'hide',
		),
		/**
		 * Dashboard Widgets
		 *
		 * @since 3.0.0
		 */
		'widgets/dashboard-widgets.php'         => array(
			'module'         => 'dashboard-widgets',
			'since'          => '3.0.0',
			'name'           => __( 'Dashboard Widgets', 'ub' ),
			'description'    => __( 'Remove default widgets from the dashboard, customize the dashboard welcome message or add new text widgets in the dashboard.', 'ub' ),
			'group'          => 'widgets',
			'options'        => array(
				'ub_dashboard_widgets',
				'ub_dashboard_widgets_items',
				'ub_rwp_all_active_dashboard_widgets',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'ub_custom_welcome_message',
				'ub_remove_wp_dashboard_widgets',
				'wpmudev_dashboard_text_widgets_options',
			),
			'allow-override' => 'allow',
			'has-help'       => true,
		),
		/**
		 * Dashboard Feeds
		 *
		 * @since 1.8.6
		 */
		'widgets/dashboard-feeds.php'           => array(
			'module'           => 'dashboard-feeds',
			'name'             => __( 'Dashboard Feeds', 'ub' ),
			'description'      => __( 'Customize the dashboard for every user in a flash with this straightforward dashboard feed replacement widget. No more WP development news or Matt\'s latest photo set.', 'ub' ),
			'group'            => 'widgets',
			'wp'               => '3.8',
			'options'          => array(
				'branda_dashboard_feeds',
				/**
				 * Deprecated options names before Branda 3.0.0
				 *
				 * @since 3.0.0
				 */
				'wpmudev_df_widget_options',
			),
			'status-indicator' => 'hide',
			'allow-override'   => 'allow',
		),
		'widgets/meta-widget.php'               => array(
			'module'           => 'rebranded-meta-widget',
			'name'             => __( 'Meta Widget', 'ub' ),
			'description'      => __( 'Rebrand the default meta widget in all multisite blogs with one that has the "Powered By" link branded for your site. It will replace the “WordPress.org” link in the meta widget with your website’s title that links back to your site.', 'ub' ),
			'public'           => true,
			'group'            => 'widgets',
			'status-indicator' => 'hide',
			'allow-override'   => 'no', // No configuration!
		),
		/**
		 * Blog creation
		 *
		 * @since 1.9.6
		 */
		'front-end/signup-blog-description.php' => array(
			'module'           => 'signup-blog-description',
			'network-only'     => true,
			'menu_title'       => __( 'Blog Description', 'ub' ),
			'name'             => __( 'Blog Description on Blog Creation', 'ub' ),
			'description'      => __( 'Allows new bloggers to be able to set their tagline when they create a blog in Multisite.', 'ub' ),
			'public'           => true,
			'group'            => 'front-end',
			'options'          => array( '' ),
			'allow-override'   => 'no', // MU only
			'status-indicator' => 'hide',
		),
		/**
		 * SMTP
		 *
		 * @since 2.0.0
		 */
		'emails/smtp.php'                       => array(
			'module'         => 'smtp',
			'since'          => '2.0.0',
			'name'           => __( 'SMTP', 'ub' ),
			'name_alt'       => __( 'SMTP Configuration', 'ub' ),
			'description'    => __( 'SMTP allows you to configure and send all outgoing emails via a SMTP server. This will prevent your emails from going into the junk/spam folder of the recipients.', 'ub' ),
			'public'         => true,
			'group'          => 'emails',
			'options'        => array( 'ub_smtp' ),
			'allow-override' => 'allow',
		),
		/**
		 * Email Logs.
		 *
		 * @since 3.4
		 */
		'emails/email-logs.php'                 => array(
			'module'           => 'email-logs',
			'since'            => '3.4',
			'name'             => __( 'Email Logs', 'ub' ),
			'name_alt'         => __( 'Email Logs', 'ub' ),
			'description'      => __( 'Get detailed information about your emails with Branda Pro. You can check recipients information and export all log history.', 'ub' ),
			'public'           => true,
			'only_pro'         => true,
			'group'            => 'emails',
			'options'          => array( 'ub_email_logs' ),
			'status-indicator' => 'hide',
			'allow-override'   => 'allow',
		),
		/**
		 * Document
		 *
		 * @since 2.3.0
		 */
		'front-end/document.php'                => array(
			'module'         => 'document',
			'since'          => '2.3.0',
			'name'           => __( 'Document', 'ub' ),
			'description'    => __( 'Allow to change defaults for entry display.', 'ub' ),
			'group'          => 'front-end',
			'public'         => true,
			'options'        => array( 'ub_document' ),
			'allow-override' => 'allow',
		),
		/**
		 * Theme Additional CSS
		 *
		 * @since 3.1.3
		 */
		'admin/theme-additional-css.php'        => array(
			'module'       => 'theme-additional-css',
			'network-only' => true,
			'name'         => __( 'Customizer', 'ub' ),
			'description'  => __( 'This feature allows subsite admins to add custom CSS via the Theme Customizer tool.', 'ub' ),
			'group'        => 'admin',
			'public'       => true,
			'options'      => array(
				'ub_theme_additional_css',
			),
		),
	);
	/**
	 * filter by WP version
	 */
	foreach ( $modules as $slug => $data ) {
		if ( isset( $data['wp'] ) ) {
			$compare = version_compare( $wp_version, $data['wp'] );
			if ( 0 > $compare ) {
				unset( $modules[ $slug ] );
			}
		}
	}
	apply_filters( 'ultimatebranding_available_modules', $modules );
	$keys = array_keys( $modules );
	sort( $keys );
	$branda_modules_list = $keys;
	if ( 'keys' == $mode ) {
		return $keys;
	}
	return $modules;
}

