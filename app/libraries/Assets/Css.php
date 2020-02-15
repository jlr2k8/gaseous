<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/9/20
 *
 * Css.php
 *
 * System CSS Asset output (e.g. assets/styles.gz.css or
 *
 **/

namespace Assets;

use Seo\Minify;

class Css
{
    public function __construct()
    {
    }


    /**
     * @return bool
     */
    public function get()
    {
        $asset_concat   = new Concat();
        $css_gz_file    = $_SERVER['WEB_ROOT'] . '/assets/styles.gz.css';

        $css_gz = file_exists($css_gz_file) && gzdecode(file_get_contents($css_gz_file))
            ? gzdecode(file_get_contents($css_gz_file))
            : false;

        return Minify::css($css_gz ? $css_gz : $asset_concat->css());
    }
}