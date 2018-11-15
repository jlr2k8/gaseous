<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 11/14/18
 *
 * Utilities.php
 *
 * Content utilities
 *
 **/

namespace Content;

class Utilities
{
    public function __construct()
    {
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