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

namespace Content;

use Assets\Output;
use Cache;
use Db\PdoMySql;
use Db\Query;
use Exception;
use Expandable;
use Log;
use Seo\Minify;
use Settings;
use SmartyException;
use Utilities\AdminView;
use Utilities\DateTime;
use Utilities\Debug;
use Utilities\Pager;
use Uri\Redirect;
use Uri\Uri;

class Get
{
    const HOMEPAGE_URI = '/home';

    public $is_cms_editor   = false;
    protected $uri, $redir, $templator, $expandable;
    public $body, $cache;

    public static $home_pages = [
        '/index.html',
        '/index.htm',
        '/index.php',
        self::HOMEPAGE_URI . '/',
    ];

    public function __construct()
    {
        $this->uri          = new Uri();
        $this->redir        = new Redirect();
        $this->body         = new Body();
        $this->templator    = new Templator();
        $this->cache        = new Cache();
        $this->expandable   = new Expandable();
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
        $uri    = filter_var($uri ?? (Settings::value('relative_uri')), FILTER_SANITIZE_URL);

        if ($redirect_proper_uri === true) {
            $this->redir->properUri($uri);
        }

        $page   = self::isHomepage()
            ? $this->page(self::HOMEPAGE_URI, $find_replace)
            : $this->page(Settings::value('relative_uri'), $find_replace);

        return $page;
    }


    /**
     * @param null $uri
     * @return bool
     */
    public static function isHomepage($uri = null)
    {
        $parsed_uri = parse_url($uri ?? Settings::value('relative_uri'));

        return (new Expandable())->return(!empty($parsed_uri['path']) && $parsed_uri['path'] == '/');
    }


