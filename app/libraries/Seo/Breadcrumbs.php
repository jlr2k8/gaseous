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
    /**
     * @param array $item_list_elements
     * @return string
     */
    public static function breadCrumbList(array $crumbs)
    {
        $item   = '<ol itemscope itemtype="http://schema.org/BreadcrumbList">';
        $i      = (int)0;

        // generate each crumb with Schema stuff
        foreach ($crumbs as $c) {

            $i++;

            $item .= self::itemListElement($i, $c['label'], $c['url']);
        }

        $item .= '</ol>';

        return $item;
    }


    /**
     * @param $position
     * @param $label
     * @param bool $url
     * @return null|string
     */
    private static function itemListElement($position, $label, $url = false)
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
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"> 
                <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="' . $url . '">
                    <span itemprop="name">' . $label . '</span>
                </a>
                <meta itemprop="position" content="' . $position . '" />
            </li>
        ';

        return $item;
    }
}