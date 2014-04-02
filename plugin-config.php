<?php defined('ABSPATH') or die;

$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

$debug = false;
if ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) {
	$debug = true;
}

$options = get_option('pixtypes_settings');

$display_settings = false;

if ( isset( $options['display_settings'] ) ){
	$display_settings = $options['display_settings'];
}

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

		'display_settings' => $display_settings,

		'github_updater' => array(
			'slug' => basename(dirname(__FILE__)).'/pixtypes.php',
			'api_url' => 'https://api.github.com/repos/pixelgrade/pixtypes',
			'raw_url' => 'https://raw.github.com/pixelgrade/pixtypes/update',
			'github_url' => 'https://github.com/pixelgrade/pixtypes/tree/update',
			'zip_url' => 'https://github.com/pixelgrade/pixtypes/archive/update.zip',
			'sslverify' => false,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'textdomain' => 'pixtypes',
			'debug_mode' => $debug
			//'access_token' => '',
		),

		// shows exception traces on error
		'debug' => $debug,

	); # config
