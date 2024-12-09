<?php
$url = plugins_url( '', __FILE__ );
return array(
	'content' => array(
		'content_background' => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
				),
			),
		),
	),
	'design'  => array(
		'background_size'         => 'cover',
		'form_rounded'            => 20,
		'form_style'              => 'flat',
		'form_button_shadow'      => 'flat',
		'form_button_text_shadow' => 'flat',
	),
	'colors'  => array(
		'form_background'               => 'rgba(238,255,238,0.75)',
		'form_container_background'     => 'transparent',
		'form_button_background'        => '#191',
		'form_button_background_active' => '#2c992c',
		'form_button_background_focus'  => '#072620',
		'form_button_background_hover'  => '#28ce12',
	),
);

