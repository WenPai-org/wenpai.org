<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'content_background'  => array(
			array(
				'meta' => array(
					$url . '/background.jpg',
					2400,
					1600,
					false,
				),
			),
		),
		'content_show_back'   => 'off',
		'content_show_policy' => 'off',
		'logo_image_meta'     => array(
			$url . '/logo.png',
			36,
			36,
		),
	),
	'design'  => array(
		'logo_width'              => 36,
		'logo_opacity'            => 80,
		'logo_position'           => 'center',
		'document_radius'         => 0,
		'form_button_border'      => 0,
		'form_style'              => 'flat',
		'form_button_rounded'     => 0,
		'form_button_shadow'      => 'flat',
		'form_button_text_shadow' => 'flat',
		'form_margin_left'        => 0,
		'form_margin_right'       => 0,
		'canvas_width'            => 100,
		'canvas_width_units'      => '%',
		'background_crop'         => 'width',
		'background_crop_width_p' => 87,
	),
	'colors'  => array(
		'form_button_background'    => '#383c44',
		'canvas_background'         => '#fff',
		'links_below_form_register' => '#0076c2',
	),
	'css'     => array(
		'css' => '
html body {
    display: flex;
    align-items: stretch;
    align-content: flex-start;
    background-position: 100% 100%;
    background-size: 60%;
}
.branda-login {
    width: 40%;
    min-width: 350px;
    display: flex;
    align-items: center;
}
#login {
    margin: 0 auto;
    width: 320px;
}
#login h1 {
     margin-bottom: 0;
}
.login {
    display: flex;
}
.login #login #nav,
.login #loginform {
    max-width: 320px;
}
',
	),
);

