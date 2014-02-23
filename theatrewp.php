<?php
/**
* The main plugin file.
*
* This file loads the plugin after checking
* PHP, WordPress® and other compatibility requirements.
*
* Copyright: © 2013
* @author Jose Bolorino
* @version: 0.39
* {@link http://www.bolorino.net/ Jose Bolorino.}
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package TheatreWP
* @since 0.1
*/

/*
Plugin Name: TheatreWP
Plugin URI: http://www.bolorino.net/theatre-wp/
Description: CMS for Theatre and Performing Arts Companies. Managing Shows and Performances made easy.
Tags: theatre, troupe, actors, shows, performing arts
Version: 0.39
License: GPLv2
Author: Jose Bolorino <jose.bolorino@gmail.com>
Author URI: http://www.bolorino.net/

Copyright 2013 Jose Bolorino
*/

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

if ( ! defined( 'WPINC' ) ) {
	die();
}

define( 'TWP_META_BOX_URL', apply_filters( 'twp_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) ) ) ) );

define( 'TWP_DIR', dirname( plugin_basename( __FILE__ ) ) );

define( 'TWP_BASE_PATH', plugin_dir_path( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-theatre-wp.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TWP_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TWP_Setup', 'deactivate' ) );

// Localization
// @TODO Got out of setup class due to early initialization of meta boxes before the localization.
// It works but needs @FIX
$locale = apply_filters( 'plugin_locale', get_locale(), 'theatrewp' );
load_plugin_textdomain('theatrewp', false, '/' . TWP_DIR . '/languages/' );

$theatre_wp = new Theatre_WP(TWP_DIR);
