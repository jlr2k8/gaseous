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

namespace Content;

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
        $text       = (string)strip_tags($text, '<br>');
        $text       = preg_replace("~\s+~", ' ', $text);
        $ellipsis   = null;

        if (strlen($text) > $strlen) {
            $text       = substr($text, 0, (int)$strlen);
            $ellipsis   = ' &#8230;';
        }

        return $text . $ellipsis;
    }


    /**
     * This function is designed to handle strange characters found in the database.
     * e.g. À was littered everywhere there was an extra space in stored CMS content.
     *
     * I completely doubt this is the correct approach, but I've looked into CKEditor's encoding,
     * MySql, the browser, headers, etc. Investigating each of these led me down some long, twisted rabbit hole
     * and this is the best I could come up with for now. In the distant future when this becomes multilingual,
     * this function right here will probably become a problem....
     *
     * TODO - need to systematically figure out encoding up and down the app. This is a bandaid fix at best.
     *
     * @param $string
     * @return false|string
     */
    public static function decodeUtf8($string)
    {
        $decoded_string = iconv(
            'UTF-8',
            'ISO-8859-1//IGNORE',
            html_entity_decode(
                $string,
                ENT_QUOTES,
                'UTF-8'
            )
        );

        return $decoded_string;
    }
}