<?php
$url = plugins_url( '', __FILE__ );

return array(
	'content' => array(
		'logo_show'             => 'on',
		'logo_image'            => $url . '/logo.png',
		'form_show_remember_me' => 'on',
		'content_show_back'     => 'off',
		'content_show_policy'   => 'off',
		'logo_image_meta'       => array(
			$url . '/logo.png',
			36,
			36,
		),
	),
	'design'  => array(
		'logo_width'              => 36,
		'form_style'              => 'flat',
		'form_button_rounded'     => 0,
		'form_margin_top'         => 0,
		'form_padding_bottom'     => 0,
		'form_padding_top'        => 0,
		'form_padding_left'       => 0,
		'form_padding_right'      => 0,
		'form_border_style'       => 'solid',
		'form_button_shadow'      => 'flat',
		'form_button_text_shadow' => 'flat',
		'canvas_padding_top'      => 20,
		'canvas_padding_bottom'   => 20,
		'canvas_padding_units'    => 'px',
	),
	'colors'  => array(
		'background_color'                 => '#fff',
		'form_background'                  => 'transparent',
		'form_input_label'                 => '#637381',
		'form_input_border'                => '#bdc4ce',
		'form_input_background'            => '#ffffff',
		'form_button_border'               => '#006799',
		'form_button_background'           => '#0061ff',
		'links_below_form_register'        => '#0061ff',
		'links_below_form_register_active' => '#000',
	),
	'css'     => array(
		'css' => '
body {
	display: flex;
	align-items: center;
}
.branda-login {
	margin-left: auto;
	margin-right: auto;
	background: transparent url(' . $url . '/background.svg) no-repeat 0 50%;
	width: 1000px;
}
.branda-login #login {
	margin: 0 0 0 auto;
	padding: 20px 0;
}
#login form, #login h1 {
	width: 320px;
}
 #login h1 {
	 margin-left: 0;
}
@media screen and ( max-width: 1001px ) {
	.branda-login #login {
		margin: 20px 20px 20px auto;
		padding: 20px;
	}
	.branda-login {
		background-position-x: 20px;
	}
}
@media screen and ( max-width: 800px ) {
	.branda-login {
		width: 100%;
		margin: 20px auto;
		padding: 20px;
		background-color: rgba( 255, 255, 255, .7 );
	}
}
',
	),
);
