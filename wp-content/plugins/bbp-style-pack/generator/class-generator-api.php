<?php

namespace Wicked_Block_Builder\REST_API\v1;

use \WP_Error;
use \Exception;
use \WP_REST_Server;
use \WP_REST_Request;
use \WP_REST_Response;
use \WP_Query;

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

class Generator_API extends REST_API {

    public function __construct() {
        $this->register_routes();
    }

	public function register_routes() {
		register_rest_route( $this->base, '/generator/posts', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_posts' ),
				'permission_callback' => array( $this, 'get_posts_permissions_check' ),
				'args' => array(
					's' => array(
						'default' 	=> '',
					),
					'post_type' 	=> array(
						'default' 	=> array(),
					),
					'post__in' 	=> array(
						'default' 	=> array(),
					),
					'posts_per_page' => array(
						'default' 	=> 10,
					),
					'page' => array(
						'default' 	=> 1,
					),
				),
			),
		) );
	}

	public function get_posts_permissions_check( $request ) {
		return current_user_can( 'read' );
	}

	public function get_posts( $request ) {
		$post_data 	= array();
		$query 		= array(
			'post_type' 		=> 'any',
			'post_status' 		=> array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page' 	=> $request->get_param( 'posts_per_page' ),
			'paged' 			=> $request->get_param( 'page' ),
		);
		$search 	= $request->get_param( 's' );
		$post_type 	= $request->get_param( 'post_type' );
		$post__in 	= $request->get_param( 'post__in' );

		// In case a comma-separated list is used...
		if ( $post_type && ! is_array( $post_type ) ) $post_type = explode( ',', $post_type );
		if ( $post__in && ! is_array( $post__in ) ) $post__in = explode( ',', $post__in );

		if ( $search ) $query['s'] = $search;
		if ( $post__in ) $query['post__in'] = $post__in;
		if ( $post_type ) $query['post_type'] = $post_type;

		$query = apply_filters( 'wbb_generator_posts_query', $query, $request );
		$query = new WP_Query( $query );
		$posts = $query->get_posts();

		foreach ( $posts as $post ) {
			$post_data[] = ( object ) array(
				'id' 		=> $post->ID,
				'title' 	=> $post->post_title,
				'author' 	=> $post->post_author,
				'status' 	=> $post->post_status,
				'menuOrder' => $post->menu_order,
				'excerpt' 	=> $post->post_exerpt,
				'name' 		=> $post->post_name,
				'parent' 	=> $post->post_parent,
				'type' 		=> $post->post_type,
			);
		}

		$result = ( object ) array(
			'posts' 		=> $post_data,
			'foundPosts' 	=> $query->found_posts,
			'maxNumPages' 	=> $query->max_num_pages,
		);

		return new WP_REST_Response( $result, 200 );
	}
}
