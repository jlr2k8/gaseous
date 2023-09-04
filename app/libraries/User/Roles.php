<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 6/10/2018
 *
 * Roles.php
 *
 * User account role management
 *
 */

namespace User;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Seo\Url;
use Settings;

class Roles
{
    protected $errors;

    public function __construct()
    {
    }


    /**
     * @return array
     */
    public function getAll()
    {
        $sql    = "SELECT * FROM role WHERE archived = '0';";
        $db     = new Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $role_name
     * @return array
     */
    public function get($role_name)
    {
        $sql    = "SELECT * FROM role WHERE role_name = ? AND archived = '0'";
        $db     = new Query($sql, [$role_name]);

        return $db->fetchAssoc();
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws Exception
     */
    public function insert(array $role_data)
    {
        self::addRolesCheck();
        
        $required_fields = [
            'role_name',
        ];

        foreach ($required_fields as $required_field) {
            if (empty($role_data[$required_field]))
                $errors[] = 'Missing ' . $required_field;
        }

        if (!empty($errors)) {

            $this->errors[] = implode(',', $errors);

            $_SESSION['admin_role_data_submission'] = $role_data;

            return false;
        }

        $role_data['role_name'] = Url::convert($role_data['role_name']);

        return self::insertRole($role_data);
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws Exception
     */
    public function update(array $role_data)
    {
        self::editRolesCheck();
        
        $required_fields = [
            'role_name',
            'old_role_name',
        ];

        foreach ($required_fields as $required_field) {
            if (empty($role_data[$required_field]))
                $errors[] = 'Missing ' . $required_field;
        }

        if (!empty($errors)) {

            $this->errors[] = implode(',', $errors);

            $_SESSION['admin_role_data_submission'] = $role_data;

            return false;
        }

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            $role_data['role_name'] = Url::convert($role_data['role_name']);

            $setting_roles  = self::getSettingRoles($role_data['old_role_name']);
            $account_roles  = self::getAccountRoles($role_data['old_role_name']);
            $content_roles     = self::getPageRoles($role_data['old_role_name']);

            self::archiveRole($role_data['old_role_name'], $transaction);
            self::archiveSettingRole($role_data['old_role_name'], $transaction);
            self::archiveAccountRole($role_data['old_role_name'], $transaction);
            self::archivePageRole($role_data['old_role_name'], $transaction);

            self::insertRole($role_data, $transaction);

            foreach ($setting_roles as $setting_role) {
                $setting_role['role_name'] = $role_data['role_name'];

                self::insertSettingRole($setting_role, $transaction);
            }

            foreach ($account_roles as $account_role) {
                $account_role['role_name'] = $role_data['role_name'];

                self::insertAccountRole($account_role, $transaction);
            }

            foreach ($content_roles as $page_role) {
                $page_role['role_name'] = $role_data['role_name'];

                self::insertContentRole($page_role, $transaction);
            }
        } catch (Exception $e) {

            $transaction->rollBack();

            Log::app($e->getTraceAsString(), $e->getMessage());

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws Exception
     */
    public function archive(array $role_data)
    {
        self::archiveRolesCheck();

        $required_fields = [
            'role_name',
        ];

        foreach ($required_fields as $required_field) {
            if (empty($role_data[$required_field]))
                $errors[] = 'Missing ' . $required_field;
        }

        if (!empty($errors)) {
            $this->errors[]                         = implode(',', $errors);
            $_SESSION['admin_role_data_submission'] = $role_data;

            return false;
        }

        $transaction    = new PdoMySql();

        $transaction->beginTransaction();

        try {
            self::archiveRole($role_data['role_name'], $transaction);
            self::archiveSettingRole($role_data['role_name'], $transaction);
            self::archiveAccountRole($role_data['role_name'], $transaction);
            self::archivePageRole($role_data['role_name'], $transaction);
        } catch (Exception $e) {
            $transaction->rollBack();

            Log::app($e->getTraceAsString(), $e->getMessage());

            throw $e;
        }

        return true;
    }


    /**
     * @param $role_name
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function archiveRole($role_name, PdoMySql $transaction = null)
    {
        $transaction = $transaction ?? new PdoMySql();

        $sql = "
            UPDATE role
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE role_name = ?
            AND archived = '0'
        ";

        $bind = [
            $role_name,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $role_name
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function archiveSettingRole($role_name, PdoMySql $transaction = null)
    {
        $transaction = $transaction ?? new PdoMySql();

        $sql = "
            UPDATE settings_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE role_name = ?
            AND archived = '0'
        ";

        $bind = [
            $role_name,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $role_name
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function archiveAccountRole($role_name, PdoMySql $transaction = null)
    {
        $transaction = $transaction ?? new PdoMySql();

        $sql = "
            UPDATE account_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE role_name = ?
            AND archived = '0'
        ";

        $bind = [
            $role_name,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $role_name
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function archivePageRole($role_name, PdoMySql $transaction = null)
    {
        $transaction = $transaction ?? new PdoMySql();

        $sql = "
            UPDATE content_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE role_name = ?
            AND archived = '0'
        ";

        $bind = [
            $role_name,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $role_name
     * @return array
     */
    private static function getSettingRoles($role_name)
    {
        $sql = "
            SELECT settings_key, role_name
            FROM settings_roles
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_name,
        ];

        $db = new Query($sql, $bind);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $role_name
     * @return array
     */
    private static function getAccountRoles($role_name)
    {
        $sql = "
            SELECT account_username, role_name
            FROM account_roles
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_name,
        ];

        $db = new Query($sql, $bind);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $role_name
     * @return array
     */
    private static function getPageRoles($role_name)
    {
        $sql = "
            SELECT content_iteration_uid, role_name
            FROM content_roles
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_name,
        ];

        $db = new Query($sql, $bind);

        return $db->fetchAllAssoc();
    }


    /**
     * @param array $setting_role_data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function insertSettingRole(array $setting_role_data, PdoMySql $transaction = null)
    {
        $transaction    = $transaction ?? new PdoMySql();
        $sql            = "
            INSERT INTO settings_roles (settings_key, role_name)
            VALUES (?, ?);
        ";

        $bind = [
            $setting_role_data['settings_key'],
            $setting_role_data['role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $setting_role_data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public static function insertAccountRole(array $setting_role_data, PdoMySql $transaction = null)
    {
        $transaction    = $transaction ?? new PdoMySql();
        $sql            = "
            INSERT INTO
                account_roles (
                    account_username,
                    role_name
                ) VALUES (
                ?,
                ?
            );
        ";

        $bind = [
            $setting_role_data['account_username'],
            $setting_role_data['role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $setting_role_data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function insertContentRole(array $setting_role_data, PdoMySql $transaction = null)
    {
        $transaction    = $transaction ?? new PdoMySql();
        $sql            = "
            INSERT INTO content_roles (content_iteration_uid, role_name)
            VALUES (?, ?);
        ";

        $bind = [
            $setting_role_data['content_iteration_uid'],
            $setting_role_data['role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $role_data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    private static function insertRole(array $role_data, PdoMySql $transaction = null)
    {
        $transaction    = $transaction ?? new PdoMySql();
        $sql            = "
            INSERT INTO role (role_name, description)
            VALUES (?,?);
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['description'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return implode('; ', $this->errors);
    }


    /**
     * @throws Exception
     */
    public static function editRolesCheck()
    {
        if (!Settings::value('edit_roles'))
            throw new Exception('Not allowed to edit roles');
    }

    
    /**
     * @throws Exception
     */
    public static function archiveRolesCheck()
    {
        if (!Settings::value('archive_roles'))
            throw new Exception('Not allowed to archive roles');
    }


    /**
     * @throws Exception
     */
    public static function addRolesCheck()
    {
        if (!Settings::value('add_roles'))
            throw new Exception('Not allowed to archive roles');
    }
}