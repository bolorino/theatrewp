<?php
/**
* Plugin Name: TheatreWP
* Plugin URI: http://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/
* Description: CMS for Theatre and Performing Arts Companies. Managing Shows and Performances made easy.
* Tags: theatre, troupe, actors, shows, performing arts
* Version: 0.66
* Author: Jose Bolorino <jose.bolorino@gmail.com>
* Author URI: https://www.bolorino.net/
* License: GPLv3
* Text Domain: theatrewp
* Domain Path: /languages
* Copyright 2013-2017 Jose Bolorino
*/

if ( realpath(__FILE__) === realpath( $_SERVER['SCRIPT_FILENAME'] ) )
	exit('Do not access this file directly.');

if ( ! defined( 'WPINC' ) )
	die();

define( 'TWP_VERSION', '0.66' );
define( 'TWP_META_BOX_URL', apply_filters( 'twp_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) ) ) ) );

define( 'TWP_DIR', dirname( plugin_basename( __FILE__ ) ) );

define( 'TWP_BASE_PATH', plugin_dir_path( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-theatre-wp.php' );

$current_version = get_option( 'twp_version' );

if ( $current_version != TWP_VERSION ) {
    update_option( 'twp_version', TWP_VERSION );
}

$theatre_wp = new Theatre_WP( TWP_DIR );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TWP_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TWP_Setup', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'TWP_Setup', 'uninstall' ) );
