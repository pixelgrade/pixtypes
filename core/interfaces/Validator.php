<?php defined('ABSPATH') or die;

/**
 * @package    pixtypes
 * @category   core
 * @author     Pixel Grade Team
 * @copyright  (c) 2013, Pixel Grade Media
 */
interface PixtypesValidator {

	/**
	 * @return array errors
	 */
	function validate($input);

	/**
	 * @param string rule
	 * @return string error message
	 */
	function error_message($rule);

} # interface
