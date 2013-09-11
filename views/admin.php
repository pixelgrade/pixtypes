<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade Media
 */

// include custom post types

$theme_types = get_option('pixtypes_theme_settings');
$theme_name = WPGRADE_SHORTNAME;
$to_check =  $theme_name . '_pixtypes_theme';
$kkkt = array_key_exists($to_check, $theme_types);
