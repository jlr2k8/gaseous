<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/8/2018
 *
 * AssetConcat.php
 *
 * In lieu of Grunt generated resources, use this iterator wrapper to grab/concatenate the assets we need for the site
 *
 **/

namespace Utilities;

class AssetConcat
{
    public $scope;

    public function __construct()
    {
        $this->scope = $_SERVER['WEB_ROOT'] . '/assets-src';
    }

    public function mode($mode)
    {
        $recursive_iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(($this->scope)));
        $output             = array();

        // pick out js and css files - store them as strings
        foreach ($recursive_iterator as $file) {
            $real_path = $file->getRealPath();

            $substr = $mode == 'css' ? substr($real_path, -4) == '.css' : substr($real_path, -3) == '.js';

            // put into array a file that is css/js (depending on mode), and that doesnt require gzdecoding
            if ($substr && @!gzdecode(file_get_contents($real_path))) {
                $output[] = file_get_contents($real_path);
            }
        }

        // echo out the found content (depending on the querystring...)
        return implode($output);
    }
}