<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'logo_image'            => $url . '/logo.png',
		'logo_image_meta'       => array(
			$url . '/logo.png',
			209,
			209,
		),
		'content_background'    => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
					2400,
					1596,
					false,
				),
			),
		),
		'content_show_register' => 'off',
		'content_show_back'     => 'off',
		'content_show_policy'   => 'off',
		'form_show_remember_me' => 'off',
	),
	'design'  => array(
		'form_style'               => 'flat',
		'form_rounded'             => 10,
		'form_button_shadow'       => 'flat',
		'form_button_rounded'      => 0,
		'form_button_border_width' => 0,
		'logo_position'            => 'center',
		'logo_width'               => 209,
	),
	'colors'  => array(
		'form_container_background' => 'transparent',
		'form_button_background'    => '#228b22',
		'error_messages_link'       => '#228b22',
		'error_messages_link_hover' => '#338c33',
	),
);

