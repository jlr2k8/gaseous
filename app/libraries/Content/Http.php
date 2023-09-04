<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * Redirect.php
 *
 * Page redirect handling
 *
 **/

namespace Content;

use Exception;
use Log;
use Settings;
use SmartyException;

class Http
{
    public static $status_codes = array (
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        410 => 'Gone',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );


    public function __construct()
    {
    }


    /**
     * @param $status_code
     * @param null $message
     * @param bool $redirect
     * @return bool
     * @throws SmartyException
     */
    public static function error($status_code, $message = null, $redirect = false)
    {
        self::header($status_code);

        Log::app('HTTP Error', $status_code, $message, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);

        if (!empty($redirect)) {
            self::redirect($redirect);
        } else {
            die(self::renderErrorPage($status_code, $message));
        }

        return true;
    }


    /**
     * @param $url
     * @param int $http_response_code
     * @return bool
     * @throws Exception
     */
    public static function redirect($url, $http_response_code = false)
    {
        $url = !empty($url) ? filter_var($url, FILTER_SANITIZE_URL) : false;

        Log::app('HTTP Redirect', $url, 'HTTP Response Code: ' . $http_response_code);

        if (!$url) {
            throw new Exception('Cannot redirect to "' . $url . '"');
        }

        if ($http_response_code) {
            http_response_code($http_response_code);
        }

        header('Location: ' . $url);

        return true;
    }


    /**
     * @param $status_code
     * @return bool
     * @throws Exception
     */
    public static function header($status_code)
    {
        $status_code = !array_key_exists((int)$status_code, self::$status_codes) ? 404 : $status_code;

        Log::app('HTTP/1.1 ' . $status_code . ' ' . self::$status_codes[$status_code]);

        header('HTTP/1.1 ' . $status_code . ' ' . self::$status_codes[$status_code]);

        return true;
    }


    /**
     * @param $status_code
     * @param null $message
     * @return string
     * @throws SmartyException
     */
    private static function renderErrorPage($status_code, $message = null)
    {
        $templator = new Templator();

        $templator->assign('error_code', $status_code);
        $templator->assign('error_name', self::$status_codes[$status_code]);
        $templator->assign('message', $message);

        $body_template          = Settings::value('http_error_template');
        $find_replace['body']   = $templator->fetch('string:' . $body_template);

        Log::app('HTTP Error Page', $status_code);

        return $templator::page($find_replace);
    }
}