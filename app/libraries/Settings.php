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

use Db\PdoMySql;
use Db\Query;
use User\Roles;

class Settings
{
    public $full_web_url, $relative_uri;

    public function __construct()
    {
        $this->getFullWebURL();
        $this->getRelativeUri();
    }


    /**
     * @return bool
     */
    private function getFullWebURL()
    {
        $protocol           = self::getFromDB('force_https_redirect') ? 'https:' : 'http:';
        $this->full_web_url = $protocol
            . '//'
            . trim($_SERVER['SERVER_NAME'] . '/' . self::getFromDB('web_uri'), '/')
        ;

        return true;
    }


    /**
     * @return bool
     */
    private function getRelativeUri()
    {
        $web_uri                = (string)trim(self::getFromDB('web_uri'), '/');
        $uri                    = $_SERVER['REQUEST_URI'];

        if (!empty($web_uri)) {
            $web_uri_in_request_uri = (strpos($_SERVER['REQUEST_URI'], $web_uri) !== false);

            if ($web_uri_in_request_uri) {
                $uri = str_replace('/' . $web_uri, null, $uri);
            }
        }
$this->relative_uri = $uri;

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
        $setting = self::getFromSession($key, $value);

        // if the setting isn't in the session, try querying the database directly
        if (empty($setting)) {
            $setting    = self::getFromDB($key, $value);
        }

        // if setting not stored there either, check fields in this class
        if (empty($setting)) {
            $setting    = self::getFromSelfProperty($key, $value);
        }

        return (new Expandable())->return($setting);
    }


    /**
     * @param $key
     * @param bool $value
     * @return bool|mixed
     */
    private static function getFromSession($key, $value = false)
    {
        $setting = false;

        if (!empty($_SESSION['settings'][$key])) {
            // get value by key, or get value as explicitly sought by key
            if (empty($value) || (!empty($value) && $_SESSION['settings'][$key] == $value))
                $setting = $_SESSION['settings'][$key];
        }

        return $setting;
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
    private static function getAllSelfProperties()
    {
        $settings   = new self();
        $reflect    = new ReflectionClass($settings);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $s          = [];

        foreach ($properties as $property) {
            // Exception - do NOT store the relative URI. this needs to be dynamic per page load...
            if ($property->getName() == 'relative_uri') {
                continue;
            }

            $s[$property->getName()] = $property->getValue($settings);
        }

        return $s;
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

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return self::processDbResult($result);
    }


    /**
     * @return bool
     */
    public static function cacheSettings()
    {
        if (empty($_SESSION['settings'])) {
            $self_settings  = self::getAllSelfProperties();
            $db_settings    = self::getAllFromDB(true, true);

            $_SESSION['settings'] = array_merge($self_settings, $db_settings);
        }

        return true;
    }


    /**
     * @param bool $values_only
     * @param bool $exclude_role_based_settings
     * @return bool|mixed
     */
    public static function getAllFromDB($values_only = false, $exclude_role_based_settings = false)
    {
        $sql = "
            SELECT *, COALESCE(s.display, s.key) AS key_display
            FROM settings AS s
            INNER JOIN settings_values AS sv
              ON s.key = sv.settings_key
            WHERE s.archived='0'
            AND sv.archived = '0'
        ";

        $bind = [];

        if ($exclude_role_based_settings === true) {
            $sql .= "
                AND s.role_based = ?
            ";

            $bind[] = 'false';
        }

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        return (new Expandable())->return(self::processDbResults($results, $values_only));
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

        $db = new Query($sql, [$setting_key]);

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

                $processed_results[] = $processed_result;
            } else {
                $processed_results[$result['key']] = self::processDbResult($result);
            }
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

        $db = new Query($sql, [$setting_key]);

        return $db->fetchAll();
    }


    /**
     * @return array|bool
     */
    public function getSettingCategories()
    {
        $sql = "
            SELECT DISTINCT category_key AS `key`, sc.category
            FROM settings AS s
            LEFT JOIN settings_categories AS sc
              ON s.category_key = sc.key
            WHERE
              ((category_key IS NULL AND sc.key IS NULL)
                OR (category_key IS NOT NULL AND sc.key IS NOT NULL))
            AND
              ((sc.archived = '0'
                OR sc.archived IS NULL));
        ";

        $db = new Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param array $settings_data
     * @return bool
     * @throws Exception
     */
    public function update(array $settings_data)
    {
        self::editSettingsCheck();

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            //self::updateSettingsValuesTable($transaction, $settings_data);
            if (self::archiveSettingValue($transaction, $settings_data['key']))
                self::insertSettingValue($transaction, $settings_data['key'], $settings_data['value']);

            // archive/re-add settings roles
            if (self::archiveSettingsRoles($transaction, $settings_data['key']) && !empty($settings_data['settings_roles']))
                self::insertSettingsRoles($transaction, $settings_data['key'], $settings_data['settings_roles']);

        } catch (Exception $e) {
            $transaction->rollBack();

            Log::app($e->getTraceAsString(), $e->getMessage());

            throw $e;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @throws Exception
     */
    public static function editSettingsCheck()
    {
        if (!Settings::value('edit_settings'))
            throw new Exception('Not allowed to edit settings');
    }


    /**
     * @param PdoMySql $transaction
     * @param string $key
     * @return bool
     * @throws Exception
     */
    private function archiveSettingsRoles(PdoMySql $transaction, $key)
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
     * @param PdoMySql $transaction
     * @param string $key
     * @return bool
     * @throws Exception
     */
    private function archiveSettingValue(PdoMySql $transaction, $key)
    {
        self::editSettingsCheck();

        $sql = "
            UPDATE settings_values
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
     * @param PdoMySql $transaction
     * @param string $key
     * @param array $settings_roles
     * @return bool
     * @throws Exception
     */
    private function insertSettingsRoles(PdoMySql $transaction, $key, $settings_roles = array())
    {
        self::editSettingsCheck();

        $roles      = new Roles();
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
     * @param PdoMySql $transaction
     * @param string $key
     * @param array $settings_roles
     * @return bool
     * @throws Exception
     */
    private function insertSettingValue(PdoMySql $transaction, $key, $value)
    {
        self::editSettingsCheck();

        $sql = "
          INSERT INTO settings_values (settings_key, `value`)
          VALUES (?, ?);
        ";

        $bind = [
            $key,
            $value,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public static function environmentIni($value)
    {
        $environment_ini_file = ENVIRONMENT_INI;

        if (is_file($environment_ini_file) && is_readable($environment_ini_file)) {
            $parsed_environment_ini_file    = parse_ini_file($environment_ini_file, true);
            $this_section                   = $parsed_environment_ini_file[ENVIRONMENT] ?? null;

            if (empty($this_section)) {
                if (PHP_SAPI == 'cli') {
                    throw new Exception(
                        'Since you are running this script via CLI, you\'ll need to pass in the environment name as the first argument'
                    );
                } else {
                    if (empty($_SESSION['setup_mode'])) {
                        trigger_error('Empty or missing ' . $_SERVER['ENVIRONMENT'] . ' section in ' . $environment_ini_file, E_USER_WARNING);
                    }

                    return false;
                }
            }
        } else {
            if (empty($_SESSION['setup_mode'])) {
                trigger_error('Missing ' . ENVIRONMENT_INI, E_USER_WARNING);
                exit;
            }

            return false;
        }

        return $this_section[$value];
    }
}