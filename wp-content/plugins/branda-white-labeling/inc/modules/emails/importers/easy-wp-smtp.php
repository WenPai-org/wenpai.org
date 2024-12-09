<?php

require_once 'class-branda-smtp-importer.php';

class Branda_SMTP_Importer_Easy_WP_SMTP extends Branda_SMTP_Importer {

	public function __construct() {
		$this->option    = 'swpsmtp_options';
		$this->translate = array(
			'header'              => array(
				'from_email'      => 'from_email_field',
				'from_name_force' => 'force_from_name_replace',
				'from_name'       => 'from_name_field',
			),
			'server'              => array(
				'smtp_host'            => array( 'smtp_settings', 'host' ),
				'smtp_type_encryption' => array( 'smtp_settings', 'type_encryption' ),
				'smtp_port'            => array( 'smtp_settings', 'port' ),
				'smtp_insecure_ssl'    => array( 'smtp_settings', 'insecure_ssl' ),
			),
			'smtp_authentication' => array(
				'smtp_authentication' => array( 'smtp_settings', 'autentication' ),
				'smtp_username'       => array( 'smtp_settings', 'username' ),
				'smtp_password'       => array( 'smtp_settings', 'password' ),
			),
		);
		add_filter( 'branda_smtp_import_swpsmtp_options_smtp_settings_password', array( $this, 'base64_decode' ) );
		add_filter( 'branda_smtp_import_swpsmtp_options_force_from_name_replace', array( $this, 'sanitize_on' ) );
		add_filter( 'branda_smtp_import_swpsmtp_options_smtp_settings_insecure_ssl', array( $this, 'sanitize_on' ) );
	}

	public function import( $module ) {
		$this->module = $module;
		$this->proceed();
	}
}
