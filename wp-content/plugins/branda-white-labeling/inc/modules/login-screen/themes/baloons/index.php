<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'content_background'     => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
					2400,
					1750,
					false,
				),
			),
		),
		'content_show_back'      => 'off',
		'content_show_policy'    => 'off',
		'form_check_remember_me' => 'on',
		'logo_image_meta'        => array(
			$url . '/logo.png',
			36,
			36,
		),
	),
	'design'  => array(
		'background_size'         => 'cover',
		'canvas_padding_units'    => 'px',
		'canvas_width'            => 100,
		'canvas_width_units'      => '%',
		'document_radius'         => 0,
		'form_button_border'      => 0,
		'form_button_rounded'     => 0,
		'form_button_shadow'      => 'flat',
		'form_button_text_shadow' => 'flat',
		'form_margin_top'         => 17,
		'form_padding_bottom'     => 2,
		'form_padding_left'       => 50,
		'form_padding_right'      => 50,
		'form_style'              => 'flat',
		'logo_position'           => 'center',
	),
	'colors'  => array(
		'form_button_background'    => '#293d52',
		'form_background'           => '#f9fcff',
		'form_container_background' => '#f9fcff',
		'form_input_label'          => '#637381',
		'links_below_form_register' => '#56a7fd',
	),
	'css'     => array(
		'css' => '
html body { display: flex; align-content: center; justify-content: flex-end; background-position: 0 100%; }
.branda-login { display: flex; align-items: center; justify-content: flex-end; }
#login { padding: 28px 0; margin: 20px 50px; width: 450px; box-shadow: 0 2px 4px 0 rgba(0,0,0,0.5); }
#login h1 { margin-bottom: 0; }
.login #login #nav { padding-left: 50px; padding-right: 50px; }
@media screen and ( max-width: 800px ) {
    html body { justify-content: center; }
    .branda-login { width: auto; margin: 20px auto; padding: 20px; }
}
',
	),
);
