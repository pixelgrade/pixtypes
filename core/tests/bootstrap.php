<?php defined('ABSPATH') or die;

	// ensure PIXTYPES_EXT is defined
	if ( ! defined('PIXTYPES_EXT')) {
		define('PIXTYPES_EXT', '.php');
	}

	error_reporting(-1);

	$basepath = realpath('..').DIRECTORY_SEPARATOR;
	require $basepath.'bootstrap'.PIXTYPES_EXT;
