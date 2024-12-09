<?php
/**
 * Branda Tracking Codes List Table class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Branda_Tracking_Codes_List_Table' ) ) {
	if ( ! class_exists( 'Branda_List_Table' ) ) {
		$file = branda_files_dir( 'class-branda-list-table.php' );
		require_once $file;
	}
	class Branda_Tracking_Codes_List_Table extends Branda_List_Table {

		public function __construct() {
			parent::__construct(
				array(
					'singular' => 'tracking-code', // singular name of the listed records
					'plural'   => 'tracking-codes', // plural name of the listed records
					'ajax'     => false, // does this table support ajax?
				)
			);
			$this->url = add_query_arg(
				array(
					'page'   => 'branding_group_utilities',
					'module' => 'tracking-codes',
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
		}

		public function column_place( $item ) {
			if ( empty( $item['code'] ) ) {
				return '-';
			}

			switch ( $item['place'] ) {
				case 'head':
					return __( 'In the HEAD tag.', 'ub' );
				case 'body':
					return __( 'After &lt;BODY&gt; tag.', 'ub' );
				case 'footer':
					return __( 'Before &lt;/BODY&gt; tag.', 'ub' );
				default:
					return __( 'Unknown', 'ub' );
			}
			return __( 'Unknown', 'ub' );
		}

		public function column_filters_active( $item ) {
			$mask = '<span class="ub-filters ub-%s">%s</span>';
			if ( isset( $item['filter'] ) && 'on' === $item['filter'] ) {
				return sprintf( $mask, 'active', esc_html__( 'Enabled', 'ub' ) );
			}
			return sprintf( $mask, 'inactive', esc_html__( 'Disabled', 'ub' ) );
		}

		public function column_status( $item ) {
			$mask = '<span class="ub-tracking ub-%s">%s</span>';
			if ( isset( $item['active'] ) && 'on' === $item['active'] ) {
				return sprintf( $mask, 'active', esc_html__( 'Active', 'ub' ) );
			}
			return sprintf( $mask, 'inactive', esc_html__( 'Inactive', 'ub' ) );
		}

		public function column_title( $item ) {
			if ( empty( $item['title'] ) ) {
				return __( '[not set]', 'ub' );
			}
			return $item['title'];
		}

		public function column_provider( $item ) {
			if ( ! empty( $item['code'] ) ) {
				$provider = '';
			} elseif ( empty( $item['provider'] ) ) {
				$provider = $this->module_class->get_provider( $item['id'] );
			} else {
				$provider = $item['provider'];
			}

			return sprintf(
				'<span class="ub-provider ub-provider-%1$s" data-ub-provider="%1$s" data-groupid="%3$s">%2$s</span>',
				$provider,
				! empty( $provider ) ? $this->module_class->get_provider_name( $provider ) : '-',
				$item['id']
			);
		}

		public function get_columns() {
			$columns = array(
				'cb'             => '<input type="checkbox" />', // Render a checkbox instead of text
				'title'          => __( 'Title', 'ub' ),
				'status'         => __( 'Status', 'ub' ),
				'provider'       => __( 'Provider', 'ub' ),
				'place'          => __( 'Insert location', 'ub' ),
				'filters_active' => __( 'Location Filter', 'ub' ),
				'actions'        => '',
			);
			return $columns;
		}

		public function get_sortable_columns() {
			$sortable_columns = array(
				// 'title' => array( 'title',false ),     //true means it's already sorted
			);
			return $sortable_columns;
		}

		public function get_bulk_actions() {
			$actions = array(
				'activate'   => __( 'Activate', 'ub' ),
				'deactivate' => __( 'Deactivate', 'ub' ),
				'delete'     => __( 'Delete', 'ub' ),
				'duplicate'  => __( 'Duplicate', 'ub' ),
			);
			return $actions;
		}

		public function process_bulk_action() {
			$uba = branda_get_uba_object();
			$ids = $names = array();
			if ( isset( $_POST[ $this->_args['singular'] ] ) && is_array( $_POST[ $this->_args['singular'] ] ) ) {
				$ids = $_POST[ $this->_args['singular'] ];
			}
			if ( empty( $ids ) ) {
				return;
			}
			$message = '';
			$update  = true;
			$value   = $this->module_class->local_get_value();
			$action  = $this->current_action();
			switch ( $action ) {
				case 'activate':
					foreach ( $ids as $id ) {
						if ( isset( $value[ $id ] ) ) {
							$value[ $id ]['tracking_active'] = 'on';
							$names[]                         = $value[ $id ]['title'];
						}
					}
					$message = esc_html__( 'Tracking Codes: %s was activated.', 'ub' );
					break;
				case 'deactivate':
					foreach ( $ids as $id ) {
						if ( isset( $value[ $id ] ) ) {
							$value[ $id ]['tracking_active'] = 'off';
							$names[]                         = $value[ $id ]['title'];
						}
					}
					$message = esc_html__( 'Tracking Codes: %s was deactivated.', 'ub' );
					break;
				case 'duplicate':
					foreach ( $ids as $id ) {
						if ( isset( $value[ $id ] ) ) {
							$names[]                = $value[ $id ]['title'];
							$one                    = $value[ $id ];
							$one['title'] .= esc_html__( ' (copy)', 'ub' );
							$one['tracking_active'] = 'off';
							$new_id                 = $this->generate_id( $one );
							$one['id']              = $new_id;
							$value[ $new_id ]       = $one;
						}
					}
					$message = esc_html__( 'Tracking Codes: %s was duplicated. New codes are inactive.', 'ub' );
					break;
				case 'delete':
					foreach ( $ids as $id ) {
						if ( isset( $value[ $id ] ) ) {
							$names[] = $value[ $id ]['title'];
							unset( $value[ $id ] );
						}
					}
					$message = esc_html__( 'Tracking Codes: %s was deleted.', 'ub' );
					break;
				default:
					$update = false;
			}
			if ( $update ) {
				$this->module_class->local_update_value( $value );
				if ( ! empty( $names ) && ! empty( $message ) ) {
					$message = array(
						'type'    => 'success',
						'message' => sprintf(
								$message,
								implode( ', ', array_map( array( $this, 'bold' ), $names ) )
							),
					);
					$uba->add_message( $message );
				}
			}
		}

		private function bold( $a ) {
			return sprintf( '<b>%s</b>', $a );
		}

		/** ************************************************************************
		 * REQUIRED! This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 *
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 **************************************************************************/
		public function prepare_items() {
			/**
			 * First, lets decide how many records per page to show
			 */
			$per_page    = get_option( 'posts_per_page' );
			$data        = $this->module_class->local_get_value();
			$total_items = count( $data );
			usort( $data, array( $this, 'usort_reorder' ) );
			/**
			 * REQUIRED. Now we need to define our column headers. This includes a complete
			 * array of columns to be displayed (slugs & titles), a list of columns
			 * to keep hidden, and a list of columns that are sortable. Each of these
			 * can be defined in another method (as we've done here) before being
			 * used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();

			// We can't use wp_list_pluck because if the `code` key does not exist it will return an error.
			$code_items = array_column( $data, 'code' );

			// No need to shoe Provider column if we only have items with custom code.
			if ( count( $code_items ) === $total_items ) {
				unset( $columns['provider'] );
			}

			// No need to show place if we only use Providers.
			if ( empty( $code_items ) ) {
				unset( $columns['place'] );
			}

			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->process_bulk_action();

			$this->set_pagination_args(
				array(
					'total_items' => $total_items, // WE have to calculate the total number of items
					'per_page'    => $per_page, // WE have to determine how many items to show on a page
					'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages
				)
			);
			$current_page = $this->get_pagenum();
			$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
			$this->items  = $data;
		}

		private function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; // If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; // If no order, default to asc
			if ( ! isset( $a[ $orderby ] ) ) {
				return -1;
			}
			if ( ! isset( $b[ $orderby ] ) ) {
				return 1;
			}
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order
			return ( 'asc' === $order ) ? $result : -$result; // Send final sort direction to usort
		}

		public function print_column_headers( $with_id = true ) {
			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

			echo '<tr>';
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url = remove_query_arg( 'paged', $current_url );

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

			foreach ( $columns as $column_key => $column_display_name ) {
				$class = array( 'manage-column', "column-$column_key" );

				if ( in_array( $column_key, $hidden ) ) {
					$class[] = 'hidden';
				}

				if ( 'cb' === $column_key ) {
					$class[] = 'check-column';
				} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
					$class[] = 'num';
				}

				if ( $column_key === $primary ) {
					$class[] = 'column-primary';
				}

				if ( isset( $sortable[ $column_key ] ) ) {
					list( $orderby, $desc_first ) = $sortable[ $column_key ];
					if ( $current_orderby === $orderby ) {
						$order   = 'asc' === $current_order ? 'desc' : 'asc';
						$class[] = 'sorted';
						$class[] = $current_order;
					} else {
						$order   = $desc_first ? 'desc' : 'asc';
						$class[] = 'sortable';
						$class[] = $desc_first ? 'asc' : 'desc';
					}
					$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
				}

				$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
				$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
				$id    = $with_id ? "id='$column_key'" : '';

				if ( ! empty( $class ) ) {
					$class = "class='" . join( ' ', $class ) . "'";
				}

				echo "<$tag $scope $id $class>$column_display_name</$tag>";
			}
			echo '</tr>';
		}

		/**
		 * No items function, show nice notice.
		 *
		 * @since 3.0.0
		 */
		public function no_items() {
			$args = array(
				'title'       => __( 'No tracking code inserted yet!', 'ub' ),
				'description' => __( 'You haven’t inserted any tracking code yet. Click on the “+ insert code” button to add one.', 'ub' ),
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
			$this->branda_bulk_actions( $which, 'branda-tracking-codes-delete-bulk' );
		}
	}
}