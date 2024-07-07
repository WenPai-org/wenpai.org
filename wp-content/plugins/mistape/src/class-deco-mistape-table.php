<?php

class Deco_Mistape_Table_Addon {
	public static $version = '1.0.1';
	public static $main_instance;
	private static $instance;
	public static $plugin_path;
	public $wp_list_table;

	protected function __construct() {
		self::$plugin_path = dirname( __FILE__, 2 ) . '/mistape-table-addon.php';

		add_filter( 'mistape_show_settings_menu_item', array( $this, 'register_admin_menu' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_filter( 'add_menu_classes', array( __CLASS__, 'show_pending_number' ) );

		add_action( 'mistape_new_record', array( __CLASS__, 'update_counts_transient' ), 10, 2 );

		add_action( 'admin_bar_menu', __CLASS__ . '::admin_bar', 100 );
		add_action( 'admin_print_styles', __CLASS__ . '::print_styles', 100 );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::load_admin_page_style', 10 );

		load_plugin_textdomain( 'mistape-table-addon', false, dirname( plugin_basename( self::$plugin_path ) ) . '/languages' );
	}

	public static function init( Deco_Mistape_Abstract $main_instance ) {
		if ( version_compare( $main_instance::$version, '1.2.0', '>=' ) && static::$instance === null ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

			static::$main_instance = $main_instance;
			static::$instance      = new self;
		}
	}

	/**
	 * @return Deco_Mistape_Table_Addon
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * @return Deco_Mistape_Admin
	 */
	public static function get_main_instance() {
		return self::$main_instance;
	}

	public function register_admin_menu( /*$show_settings_submenu*/ ) {
		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMTQ2cHgiIGhlaWdodD0iMTQ2cHgiIHZpZXdCb3g9IjAgMCAxNDYgMTQ2IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAxNDYgMTQ2IiB4bWw6c3BhY2U9InByZXNlcnZlIj48cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTMzLjIsNTUuNWMtMC44LTIuMi0zLjItMy4zLTUuNC0yLjZjLTAuNiwwLjItMS4xLDAuNS0xLjUsMC45Yy0xLjIsMS4xLTEuNiwyLjgtMS4xLDQuNGMxLjcsNC45LDIuNiw5LjksMi43LDE0LjljMCwwLjUsMCwxLDAsMS40YzAsMS41LTAuMSwzLjEtMC4zLDQuNmMtMC42LDEuOC0yLjMsMy4xLTQuMywyLjljLTEuOC0wLjEtMy4zLTEuNC0zLjctMy4xYzAuNi0xMS43LTIuNS0yMy43LTkuNS0zMy45Yy0xLjMtMS45LTMuOS0yLjQtNS44LTEuMWMtMC4yLDAuMS0wLjQsMC4zLTAuNiwwLjVjLTEuNCwxLjQtMS43LDMuNy0wLjUsNS40YzUuMyw3LjgsOCwxNi45LDguMSwyNS45Yy0wLjQsMTAtNC41LDE5LjMtMTEuOCwyNi4zYy03LjUsNy4yLTE3LjMsMTEuMS0yNy42LDEwLjljLTEwLjMtMC4yLTIwLTQuNC0yNy4yLTExLjhjLTcuMi03LjUtMTEuMS0xNy4zLTEwLjktMjcuNmMwLjItMTAuNCw0LjQtMjAsMTEuOC0yNy4yYzguNi04LjMsMjAuNC0xMi4yLDMyLjItMTAuNWMyLjMsMC4zLDQuNiwwLDYuNy0wLjljMy45LTEuNyw2LjgtNS4zLDcuNC05LjhjMC45LTYuOC0zLjktMTMuMS0xMC43LTE0LjFjLTE5LjUtMi42LTM4LjgsMy43LTUyLjksMTcuM2wwLDBjMCwwLDAsMCwwLDBjMCwwLDAsMCwwLDBjMCwwLDAsMCwwLDBDMTYuMSw0MC4zLDkuMiw1Ni4xLDguOSw3My4xYy0wLjMsMTcsNi4xLDMzLjEsMTcuOSw0NS40bDAsMGMwLDAsMCwwLDAsMGMwLDAsMCwwLDAsMGwwLDBjMTEuOSwxMi4yLDI3LjcsMTkuMSw0NC43LDE5LjRjOS4zLDAuMiwxOC4zLTEuNywyNi41LTUuMmM2LjktMywxMy4zLTcuMiwxOC45LTEyLjZsMCwwYzAsMCwwLDAsMCwwYzAsMCwwLDAsMCwwbDAsMGMxMi43LTEyLjMsMTkuOC0yOS42LDE5LjQtNDcuM2wwLDBDMTM2LjMsNjcsMTM1LjIsNjEuMiwxMzMuMiw1NS41eiIvPjwvc3ZnPg==';

		$hook = add_menu_page( 'Mistape', 'Mistape', 'edit_posts', 'mistape', array(
			$this,
			'print_reports_table'
		), $icon_svg, 50 );
		add_submenu_page( 'mistape', __( 'Error reports', 'mistape-table-addon' ), 'Error reports', 'edit_posts', 'mistape' );
		add_submenu_page( 'mistape', __( 'Settings', 'mistape-table-addon' ), 'Settings', 'manage_options', 'mistape_settings', array(
			static::$main_instance,
			'print_options_page'
		) );

		add_action( "load-$hook", array( $this, 'load_reports_table_view' ) );

		// this suppresses default Mistape settings menu item
		return $show_settings_submenu = false;
	}

	public function print_reports_table() {
		/* @var $wp_list_table Deco_Mistape_Reports_List_Table */
		$wp_list_table = $this->wp_list_table;
		?>
		<div class="wrap">
			<h1><?php _e( 'Mistape Error Reports', 'mistape-table-addon' ); ?></h1>
			<?php do_action( 'mistape_reports_table_top' ); ?>

			<?php
			$wp_list_table->views();
			?>

			<form method="post" id="mistape-reports-list">
				<?php
				$wp_list_table->prepare_items();
				$wp_list_table->display();
				?>
				<br class="clear">
				<input type="hidden" name="page" value="mistape" />
			</form>
		</div>
		<?php
	}

	public function load_reports_table_view() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Reports per page',
			'default' => 20,
			'option'  => 'reports_per_page'
		);

		add_screen_option( $option, $args );

		require_once( 'class-deco-mistape-wp-list-table.php' );
		$this->wp_list_table = new Deco_Mistape_Reports_List_Table();

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ) );
		add_action( 'admin_footer', array( $this, 'output_thickbox' ) );
		add_action( 'admin_footer', array( $this, 'output_styles' ) );
	}

	public function load_scripts_styles() {
		wp_enqueue_script( 'mistape-table-addon', plugins_url( 'assets/js/mistape-table.js', self::$plugin_path ), array( 'jquery' ), self::$version, true );
		wp_localize_script( 'mistape-table-addon', 'decoMistape', array(
			'strings' => array(
				'add_to_ban_list'      => _x( 'Ignore reports from IP %s?', 'text for confirmation dialog', 'mistape-table-addon' ),
				'remove_from_ban_list' => _x( 'Stop blocking IP %s from sending reports?', 'text for confirmation dialog', 'mistape-table-addon' ),
			),
		) );
		self::load_admin_page_style();
	}

	public static function load_admin_page_style() {
		// admin page style
		if ( current_user_can( 'manage_options' ) ) {
			wp_register_style( 'mistape-table_admin-header_style', plugins_url( 'assets/css/admin-header-style.css', self::$plugin_path ) );
			wp_enqueue_style( 'mistape-table_admin-header_style' );
		}
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function output_thickbox() {
		add_thickbox();
		echo '<div id="mistape-thickbox" style="display:none;"><p></p></div>';
	}

	public function output_styles() {
		?>
		<style scoped>
			.banned {
				color:       #C94E50;
				font-weight: bold;
			}
		</style>
		<?php
	}

	public static function get_report_counts_by_users() {
		global $wpdb;
		$counts = array();

		if ( is_multisite() ) {
			$blog_id = get_current_blog_id();
			$blog_id_query = "and blog_id=$blog_id";
        } else {
			$blog_id_query = "";
        }


		$table_name = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
			$results = $wpdb->get_results( "SELECT post_author, COUNT(*) as count, status FROM " . $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE . " WHERE status = 'pending' " . $blog_id_query . " GROUP BY post_author", ARRAY_A );


			$counts = array();
			foreach ( $results as $result ) {
				if ( isset( $counts[ $result['post_author'] ] ) ) {
					$counts[ $result['post_author'] ] += intval( $result['count'] );
				} else {
					$counts[ intval( $result['post_author'] ) ] = intval( $result['count'] );
				}
			}
		}

		return $counts;
	}

	public static function update_counts_transient( $report_data = array(), $succesful_record = true ) {
		if ( $succesful_record ) {
			$reports = self::get_report_counts_by_users();

//			set_transient( 'mistape_pending_counts', $reports, HOUR_IN_SECONDS );

			return $reports;
		}

		return false;
	}

	public static function get_user_pending_reports_count( $force_refresh = true ) {
		if ( $force_refresh || get_transient( 'mistape_pending_counts' ) === false ) {
			$counts = self::update_counts_transient();
		} else {
			$counts = get_transient( 'mistape_pending_counts' );
		}

		if ( current_user_can( 'edit_others_posts' ) ) {
			return array_sum( $counts );
		} else {
			$user_id = get_current_user_id();

			return isset( $counts[ $user_id ] ) ? $counts[ $user_id ] : 0;
		}
	}

	public static function show_pending_number( $menu ) {
		$pending_count = self::get_user_pending_reports_count();

		// loop through $menu items, find match, add indicator
		if ( $pending_count && $menu ) {
			foreach ( $menu as $menu_key => $menu_data ) {
				if ( $menu_data[2] === 'mistape' ) {
					$menu[ $menu_key ][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n( $pending_count ) . '</span></span>';
					break;
				}
			}
		}

		return $menu;
	}

	public static function admin_bar() {
		global $wp_admin_bar;
		$pending_count = self::get_user_pending_reports_count();

		$count_errors = $pending_count;
		if ( $pending_count ) {
			$count_errors = '<span class="wp-adminbar-mistape-table-addon-count-errors">' . $pending_count . '</span>';
		}

		// Create admin bar menu
		$wp_admin_bar->add_menu( array(
			'id'    => 'mistape-table-addon',
//			'title' => '<span class="wp-adminbar-mistape-table-addon-icon"></span>' . __( 'Mistape PRO' ) . $count_errors,
			'title' => '<span class="wp-adminbar-mistape-table-addon-icon"></span>' . $count_errors,
			'href'  => admin_url( 'admin.php?page=mistape' )
		) );
	}

	public static function print_styles() {
		wp_enqueue_style( 'mistape-table-addon-admin', plugins_url( 'assets/css/admin-header-style.css', self::$plugin_path ), array(), self::$version );
	}
}