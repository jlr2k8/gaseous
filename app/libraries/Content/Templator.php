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

namespace Content\Pages;

use Smarty;
use Smarty_Security;
use SmartyException;

require_once $_SERVER['WEB_ROOT'] . '/libraries/Vendor/Smarty/Smarty.class.php';

class Templator extends Smarty
{
    public $caching, $cache_dir, $security;

    public function __construct()
    {
        parent::__construct();

        $this->security = new Smarty_Security($this);

        $this->setTemplateDir($_SERVER['WEB_ROOT'] . '/views/smarty');
        $this->setCacheDir('/dev/null');
        $this->setCompileDir('/tmp/');

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
     * @param bool $is_cms_editor
     * @return mixed
     * @throws SmartyException
     */
    public static function page($find_replace = [], $is_cms_editor = false)
    {
        $get                = new Get();
        $get->is_cms_editor = $is_cms_editor;

        return $get->templatedPage($find_replace);
    }
}