<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 3/25/20
 *
 * Output.php
 *
 * Output CSS and JS assets
 *
 **/

namespace Assets;

use Content\Templator;
use Seo\Minify;
use Settings;
use SmartyException;

class Output
{
    public function __construct()
    {
    }


    /**
     * @param Templator $templator
     * @param CssIterator|null $css_iterator
     * @param null $uid
     * @return string
     * @throws SmartyException
     */
    public static function css(Templator $templator, CssIterator $css_iterator = null, $uid = null)
    {
        $inline     = Settings::value('output_iterative_css_inline_html');
        $base_url   = Settings::value('full_web_url');

        if (is_null($uid)) {
            $href   = $base_url . '/styles.gz.css';

            $templator->assign('href', $href);

            $css_output =  $templator->fetch('link-href.tpl');
        } elseif (empty($inline) && !is_null($uid)) {
            $css_iterator   = $css_iterator ?? new CssIterator();
            $css_iteration  = $css_iterator->getCssIteration($uid, true);
            $href           = !empty($css_iteration) ? $base_url . '/styles-' . $css_iteration['uid'] . '.gz.css' : null;

            $templator->assign('href', $href);

            $css_output =  $templator->fetch('link-href.tpl');
        } elseif (!empty($inline) && !is_null($uid)) {
            $css_iterator   = $css_iterator ?? new CssIterator();
            $css_iteration  = $css_iterator->getCssIteration($uid, true);
            $css            = Minify::css($_SESSION['css_preview']['css'] ?? $css_iteration['css']);

            $templator->assign('style', $css);

            $css_output =  $templator->fetch('style.tpl');
        }

        return $css_output;
    }


    /**
     * @param Templator $templator
     * @return string|null
     * @throws SmartyException
     */
    public static function latestCss(Templator $templator)
    {
        $css_iterator       = new CssIterator();
        $latest_css         = $css_iterator->getCurrentCssIteration(true);
        $latest_css_output  = null;

        if (!empty($latest_css)) {
            $latest_css_output  = self::css($templator, $css_iterator, $latest_css['uid'] ?? false);
        }

        return $latest_css_output;
    }


    /**
     * @param Templator $templator
     * @param JsIterator|null $js_iterator
     * @param null $uid
     * @param $async
     * @param $defer
     * @return string
     * @throws SmartyException
     */
    public static function js(Templator $templator, JsIterator $js_iterator = null, $uid = null, $async = true, $defer = true)
    {
        $inline     = Settings::value('output_iterative_js_inline_html');
        $base_url   = Settings::value('full_web_url');

        if (is_null($uid)) {
            $src        = $base_url . '/js.gz.js';

            $templator->assign('src', $src);
            $templator->assign('async', $async);
            $templator->assign('defer', $defer);

            $js_output  = $templator->fetch('script.tpl');
        } elseif (empty($inline) && !is_null($uid)) {
            $js_iterator    = $js_iterator ?? new JsIterator();
            $js_iteration   = $js_iterator->getJsIteration($uid, true);
            $src            = !empty($js_iteration) ? $base_url . '/js-' . $js_iteration['uid'] . '.gz.js' : null;

            $templator->assign('async', $async);
            $templator->assign('defer', $defer);
            $templator->assign('src', $src);

            $js_output  = $templator->fetch('script.tpl');
        } elseif (!empty($inline) && !is_null($uid)) {
            $js_iterator    = $js_iterator ?? new JsIterator();
            $js_iteration   = $js_iterator->getJsIteration($uid, true);
            $js             = Minify::js($_SESSION['js_preview']['js'] ?? $js_iteration['js']);

            $templator->assign('async', $async);
            $templator->assign('defer', $defer);
            $templator->assign('script', $js);
            $templator->assign('src', null);

            $js_output  = $templator->fetch('script.tpl');
        }

        return $js_output;
    }


    /**
     * @param Templator $templator
     * @return string|null
     * @throws SmartyException
     */
    public static function latestJs(Templator $templator)
    {
        $js_iterator        = new JsIterator();
        $latest_js          = $js_iterator->getCurrentJsIteration(true);
        $latest_js_output   = null;

        if (!empty($latest_js)) {
            $latest_js_output  = self::js($templator, $js_iterator, $latest_js['uid'] ?? false);
        }

        return $latest_js_output;
    }

}