<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 5/19/19
 *
 * Log.php
 *
 * Basic logging library
 *
 **/

class Log
{
    public $log_file;

    public function __construct()
    {
    }


    public static function general()
    {
        $backtrace      = debug_backtrace();
        $last_level     = array_shift($backtrace);
        $line_number    = $last_level['line'];
        $file           = $last_level['file'];
        $message_arg    = func_get_args();
        $today_log_file = self::getLogFile();

        ob_start();
        var_dump($message_arg[0]);
        $log = ob_get_clean();

        return error_log (
            PHP_EOL
            . PHP_EOL
            . $file
            . ' on line '
            . $line_number
            . ':'
            . PHP_EOL
            . $log
            . PHP_EOL
            . PHP_EOL
            . PHP_EOL
            . PHP_EOL
            ,
            3,
            $today_log_file
        );
    }


    public static function dev()
    {
        $backtrace      = debug_backtrace();
        $last_level     = array_shift($backtrace);
        $line_number    = $last_level['line'];
        $file           = $last_level['file'];
        $message_arg    = func_get_args();
        $today_log_file = self::getLogFile();

        ob_start();
        var_dump($backtrace, $message_arg[0]);
        $log = ob_get_clean();

        return error_log (
            PHP_EOL
            . PHP_EOL
            . $file
            . ' on line '
            . $line_number
            . ':'
            . PHP_EOL
            . $log
            . PHP_EOL
            . PHP_EOL
            . PHP_EOL
            . PHP_EOL
            ,
            3,
            $today_log_file
        );
    }


    private static function getLogFile()
    {
        $log_file_settings  = \Settings::value('log_file') ?: '/var/log/gaseous-{{today}}.log';
        $today_log_file     = str_replace('{{today}}', date('Y-m-d'), $log_file_settings);

        return $today_log_file;
    }
}