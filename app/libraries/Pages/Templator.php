<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/1/2018
 *
 * Templator.php
 *
 * Smarty templating
 *
 **/

namespace Pages;

require_once $_SERVER['WEB_ROOT'] . '/libraries/Vendor/Smarty/Smarty.class.php';

class Templator extends \Smarty
{
    public $caching, $cache_dir;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplateDir($_SERVER['WEB_ROOT'] . '/views/smarty');
        $this->cache();
    }


    /**
     * @return bool
     */
    private function cache()
    {
        // TODO - need settings for these
        $this->caching      = false;
        $this->cache_dir    = null;

        return true;
    }


    /**
     * @param array $find_replace
     * @return string
     */
    public static function page($find_replace = array())
    {
        $get = new \Pages\Get();

        return $get->templatedPage($find_replace);
    }
}