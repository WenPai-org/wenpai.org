<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'logo_show'          => 'off',
		'content_background' => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
					2400,
					1596,
					false,
				),
			),
		),
	),
	'colors'  => array(
		'form_container_background'              => 'transparent',
		'colors_links_below_form_back'           => '#aaa',
		'colors_links_below_form_policy'         => '#aaa',
		'colors_links_below_form_register'       => '#aaa',
		'colors_links_below_form_back_hover'     => '#ddd',
		'colors_links_below_form_policy_hover'   => '#ddd',
		'colors_links_below_form_register_hover' => '#ddd',
	),
);

