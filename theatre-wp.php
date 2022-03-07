<?php
/**
* Plugin Name: TheatreWP
* Plugin URI: https://www.bolorino.net/pages/theatre-wp-wordpress-plugin-performing-arts.html
* Description: CMS for Theatre and Performing Arts Companies. Managing Shows and Performances made easy.
* Tags: theatre, troupe, actors, shows, performing arts
* Version: 1.0
* Author: Jose Bolorino <jose.bolorino@gmail.com>
* Author URI: https://www.bolorino.net/
* License: GPLv3
* Text Domain: theatre-wp
* Domain Path: /languages
* Copyright 2013-2022 Jose Bolorino
*/

if ( realpath(__FILE__) === realpath( $_SERVER['SCRIPT_FILENAME'] ) )
	exit('Do not access this file directly.');

if ( ! defined( 'WPINC' ) )
	die();

const TWP_VERSION = '1.0';

define( 'TWP_META_BOX_URL', plugin_dir_url( __FILE__ ) );
define( 'TWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'TWP_DIR', dirname( TWP_PLUGIN_BASENAME ) );
define( 'TWP_BASE_PATH', plugin_dir_path( __FILE__ ) );

// Autoload TheatreWP classes
spl_autoload_register( function($classname) {

	// Regular
	$class      = str_replace( '\\', DIRECTORY_SEPARATOR, strtolower($classname) );
	$classpath  = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $class . '.php';

	// WordPress
	$parts      = explode('\\', $classname);
	$class      = 'class-' . strtolower( array_pop($parts) );
	$folders    = strtolower( implode(DIRECTORY_SEPARATOR, $parts) );
	$wppath     = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $folders . DIRECTORY_SEPARATOR . $class . '.php';

	/*
	if ( substr($folders, 0, 9) !== 'theatrewp' ) {
		return false;
	}
	*/

	if ( file_exists( $classpath ) ) {
		include_once $classpath;
	} elseif(  file_exists( $wppath ) ) {
		include_once $wppath;
	}

} );

/**
 * Log messages in development
 */
if ( ! function_exists('log_it') ) {
	function log_it( $message ) {
		if ( WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}

$current_version = get_option( 'twp_version' );

if ( $current_version != TWP_VERSION ) {
	update_option( 'twp_version', TWP_VERSION );
}

$twp = new TheatreWP\Setup();

// Facade. The main object to use through templates
$theatre_wp = new \TheatreWP\TheatreWP;

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TheatreWP\Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TheatreWP\Setup', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'TheatreWP\Setup', 'uninstall' ) );