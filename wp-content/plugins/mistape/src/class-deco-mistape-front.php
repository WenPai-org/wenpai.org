<?php

class Deco_Mistape extends Deco_Mistape_Abstract {

	private static $instance;
	private $is_appropriate_post;

	function __construct() {

		parent::__construct();

		// shortcode
		if ( $this->options['register_shortcode'] === 'yes' ) {
			add_shortcode( 'mistape', array( $this, 'render_shortcode' ) );
		}

//		if ( ! static::is_appropriate_useragent() ) {
//			return;
//		}

		if ( $this->options['first_run'] === 'yes' ) {
			return;
		}

		// Load textdomain
		$this->load_textdomain();

		// actions
		add_action( 'wp_footer', array( $this, 'insert_dialog' ), 1000 );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_load_scripts_styles' ) );

		// filters
		add_filter( 'the_content', array( $this, 'append_caption_to_content' ), 1 );


		add_action( 'wp_print_styles', array( $this, 'custom_styles' ), 10 );
	}

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Handle shortcode
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function render_shortcode( $atts ) {

//		if ( ! static::is_appropriate_useragent() ) {
//			return;
//		}

		$atts = shortcode_atts(
			array(
				'format' => $this->options['caption_format'],
				'class'  => 'mistape_caption',
				'image'  => '',
				'text'   => $this->get_caption_text(),
			),
			$atts,
			'mistape'
		);

		if ( ( $atts['format'] === 'image' && ! empty( $this->options['caption_image_url'] ) ) || ( $atts['format'] !== 'text' && ! empty( $atts['image'] ) ) ) {
			$imagesrc = $atts['image'] ? $atts['image'] : $this->options['caption_image_url'];
			$output   = '<div class="' . $atts['class'] . '"><img src="' . $imagesrc . '" alt="' . $atts['text'] . '"></div>';
		} else {
			$icon_id      = (int) $this->options['show_logo_in_caption'];
			$icon_svg     = apply_filters( 'mistape_get_icon', array( 'icon_id' => $icon_id ) );
			$icon_svg_str = '';

			if ( $this->options['enable_powered_by'] === 'yes' ) {
				$icon_svg_str = '<span class="mistape-link-wrap"><a href="' . $this->plugin_url . '" target="_blank" rel="nofollow" class="mistape-link mistape-logo">' . $icon_svg['icon'] . '</a></span>';
			} else if ( ! empty( $icon_svg['icon'] ) ) {
				$icon_svg_str = '<span class="mistape-link-wrap"><span class="mistape-link mistape-logo">' . $icon_svg['icon'] . '</span></span>';
			}

			$output = '<div class="' . $atts['class'] . '">' . $icon_svg_str . '<p>' . $atts['text'] . '</p></div>';
		}

		return $output;
	}

	/**
	 * Load scripts and styles - frontend
	 */
	public function front_load_scripts_styles() {

		if ( ! $this->is_appropriate_post() && $this->options['register_shortcode'] !== 'yes' ) {
			return;
		}

		$this->enqueue_dialog_assets();
	}

