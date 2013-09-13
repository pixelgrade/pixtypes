<?php defined('ABSPATH') or die;

	function pixtypes_validate_not_empty($fieldvalue, $processor) {
		return ! empty($fieldvalue);
	}
