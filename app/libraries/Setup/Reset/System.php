<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/30/20
 *
 * Settings.php
 *
 * Setting defaults
 *
 **/

namespace Setup\Reset;

use Db\PdoMySql;
use PDOException;

class System
{
    const ADMIN_ROLE = 'admin';

    static $properties = [
        'boolean'                           => 'True or False',
        'ckeditor'                          => 'CK Editor to manage setting value content with wyswyg interface',
        'codemirror'                        => 'CodeMirror to manage setting value content with wyswyg interface',
        'file_upload_allowed_extensions'    => 'Allowed list (comma-separated) of allowed extensions for file uploads',
        'file_upload_allowed_size'          => 'Size range for file upload (range comma-separated in bytes)',
    ];

    static $roles = [
        self::ADMIN_ROLE    => 'Administrator',
        'basic'             => 'Basic User',
    ];

    static $uri_routes = [
        '/register/?'                           => 'controllers/user/register.php',
        '/login/?'                              => 'controllers/user/login.php',
        '/admin/settings/?'                     => 'controllers/admin/settings.php',
        '/sitemap.xml'                          => 'controllers/services/sitemap_output.php',
        '/styles(\\-([\\w]{32}))?.gz.css'       => 'controllers/services/css_output.php?iteration=$2',
        '/js(\\-([\\w]{32}))?.gz.js'            => 'controllers/services/js_output.php?iteration=$2',
        '/robots.txt'                           => 'controllers/services/robots.txt.php',
        '/logout/?'                             => 'controllers/user/logout.php',
        '/img/(.*)'                             => 'controllers/services/images.php?src=$1',
        '/assets/img/(.*)'                      => 'controllers/services/images.php?src=$1',
        '/files/(.*)'                           => 'controllers/services/files.php?src=$1',
        '/admin/?'                              => 'controllers/admin/index.php',
        '/admin/css/?'                          => 'controllers/admin/css.php',
        '/admin/js/?'                           => 'controllers/admin/js.php',
        '/admin/roles/?'                        => 'controllers/admin/roles.php',
        '/admin/routes/?'                       => 'controllers/admin/routes.php',
        '/admin/content(/(.*))?'                => 'controllers/admin/content.php?content_body_type_id=$2',
        '/admin/users/?'                        => 'controllers/admin/users.php',
        '/admin/redirects/?'                    => 'controllers/admin/redirects.php',
        '/admin/menu/?'                         => 'controllers/admin/menu.php',
        '([\w\/\-]+)/?'                         => 'controllers/cms/index.php?page=$1',
    ];

    static $settings_categories = [
        'email' => [
            'category'          => 'Email',
            'description'       => 'Email and webmaster configuration',
        ],
        'administrative' => [
            'category'          => 'Administrative',
            'description'       => 'Core site functionality and configuration settings',
        ],
        'templates' => [
            'category'          => 'Templates',
            'description'       => 'Major runtime views, such as nav, footer and main html template',
        ],
        'cookie' => [
            'category'          => 'Cookies',
            'description'       => 'Site cookie handling',
        ],
        'development' => [
            'category'          => 'Development',
            'description'       => 'Debugging, reporting and troubleshooting options',
        ],
        'filesystem' => [
            'category'          => 'Filesystem',
            'description'       => 'Directory and file handling for uploaded files and images'
        ],
    ];

