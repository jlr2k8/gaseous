<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 9/13/19
 *
 * Reset.php
 *
 *
 *
 **/

namespace Utilities;

class Reset
{
    static $settings = [
        'web_url' =>    [
            'display'       => 'Web URL',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
            'value'         => 'localhost'
        ],
        'cookie_domain' => [
            'display'       => 'Cookie domain',
            'category_key'  => 'cookie',
            'role_based'    => false,
            'description'   => '',
            'value'         => '.localhost'
        ],
        'login_cookie_expire_days' => [
            'display'       => 'Expiration for login cookies',
            'category_key'  => 'cookie',
            'role_based'    => false,
            'description'   => '',
            'value'         => '7'

        ],
        'enable_template_caching' => [
            'display'       => 'Enable Template Caching',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
            'value'         => 'false'
        ],
        'enable_ssl' => [
            'display'       => 'Enable SSL',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
            'value'         => 'false'
        ],
        'recaptcha_public_key' => [
            'display'       => 'ReCaptcha Public Key',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => '',
            'value'         => ''
        ],
        'recaptcha_private_key' => [
            'display'       => 'ReCaptcha Private Key',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => '',
            'value'         => ''
        ],
        'require_recaptcha' => [
            'display'       => 'Require ReCaptcha',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => 'Any page that has a ReCaptcha, make the validation required.',
            'value'         => 'false'
        ],
        'maintenance_mode' => [
            'display'       => 'Maintenance Mode',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => 'When enabled, this will set the HTTP 503 header and prevent every page from being displayed, instead, showing a temporary site downtime on every URI. Search engines, if caching the site during this time, will simply receive this as a temporary downtime then return later. This mode is especially useful when the site\'s software is undergoing updates or the site is intentionally being maintained.',
            'value'         => 'false'
        ],
        'show_debug' => [
            'display'       => 'Show Debug Footer',
            'category_key'  => 'development',
            'role_based'    => true,
            'description'   => 'Per role, this allows an app-level troubleshooting footer (below the site\'s footer) to display information such as page load time, server globals and session globals.',
            'value'         => 'false'
        ],
        'smtp_host' => [
            'display'       => 'SMTP Server Hostname/IP',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
            'value'         => 'localhost'
        ],
        'smtp_port' => [
            'display'       => 'SMTP Host\'s Port',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
            'value'         => '25'
        ],
        'smtp_user' => [
            'display'       => 'SMTP User',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
            'value'         => ''
        ],
        'smtp_password' => [
            'display'       => 'SMTP Password',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
            'value'         => ''
        ],
        'registration_access_code' => [
            'display'       => 'Registration Access Code',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
            'value'         => ''
        ],
        'main_template' => [
            'display'       => 'Main Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
            'value'         => '
                &lt;!DOCTYPE html&gt;&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot;&gt;
                &lt;head&gt;
                &lt;meta name=&quot;description&quot; content=&quot;{$meta_description}&quot; /&gt;
                &lt;meta charset=&quot;UTF-8&quot; /&gt;
                &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot; /&gt;
                &lt;meta name=&quot;robots&quot; content=&quot;{$meta_robots}&quot; /&gt;
                &lt;title&gt;{$page_title_seo}&lt;/title&gt;
                &lt;style&gt;{$css}&lt;/style&gt;
                &lt;link href=&quot;https://fonts.googleapis.com/css?family=Open+Sans&quot; rel=&quot;stylesheet&quot;&gt; 
                &lt;link href=&quot;/styles.gz.css&quot; rel=&quot;stylesheet&quot; /&gt;
                &lt;link rel=&quot;shortcut icon&quot; href=&quot;/favicon.ico&quot;&gt;
                &lt;/head&gt;
                &lt;body itemscope=&quot;itemscope&quot; itemtype=&quot;http://schema.org/WebPage&quot;&gt;
                &lt;nav&gt;
                    {$nav}
                &lt;/nav&gt;
                &lt;main&gt;
                    &lt;div class=&quot;page&quot; id=&quot;container&quot;&gt;
                        &lt;div class=&quot;page&quot; id=&quot;content&quot;&gt;
                            &lt;div&gt;
                                {if !empty({$page_title_h1})}
                                    &lt;h1&gt;{$page_title_h1}&lt;/h1&gt;
                                {/if}
                                {$breadcrumbs}
                            &lt;/div&gt;
                            &lt;div&gt;
                                {$body}
                            &lt;/div&gt;
                            &lt;div style=&quot;clear:both;&quot;&gt;
                                
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/main&gt;
                &lt;footer&gt;
                    {$footer}
                &lt;/footer&gt;
                {$debug_footer}
                &lt;/body&gt;
                &lt;script async=&quot;async&quot; defer=&quot;defer&quot;&gt;
                    {$js}
                &lt;/script&gt;
                &lt;/html&gt;
            '
        ],
        'http_error_template' => [
            'display'       => 'HTTP Error Page Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
            'value'         => '
                &lt;div class=&quot;margin_on_top&quot;&gt;
                &lt;p&gt;
                    {$error_code} {$error_name}. Please click &lt;a href=&quot;{$full_web_url}&quot;&gt;here&lt;/a&gt; to return home.
                &lt;/p&gt;
                &lt;/div&gt;
            ',
        ],
        'nav_template' => [
            'display'       => 'Nav Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
            'value'         => '&lt;div class=&quot;page&quot; id=&quot;banner&quot;&gt;&lt;/div&gt;'

        ],
        'footer_template' => [
            'display'       => 'Footer Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
            'value'         => '&lt;div class=&quot;page&quot; id=&quot;footer&quot;&gt;&lt;/div&gt;
'
        ],
        'add_redirects' => [
            'display'       => 'Add Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'edit_redirects' => [
            'display'       => 'Edit Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'archive_redirects' => [
            'display'       => 'Archive Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'add_routes' => [
            'display'       => 'Add URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'edit_routes' => [
            'display'       => 'Edit URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'archive_routes' => [
            'display'       => 'Archive URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
            'value'         => '0'
        ],
        'log_file' => [
            'display'       => 'Log File',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Location of the system\'s log file. Use {{today}} as a variable in the filename. e.g. log-{{today}}.log will render a log file as log-2001-01-01.log on January 1st of 2001.',
            'value'         => '/tmp/gaseous-{{today}}.txt'
        ],
        'archive_users' => [
            'display'       => 'Archive Users',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to archive users',
            'value'         => '0',
        ],
        'edit_users' => [
            'display'       => 'Edit Users',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit users',
            'value'         => '0'
        ],
        'archive_roles' => [
            'display'       => 'Archive Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to archive roles',
            'value'         => '0'
        ],
        'edit_roles' => [
            'display'       => 'Edit Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit roles',
            'value'         => '0'
        ],
        'add_roles' => [
            'display'       => 'Add Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to add roles',
            'value'         => '0'
        ],
        'edit_settings' => [
            'display'       => 'Edit Settings',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit settings',
            'value'         => '0'
        ],
        'add_pages' => [
            'display'       => 'Add Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to add CMS pages',
            'value'         => '0'
        ],
        'edit_pages' => [
            'display'       => 'Edit Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to edit CMS pages',
            'value'         => '0'
        ],
        'archive_pages' => [
            'display'       => 'Archive Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to archive CMS pages',
            'value'         => '0'
        ],
        'upload_root' => [
            'display'       => 'Upload Root Directory',
            'category_key'  => 'cms',
            'role_based'    => false,
            'description'   => 'Filesystem directory for uploaded/pasted CMS files',
            'value'         => '/tmp'
        ],
        'upload_url_relative' => [
            'display'       => 'Upload URL Relative Path (relative to site URL)',
            'category_key'  => 'cms',
            'role_based'    => false,
            'description'   => 'Relative path (client-facing/browser) for files',
            'value'         => '0'
        ],
        'manage_css' => [
            'display'       => 'Upload Root Directory',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Allow site-wide management of custom CSS',
            'value'         => '0'
        ],
        'robots_txt_value' => [
            'display'       => 'robots.txt value',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => 'The site\'s top level /robots.txt output',
            'value'         => '
                User-agent: *
                Disallow:
            '
        ],
    ];


