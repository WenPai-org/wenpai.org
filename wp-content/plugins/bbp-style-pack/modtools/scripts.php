<?php
class bspbbPressModToolsPlugin_Scripts extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();
		
		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $self, 'load_scripts' ) );

	}

	public function load_scripts() {
		bsp_modtools_report_post_enqueue();
	}

}

bspbbPressModToolsPlugin_Scripts::init();
