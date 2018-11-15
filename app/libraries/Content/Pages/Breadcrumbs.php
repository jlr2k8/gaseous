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
        $this->crumb('Home', '/');
    }


    /**
     * @param $label
     * @param bool $url
     * @return $this
     */
    public function crumb($label, $url = false)
    {
        // if no URL is specified, use current URI so url is "self"
        $url = $url ? filter_var($url, FILTER_SANITIZE_URL) : $_SERVER['REQUEST_URI'];

        $this->crumbs[] = [
            'label' => strip_tags(trim($label)),
            'url'   => $url,
        ];

        return $this;
    }


    /**
     * @return null|string
     */
    public function __toString()
    {
        // sanity check - shouldnt happen because the very first crumb is set in the constructor
        if (empty($this->crumbs))
            return null;

        return '<div class="breadcrumbs">' . \Seo\Breadcrumbs::breadCrumbList($this->crumbs) . '</div>';
    }
}