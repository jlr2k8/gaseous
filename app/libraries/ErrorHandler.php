<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 11/1/18
 *
 * ErrorHandler.php
 *
 * Custom error handling
 *
 **/

class ErrorHandler
{
    public function __construct()
    {
    }


    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws Exception
     */
    public function errorAsException($errno, $errstr, $errfile, $errline)
    {
        throw new Exception('Error #' . $errno . ' - ' . $errstr . ' on line ' . $errline . ' of ' . $errfile);
    }
}