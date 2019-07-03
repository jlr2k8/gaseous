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
        ],
        'cookie_domain' => [
            'display'       => 'Cookie domain',
            'category_key'  => 'cookie',
            'role_based'    => false,
            'description'   => '',
        ],
        'login_cookie_expire_days' => [
            'display'       => 'Expiration for login cookies',
            'category_key'  => 'cookie',
            'role_based'    => false,
            'description'   => '',
        ],
        'enable_template_caching' => [
            'display'       => 'Enable Template Caching',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
        ],
        'enable_ssl' => [
            'display'       => 'Enable SSL',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
        ],
        'recaptcha_public_key' => [
            'display'       => 'ReCaptcha Public Key',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => '',
        ],
        'recaptcha_private_key' => [
            'display'       => 'ReCaptcha Private Key',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => '',
        ],
        'require_recaptcha' => [
            'display'       => 'Require ReCaptcha',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => 'Any page that has a ReCaptcha, make the validation required.',
        ],
        'maintenance_mode' => [
            'display'       => 'Maintenance Mode',
            'category_key'  => 'recaptcha',
            'role_based'    => false,
            'description'   => 'When enabled, this will set the HTTP 503 header and prevent every page from being displayed, instead, showing a temporary site downtime on every URI. Search engines, if caching the site during this time, will simply receive this as a temporary downtime then return later. This mode is especially useful when the site\'s software is undergoing updates or the site is intentionally being maintained.',
        ],
        'show_debug' => [
            'display'       => 'Show Debug Footer',
            'category_key'  => 'development',
            'role_based'    => true,
            'description'   => 'Per role, this allows an app-level troubleshooting footer (below the site\'s footer) to display information such as page load time, server globals and session globals.',
        ],
        'smtp_host' => [
            'display'       => 'SMTP Server Hostname/IP',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
        ],
        'smtp_port' => [
            'display'       => 'SMTP Host\'s Port',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
        ],
        'smtp_user' => [
            'display'       => 'SMTP User',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
        ],
        'smtp_password' => [
            'display'       => 'SMTP Password',
            'category_key'  => 'email',
            'role_based'    => false,
            'description'   => '',
        ],
        'registration_access_code' => [
            'display'       => 'Registration Access Code',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => '',
        ],
        'main_template' => [
            'display'       => 'Main Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
        ],
        'http_error_template' => [
            'display'       => 'HTTP Error Page Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
        ],
        'nav_template' => [
            'display'       => 'Nav Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
        ],
        'footer_template' => [
            'display'       => 'Footer Template',
            'category_key'  => 'templates',
            'role_based'    => false,
            'description'   => '',
        ],
        'add_redirects' => [
            'display'       => 'Add Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'edit_redirects' => [
            'display'       => 'Edit Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'archive_redirects' => [
            'display'       => 'Archive Redirects',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'add_routes' => [
            'display'       => 'Add URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'edit_routes' => [
            'display'       => 'Edit URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'archive_routes' => [
            'display'       => 'Archive URI Routes',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => '',
        ],
        'log_file' => [
            'display'       => 'Log File',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Location of the system\'s log file. Use {{today}} as a variable in the filename. e.g. log-{{today}}.log will render a log file as log-2001-01-01.log on January 1st of 2001.',
        ],
        'archive_users' => [
            'display'       => 'Archive Users',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to archive users',
        ],
        'edit_users' => [
            'display'       => 'Edit Users',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit users',
        ],
        'archive_roles' => [
            'display'       => 'Archive Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to archive roles',
        ],
        'edit_roles' => [
            'display'       => 'Edit Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit roles',
        ],
        'add_roles' => [
            'display'       => 'Add Roles',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to add roles',
        ],
        'edit_settings' => [
            'display'       => 'Edit Settings',
            'category_key'  => 'administrative',
            'role_based'    => true,
            'description'   => 'Ability to edit settings',
        ],
        'add_pages' => [
            'display'       => 'Add Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to add CMS pages',
        ],
        'edit_pages' => [
            'display'       => 'Edit Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to edit CMS pages',
        ],
        'archive_pages' => [
            'display'       => 'Archive Pages',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Ability to archive CMS pages',
        ],
        'upload_root' => [
            'display'       => 'Upload Root Directory',
            'category_key'  => 'cms',
            'role_based'    => false,
            'description'   => 'Filesystem directory for uploaded/pasted CMS files',
        ],
        'upload_url_relative' => [
            'display'       => 'Upload URL Relative Path (relative to site URL)',
            'category_key'  => 'cms',
            'role_based'    => false,
            'description'   => 'Relative path (client-facing/browser) for files',
        ],
        'manage_css' => [
            'display'       => 'Upload Root Directory',
            'category_key'  => 'cms',
            'role_based'    => true,
            'description'   => 'Allow site-wide management of custom CSS',
        ],
        'robots_txt_value' => [
            'display'       => 'robots.txt value',
            'category_key'  => 'administrative',
            'role_based'    => false,
            'description'   => 'The site\'s top level /robots.txt output',
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
        'add_redirects'             => 'boolean' ,
        'add_roles'                 => 'boolean' ,
        'add_routes'                => 'boolean' ,
        'archive_pages'             => 'boolean' ,
        'archive_redirects'         => 'boolean' ,
        'archive_roles'             => 'boolean' ,
        'archive_routes'            => 'boolean' ,
        'archive_users'             => 'boolean' ,
        'edit_pages'                => 'boolean' ,
        'edit_redirects'            => 'boolean' ,
        'edit_roles'                => 'boolean' ,
        'edit_routes'               => 'boolean' ,
        'edit_settings'             => 'boolean' ,
        'edit_users'                => 'boolean' ,
        'enable_ssl'                => 'boolean' ,
        'enable_template_caching'   => 'boolean' ,
        'footer_template'           => 'codemirror' ,
        'http_error_template'       => 'codemirror' ,
        'main_template'             => 'codemirror' ,
        'maintenance_mode'          => 'boolean' ,
        'manage_css'                => 'boolean' ,
        'manage_menu'               => 'boolean' ,
        'nav_template'              => 'codemirror' ,
        'pdo_debug'                 => 'boolean' ,
        'require_recaptcha'         => 'boolean' ,
        'robots_txt_value'          => 'codemirror' ,
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
     * Using the arrays above, a script can be written to replace any settings, properties or settings_properties that may have been removed from the database.
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