    static $settings = [
        'smtp_host' => [
            'display'       => 'SMTP Server Hostname/IP',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => '',
            'value'         => 'localhost',
            'properties'    => [],
        ],
        'smtp_port' => [
            'display'       => 'SMTP Host\'s Port',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '25',
            'properties'    => [],
        ],
        'smtp_user' => [
            'display'       => 'SMTP User',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '',
            'properties'    => [],
        ],
        'smtp_password' => [
            'display'       => 'SMTP Password',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '',
            'properties'    => [],
        ],
        'webmaster_name' => [
            'display'       => 'Webmaster Name',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => 'Technical contact\'s name for the site',
            'value'         => '',
            'properties'    => [],
        ],
        'webmaster_email' => [
            'display'       => 'Webmaster Email',
            'category_key'  => 'email',
            'role_based'    => '0',
            'description'   => 'Technical contact email address for the site',
            'value'         => '',
            'properties'    => [],
        ],



        'require_recaptcha' => [
            'display'       => 'Require ReCaptcha v3',
            'category_key'  => 'recaptcha',
            'role_based'    => '0',
            'description'   => 'Require ReCaptcha on common user submission pages, such as login',
            'value'         => '0',
            'properties'    => [
                'boolean',
            ],
        ],
        'recaptcha_public_key' => [
            'display'       => 'ReCaptcha v3 Public Key',
            'category_key'  => 'recaptcha',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '',
            'properties'    => [],
        ],
        'recaptcha_private_key' => [
            'display'       => 'ReCaptcha v3 Private Key',
            'category_key'  => 'recaptcha',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '',
            'properties'    => [],
        ],
        'site_title' =>    [
            'display'       => 'Site Title',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => '',
            'value'         => 'Gaseous Content Management System',
            'properties'    => [],
        ],
        'web_url' =>    [
            'display'       => 'Web URL',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => '',
            'value'         => 'localhost',
            'properties'    => [],
        ],
        'force_https_redirect' => [
            'display'       => 'Force HTTPS redirect',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '0',
            'properties'    => [
                'boolean',
            ],
        ],
        'maintenance_mode' => [
            'display'       => 'Maintenance Mode',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => 'When enabled, this will set the HTTP 503 header and prevent every page from being displayed, instead, showing a temporary site downtime on every URI. Search engines, if caching the site during this time, will simply receive this as a temporary downtime then return later. This mode is especially useful when the site\'s software is undergoing updates or the site is intentionally being maintained.',
            'value'         => '0',
            'properties'    => [
                'boolean',
            ],
        ],
        'output_iterative_css_inline_html' => [
            'display'       => 'Output CSS iterator content in HTML style tag',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => 'If set to true, output the CSS directly in the HTML within a style tag. Otherwise, the CSS is compressed and served as a gzipped file with Not Modified headers via a HTML link element.',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'output_iterative_js_inline_html' => [
            'display'       => 'Output JS iterator content in HTML script tag',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => 'If set to true, output the javascript directly in the HTML within a script tag. Otherwise, the javascript is compressed and served as a gzipped file with Not Modified headers via a HTML script element\'s src attribute.',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'cookie_domain' => [
            'display'       => 'Cookie domain',
            'category_key'  => 'cookie',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '.localhost',
            'properties'    => [],
        ],
        'login_cookie_expire_days' => [
            'display'       => 'Expiration for login cookies',
            'category_key'  => 'cookie',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '7',
            'properties'    => [],
        ],
        'add_routes' => [
            'display'       => 'Add URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'edit_routes' => [
            'display'       => 'Edit URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'archive_routes' => [
            'display'       => 'Archive URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'add_redirects' => [
            'display'       => 'Add Redirects',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'edit_redirects' => [
            'display'       => 'Edit Redirects',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'archive_redirects' => [
            'display'       => 'Archive Redirects',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => '',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'archive_users' => [
            'display'       => 'Archive Users',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to archive users',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'edit_users' => [
            'display'       => 'Edit Users',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to edit users',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'archive_roles' => [
            'display'       => 'Archive Roles',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to archive roles',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'edit_roles' => [
            'display'       => 'Edit Roles',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to edit roles',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'add_roles' => [
            'display'       => 'Add Roles',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to add roles',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'edit_settings' => [
            'display'       => 'Edit Settings',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to edit settings',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'manage_menu' => [
            'display'       => 'Edit Site Menu',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to edit the websites\'s navigation menu',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'manage_css' => [
            'display'       => 'Manage CSS',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Allow site-wide management of custom CSS',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'manage_js' => [
            'display'       => 'Manage JS',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Allow site-wide management of custom JS',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'add_content' => [
            'display'       => 'Add Content',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to add CMS content',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],

        'edit_content' => [
            'display'       => 'Edit Content',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to edit CMS content',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'archive_content' => [
            'display'       => 'Archive Content',
            'category_key'  => 'administrative',
            'role_based'    => '1',
            'description'   => 'Ability to archive CMS content',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],
        'registration_access_code' => [
            'display'       => 'Registration Access Code',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '',
            'properties'    => [
            ],
        ],
        'robots_txt_value' => [
            'display'       => 'robots.txt value',
            'category_key'  => 'administrative',
            'role_based'    => '0',
            'description'   => 'The site\'s top level /robots.txt output',
            'value'         => '
                    User-agent: *
                    Disallow:
                ',
            'properties'    => [
                'codemirror',
            ],
        ],



        'show_debug' => [
            'display'       => 'Show Debug Footer',
            'category_key'  => 'development',
            'role_based'    => '1',
            'description'   => 'Per role, this allows an app-level troubleshooting footer (below the site\'s footer) to display information such as page load time, server globals and session globals.',
            'value'         => '1',
            'properties'    => [
                'boolean',
            ],
        ],



        'main_template' => [
            'display'       => 'Main Template',
            'category_key'  => 'templates',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '
                    &lt;!DOCTYPE html&gt;&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot; itemscope=&quot;itemscope&quot; itemtype=&quot;http://schema.org/WebPage&quot;&gt;
                    &lt;head&gt;
                        {$css_output}
                        {$css_iterator_output}
                        &lt;meta itemprop=&quot;description&quot; name=&quot;description&quot; content=&quot;{$meta_description}&quot; /&gt;
                        &lt;meta charset=&quot;UTF-8&quot; /&gt;
                        &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot; /&gt;
                        &lt;meta name=&quot;robots&quot; content=&quot;{$meta_robots}&quot; /&gt;
                        &lt;title itemprop=&quot;name&quot;&gt;{$page_title_seo}&lt;/title&gt;
                        &lt;link rel=&quot;shortcut icon&quot; href=&quot;/assets/img/favicon.ico&quot;&gt;
                    &lt;/head&gt;
                    &lt;body&gt;
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
                    
                                    {if !empty($site_announcements)}    
                                        &lt;div class=&quot;site_dialog&quot; title=&quot;Notifications&quot;&gt;
                                            &lt;ul&gt;
                                                {foreach key=key from=$site_announcements item=sa}
                                                    &lt;li&gt;{$sa}&lt;/li&gt;
                                                {/foreach}
                                            &lt;/ul&gt;
                                        &lt;/div&gt;
                                        {include file="common/dialog.tpl"}
                                    {/if}
                                &lt;/div&gt;
                                &lt;div style=&quot;clear:both;&quot;&gt;
                                    
                                &lt;/div&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/main&gt;
                    &lt;footer&gt;
                        {$footer}
                        {$administration}
                    &lt;/footer&gt;
                    {$debug_footer}
                    &lt;/body&gt;
                        {$js_output}
                        {$js_iterator_output}
                    &lt;/html&gt;
            ',
            'properties'    => [
                'codemirror',
            ],
        ],
        'http_error_template' => [
            'display'       => 'HTTP Error Page Template',
            'category_key'  => 'templates',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '
                &lt;div class=&quot;margin_on_top&quot;&gt;
                    &lt;p&gt;
                        {$error_code} {$error_name}. Please click &lt;a href=&quot;{$full_web_url}&quot;&gt;here&lt;/a&gt; to return home.
                    &lt;/p&gt;
                &lt;/div&gt;
            ',
            'properties'    => [
                'codemirror',
            ],
        ],
        'nav_template' => [
            'display'       => 'Nav Template',
            'category_key'  => 'templates',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '
                &lt;div class=&quot;page&quot; id=&quot;banner&quot;&gt;
                    &lt;div class=&quot;page&quot; id=&quot;menu_container&quot;&gt;
                        {$menu}
                    &lt;/div&gt;
                    &lt;div id=&quot;logo&quot;&gt;&lt;img src=&quot;{$full_web_url}/assets/img/gaseous.png&quot; alt=&quot;Gaseous logo&quot; /&gt;&lt;/div&gt;
                &lt;/div&gt;
            ',
            'properties'    => [
                'codemirror',
            ],
        ],
        'footer_template' => [
            'display'       => 'Footer Template',
            'category_key'  => 'templates',
            'role_based'    => '0',
            'description'   => '',
            'value'         => '
                &lt;div&gt;
                    &lt;div class=&quot;page&quot; id=&quot;footer&quot;&gt;
                        &amp;copy; {date(\'Y\')} Josh L. Rogers. All Rights Reserved.
                    &lt;/div&gt;
                &lt;/div&gt;
            ',
            'properties'    => [
                'codemirror',
            ],
        ],



        'upload_root' => [
            'display'       => 'Upload Root Directory',
            'category_key'  => 'filesystem',
            'role_based'    => '0',
            'description'   => 'Filesystem directory for uploaded/pasted CMS files',
            'value'         => '/tmp',
            'properties'    => [],
        ],
        'upload_image_relative' => [
            'display'       => 'Upload image URL Relative Path',
            'category_key'  => 'filesystem',
            'role_based'    => '0',
            'description'   => 'Relative path (client-facing/browser) for images. Should match the route to images service.',
            'value'         => '/img',
            'properties'    => [],
        ],
        'upload_file_relative' => [
            'display'       => 'Upload file URL Relative Path',
            'category_key'  => 'filesystem',
            'role_based'    => '0',
            'description'   => 'Relative path (client-facing/browser) for uploaded file attachments. Should match the route to files…',
            'value'         => '/files',
            'properties'    => [],
        ],
        'log_file' => [
            'display'       => 'Log File',
            'category_key'  => 'filesystem',
            'role_based'    => '0',
            'description'   => 'Location of the system\'s log file. Use {{today}} as a variable in the filename. e.g. log-{{today}}.log will render a log file as log-2001-01-01.log on January 1st of 2001.',
            'value'         => '/var/log/gaseous-log-{{today}}.log',
            'properties'    => [],
        ],
        'template_compile_dir' => [
            'display'       => 'Template Compile Directory',
            'category_key'  => 'filesystem',
            'role_based'    => '0',
            'description'   => 'Location of the Smarty template compilation',
            'value'         => '/tmp',
            'properties'    => [],
        ],
    ];


    /**
     * @return string
     */
    public static function form()
    {
        $form = '
            <form method="post" action="?system">
                <h1>Basic Settings</h1>
                <p>
                    There are various configuration components for the site. Let\'s start with the basics.
                </p>
                <div>
                    <h2>
                        Site Info
                    </h2>
                    <div>
                        <label>
                            Site Title:
                        </label><br />
                        <input type="text" name="site_title" id="site_title" placeholder="Name of this site, such as organization name" value="' . ($_POST['site_title'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Web URL:
                        </label><br />
                        http(s)://<input type="text" name="web_url" id="web_url" placeholder="Site domain" value="' . ($_POST['web_url'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                </div>
                <div>
                    <h2>
                        Email
                    </h2>
                    <div>
                        <label>
                            SMTP Host:
                        </label><br />
                        <input type="text" name="smtp_host" id="smtp_host" placeholder="Domain name or IP address for SMTP server" value="' . ($_POST['smtp_host'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            SMTP Username:
                        </label><br />
                        <input type="text" name="smtp_user" id="smtp_user" placeholder="User for SMTP server" value="' . ($_POST['smtp_user'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            SMTP Password:
                        </label><br />
                        <input type="text" name="smtp_password" id="smtp_password" placeholder="Password for SMTP server" value="' . ($_POST['smtp_password'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            SMTP Port:
                        </label><br />
                        <input type="text" name="smtp_port" id="smtp_port" placeholder="Port for SMTP server, usually TCP 25" value="' . ($_POST['smtp_port'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Webmaster Full Name:
                        </label><br />
                        <input type="text" name="webmaster_name" id="webmaster_name" placeholder="Primary site contact name" value="' . ($_POST['webmaster_name'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Webmaster Email Address:
                        </label><br />
                        <input type="text" name="webmaster_email" id="webmaster_email" placeholder="Primary site contact email address" value="' . ($_POST['smtp_host'] ?? filter_var($_SERVER['SERVER_ADMIN'] ?? null, FILTER_SANITIZE_EMAIL, FILTER_NULL_ON_FAILURE) ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                </div>
                <div>
                    <h2>
                        Filesystem
                    </h2>
                    <div>
                        <label>
                            Upload Root:
                        </label><br />
                        <input type="text" name="upload_root" id="upload_root" placeholder="Server file path for site uploads. Choose a location outside of the site root folder." value="' . ($_POST['upload_root'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Daily Log File (use <code>{{today}}</code> for dynamic date as part of filename):
                        </label><br />
                        <input type="text" name="log_file" id="log_file" placeholder="e.g. /var/log/gaseous-{{today}}.log" value="' . ($_POST['log_file'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Relative File URI:
                        </label><br />
                        <input type="text" name="upload_file_relative" id="upload_file_relative" placeholder="e.g. /files" value="' . ($_POST['upload_file_relative'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                    <div>
                        <label>
                            Relative Image URI:
                        </label><br />
                        <input type="text" name="upload_image_relative" id="upload_image_relative" placeholder="e.g. /img" value="' . ($_POST['upload_image_relative'] ?? null) . '" />
                    </div>
                    <p>&#160;</p>
                </div>
                <div>
                    <input type="hidden" name="setup_mode" value="' . $_SESSION['setup_mode'] . '" />
                    <input type="submit" value="Submit Basic Settings &#187;" />
                </div>
            </form>
        ';

        return $form;
    }


    /**
     * @return false|string
     */
    public static function runChangesets()
    {
        ob_start();

        echo '<h2>Database Update Status</h2>';
        echo '<textarea readonly="readonly">';

        require_once WEB_ROOT . '/../db/run-changesets.php';

        echo '</textarea>';

        return ob_get_clean();
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setProperties(PdoMySql $transaction)
    {
        try {
            foreach (self::$properties as $property => $description) {
                $sql = "
                    INSERT INTO property (
                        property,
                        description
                    ) VALUES (
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        `description` = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $property,
                    $description,
                    $description,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
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
    public function setUriRoutes(PdoMySql $transaction)
    {
        try {
            $i = (int)0;

            foreach (self::$uri_routes as $uri_pattern => $controller) {
                $i++;

                $sql = "
                    INSERT INTO uri_routes (
                        regex_pattern,
                        destination_controller,
                        priority_order
                    ) VALUES (
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        destination_controller = ?,
                        priority_order = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $uri_pattern,
                    $controller,
                    $i,
                    $controller,
                    $i,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
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
    public function setSettingCategories(PdoMySql $transaction)
    {
        try {
            foreach (self::$settings_categories as $key => $data) {
                $category       = $data['category'];
                $description    = $data['description'];

                $sql = "
                    INSERT INTO settings_categories (
                        `key`,
                        category,
                        description
                    ) VALUES (
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        category = ?,
                        description = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $key,
                    $category,
                    $description,
                    $category,
                    $description,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @param array $key_val
     * @return PdoMySql
     */
    public function setSettings(PdoMySql $transaction, array $key_val)
    {
        try {
            foreach (self::$settings as $key => $data) {
                $setting_display        = $data['display'];
                $setting_category_key   = $data['category_key'];
                $is_role_based          = $data['role_based'] ? 'true' : 'false';
                $setting_description    = $data['description'];
                $setting_value          = (string)($key_val[$key] ?? $data['value']);

                if ($key == 'web_url') {
                    $setting_value = '//' . str_replace(['//', 'http://', 'https://'], null, $setting_value);
                }

                if ($is_role_based == 'true') {
                    $sql = "
                        INSERT INTO settings_roles (
                            settings_key,
                            role_name
                        ) VALUES (
                            ?,
                            ?
                        );
                    ";

                    $bind = [
                        $key,
                        self::ADMIN_ROLE,
                    ];

                    $transaction
                        ->prepare($sql)
                        ->execute($bind);
                }

                $sql = "
                    INSERT INTO
                        settings (
                            `key`,
                            display,
                            category_key,
                            role_based,
                            description
                        ) VALUES (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?
                        ) ON DUPLICATE KEY UPDATE 
                            display = ?,
                            category_key = ?,
                            role_based = ?,
                            description = ?,
                            modified_datetime = NOW();
                ";

                $bind = [
                    $key,
                    $setting_display,
                    $setting_category_key,
                    $is_role_based,
                    $setting_description,
                    $setting_display,
                    $setting_category_key,
                    $is_role_based,
                    $setting_description
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);


                $sql = "
                    INSERT INTO
                        settings_values (
                            settings_key,
                            `value`
                        ) VALUES (
                            ?,
                            ?
                        ) ON DUPLICATE KEY UPDATE 
                            `value` = ?,
                            modified_datetime = NOW();
                ";

                $bind = [
                    $key,
                    $setting_value,
                    $setting_value,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);


                foreach ($data['properties'] as $property) {
                    $sql = "
                        INSERT INTO
                            settings_properties (
                                settings_key,
                                property
                            ) VALUES (
                                ?,
                                ?
                            ) ON DUPLICATE KEY UPDATE 
                                property = ?,
                                modified_datetime = NOW();
                    ";

                    $bind = [
                        $key,
                        $property,
                        $property,
                    ];

                    $transaction
                        ->prepare($sql)
                        ->execute($bind);
                }
            }
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
    public function setSettingsRoles(PdoMySql $transaction)
    {
        try {
            foreach (self::$roles as $role => $description) {
                $sql = "
                    INSERT INTO role (
                        role_name,
                        description
                    ) VALUES (
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE 
                        description = ?;
                ";

                $bind = [
                    $role,
                    $description,
                    $description,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }
}