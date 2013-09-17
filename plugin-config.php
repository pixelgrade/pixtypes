<?php defined('ABSPATH') or die;

	$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

return array
	(
		'plugin-name' => 'pixtypes',

		'settings-key' => 'pixtypes_settings',

		'textdomain' => 'pixtypes_txtd',

		'template-paths' => array
			(
				$basepath.'core/views/form-partials/',
				$basepath.'views/form-partials/',
			),

		'fields' => array
			(
				'post_types'
					=> include 'settings/post_types'.EXT,
				'taxonomies'
					=> include 'settings/taxonomies'.EXT,
			),

		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
			),

		'errors' => array
			(
				'not_empty' => __('Invalid Value.', pixtypes::textdomain()),
			),

		'callbacks' => array
			(
				// empty
			),

		// shows exception traces on error
		'debug' => false,

	); # config
