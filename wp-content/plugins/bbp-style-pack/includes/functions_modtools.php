<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


global $bsp_style_settings_modtools ;
if( !class_exists( 'bbPressModToolsPlugin') && !empty($bsp_style_settings_modtools['modtools_activate']) ) {
add_shortcode ('bsp-moderation-pending' , 'bsp_mod_pending') ;
add_filter ('bbp_toggle_topic', 'rew_redirect_to') ;
add_filter ('bbp_toggle_reply', 'rew_redirect_to') ;
}

function bsp_mod_pending () {
        update_option ( 'bsp_mod_pending_page' , $_SERVER["REQUEST_URI"]) ;	
	if ( bbp_is_user_keymaster() || current_user_can( 'moderate' )) {
		// Start an output buffer
		ob_start();
	?>
		<div id="bbpress-forums" class="bbpress-wrapper">
		<?php if ( bsp_has_pending_results() ) { ?>

			<?php bbp_get_template_part( 'pagination', 'search' ); ?>

			<?php bbp_get_template_part( 'loop',       'pending' ); ?>

			<?php bbp_get_template_part( 'pagination', 'search' ); ?>
			
		</div>
		<?php 
		// Output the current buffer
		$output =  ob_get_clean();
		}
                else $output = __( 'Nothing Pending', 'bbp-style-pack' );
	}
	
	else {
		$output =  __( 'Sorry, You are not authorised to access this page', 'bbp-style-pack' );
		
	}
        
        return $output ;
}

function bsp_has_pending_results( $args = array() ) {

	/** Defaults **************************************************************/

	$default_post_types   = bbp_get_post_types();

	// Default query args
	$default = array(
		'post_type'           => $default_post_types,        // Forums, topics, and replies
		'posts_per_page'      => bbp_get_replies_per_page(), // This many
		'paged'               => bbp_get_paged(),            // On this page
		'orderby'             => 'date',                     // Sorted by date
		'order'               => 'DESC',                     // Most recent first
		'ignore_sticky_posts' => true,                       // Stickies not supported,
		'post_status'		=>	'pending',
		's' 		=> '' ,
		// Conditionally prime the cache for last active posts
		//'update_post_family_cache' => true
	);

	//$default['s'] = '';
		
	/** Setup *****************************************************************/

	// Parse arguments against default values
	$r = bbp_parse_args( $args, $default, 'has_pending_results' );
	
	// Get bbPress
	$bbp = bbpress();

	//create serach query
	$bbp->search_query = new WP_Query( $r );
	

	// Add pagination values to query object
	$bbp->search_query->posts_per_page = (int) $r['posts_per_page'];
	$bbp->search_query->paged          = (int) $r['paged'];

	// Never home, regardless of what parse_query says
	$bbp->search_query->is_home        = false;

	// Only add pagination is query returned results
	if ( ! empty( $bbp->search_query->found_posts ) && ! empty( $bbp->search_query->posts_per_page ) ) {

		// Total for pagination boundaries
		$total_pages = ( $bbp->search_query->posts_per_page === $bbp->search_query->found_posts )
			? 1
			: ceil( $bbp->search_query->found_posts / $bbp->search_query->posts_per_page );

		// Pagination settings with filter
		$bbp_search_pagination = apply_filters( 'bbp_search_results_pagination', array(
			'base'    => bbp_get_search_pagination_base(),
			'total'   => $total_pages,
			'current' => $bbp->search_query->paged
		) );

		// Add pagination to query object
		$bbp->search_query->pagination_links = bbp_paginate_links( $bbp_search_pagination );
	}

	// Filter & return
	return apply_filters( 'bbp_has_pending_results', $bbp->search_query->have_posts(), $bbp->search_query );
}

function rew_redirect_to ($retval) {
	//this hooks to the toggle functions of topics and replies, and adds a redirect to get back to the pending page after action
	$shortcode_page = get_option ('bsp_mod_pending_page') ;
	if (!empty ($shortcode_page)) {
		$check = $_SERVER["REQUEST_URI"] ;  //this is the page name followed by the ?request parameters
		if (strpos($check, $shortcode_page) !== false) $retval['redirect_to'] = $shortcode_page ;
	}
return $retval ;
}