<?php
/**
 * Register settings
 *
 * @package     UsernameChanger\Admin\Settings\Register
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Setup the settings menu
 *
 * @since       3.0.0
 * @param       array $menu The default menu settings.
 * @return      array $menu Our defined settings
 */
function username_changer_add_menu( $menu ) {
	$menu['type']       = 'submenu';
	$menu['page_title'] = __( 'Username Changer Settings', 'username-changer' );
	$menu['menu_title'] = __( 'Username Changer', 'username-changer' );

	return $menu;
}
add_filter( 'username_changer_menu', 'username_changer_add_menu' );


/**
 * Define our settings tabs
 *
 * @since       3.0.0
 * @param       array $tabs The default tabs.
 * @return      array $tabs Our defined tabs
 */
function username_changer_settings_tabs( $tabs ) {
	$tabs['settings'] = __( 'Settings', 'username-changer' );
	$tabs['support']  = __( 'Support', 'username-changer' );

	return $tabs;
}
add_filter( 'username_changer_settings_tabs', 'username_changer_settings_tabs' );


/**
 * Define settings sections
 *
 * @since       3.0.0
 * @param       array $sections The default sections.
 * @return      array $sections Our defined sections
 */
function username_changer_registered_settings_sections( $sections ) {
	$sections = array(
		'settings' => apply_filters(
			'username_changer_settings_sections_settings',
			array(
				'main'    => __( 'General Settings', 'username-changer' ),
				'strings' => __( 'String Settings', 'username-changer' ),
			)
		),
		'support'  => apply_filters( 'username_changer_settings_sections_support', array() ),
	);

	return $sections;
}
add_filter( 'username_changer_registered_settings_sections', 'username_changer_registered_settings_sections' );


/**
 * Disable save button on unsavable tabs
 *
 * @since       3.0.0
 * @return      array $tabs The updated tabs
 */
function username_changer_define_unsavable_tabs() {
	$tabs = array( 'support' );

	return $tabs;
}
add_filter( 'username_changer_unsavable_tabs', 'username_changer_define_unsavable_tabs' );


/**
 * Define our settings
 *
 * @since       3.0.0
 * @param       array $settings The default settings.
 * @return      array $settings Our defined settings
 */
