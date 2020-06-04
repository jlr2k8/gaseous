<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/1/18
 *
 * GetDiff.php
 *
 * Get diff between two structures of text/code
 *
 **/

namespace Utilities;

require_once WEB_ROOT . '/includes/simplediff.php';

class GetDiff
{
    public function __construct()
    {
    }


    /**
     * @param $old
     * @param $new
     * @return null|string|string[]
     */
    static function formattedHtml($old, $new)
    {
        if (is_file($old) && is_readable($old))
            $old = file_get_contents($old);

        if (is_file($new) && is_readable($new))
            $new = file_get_contents($new);

        /*
         * if we already have an encoded set of html code coming in,
         * let's prevent double-encoding so it doesn't encode (possibly corrupt) the rendered HTML to the viewer
         *
         * we want to have any html to be encoded at least once so that we can replace the non-encoded
         * <ins> and <del> tags with <ins class="diff_insert"> and <del class="diff_delete"> (respectively)
         * without making these changes in the core simplediff.php file.
         */
        $old = htmlspecialchars($old, ENT_COMPAT, 'UTF-8', false);
        $new = htmlspecialchars($new, ENT_COMPAT, 'UTF-8', false);

        $html_diff = @htmlDiff($old, $new);

        $find_replace = [
            '~<ins>~' => '<ins class="diff_insert">',
            '~<del>~' => '<del class="diff_delete">',
        ];

        $formatted_diff = preg_replace(
            array_keys($find_replace),
            array_values($find_replace),
            $html_diff
        );

        return $formatted_diff;
    }


    /**
     * @param array $old
     * @param array $new
     * @return null|string|string[]
     */
    static function formattedHtmlArray($old = [], $new = [])
    {
        $old_as_string = implode(', ', $old);
        $new_as_string = implode(', ', $new);

        return self::formattedHtml($old_as_string, $new_as_string);
    }
}