<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


//User Name Shortcode:[wpsite_username]
function wpsite_shortcode_user_name( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $current_user = wp_get_current_user();
  $user_name = $current_user->display_name;

  return $atts['before'] . $user_name . $atts['after'];
}
add_shortcode( 'wpsite_username', 'wpsite_shortcode_user_name' );


//User Login Shortcode: [wpsite_userlogin]
function wpsite_user_login_shortcode() {
    $user_login = '';

    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $user_login = $current_user->user_login;
    }

    return $user_login;
}
add_shortcode( 'wpsite_userlogin', 'wpsite_user_login_shortcode' );


//Nickname Shortcode: [wpsite_nickname]
function wpsite_user_nickname_shortcode() {
    $current_user = wp_get_current_user();
    $user_nickname = $current_user->nickname;
    return $user_nickname;
}
add_shortcode( 'wpsite_nickname', 'wpsite_user_nickname_shortcode' );



//User Bio Shortcode: [wpsite_userbio]
function wpsite_user_bio_shortcode() {
    $current_user = wp_get_current_user();
    $user_bio = $current_user->description;
    return $user_bio;
}
add_shortcode( 'wpsite_userbio', 'wpsite_user_bio_shortcode' );



// User Website Shortcode: [wpsite_website]
function wpsite_user_website_shortcode() {
    $current_user = wp_get_current_user();
    $user_website = $current_user->user_url;
    return $user_website;
}
add_shortcode( 'wpsite_website', 'wpsite_user_website_shortcode' );



//User EMail Shortcode:[wpsite_useremail]
function wpsite_shortcode_user_email( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $user = wp_get_current_user();
  $user_email = $user->user_email;

  return $atts['before'] . $user_email . $atts['after'];
}
add_shortcode( 'wpsite_useremail', 'wpsite_shortcode_user_email' );



//User Avatar Shortcode:[wpsite_avatar]
function wpsite_shortcode_user_avatar( $atts ) {
  $atts = shortcode_atts( array(
    'size' => 256,
    'class' => '',
    'alt' => '',
  ), $atts );

  $user_avatar = get_avatar( get_current_user_id(), $atts['size'], '', '', array(
    'class' => $atts['class'],
    'alt' => $atts['alt'],
  ) );

  return $user_avatar;
}
add_shortcode( 'wpsite_avatar', 'wpsite_shortcode_user_avatar' );



//User Role Shortcode:[wpsite_userrole]
function wpsite_shortcode_user_role( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $user_roles = wp_get_current_user()->roles;
  $user_role = reset($user_roles); // get first user role

  return $atts['before'] . $user_role . $atts['after'];
}
add_shortcode( 'wpsite_userrole', 'wpsite_shortcode_user_role' );



//User Role Name Shortcode:[wpsite_role]
function wpsite_shortcode_role( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
  ), $atts );

  $user_roles = wp_get_current_user()->roles;
  $user_role = reset($user_roles); // get first user role

  $role_name = '';

  $role_map = array(
    'administrator' => __( 'Administrator', 'wpsite-shortcode' ),
    'editor' => __( 'Editor', 'wpsite-shortcode' ),
    'author' => __( 'Author', 'wpsite-shortcode' ),
		'contributor' => __( 'Contributor', 'wpsite-shortcode' ),
		'subscriber' => __( 'Subscriber', 'wpsite-shortcode' ),
  );

  if ( array_key_exists( $user_role, $role_map ) ) {
    $role_name = $role_map[ $user_role ];
  }

  return $atts['before'] . $role_name . $atts['after'];
}
add_shortcode( 'wpsite_role', 'wpsite_shortcode_role' );





//User Registered Date: [wpsite_userdate]
function wpsite_shortcode_user_reg_date( $atts ) {
  $atts = shortcode_atts( array(
    'before' => '',
    'after' => '',
    'format' => get_option( 'date_format' ),
  ), $atts );

  $current_user = wp_get_current_user();
  $user_reg_date = $current_user->user_registered;
  $user_reg_date = date_i18n( $atts['format'], strtotime( $user_reg_date ) );

  return $atts['before'] . $user_reg_date . $atts['after'];
}
add_shortcode( 'wpsite_userdate', 'wpsite_shortcode_user_reg_date' );



// User Last Login: [wpsite_lastlogin]
function user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );

function wpsite_shortcode_user_lastlogin() {
    $current_user = wp_get_current_user();
    $user_ID = $current_user->id;
    $last_login = get_user_meta($user_ID,'last_login',true);

    if ( ! $last_login ) {
        return 'Never';
    }

    $the_login_date = human_time_diff($last_login);
    return $the_login_date;
}

add_shortcode('wpsite_lastlogin','wpsite_shortcode_user_lastlogin');
