<?php
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'Parsedown.php';

// Hook into the 'publish_post' and 'bbp_new_topic' actions
add_action( 'publish_post', 'wpchat_autoreply_create_async_task', 10, 1 );
add_action( 'bbp_new_topic', 'wpchat_autoreply_create_async_task', 10, 1 );

function wpchat_autoreply_create_async_task( $post_ID ) {
	wp_schedule_single_event( time() + 10, 'wpchat_autoreply_fetch_comment', array( $post_ID ) );
}

// Add a new action for the scheduled event
add_action( 'wpchat_autoreply_fetch_comment', 'wpchat_autoreply_fetch_comment_callback', 10, 1 );

function wpchat_autoreply_fetch_comment_callback( $post_ID ) {
	$options       = get_option( 'wpchat_autoreply_options' );
	$accounts      = isset( $options['accounts'] ) && is_array( $options['accounts'] ) ? $options['accounts'] : array();
	$api_urls      = isset( $options['api_urls'] ) && is_array( $options['api_urls'] ) ? $options['api_urls'] : array();
	$account_count = count( $accounts );
	$api_url_count = count( $api_urls );

	if ( $account_count == 0 || $api_url_count == 0 ) {
		return;
	}

	$index   = rand( 1, $account_count );
	$account = $accounts[ $index - 1 ];

	if ( isset( $options['reply_type'] ) && $options['reply_type'] == 'summary' ) {
		$content = "以下的文章为HTML格式，请尽可能简洁地总结它：\n";
	} else {
		$content = "以下的问题为HTML格式，请尽可能简洁地回答它：\n";
	}

	// Get the post content
	$post = get_post( $post_ID );
	if ( ! $post || $post->post_type != 'post' && $post->post_type != 'topic' ) {
		return;
	}
	$content .= $post->post_title . "\n";
	$content .= preg_replace('/<!--[\s\S]*?-->/', '', $post->post_content);

	$messages   = [];
	$messages[] = [
		'role'    => 'system',
		'content' => "你是一个高级 PHP 开发工程师和 WordPress 专家，对于每个问题尽可能详细地回答。",
	];
	$messages[] = [
		'role'    => 'user',
		'content' => $content,
	];

	$i     = 0;
	$reply = false;
	while ( $i < $api_url_count ) {
		$api_url = $api_urls[ $i ];
		$reply   = wpchat_autoreply_request( $api_url, $account['token'], $messages );
		if ( $reply ) {
			break;
		}
		$i ++;
	}
	if ( ! $reply ) {
		return;
	}

	wp_set_current_user( isset( $options['reply_user'] ) ? $options['reply_user'] : 0 );
	global $current_user;

	$information_sources = isset( $options['information_sources'] ) ? $options['information_sources'] : "";

	kses_remove_filters();

	$Parsedown = new Parsedown();
	$Parsedown->setSafeMode( true );

	if ( $post->post_type == 'topic' ) {
		// Prepare the reply data for bbPress topic
		$reply_data = array(
			'post_parent'  => $post_ID,
			'post_author'  => $current_user->ID,
			'post_content' => $Parsedown->text( $reply ) . $information_sources,
			'post_status'  => 'publish',
			'post_type'    => 'reply',
		);

		// Insert the reply
		$reply_id = bbp_insert_reply( $reply_data );
		do_action( 'bbp_new_reply', $reply_id, $post_ID, 0, array(), $current_user->ID, false, $reply_id );
	} else {
		// Prepare the comment data
		$comment = array(
			'comment_post_ID'   => $post_ID,
			'comment_author'    => $current_user->display_name,
			'comment_content'   => $Parsedown->text( $reply ) . $information_sources,
			'user_id'           => $current_user->ID,
			'comment_author_IP' => '127.0.0.1',
			'comment_agent'     => 'WPChat Autoreply',
			'comment_approved'  => 1,
		);

		// Insert the comment
		wp_insert_comment( $comment );
	}
}

function wpchat_autoreply_request( $api, $token, $messages ) {
	$request_data = array(
		'model'       => 'gpt-3.5-turbo',
		'messages'    => $messages,
		'stream'      => false,
		'temperature' => 1,
		'top_p'       => 0.5,
		'n'           => 1,
		'user'        => 'wpchat_autoreply',
	);
	$request_args = array(
		'body'    => json_encode( $request_data ),
		'headers' => array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $token
		),
		'method'  => 'POST',
		'timeout' => 600,
	);
	$response     = wp_remote_post( $api, $request_args );

	// Check for errors
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
		return false;
	}

	$response_data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		error_log( 'WPChat Autoreply: JSON解析错误 ' . $response_data );

		return false;
	}

	if ( ! isset( $response_data['choices'][0]['message']['content'] ) ) {
		error_log( 'WPChat Autoreply: 回答不存在 ' . $response_data );

		return false;
	}

	return $response_data['choices'][0]['message']['content'];
}
