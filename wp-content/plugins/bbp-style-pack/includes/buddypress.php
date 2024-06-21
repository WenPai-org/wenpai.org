<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// this file holds buddypress filters and functions
// Original code taken from plugin members_page_only_for_logged_in_users.
// Heavily modified for bbP Style Pack use.


// function for BuddyPress PROFILE redirects
// This function is used if profiles are switched off, only own profile, or just for logged in users.
function bsp_buddypress_surpress_profile_pages() {
        // if the current page is not a BuddyPress page, just return false (no further BuddyPress checks)
        if ( ! is_buddypress() ) {  
                // BuddyPress doesn't include user pages for some reason, so let's check for it
                if ( ! bp_is_user() ) return false;
        }
        
        global $bp;
        global $bsp_profile;
        $current_user = wp_get_current_user()->ID;

        // set default to false
        $test = false;

        // if only logged in set $test
        if ($bsp_profile['profile'] == 1  && is_user_logged_in() ) $test = true;
        
        //if only users own profile
        if ( $bsp_profile['profile'] == 2 && is_user_logged_in() ) {
            
                // let's find out if the current URL is for the current user profile
                $current_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) ? 'https://' : 'http://' ) . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

                // set test URL
                if ( defined( 'BP_ENABLE_ROOT_PROFILES' ) ) $test_url = $bp->root_domain.'/';
                else $test_url = $bp->root_domain.'/'.BP_MEMBERS_SLUG.'/';
            
                //see if username is in the url - ie matches
                $current_username = wp_get_current_user()->user_nicename;
                
                // check if the current URL is the current user's profile
                if ( strpos( $current_url, $test_url.$current_username) === 0 ) $test = true;
                
        } 
        
        // if turn off all profiles then set in all cases...
        if ($bsp_profile['profile'] == 3 ) $test = false;
        
        // then set true for keymaster
        if ( bbp_is_user_keymaster( $current_user ) ) $test = true;
        
        // and check if moderators are allowed to see
        $role = bbp_get_user_role( $current_user );
        if ($role == 'bbp_moderator' && ( !empty( $bsp_profile['moderator'] ) ) ) $test = true;
        
        // profile viewing not allowed based on all previous checks
        if ( $test == false ) {

                // if current page is a profile page, then let's handle the redirect
                if ( bp_is_user_profile() || bp_is_user() ) {
                        // return true for bsp_bp_profile_redirect() function to actually do a redirect after page headers sent
                        return true;
                }

        }
        // return false for bsp_bp_profile_redirect() function so no redirect happens for sections
        return false;

}
	

// function for BuddyPress SECTIONS redirects
// return true if redirect should happen, false if not
// bsp_bp_redirect() function actually processes the redirects for sections
function bsp_buddypress_surpress_section_pages() {  
        // if the current page is not a BuddyPress page, just return false (no further BuddyPress checks)
        if ( ! is_buddypress() ) {  
                // BuddyPress doesn't include user pages for some reason, so let's check for it
                if ( ! bp_is_user() ) return false;
        }
        
        global $bsp_buddypress_support;
        $bp_sections = array(
                'activity',
                'groups',
                'members',
        );
        $current_user = wp_get_current_user()->ID;

        // set default to false
        $test = false;
        
        // loop through BuddyPress sections
        foreach ( $bp_sections as $section ) {
                if ( ! empty( $bsp_buddypress_support['' . $section . ''] ) ) {
                        $val = $bsp_buddypress_support['' . $section . ''];
                        $mod_val = empty( $bsp_buddypress_support['' . $section . '_mod_visibility'] ) ? false : true;
                        
                        // if only logged in set $test
                        if ( $val == 1  && is_user_logged_in() ) $test = true;
                        
                        // if turn off all profiles then set in all cases...
                        if ( $val == 2 ) $test = false;
                        
                        // then set true for keymaster
                        if ( bbp_is_user_keymaster( $current_user ) ) $test = true;

                        // and check if moderators are allowed to see
                        $role = bbp_get_user_role( $current_user );
                        if ( $role == 'bbp_moderator' && ( ! empty( $mod_val ) ) ) $test = true;
                        
                        // current section not allowed based on all previous checks
                        if ( $test == false ) {
                            
                                // make sure we're on a valid section page for the current section of the loop
                                if ( 
                                        ( bp_is_activity_component() && ( ! bp_is_user() ) && $section == 'activity' ) || 
                                        ( bp_is_groups_component() && ( ! bp_is_user() ) && ( ! bp_is_group_single() ) && $section == 'groups' ) || 
                                        ( bp_is_members_directory() && $section == 'members' )
                                ) {
                                        // return true for bsp_bp_section_redirect() function to actually do a redirect after page headers sent
                                        return true;
                                }
                                
                        }
                }
        }
        // return false for bsp_bp_section_redirect() function so no redirect happens for sections
        return false;

}


// hook into bp_include so we only run code if bp is active
add_action( 'bp_include', 'bsp_buddy1' );		


// hook into the right parts for processing BuddyPress alterations
function bsp_buddy1() {
	global $bsp_profile;
        global $bsp_buddypress_support;
        $bp_sections = array(
                'activity',
                'groups',
                'members',
        );
        
        // HOOK PROFILES
        if ( ! empty( $bsp_profile['profile'] ) ) {
                $val = $bsp_profile['profile'];
                if ( $val == 1  || $val == 2 || $val == 3 ) {
                        //add_action( $hook, 'bsp_buddypress_surpress_profile_pages' );
                        add_action( 'template_redirect', 'bsp_bp_profile_redirect' );
                } 
        }
        
        // HOOK BUDDYPRESS SECTIONS
        foreach ( $bp_sections as $section ) {
                if ( ! empty( $bsp_buddypress_support['' . $section . ''] ) ) {
                        $val = $bsp_buddypress_support['' . $section . ''];
                        if ( $val == 1  || $val == 2 ) {
                                add_action( 'template_redirect', 'bsp_bp_section_redirect' );
                        } 
                }
        }

}


// handle redirects after page loaded and headers already sent for profiles
function bsp_bp_profile_redirect() {
        if ( bsp_buddypress_surpress_profile_pages() === true ) {
                global $bsp_profile;
                $url = empty( $bsp_profile['profile-redirect'] ) ? site_url() : esc_url_raw( $bsp_profile['profile-redirect'] );
                wp_redirect( $url, 301 );
                exit();
        }
}


// handle redirects after page loaded and headers already sent for BuddyPress sections
function bsp_bp_section_redirect() {
        if ( bsp_buddypress_surpress_section_pages() === true ) {
                global $bsp_buddypress_support;
                $url = empty( $bsp_buddypress_support['section-redirect'] ) ? site_url() : esc_url_raw( $bsp_buddypress_support['section-redirect'] );
                wp_redirect( $url, 301 );
                exit();
        }
}


//code to handle multiple pages on user topics and replies if buddypress active
add_filter ('bbp_is_single_user_replies' , 'bsp_check_buddypress_user') ;
add_filter ('bbp_is_single_user_topics' , 'bsp_check_buddypress_user') ;

function bsp_check_buddypress_user ($retval= false) {
	if (function_exists ('buddypress')) {
	//check if we are on a buddpress single user page (and logged out), as this produces a pagination error in buddypress
	//see https://bbpress.trac.wordpress.org/ticket/3355
	if (!is_user_logged_in() && bp_is_user())$retval = true ;
	}	
return $retval ;
}

