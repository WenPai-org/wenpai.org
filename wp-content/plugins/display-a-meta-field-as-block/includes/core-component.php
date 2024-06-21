<?php
/**
 * Core component
 *
 * @package    MetaFieldBlock
 * @author     Phi Phan <mrphipv@gmail.com>
 * @copyright  Copyright (c) 2023, Phi Phan
 */

namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( CoreComponent::class ) ) :
	/**
	 * Create/edit custom content blocks.
	 */
	abstract class CoreComponent {
		/**
		 * The plugin instance
		 *
		 * @var MetaFieldBlock
		 */
		protected $the_plugin_instance;

		/**
		 * A constructor
		 */
		public function __construct( $the_plugin_instance ) {
			$this->the_plugin_instance = $the_plugin_instance;
		}

		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		abstract public function run();
	}
endif;
