<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/31/20
 *
 * Assets.php
 *
 * Default javascript and CSS
 *
 **/

namespace Setup\Reset;

use Db\PdoMySql;
use PDOException;

class Assets
{
    static $css = '
        .page#banner {
            height: 150px;
            background: radial-gradient(circle, lightsteelblue 0%, steelblue 150%);
            box-shadow: -2px 2px 4px #646464;
            position: fixed;
            width: 100%;
            z-index: 9001;
        }
        
        .page#banner #logo img {
            max-height: 75px;
            width: auto;
            position: absolute;
            top: 36px;
            left: 36px;
        }
        
        .page#banner.reduced {
            height: 50px;
        }
        
        .page#banner.reduced #logo img {
            width: auto;
            height: 80%;
            top: 10%;
            left: 10px;
        }
        
        .page#container {
            float: left;
            width: 100%;
            position: relative; 
            margin-top: 100px;
        }
        
        .page#content {
            padding: 100px 2% 2%;
            background-color: #fff;
            position: relative;
            width: 80%;
            margin: 0 auto;
            min-height: 800px;
            height: 100%;
            display: block; 
        }
        
        .page#menu_container {
            position: absolute;
            max-width: 500px;
            right: 0;
            top: 0;
            z-index: 10;
        }
        
        .page#banner.reduced #menu_container {
          right: initial;
          top: 50px;
          width: 100%;
          max-width: initial;
          background: steelblue;
          color: white;
        }
        
        .page#menu_container .menu_group {
            position: relative;
            display: table;
            margin: auto;
        }
        
        .page#menu_container .menu_group li.menu-item {
            height: 1em;
            padding: .5em 0;
            text-align: center;
            list-style-type: none;
            position: relative;
            margin: 2px;
            min-width: 100px;
            display: table-cell;
            text-transform: uppercase;
            border-top: 1px solid transparent;
            float: left;
            text-decoration: none;
        }
        
        .page#menu_container .menu_group li.menu-item a {
            text-decoration: none;
            color: inherit;
        }
        
        .page#content .breadcrumbs li.home-crumb.only-crumb {
          display: none;
        }
        
        .blog_image.article {
            max-width: 500px;
            float: right;
            margin: 2em;
        }
        
        .blog_image.article img {
          max-width: 100%;
        }
        
        @media screen and (max-width: 800px) {
            .page#banner {
                height: 50px;
            }
            
            .page#banner #logo img {
                width: auto;
                height: 80%;
                top: 10%;
                left: 10px;
            }
            
            .page#menu_container {
                right: initial;
                top: 50px;
                width: 100%;
                max-width: initial;
                background: steelblue;
                color: white;
            }
            
            .page#container {
                margin-top: 25px;
            }
        }
    ';

    static $js = '
        window.onload = function()
        { 
            $(document).ready(function() {
                checkBanner();
            });
            
            $(window).on(\'scroll resize\', function () {
                checkBanner();
            });
            
            function checkBanner()
            {
                if ($(window).scrollTop() &gt; 80 || $(window).width() &lt; 800) {
                    bannerReduced();
                } else {
                    bannerDefault();
                }
            
                return true;
            }
            
            function bannerReduced()
            {
                $(\'#banner\').addClass(\'reduced\', 1000);
                
                return true;
            }
            
            function bannerDefault()
            {
                $(\'#banner\').removeClass(\'reduced\', 1000);
                
                return true;
            }
        }
    ';


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setCss(PdoMySql $transaction)
    {
        $css = self::$css;
        $uid = hash('md5', $css);

        try {
            $sql = "
                INSERT INTO css_iteration (
                    uid,
                    css,
                    description,
                    is_selected
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?
                ) ON DUPLICATE KEY UPDATE
                    css = ?,
                    `description` = ?,
                    is_selected = ?,
                    modified_datetime = NOW(); 
            ";

            $bind = [
                $uid,
                $css,
                'System Default',
                '1',
                $css,
                'System Default',
                '1',
            ];

            $transaction
                ->prepare($sql)
                ->execute($bind);
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setJs(PdoMySql $transaction)
    {
        $js = self::$js;
        $uid = hash('md5', $js);

        try {
            $sql = "
                INSERT INTO js_iteration (
                    uid,
                    js,
                    description,
                    is_selected
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?
                ) ON DUPLICATE KEY UPDATE
                    js = ?,
                    `description` = ?,
                    is_selected = ?,
                    modified_datetime = NOW(); 
            ";

            $bind = [
                $uid,
                $js,
                'System Default',
                '1',
                $js,
                'System Default',
                '1',
            ];

            $transaction
                ->prepare($sql)
                ->execute($bind);
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }
}