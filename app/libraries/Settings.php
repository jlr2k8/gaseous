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
use Utilities\Reset;

class Settings
{
    public $full_web_url;

    public function __construct()
    {
        $this->getFullWebURL();
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
        // get setting from database
        $setting    = self::getFromDB($key, $value);

        // if setting not stored there, check fields in this class
        if (empty($setting)) {
            $setting    = self::getFromSelfProperty($key, $value);
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
     * @throws ErrorException
     */
    public static function checkCoreTables()
    {
        $core_tables_clause  = "'" . implode("','", Reset::$core_tables) . "'";
        asort(Reset::$core_tables, SORT_ASC);

        $sql = "
            SELECT
                table_name
            FROM
                information_schema.TABLES
            WHERE
                table_schema = ?
            AND
                table_name IN($core_tables_clause)
            ORDER BY table_name;
        ";

        $bind = [
            Settings::environmentIni('mysql_database'),
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAll();

        return ($results == Reset::$core_tables);
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

        $db         = new Query($sql);
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

        $db = new Query($sql, [$setting_key]);

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
            self::updateSettingsValuesTable($transaction, $settings_data);

            // archive/re-add settings roles
            if (self::archiveSettingsRoles($transaction, $settings_data['key']) && !empty($settings_data['settings_roles']))
                self::insertSettingsRoles($transaction, $settings_data['key'], $settings_data['settings_roles']);

        } catch (Exception $e) {
            $transaction->rollBack();

            error_log($e->getMessage() . ' ' . $e->getTraceAsString());

            return false;
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
     * @param array $settings_data
     * @return bool
     * @throws Exception
     */
    private function updateSettingsValuesTable(PdoMySql $transaction, array $settings_data)
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
                    throw new ErrorException(
                        'Since you are running this script via CLI, you\'ll need to pass in the environment name as the first argument'
                    );
                } else {
                    throw new ErrorException(
                        'Empty or missing ' . $_SERVER['ENVIRONMENT'] . ' section in ' . $environment_ini_file
                    );
                }
            }
        } else {
            throw new ErrorException(
                'Missing '
                . $environment_ini_file
                . '. Please create that file and an array section that matches your Apache ENVIRONMENT directive.'
            );
        }

        return $this_section[$value];
    }
}