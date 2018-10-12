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

namespace Pages;

class Utilities
{
    public function __construct()
    {
    }


    /**
     * $uri can be an array (each uri piece making up the array element values) or a string (the uri string itself)
     *
     * TODO - refactor uri normalization
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
     * TODO - refactor uri normalization
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
        $uri = '/';

        if (!empty($uri_pieces)) {
            $uri .= implode('/', $uri_pieces);
        }

        return $uri;
    }
}