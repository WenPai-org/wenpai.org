<?php
/**
 * Plugin name: GlotPress 自定义统计
 * Description: Provides custom stats for users, projects, and discared warnings for translate.wenpai.org.
 * Version:     1.0
 * Author:      树新蜂
 * License:     GPLv2 or later
 */

require_once __DIR__ . '/stats/user.php';
require_once __DIR__ . '/stats/project.php';
require_once __DIR__ . '/stats/warning.php';
require_once __DIR__ . '/stats/discarded-warning.php';

class WPorg_GP_Custom_Stats {

	/**
	 * Holds the user stats instance.
	 *
	 * @var WPorg_GP_User_Stats
	 */
	public $user;

	/**
	 * Holds the project stats instance.
	 *
	 * @var WPorg_GP_Project_Stats
	 */
	public $project;

	/**
	 * Holds the warning stats instance.
	 *
	 * @var WPorg_GP_Warning_Stats
	 */
	public $warnings;

	/**
	 * Holds the discarded warning stats instance.
	 *
	 * @var WPorg_GP_Discarded_Warning_Stats
	 */
	public $discarded_warning;

	public function __construct() {
		add_action( 'gp_init', array( $this, 'init_stats' ) );
	}

	/**
	 * Initializes custom stats classes.
	 */
	public function init_stats() {
		$this->user    = new WPorg_GP_User_Stats();
		$this->project = new WPorg_GP_Project_Stats();
		$this->warnings = new WPorg_GP_Warning_Stats();
		$this->discarded_warning = new WPorg_GP_Discarded_Warning_Stats();
	}
}

function wporg_gp_custom_stats() {
	global $wporg_gp_custom_stats;

	if ( ! isset( $wporg_gp_custom_stats ) ) {
		$wporg_gp_custom_stats = new WPorg_GP_Custom_Stats();
	}

	return $wporg_gp_custom_stats;
}
add_action( 'plugins_loaded', 'wporg_gp_custom_stats' );
