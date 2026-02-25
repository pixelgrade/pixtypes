<?php defined('ABSPATH') or die;

	// ensure PIXTYPES_EXT is defined
	if ( ! defined('PIXTYPES_EXT')) {
		define('PIXTYPES_EXT', '.php');
	}

	$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;
	require $basepath.'core'.PIXTYPES_EXT;

	// load classes

	$interfacepath = $basepath.'interfaces'.DIRECTORY_SEPARATOR;
	pixtypes::require_all($interfacepath);

	$classpath = $basepath.'classes'.DIRECTORY_SEPARATOR;
	pixtypes::require_all($classpath);

	// load callbacks

	$callbackpath = $basepath.'callbacks'.DIRECTORY_SEPARATOR;
	pixtypes::require_all($callbackpath);
