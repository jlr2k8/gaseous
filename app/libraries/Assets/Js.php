<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/9/20
 *
 * Js.php
 *
 *
 **/

namespace Assets;

use Seo\Minify;

class Js
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
        $asset_concat   = new Concat();
        $js_gz_file    = $_SERVER['WEB_ROOT'] . '/assets/js.gz.js';

        $js_gz = file_exists($js_gz_file) && gzdecode(file_get_contents($js_gz_file))
            ? gzdecode(file_get_contents($js_gz_file))
            : false;

        return Minify::js($js_gz ? $js_gz : $asset_concat->js());
    }
}