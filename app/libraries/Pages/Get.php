<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * Get.php
 *
 * Get page by URI, or show error page
 *
 **/

namespace Pages;

class Get
{
    public function __construct()
    {
    }


    /**
     * @throws \Exception
     */
    public function enforceSystemPageRoles()
    {
        $check_system_page_roles = $this->checkSystemPageRoles();

        if (empty($check_system_page_roles))
            \Pages\HTTP::error(401);
    }


    /**
     * @param null $username
     * @param null $uri
     * @return bool
     */
    public function checkSystemPageRoles($username = null, $uri = null)
    {
        $username   = $username ?? \User\Account::getUsername($username);
        $uri        = $uri ?? $_SERVER['REQUEST_URI'];
        $parsed_uri = parse_url($uri);

        $sql = "
            SELECT COUNT(*)
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            LEFT JOIN page_roles AS pr
              ON pr.uri_uid = p.uri_uid
            WHERE p.is_system_page = '1'
            AND uri.uri = ?
        ";

        $bind[] = trim($parsed_uri['path'], '/');

        if ($username) {

            $sql .= "
               AND (
                    pr.role_name IN (SELECT role_name FROM account_roles WHERE account_username = ? AND archived = '0')
                    OR pr.role_name IS NULL
                )
            ";

            $bind[] = $username;

        } else {

            $sql .= " AND (pr.role_name IS NULL OR pr.archived = '1') ";
        }

        $db     = new \Db\Query($sql, $bind);
        $count  = (int)$db->fetch();

        return ($count > (int)0);
    }


    /**
     * @return bool
     */
    public function byUri()
    {
        $uri = $_SERVER['REQUEST_URI'];

        return self::page($uri);
    }


    /**
     * @param $parsed_uri
     * @throws \Exception
     */
    protected function redirectProperUri(array $parsed_uri)
    {
        $current_uri    = $parsed_uri['path'];
        $querystring    = !empty($parsed_uri['query']) ? '?' . $parsed_uri['query'] : null;

        if (!empty($querystring) && $_SERVER['REQUEST_URI'] != $current_uri . $querystring)
            \Pages\HTTP::redirect($current_uri . $querystring, 301);

        if (!stristr($current_uri, '?') && substr($current_uri, -1) != '/')
            \Pages\HTTP::redirect($current_uri . '/' . $querystring, 301);
    }


