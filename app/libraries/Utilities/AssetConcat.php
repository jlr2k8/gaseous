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
    public $scope, $jquery_file;

    public function __construct()
    {
        $this->scope                = $_SERVER['WEB_ROOT'] . '/assets-src';
        $this->jquery_file          = $this->scope . '/js/jquery*.min.js';
        $this->jquery_file_match    = '~' . $this->scope . '/js/jquery(.*).min.js~';
    }


    /**
     * @param $mode
     * @return string
     */
    public function mode($mode)
    {
        $recursive_iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->scope));
        $output             = [];

        // keep jquery at the top
        if ($mode == 'js') {
            $jquery_files = glob($this->jquery_file);

            foreach($jquery_files as $jquery_file) {
                $output[] = file_get_contents($jquery_file);
            };
        }

        // pick out js and css files - store them as strings
        foreach ($recursive_iterator as $file) {
            $real_path = $file->getRealPath();

            // jquery already included, skip
            if($mode == 'js' && preg_match($this->jquery_file_match, $real_path)) {
                continue;
            }

            $substr = $mode == 'css' ? substr($real_path, -4) == '.css' : substr($real_path, -3) == '.js';

            // put into array a file that is css/js (depending on mode), and that doesnt require gzdecoding
            if ($substr && @!gzdecode(file_get_contents($real_path))) {
                $output[] = file_get_contents($real_path);
            }
        }

        return implode($output);
    }
}