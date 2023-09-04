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

namespace Content;

use Seo\Url;
use Seo\Breadcrumbs as SeoBreadcrumbs;
use Settings;
use SmartyException;

class Breadcrumbs
{
    public $crumbs = [];
    public $base_url;

    public function __construct()
    {
        $this->base_url   = Settings::value('full_web_url');

        $this->crumb('Home', $this->base_url, ['first-crumb', 'home-crumb']);
    }


    /**
     * @param $uri
     * @param Get $content_obj
     * @param array $crumb_array
     * @return array
     * @throws SmartyException
     */
    protected function buildBreadcrumbArray($uri, Get $content_obj, $crumb_array = [])
    {
        $uri_pieces = Utilities::uriAsArray($uri);
        $parent_uri = Utilities::generateParentUri($uri_pieces);
        $content    = $content_obj->contentByUri($uri, 'active', true, true);

        if (!empty($content)) {
            $key                = hash('md5', serialize($content));
            $crumb_array[$key]  = [
                'label' => $content['page_title_h1'],
                'url'   => $this->base_url . $uri .'/',
            ];
        }

        if (!empty($parent_uri)) {
            return $this->buildBreadcrumbArray($parent_uri, $content_obj, $crumb_array);
        }

        return $crumb_array;
    }


    /**
     * To build the breadcrumbs for the current CMS page, we parse/break up the URI and work our way up to the top.
     * Since the breadcrumbs are built from the top down, however, we have to build the array then reverse it (before
     * we feed it to the rest of the class). Once we pass off the reversed array, then the "Home" breadcrumb will be
     * automatically applied at the beginning.
     *
     * @param $uri
     * @param Get $content_obj
     * @return Breadcrumbs
     * @throws SmartyException
     */
    public function cms($uri, Get $content_obj)
    {
        $crumb_array    = array_reverse($this->buildBreadcrumbArray($uri, $content_obj));

        foreach ($crumb_array as $key => $crumb) {
            $class      = [];
            $class[]    = 'crumb-label-' . Url::convert($crumb['label']);
            $class[]    = 'crumb-url-' . Url::convert($crumb['url']);

            if ($key == array_key_last($crumb_array)) {
                $class[] = 'last-crumb';
            }

            $this->crumb (
                $crumb['label'],
                $crumb['url'],
                $class
            );
        }

        return $this;
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
        $url = $url ? filter_var($url, FILTER_SANITIZE_URL) : Settings::value('relative_uri');

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

        return '<div class="breadcrumbs">' . SeoBreadcrumbs::breadCrumbList($this->crumbs) . '</div>';
    }
}