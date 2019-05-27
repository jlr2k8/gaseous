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

namespace Content\Pages;

class Get
{
    public $is_cms_editor = false;

    public function __construct()
    {
    }


    /**
     * @param $uri
     * @return string
     * @throws \Exception
     */
    public function byUri($uri = null)
    {
        $find_replace   = [];
        $uri            = filter_var($uri ?? $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

        return self::page($uri, $find_replace);
    }


    /**
     * @param array $parsed_uri
     * @return bool
     * @throws \Exception
     */
    protected function redirectProperUri(array $parsed_uri)
    {
        $current_uri    = $parsed_uri['path'];
        $querystring    = !empty($parsed_uri['query']) ? '?' . $parsed_uri['query'] : null;

        if (!empty($querystring) && $_SERVER['REQUEST_URI'] != $current_uri . $querystring) {
            HTTP::redirect($current_uri . $querystring, 301);
        }

        if (!stristr($current_uri, '?') && substr($current_uri, -1) != '/') {
            HTTP::redirect($current_uri . '/' . $querystring, 301);
        }

        return false;
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
            rtrim($uri, '/'),
        ];

        $db         = new \Db\Query($sql, $bind);
        $valid_uri  = $db->fetch();

        return $valid_uri;
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @param bool $redirect_proper_uri
     * @return string
     * @throws \Exception
     */
    public function page($page_uri, $find_replace = [], $redirect_proper_uri = true)
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
            HTTP::error(404);
        }

