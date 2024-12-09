<?php

if ( ! function_exists( 'ub_set_ub_version' ) ) {
	function ub_set_ub_version() {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_set_ub_version();
	}
}

if ( ! function_exists( 'ub_get_url_valid_shema' ) ) {
	function ub_get_url_valid_shema( $url ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_url_valid_schema( $url );
	}
}

if ( ! function_exists( 'ub_url' ) ) {
	function ub_url( $extended ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_url( $extended );
	}
}

if ( ! function_exists( 'ub_dir' ) ) {
	function ub_dir( $extended ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_dir( $extended );
	}
}

if ( ! function_exists( 'ub_files_url' ) ) {
	function ub_files_url( $extended ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_files_url( $extended );
	}
}

if ( ! function_exists( 'ub_files_dir' ) ) {
	function ub_files_dir( $extended ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_files_dir( $extended );
	}
}

if ( ! function_exists( 'ub_is_active_module' ) ) {
	function ub_is_active_module( $module ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_is_active_module( $module );
	}
}

if ( ! function_exists( 'ub_get_option' ) ) {
	function ub_get_option( $option, $default = false, $mode = 'normal', $force_network = false ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_option( $option, $default, $mode, $force_network );
	}
}

if ( ! function_exists( 'ub_get_option_filtered' ) ) {
	function ub_get_option_filtered( $option ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_option_filtered( $option );
	}
}

if ( ! function_exists( 'ub_update_option' ) ) {
	function ub_update_option( $option, $value = null ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_update_option( $option, $value );
	}
}

if ( ! function_exists( 'ub_add_option' ) ) {
	function ub_add_option( $option, $value = null, $autoload = 'yes' ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_add_option( $option, $value, $autoload );
	}
}

if ( ! function_exists( 'get_ub_activated_modules' ) ) {
	function get_ub_activated_modules( $mode = 'normal' ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return get_branda_activated_modules( $mode );
	}
}

if ( ! function_exists( 'update_ub_activated_modules' ) ) {
	function update_ub_activated_modules( $data ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		update_branda_activated_modules( $data );
	}
}

if ( ! function_exists( 'ub_delete_option' ) ) {
	function ub_delete_option( $option ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_delete_option( $option );
	}
}

if ( ! function_exists( 'ub_load_single_module' ) ) {
	function ub_load_single_module( $module ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_load_single_module( $module );
	}
}

if ( ! function_exists( 'ub_has_menu' ) ) {
	function ub_has_menu( $menuhook ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_has_menu( $menuhook );
	}
}

if ( ! function_exists( 'ub_wp_upload_url' ) ) {
	function ub_wp_upload_url() {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_wp_upload_url();
	}
}

if ( ! function_exists( 'ub_wp_upload_dir' ) ) {
	function ub_wp_upload_dir() {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_wp_upload_dir();
	}
}

if ( ! function_exists( 'ub_get_option_name_by_module' ) ) {
	function ub_get_option_name_by_module( $module ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_option_name_by_module( $module );
	}
}

if ( ! function_exists( 'ub_deprecated_module' ) ) {
	function ub_deprecated_module( $deprecated, $substitution, $tab, $removed_in = 0 ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_deprecated_module( $deprecated, $substitution, $tab, $removed_in );
	}
}

if ( ! function_exists( 'ub_register_activation_hook' ) ) {
	function ub_register_activation_hook() {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_register_activation_hook();
	}
}

if ( ! function_exists( 'ub_get_main_site_id' ) ) {
	function ub_get_main_site_id() {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_main_site_id();
	}
}

if ( ! function_exists( 'ub_register_deactivation_hook' ) ) {
	function ub_register_deactivation_hook() {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_register_deactivation_hook();
	}
}

if ( ! function_exists( 'ub_register_uninstall_hook' ) ) {
	function ub_register_uninstall_hook() {
		_deprecated_function( __METHOD__, '3.3.1' );
		branda_register_uninstall_hook();
	}
}

if ( ! function_exists( 'ub_get_uba_object' ) ) {
	function ub_get_uba_object() {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_uba_object();
	}
}

if ( ! function_exists( 'ub_get_array_value' ) ) {
	function ub_get_array_value( $array, $key ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_array_value( $array, $key );
	}
}

if ( ! function_exists( 'ub_get_modules_list' ) ) {
	function ub_get_modules_list( $mode = 'full' ) {
		_deprecated_function( __METHOD__, '3.3.1' );
		return branda_get_modules_list( $mode );
	}
}
