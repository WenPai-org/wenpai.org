<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Deco_Mistape_Reports_List_Table extends WP_List_Table {

	public $query_string;
	public $nonce;
	public $report_counts;
	public $last_action;

	function __construct() {
		parent::__construct( array(
			'singular' => 'mistape_report',
			'plural'   => 'mistape_reports',
			'ajax'     => true
		) );

		$this->get_last_action_result();

		$this->query_string_maintenance();

		$this->nonce = wp_create_nonce( $this->_args['plural'] );

		$this->process_bulk_action();

		$this->get_report_counts();

		add_action( 'mistape_reports_table_top', array( $this, 'admin_notice_bulk_actions' ) );
	}

	/**
	 * Retrieve reports from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_reports( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

		$results    = array();
		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {

			$sql = "SELECT * FROM " . $table_name;

			$status       = ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'pending';
			$status_trash = 'trash';
			if ( $status === 'all' ) {
				$sql .= $wpdb->prepare( " WHERE status!='%s'", $status_trash );
			} elseif ( $status === 'mine' ) {
				$sql .= $wpdb->prepare( " WHERE status='pending' AND post_author='%d'", get_current_user_id() );
			} else {
				$sql .= $wpdb->prepare( " WHERE status='%s'", $status );
			}

			if ( ! current_user_can( 'edit_others_posts' ) ) {
				$sql .= $wpdb->prepare( " AND post_author='%d'", get_current_user_id() );
			}

			if ( is_multisite() ) {
				$sql .= $wpdb->prepare( " AND blog_id='%d'", get_current_blog_id() );
			}

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				$sql .= $wpdb->prepare( " ORDER BY '%s'", $_REQUEST['orderby'] );
				$sql .= ! empty( $_REQUEST['order'] ) && in_array( $_REQUEST['order'],
					array( 'asc', 'desc' ) ) ? ' ' . $_REQUEST['order'] : ' desc';
			} else {
				$sql .= " ORDER BY ID DESC";
			}

			if ( $per_page > 0 ) {
				$sql .= ' LIMIT ' . (int) $per_page;
				$sql .= ' OFFSET ' . ( absint( $page_number ) - 1 ) * $per_page;
			}

			$results = $wpdb->get_results( $sql, 'ARRAY_A' );
		}

		return $results;
	}

	public function get_report_counts() {
		global $wpdb;

		$num_posts = array(
			'total'   => 0,
			'mine'    => 0,
			'resolve' => 0,
			'pending' => 0,
			'archive' => 0,
			'trash'   => 0
		);

		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {

			$only_own_posts        = ! current_user_can( 'edit_others_posts' );
			$sql_where_post_author = $only_own_posts ? $wpdb->prepare( " AND post_author='%d'", get_current_user_id() ) : '';
			if ( is_multisite() ) {
				$blog_id       = get_current_blog_id();
				$blog_id_query = "and blog_id=$blog_id";
			} else {
				$blog_id_query = "";
			}
			$db_name          = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
			$sql              = "SELECT status, COUNT(*) AS count FROM $db_name WHERE 1=1 $sql_where_post_author " . $blog_id_query . " GROUP BY status";
			$results_statuses = (array) $wpdb->get_results( $sql, ARRAY_A );

			foreach ( $results_statuses as $status ) {
				$num_posts[ $status['status'] ] = (int) $status['count'];
			}

			$num_posts['total'] = array_sum( $num_posts ) - $num_posts['trash'];

			if ( ! $only_own_posts ) {
				$sql               = $wpdb->prepare( "SELECT COUNT(*) FROM $db_name WHERE post_author='%d' AND status='pending' " . $blog_id_query, get_current_user_id() );
				$num_posts['mine'] = (int) $wpdb->get_var( $sql );
			}
		}
		$this->report_counts = $num_posts;
	}

	public function get_views() {
		$current_status = ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'pending';

		$status_links = array();

		$class = '';

		if ( $current_status == 'all' ) {
			$class = 'current';
		}

		$all_inner_html = sprintf(
			_nx(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				$this->report_counts['total'],
				'reports',
				'mistape-table-addon'
			),
			$this->report_counts['total']
		);

		$status_links['all'] = '<a href="' . remove_query_arg( 'status' ) . '&status=all" class="' . $class . '">' . $all_inner_html . '</a>';

		$statuses = array(
			'mine'    => sprintf(
				_nx(
					'Mine <span class="count">(%s)</span>',
					'Mine <span class="count">(%s)</span>',
					$this->report_counts['mine'],
					'reports',
					'mistape-table-addon'
				),
				$this->report_counts['mine']
			),
			'resolve' => sprintf(
				_nx(
					'<i class="dashicons dashicons-yes"></i> Resolved <span class="count">(%s)</span>',
					'<i class="dashicons dashicons-yes"></i> Resolved <span class="count">(%s)</span>',
					$this->report_counts['resolve'],
					'reports',
					'mistape-table-addon'
				),
				$this->report_counts['resolve']
			),
			'pending' => sprintf(
				_nx(
					'<i class="dashicons dashicons-warning"></i> Pending <span class="count">(%s)</span>',
					'<i class="dashicons dashicons-warning"></i> Pending <span class="count">(%s)</span>',
					$this->report_counts['pending'],
					'reports',
					'mistape-table-addon'
				),
				$this->report_counts['pending']
			),
			'archive' => sprintf(
				_nx(
					'<i class="dashicons dashicons-minus"></i> Archive <span class="count">(%s)</span>',
					'<i class="dashicons dashicons-minus"></i> Archive <span class="count">(%s)</span>',
					$this->report_counts['archive'],
					'reports',
					'mistape-table-addon'
				),
				$this->report_counts['archive']
			),
			'trash'   => sprintf(
				_nx(
					'<i class="dashicons dashicons-no"></i> Trash <span class="count">(%s)</span>',
					'<i class="dashicons dashicons-no"></i> Trash <span class="count">(%s)</span>',
					$this->report_counts['trash'],
					'reports',
					'mistape-table-addon'
				),
				$this->report_counts['trash']
			),
		);

		foreach ( $statuses as $status_name => $status_label ) {
			if ( empty( $this->report_counts[ $status_name ] ) && $current_status != $status_name ) {
				continue;
			}

			$class = '';

			if ( $current_status === $status_name ) {
				$class = 'current';
			}

			$status_links[ $status_name ] = '<a href="' . add_query_arg( 'status', $status_name, $this->query_string ) . '" class="' . $class . '">' . $status_label . '</a>';
		}

		return $status_links;
	}

	public function get_last_action_result() {
		if ( isset( $_GET['last_action'] ) ) {
			$this->last_action = array(
				'action'  => filter_input( INPUT_GET, 'last_action', FILTER_SANITIZE_STRING ),
				'success' => filter_input( INPUT_GET, 'success', FILTER_VALIDATE_INT ),
				'failure' => filter_input( INPUT_GET, 'failure', FILTER_VALIDATE_INT ),
			);
		}
	}

	/**
	 * Strip unwanted query vars from the query string or ensure the correct
	 * vars are passed around and those we don't want to preserve are discarded.
	 *
	 * @since 0.0.1
	 */
	public function query_string_maintenance() {
		if ( isset( $_GET['last_action'] ) ) {
			add_action( 'admin_footer', array( $this, 'add_js_script_to_clear_query_string' ) );
		}
		$this->query_string = remove_query_arg( array(
			'action',
			'last_action',
			'success',
			'failure',
			'_wpnonce',
			'report'
		) );
	}

	/**
	 * Archive a Mistape record.
	 *
	 * @param int $id report ID
	 *
	 * @param      $status
	 *
	 * @param null $post_author
	 *
	 * @return false|int
	 */
	public static function set_report_status( $id, $status, $post_author = null ) {
		global $wpdb;

		if ( ! in_array( $status, array( 'pending', 'archive', 'trash', 'resolve' ) ) ) {
			return false;
		}
		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			return false;
		}

		if ( is_multisite() ) {
			$blog_id       = get_current_blog_id();
			$blog_id_query = "and blog_id=$blog_id";
		} else {
			$blog_id_query = "";
		}

		if ( is_array( $id ) ) {
			$ids = array_filter( filter_var( $id, FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY ) );
			$sql = $wpdb->prepare( "UPDATE `" . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . "` SET status='%s' WHERE ID IN (" . implode( ', ', $ids ) . ") " . $blog_id_query, $status );
		} elseif ( is_string( $id ) || is_int( $id ) ) {
			$sql = $wpdb->prepare( "UPDATE `" . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . "` SET status='%s' WHERE ID=%d " . $blog_id_query, $status, $id );
		} else {
			return false;
		}

		if ( $post_author ) {
			$sql .= $wpdb->prepare( " AND post_author='%d'", $post_author );
		}

		return $wpdb->query( $sql );
	}

	/**
	 * Delete a Mistape record.
	 *
	 * @param int|array $id report ID
	 *
	 * @param null $post_author
	 *
	 * @return bool|false|int
	 */
	public static function delete_report( $id, $post_author = null ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			return false;
		}

		if ( is_multisite() ) {
			$blog_id       = get_current_blog_id();
			$blog_id_query = "and blog_id=$blog_id";
		} else {
			$blog_id_query = "";
		}

		if ( is_array( $id ) ) {
			$ids = array_filter( filter_var( $id, FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY ) );
			$sql = "DELETE FROM `" . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . "` WHERE ID IN (" . implode( ', ', $ids ) . ") " . $blog_id_query;
		} elseif ( is_string( $id ) || is_int( $id ) ) {
			$sql = $wpdb->prepare( "DELETE FROM `" . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . "` WHERE ID='%d' " . $blog_id_query, $id );
		} else {
			return false;
		}

		if ( $post_author ) {
			$sql .= $wpdb->prepare( " AND post_author='%d'", $post_author );
		}

		return $wpdb->query( $sql );
	}

	/** Text displayed when no reports are available */
	public function no_items() {
		_e( 'No reports here.', 'mistape-table-addon' );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="reports[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_post_title( $item ) {

		if ( ! $item['post_id'] ) {
			return __( 'Post not defined', 'mistape-table-addon' );
		}

		$post_title = get_post_field( 'post_title', $item['post_id'] );
		$title      = '<a href="' . get_edit_post_link( $item['post_id'] ) . '"><strong>' . $post_title . '</strong></a>';
		$actions    = array();

		if ( $item['status'] !== 'pending' ) {
			$actions['set_as_pending'] = '<a href="' . esc_attr( add_query_arg(
					array(
						'action'   => 'pending',
						'reports'  => $item['ID'],
						'_wpnonce' => $this->nonce
					), $this->query_string ) ) . '">' . __( 'Set as Pending', 'mistape-table-addon' ) . '</a>';
		} else {
			$actions['edit_post'] = '<a href="' . esc_attr( get_edit_post_link( $item['post_id'] ) ) . '">' . __( 'Edit', 'mistape-table-addon' ) . '</a>';
		}

		if ( $item['status'] !== 'archive' ) {
			$actions['set_as_pending'] = '<a href="' . esc_attr( add_query_arg(
					array(
						'action'   => 'archive',
						'reports'  => $item['ID'],
						'_wpnonce' => $this->nonce
					), $this->query_string ) ) . '">' . __( 'Archive report', 'mistape-table-addon' ) . '</a>';
		}

		if ( $item['status'] !== 'trash' ) {
			$actions['trash'] = '<a href="' . esc_attr( add_query_arg(
					array(
						'action'   => 'trash',
						'reports'  => $item['ID'],
						'_wpnonce' => $this->nonce
					), $this->query_string ) ) . '" class="trash">' . __( 'Delete report', 'mistape-table-addon' ) . '</a>';
		} else {
			$actions['untrash'] = '<a href="' . esc_attr( add_query_arg(
					array(
						'action'   => 'archive',
						'reports'  => $item['ID'],
						'_wpnonce' => $this->nonce
					), $this->query_string ) ) . '" class="untrash">' . __( 'Restore report', 'mistape-table-addon' ) . '</a>';
			$actions['delete']  = '<a href="' . esc_attr( add_query_arg(
					array(
						'action'   => 'delete',
						'reports'  => $item['ID'],
						'_wpnonce' => $this->nonce
					), $this->query_string ) ) . '" class="trash">' . __( 'Delete Permanently', 'mistape-table-addon' ) . '</a>';
		}

		return $title . $this->row_actions( $actions );
	}

	public function column_post_author( $item ) {

		if ( $item['post_author'] ) {
			$author      = get_user_by( 'id', $item['post_author'] );
			$author_link = get_edit_user_link( $item['post_author'] );

			return '<a href="' . $author_link . '">' . $author->data->display_name . '</a>';
		}

		return '&mdash;';
	}

	public function column_reporter_user_id( $item ) {

		if ( $item['reporter_user_id'] ) {
			$reporter      = get_user_by( 'id', $item['reporter_user_id'] );
			$reporter_link = get_edit_user_link( $item['reporter_user_id'] );

			return '<a href="' . $reporter_link . '">' . $reporter->data->display_name . '</a>';
		} else {
			return __( 'Guest', 'mistape-table-addon' );
		}

	}

	public function column_reporter_IP( $item ) {

		if ( $item['reporter_IP'] ) {
			$is_banned = Deco_Mistape_Table_Addon::get_main_instance()->is_ip_in_banlist( $item['reporter_IP'] );

			$actions = array();
			if ( current_user_can( 'edit_others_posts' ) ) {
				if ( $is_banned ) {
					$actions['unban'] = '<a href="' . esc_attr( add_query_arg(
							array(
								'action'   => 'unban_ip',
								'reports'  => $item['ID'],
								'_wpnonce' => $this->nonce
							), $this->query_string ) ) . '" class="remove-from-ban-list">' . __( 'Remove from ban list', 'mistape-table-addon' ) . '</a>';
				} else {
					$actions['ban'] = '<a href="' . esc_attr( add_query_arg(
							array(
								'action'   => 'ban_ip',
								'reports'  => $item['ID'],
								'_wpnonce' => $this->nonce
							), $this->query_string ) ) . '" class="add-to-ban-list">' . __( 'Add to ban list', 'mistape-table-addon' ) . '</a>';
				}
			}

			$ip_string = '<span class="ip' . ( $is_banned ? ' banned' : '' ) . '">' . $item['reporter_IP'] . '</span>';

			return $ip_string . $this->row_actions( $actions );
		}

		return '';
	}

	function column_date( $item ) {

		if ( $item['date'] ) {
			return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item['date'] ) );
		}

		return '';
	}

	function column_reported_text( $item ) {

		if ( $item['selection'] ) {
			return Deco_Mistape_Admin::get_formatted_reported_text( $item['selection'], $item['selection_word'], $item['selection_replace_context'], $item['selection_context'] );
		}

		return '';
	}

	function column_comment( $item ) {

		if ( $item['comment'] ) {
			if ( str_word_count( $item['comment'] ) > 18 ) {
				$actions = array(
					'show_comment' =>
						'<a href="#TB_inline?width=400&height=200&inlineId=mistape-thickbox&repirtId=' . $item['ID'] . '"
						 class="thickbox"
						 data-thickbox-content="' . esc_attr( $item['comment'] ) . '">' . __( 'Show full comment', 'mistape-table-addon' ) . '</a>',
				);

				return wp_trim_words( $item['comment'], 18 ) . $this->row_actions( $actions );
			} else {
				return $item['comment'];
			}

		}

		return '';
	}

	function column_status( $item ) {

		if ( $item['status'] == 'pending' ) {
			return '<i class="dashicons dashicons-warning ' . $item['status'] . '"></i>';
		} elseif ( $item['status'] == 'resolve' ) {
			return '<i class="dashicons dashicons-yes ' . $item['status'] . '"></i>';
		} elseif ( $item['status'] == 'archive' ) {
			return '<i class="dashicons dashicons-minus ' . $item['status'] . '"></i>';
		} elseif ( $item['status'] == 'trash' ) {
			return '<i class="dashicons dashicons-no ' . $item['status'] . '"></i>';
		}

		return '';
	}

	function column_actions( $item ) {

		if ( $item['status'] ) {
			$actions = array();

			if ( $item['status'] !== 'resolve' && $item['status'] !== 'trash' ) {
				$actions['set_as_resolve'] = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'resolve',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips archive" data-tooltip="' . __( 'Set as Resolved', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-yes"></i></a>';
			}

			if ( $item['status'] !== 'pending' && $item['status'] !== 'resolve' ) {
				$actions['set_as_pending'] = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'pending',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips pending" data-tooltip="' . __( 'Set as Pending', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-warning"></i></a>';
			}

			if ( $item['status'] !== 'archive' && $item['status'] !== 'resolve' ) {
				$actions['set_as_pending'] = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'archive',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips archive" data-tooltip="' . __( 'Archive Report', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-minus"></i></a>';
			}

			if ( $item['status'] !== 'trash' ) {
				$actions['trash'] = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'trash',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips trash" data-tooltip="' . __( 'Move to Trash', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-no"></i></a>';
			} else {
				$actions['untrash']        = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'pending',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips pending" data-tooltip="' . __( 'Set as Pending', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-warning"></i></a>';
				$actions['set_as_pending'] = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'archive',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips archive" data-tooltip="' . __( 'Archive Report', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-minus"></i></a>';
				$actions['delete']         = '<a href="' . esc_attr( add_query_arg(
						array(
							'action'   => 'delete',
							'reports'  => $item['ID'],
							'_wpnonce' => $this->nonce
						), $this->query_string ) ) . '" class="button tips trash" data-tooltip="' . __( 'Delete Report', 'mistape-table-addon' ) . '"><i class="dashicons dashicons-no"></i></a>';
			}

			$out = '<div class="row-actions visible">';
			foreach ( $actions as $action => $link ) {
				$out .= "<span class='$action'>$link</span>";
			}
			$out .= '</div>';

			return $out;
		}

		return '';
	}

	/**
	 * Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'               => '<input type="checkbox" />',
			'status'           => __( 'Status', 'mistape-table-addon' ),
			'post_title'       => __( 'Post Title', 'mistape-table-addon' ),
			'post_author'      => __( 'Post Author', 'mistape-table-addon' ),
			'reporter_user_id' => __( 'Reported by', 'mistape-table-addon' ),
			'reporter_IP'      => __( 'Reported from IP', 'mistape-table-addon' ),
			'date'             => __( 'Date', 'mistape-table-addon' ),
			'reported_text'    => __( 'Selection', 'mistape-table-addon' ),
			'comment'          => __( 'Comment', 'mistape-table-addon' ),
			'actions'          => __( 'Actions', 'mistape-table-addon' ),
		);

		return $columns;
	}

	public function get_hidden_columns() {
		return array(
			'reporter_user_id' => __( 'Reported by', 'mistape-table-addon' ),
			'reporter_IP'      => __( 'Reported from IP', 'mistape-table-addon' ),
			'comment'          => __( 'Comment', 'mistape-table-addon' ),
		);
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'date' => __( 'Date', 'mistape-table-addon' ),
		);
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$current_status = ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'pending';
		$actions        = array();

		if ( $current_status !== 'resolve' ) {
			$actions['bulk-resolve'] = __( 'Set as Resolved', 'mistape-table-addon' );
		}

		if ( $current_status !== 'pending' ) {
			$actions['bulk-pending'] = __( 'Set as Pending', 'mistape-table-addon' );
		}

		if ( ! in_array( $current_status, array( 'archive', 'trash' ) ) ) {
			$actions['bulk-archive'] = __( 'Archive reports', 'mistape-table-addon' );
		}

		if ( $current_status !== 'trash' ) {
			$actions['bulk-trash'] = __( 'Delete reports', 'mistape-table-addon' );
		} else {
			$actions['bulk-untrash'] = __( 'Restore', 'mistape-table-addon' );
			$actions['bulk-delete']  = __( 'Delete Permanently', 'mistape-table-addon' );
		}

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$per_page     = $this->get_items_per_page( 'reports_per_page', 20 );
		$current_page = $this->get_pagenum();

		$total_items = $this->report_counts['pending'];
		if ( ! empty( $_GET['status'] ) ) {
			if ( in_array( $_GET['status'], array_keys( $this->report_counts ) ) ) {
				$total_items = $this->report_counts[ $_GET['status'] ];
			} elseif ( $_GET['status'] == 'all' ) {
				$total_items = $this->report_counts['total'];
			}
		}

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );


		$this->items = self::get_reports( $per_page, $current_page );
	}

	/**
	 * Handles bulk actions as well as quick actions
	 */
	public function process_bulk_action() {

		if ( ! ( $action = $this->current_action() ) || ! isset( $_REQUEST['reports'] ) ) {
			return;
		}

		$nonce_prefix = '';
		if ( ! empty( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'bulk-' ) === 0
		     || ! empty( $_REQUEST['action2'] ) && strpos( $_REQUEST['action2'], 'bulk-' ) === 0
		) {
			$nonce_prefix = 'bulk-';
		}
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce_prefix . $this->_args['plural'] ) ) {
			$html = '<br><br><p>' . __( 'Are you sure you want to do this?', 'mistape-table-addon' ) . '</p>';
			if ( wp_get_referer() ) {
				$html .= "<p><a href='" . esc_url( remove_query_arg( 'updated', wp_get_referer() ) ) . "'>" . __( 'Please try again.', 'mistape-table-addon' ) . "</a></p>";
			}
			wp_die( $html, __( 'WordPress Failure Notice', 'mistape-table-addon' ), 403 );
		}

		$post_author = null;
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		} elseif ( ! current_user_can( 'edit_others_posts' ) ) {
			$post_author = get_current_user_id();
		}

		$ids = is_array( $_REQUEST['reports'] ) ? filter_var( $_REQUEST['reports'], FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY ) : filter_var( $_REQUEST['reports'], FILTER_SANITIZE_NUMBER_INT );

		switch ( $action ) {
			case 'resolve':
			case 'pending':
			case 'archive':
			case 'trash':
				$result = self::set_report_status( $ids, $action, $post_author );
				break;
			case 'untrash':
				$result = self::set_report_status( $ids, 'archive', $post_author );
				break;
			case 'delete':
				$result = self::delete_report( $ids, $post_author );
				break;
			case 'bulk-resolve':
			case 'bulk-pending':
			case 'bulk-archive':
			case 'bulk-trash':
				$result = self::set_report_status( $ids, substr( $action, 5 ), $post_author );
				break;
			case 'bulk-untrash':
				$result = self::set_report_status( $ids, 'archive', $post_author );
				break;
			case 'bulk-delete':
				$result = self::delete_report( $ids, $post_author );
				break;
			case 'ban_ip':
				$result = current_user_can( 'edit_others_posts' ) ? self::ban_ip_by_reports_id( $ids ) : false;
				break;
			case 'unban_ip':
				$result = current_user_can( 'edit_others_posts' ) ? self::unban_ip_by_reports_id( $ids ) : false;
				break;
			default:
				return;
		}

		delete_transient( 'mistape_pending_counts' );

		$args = array(
			'last_action' => $this->current_action(),
		);

		if ( $result ) {
			$args['success'] = $result;
		}
		if ( $failed = ( count( (array) $ids ) - $result ) ) {
			$args['failure'] = $failed;
		}

		wp_redirect( add_query_arg( $args, $this->query_string ) );
		exit;
	}

	public static function ban_ip_by_reports_id( $ids ) {
		$ips_to_process = self::get_ip_list_by_reports_id( $ids );

		$banlist = (array) get_option( Deco_Mistape_Abstract::IP_BANLIST_OPTION, array() );
		$banlist = array_unique( array_merge( $banlist, $ips_to_process ) );

		update_option( Deco_Mistape_Abstract::IP_BANLIST_OPTION, $banlist );

		return count( $ips_to_process );
	}

	public static function unban_ip_by_reports_id( $ids ) {
		$ips_to_process = self::get_ip_list_by_reports_id( $ids );

		$banlist = (array) get_option( Deco_Mistape_Abstract::IP_BANLIST_OPTION, array() );
		$banlist = array_diff( $banlist, $ips_to_process );

		update_option( Deco_Mistape_Abstract::IP_BANLIST_OPTION, $banlist );

		return count( $ips_to_process );
	}

	/**
	 * Returns list of IPs for given report IDs
	 *
	 * @param $ids
	 *
	 * @return array|bool
	 */
	public static function get_ip_list_by_reports_id( $ids ) {
		global $wpdb;

		if ( empty( $ids ) ) {
			return false;
		}

		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			return false;
		}

		$ids_filtered = array_unique( array_filter( filter_var( $ids, FILTER_VALIDATE_INT, FILTER_FORCE_ARRAY ) ) );
		$ids_string   = implode( ', ', $ids_filtered );

		if ( is_multisite() ) {
			$blog_id       = get_current_blog_id();
			$blog_id_query = "and blog_id=$blog_id";
		} else {
			$blog_id_query = "";
		}

		$sql = $wpdb->prepare( "SELECT DISTINCT reporter_IP FROM `" . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . "` WHERE ID IN (%s) " . $blog_id_query, $ids_string );

		return wp_list_pluck( $wpdb->get_results( $sql, ARRAY_A ), 'reporter_IP' );
	}

	/**
	 * Display an admin notice when a bulk action is completed
	 */
	public function admin_notice_bulk_actions() {
		if ( ! $this->last_action ) {
			return;
		}

		$action  = $this->last_action['action'];
		$success = $this->last_action['success'];
		$failure = $this->last_action['failure'];

		if ( strpos( $action, 'bulk-' ) === 0 ) {
			$action = substr( $action, 5 );
		}

		if ( $success ) :
			?>

            <div id="mistape-notice-bulk-<?php esc_attr( $this->current_action() ); ?>" class="updated">

				<?php if ( $action == 'delete' ) : ?>
                    <p><?php echo sprintf( _n( '%d report deleted successfully.', '%d reports deleted successfully.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php elseif ( $action == 'untrash' ) : ?>
                    <p><?php echo sprintf( _n( '%d report restored from the Trash.', '%d reports restored from the Trash.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php elseif ( $action == 'archive' ) : ?>
                    <p><?php echo sprintf( _n( '%d report archived.', '%d reports archived.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php elseif ( $action == 'pending' ) : ?>
                    <p><?php echo sprintf( _n( '%d report set as pending.', '%d reports set as pending.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php elseif ( $action == 'resolve' ) : ?>
                    <p><?php echo sprintf( _n( '%d report set as resolved.', '%d reports set as resolved.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php elseif ( $action == 'trash' ) : ?>
                    <p><?php echo sprintf( _n( '%d report moved to the Trash.', '%d reports moved to the Trash.', $success, 'mistape-table-addon' ), $success ); ?></p>

				<?php endif; ?>
            </div>

			<?php
		endif;
		if ( $failure ) :
			?>

            <div id="mistape-notice-bulk-<?php esc_attr( $action ); ?>" class="error">
                <p><?php echo sprintf( _n( '%d report had errors and could not be processed.', '%d reports had errors and could not be processed.', $failure, 'mistape-table-addon' ), $failure ); ?></p>
            </div>

			<?php
		endif;
	}

	public function add_js_script_to_clear_query_string() {
		?>
        <script>
            if (window.history.replaceState) {
                var removeParam = function (parameters, url) {
                    var urlparts = url.split('?');
                    if (urlparts.length >= 2) {

                        if (typeof parameters === 'string') {
                            parameters = [parameters];
                        }

                        var pars = urlparts[1].split(/[&;]/g);

                        for (var i = pars.length; i-- > 0;) {
                            for (var j = parameters.length; j-- > 0;) {
                                var prefix = encodeURIComponent(parameters[j]) + '=';
                                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                                    pars.splice(i, 1);
                                    i--;
                                    j = parameters.length;
                                }
                            }
                        }

                        url = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
                        return url;
                    } else {
                        return url;
                    }
                };
                var parametersToRemove = ['last_action', 'success', 'failure', 'reports', '_wpnonce'];
                window.history.replaceState({}, Document.title, removeParam(parametersToRemove, window.location.href));
            }
        </script>
		<?php
	}

}