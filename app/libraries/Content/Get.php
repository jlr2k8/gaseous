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

use Assets\Output;
use Content\Menu;
use Db\Query;
use ErrorHandler;
use Exception;
use Seo\Minify;
use Seo\Url;
use Settings;
use SmartyException;
use Utilities\AdminView;
use Utilities\DateTime;
use Utilities\Debug;
use Utilities\SystemPages;
use Uri\Redirect;
use Uri\Uri;

class Get
{
    const HOMEPAGE_URI = '/home';

    public $is_cms_editor   = false;
    protected $uri, $redir;

    public static $home_pages = [
        '/index.html',
        '/index.htm',
        '/index.php',
        self::HOMEPAGE_URI . '/',
    ];

    public function __construct()
    {
        $this->uri      = new Uri();
        $this->redir    = new Redirect();
    }


    /**
     * @param null $uri
     * @param array $find_replace
     * @param $redirect_proper_uri
     * @return string
     * @throws Exception
     */
    public function byUri($uri = null, $find_replace = [], $redirect_proper_uri = true)
    {
        $uri    = filter_var($uri ?? $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

        if ($redirect_proper_uri === true) {
            $this->redir->properUri($uri);
        }

        $page   = self::isHomepage()
            ? $this->page(self::HOMEPAGE_URI, $find_replace)
            : $this->page($uri, $find_replace);

        return $page;
    }


    /**
     * @return bool
     */
    protected static function isHomepage()
    {
        $parsed_uri = parse_url($_SERVER['REQUEST_URI']);

        return ($parsed_uri['path'] == '/');
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @return string
     * @throws Exception
     */
    public function page($page_uri, $find_replace = [])
    {
        $valid_uri      = $this->uri->validate($page_uri);
        $page_content   = $this->pageContent($valid_uri);
        $find_replace   = array_merge($page_content, $find_replace);
        $page           = null;

        if (!empty($page_content)) {
            $page = $this->templatedPage($find_replace);

            if ($page_content['minify_html_output'] == '1') {
                $page = Minify::html($page);
            }
        } else {
            $page = HTTP::error(404);
        }

        return $page;
    }


    /**
     * @param $content_iteration_uid
     * @param $content_uid
     * @param bool $content_only
     * @return bool|string
     * @throws SmartyException
     * @throws Exception
     */
    public function previewByIterationUid($content_iteration_uid, $content_uid, $content_only = false)
    {
        $diff                   = new Diff();
        $iteration              = $diff->getPageIteration($content_iteration_uid);
        $find_replace           = $this->contentForPreview($content_iteration_uid);
        $content                = $this->templatedPage($find_replace);
        $return_url_encoded     = urlencode(Settings::value('full_web_url') . $_SERVER['REQUEST_URI']);
        $current_iteration      = $this->currentPageIteration($content_uid);
        $is_current_iteration   = ($current_iteration == $content_iteration_uid);

        if ($content_only == false) {
            $templator = new Templator();

            $templator->assign('find_replace', $find_replace);
            $templator->assign('content_iteration_uid', $content_iteration_uid);
            $templator->assign('content_uid', $content_uid);
            $templator->assign('full_web_url', Settings::value('full_web_url'));
            $templator->assign('iteration', $iteration);
            $templator->assign('is_current_iteration', $is_current_iteration);
            $templator->assign('return_url_encoded', $return_url_encoded);

            $content = $templator->fetch('admin/preview_content_iteration.tpl');
        }

        return $content;
    }


    /**
     * @param $content_uid
     * @return string
     */
    protected function currentPageIteration($content_uid)
    {
        $sql = "
            SELECT content_iteration_uid
            FROM current_content_iteration
            WHERE content_uid = ?
            AND archived = '0';
        ";

        $bind = [
            $content_uid,
        ];

        $db = new Query($sql, $bind);

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
                ci.page_title_seo,
                ci.page_title_h1,
                c.uri_uid,
                c.uid AS content_uid,
                ci.uid,
                ci.meta_desc,
                ci.meta_robots,
                ci.content AS body,
                ci.status,
                ci.include_in_sitemap,
                ci.minify_html_output,
                pr.role_name,
                COALESCE(ci.page_title_h1, ci.page_title_seo, uri.uri) AS page_identifier_label
            FROM content AS c
            INNER JOIN uri ON uri.uid = c.uri_uid
            INNER JOIN current_content_iteration AS cci ON cci.content_uid = c.uid
            INNER JOIN content_iteration AS ci ON ci.uid = cci.content_iteration_uid
            LEFT JOIN content_roles AS pr
              ON pr.content_iteration_uid = ci.uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            WHERE uri.uri = ?
            AND ci.status = ?
            AND c.archived = '0'
            AND ci.archived = '0'
            AND cci.archived = '0'
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

        $db                 = new Query($sql, $bind);
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
            FROM content_roles
            WHERE content_iteration_uid = ?
            AND archived = '0'
        ";

        $bind = [
            $uri_uid,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAll();

        return $results;
    }


    /**
     * @param $content_iteration_uid
     * @return array
     * @throws Exception
     */
    public function contentForPreview($content_iteration_uid)
    {
        self::editPageCheck();

        if (empty($content_iteration_uid))
            return [];

        $sql = "
            SELECT
                ci.page_title_seo,
                ci.page_title_h1,
                ci.uid,
                ci.meta_desc,
                ci.meta_robots,
                ci.content AS body,
                ci.status,
                ci.include_in_sitemap,
                ci.minify_html_output,
                ci.created_datetime,
                ci.modified_datetime,
                COALESCE(ci.page_title_h1, ci.page_title_seo) AS page_identifier_label
            FROM content_iteration AS ci            
            WHERE ci.uid = ?
            AND ci.archived = '0'
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();
        $roles  = $this->pageRoles($result['uid']);

        $result['roles']                = $roles;
        $result['formatted_modified']   = DateTime::formatDateTime($result['modified_datetime']);

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
    public function all($status = 'active')
    {
        $sql = "
            SELECT DISTINCT
                uri.uri,
                ci.page_title_seo,
                ci.page_title_h1,
                ci.uid,
                ci.meta_desc,
                ci.content AS body,
                ci.status,
                ci.include_in_sitemap,
                ci.minify_html_output,
                cic.author,
                c.created_datetime AS content_created,
                ci.created_datetime AS content_modified,
                COALESCE(
                  NULLIF(ci.page_title_h1, ''),
                  NULLIF(ci.page_title_seo, ''),
                  NULLIF(uri.uri, '')
                ) AS page_identifier_label
            FROM content AS c
            INNER JOIN uri ON uri.uid = c.uri_uid
            INNER JOIN current_content_iteration AS cci ON cci.content_uid = c.uid
            INNER JOIN content_iteration AS ci ON ci.uid = cci.content_iteration_uid
            LEFT JOIN content_iteration_commits AS cic ON ci.uid = cic.content_iteration_uid
            LEFT JOIN content_roles AS pr
              ON pr.content_iteration_uid = ci.uid
            LEFT JOIN account_roles AS ar
              ON pr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            WHERE ci.status = ?
            AND c.archived = '0'
            AND ci.archived = '0'
            AND cci.archived = '0'
            AND (pr.role_name IS NULL OR pr.archived = '1')
            GROUP BY uri
            ORDER BY
            CASE WHEN uri.uri = ?
              THEN 0
              ELSE 1
            END,
            uri.uri
        ";

        $bind = [
            $status,
            self::HOMEPAGE_URI,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        foreach($results as $key => $val) {
            $results[$key]['formatted_content_created']    = DateTime::formatDateTime($val['content_created'], 'm/d/Y g:i A e');
            $results[$key]['formatted_content_modified']   = DateTime::formatDateTime($val['content_modified'], 'm/d/Y g:i A e');
        }

        return $results;
    }


    /**
     * @param array $find_replace
     * @return mixed
     * @throws SmartyException
     */
    public function templatedPage($find_replace = array())
    {
        $templator  = new Templator();

        // core template items
        $find_replace = [
            'page_title_seo'        => $find_replace['page_title_seo'] ?? null,
            'page_title_h1'         => $find_replace['page_title_h1'] ?? null,
            'meta_description'      => $find_replace['meta_description'] ?? null,
            'meta_robots'           => $find_replace['meta_robots'] ?? null,
            'css_output'            => Output::css($templator),
            'css_iterator_output'   => Output::latestCss($templator),
            'js_output'             => Output::js($templator),
            'js_iterator_output'    => Output::latestJs($templator),
            'breadcrumbs'           => $find_replace['breadcrumbs'] ?? $this->cmsBreadcrumbs($_SERVER['REQUEST_URI']) ?? null,
            'nav'                   => self::nav($templator, $find_replace),
            'body'                  => $this->renderTemplate($find_replace['body'], $templator),
            'footer'                => self::footer($templator, $find_replace),
            'administration'        => AdminView::renderAdminList(),
            'debug_footer'          => Debug::footer(),
        ];

        $templated_page = $this->main($templator, $find_replace);

        return $templated_page;
    }


    /**
     * @param $string
     * @param Templator|null $templator
     * @return string|null
     * @throws SmartyException
     */
    private function renderTemplate($string, Templator $templator = null)
    {
        if (!empty($templator)) {
            $templator->security->php_functions             = null;
            $templator->security->php_handling              = $templator::PHP_REMOVE;
            $templator->security->php_modifiers             = null;
            $templator->security->trusted_static_methods    = [
                '\Content' => [],
            ];
            $templator->security->allow_constants           = false;
            $templator->security->allow_super_globals       = false;

            $templator->enableSecurity($templator->security);
        }

        $return = null;
        $error  = new ErrorHandler();

        /***
         * temporarily crank up error handling during this function's runtime so we can catch any errors including
         * notices and warnings. if something goes wrong while trying to process a template, catch the error then
         * output the template as a plain ol' string instead.
        ***/

        set_error_handler([$error, 'errorAsException'], E_ALL);

        try{
            $return = !empty($templator) && $this->is_cms_editor === false ? $templator->fetch('string: ' . $string) : $string;
        } catch (Exception $e) {
            $return = $string;
        }

        restore_error_handler();

        // restore templator security settings
        $templator->enableSecurity();

        return $return;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws SmartyException
     */
    private function main(Templator $templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $main_template          = Settings::value('main_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $main_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws SmartyException
     */
    private static function nav(Templator $templator, array $find_replace)
    {
        $menu                   = new Menu();
        $find_replace['menu']   = $menu->renderMenu();

        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $nav_template           = Settings::value('nav_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $nav_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws SmartyException
     */
    private static function footer(Templator $templator, array $find_replace)
    {
        foreach($find_replace as $key => $val)
            $templator->assign($key, $val);

        $footer_template        = Settings::value('footer_template');
        $fetch_encoded_template = $templator->fetch('string: ' . $footer_template);
        $decoded_template       = htmlspecialchars_decode($fetch_encoded_template);

        return $decoded_template;
    }


    /**
     * @throws Exception
     */
    public static function editPageCheck()
    {
        if (!Settings::value('edit_pages'))
            throw new Exception('Editing pages not allowed');
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
          INNER JOIN content ON content.uri_uid = uri.uid
          WHERE uri.archived = '0'
          AND content.archived = '0'
          ORDER BY uri.uri ASC
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        if ($include_system_pages) {
            $system_pages           = new SystemPages();
            $system_pages_results   = $system_pages->getSystemPagesAsResultSet();

            $results = array_merge($results, $system_pages_results);
        }

        return $results;
    }


    /**
     * @param $uri_uid
     * @return string
     */
    public static function uri($uri_uid)
    {
        $sql = "
          SELECT uri
          FROM uri
          WHERE uri.archived = '0'
          AND uid = ?
        ";

        $db = new Query($sql, [$uri_uid]);

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
    protected function buildBreadcrumbArray($uri, $crumb_array = [])
    {
        $base_url   = Settings::value('full_web_url');
        $valid_uri  = $this->uri->validate($uri);

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
    protected function cmsBreadcrumbs($uri)
    {
        $crumb_array    = array_reverse($this->buildBreadcrumbArray($uri));
        $breadcrumbs    = new Breadcrumbs();

        foreach ($crumb_array as $key => $crumb) {
            $class      = [];
            $class[]    = 'crumb-label-' . $crumb['label'];
            $class[]    = 'crumb-url-' . Url::convert($crumb['url']);

            if ($key == array_key_last($crumb_array)) {
                $class[] = 'last-crumb';
            }

            $breadcrumbs->crumb (
                $crumb['label'],
                $crumb['url'],
                $class
            );
        }

        return $breadcrumbs;
    }
}