<?php

class Deco_Mistape_Ajax extends Deco_Mistape_Abstract {

	public $request;
	public $reported_text;
	public $url;
	public $post_id;
	public $post_author;
	public $user;
	public $reporter_ip;
	private static $supported_actions = array(
		'mistape_report_error',
		'mistape_preview_dialog',
		'update-plugin',
	);

	public static function maybe_instantiate() {
		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], self::$supported_actions ) ) {
			new self;
		}
	}

	/**
	 * Constructor
	 */
	protected function __construct() {

		parent::__construct();

		// Load textdomain
		$this->load_textdomain();

		// frontend
		add_action( 'wp_ajax_mistape_report_error', array( $this, 'ajax_process_report' ) );
		add_action( 'wp_ajax_nopriv_mistape_report_error', array( $this, 'ajax_process_report' ) );

		// admin preview
		add_action( 'wp_ajax_mistape_preview_dialog', array( $this, 'ajax_update_admin_dialog' ) );
	}

	/**
	 * Load plugin defaults
	 */
	public function init() {

		// sanitize $_POST
		$this->request       = array(
			'selection'       => ! empty( $_POST['selection'] ) ? sanitize_text_field( stripslashes( $_POST['selection'] ) ) : '',
			'word'            => ! empty( $_POST['word'] ) ? sanitize_text_field( stripslashes( $_POST['word'] ) ) : '',
			'context'         => ! empty( $_POST['context'] ) ? sanitize_text_field( stripslashes( $_POST['context'] ) ) : '',
			'replace_context' => ! empty( $_POST['replace_context'] ) ? sanitize_text_field( stripslashes( $_POST['replace_context'] ) ) : '',
			'comment'         => ! empty( $_POST['comment'] ) ? sanitize_text_field( stripslashes( $_POST['comment'] ) ) : '',
		);
		$this->reported_text = $this->get_reported_text();
		$this->url           = wp_get_referer();
		$this->post_id       = ! empty( $_POST['post_id'] ) && (int) $_POST['post_id'] > 0 ? (int) $_POST['post_id'] : url_to_postid( $this->url );
		$this->post_author   = $this->post_id ? (int) get_post_field( 'post_author', $this->post_id ) : null;
		$this->user          = wp_get_current_user();
		$this->reporter_ip   = self::get_ip_address();

		$this->recipient_email = $this->get_recipient_email();
	}

	/**
	 * Handle AJAX reports
	 */
	public function ajax_process_report() {
		$this->init();

		if ( ! $this->validate_ip() ) {
			wp_send_json_error( array(
				'title'   => __( 'Report not sent', 'mistape' ),
				'message' => __( 'Spam protection: too many reports from your IP address.', 'mistape' ),
			) );
		}

		// do not process reports without selection data
		if ( ! $this->request['selection'] ) {
			wp_send_json_error( array(
				'title'   => __( 'Report not sent', 'mistape' ),
				'message' => __( 'No text selected.', 'mistape' ),
			) );
		}

		do_action( 'mistape_process_report', $this );

		$db_data = $this->get_db_data();
		if ( $this->is_report_unique( $db_data ) ) {
			$this->record_report( $db_data );
		} else {
			wp_send_json_error( array(
				'title'   => __( 'Thanks!', 'mistape' ),
				'message' => __( "Our editors already got notified about this error. Thanks for your care.", 'mistape' ),
			) );
		}

		if ( $stop = apply_filters( 'mistape_custom_email_handling', false, $this ) ) {
			return;
		}

		$result = $this->send_email();

		self::statistics( 1 );

		if ( $result ) {
			wp_send_json_success( array(
				'title'   => __( 'Thanks!', 'mistape' ),
				'message' => __( 'Our editors are notified.', 'mistape' ),
			) );
		} else {
			wp_send_json_error( array(
				'title'   => __( 'Report not sent', 'mistape' ),
				'message' => __( "Email service returned an error while trying to deliver your report.", 'mistape' ),
			) );
		}
	}

	public function ajax_update_admin_dialog() {
		if ( ! empty( $_POST['mode'] ) ) {
			$args = array(
				'mode'                  => $_POST['mode'],
				'reported_text_preview' => 'Lorem <span class="mistape_mistake_highlight">upsum</span> dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
			);
			wp_send_json_success( $this->get_dialog_html( $args ) );
		}

		wp_send_json_error();
	}

	/**
	 * Get recipient email
	 */
	public function get_recipient_email() {
		if ( $this->options['email_recipient']['post_author_first'] == 'yes' && $this->post_author ) {
			return get_the_author_meta( 'user_email', $this->post_author );
		}

		if ( $this->options['email_recipient']['type'] == 'other' && $this->options['email_recipient']['email'] ) {
			return $this->options['email_recipient']['email'];
		} elseif ( $this->options['email_recipient']['type'] != 'other' && $this->options['email_recipient']['id'] ) {
			return get_the_author_meta( 'user_email', $this->options['email_recipient']['id'] );
		} else {
			return get_bloginfo( 'admin_email' );
		}
	}

	/**
	 * duplicate of original WP function excluding user capabilities check
	 *
	 * @param int    $id
	 * @param string $context
	 *
	 * @return mixed|null|void
	 */
	public static function get_edit_post_link( $id = 0, $context = 'display' ) {
		if ( ! $post = get_post( $id ) ) {
			return null;
		}

		if ( 'revision' === $post->post_type ) {
			$action = '';
		} elseif ( 'display' == $context ) {
			$action = '&amp;action=edit';
		} else {
			$action = '&action=edit';
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return null;
		}

		// this part of original WP function is commented out
		/*if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		*/

		return apply_filters( 'get_edit_post_link', admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) ), $post->ID, $context );
	}

	public function get_reported_text() {
		if ( is_null( $this->reported_text ) ) {
			$req                 = $this->request;
			$this->reported_text = self::get_formatted_reported_text( $req['selection'], $req['word'], $req['replace_context'], $req['context'] );
		}

		return $this->reported_text;
	}

	public function get_email_body() {
		$reported_text = $this->get_reported_text();

		// referrer
		$message = '<p>' . __( 'Reported from page:', 'mistape' ) . ' ';
		$message .= ! empty( $this->url ) ? '<a href="' . $this->url . '">' . urldecode( $this->url ) . '</a>' : _x( 'unknown', '[Email] Reported from page: unknown', 'mistape' );
		$message .= "</p>\n";

		// post edit link
		if ( $this->post_id ) {
			if ( $edit_post_link = $this->get_edit_post_link( $this->post_id, 'raw' ) ) {
				$message .= '<p>' . __( 'Post edit URL:', 'mistape' ) . ' <a href="' . $edit_post_link . '">' . $edit_post_link . "</a></p>\n";
			}
		}

		// reported by
		if ( $this->user->ID ) {
			$message .= '<p>' . __( 'Reported by:', 'mistape' ) . ' ' . $this->user->display_name . ' (<a href="mailto:' . $this->user->data->user_email . '">' . $this->user->data->user_email . "</a>)</p>\n";
		}
		// reported text
		$message .= '<h3>' . __( 'Reported text', 'mistape' ) . ":</h3>\n";
		$message .= '<div style="padding: 8px; border: 1px solid #eee; font-size: 18px; line-height: 26px"><code>' . $reported_text . "</code></div>\n";

		if ( $this->request['comment'] ) {
			$message .= '<h3>' . __( 'Comment:', 'mistape' ) . "</h3>\n";
			$message .= '<div style="padding: 8px; border: 1px solid #eee; font-size: 14px; line-height: 20px">' . $this->request['comment'] . "</div>\n";
		}

		return apply_filters( 'mistape_mail_message', $message, $this->url, $this->user );
	}

	public function send_email() {
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$to      = apply_filters( 'mistape_mail_recipient', $this->recipient_email, $this->url, $this->user );
		$subject = apply_filters( 'mistape_mail_subject', __( 'Spelling error reported', 'mistape' ), $this->url, $this->user );
		$message = $this->get_email_body();

		return wp_mail( $to, $subject, $message, $headers );
	}

	public function get_db_data() {
		return array(
			'blog_id'                   => get_current_blog_id(),
			'post_id'                   => $this->post_id,
			'post_author'               => $this->post_author,
			'reporter_user_id'          => $this->user->ID ? $this->user->ID : null,
			'reporter_IP'               => $this->reporter_ip,
			'date'                      => current_time( 'mysql' ),
			'date_gmt'                  => current_time( 'mysql', true ),
			'selection'                 => $this->request['selection'],
			'selection_word'            => $this->request['word'] != $this->request['selection'] ? $this->request['word'] : null,
			'selection_replace_context' => $this->request['replace_context'] != $this->request['word'] ? $this->request['replace_context'] : null,
			'selection_context'         => $this->request['context'] != $this->request['replace_context'] ? $this->request['context'] : null,
			'comment'                   => $this->request['comment'],
			'url'                       => $this->url,
			'agent'                     => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null,
		);
	}

	public function record_report( $data ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'mistape_reports';
		$result     = $wpdb->insert( $table_name, $data );
		do_action( 'mistape_new_record', $data, $result );
	}

	public function is_report_unique( $data ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'mistape_reports';

		$blog_id = get_current_blog_id();

		$existing = $wpdb->get_results( $wpdb->prepare( "SELECT selection, selection_word FROM $table_name WHERE url=%s AND blog_id = $blog_id AND date >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)", $data['url'] ), ARRAY_A );

		foreach ( $existing as $existing_record ) {
			$existing_record['selection_word'] = $existing_record['selection_word'] ? $existing_record['selection_word'] : $existing_record['selection'];

			// if selection contains (or is contained in) any of existing records of the past 10 days, return false
			if ( ( strpos( $data['selection'], $existing_record['selection'] ) !== false
			       || strpos( $existing_record['selection'], $data['selection'] ) !== false
			     ) &&
			     // also check the nearest context of selection--the word (since selection can be as short as one letter)
			     ( strpos( $data['selection_word'], $existing_record['selection_word'] ) !== false
			       || strpos( $existing_record['selection_word'], $data['selection_word'] ) !== false
			     )
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get current user IP Address.
	 * @return string
	 * @package: woocommerce
	 */
	public static function get_ip_address() {
		if ( isset( $_SERVER['X-Real-IP'] ) ) {
			return $_SERVER['X-Real-IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			return trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return null;
	}

	public function validate_ip() {
		global $wpdb;

		// check banlist;
		if ( self::is_ip_in_banlist( $this->reporter_ip ) ) {
			return false;
		}
		$blog_id = get_current_blog_id();
		// check reporting frequency
		$sql            = $wpdb->prepare( "SELECT date  FROM `" . $wpdb->base_prefix . self::DB_TABLE . "` WHERE reporter_IP='%s' and blog_id = $blog_id AND date >= DATE_ADD(CURDATE(), INTERVAL -1 DAY) ORDER BY date DESC", $this->reporter_ip );
		$todays_reports = $wpdb->get_results( $sql, ARRAY_A );

		// check total reports for past 24 hours
		if ( count( $todays_reports ) > 30 ) {
			return false;
		}

		// check reports for past 30 and 5 minutes
		$count_per_30_min = 0;
		$count_per_5_min  = 0;
		foreach ( $todays_reports as $report ) {
			$report_timestamp = strtotime( $report['date'] );
			$seconds_passed   = current_time( 'timestamp' ) - $report_timestamp;
			if ( $seconds_passed < 5 * MINUTE_IN_SECONDS ) {
				$count_per_5_min ++;
			} elseif ( $seconds_passed < 30 * MINUTE_IN_SECONDS ) {
				$count_per_30_min ++;
			} else {
				break;
			}
		}

		if ( $count_per_30_min > 15 || $count_per_5_min > 5 ) {
			return false;
		}

		return true;
	}

}
