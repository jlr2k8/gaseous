<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/5/18
 *
 * Minify.php
 *
 * Minify CSS, JS and HTML output
 *
 **/

namespace Seo;

class Minify
{
    public function __construct()
    {
    }


    /**
     * @param $input
     * @return null|string|string[]
     */
    public static function html($input)
    {
        $find_replace = array(
            '~\n|\r|\t~' => ' ',
            '~\s{2,}~' => ' ',
            '~>\s<~' => '><',
            '~<!--.*?-->~' => null,
        );

        $find       = array_keys($find_replace);
        $replace    = $find_replace;

        return preg_replace($find, $replace, $input);
    }


    /**
     * @param $input
     * @return null|string|string[]
     */
    public static function css($input)
    {
        $find_replace = array(
            '~\n|\r|\t~' => ' ',
            '~\/\*[\w\s\*]*\*\/~' => null,
            '~\s{2,}~' => ' ',
            '~\s({|})\s~' => '$1',
        );

        $find       = array_keys($find_replace);
        $replace    = $find_replace;

        return preg_replace($find, $replace, $input);
    }
}