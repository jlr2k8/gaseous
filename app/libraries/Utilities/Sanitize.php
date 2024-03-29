<?php
/**
* Created by Josh L. Rogers.
* Copyright (c) 2020 All Rights Reserved.
* 9/2/23
*
* Sanitize.php
*
* Static methods and wrapper to extend and maintain PHP's filter_var()'s sanitize filters
*
**/

namespace Utilities;


class Sanitize
{
    public function __construct()
    {

    }


    /**
     * @param $string
     * @return mixed
     */
    public static function email($string)
    {
        return filter_var($string, FILTER_SANITIZE_EMAIL);
    }


    /**
     * @param $string
     * @param $flag
     * @return mixed
     */
    public static function encoded($string, $flag = null)
    {
        return filter_var($string, FILTER_SANITIZE_ENCODED, $flag);
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function magicQuotes($string)
    {
        return self::addSlashes($string);
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function addSlashes($string)
    {
        return filter_var($string, FILTER_SANITIZE_ADD_SLASHES);
    }


    /**
     * @param $string
     * @param $flag
     * @return mixed
     */
    public static function numberFloat($string, $flag = null)
    {
        return filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, $flag);
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function numberInt($string)
    {
        return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
    }


    /**
     * @param $string
     * @param $flag
     * @return mixed
     */
    public static function specialChars($string, $flag = FILTER_FLAG_STRIP_HIGH)
    {
        return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS, $flag);
    }


    /**
     * @param $string
     * @param $flag
     * @return mixed
     */
    public static function fullSpecialChars($string, $flag = FILTER_FLAG_STRIP_HIGH)
    {
        return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS, $flag);
    }


    /**
     * @param $string
     * @return string
     */
    public static function string($string)
    {
        /* NOTE (PHP 8.1 deprecation) - https://www.php.net/manual/en/filter.filters.sanitize.php
         * --------------------------------------------------------------------------------------
         * Strip tags and HTML-encode double and single quotes, optionally strip or encode special characters.
         * Encoding quotes can be disabled by setting FILTER_FLAG_NO_ENCODE_QUOTES.
         * (Deprecated as of PHP 8.1.0, use htmlspecialchars() instead.)
         */

        return htmlspecialchars(strip_tags($string));
    }


    /**
     * @param $string
     * @return string
     */
    public static function stripped($string)
    {
        // Deprecated (PHP 8.1). Alias of FILTER_SANITIZE_STRING - which is also deprecated in PHP 8.1.
        // Due to its deprecation, bitwise flag options will not be supported. Leaving this here for reference:
        // return filter_var($string, FILTER_SANITIZE_STRIPPED, $flag);

        return self::string($string);
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function url($string)
    {
        return filter_var($string, FILTER_SANITIZE_URL);
    }


    /**
     * @param $string
     * @return mixed
     */
    public static function unsafeRaw($string)
    {
        return filter_var($string, FILTER_UNSAFE_RAW);
    }
}