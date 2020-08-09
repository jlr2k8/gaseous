<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/9/20
 *
 * Css.php
 *
 *
 **/

namespace Assets;

use Seo\Minify;

class Css
{
    public $concat;

    public function __construct()
    {
        $this->concat = new Concat();
    }


    /**
     * @return bool
     */
    public function get()
    {
        $css_gz_file    = WEB_ROOT . '/assets/styles.gz.css';

        $css_gz = file_exists($css_gz_file) && gzdecode(file_get_contents($css_gz_file))
            ? gzdecode(file_get_contents($css_gz_file))
            : false;

        return Minify::css($css_gz ? $css_gz : $this->concat->css());
    }
}