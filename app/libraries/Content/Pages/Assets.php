<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * Get.php
 *
 * Get page by URI, or show error page
 *
 **/

namespace Content\Pages;

use Seo;
use Utilities\AssetConcat;

class Assets
{
    public $css, $js;

    public function __construct()
    {
        $this->get();
    }


    /**
     * @return bool
     */
    private function get()
    {
        $asset_concat   = new AssetConcat();
        $css_gz_file    = $_SERVER['WEB_ROOT'] . '/assets/styles.gz.css';
        $js_gz_file     = $_SERVER['WEB_ROOT'] . '/assets/js.gz.js';
        $css_preview    = $_SESSION['css_preview']['css'] ?? [];


        $css_gz = file_exists($css_gz_file) && gzdecode(file_get_contents($css_gz_file))
            ? gzdecode(file_get_contents($css_gz_file))
            : false;

        $this->css  = Seo\Minify::css($css_gz ? $css_gz : $asset_concat->mode('css'));

        if (!empty($css_preview)) {
            $this->css .= Seo\Minify::css($css_preview);
        }

        // TODO - JS preview mode
        $js_gz = file_exists($js_gz_file) && gzdecode(file_get_contents($js_gz_file))
            ? gzdecode(file_get_contents($js_gz_file))
            : false;

        $this->js   = Seo\Minify::js($js_gz ? $js_gz : $asset_concat->mode('js'));

        return true;
    }
}