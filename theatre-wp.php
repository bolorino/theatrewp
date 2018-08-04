<?php
/**
* Plugin Name: TheatreWP
* Plugin URI: https://www.bolorino.net/pages/theatre-wp-wordpress-plugin-performing-arts.html
* Description: CMS for Theatre and Performing Arts Companies. Managing Shows and Performances made easy.
* Tags: theatre, troupe, actors, shows, performing arts
* Version: 0.67
* Author: Jose Bolorino <jose.bolorino@gmail.com>
* Author URI: https://www.bolorino.net/
* License: GPLv3
* Text Domain: theatre-wp
* Domain Path: /languages
* Copyright 2013-2017 Jose Bolorino
*/

if ( realpath(__FILE__) === realpath( $_SERVER['SCRIPT_FILENAME'] ) )
	exit('Do not access this file directly.');

if ( ! defined( 'WPINC' ) )
	die();

define( 'TWP_VERSION', '0.68' );
define( 'TWP_META_BOX_URL', apply_filters( 'twp_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) ) ) ) );

define( 'TWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'TWP_DIR', dirname( TWP_PLUGIN_BASENAME ) );
define( 'TWP_BASE_PATH', plugin_dir_path( __FILE__ ) );

require_once( TWP_BASE_PATH . 'includes/classes/class-theatre-wp.php' );

$current_version = get_option( 'twp_version' );

if ( $current_version != TWP_VERSION ) {
    update_option( 'twp_version', TWP_VERSION );
}

$theatre_wp = new Theatre_WP( TWP_DIR );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TWP_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TWP_Setup', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'TWP_Setup', 'uninstall' ) );
