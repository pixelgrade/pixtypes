<?php return array
	(
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
				'is_numeric' => __('Numberic value required.', pixtypes::textdomain()),
				'not_empty' => __('Field is required.', pixtypes::textdomain()),
			),

		'callbacks' => array
			(
			// cleanup callbacks
				'switch_not_available' => 'pixtypes_cleanup_switch_not_available',

			// validation callbacks
				'is_numeric' => 'pixtypes_validate_is_numeric',
				'not_empty' => 'pixtypes_validate_not_empty'
			)

	); # config
