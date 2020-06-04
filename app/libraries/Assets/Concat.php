<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/8/2018
 *
 * Concat.php
 *
 * In lieu of Grunt generated resources, use this iterator wrapper to grab/concatenate the assets we need for the site
 *
 **/

namespace Assets;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Concat
{
    public $scope, $recursive_iterator, $directory_modified;

    public function __construct()
    {
        $this->scope                = WEB_ROOT . '/assets-src';
        $this->recursive_iterator   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->scope));
        $this->directory_modified   = filemtime($this->scope);
    }


    /**
     * @return string
     */
    public function css()
    {
        $output = [];

        // pick out css files - store them as strings
        foreach ($this->recursive_iterator as $file) {
            $real_path  = $file->getRealPath();
            $substr     = (substr($real_path, -4) == '.css');

            // put into array a file that is css, and that doesnt require gzdecoding
            if ($substr && @!gzdecode(file_get_contents($real_path))) {
                $output[] = file_get_contents($real_path);
            }
        }

        return implode($output);
    }


    /**
     * @return string
     */
    public function js()
    {
        $output                 = [];
        $jquery_file          = $this->scope . '/js/jquery*.min.js';
        $jquery_file_match    = '~' . $this->scope . '/js/jquery(.*).min.js~';

        // keep jquery at the top
        $jquery_files = glob($jquery_file);

        foreach($jquery_files as $jquery_file) {
            $output[] = file_get_contents($jquery_file);
        }

        // pick out js files - store them as strings
        foreach ($this->recursive_iterator as $file) {
            $real_path = $file->getRealPath();

            // jquery already included, skip
            if(preg_match($jquery_file_match, $real_path)) {
                continue;
            }

            $substr = substr($real_path, -3) == '.js';

            // put into array a file that is js, and that doesnt require gzdecoding
            if ($substr && @!gzdecode(file_get_contents($real_path))) {
                $output[] = file_get_contents($real_path);
            }
        }

        return implode($output);
    }
}