        return $content;
    }


    /**
     * @param $page_iteration_uid
     * @param $page_master_uid
     * @param bool $content_only
     * @return bool|string
     * @throws \SmartyException
     */
    public function pagePreviewByIterationUid($page_iteration_uid, $page_master_uid, $content_only = false)
    {
        $diff                   = new Diff();
        $iteration              = $diff->getPageIteration($page_iteration_uid);
        $find_replace           = $this->pageContentForPreview($page_iteration_uid);
        $content                = $this->templatedPage($find_replace);
        $return_url_encoded     = urlencode(\Settings::value('full_web_url') . $_SERVER['REQUEST_URI']);
        $current_iteration      = $this->currentPageIteration($page_master_uid);
        $is_current_iteration   = ($current_iteration == $page_iteration_uid);

        if (empty($content)) {
            $content = HTTP::error(404);
        } else {
            $content = $this->templatedPage($find_replace);
        }

        if ($content_only == false) {
            $templator = new Templator();

            $templator->assign('find_replace', $find_replace);
            $templator->assign('page_iteration_uid', $page_iteration_uid);
            $templator->assign('page_master_uid', $page_master_uid);
            $templator->assign('full_web_url', \Settings::value('full_web_url'));
            $templator->assign('iteration', $iteration);
            $templator->assign('is_current_iteration', $is_current_iteration);
            $templator->assign('return_url_encoded', $return_url_encoded);

            $content = $templator->fetch('admin/preview_page_iteration.tpl');
        }

        return $content;
    }


    /**
     * @param $page_master_uid
     * @return string
     */
    private function currentPageIteration($page_master_uid)
    {
        $sql = "
            SELECT page_iteration_uid
            FROM current_page_iteration
            WHERE page_master_uid = ?
        ";

        $bind = [
            $page_master_uid,
        ];

        $db = new \Db\Query($sql, $bind);

        return $db->fetch();
    }


    /**
     * @param $page_uri
     * @param $status
     * @param $username
     * @return array
     */
    public function pageContent($page_uri, $status = 'active', $username = null)
    {
        $username = $username ?? $_SESSION['account']['username'] ?? false;

        if (empty($page_uri))
            return [];

        $sql = "
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
                pr.role_name,
                COALESCE(pi.page_title_h1, pi.page_title_seo, uri.uri) AS page_identifier_label
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            INNER JOIN page_iteration AS pi ON pi.uid = cpi.page_iteration_uid
            LEFT JOIN page_roles AS pr
              ON pr.page_iteration_uid = pi.uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            WHERE uri.uri = ?
            AND pi.status = ?
            AND p.archived = '0'
            AND pi.archived = '0'
            AND cpi.archived = '0'
        ";

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

        $db                 = new \Db\Query($sql, $bind);
        $result             = $db->fetchAssoc();
        $roles              = $this->pageRoles($result['uid']);
        $result['roles']    = $roles;

        return !empty($result['uid']) ? $result : array();
    }


    /**
     * @param $uri_uid
     * @return array
     */
    public function pageRoles($uri_uid)
    {
        $sql = "
            SELECT role_name
            FROM page_roles
            WHERE page_iteration_uid = ?
            AND archived = '0'
        ";

        $bind = [
            $uri_uid,
        ];

        $db         = new \Db\Query($sql, $bind);
        $results    = $db->fetchAll();

        return $results;
    }


    /**
     * @param $page_iteration_uid
     * @return array
     * @throws \Exception
     */
    public function pageContentForPreview($page_iteration_uid)
    {
        self::editPageCheck();

        if (empty($page_iteration_uid))
            return [];

        $sql = "
            SELECT
                pi.page_title_seo,
                pi.page_title_h1,
                pi.uid,
                pi.meta_desc,
                pi.meta_robots,
                pi.content AS body,
                pi.status,
                pi.include_in_sitemap,
                pi.created,
                pi.modified,
                COALESCE(pi.page_title_h1, pi.page_title_seo) AS page_identifier_label
            FROM page_iteration AS pi            
            WHERE pi.uid = ?
            AND pi.archived = '0'
        ";

        $bind = [
            $page_iteration_uid,
        ];

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();
        $roles  = $this->pageRoles($result['uid']);

        $result['roles']                = $roles;
        $result['formatted_modified']   = \Utilities\DateTime::formatDateTime($result['modified']);

        if (empty($result['page_identifier_label']) && !empty($result['body'])) {
            $result['page_identifier_label'] = \Content\Utilities::snippet($result['body']);
        }

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

        $sql = "
            SELECT DISTINCT
                uri.uri,
                pi.page_title_seo,
                pi.page_title_h1,
                pi.uid,
                pi.meta_desc,
                pi.content AS body,
                pi.status,
                pi.include_in_sitemap,
                pic.author,
                p.created AS page_created,
                pi.created AS page_modified,
                COALESCE(
                  NULLIF(pi.page_title_h1, ''),
                  NULLIF(pi.page_title_seo, ''),
                  NULLIF(uri.uri, '')
                ) AS page_identifier_label
            FROM page AS p
            INNER JOIN uri ON uri.uid = p.uri_uid
            INNER JOIN current_page_iteration AS cpi ON cpi.page_master_uid = p.page_master_uid
            INNER JOIN page_iteration AS pi ON pi.uid = cpi.page_iteration_uid
            INNER JOIN page_iteration_commits AS pic ON pi.uid = pic.page_iteration_uid
            LEFT JOIN page_roles AS pr
              ON pr.page_iteration_uid = pi.uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            WHERE pi.status = ?
            AND p.archived = '0'
            AND pi.archived = '0'
            AND cpi.archived = '0'
            AND pic.archived = '0'
        ";

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

        $sql .= "
            GROUP BY uri
            ORDER BY
            CASE WHEN uri.uri = '/home'
              THEN 0
              ELSE 1
            END,
            uri.uri
        ";

        $db         = new \Db\Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        foreach($results as $key => $val) {
            $results[$key]['formatted_page_created']    = \Utilities\DateTime::formatDateTime($val['page_created'], 'm/d/Y g:i A e');
            $results[$key]['formatted_page_modified']   = \Utilities\DateTime::formatDateTime($val['page_modified'], 'm/d/Y g:i A e');
        }

        return $results;
    }


    /**
     * @param array $find_replace
     * @return mixed
     * @throws \SmartyException
     */
    public function templatedPage($find_replace = array())
    {
        $templator  = new Templator();
        $assets     = new Assets();

        // core template items
        $find_replace = [
            'page_title_seo'    => !empty($find_replace['page_title_seo']) ? $find_replace['page_title_seo'] : \Settings::value('default_page_seo_title'),
            'page_title_h1'     => !empty($find_replace['page_title_h1']) ? $find_replace['page_title_h1'] : \Settings::value('default_page_title_h1'),
            'meta_description'  => !empty($find_replace['meta_description']) ? $find_replace['meta_description'] : \Settings::value('default_meta_description'),
            'meta_robots'       => !empty($find_replace['meta_robots']) ? $find_replace['meta_robots'] : \Settings::value('default_meta_robots'),
            'css'               => $assets->css,
            'js'                => $assets->js,
            'breadcrumbs'       => !empty($find_replace['breadcrumbs']) ? $find_replace['breadcrumbs'] : $this->cmsBreadcrumbs($_SERVER['REQUEST_URI']),
            'nav'               => $this->nav($templator, $find_replace),
            'body'              => $this->renderTemplate($find_replace['body'], $templator),
            'footer'            => $this->footer($templator, $find_replace),
            'debug_footer'      => $this->debugFooter(),
        ];

        return $this->main($templator, $find_replace);
    }


    /**
     * @param $string
     * @param Templator|null $templator
     * @return string|null
     * @throws \SmartyException
     */
    private function renderTemplate($string, Templator $templator = null)
    {
        if (!empty($templator)) {
            $templator->security->php_functions         = ['date'];
            $templator->security->php_handling          = $templator::PHP_REMOVE;
            $templator->security->php_modifiers         = null;
            $templator->security->static_classes        = null;
            $templator->security->allow_constants       = false;
            $templator->security->allow_super_globals   = false;

            $templator->enableSecurity($templator->security);
        }

        $return = null;
        $error  = new \ErrorHandler();

        /***
         * temporarily crank up error handling during this function's runtime so we can catch any errors including
         * notices and warnings. if something goes wrong while trying to process a template, catch the error then
         * output the template as a plain ol' string instead.
        ***/

        set_error_handler([$error, 'errorAsException'], E_ALL);

        try{
            $return = !empty($templator) && $this->is_cms_editor === false ? $templator->fetch('string: ' . $string) : $string;
        } catch (\Exception $e) {
            $return = $string;
        }

        restore_error_handler();

        // restore templator security settings
        $templator->enableSecurity();

        return $return;
    }


    /**
     * @param Templator $templator
     * @return string
     * @throws \SmartyException
     */
    private function debugFooter()
    {
        ob_start();

        require_once $_SERVER['WEB_ROOT'] . '/includes/debug_footer.php';

        return ob_get_clean();
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws \SmartyException
     */
    private function main(Templator $templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $main_template          = \Settings::value('main_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $main_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws \SmartyException
     */
    private function nav(Templator $templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $nav_template           = \Settings::value('nav_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $nav_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws \SmartyException
     */
    private function footer(Templator $templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $footer_template        = \Settings::value('footer_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $footer_template);
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
          ORDER BY uri.uri ASC
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        if ($include_system_pages) {
            $system_pages           = new \Utilities\SystemPages();
            $system_pages_results   = $system_pages->getSystemPagesAsResultSet();

            $results = array_merge($results, $system_pages_results);
        }

        return $results;
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


    /**
     * @param $uri
     * @param array $crumb_array
     * @return array
     */
    private function buildBreadcrumbArray($uri, $crumb_array = [])
    {
        $base_url       = \Settings::value('full_web_url');
        $parsed_uri     = parse_url($uri);
        $valid_uri      = $this->validUri($parsed_uri['path']);

        if ($valid_uri) {
            $uri_pieces     = Utilities::uriAsArray($valid_uri);
            $parent_uri     = Utilities::generateParentUri($uri_pieces);

            $page           = $this->pageContent($valid_uri);

            if (!empty($page)) {
                $crumb_array[]  = [
                    'label' => $page['page_title_h1'],
                    'url'   => $base_url . $valid_uri .'/',
                ];
            }

            if (!empty($parent_uri)) {
                return $this->buildBreadcrumbArray($parent_uri, $crumb_array);
            }
        }

        return $crumb_array;
    }


    /**
     * To build the breadcrumbs for the current CMS page, we parse/break up the URI and work our way up to the top.
     * Since the breadcrumbs are built from the top down, however, we have to build the array then reverse it (before
     * we feed it to the \Content\Pages\Breadcrumbs() class). Once we pass off the reversed array, then that class will
     * apply the "Home" breadcrumb at the very beginning.
     *
     * @param $uri
     * @return Breadcrumbs
     */
    private function cmsBreadcrumbs($uri)
    {
        $crumb_array    = array_reverse($this->buildBreadcrumbArray($uri));
        $breadcrumbs    = new Breadcrumbs();

        foreach ($crumb_array as $crumb) {
            $breadcrumbs->crumb (
                $crumb['label'],
                $crumb['url']
            );
        }

        return $breadcrumbs;
    }
}