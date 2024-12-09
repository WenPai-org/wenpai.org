<?php
// Footer.
$args    = array(
	'text' => __( 'Cancel', 'ub' ),
	'sui'  => 'ghost',
	'data' => array(
		'modal-close' => '',
	),
);
$footer  = $this->button( $args );
$args    = array(
	'sui'   => 'blue',
	'text'  => __( 'Save Settings', 'ub' ),
	'class' => 'branda-module-save-email-logs-settings',
);
$footer .= $this->button( $args );

// Dialog.
$args = array(
	'id'           => $id,
	'title'        => __( 'Log Settings', 'ub' ),
	'content'      => $content,
	'confirm_type' => false,
	'footer'       => array(
		'content' => $footer,
		'classes' => array( 'sui-space-between' ),
	),
	'classes'      => array(
		'sui-modal-lg',
		$this->get_name( 'dialog' ),
	),
);
echo $this->sui_dialog( $args );
