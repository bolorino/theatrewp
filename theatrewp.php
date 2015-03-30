<?php
/**
* The main plugin file.
*
* This file loads the plugin after checking
* PHP, WordPress® and other compatibility requirements.
*
* Copyright: © 2013-2015
* @author Jose Bolorino
* @version: 0.55
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

/**
* Plugin Name: TheatreWP
* Plugin URI: http://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/
* Description: CMS for Theatre and Performing Arts Companies. Managing Shows and Performances made easy.
* Tags: theatre, troupe, actors, shows, performing arts
* Version: 0.55
* License: GPLv2
* Author: Jose Bolorino <jose.bolorino@gmail.com>
* Author URI: http://www.bolorino.net/
* Text Domain: theatrewp
*
* Copyright 2013-2015 Jose Bolorino
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

/* After v 0.46 a DB update is needed to change performances metadata.
 * $performance_custom['performance'] contained the Production slug (Ouch!)
 * Now it should be $performance_custom['spectacle_id']
 * The twp_version option is saved for the first time after v 0.46
 * so, if it doesn't exist we call a method to fix the mess.
 */

$current_version = get_option( 'twp_version' );

if ( ! $current_version OR $current_version < '0.49' ) {
    _upgrade_performances_meta();
    // Temporary ugly fix
    update_option( 'twp_version', '0.55' );
}

function _upgrade_performances_meta() {
    global $wpdb;

    $shows =  get_posts( 'post_type=spectacle&orderby=title&order=ASC&numberposts=-1' );

    if ( ! $shows ) {
        return false;
    }

    foreach ( $shows as $show ) {
        $update_meta_query = "UPDATE $wpdb->postmeta
            SET meta_value = '$show->ID'
            WHERE meta_key = 'twp_performance'
            AND meta_value = '$show->post_title'";
        $wpdb->query( $update_meta_query );
    }

    $update_meta_key = "UPDATE $wpdb->postmeta
        SET meta_key = 'twp_spectacle_id'
        WHERE meta_key = 'twp_performance' ";

    $wpdb->query( $update_meta_key );
}

$theatre_wp = new Theatre_WP( TWP_DIR );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TWP_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TWP_Setup', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'TWP_Setup', 'uninstall' ) );
