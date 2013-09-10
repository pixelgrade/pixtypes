<?php
/**
 *
 * @package   PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade Media
 *
 * @wordpress-plugin
 * Plugin Name: PixTypes
 * Plugin URI:  http://pixelgrade.com
 * Description: Custom entities needed by your theme
 * Version:     1.0.0
 * Author:      pixelgrade
 * Author URI:  http://pixelgrade.com
 * Text Domain: pixtypes
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-pixtypes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'PixTypes', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'PixTypes', 'deactivate' ) );

global $pixtypes;
$pixtypes = PixTypes::get_instance();