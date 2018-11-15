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

class GetHomePage extends \Content\Pages\Get
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


    public function redirectHome()
    {
        return \Content\Pages\HTTP::redirect('/', 301);
    }


    public static function isHomepage()
    {
        $parsed_uri = parse_url($_SERVER['REQUEST_URI']);

        return ($parsed_uri['path'] == '/');
    }


    /**
     * @param $parsed_uri
     * @throws \Exception
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
     * @return bool
     */
    public function byUri()
    {
        return self::isHomepage() ? $this->page('home', [], false) : parent::byUri();
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @return bool
     */
    public function page($page_uri = 'home', $find_replace = [], $redirect_proper_uri = true)
    {
        $find_replace   = $this->pageContent('home');
        $parsed_uri     = parse_url($page_uri);

        if (!empty($find_replace) && $page_uri == 'home') {

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