<?php

/**
 * Plugin Name:       Meta Field Block
 * Plugin URI:        https://metafieldblock.com?utm_source=MFB&utm_campaign=MFB+visit+site&utm_medium=link&utm_content=Plugin+URI
 * Description:       Display a custom field as a block on the front end. It supports custom fields for posts, terms, and users. It supports ACF fields explicitly.
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Version:           1.3.2
 * Author:            Phi Phan
 * Author URI:        https://metafieldblock.com?utm_source=MFB&utm_campaign=MFB+visit+site&utm_medium=link&utm_content=Author+URI
 * License:           GPL-3.0
 *
 * @package   MetaFieldBlock
 * @copyright Copyright(c) 2022, Phi Phan
 *
 */
namespace MetaFieldBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
if ( function_exists( __NAMESPACE__ . '\\mfb_fs' ) ) {
    mfb_fs()->set_basename( false, __FILE__ );
    return;
}
// Include Freemius functions.
require_once dirname( __FILE__ ) . '/freemius.php';
if ( !class_exists( MetaFieldBlock::class ) ) {
    /**
     * The main class
     */
    class MetaFieldBlock {
        /**
         * Plugin version
         *
         * @var String
         */
        protected $version = '1.3.2';

        /**
         * Components
         *
         * @var Array
         */
        protected $components = [];

        /**
         * Plugin instance
         *
         * @var MetaFieldBlock
         */
        private static $instance;

        /**
         * A dummy constructor
         */
        private function __construct() {
        }

        /**
         * Initialize the instance.
         *
         * @return MetaFieldBlock
         */
        public static function get_instance() {
            if ( !isset( self::$instance ) ) {
                self::$instance = new MetaFieldBlock();
                self::$instance->initialize();
            }
            return self::$instance;
        }

        /**
         * Kick start function.
         * Define constants
         * Load dependencies
         * Register components
         * Run the main hooks
         *
         * @return void
         */
        public function initialize() {
            // Setup constants.
            $this->setup_constants();
            // Load dependencies.
            $this->load_dependencies();
            // Register components.
            $this->register_components();
            // Run hooks.
            $this->run();
        }

        /**
         * Setup constants
         *
         * @return void
         */
        public function setup_constants() {
            $this->define_constant( 'MFB', true );
            $this->define_constant( 'MFB_ROOT_FILE', __FILE__ );
            $this->define_constant( 'MFB_VERSION', $this->version );
            $this->define_constant( 'MFB_PATH', trailingslashit( plugin_dir_path( MFB_ROOT_FILE ) ) );
            $this->define_constant( 'MFB_URL', trailingslashit( plugin_dir_url( MFB_ROOT_FILE ) ) );
        }

        /**
         * Load core components
         *
         * @return void
         */
        public function register_components() {
            // Load & register core components.
            $this->include_file( 'includes/rest-fields.php' );
            $this->include_file( 'includes/acf-fields.php' );
            $this->include_file( 'includes/dynamic-field.php' );
            $this->include_file( 'includes/freemius-config.php' );
            $this->include_file( 'includes/settings.php' );
            $core_components = [
                RestFields::class,
                ACFFields::class,
                DynamicField::class,
                Settings::class,
                FreemiusConfig::class
            ];
            $components = apply_filters( 'meta_field_block_get_components', $core_components );
            foreach ( $components as $component ) {
                $this->register_component( $component );
            }
        }

        /**
         * Load dependencies
         *
         * @return void
         */
        public function load_dependencies() {
            // Load core component.
            $this->include_file( 'includes/core-component.php' );
            $this->include_file( 'includes/helper-functions.php' );
        }

        /**
         * Run main hooks
         *
         * @return void
         */
        public function run() {
            // Load translations.
            add_action( 'init', [$this, 'load_textdomain'] );
            // Register the block.
            add_action( 'init', [$this, 'register_block'] );
            // Save version and trigger upgraded hook.
            add_action( 'admin_menu', [$this, 'version_upgrade'], 1 );
            // Flush the server cache.
            add_action(
                'save_post',
                [$this, 'flush_cache'],
                10,
                2
            );
            // Run all components.
            foreach ( $this->components as $component ) {
                $component->run();
            }
        }

        /**
         * Load text domain
         *
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'display-a-meta-field-as-block', false, plugin_basename( realpath( __DIR__ . '/languages' ) ) );
        }

        /**
         * Register the block
         *
         * @return void
         */
        public function register_block() {
            // Register block.
            register_block_type( MFB_PATH . '/build', [
                'render_callback'   => [$this, 'render_block'],
                'skip_inner_blocks' => true,
            ] );
        }

        /**
         * Renders the `mbf/meta-field-block` block on the server.
         *
         * @param  array    $attributes Block attributes.
         * @param  string   $content    Block default content.
         * @param  WP_Block $block      Block instance.
         * @return string   Returns the value for the field.
         */
        public function render_block( $attributes, $content, $block ) {
            $field_name = $attributes['fieldName'] ?? '';
            if ( empty( $field_name ) ) {
                return '';
            }
            // Get object type.
            $object_type = $this->get_object_type( $field_name, $attributes, $block );
            // Get object id.
            $object_id = $this->get_object_id( $object_type, $attributes, $block );
            // Get field type.
            $field_type = $attributes['fieldType'] ?? 'rest_field';
            // Is dynamic block?
            $is_dynamic_block = $this->is_dynamic_block( $attributes );
            if ( $is_dynamic_block ) {
                if ( 'acf' === $field_type ) {
                    if ( function_exists( 'get_field_object' ) ) {
                        $block_value = $this->get_component( ACFFields::class )->get_field_value( $field_name, $object_id, $object_type );
                        $content = $block_value['value'] ?? '';
                        $content = apply_filters(
                            '_meta_field_block_render_dynamic_block',
                            $content,
                            $block_value,
                            $object_id,
                            $object_type,
                            $attributes,
                            $block
                        );
                    } else {
                        $content = '<code><em>' . __( 'This data type requires the ACF plugin installed and activated!', 'display-a-meta-field-as-block' ) . '</em></code>';
                    }
                } else {
                    if ( in_array( $object_type, ['post', 'term', 'user'], true ) ) {
                        $get_meta_callback = "get_{$object_type}_meta";
                        $content = $get_meta_callback( $object_id, $field_name, true );
                    } else {
                        $content = apply_filters(
                            '_meta_field_block_get_field_value_other_type',
                            $content,
                            $field_name,
                            $object_id,
                            $object_type,
                            $attributes,
                            $block
                        );
                    }
                    $content = apply_filters(
                        '_meta_field_block_get_field_value',
                        $content,
                        $field_name,
                        $object_id,
                        $object_type,
                        $attributes,
                        $block
                    );
                }
            } else {
                $content = apply_filters(
                    '_meta_field_block_render_static_block',
                    $content,
                    $field_name,
                    $object_id,
                    $object_type,
                    $attributes,
                    $block
                );
            }
            // Additional block classes.
            $classes = "is-{$field_type}-field";
            if ( $attributes['fieldSettings']['type'] ?? false ) {
                $classes .= " is-{$attributes['fieldSettings']['type']}-field";
            }
            // Get the block markup.
            return meta_field_block_get_block_markup(
                $content,
                $attributes,
                $block,
                $object_id,
                $object_type,
                $classes,
                $is_dynamic_block
            );
        }

        /**
         * Get object type.
         *
         * @param string   $field_name Field name.
         * @param array    $attributes Block attributes.
         * @param WP_Block $block      The block instance.
         * @return string
         */
        public function get_object_type( $field_name, $attributes, $block ) {
            // Get object type from meta type.
            $object_type = $attributes['metaType'] ?? '';
            if ( !$object_type ) {
                // Cache key.
                $cache_key = 'object_type';
                // Get from the cache.
                $cache_data = wp_cache_get( $cache_key, 'mfb' );
                if ( false === $cache_data ) {
                    $cache_data = [];
                }
                if ( isset( $cache_data[$field_name] ) ) {
                    $object_type = $cache_data[$field_name];
                } else {
                    $object_type = 'post';
                    if ( $this->is_custom_context( $field_name, $block, is_category() || is_tag() || is_tax() ) ) {
                        $object_type = 'term';
                    } elseif ( $this->is_custom_context( $field_name, $block, is_author() ) ) {
                        $object_type = 'user';
                    }
                    // Update cache.
                    $cache_data[$field_name] = $object_type;
                    wp_cache_set( $cache_key, $cache_data, 'mfb' );
                }
            }
            return apply_filters(
                'meta_field_block_get_object_type',
                $object_type,
                $attributes,
                $block
            );
        }

        /**
         * Get object id by object type.
         *
         * @param string   $object_type Object type.
         * @param array    $attributes  Block attributes.
         * @param WP_Block $block       Block instance.
         *
         * @return string
         */
        public function get_object_id( $object_type, $attributes, $block ) {
            if ( in_array( $object_type, ['post', 'term', 'user'], true ) && ($attributes['isCustomSource'] ?? false) && ($attributes['objectId'] ?? false) ) {
                return $attributes['objectId'];
            }
            if ( in_array( $object_type, ['term', 'user'], true ) ) {
                // Get queried object id.
                $object_id = get_queried_object_id();
            } else {
                if ( isset( $block->context['postId'] ) ) {
                    // Get value from the context.
                    $object_id = $block->context['postId'];
                } else {
                    // Fallback to the current queried object id.
                    $object_id = get_queried_object_id();
                }
            }
            return $object_id;
        }

        /**
         * Is the field is in a custom context?
         *
         * @param string   $field_name
         * @param WP_Block $block
         * @param boolean  $condition
         * @return boolean
         */
        private function is_custom_context( $field_name, $block, $condition ) {
            if ( $condition ) {
                if ( !isset( $block->context['postId'] ) ) {
                    return true;
                } else {
                    global $_wp_current_template_id, $_wp_current_template_content;
                    if ( !$_wp_current_template_id || !$_wp_current_template_content ) {
                        return false;
                    } else {
                        // Cache key for the blocks of template.
                        $cache_key = 'blocks_by_template';
                        // Build template key.
                        $template_key = str_replace( '//', '__', $_wp_current_template_id );
                        // Get from the cache.
                        $cache_data = wp_cache_get( $cache_key, 'mfb' );
                        if ( false === $cache_data ) {
                            $cache_data = [];
                        }
                        if ( isset( $cache_data[$template_key] ) ) {
                            $blocks = $cache_data[$template_key];
                        } else {
                            $blocks = \parse_blocks( $_wp_current_template_content );
                            // Update cache.
                            if ( !empty( $blocks ) ) {
                                $cache_data[$template_key] = $blocks;
                                wp_cache_set( $cache_key, $cache_data, 'mfb' );
                            }
                        }
                        return $this->find_mfb( $field_name, $blocks );
                    }
                }
            }
            return false;
        }

        /**
         * Find MFB not within a core query from an array of blocks
         *
         * @param string $field_name
         * @param array  $blocks
         * @return boolean
         */
        private function find_mfb( $field_name, $blocks ) {
            $found = false;
            foreach ( $blocks as $block ) {
                $block_name = $block['blockName'] ?? '';
                $query_blocks = apply_filters( 'meta_field_block_get_query_blocks', ['core/query', 'woocommerce/product-collection'] );
                if ( in_array( $block_name, $query_blocks, true ) ) {
                    continue;
                }
                if ( 'mfb/meta-field-block' === $block_name ) {
                    if ( $field_name === ($block['attrs']['fieldName'] ?? '') ) {
                        $found = true;
                        break;
                    }
                } elseif ( !empty( $block['innerBlocks'] ) ) {
                    $found = $this->find_mfb( $field_name, $block['innerBlocks'] );
                    if ( $found ) {
                        break;
                    }
                }
            }
            return $found;
        }

        /**
         * Check whether if the block is dynamic of static
         *
         * @param array    $attributes
         * @param mixed    $content
         * @param WP_Block $block
         * @return boolean
         */
        private function is_dynamic_block( $attributes ) {
            $field_type = $attributes['fieldType'] ?? '';
            if ( 'acf' === $field_type ) {
                if ( $attributes['fieldSettings']['isStatic'] ?? false ) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Save version and trigger an upgrade hook
         *
         * @return void
         */
        public function version_upgrade() {
            if ( get_option( 'mfb_current_version' ) !== $this->version ) {
                do_action( 'mfb_version_upgraded', get_option( 'mfb_current_version' ), $this->version );
                update_option( 'mfb_current_version', $this->version );
            }
        }

        /**
         * Invalidate the server cache
         *
         * @param int     $post_id
         * @param WP_Post $post
         * @return void
         */
        public function flush_cache( $post_id, $post ) {
            if ( in_array( $post->post_type, ['wp_template', 'wp_template_part'] ) ) {
                wp_cache_delete( 'object_type', 'mfb' );
                wp_cache_delete( 'blocks_by_template', 'mfb' );
            }
        }

        /**
         * Register component
         *
         * @param string $classname The class name of the component.
         * @return void
         */
        public function register_component( $classname ) {
            $this->components[$classname] = new $classname($this);
        }

        /**
         * Get a component by class name
         *
         * @param string $classname The class name of the component.
         * @return mixed
         */
        public function get_component( $classname ) {
            return $this->components[$classname] ?? false;
        }

        /**
         * Define constant
         *
         * @param string $name The name of the constant.
         * @param mixed  $value The value of the constant.
         * @return void
         */
        public function define_constant( $name, $value ) {
            if ( !defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * Retrn file path for file or folder.
         *
         * @param string $path file path.
         * @return string
         */
        public function get_file_path( $path ) {
            return MFB_PATH . $path;
        }

        /**
         * Include file path.
         *
         * @param string $path file path.
         * @return mixed
         */
        public function include_file( $path ) {
            return include_once $this->get_file_path( $path );
        }

        /**
         * Get file uri by file path.
         *
         * @param string $path file path.
         * @return string
         */
        public function get_file_uri( $path ) {
            return MFB_URL . $path;
        }

        /**
         * Create version for scripts/styles
         *
         * @param array $asset_file
         * @return string
         */
        public function get_script_version( $asset_file ) {
            return ( wp_get_environment_type() !== 'production' ? $asset_file['version'] ?? MFB_VERSION : MFB_VERSION );
        }

        /**
         * Get the plugin version
         *
         * @return string
         */
        public function get_plugin_version() {
            return $this->version;
        }

        /**
         * Is Debugging
         *
         * @return boolean
         */
        public function is_debug_mode() {
            return defined( 'MFB_DEBUG' ) && MFB_DEBUG || 'development' === wp_get_environment_type();
        }

        /**
         * Enqueue debug log information
         *
         * @param string $handle
         * @return void
         */
        public function enqueue_debug_information( $handle ) {
            wp_add_inline_script( $handle, 'var MFBLOG=' . wp_json_encode( [
                'environmentType' => ( $this->is_debug_mode() ? 'development' : wp_get_environment_type() ),
            ] ), 'before' );
        }

    }

    /**
     * Kick start
     *
     * @return MetaFieldBlock instance
     */
    function mfb_get_instance() {
        return MetaFieldBlock::get_instance();
    }

    // Instantiate.
    mfb_get_instance();
}
if ( !function_exists( __NAMESPACE__ . '\\meta_field_block_activate' ) ) {
    /**
     * Trigger an action when the plugin is activated.
     *
     * @return void
     */
    function meta_field_block_activate() {
        do_action( 'meta_field_block_activate' );
    }

    register_activation_hook( __FILE__, __NAMESPACE__ . '\\meta_field_block_activate' );
}
