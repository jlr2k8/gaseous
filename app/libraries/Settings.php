<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * Settings.php
 *
 * Site configuration
 *
 **/

class Settings
{
    public $full_web_url;

    public static $settings = [
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
                &lt;link rel=&quot;shortcut icon&quot; href=&quot;https://www.joshlrogers.com/assets/img/favicon.ico&quot;&gt;
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


    public static $properties = [
        'boolean'       => 'True or false',
        'ckeditor'      => 'Uses CK Editor to manage value',
        'codemirror'    =>'Uses CodeMirror to manage value',
    ];


    /*
     * TODO - put values into their own sub-array. Someday, the default settings could have multiple properties.
     * e.g.
     * 'add_pages'  => [
     *     'boolean',
     *     'some-other-property',
     *  ]
     */

    public static $settings_properties = [
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


    public static $core_tables = [
        'current_page_iteration',
        'page',
        'page_iteration',
        'property',
        'settings',
        'settings_properties',
        'uri',
        'uri_routes',
    ];

    public function __construct()
    {
        $this->getFullWebURL();
    }


    /***
     * TODO (next three functions)
     * Using the arrays above, a script can be written to replace any settings, properties, settings_properties, or settings_values that may have been removed from the database.
     ***/

    public function resetSettings()
    {

    }


    public function resetProperties()
    {

    }


    public function resetSettingsProperties()
    {

    }


    /**
     * @return bool
     */
    private function getFullWebURL()
    {
        $protocol           = self::getFromDB('enable_ssl') ? 'https:' : 'http:';
        $this->full_web_url = $protocol . self::getFromDB('web_url');

        return true;
    }


    /**
     * Coalesce setting key values from the db, then (this) class' properties.
     *
     * @param $key
     * @param bool $value
     * @return array|bool
     */
    public static function value($key, $value = false)
    {
        $setting_from_db     = self::getFromDB($key, $value);
        $setting_from_class  = self::getFromSelfProperty($key, $value);

        return !empty($setting_from_db) ? $setting_from_db : $setting_from_class;
    }


    /**
     * Coalesce setting key values from the db, then (this) class' properties.
     *
     * @param $username
     * @param $key
     * @param bool $value
     * @return array|bool
     */
    public static function valueByUsername($username, $key, $value = false)
    {
        $setting_from_db     = self::getFromDB($key, $value, $username);
        $setting_from_class  = self::getFromSelfProperty($key, $value);

        return !empty($setting_from_db) ? $setting_from_db : $setting_from_class;
    }


    /**
     * @param $key
     * @param bool $value
     * @return bool
     */
    private static function getFromSelfProperty($key, $value = false)
    {
        $setting = new self();

        if (!empty($setting->$key)) {
            // get value by key, or get value as explicitly sought by key
            if (!$value || ($value && $setting->$key == $value))
                return $setting->$key;
        }

        return false;
    }


    /**
     * @return array
     * @throws ReflectionException
     */
    protected static function getAllSelfProperties()
    {
        $setting    = new self();
        $reflect    = new \ReflectionClass($setting);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $return     = [];

        foreach ($properties as $property) {
            $return[$property->getName()] = $property->getValue($setting);
        }

        return $return;
    }


    /**
     * Pass in a key to get the value, pass in key/value to get expected value
     *
     * @param $key
     * @param bool $value
     * @param string $username
     * @return bool|mixed
     */
    protected static function getFromDB($key, $value = false, $username = null)
    {
        $username = $username ?? $_SESSION['account']['username'] ?? false;

        $sql = "
            SELECT *, COALESCE(s.display, s.key) AS key_display
            FROM settings AS s
            INNER JOIN settings_values AS sv
              ON s.key = sv.settings_key
            LEFT JOIN settings_roles AS sr
              ON s.key = sr.settings_key
            LEFT JOIN account_roles AS ar
              ON sr.role_name = ar.role_name
            LEFT JOIN account AS a
              ON ar.account_username = a.username
            WHERE s.key = ?
            AND s.archived='0'
        ";

        $bind[] = $key;

        if ($username) {
            $sql .= "
               AND (
                    (s.role_based = 'true' AND sr.role_name IN (SELECT role_name FROM account_roles WHERE account_username = ? AND archived = '0') AND sr.archived != '1' AND ar.archived != '1')
                    OR
                    (s.role_based != 'true')
                )
            ";

            $bind[] = $username;
        } else {
            $sql .= " AND (sr.role_name IS NULL OR sr.archived = '1') AND s.role_based = 'false'";
        }

        if ($value) {
            $sql .= " AND sv.value = ? ";

            $bind[] = $value;
        }

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();

        return self::processDbResult($result);
    }


    /**
     * @return bool
     * @throws ErrorException
     */
    public static function checkCoreTables()
    {
        $core_tables        = "'" . implode("','", self::$core_tables) . "'";
        $count_core_tables  = (int)count(self::$core_tables);

        $sql = "
            SELECT COUNT(*)
            FROM
                information_schema.TABLES
            WHERE
                table_schema = ?
            AND
                table_name IN($core_tables);
        ";

        $bind = [
            \Settings::environmentIni('mysql_database'),
        ];

        $db     = new \Db\Query($sql, $bind);
        $result = (int)$db->fetch();

        return ($result == $count_core_tables);
    }


    /**
     * @param bool $values_only
     * @return bool|mixed
     */
    public static function getAllFromDB($values_only = false)
    {
        $sql = "
            SELECT *, COALESCE(s.display, s.key) AS key_display
            FROM settings AS s
            INNER JOIN settings_values AS sv
              ON s.key = sv.settings_key
            WHERE s.archived='0'
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        return self::processDbResults($results, $values_only);
    }


    /**
     * @param $result
     * @return bool|mixed
     */
    protected static function processDbResult($result)
    {
        if (empty($result))
            return false;

        // first, try to determine if match is some type of boolean (ie. 1, true, yes, on)
        $boolean_value = filter_var($result['value'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return is_null($boolean_value) ? $result['value'] : $boolean_value;
    }


    /**
     * @param $setting_key
     * @return array
     */
    public static function getSettingRoles($setting_key)
    {
        $sql = "
            SELECT role_name
            FROM settings_roles
            WHERE settings_key = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$setting_key]);

        return $db->fetchAll();
    }


    /**
     * @param array $results
     * @param bool $values_only
     * @return bool|mixed
     */
    protected static function processDbResults(array $results, $values_only = false)
    {
        $processed_results = [];

        foreach ($results as $result) {

            if (!$values_only) {

                foreach ($result as $col => $val) {
                    $processed_result[$col]  = $val;
                }

                $processed_result['value']      = self::processDbResult($result);
                $processed_result['properties'] = self::getSettingProperties($processed_result['key']);
                $processed_result['roles']      = self::getSettingRoles($processed_result['key']);

            } else {
                $processed_result = self::processDbResult($result);
            }

            $processed_results[] = $processed_result;
        }

        return $processed_results;
    }


    /**
     * @param $setting_key
     * @return array
     */
    protected static function getSettingProperties($setting_key)
    {
        $sql = "
            SELECT property
            FROM settings_properties
            WHERE settings_key = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$setting_key]);

        return $db->fetchAll();
    }


