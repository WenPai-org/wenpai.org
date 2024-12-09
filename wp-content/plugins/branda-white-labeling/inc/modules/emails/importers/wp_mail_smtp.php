<?php

require_once 'class-branda-smtp-importer.php';

class Branda_SMTP_Importer_WP_Mail_SMTP extends Branda_SMTP_Importer {

	public function __construct() {
		$this->option    = 'wp_mail_smtp';
		$this->translate = array(
			'header'              => array(
				'from_email'      => array( 'mail', 'from_email' ),
				'from_name_force' => array( 'mail', 'from_name_force' ),
				'from_name'       => array( 'mail', 'from_name' ),
			),
			'server'              => array(
				'smtp_host'            => array( 'smtp', 'host' ),
				'smtp_type_encryption' => array( 'smtp', 'encryption' ),
				'smtp_port'            => array( 'smtp', 'port' ),
				'smtp_insecure_ssl'    => array( 'smtp', '' ),
			),
			'smtp_authentication' => array(
				'smtp_authentication' => array( 'smtp', 'auth' ),
				'smtp_username'       => array( 'smtp', 'user' ),
				'smtp_password'       => array( 'smtp', 'pass' ),
			),
		);
		add_filter( 'branda_smtp_import_wp_mail_smtp_mail_from_name_force', array( $this, 'sanitize_on' ) );
		add_filter( 'branda_smtp_import_wp_mail_smtp_smtp_auth', array( $this, 'sanitize_on' ) );
	}

	public function import( $module ) {
		$this->module = $module;
		$this->proceed();
	}
}
