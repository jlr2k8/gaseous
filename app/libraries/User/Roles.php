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

class Roles
{
    protected $errors;

    public function __construct()
    {
    }


    /**
     * @return bool
     */
    public static function rolesExist()
    {
        $sql = "
            SELECT COUNT(*) AS count
            FROM role
            WHERE archived = '0';
        ";

        $db     = new \Db\Query($sql);
        $count  = (int)$db->fetch();

        return ($count > (int)0);
    }


    /**
     * @return array|bool
     */
    public function getAll()
    {
        $sql    = "SELECT * FROM role WHERE archived = '0';";
        $db     = new \Db\Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $role_name
     * @return array|bool
     */
    public function get($role_name)
    {
        $sql    = "SELECT * FROM role WHERE role_name = ? AND archived = '0'";
        $db     = new \Db\Query($sql, [$role_name]);

        return $db->fetchAssoc();
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws \Exception
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

        $role_data['role_name'] = \Seo\Url::convert($role_data['role_name']);
        $sql                    = "
            INSERT INTO role (role_name, description)
            VALUES (?,?);
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['description'],
        ];

        $db = new \Db\Query($sql, $bind);

        return $db->run();
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws \Exception
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

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {

            $role_data['role_name'] = \Seo\Url::convert($role_data['role_name']);

            self::updateRoleTable($transaction, $role_data);
            self::updateAccountRoles($transaction, $role_data);
            self::updatePageRoles($transaction, $role_data);
            self::updateConfigRoles($transaction, $role_data);

        } catch (\Exception $e) {

            $transaction->rollBack();

            echo $e->getMessage() . ' ' . $e->getTraceAsString();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param array $role_data
     * @return bool
     * @throws \Exception
     */
    private function updateRoleTable(\Db\PdoMySql $transaction, array $role_data)
    {
        self::editRolesCheck();
        
        $sql = "
            UPDATE role
            SET
              role_name = ?,
              description = ?
            WHERE role_name = ?
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['description'],
            $role_data['old_role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param array $role_data
     * @return bool
     * @throws \Exception
     */
    private function updateAccountRoles(\Db\PdoMySql $transaction, array $role_data)
    {
        \User\Account::editUsersCheck();

        $sql = "
            UPDATE account_roles
            SET role_name = ?
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['old_role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param array $role_data
     * @return bool
     */
    private function updatePageRoles(\Db\PdoMySql $transaction, array $role_data)
    {
        $sql = "
            UPDATE page_roles
            SET role_name = ?
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['old_role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param array $role_data
     * @return bool
     */
    private function updateConfigRoles(\Db\PdoMySql $transaction, array $role_data)
    {
        $sql = "
            UPDATE config_roles
            SET role_name = ?
            WHERE role_name = ?
            AND archived = '0';
        ";

        $bind = [
            $role_data['role_name'],
            $role_data['old_role_name'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $role_data
     * @return bool
     * @throws \Exception
     */
    public function archive(array $role_data)
    {
        self::archiveRolesCheck();

        // just need the role name for this - maybe not pass in an array?
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

        $sql = "
            UPDATE role
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE role_name = ?
            AND archived = '0'
        ";

        $bind = [
            $role_data['role_name'],
        ];

        $db = new \Db\Query($sql, $bind);

        return $db->run();
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return implode('; ', $this->errors);
    }


    /**
     * @throws \Exception
     */
    public static function editRolesCheck()
    {
        if (!\Settings::value('edit_roles'))
            throw new \Exception('Not allowed to edit roles');
    }

    
    /**
     * @throws \Exception
     */
    public static function archiveRolesCheck()
    {
        if (!\Settings::value('archive_roles'))
            throw new \Exception('Not allowed to archive roles');
    }


    /**
     * @throws \Exception
     */
    public static function addRolesCheck()
    {
        if (!\Settings::value('add_roles'))
            throw new \Exception('Not allowed to archive roles');
    }
}