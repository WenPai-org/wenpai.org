<?php
class bspbbPressModToolsPlugin_Styles extends bspbbPressModToolsPlugin {

	public static function init() {

		$self = new self();

		// Enqueue CSS
		add_action( 'wp_enqueue_scripts', array( $self, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $self, 'admin_enqueue_scripts' ) );

	}

	/**
	 * Enqueue Front End Scripts/CSS
	 * @since  0.1.0
	 */
	function wp_enqueue_scripts() {
		
		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __DIR__ ) . 'css/front.css', '', $this->version );

	}

	/**
	 * Enqueue Admin Scripts/CSS
	 * @since  1.0.0
	 */
	function admin_enqueue_scripts() {

		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __DIR__ ) . 'css/admin.css', '', $this->version );

	}

}

bspbbPressModToolsPlugin_Styles::init();
