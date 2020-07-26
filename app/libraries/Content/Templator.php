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

namespace Content;

use ErrorHandler;
use Settings;
use Smarty;
use Smarty_Security;
use SmartyException;

class Templator extends Smarty
{
    public $security;

    public function __construct()
    {
        parent::__construct();

        $this->security = new Smarty_Security($this);
        $this->setTemplateDir(WEB_ROOT . '/views/smarty');

        // Universally available assignments
        $this->assign('full_web_url', Settings::value('full_web_url'));
        $this->assign('relative_uri', Settings::value('relative_uri'), true);

        $this->compile();
        $this->cache();
    }


    /**
     * @return bool
     */
    private function compile()
    {
        $template_compile_dir   = Settings::value('template_compile_dir');

        $this->compile_dir      = is_writable($template_compile_dir) ? $template_compile_dir : '/tmp/' . date('Ymdhms');

        $this->setCompileDir($this->compile_dir);

        return true;
    }


    /**
     * @return bool
     */
    private function cache()
    {
        $this->caching      = false;
        $this->cache_dir    = '/dev/null';

        $this->setCacheDir($this->cache_dir);

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


    /**
     * @param null $security_class
     * @param array $trusted_static_methods
     * @return Smarty|void
     * @throws SmartyException
     */
    public function enableSecurity($security_class = null, $trusted_static_methods = [])
    {
        $this->security->php_functions             = array_merge($this->security->php_functions, ['strtotime', 'date', 'array_merge']);
        $this->security->php_handling              = null;
        $this->security->php_modifiers             = null;
        $this->security->trusted_static_methods    = $trusted_static_methods;
        $this->security->allow_constants           = false;
        $this->security->allow_super_globals       = false;

        $error  = new ErrorHandler();

        set_error_handler([$error, 'errorAsException'], E_ALL);

        return parent::enableSecurity($this->security);
    }


    /**
     * @return Smarty
     */
    public function disableSecurity()
    {
        restore_error_handler();

        return parent::disableSecurity();
    }
}