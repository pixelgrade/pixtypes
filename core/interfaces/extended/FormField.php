<?php defined('ABSPATH') or die;

/**
 * @package    pixtypes
 * @category   core
 * @author     Pixel Grade Team
 * @copyright  (c) 2013, Pixel Grade Media
 */
interface PixtypesFormField extends PixtypesHTMLElement {

	/**
	 * @return boolean true if field has errors
	 */
	function has_errors();

	/**
	 * @return string first error message
	 */
	function one_error();

	/**
	 * Render field emulates wordpress template behaviour. First searches for
	 * name, then searches field type and so on.
	 *
	 * @return string
	 */
	function render();

} # interface
