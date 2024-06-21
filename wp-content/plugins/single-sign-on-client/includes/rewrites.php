<?php
/**
 * File: rewrites.php
 *
 * @author Justin Greer <justin@justin-greer.com
 * @package WP Single Sign On Client
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class WPOSSO_Rewrites
 *
 */
class WPOSSO_Rewrites {

	function create_rewrite_rules( $rules ) {
		global $wp_rewrite;
		$newRule  = array( 'auth/(.+)' => 'index.php?auth=' . $wp_rewrite->preg_index( 1 ) );
		$newRules = $newRule + $rules;

		return $newRules;
	}

	function add_query_vars( $qvars ) {
		$qvars[] = 'auth';

		return $qvars;
	}

	function flush_rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

$WPOSSO_Rewrites = new WPOSSO_Rewrites();
add_filter( 'rewrite_rules_array', array( $WPOSSO_Rewrites, 'create_rewrite_rules' ) );
add_filter( 'query_vars', array( $WPOSSO_Rewrites, 'add_query_vars' ) );
add_filter( 'wp_loaded', array( $WPOSSO_Rewrites, 'flush_rewrite_rules' ) );
if ( isset( $_GET['auth'] ) && $_GET['auth'] == 'sso' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/includes/callback.php' );
	exit;
}