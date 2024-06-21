<?php
/*
Plugin Name: GP SRT Support
Plugin URI: http://glot-o-matic.com/gp-srt
Description: Add SRT file support to GlotPress.
Version: 0.5
Author: Greg Ross
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin, translate, srt
License: GPLv2 or later
*/

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_srt_init' );
add_filter( 'gp_sort_by_fields', 'gp_srt_sort_by_fields' );

// This function creates the plugin.
function gp_srt_init() {
	include( dirname( __FILE__ ) . '/format_srt.php' );
}

function gp_srt_sort_by_fields( $sort_fields ) {
	$sort_fields['context'] = array( 
		'title' => __( 'Context', 'glotpress' ),
		'sql_sort_by' => 'o.context %s',
	);
	
	return $sort_fields;
}