    /**
     * @return array|bool
     */
    public function getSettingCategories()
    {
        $sql = "
            SELECT DISTINCT category_key AS `key`, c.category
            FROM settings AS s
            LEFT JOIN category AS c
              ON s.category_key = c.key
            WHERE
              ((category_key IS NULL AND c.key IS NULL)
                OR (category_key IS NOT NULL AND c.key IS NOT NULL))
            AND
              ((c.archived = '0'
                OR c.archived IS NULL));
        ";

        $db = new \Db\Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param array $settings_data
     * @return bool
     * @throws \Exception
     */
    public function update(array $settings_data)
    {
        self::editSettingsCheck();

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {

            self::updateSettingsValuesTable($transaction, $settings_data);

            // archive/re-add settings roles
            if (self::archiveSettingsRoles($transaction, $settings_data['key']) && !empty($settings_data['settings_roles']))
                self::insertSettingsRoles($transaction, $settings_data['key'], $settings_data['settings_roles']);

        } catch (\Exception $e) {

            $transaction->rollBack();

            error_log($e->getMessage() . ' ' . $e->getTraceAsString());

            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * @throws \Exception
     */
    public static function editSettingsCheck()
    {
        if (!\Settings::value('edit_settings'))
            throw new \Exception('Not allowed to edit settings');
    }

    /**
     * @param \Db\PdoMySql $transaction
     * @param array $settings_data
     * @return bool
     * @throws \Exception
     */
    private function updateSettingsValuesTable(\Db\PdoMySql $transaction, array $settings_data)
    {
        self::editSettingsCheck();

        $sql = "
            UPDATE settings_values
            SET
              value = ?
            WHERE settings_key = ?
        ";

        $bind = [
            $settings_data['value'],
            $settings_data['key'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param string $key
     * @return bool
     * @throws \Exception
     */
    private function archiveSettingsRoles(\Db\PdoMySql $transaction, $key)
    {
        self::editSettingsCheck();

        $sql = "
            UPDATE settings_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE settings_key = ?
            AND archived = '0'
        ";

        $bind = [$key];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param string $key
     * @param array $settings_roles
     * @return bool
     * @throws \Exception
     */
    private function insertSettingsRoles(\Db\PdoMySql $transaction, $key, $settings_roles = array())
    {
        self::editSettingsCheck();

        $roles      = new \User\Roles();
        $all_roles  = $roles->getAll();

        foreach ($all_roles as $role) {

            $sql    = null;
            $bind   = [];

            if (in_array($role['role_name'], $settings_roles)) {

                $sql .= "
                  INSERT INTO settings_roles (settings_key, role_name)
                  VALUES (?, ?);
                ";

                $bind[] = $key;
                $bind[] = $role['role_name'];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        }

        return true;
    }


    /**
     * @param $value
     * @return mixed
     * @throws ErrorException
     */
    public static function environmentIni($value)
    {
        $environment_ini_file = $_SERVER['WEB_ROOT'] . '/setup/environment.ini';

        if (is_file($environment_ini_file) && is_readable($environment_ini_file)) {
            $parsed_environment_ini_file    = parse_ini_file($environment_ini_file, true);
            $this_section                   = $parsed_environment_ini_file[ENVIRONMENT];

            if (empty($this_section)) {
                if (PHP_SAPI == 'cli') {
                    throw new \ErrorException(
                        'Since you are running this script via CLI, you\'ll need to pass in the environment name as the first argument'
                    );
                } else {
                    throw new \ErrorException(
                        'Empty or missing ' . $_SERVER['ENVIRONMENT'] . ' section in ' . $environment_ini_file
                    );
                }
            }
        } else {
            throw new \ErrorException(
                'Missing '
                . $environment_ini_file
                . '. Please create that file and an array section that matches your Apache ENVIRONMENT directive.'
            );
        }

        return $this_section[$value];
    }
}