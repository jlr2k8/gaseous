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
        $find_replace = [
            '~\s+~'         => ' ',
            '~\s{2,}~'      => ' ',
            '~>\s<~'        => '><',
            '~<!--.*?-->~'  => null,
        ];

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
        $find_replace = [
            '~\n|\r|\t~'            => ' ',
            '~\/\*[\w\s\*]*\*\/~'   => null,
            '~\s{2,}~'              => ' ',
            '~\s({|})\s~'           => '$1',
        ];

        $find       = array_keys($find_replace);
        $replace    = $find_replace;

        return preg_replace($find, $replace, $input);
    }


    /**
     * @param $input
     * @return null|string|string[]
     */
    public static function js($input)
    {
        // regex taken from https://gist.github.com/Rodrigo54/93169db48194d470188f

        $find_replace = [
            '~("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*~s'  => '$1$2',
            '~;+\}~'                                                                                                                                                    => '}',
            '~([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)~i'                                                                                                            => '$1$3',
            '~([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]~i'                                                                                                         => '$1.$3',
        ];

        $find       = array_keys($find_replace);
        $replace    = $find_replace;

        return preg_replace($find, $replace, $input);
    }
}