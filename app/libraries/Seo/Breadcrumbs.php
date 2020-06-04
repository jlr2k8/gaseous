<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/15/18
 *
 * Breadcrumbs.php
 *
 * Schema.org face-lift for navigation breadcrumbs
 *
 */

namespace Seo;

class Breadcrumbs
{
    public function __construct()
    {
    }


    /**
     * @param array $crumbs
     * @return string
     */
    public static function breadCrumbList(array $crumbs)
    {
        $item   = '<ol itemscope itemtype="http://schema.org/BreadcrumbList">';
        $i      = (int)0;

        // generate each crumb with Schema stuff
        foreach ($crumbs as $c) {
            // throw in an only-crumb class if only one crumb is shown on the page (e.g. the homepage)
            if (count($crumbs) == (int)1) {
                $c['classes'] .= ' only-crumb';
            }

            $i++;

            $item .= self::itemListElement($i, $c['label'], $c['url'], $c['classes']);
        }

        $item .= '</ol>';

        return $item;
    }


    /**
     * @param $position
     * @param $label
     * @param $url
     * @param $classes
     * @return null|string
     */
    private static function itemListElement($position, $label, $url = false, $classes = null)
    {
        // sanity check
        if (empty($label)) {
            return null;
        }

        // filter passed in URL or grab the URI as default (so anchor href link will effectively link to itself)
        $url = $url ? filter_var($url, FILTER_SANITIZE_URL) : $_SERVER['REQUEST_URI'];

        // first crumb does not have an arrow before it
        $arrow = $position != (int)1 ? '&#160;&#187;&#160;' : null;

        // TODO - put in template
        $item = $arrow . '
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="' . $classes . '"> 
                <a itemprop="item" href="' . $url . '"><span itemprop="name">' . $label . '</span></a>
                <meta itemprop="position" content="' . $position . '" />
            </li>
        ';

        return $item;
    }
}