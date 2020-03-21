<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/1/2018
 *
 * GetHomePage.php
 *
 * Check URI and redirect home
 *
 **/

namespace Content\Pages;

use Exception;

class GetHomePage extends Get
{
    public function __construct()
    {
    }


    public static $home_pages = [
        '/index.html',
        '/index.htm',
        '/index.php',
        '/home/',
    ];


    /**
     * @return bool
     * @throws Exception
     */
    public function redirectHome()
    {
        return HTTP::redirect('/', 301);
    }


    /**
     * @return bool
     */
    public static function isHomepage()
    {
        $parsed_uri = parse_url($_SERVER['REQUEST_URI']);

        return ($parsed_uri['path'] == '/');
    }


    /**
     * @param array $parsed_uri
     * @return bool
     * @throws Exception
     */
    protected function redirectProperUri(array $parsed_uri)
    {
        $current_uri = $parsed_uri['path'];

        if (in_array($current_uri, self::$home_pages)) {

            return parent::redirectProperUri($parsed_uri);
        }

        return false;
    }


    /**
     * @param null $uri
     * @return bool|string
     * @throws Exception
     */
    public function byUri($uri = null)
    {
        return self::isHomepage() ? $this->page('/home', [], false) : parent::byUri($uri);
    }


    /**
     * @param string $page_uri
     * @param array $find_replace
     * @param bool $redirect_proper_uri
     * @return string
     * @throws Exception
     */
    public function page($page_uri = 'home', $find_replace = [], $redirect_proper_uri = true)
    {
        $parsed_uri         = parse_url($page_uri);
        $find_replace_page  = $this->pageContent('/home');
        $find_replace       = array_merge($find_replace_page, $find_replace);

        if (!empty($find_replace) && $page_uri == '/home') {
            if ($redirect_proper_uri === true) {
                self::redirectProperUri($parsed_uri);
            }

            $content = $this->templatedPage($find_replace);
        } else {
            $content = parent::page($page_uri, $find_replace, false);
        }

        return $content;
    }
}