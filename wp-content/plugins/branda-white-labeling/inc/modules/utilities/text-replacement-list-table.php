<?php
/**
 * Branda Text Replacement List Table class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Branda_Text_Replacement_List_Table' ) ) {
	if ( ! class_exists( 'Branda_List_Table' ) ) {
		$file = branda_files_dir( 'class-branda-list-table.php' );
		require_once $file;
	}
	class Branda_Text_Replacement_List_Table extends Branda_List_Table {

		public function __construct() {
			global $status, $page;
			// Set parent defaults
			parent::__construct(
				array(
					'singular' => 'Archive', // singular name of the listed records
					'plural'   => 'Archive', // plural name of the listed records
					'ajax'     => false, // does this table support ajax?
				)
			);
			$this->url = add_query_arg(
				array(
					'page'   => 'branding_group_utilities',
					'module' => 'text-replacement',
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
		}

		public function get_columns() {
			$columns = array(
				'cb'      => true,
				'find'    => __( 'Text', 'ub' ),
				'replace' => __( 'Replaced with', 'ub' ),
				'actions' => '',
			);
			return $columns;
		}

		function prepare_items( $text_replacement_items = array() ) {
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			foreach ( $text_replacement_items as $text_replacement_key => $text_replacement_item ) {
				if ( ! isset( $text_replacement_items[ $text_replacement_key ]['number'] ) ) {
					$text_replacement_items[ $text_replacement_key ]['number'] = $text_replacement_key;
				}
			}
			$per_page = get_option( 'posts_per_page' );
			$this->set_pagination_args(
				array(
					'total_items' => count( $text_replacement_items ), // WE have to calculate the total number of items
					'per_page'    => intval( $per_page ), // WE have to determine how many items to show on a page
					'total_pages' => ceil( intval( count( $text_replacement_items ) ) / intval( $per_page ) ), // WE have to calculate the total number of pages
				)
			);
			$current_page = $this->get_pagenum();
			if ( count( $text_replacement_items ) > $per_page ) {
				$this->items = array_slice( $text_replacement_items, ( ( $current_page - 1 ) * intval( $per_page ) ), intval( $per_page ), true );
			} else {
				$this->items = $text_replacement_items;
			}
		}

		/**
		 * Print column headers, accounting for hidden and sortable columns.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $with_id Whether to set the id attribute or not
		 */
		public function print_column_headers( $with_id = true ) {
			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
			$current_url                                   = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url                                   = remove_query_arg( 'paged', $current_url );
			if ( isset( $_GET['orderby'] ) ) {
				$current_orderby = $_GET['orderby'];
			} else {
				$current_orderby = '';
			}
			if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
				$current_order = 'desc';
			} else {
				$current_order = 'asc';
			}
			if ( ! empty( $columns['cb'] ) ) {
				$columns['cb'] = $this->get_header_column_cb_all();
			}
			echo '<thead><tr>';
			foreach ( $columns as $column_key => $column_display_name ) {
				$class = array( 'manage-column', "column-$column_key" );
				if ( 'cb' === $column_key ) {
					$class[] = 'check-column';
				}
				if ( $column_key === $primary ) {
					$class[] = 'column-primary';
				}
				$tag   = 'th';
				$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
				$id    = $with_id ? "id='$column_key'" : '';
				if ( ! empty( $class ) ) {
					$class = "class='" . join( ' ', $class ) . "'";
				}
				echo "<$tag $scope $id $class>$column_display_name</$tag>";
			}
			echo '</tr></thead>';
		}

		/**
		 * No items function, show nice notice.
		 *
		 * @since 3.0.0
		 */
		public function no_items() {
			$args = array(
				'title'       => __( 'No text replacement added yet!', 'ub' ),
				'description' => __( 'You haven’t defined any text replacement rule yet. Click on the “+ Add Rule” button to add one.', 'ub' ),
				'button'      => $this->module_class->button_add(),
			);
			$this->module_class->no_items( $args );
		}

		/**
		 * Bulk actions
		 *
		 * @since 3.0.0
		 */
		protected function bulk_actions( $which = '' ) {
			$this->branda_bulk_actions( $which, 'branda-text-replacement-delete-bulk' );
		}
	}
}