    static $properties = [
        'boolean'       => 'True or false',
        'ckeditor'      => 'Uses CK Editor to manage value',
        'codemirror'    =>'Uses CodeMirror to manage value',
    ];


    static $settings_properties = [
        'add_pages'                 => 'boolean',
        'add_redirects'             => 'boolean',
        'add_roles'                 => 'boolean',
        'add_routes'                => 'boolean',
        'archive_pages'             => 'boolean',
        'archive_redirects'         => 'boolean',
        'archive_roles'             => 'boolean',
        'archive_routes'            => 'boolean',
        'archive_users'             => 'boolean',
        'edit_pages'                => 'boolean',
        'edit_redirects'            => 'boolean',
        'edit_roles'                => 'boolean',
        'edit_routes'               => 'boolean',
        'edit_settings'             => 'boolean',
        'edit_users'                => 'boolean',
        'enable_ssl'                => 'boolean',
        'enable_template_caching'   => 'boolean',
        'footer_template'           => 'codemirror',
        'http_error_template'       => 'codemirror',
        'main_template'             => 'codemirror',
        'maintenance_mode'          => 'boolean',
        'manage_css'                => 'boolean',
        'nav_template'              => 'codemirror',
        'pdo_debug'                 => 'boolean',
        'require_recaptcha'         => 'boolean',
        'robots_txt_value'          => 'codemirror',
        'show_debug'                => 'boolean'
    ];


    static $core_tables = [
        'current_page_iteration',
        'page',
        'page_iteration',
        'property',
        'settings',
        'settings_properties',
        'uri',
        'uri_routes',
    ];


    static $uri_routes = [
        '/register/?'                   => 'controllers/user/register.php',
        '/login/?'                      => 'controllers/user/login.php',
        '/admin/settings/?'             => 'controllers/admin/settings.php',
        '/sitemap.xml'                  => 'controllers/services/sitemap_output.php',
        '/styles.gz.css'                => 'controllers/services/css_output.php',
        '/js.gz.js'                     => 'controllers/services/js_output.php',
        '/robots.txt'                   => 'controllers/services/robots.txt.php',
        '/css-preview-check/?'          => 'controllers/services/css_preview_check.php',
        '/js-preview-check/?'           => 'controllers/services/js_preview_check.php',
        '/logout/?'                     => 'controllers/user/logout.php',
        '/img/(.*)'                     => 'controllers/services/images.php?src=$1',
        '/register/([\\w]+)/?'          => 'controllers/user/register.php?access_code=$1',
        '/admin/?'                      => 'controllers/admin/index.php',
        '/admin/css/?'                  => 'controllers/admin/css.php',
        '/admin/roles/?'                => 'controllers/admin/roles.php',
        '/admin/routes/?'               => 'controllers/admin/routes.php',
        '/admin/pages/?'                => 'controllers/admin/pages.php',
        '/admin/users/?'                => 'controllers/admin/users.php',
        '/admin/redirects/?'            => 'controllers/admin/redirects.php',
        '/([\\w\\/\\-]+(\\.html)?)?'    => 'controllers/cms/index.php?page=$1',
    ];


    public function __construct()
    {
    }


    public function pages()
    {

    }


    public function routes()
    {

    }


    public function settings()
    {

    }


    public function properties()
    {

    }


    public function settingsProperties()
    {

    }
}