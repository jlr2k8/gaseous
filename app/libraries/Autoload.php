<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2017 All Rights Reserved.
 * 12/17/2017
 *
 * Autoload.php
 *
 * SPL Autoloader for library classes
 *
 **/

function autoLoader($class)
{
    $namespaced_filepath    = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file                   = $_SERVER['WEB_ROOT'] . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $namespaced_filepath . '.php';

    if (is_readable($file)) {

        require_once $file;

        return true;
    }

    return false;
}

spl_autoload_register('autoLoader');