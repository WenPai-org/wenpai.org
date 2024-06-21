<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'Wicked_Block_Builder\REST_API\v1\REST_API' ) ) {
    include( 'class-rest-api.php' );
}

if ( ! class_exists( 'Wicked_Block_Builder\REST_API\v1\Generator_API' ) ) {
    include( 'class-generator-api.php' );
}


add_action( 'enqueue_block_editor_assets', 	'bsp_enqueue_block_editor_assets');

function bsp_enqueue_block_editor_assets () {

	$deps       = array( 'lodash', 'react', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-data', 'wp-data-controls', 'wp-element', 'wp-polyfill', 'wp-server-side-render' );
    $plugin_url = plugin_dir_url( dirname( __FILE__ ) );
    $version    = 1;

    wp_enqueue_style( 'wicked-block-generator-v1', $plugin_url . '/generator/generator.css', array(), $version );

	wp_enqueue_script( 'wicked-block-generator-v1', $plugin_url . '/generator/generator.js', $deps, $version );

    //still using this, but changed in later wicked block versions
		wp_localize_script( 'wicked-block-generator-v1', 'wickedBlockBuilder', array(
        'restRoot'  => get_rest_url(),
        'restNonce' => wp_create_nonce( 'wp_rest' ),
    ) );

}

add_action( 'rest_api_init', function() {
    $generator_api = new Wicked_Block_Builder\Rest_API\v1\Generator_API();
} );