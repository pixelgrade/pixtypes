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
				'hiddens'
					=> include 'settings/hiddens'.EXT,
				'post_types'
					=> include 'settings/post_types'.EXT,
				'taxonomies'
					=> include 'settings/taxonomies'.EXT,
			),

		'processor' => array
			(
				// callback signature: (array $input, PixtypesProcessor $processor)

				'preupdate' => array
				(
					// callbacks to run before update process
					// cleanup and validation has been performed on data
				),
				'postupdate' => array
				(
					'save_settings'
				),
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
				'save_settings' => 'save_pixtypes_settings'
			),

		// shows exception traces on error
		'debug' => true,

	); # config
