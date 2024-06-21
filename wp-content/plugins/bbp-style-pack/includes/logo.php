<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_logo ($width='448' , $height='448') {
	ob_start();
	?>
	<svg width=" <?php echo $width ?> " height=" <?php echo $height ?> " viewBox="0 0 334.8135980824518 326">
	<defs>
		<linearGradient id="SvgjsLinearGradient1019">
			<stop stop-color="#2d388a" offset="0">
			</stop>
			<stop stop-color="#00aeef" offset="1">
				</stop>
		</linearGradient>
		<style>
		  .cls-1, .cls-3, .cls-4 {
			font-size: 40px;
			text-anchor: middle;
			font-family: "Century Gothic";
			font-weight: 700;
		  }

		  .cls-1 {
			fill: #5180bd;
		  }

		  .cls-2, .cls-4 {
			fill: #b85f5f;
		  }

		  .cls-3 {
			fill: #fff;
		  }
		  .cls-5 {
			font-size: 25px;
		  }
		</style>
	</defs>
	<g featurekey="rootContainer" transform="matrix(1,0,0,1,0,0)" fill="#fb9c2a">
		<path xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" d="M5,65 L167.5,0 L330, 65 L167.5, 10 z M5,246 L167.5,326 L330,241 L167.5, 316 z">
		</path>
	</g>
	<g featurekey="symbolFeature-0" transform="matrix(0.65, 0, 0, 0.65, 145, 56)" fill="url(#SvgjsLinearGradient1019)">
		<path xmlns="http://www.w3.org/2000/svg" d="M52.094,93.749c0.001,0,11.108-7.41,0.883-22.909c-4.773-7.235-12.596-14.971-11.701-22.63  c-1.015,0.883-1.937,1.859-2.38,2.453c-1.618,2.169-2.791,4.529-3.392,6.964c-0.697,2.823-0.515,5.795,1.102,8.686  c1.501,2.683,4.107,5.103,6.558,7.537c5.868,5.826,11.036,12.018,9.461,18.607C52.541,92.811,52.5,93.478,52.094,93.749z">
		</path>
		<path xmlns="http://www.w3.org/2000/svg" d="M59.398,79.14c0.001-0.001,17.835-11.897,1.417-36.782C53.152,30.742,40.592,18.321,42.03,6.024  c-1.365,1.194-3.11,2.985-3.821,3.939c-2.598,3.482-4.481,7.271-5.446,11.181c-1.12,4.533-0.827,9.304,1.77,13.946  c2.41,4.308,6.594,8.193,10.53,12.102c9.421,9.355,17.719,19.295,15.19,29.876C60.116,77.635,60.049,78.706,59.398,79.14z">
		</path>
	</g>
	<g id="SvgjsG1009" featurekey="sloganFeature-0" transform="matrix(1.1,0,0,1.1,0,170)" fill="#137dc5">
		<ellipse class="cls-2" cx="53" cy="1" rx="45" ry="45"/>
		<text id="bbp" class="cls-3" x="55" y="15"><tspan x="55">bbp</tspan></text>
		<text id="style" class="cls-4" x="150" y="15"><tspan x="150">style</tspan></text>
		<text id="Pack" class="cls-1" x="250" y="15"><tspan x="250">Pack</tspan></text>
		<text class="cls-5" x="100" y="65" >for bbPress</text>
	</g>
</svg>
	<?php
	$logo = ob_get_clean();
	return $logo ;
}