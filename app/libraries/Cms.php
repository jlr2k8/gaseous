 <?php
 /**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 4/2/20
 *
 * Cms.php
 *
 * Class of Smarty-trusted static methods for handy CMS data utilization.
 *
 **/

use Content\Get;
 use Content\Templator;
 use Content\Utilities;
 use Utilities\Pager;

 class Cms
{
    public function __construct()
    {
    }


     /**
      * Allows developers to create their own CMS static methods for templating. If a called method does not exist in
      * this class, this will attempt to call an existing method in an expanded Cms class with this function name. If no
      * matching method from any loaded expansion exists, a null value is returned.
      *
      * @param $name
      * @param $arguments
      * @return mixed|null
      */
     static function __callStatic($name, $arguments)
    {
        $expandable = new Expandable([['Cms', $name], $arguments]);

        return $expandable->return();
    }

     /**
      * @param null $uri
      * @return array
      * @throws SmartyException
      */
    static function getContentByUri($uri = null)
    {
        $uri    = filter_var($uri ?? Settings::value('relative_uri'), FILTER_SANITIZE_URL);

        if (Get::isHomepage($uri)) {
            $uri = GET::HOMEPAGE_URI;
        }

        $get            = new Get();
        $page_content   = $get->contentByUri($uri, 'active', true);

        return $page_content;
    }


     /**
      * @param $content_uid
      * @return array
      * @throws SmartyException
      */
     static function getContentByContentUid($content_uid)
    {
        $get        = new Get();
        $content    = $get->contentByUid($content_uid, 'active', true);

        return $content;
    }


     /**
      * @param $content_uid
      * @param int $page
      * @param int $items_per_page
      * @param string $sortby
      * @param string $order
      * @return array
      * @throws SmartyException
      */
     static function getChildContentByContentUid($content_uid, $sort_by = 'page_title_h1', $sort_ascending = true)
    {
        $get                        = new Get();
        $content_children           = $get->childContent($content_uid, 'active', true);
        $processed_content_children = [];

        $sort_by_col        = array_column($content_children, $sort_by);

        array_multisort($sort_by_col, ($sort_ascending === true ? SORT_ASC : SORT_DESC), $content_children);

        foreach ($content_children as $content) {
            foreach ($content as $row => $row_item) {
                $content_body_fields = $get->body->getBodyFieldValues($content['uid']);

                foreach ($content_body_fields as $key_val) {
                    foreach ($key_val as $key => $val) {
                        $content['body_fields'][$val['template_token']] = $val['value'];
                    }
                }
            }

            $processed_content_children[] = $content;
        }

        return $processed_content_children;
    }


     /**
      * @param null $content_body_type_id
      * @return array
      */
    static function getAllContent($content_body_type_id = null)
    {
        $get                = new Get();
        $content            = $get->all('active', $content_body_type_id);

        foreach ($content as $i => $item) {
            $content_body_fields    = $get->body->getBodyFieldValues($content[$i]['uid']);

            foreach ($content_body_fields as $key_val) {
                foreach ($key_val as $key => $val) {
                    $content[$i]['body_fields'][$val['template_token']] = $val['value'];
                }
            }
        }

        return $content;
    }


     /**
      * @param array $content
      * @param int $page
      * @param int $items_per_page
      * @param string $sort_by
      * @param bool $sort_ascending
      * @return array
      */
    static function getContentPaged(array $content, $page = 1, $items_per_page = 10, $sort_by = 'created_datetime', $sort_ascending = false)
    {
        $pager_status = Pager::status();

        $page               = $pager_status['p'] ?? $page;
        $items_per_page     = $pager_status['per_page'] ?? $items_per_page;
        $sort_by            = $pager_status['sort_by'] ?? $sort_by;
        $sort_ascending    = $pager_status['sort_ascending'] ?? $sort_ascending;

        $sort_by_col        = array_column($content, $sort_by);

        array_multisort($sort_by_col, $sort_ascending ? SORT_ASC : SORT_DESC, $content);

        $displayed_content      = [];
        $page_range['start']    = (int)(($page*$items_per_page)-$items_per_page);
        $page_range['end']      = (int)($page_range['start']+$items_per_page);

        for ($i = $page_range['start']; $i < $page_range['end']; $i++) {
            if (empty($content[$i])) {
                continue;
            }

            $displayed_content[$i]  = $content[$i];
        }

        return $displayed_content;
     }


     /**
      * @param $content
      * @param int $strlen
      * @return mixed|string
      */
    static function teaser($content, $strlen = 150)
    {
        $decoded_content    = htmlspecialchars_decode($content);
        $stripped_content   = strip_tags($decoded_content);

        return Utilities::snippet($stripped_content, $strlen);
    }


     /**
      * @param array $content
      * @param int $page
      * @param int $items_per_page
      * @param string $sort_by
      * @param bool $sort_ascending
      * @param string $pager_style
      * @return bool|string
      * @throws SmartyException
      */
    static function pager(array $content, $page = 1, $items_per_page = 10, $sort_by = 'created_datetime', $sort_ascending = false, $pager_style = 'default')
    {
        $templator      = new Templator();
        $content_count  = (int)count($content);
        $pager_status   = Pager::status();

        $page           = $pager_status['p'] ?? $page;
        $items_per_page = $pager_status['per_page'] ?? $items_per_page;
        $sort_by        = $pager_status['sort_by'] ?? $sort_by;
        $sort_ascending = $pager_status['sort_ascending'] ?? $sort_ascending;

        $page_range['start']    = (int)(($page*$items_per_page)-$items_per_page);
        $page_range['end']      = (int)($page_range['start']+$items_per_page);
        $total_pages            = ($items_per_page != 0 && $content_count > 0)
            ? (int)ceil($content_count / $items_per_page) : (int)1;

        $templator->assign('content_count', $content_count);
        $templator->assign('page', $page);
        $templator->assign('items_per_page', $items_per_page);
        $templator->assign('sort_by', $sort_by);
        $templator->assign('sort_ascending', $sort_ascending);
        $templator->assign('pager_style', $pager_style);
        $templator->assign('page_range', $page_range);
        $templator->assign('total_pages', $total_pages);

        return $templator->fetch('content/body/pager/' . $pager_style . '.tpl');
    }
}