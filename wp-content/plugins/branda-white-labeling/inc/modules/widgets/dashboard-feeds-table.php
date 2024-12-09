<?php
/**
 * Branda Dashboard Feeds List Table class.
 *
 * @package Branda
 * @subpackage Widgets
 */
if ( ! class_exists( 'Branda_Dashboard_Feeds_Table' ) ) {
	if ( ! class_exists( 'Branda_List_Table' ) ) {
		$file = branda_files_dir( 'class-branda-list-table.php' );
		require_once $file;
	}
	class Branda_Dashboard_Feeds_Table extends Branda_List_Table {

		function __construct() {
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
					'page' => 'branding',
					'tab'  => 'dashboard-feeds',
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
		}

		public function get_columns() {
			$columns = array(
				'cb'    => true,
				'title' => __( 'Title', 'ub' ),
				'url'   => __( 'Feed URL', 'ub' ),
				'link'  => __( 'Site URL', 'ub' ),
			);
			return $columns;
		}

		public function column_title( $item ) {
			$title = isset( $item['title'] ) && ! empty( $item['title'] ) ? $item['title'] : __( '[no title]', 'ub' );
			if ( isset( $item['link'] ) && ! empty( $item['link'] ) ) {
				printf(
					'<a class="branda-feed-ellipsis" data-modal-open="%s" data-modal-mask="true">%s</a>',
					$this->module_class->get_nonce_action( $item['id'], 'edit' ),
					esc_html( $title )
				);
			} else {
				echo esc_html( $title );
			}
		}

		public function column_url( $item ) {
			if ( isset( $item['url'] ) && ! empty( $item['url'] ) ) {
				echo '<div class="branda-feed-ellipsis">';
				echo esc_url( $item['url'] );
				echo '</div>';
			} else {
				echo '&ndash;';
			}
		}

		public function column_link( $item ) {
			if ( isset( $item['link'] ) && ! empty( $item['link'] ) ) {
				echo '<div class="branda-feed-ellipsis">';
				echo esc_url( $item['link'] );
				echo '</div>';
			} else {
				echo '&ndash;';
			}
			printf(
				'<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="%s">',
				esc_attr__( 'Show feed details', 'ub' )
			);
			echo '<i class="sui-icon-chevron-down" aria-hidden="true"></i>';
			echo '</button>';
		}

		public function column_meta( $item ) {
			echo '<div class="sui-box">';
			echo '<div class="sui-box-body branda-accordion-body">';
			$template = sprintf( '/admin/modules/%s/elements/row', $this->module_class->get_module_name() );
			foreach ( $this->config as $one ) {
				$args = array(
					'one'  => $one,
					'item' => $item,
				);
				$this->module_class->render( $template, $args );
			}
			echo '</div>';
			/**
			 * footer
			 */
			$uba = branda_get_uba_object();
			echo '<div class="sui-box-footer sui-space-between">';
			$args = array(
				'data' => array(
					'id'         => $item['id'],
					'modal-open' => $this->module_class->get_nonce_action( $item['id'], 'delete' ),
				),
				'icon' => 'trash',
				'text' => __( 'Delete', 'ub' ),
				'sui'  => array(
					'ghost',
					'red',
				),
			);
			echo $uba->button( $args );
			$args = array(
				'data'  => array(
					'nonce'      => $this->module_class->get_nonce_value( $item['id'], 'edit' ),
					'id'         => $item['id'],
					'modal-open' => $this->module_class->get_nonce_action( $item['id'], 'edit' ),
				),
				'icon'  => 'pencil',
				'text'  => __( 'Edit', 'ub' ),
				'sui'   => 'ghost',
				'class' => $this->module_class->get_name( 'edit' ),
			);
			echo $uba->button( $args );
			echo '</div>';
			echo '</div>';
			$delete_dialog_configuration = array(
				'title'       => __( 'Delete Dashboard Feed', 'ub' ),
				'description' => __( 'Are you sure you wish to permanently delete this dashboard feed?', 'ub' ),
			);
			echo $this->module_class->get_dialog_delete( $item['id'], $delete_dialog_configuration );
			echo $this->module_class->get_feed_form( $item );
		}

		public function prepare_items( $df_items = array() ) {
			/**
			 * Sanitize $df_items
			 */
			if ( ! is_array( $df_items ) ) {
				$df_items = array();
			}
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			foreach ( $df_items as $df_key => $df_item ) {
				if ( ! isset( $df_items[ $df_key ]['number'] ) ) {
					$df_items[ $df_key ]['number'] = $df_key;
				}
			}
			$per_page     = get_option( 'posts_per_page' );
			$current_page = $this->get_pagenum();
			if ( count( $df_items ) > $per_page ) {
				$this->items = array_slice( $df_items, ( ( $current_page - 1 ) * intval( $per_page ) ), intval( $per_page ), true );
			} else {
				$this->items = $df_items;
			}
			$this->set_pagination_args(
				array(
					'total_items' => count( $df_items ), // WE have to calculate the total number of items
					'per_page'    => intval( $per_page ), // WE have to determine how many items to show on a page
					'total_pages' => ceil( intval( count( $df_items ) ) / intval( $per_page ) ), // WE have to calculate the total number of pages
				)
			);
		}

		/**
		 * Display the table
		 *
		 * @since 3.1.0
		 */
		public function display() {
			if ( ! $this->has_items() ) {
				$this->no_items();
				return;
			}
			$singular = $this->_args['singular'];
			$this->display_tablenav( 'top' );
			$this->screen->render_screen_reader_content( 'heading_list' );
			?>
			<div class="sui-accordion sui-accordion-flushed">
				<div class="sui-accordion-header">
					<?php $this->print_column_headers(); ?>
				</div>
			<?php $this->display_rows(); ?>
			</div>
			<?php
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
			foreach ( $columns as $column_key => $column_display_name ) {
				$class = array( 'manage-column', "column-$column_key" );
				if ( 'cb' !== $column_key ) {
					$class[] = 'sui-accordion-col-3';
				}
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
				// $tag = ( 'cb' === $column_key ) ? 'td' : 'th';
				$tag   = 'div';
				$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
				$id    = $with_id ? "id='$column_key'" : '';
				if ( ! empty( $class ) ) {
					$class = "class='" . join( ' ', $class ) . "'";
				}
				echo "<$tag $scope $id $class>$column_display_name</$tag>";
			}
		}

		/**
		 * Generates content for a single row of the table
		 *
		 * @since 3.1.0
		 *
		 * @param object $item The current item
		 */
		public function single_row( $item ) {
			echo '<div class="sui-accordion-item">';
			/**
			 * header
			 */
			echo '<div class="sui-accordion-item-header">';
			$this->single_row_columns( $item );
			echo '</div>';
			/**
			 * body
			 */
			echo '<div class="sui-accordion-item-body">';
			$this->column_meta( $item );
			echo '</div>';
			/**
			 * close
			 */
			echo '</div>';
		}

		/**
		 * Generates the columns for a single row of the table
		 *
		 * @since 3.1.0
		 *
		 * @param object $item The current item
		 */
		protected function single_row_columns( $item ) {
			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
			foreach ( $columns as $column_name => $column_display_name ) {
				$classes = "$column_name column-$column_name";
				if ( $primary === $column_name ) {
					$classes .= ' column-primary';
				}
				if ( 'cb' !== $column_name ) {
					$classes .= ' sui-accordion-col-3';
				}
				if ( in_array( $column_name, $hidden ) ) {
					$classes .= ' hidden';
				}
				// Comments column uses HTML in the display name with screen reader text.
				// Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
				$data       = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';
				$attributes = "class='$classes' $data";
				if ( 'cb' === $column_name ) {
					echo '<div scope="row" class="check-column">';
					echo $this->column_cb( $item );
					echo '</div>';
				} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
					echo call_user_func(
						array( $this, '_column_' . $column_name ),
						$item,
						$classes,
						$data,
						$primary
					);
				} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
					echo "<div $attributes>";
					echo call_user_func( array( $this, 'column_' . $column_name ), $item );
					echo $this->handle_row_actions( $item, $column_name, $primary );
					echo '</div>';
				} else {
					echo "<div $attributes>";
					echo $this->column_default( $item, $column_name );
					echo $this->handle_row_actions( $item, $column_name, $primary );
					echo '</div>';
				}
			}
		}

		/**
		 * No items function, show nice notice.
		 *
		 * @since 3.0.0
		 */
		public function no_items() {
			$args = array(
				'title'       => __( 'No feed added yet!', 'ub' ),
				'description' => __( 'You haven’t added any dashboard feed yet. Click on the “+ Add Feed” button to add one.', 'ub' ),
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
			if ( ! $this->has_items() ) {
				return;
			}
			if ( 'bottom' === $which ) {
				return;
			}
			echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action', 'ub' ) . '</label>';
			echo '<select name="action" class="sui-select-sm sui-select-inline" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
			echo '<option value="-1">' . __( 'Bulk Actions', 'ub' ) . "</option>\n";
			$actions = array(
				'delete' => __( 'Delete', 'ub' ),
			);
			foreach ( $actions as $name => $title ) {
				$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';
				echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
			}
			echo "</select>\n";
			$uba  = branda_get_uba_object();
			$args = array(
				'text'    => __( 'Apply', 'ub' ),
				'sui'     => 'ghost',
				'classes' => array(
					'branda-bulk-delete',
				),
				'data'    => array(
					'dialog' => $this->module_class->get_name( 'delete-bulk' ),
				),
			);
			echo $uba->button( $args );
		}
	}
}

