<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'content_background' => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
					2400,
					1600,
					false,
				),
			),
		),
	),
	'design'  => array(
		'document_radius'         => 0,
		'form_button_border'      => 0,
		'form_style'              => 'flat',
		'form_button_rounded'     => 0,
		'form_button_shadow'      => 'flat',
		'form_button_text_shadow' => 'flat',

	),
	'colors'  => array(
		'form_container_background'        => 'transparent',
		'form_button_background'           => '#000',
		'colors_links_below_form_register' => '#fff',
		'colors_links_below_form_back'     => '#fff',
		'colors_links_below_form_policy'   => '#fff',
	),
);

