<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 8/31/18
 *
 * Utilities.php
 *
 * Static helper functions for pages
 *
 */

namespace Content\Pages;

use Db\Query;

class Utilities
{
    public function __construct()
    {
    }


    /**
     * $uri can be an array (each uri piece making up the array element values) or a string (the uri string itself)
     *
     * @param $uri
     * @return string
     */
    static function generateParentUri($uri)
    {
        if (is_array($uri)) {
            $uri_as_array = $uri;
        } elseif (!is_array($uri) && is_string($uri)) {
            $uri_as_array = self::uriAsArray($uri);
        } else {
            return null;
        }

        array_pop($uri_as_array);

        return self::arrayAsUri($uri_as_array);
    }


    /**
     * $uri can be an array (each uri piece making up the array element values) or a string (the uri string itself)
     *
     * @param array | string $uri
     * @return string
     */
    static function getLastPartOfUri($uri)
    {
        if (is_array($uri)) {
            $uri_as_array = $uri;
        } elseif (!is_array($uri) && is_string($uri)) {
            $uri_as_array = self::uriAsArray($uri);
        } else {
            return null;
        }

        $last_array_element = end($uri_as_array);
        $last_uri_piece     = trim($last_array_element,'/');

        return $last_uri_piece;
    }


    /**
     * @param $uri
     * @return array
     */
    static function uriAsArray($uri)
    {
        trim($uri, '/');

        $uri_exploded = explode('/', $uri);

        return $uri_exploded;
    }


    /**
     * @param array $uri_pieces
     * @return string
     */
    static function arrayAsUri(array $uri_pieces)
    {
        $uri = null;

        if (!empty($uri_pieces)) {
            $uri .= implode('/', $uri_pieces);
        }

        return $uri;
    }


    /**
     * @param $text
     * @param int $strlen
     * @return mixed|string
     */
    public static function snippet($text, $strlen = 10)
    {
        $text = (string)strip_tags(htmlspecialchars_decode($text), '<br>');

        $ellipsis = ' &#8230;';

        // no need to trim
        if (strlen($text) <= $strlen) {

            return $text;
        }

        // replace non-alphanum chars in stripped/decoded string
        $text = preg_replace('~^[A-Za-z0-9]$~', null, $text);

        // see if length is still greater than $strlen
        if (strlen($text) <= $strlen) {
            $ellipsis = null;
        }

        return substr($text, 0, (int)$strlen) . $ellipsis;
    }
}