	/**
	 * Add Mistape caption to post content
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function append_caption_to_content( $content ) {

		static $is_already_displayed_after_the_content = false;

		if ( $is_already_displayed_after_the_content === true ) {
			return $content;
		}

		if ( ( $format = $this->options['caption_format'] ) === 'disabled' ) {
			return $content;
		}


		if ( ! $this->is_appropriate_post() ) {
			return $content;
		}

		$output = '';

		$raw_post_content = get_the_content();

		// check if we really deal with post content
		if ( $content !== $raw_post_content ) {
			return $content;
		}

		if ( $format === 'text' ) {
			$icon_id      = (int) $this->options['show_logo_in_caption'];
			$icon_svg     = apply_filters( 'mistape_get_icon', array( 'icon_id' => $icon_id ) );
			$icon_svg_str = '';
			if ( $this->options['enable_powered_by'] = 'yes' ) {
				$icon_svg_str = '<span class="mistape-link-wrap"><a href="' . $this->plugin_url . '" target="_blank" rel="nofollow" class="mistape-link mistape-logo">' . $icon_svg['icon'] . '</a></span>';
			} else if ( ! empty( $icon_svg['icon'] ) ) {
				$icon_svg_str = '<span class="mistape-link-wrap"><span class="mistape-link mistape-logo">' . $icon_svg['icon'] . '</span></span>';
			}
			// Only text without link plugin site!!
			$caption_text = '';
			if ( self::wp_is_mobile() ) {
				$caption_text = $this->get_caption_text_for_mobile();
			} else {
				$caption_text = $this->get_caption_text();
			}
			$output = "\n" . '<div class="mistape_caption">' . $icon_svg_str . '<p>' . $caption_text . '</p></div>';
		} elseif ( $format === 'image' ) {
			if ( self::wp_is_mobile() ) {
				$img_alt = strip_tags( $this->get_caption_text_for_mobile() );
			} else {
				$img_alt = strip_tags( $this->get_caption_text() );
			}

			$img_alt = str_replace( array( "\r", "\n" ), '', $img_alt );
			$output  = "\n" . '<div class="mistape_caption"><img src="' . $this->options['caption_image_url'] . '" alt="' . esc_attr( $img_alt ) . '"/></div>';
		}

//		$output = apply_filters( 'mistape_caption_output', $output, $this->options );

		$is_already_displayed_after_the_content = true;


		return $content . $output;
	}

	/**
	 * Mistape custom styles
	 */
	public function custom_styles() {
		echo '
		<style type="text/css">
			.mistape-test, .mistape_mistake_inner {color: ' . $this->options['color_scheme'] . ' !important;}
			#mistape_dialog h2::before, #mistape_dialog .mistape_action, .mistape-letter-back {background-color: ' . $this->options['color_scheme'] . ' !important; }
			#mistape_reported_text:before, #mistape_reported_text:after {border-color: ' . $this->options['color_scheme'] . ' !important;}
            .mistape-letter-front .front-left {border-left-color: ' . $this->options['color_scheme'] . ' !important;}
            .mistape-letter-front .front-right {border-right-color: ' . $this->options['color_scheme'] . ' !important;}
            .mistape-letter-front .front-bottom, .mistape-letter-back > .mistape-letter-back-top, .mistape-letter-top {border-bottom-color: ' . $this->options['color_scheme'] . ' !important;}
            .mistape-logo svg {fill: ' . $this->options['color_scheme'] . ' !important;}
		</style>
		';
	}


	/**
	 * Mistape dialog output
	 */
	public function insert_dialog() {

		if ( ! $this->is_appropriate_post() && $this->options['register_shortcode'] !== 'yes' ) {
			return;
		}

		// dialog output
		$output = $this->get_dialog_html();

		echo apply_filters( 'mistape_dialog_output', $output, $this->options );
	}

	/**
	 * exit early if user agent is unlikely to behave reasonable
	 *
	 * @return bool
	 */
	public static function is_appropriate_useragent() {
		if ( self::wp_is_mobile() ) {
			return false;
		}

		// check for IE, save some resources avoiding regex
//		if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE ' )
//		     || false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/' )
//		     || false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Edge/' )
//		) {
//			return false;
//		}

		return true;
	}

	public function is_appropriate_post() {

		if ( $this->is_appropriate_post === null ) {
			$result = false;

			// a bit inefficient logic is necessary for some illogical themes and plugins
			if ( ( ( is_single() && in_array( get_post_type(), $this->options['post_types'] ) )
			       || ( is_page() && in_array( 'page', $this->options['post_types'] ) ) ) && ! post_password_required()
			) {
				$result = true;
			}

			$this->is_appropriate_post = apply_filters( 'mistape_is_appropriate_post', $result );
		}

		return $this->is_appropriate_post;
	}


	/**
	 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
	 *
	 * @staticvar bool $is_mobile
	 *
	 * @return bool
	 */
	public static function wp_is_mobile() {
		static $is_mobile = null;

		if ( \function_exists( 'wp_is_mobile' ) ) {
			$is_mobile = wp_is_mobile();
		}


		if ( $is_mobile ) {
			return $is_mobile;
		}

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices (all iPhone, iPad, etc.)
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false
		) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}
}