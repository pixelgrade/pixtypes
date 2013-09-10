<?php
function load_all_files($directory)
{
    foreach (scandir($directory) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
            load_all_files($directory . DIRECTORY_SEPARATOR . $file);
        } else {

            echo $directory . DIRECTORY_SEPARATOR . $file;
            require_once $directory . DIRECTORY_SEPARATOR . $file;
        }
    }
}

include_once __DIR__ . DIRECTORY_SEPARATOR . 'PixTypesPixTypesFormField.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'Interfaces/PixTypesFormManagerInterface.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'Interfaces/PixTypesFormRendererInterface.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'PixTypesFormBuilder.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'PixTypesFormOptionsManager.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'AdminPixTypesFormRenderer.php';