    /**
     * @param $page_uri
     * @param array $find_replace
     * @return string
     * @throws Exception
     */
    public function page($uri, $find_replace = [])
    {
        $page_content   = $this->contentByUri($uri, 'active', false, true);

        $this->validateUri($page_content);

        $find_replace   = array_merge($page_content, $find_replace);
        $page           = null;

        if (!empty($page_content)) {
            $page = $this->templatedPage($find_replace);

            if ($page_content['minify_html_output'] == '1') {
                $page = Minify::html($page);
            }
        } else {
            $page = Http::error(404);
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
        $current_iteration      = $this->currentPageIteration($content_uid);

        $return_url_encoded     = urlencode(Settings::value('full_web_url') . $_SERVER['REQUEST_URI']);
        $is_current_iteration   = ($current_iteration == $content_iteration_uid);

        if ($content_only == false) {
            $templator = $this->templator;

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
     * @param $content_uid
     * @param $exclude_rendered_content
     * @return string
     */
    public static function contentUidCacheKey($content_uid, $without_rendered_content = false)
    {
        return print_r($content_uid, true) . '_' . hash('md5', print_r(Pager::status(), true) . print_r($content_uid, true) . (int)$without_rendered_content);
    }


    /**
     * @param $content_uid
     * @param string $status
     * @param $exclude_rendered_body
     * @param bool $cache
     * @return array
     * @throws SmartyException
     */
    public function contentByUid($content_uid, $status = 'active', $exclude_rendered_body = false, $cache = false)
    {
        $username       = $_SESSION['account']['username'] ?? false;
        $cache_key      = self::contentUidCacheKey($content_uid, $exclude_rendered_body);
        $cached_content = $this->cache->get($cache_key);

        // Only anonymous users should be getting/setting cache
        if (!empty($cached_content) && $cache === true && empty($username)) {
            $return = $cached_content;
        } else {
            $db = new Query();

            $db->select(
                [
                    'uri.uri',
                    'ci.page_title_seo',
                    'ci.page_title_h1',
                    'c.uri_uid',
                    'content_uid'               => 'c.uid',
                    'parent_content_uid'        => 'c.parent_uid',
                    'c.content_body_type_id',
                    'content_body_type_label'   => 'cbt.label',
                    'ci.uid',
                    'ci.meta_desc',
                    'ci.meta_robots',
                    'ci.generated_page_uri',
                    'ci.status',
                    'ci.include_in_sitemap',
                    'ci.minify_html_output',
                    'pr.role_name',
                    'content_created'           => 'c.created_datetime',
                    'content_modified'          => 'ci.created_datetime',
                    'c.created_datetime',
                    'modified_datetime'         => 'ci.created_datetime',
                    'page_identifier_label'     => 'COALESCE(ci.page_title_h1, ci.page_title_seo, uri.uri)'
                ], 'content AS c'
            )->innerJoin(
                'content_body_types AS cbt', 'cbt.type_id = c.content_body_type_id'
            )->innerJoin(
                'uri', 'uri.uid = c.uri_uid'
            )->innerJoin(
                'current_content_iteration AS cci', 'cci.content_uid = c.uid'
            )->innerJoin(
                'content_iteration AS ci', 'ci.uid = cci.content_iteration_uid'
            )->leftJoin(
                'content_roles AS pr', 'pr.content_iteration_uid = ci.uid'
            )->leftJoin(
                'account_roles AS ar', 'pr.role_name = ar.role_name'
            )->leftJoin(
                'account AS a', 'ar.account_username = a.username'
            );


            if (is_array($content_uid)) {
                $db->where(
                    [
                        "c.uid IN ('" . implode("', '", $content_uid) . "')"
                    ]
                );
            } else {
                $db->where(
                    [
                        "c.uid = ?" => [$content_uid]
                    ]
                );
            }

            $db->where(
                [
                    "uri.archived = '0'",
                    "ci.status = ?"         => [$status],
                    "c.archived = '0'",
                    "ci.archived = '0'",
                    "cci.archived = '0'",
                    "cbt.archived = '0'",
                ]
            );

            if ($username) {
                $db->where(
                    [
                        "(pr.role_name IN (SELECT role_name FROM account_roles WHERE account_username = ? AND archived = '0') OR pr.role_name IS NULL)" => [$username]
                    ]
                );
            } else {
                $db->where(
                    [
                        "(pr.role_name IS NULL OR pr.archived = '1')"
                    ]
                );
            }

            if (is_array($content_uid)) {
                $results = $db->fetchAllAssoc();

                foreach ($results as $row => $result) {
                    $roles                  = $this->pageRoles($result['uid']);
                    $results[$row]['roles'] = $roles;
                    $results[$row]['url']   = Settings::value('full_web_url') . '/' . ltrim($result['uri'], '/');

                    if (!$exclude_rendered_body) {
                        $results[$row]['body']  = $this->body->renderTemplate($result['uid'], $this->templator, $result);
                    }
                }

                $return = $results;
            } else {
                $result = $db->fetchAssoc() ?: [];

                if (!empty($result)) {
                    $roles              = $this->pageRoles($result['uid']);
                    $result['roles']    = $roles;
                    $result['url']      = Settings::value('full_web_url') . '/' . ltrim($result['uri'], '/');

                    if (!$exclude_rendered_body) {
                        $result['body'] = $this->body->renderTemplate($result['uid'], $this->templator, $result);
                    }
                }

                $return = $result;
            }

            if ($cache === true && empty($username)) {
                $this->cache->set($cache_key, $return);
            }
        }

        return $this->expandable->return(!empty($return) ? $return : []);
    }


    /**
     * @param $page_uri
     * @param string $status
     * @param $exclude_rendered_template
     * @param bool $cache
     * @return array
     * @throws SmartyException
     */
    public function contentByUri($page_uri, $status = 'active', $exclude_rendered_template = false, $cache = false)
    {
        $parsed_uri = parse_url($page_uri);
        $page_uri   = '/' . trim($parsed_uri['path'], '/');

        $sql        = "
            SELECT DISTINCT
                c.uid
            FROM
                content AS c
            INNER JOIN
                uri ON uri.uid = c.uri_uid
            WHERE
                uri.uri = ?
            AND
                c.archived = '0'
            AND 
                uri.archived = '0';
        ";

        $bind = [
            $page_uri,
        ];

        $db         = new Query($sql, $bind);
        $uid        = $db->fetch();
        $content    = !empty($uid) ? $this->contentByUid($uid, $status, $exclude_rendered_template, $cache) : [];

        return $content;
    }


    /**
     * @param $parent_content_uid
     * @param string $status
     * @param bool $exclude_rendered_template
     * @param bool $cache
     * @return array
     * @throws SmartyException
     */
    public function childContent($parent_content_uid, $status = 'active', $exclude_rendered_template = false, $cache = false)
    {
        $sql = "
            SELECT
                uid
            FROM
                content
            WHERE
                parent_uid = ?
            AND 
                archived = '0';
        ";

        $bind = [
            $parent_content_uid,
        ];

        $db                 = new Query($sql, $bind);
        $child_content_uids = $db->fetchAll();

        $content = $this->contentByUid($child_content_uids, $status, $exclude_rendered_template, $cache);

        return $content;
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
                ci.generated_page_uri,
                ci.status,
                ci.include_in_sitemap,
                ci.minify_html_output,
                ci.created_datetime,
                ci.modified_datetime,
                COALESCE(ci.page_title_h1, ci.page_title_seo) AS page_identifier_label
            FROM content_iteration AS ci
            INNER JOIN content_body_field_values AS cbfv ON ci.uid = cbfv.content_iteration_uid
            INNER JOIN content_body_fields  AS cbf ON cbf.uid = cbfv.content_body_field_uid
            INNER JOIN content_body_types AS cbt ON cbt.type_id = cbf.content_body_type_id
            WHERE ci.uid = ?
            AND ci.archived = '0'
            AND cbfv.archived = '0'
            AND cbf.archived = '0'
            AND cbt.archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();
        $roles  = $this->pageRoles($result['uid']);

        $result['roles']                = $roles;
        $result['body']                 = $this->body->renderTemplate($content_iteration_uid, $this->templator);
        $result['formatted_modified']   = DateTime::formatDateTime($result['modified_datetime']);

        if (empty($result['page_identifier_label']) && !empty($result['body'])) {
            $result['page_identifier_label'] = Utilities::snippet($result['body']);
        }

        return !empty($result) ? $result : array();
    }


    /**
     * @param $status
     * @param $content_body_type_id
     * @return array
     */
    public function all($status = 'active', $content_body_type_id = null)
    {
        $sql = "
            SELECT DISTINCT
                uri.uri,
                c.uid AS content_uid,
                c.parent_uid AS parent_content_uid,
                c.content_body_type_id,
                cbt.label AS content_body_type_label,
                ci.page_title_seo,
                ci.page_title_h1,
                ci.uid,
                ci.meta_desc,
                ci.meta_robots,
                ci.generated_page_uri,
                ci.status,
                ci.include_in_sitemap,
                ci.minify_html_output,
                cic.author,
                c.created_datetime AS content_created,
                ci.created_datetime AS content_modified,
                c.created_datetime,
                ci.created_datetime AS modified_datetime,
                
                COALESCE(
                  NULLIF(ci.page_title_h1, ''),
                  NULLIF(ci.page_title_seo, ''),
                  NULLIF(uri.uri, '')
                ) AS page_identifier_label
            FROM content AS c
            INNER JOIN content_body_types AS cbt ON cbt.type_id = c.content_body_type_id
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
        ";

        $bind[] =   $status;

        if (!empty($content_body_type_id)) {
            $sql .= "
                AND cbt.type_id = ?
                AND cbt.archived = '0'
            ";

            $bind[] = $content_body_type_id;
        }

        $sql .= "
            AND c.archived = '0'
            AND ci.archived = '0'
            AND cci.archived = '0'
            AND uri != ''
            AND uri IS NOT NULL
            GROUP BY uri
            ORDER BY
            CASE WHEN uri.uri = ?
              THEN 0
              ELSE 1
            END,
            uri.uri
        ";

        $bind[] =   self::HOMEPAGE_URI;

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        foreach($results as $key => $val) {
            $results[$key]['formatted_content_created']    = DateTime::formatDateTime($val['content_created'], 'm/d/Y g:i A e');
            $results[$key]['formatted_content_modified']   = DateTime::formatDateTime($val['content_modified'], 'm/d/Y g:i A e');
            $results[$key]['url']                          = Settings::value('full_web_url') . '/' . ltrim($val['uri'], '/');
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
        $templator              = $this->templator;
        $breadcrumbs            = new Breadcrumbs();
        $official_canonical_url = filter_var(Settings::value('official_canonical_url'), FILTER_SANITIZE_URL);

        // core template items
        $find_replace = [
            'page_title_seo'            => $find_replace['page_title_seo'] ?? null,
            'site_announcements'        => $_SESSION['site_announcements'] ?? [],
            'page_title_h1'             => $find_replace['page_title_h1'] ?? null,
            'meta_description'          => $find_replace['meta_desc'] ?? null,
            'meta_robots'               => $find_replace['meta_robots'] ?? null,
            'css_output'                => Output::css($templator),
            'css_iterator_output'       => Output::latestCss($templator),
            'js_output'                 => Output::js($templator),
            'js_iterator_output'        => Output::latestJs($templator),
            'official_canonical_url'    => !empty($official_canonical_url) ? rtrim($official_canonical_url, '/') . $_SERVER['REQUEST_URI'] : 'boogers',
            'breadcrumbs'               => $find_replace['breadcrumbs'] ?? $breadcrumbs->cms(Settings::value('relative_uri'), $this) ?? null,
            'nav'                       => self::nav($templator, $find_replace),
            'body'                      => $find_replace['body'] ?? null,
            'footer'                    => self::footer($templator, $find_replace),
            'administration'            => AdminView::renderAdminList(),
            'debug_footer'              => Debug::footer(),
        ];

        $templated_page = self::main($templator, $find_replace);

        return $templated_page;
    }


    /**
     * @param Templator $templator
     * @param array $find_replace
     * @return string
     * @throws SmartyException
     */
    private static function main(Templator $templator, array $find_replace)
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
        if (!Settings::value('edit_content'))
            throw new Exception('Editing pages not allowed');
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
     * @param $content_uid
     * @return array
     * @throws SmartyException
     */
    private function contentAncestry($content_uid)
    {
        $content = $this->contentByUid($content_uid, 'active', true);

        if (!empty($content['parent_content_uid'])) {
            $content['parent'] = $this->contentAncestry($content['parent_content_uid']);
        }

        return $content;
    }


    /**
     * @param $content_uid
     * @param array $uri
     * @return string
     */
    public function contentUriAncestry($content_uid, $uri = [])
    {
        $content    = $this->contentAncestry($content_uid);

        if (!empty($content)) {
            $uri[] = $content['generated_page_uri'];

            if (!empty($content['parent']) && !empty($content['uri'])) {
                return $this->contentUriAncestry($content['parent']['content_uid'], $uri);
            }
        }

        $uri_array  = array_reverse($uri);
        $real_uri   = implode('/', $uri_array);

        return $real_uri;
    }


    /**
     * @param bool $uri
     * @return null
     */
    public function validateUri(array $content)
    {
        $uri        = !empty($content['uri']) ? (string)$content['uri'] : null;
        $real_uri   = !empty($content['content_uid']) ? (string)$this->contentUriAncestry($content['content_uid']) : null;

        if ($uri != $real_uri && $uri != '/home') {
            $transaction    = new PdoMySql();
            $uri_redirect   = new Redirect();

            $data           = [
                'uri_uid'           => $content['uri_uid'],
                'destination_url'   => $real_uri,
                'http_status_code'  => 301,
                'description'       => 'Page URI was updated on page load to match the correct URI ancestry',
            ];

            $transaction->beginTransaction();

            try {
                $uri_obj    = new Uri($transaction);

                if (!Uri::uriExists($real_uri)) {
                    $uri_obj->insertUri($real_uri);
                }

                // get existing URI UID if this URI already exists, or retrieve it from the newly generated URI in this transaction
                $new_uri_uid = $uri_obj->getUriUid($real_uri);

                // make room for new URI rule in content by eliminating current redirect rules to this new URI, if needed
                if (Uri::uriExistsAsRedirect($real_uri)) {
                    $this->redir->archive($new_uri_uid, $transaction);
                }

                if (Submit::archiveContent($transaction, $content['content_uid'])) {
                    Submit::insertContent($transaction, $content['parent_content_uid'], $content['content_body_type_id'], $new_uri_uid, $content['content_uid']);
                }

                $uri_redirect->insert($data, $transaction);
            } catch (Exception $e) {
                $transaction->rollBack();

                Log::app('URI could not be automatically corrected while validating ancestry (during page load)', $content, $e->getMessage(), $e->getTraceAsString());
            }

            $transaction->commit();

            return $this->expandable->return(Http::redirect($real_uri, 301));
        }

        return $this->expandable->return(true);
    }
}