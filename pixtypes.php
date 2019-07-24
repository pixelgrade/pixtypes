<?php
/**
 * Plugin Name: PixTypes
 * Plugin URI: https://wordpress.org/plugins/pixtypes/
 * Description: Custom post types and meta-boxes needed by your themes.
 * Version: 1.4.13
 * Author: Pixelgrade
 * Author URI: https://pixelgrade.com
 * Author Email: contact@pixelgrade.com
 * Requires at least: 4.9.9
 * Tested up to: 5.2.2
 * Text Domain: pixtypes
 * License:     GPL-2.0 or later.
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// ensure EXT is defined
if ( ! defined( 'EXT' ) ) {
	define( 'EXT', '.php' );
}

require 'core/bootstrap' . EXT;

$config = include 'plugin-config' . EXT;
// set textdomain
pixtypes::settextdomain( $config['textdomain'] );

// Ensure Test Data
// ----------------

$defaults = include 'plugin-defaults' . EXT;

$current_data = get_option( $config['settings-key'] );

if ( $current_data === false ) {
	add_option( $config['settings-key'], $defaults );
} else if ( count( array_diff_key( $defaults, $current_data ) ) != 0 ) {
	$plugindata = array_merge( $defaults, $current_data );
	update_option( $config['settings-key'], $plugindata );
}
# else: data is available; do nothing

// Load Callbacks
// --------------

$basepath     = trailingslashit( dirname( __FILE__ ) );
$callbackpath = trailingslashit( $basepath . 'callbacks' );
pixtypes::require_all( $callbackpath );

require_once( plugin_dir_path( __FILE__ ) . 'class-pixtypes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'PixTypesPlugin', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'PixTypesPlugin', 'deactivate' ) );

global $pixtypes_plugin;
$pixtypes_plugin = PixTypesPlugin::get_instance( '1.4.13' );
