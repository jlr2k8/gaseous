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
    public function __construct()
    {
    }


    /**
     * @param mixed $_
     * @return bool
     * @throws Exception
     */
    public static function app($_)
    {
        $backtrace      = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $last_level     = array_shift($backtrace);
        $line_number    = $last_level['line'];
        $file           = $last_level['file'];

        ob_start();

        var_dump(func_get_args());

        $log                    = ob_get_clean();
        $formatted_log_entry    = self::formatAppLog($file, $line_number, $log);

        if (PHP_SAPI == 'cli') {
            echo self::outputCli($file, $line_number, $log);
        }

        return self::logEntry($formatted_log_entry);
    }


    /**
     * @param Exception $e
     * @param $file
     * @param $line_number
     * @param $log
     * @return string
     */
    private static function formatAppLog($file, $line_number, $log)
    {
        $logged_in_user = $_SESSION['account']['username'] ?? null;
        $content        =
            date('Y-m-d H:i:s')
            . (!empty($logged_in_user) ? ' (' . $logged_in_user . ')' : null)
            . PHP_EOL
            . $file
            . ' on line '
            . $line_number
            . PHP_EOL
            . $log
            . PHP_EOL
            . PHP_EOL
        ;

        return $content;
    }


    /**
     * @param $file
     * @param $line_number
     * @param $log
     * @return string
     */
    private static function outputCli($file, $line_number, $log)
    {
        $logged_in_user = $_SESSION['account']['username'] ?? null;
        $content        = date('Y-m-d H:i:s') . (!empty($logged_in_user) ? ' (' . $logged_in_user . ')' : null)
            . $file
            . ' on line '
            . $line_number
            . PHP_EOL
            . $log
            . PHP_EOL
        ;

        return $content;
    }



    /**
     * @return mixed|string
     * @throws Exception
     */
    private static function getLogFileName()
    {
        if (!empty($_SESSION['setup_mode'])) {
            $today_log_file = '/tmp/gaseous-setup.log';
        } else {
            $log_file_settings  = Settings::value('log_file') ?: '/var/log/gaseous-{{today}}.log';
            $today_log_file     = str_replace('{{today}}', date('Y-m-d'), $log_file_settings);
        }

        return $today_log_file;
    }


    /**
     * @param $filename
     * @return bool
     * @throws Exception
     */
    private static function createLogFile($filename)
    {
        try {
            touch($filename);
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }


    /**
     * @param $message
     * @param bool $underline
     * @param bool $overline
     * @return bool
     * @throws Exception
     */
    private static function logEntry($message, $overline = false, $underline = false)
    {
        $filename = self::getLogFileName();

        if ($overline === true || $underline === true) {
            $message_length = (int)strlen($message);

            if ($overline === true) {
                $message = self::newLine($message_length) . $message;
            }

            if ($underline === true) {
                $message .= self::newLine($message_length);
            }
        }

        try {
            $file_append = false;

            if (is_writable($filename)) {
                $file_append = true;
            } else {
                self::createLogFile($filename);

                $new_log_message    =  'Log file ' . $filename . ' initiated ' . date('Y-m-d H:i:s');
                $dash_line          = self::newLine(strlen($new_log_message));
                $message            = $dash_line . $new_log_message . $dash_line . $message;
            }

            file_put_contents($filename, $message . self::newLine(), ($file_append ? FILE_APPEND : 0));
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }


    /**
     * @param int $underline
     * @return string
     */
    private static function newLine($underline = 0)
    {
        $newline = PHP_EOL;

        if (!empty($underline) && (int)$underline > (int)0) {
            $i = 0;

            while ($i <= (int)$underline) {
                $newline .= '-';
                $i++;
            }

            $newline .= PHP_EOL;
        }

        return $newline;
    }
}