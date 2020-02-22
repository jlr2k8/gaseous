<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 7/29/2016
 *
 * Breadcrumbs.php
 *
 * Build breadcrumbs on page
 */

namespace Content\Pages;

class Breadcrumbs
{
    public $crumbs = [];

    public function __construct()
    {
        $this->crumb('Home', '/', ['first-crumb', 'home-crumb']);
    }


    /**
     * @param $label
     * @param $url
     * @param $classes
     * @return $this
     */
    public function crumb($label, $url = false, $classes = [])
    {
        // if no URL is specified, use current URI so url is "self"
        $url = $url ? filter_var($url, FILTER_SANITIZE_URL) : $_SERVER['REQUEST_URI'];

        $this->crumbs[] = [
            'label'     => strip_tags(trim($label)),
            'url'       => $url,
            'classes'   => implode(' ', $classes),
        ];

        return $this;
    }


    /**
     * @return null|string
     */
    public function __toString()
    {
        if (empty($this->crumbs))
            return null;

        return '<div class="breadcrumbs">' . \Seo\Breadcrumbs::breadCrumbList($this->crumbs) . '</div>';
    }
}