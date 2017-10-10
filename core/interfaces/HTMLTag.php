<?php defined('ABSPATH') or die;

/**
 * @package    pixtypes
 * @category   core
 * @author     Pixel Grade Team
 * @copyright  (c) 2013, Pixel Grade Media
 */
interface PixtypesHTMLTag {

	/**
	 * @param string key
	 * @param mixed default
	 * @return mixed
	 */
	function get($key, $default = null);

	/**
	 * @param string key
	 * @param mixed value
	 * @return static $this
	 */
	function set($key, $value);

	/**
	 * @return string
	 */
	function htmlattributes(array $extra = array());

} # interface
