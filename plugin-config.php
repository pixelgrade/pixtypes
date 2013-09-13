<?php defined('ABSPATH') or die;

	$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

return array
	(
		'plugin-name' => 'pixtypes',

		'textdomain' => 'pixtypes_txtd',

		'template-paths' => array
			(
				$basepath.'core/views/form-partials/',
				$basepath.'views/form-partials/',
			),

		'fields' => array
			(
				'article_settings_sample' => array
					(
						'type' => 'tabular-group',
						'label' => 'Portfolio',

						// Custom field settings
						// ---------------------

						'options' => array
							(
								'enable_portfolio' => array
									(
										'label' => 'Enable Portfolio',
										'default' => true,
										'type' => 'switch',
									),
								'portfolio_single_item_label' => array
									(
										'label' => 'Single Item Label',
										'description' => '',
										'default' => true,
										'type' => 'text',
									),
								'portfolio_multiple_items_label' => array
									(
										'label' => 'Multiple Items Label (plural)',
										'default' => true,
										'type' => 'text',
									),
							)
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
				// empty
			),

		// shows exception traces on error
		'debug' => false,

	); # config