function username_changer_registered_settings( $settings ) {
	$new_settings = array(
		// General Settings.
		'settings' => apply_filters(
			'username_changer_settings_settings',
			array(
				'main'    => array(
					array(
						'id'   => 'settings_header',
						'name' => __( 'General Settings', 'username-changer' ),
						'desc' => '',
						'type' => 'header',
					),
					array(
						'id'            => 'allowed_roles',
						'name'          => __( 'Allowed Roles', 'username-changer' ),
						'desc'          => __( 'Select the user roles which are permitted to change their own username.', 'username-changer' ),
						'type'          => 'multicheck',
						'options'       => username_changer_get_user_roles(),
						'tooltip_title' => __( 'Allowed Roles', 'username-changer' ),
						'tooltip_desc'  => __( 'Administrators can always change usernames, and are the only role capable of changing other users username.', 'username-changer' ),
					),
					array(
						'id'            => 'minimum_length',
						'name'          => __( 'Minimum Length', 'username-changer' ),
						'desc'          => __( 'Specify the minimum allowed username length.', 'username-changer' ),
						'type'          => 'number',
						'size'          => 'small-text',
						'min'           => 3,
						'step'          => 1,
						'std'           => 3,
						'tooltip_title' => __( 'Minimum Length', 'username-changer' ),
						'tooltip_desc'  => __( 'The minimum allowed length for usernames is {minlength} characters.', 'username-changer' ),
					),
					array(
						'id'   => 'email_header',
						'name' => __( 'Email Settings', 'username-changer' ),
						'desc' => '',
						'type' => 'header',
					),
					array(
						'id'            => 'enable_notifications',
						'name'          => __( 'Enable Email Notifications', 'username-changer' ),
						'desc'          => __( 'Enable to send notification emails when usernames are changed.', 'username-changer' ),
						'type'          => 'checkbox',
						'tooltip_title' => __( 'Enable Email Notifications', 'username-changer' ),
						'tooltip_desc'  => __( 'Notifications are not sent when a user changes their own username.', 'username-changer' ),
					),
					array(
						'id'   => 'email_subheader',
						'name' => '',
						'desc' => '',
						'type' => 'hook',
					),
					array(
						'id'   => 'email_subject',
						'name' => __( 'Email Subject', 'username-changer' ),
						'desc' => __( 'Specify the subject for username change notifications.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Username change notification - {sitename}', 'username-changer' ),
					),
					array(
						'id'   => 'email_message',
						'name' => __( 'Email Message', 'username-changer' ),
						'desc' => __( 'Specify the message to send for username change notifications.', 'username-changer' ),
						'type' => 'editor',
						'std'  => __( 'Howdy! We\'re just writing to let you know that your username for {siteurl} has been changed to {new_username}.', 'username-changer' ) . "\n\n" . __( 'Login now at {loginurl}', 'username-changer' ),
					),
				),
				'strings' => array(
					array(
						'id'   => 'button_labels_header',
						'name' => __( 'Button Labels', 'username-changer' ),
						'desc' => '',
						'type' => 'header',
					),
					array(
						'id'   => 'change_button_label',
						'name' => __( 'Change Button Label', 'username-changer' ),
						'desc' => __( 'Customize the text for the \'change username\' button.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Change Username', 'username-changer' ),
					),
					array(
						'id'   => 'save_button_label',
						'name' => __( 'Save Button Label', 'username-changer' ),
						'desc' => __( 'Customize the text for the save button.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Save Username', 'username-changer' ),
					),
					array(
						'id'   => 'cancel_button_label',
						'name' => __( 'Cancel Button Label', 'username-changer' ),
						'desc' => __( 'Customize the text for the cancel button.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Cancel', 'username-changer' ),
					),
					array(
						'id'   => 'messages_header',
						'name' => __( 'Messages', 'username-changer' ),
						'desc' => 'test',
						'type' => 'header',
					),
					array(
						'id'   => 'messages_subheader',
						'name' => '',
						'desc' => '',
						'type' => 'hook',
					),
					array(
						'id'   => 'please_wait_message',
						'name' => __( 'Please Wait Message', 'username-changer' ),
						'desc' => __( 'Customize the text displayed while usernames are being checked.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Please wait...', 'username-changer' ),
					),
					array(
						'id'   => 'success_message',
						'name' => __( 'Username Changed Message', 'username-changer' ),
						'desc' => __( 'Customize the message displayed when a username is changed successfully.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Username successfully changed to {new_username}.', 'username-changer' ),
					),
					array(
						'id'   => 'relogin_message',
						'name' => __( 'Relogin Message', 'username-changer' ),
						'desc' => __( 'Customize the text for the relogin link shown if a user changes their own username.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Click here to log back in.', 'username-changer' ),
					),
					array(
						'id'   => 'error_short_username',
						'name' => __( 'Short Username Error', 'username-changer' ),
						'desc' => __( 'Customize the error displayed when a username is too short.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'Username is too short, the minimum length is {minlength} characters.', 'username-changer' ),
					),
					array(
						'id'            => 'error_wrong_permissions',
						'name'          => __( 'Wrong Permissions Error', 'username-changer' ),
						'desc'          => __( 'Customize the error displayed when a user attempts to change a username they do not have permission to change.', 'username-changer' ),
						'type'          => 'text',
						'std'           => __( 'You do not have the correct permissions to change this username.', 'username-changer' ),
						'tooltip_title' => __( 'Wrong Permissions Error', 'username-changer' ),
						'tooltip_desc'  => __( 'In normal circumstances, this message should never be triggered. It exists only to provide an extra layer of security against unauthorized use.', 'username-changer' ),
					),
					array(
						'id'   => 'error_duplicate_username',
						'name' => __( 'Duplicate Username Error', 'username-changer' ),
						'desc' => __( 'Customize the error displayed when a user attempts to change a username to something that is already in use.', 'username-changer' ),
						'type' => 'text',
						'std'  => __( 'The username {new_username} is already in use. Please try again.', 'username-changer' ),
					),
				),
			)
		),
		'support'  => apply_filters(
			'username_changer_settings_support',
			array(
				array(
					'id'   => 'support_header',
					'name' => __( 'Username Changer Support', 'username-changer' ),
					'desc' => '',
					'type' => 'header',
				),
				array(
					'id'   => 'system_info',
					'name' => __( 'System Info', 'username-changer' ),
					'desc' => '',
					'type' => 'sysinfo',
				),
			)
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'username_changer_registered_settings', 'username_changer_registered_settings' );


/**
 * Display the subheader for the emails section
 *
 * @since       3.1.0
 * @return      void
 */
function username_changer_display_email_subheader() {
	?>
	<div class="username-changer-settings-note">
		<span class="note-title"><?php esc_attr_e( 'Template Tags', 'username-changer' ); ?></span>
		<p><?php esc_attr_e( 'Emails allow the use of the following template tags:', 'username-changer' ); ?></p>
		<?php username_changer_tags_list( 'email' ); ?>
	</div>
	<?php
}
add_action( 'username_changer_email_subheader', 'username_changer_display_email_subheader' );


/**
 * Display the subheader for the messages section
 *
 * @since       3.0.0
 * @return      void
 */
function username_changer_display_messages_subheader() {
	?>
	<div class="username-changer-settings-note">
		<span class="note-title"><?php esc_attr_e( 'Template Tags', 'username-changer' ); ?></span>
		<p><?php esc_attr_e( 'The message settings fields allow the use of the following template tags:', 'username-changer' ); ?></p>
		<?php username_changer_tags_list( 'message' ); ?>
	</div>
	<?php
}
add_action( 'username_changer_messages_subheader', 'username_changer_display_messages_subheader' );