    /**
     * @param bool $uri
     * @return null
     */
    private function validUri($uri = false)
    {
        $uri = !$uri ? $_SERVER['REQUEST_URI'] : $uri;

        $sql       = '                
            SELECT uri.uri
            FROM page AS p
            INNER JOIN uri ON p.uri_uid = uri.uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            INNER JOIN page_iteration AS pi ON pi.uid = cpi.page_iteration_uid
            WHERE uri.uri = ?
        ';

        $bind = [
            trim($uri, '/'),
        ];

        $db         = new \Db\Query($sql, $bind);
        $valid_uri  = $db->fetch();

        return $valid_uri;
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @return bool
     */
    public function page($page_uri, $find_replace = array(), $redirect_proper_uri = true)
    {
        $parsed_uri         = parse_url($page_uri);
        $valid_uri          = $this->validUri($parsed_uri['path']);
        $find_replace_page  = $this->pageContent($valid_uri);
        $find_replace       = array_merge($find_replace_page, $find_replace);

        if (!empty($find_replace_page)) {

            if ($redirect_proper_uri === true) {
                self::redirectProperUri($parsed_uri);
            }

            $content = $this->templatedPage($find_replace);
        }

        if (empty($content)) {
            $content = \Pages\HTTP::error(404);
        }

        return $content;
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @return bool
     */
    public function pagePreviewByIterationUid($page_iteration_uid, $content_only = false)
    {
        $diff           = new \Pages\Diff();
        $iteration      = $diff->getPageIteration($page_iteration_uid);
        $find_replace   = $this->pageContentForPreview($page_iteration_uid);
        $content        = $this->templatedPage($find_replace);

        if (empty($content)) {
            $content = \Pages\HTTP::error(404);
        } else {
            $content = $this->templatedPage($find_replace);
        }

        if ($content_only == false) {
            $templator = new \Pages\Templator();

            $templator->assign('find_replace', $find_replace);
            $templator->assign('page_iteration_uid', $page_iteration_uid);
            $templator->assign('full_web_url', \Settings::value('full_web_url'));
            $templator->assign('iteration', $iteration);

            $content = $templator->fetch('admin/preview_page_iteration.tpl');
        }

        return $content;
    }


    /**
     * @param $page_uri
     * @param $status
     * @param $username
     * @return bool
     */
    public function pageContent($page_uri, $status = 'active', $username = null)
    {
        $username = $username ?? $_SESSION['account']['username'] ?? false;

        if (empty($page_uri))
            return array();

        $sql = '
            SELECT
                uri.uri,
                pi.page_title_seo,
                pi.page_title_h1,
                p.uri_uid,
                p.page_master_uid,
                pi.uid,
                pi.meta_desc,
                pi.meta_robots,
                pi.content AS body,
                pi.status,
                pi.include_in_sitemap,
                pr.role_name
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            LEFT JOIN page_roles AS pr
              ON pr.uri_uid = p.uri_uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            INNER JOIN page_iteration AS pi ON pi.uid = cpi.page_iteration_uid
            WHERE uri.uri = ?
            AND pi.status = ?
            AND p.archived = \'0\'
            AND pi.archived = \'0\'
            AND cpi.archived = \'0\'
        ';

        $bind = [
            $page_uri,
            $status,
        ];

        if ($username) {
            $sql .= "
               AND (
                    pr.role_name IN (SELECT role_name FROM account_roles WHERE account_username = ? AND archived = '0')
                    OR pr.role_name IS NULL
                )
            ";

            $bind[] = $username;
        } else {
            $sql .= " AND (pr.role_name IS NULL OR pr.archived = '1')";
        }

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();

        return !empty($result) ? $result : array();
    }


    /**
     * @param $page_iteration_uid
     * @return bool
     */
    public function pageContentForPreview($page_iteration_uid)
    {
        $this->editPageCheck();

        $username = $username ?? $_SESSION['account']['username'] ?? false;

        if (empty($page_iteration_uid))
            return [];

        $sql = '
            SELECT
                uri.uri,
                pi.page_title_seo,
                pi.page_title_h1,
                p.uri_uid,
                p.page_master_uid,
                pi.uid,
                pi.meta_desc,
                pi.meta_robots,
                pi.content AS body,
                pi.status,
                pi.include_in_sitemap,
                pi.created,
                pi.modified,
                pr.role_name
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            LEFT JOIN page_roles AS pr
              ON pr.uri_uid = p.uri_uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            INNER JOIN page_iteration AS pi ON pi.uid = ?
            WHERE p.archived = \'0\'
            AND pi.archived = \'0\'
        ';

        $bind = [
            $page_iteration_uid,
        ];

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();

        $result['formatted_modified'] = \Utilities\DateTime::formatDateTime($result['modified']);

        return !empty($result) ? $result : array();
    }


    /**
     * @param $status
     * @param $username
     * @return array
     */
    public function allPages($status = 'active', $username = null)
    {
        $username = $username ?? $_SESSION['account']['username'] ?? false;

        $sql = '
            SELECT
                uri.uri,
                pi.page_title_seo,
                pi.page_title_h1,
                pi.uid,
                pi.meta_desc,
                pi.content AS body,
                pi.status,
                pi.include_in_sitemap
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            LEFT JOIN page_roles AS pr
              ON pr.uri_uid = p.uri_uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            INNER JOIN page_iteration AS pi ON pi.uid = cpi.page_iteration_uid
            WHERE pi.status = ?
            AND p.archived = \'0\'
            AND pi.archived = \'0\'
            AND cpi.archived = \'0\'
        ';

        $bind = [
            $status,
        ];

        if ($username) {
            $sql .= "
               AND (
                    pr.role_name IN (SELECT role_name FROM account_roles WHERE account_username = ? AND archived = '0')
                    OR pr.role_name IS NULL
                )
            ";

            $bind[] = $username;
        } else {
            $sql .= " AND (pr.role_name IS NULL OR pr.archived = '1')";
        }

        $sql .= ' GROUP BY uri.uri ORDER BY uri ASC;';

        $db         = new \Db\Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param array $find_replace
     * @return string
     */
    public function templatedPage($find_replace = array())
    {
        $templator  = new \Pages\Templator();
        $assets     = new \Pages\Assets();

        // core template items
        $find_replace['page_title_seo']     = !empty($find_replace['page_title_seo']) ? $find_replace['page_title_seo'] : \Settings::value('default_page_seo_title');
        $find_replace['page_title_h1']      = !empty($find_replace['page_title_h1']) ? $find_replace['page_title_h1'] : \Settings::value('default_page_title_h1');
        $find_replace['meta_description']   = !empty($find_replace['meta_description']) ? $find_replace['meta_description'] : \Settings::value('default_meta_description');
        $find_replace['meta_robots']        = !empty($find_replace['meta_robots']) ? $find_replace['meta_robots'] : \Settings::value('default_meta_robots');
        $find_replace['css']                = $assets->css;
        $find_replace['js']                 = $assets->js;
        $find_replace['breadcrumbs']        = !empty($find_replace['breadcrumbs']) ? $find_replace['breadcrumbs'] : null;
        $find_replace['body']               = !empty($find_replace['body']) ? $find_replace['body'] : null;
        $find_replace['debug_footer']       = $this->debugFooter($templator);

        return $this->main($templator, $find_replace);
    }


    /**
     * @param Templator $templator
     * @return string
     * @throws \SmartyException
     */
    private function debugFooter(\Pages\Templator $templator)
    {
        ob_start();

        require_once $_SERVER['WEB_ROOT'] . '/includes/debug_footer.php';

        return ob_get_clean();
    }


    /**
     * @param $templator
     * @param array $content
     * @return mixed
     */
    private function main($templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $main_template          = \Settings::value('main_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $main_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @throws \Exception
     */
    public static function addPageCheck()
    {
        if (!\Settings::value('add_pages'))
            throw new \Exception('Adding pages not allowed');
    }


    /**
     * @throws \Exception
     */
    public static function editPageCheck()
    {
        if (!\Settings::value('edit_pages'))
            throw new \Exception('Editing pages not allowed');
    }


    /**
     * @throws \Exception
     */
    public static function archivePageCheck()
    {
        if (!\Settings::value('archive_pages'))
            throw new \Exception('Archiving pages not allowed');
    }


    /**
     * @param $include_system_pages
     * @return array|bool
     */
    public static function allUris($include_system_pages = false)
    {
        $sql = "
          SELECT uri.uid, uri.uri
          FROM uri
          INNER JOIN page ON page.uri_uid = uri.uid
          WHERE uri.archived = '0'
          AND page.archived = '0'
        ";

        if (!$include_system_pages) {
            $sql .= " AND is_system_page = '0' ";
        }

        $sql    .= "ORDER BY uri.uri ASC";
        $db     = new \Db\Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @return array|bool
     */
    public static function uri($uri_uid)
    {
        $sql = "
          SELECT uri
          FROM uri
          WHERE uri.archived = '0'
          AND uid = ?
        ";

        $db = new \Db\Query($sql, [$uri_uid]);

        return $db->fetch();
    }


    /**
     * @return array
     */
    public static function statuses()
    {
        // TODO store in db (new table)
        $statuses = [
            'active',
            'inactive',
        ];

        return $statuses;
    }
}