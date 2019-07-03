<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * Account.php
 *
 * User, login and account management
 *
 **/

namespace User;

class Account
{
    protected $errors = array();

    public function __construct()
    {
    }


    /**
     * @param $username
     * @return array|bool
     */
    public function get($username)
    {
        $sql = "
          SELECT account.*
          FROM account
          INNER JOIN account_password ON account.username = account_password.account_username
          LEFT JOIN account_roles ON account.username = account_roles.account_username
          WHERE account.username = ?
          AND account.archived = '0'
          AND account_roles.archived = '0'
        ";

        $db     = new \Db\Query($sql, [$username]);
        $result = $db->fetchAssoc();

        // Include account roles
        $result['account_roles'] = $this->getAccountRoles($username);

        return $result;
    }


    /**
     * @return array|bool
     */
    public function getAll()
    {
        $sql = "
          SELECT *
          FROM account
          WHERE archived='0'
        ";

        $db         = new \Db\Query($sql);
        $accounts   = $db->fetchAllAssoc();
        $results    = [];

        foreach($accounts as $account) {

            $results[$account['username']]                  = $account;
            $results[$account['username']]['account_roles'] = $this->getAccountRoles($account['username']);
        }

        return $results;
    }


    /**
     * @param $username
     * @return array|bool
     */
    private function getAccountRoles($username)
    {
        $sql = "
            SELECT role_name
            FROM account_roles
            WHERE account_username = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->fetchAll();
    }


    /**
     * @return bool
     */
    public function getAccountFromCookieValidation()
    {
        $cookie = !empty($_COOKIE[LOGIN_COOKIE]) ? $_COOKIE[LOGIN_COOKIE] : false;

        return $this->getAccountFromLoginSession($cookie);
    }


    /**
     * @return bool
     */
    public function getAccountFromSessionValidation()
    {
        $session = !empty($_SESSION[LOGIN_COOKIE]) ? $_SESSION[LOGIN_COOKIE] : false;

        return $this->getAccountFromLoginSession($session);
    }


    /**
     * @param $value
     * @return bool
     */
    private function getAccountFromLoginSession($value)
    {
        $sql    = "
            SELECT DISTINCT login_session.account_username AS login_session_account_username, login_session.uid, expiration
            FROM login_session
            INNER JOIN account ON login_session.account_username=account.username
            LEFT JOIN account_roles ON account.username = account_roles.account_username
            WHERE login_session.archived = '0'
            AND account.archived = '0'
            AND account_roles.archived = '0'
            ORDER BY expiration DESC
        ";

        $db     = new \Db\Query($sql);
        $result = $db->fetchAllAssoc();

        foreach ($result as $key => $row) {
            $username   = $row['login_session_account_username'];
            $uid        = $row['uid'];
            $exp        = date('Y-m-d', strtotime($row['expiration']));

            if (\User\Login::hashCookie($username, $uid, $exp) == $value) {
                $username_hit = $row['login_session_account_username'];

                continue;
            }
        }

        return !empty($username_hit) ? $this->get($username_hit) : false;
    }


    /**
     * @param array $account_data
     * @return bool
     * @throws \Exception
     */
    public function update(array $account_data)
    {
        self::editUsersCheck();
        
        $required_fields = [
            'username',
            'firstname',
            'lastname',
            'email',
        ];

        $optional_fields = [
            'account_roles',
        ];

        foreach ($required_fields as $required_field) {
            if (empty($account_data[$required_field]))
                $errors[] = 'Missing ' . $required_field;
        }

        if (!empty($errors)) {

            $this->errors[] = implode(',', $errors);

            return false;
        }

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {

            self::updateAccountTable($transaction, $account_data);

            // archive/re-add account roles
            if (self::archiveAccountRoles($transaction, $account_data['username']))
                self::insertAccountRoles($transaction, $account_data['username'], $account_data['account_roles']);

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
     * @param array $account_data
     * @return bool
     * @throws \Exception
     */
    private function updateAccountTable(\Db\PdoMySql $transaction, array $account_data)
    {
        self::editUsersCheck();
        
        $sql = "
            UPDATE account
            SET
              firstname = ?,
              lastname = ?,
              email = ?
            WHERE username = ?
        ";

        $bind = [
            $account_data['firstname'],
            $account_data['lastname'],
            $account_data['email'],
            $account_data['username'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param string $username
     * @return bool
     * @throws \Exception
     */
    private function archiveAccountRoles(\Db\PdoMySql $transaction, $username)
    {
        self::editUsersCheck();
        
        $sql = "
            UPDATE account_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE account_username = ?
            AND archived = '0'
        ";

        $bind = [$username];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param string $username
     * @param array $account_roles
     * @return bool
     * @throws \Exception
     */
    private function insertAccountRoles(\Db\PdoMySql $transaction, $username, $account_roles = array())
    {
        self::editUsersCheck();
        
        $roles      = new \User\Roles();
        $all_roles  = $roles->getAll();

        foreach ($all_roles as $role) {

            $sql    = null;
            $bind   = [];

            if (in_array($role['role_name'], $account_roles)) {

                $sql .= "
                  INSERT INTO account_roles (account_username, role_name)
                  VALUES (?, ?);
                ";

                $bind[] = $username;
                $bind[] = $role['role_name'];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        }

        return true;
    }


    /**
     * @param $username
     * @return array|bool
     * @throws \Exception
     */
    public function archiveAccount($username)
    {
        self::archiveUsersCheck();
        
        if (self::getUsername() == $username)
            throw new \Exception('The logged in account cannot archive itself...');

        $sql    = "UPDATE account SET archived='1', archived_datetime = NOW() WHERE username = ?;";
        $db     = new \Db\Query($sql, [$username]);

        return $db->run();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getUsername($username = null)
    {
        $username = $username ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT username
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getFirstname($username = null)
    {
        $username = (string)filter_var($username, FILTER_SANITIZE_STRING) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT firstname
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getLastname($username = null)
    {
        $username = (string)filter_var($username, FILTER_SANITIZE_STRING) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT lastname
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getEmail($username = null)
    {
        $username = (string)filter_var($username, FILTER_SANITIZE_STRING) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT email
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return implode('; ', $this->errors);
    }


    /**
     * @param array $data
     * @return bool
     */
    public function archive(array $data)
    {
        $sql = "
            UPDATE account
            SET archived = '1',
            archived_datetime = NOW()
            WHERE username = ?
            AND archived='0'
        ";

        $bind = [
            $data['username']
        ];

        $db = new \Db\Query($sql, $bind);

        return $db->run();
    }


    /**
     * @throws \Exception
     */
    public static function editUsersCheck()
    {
        if (!\Settings::value('edit_users'))
            throw new \Exception('Not allowed to edit users');
    }


    /**
     * @throws \Exception
     */
    public static function archiveUsersCheck()
    {
        if (!\Settings::value('archive_users'))
            throw new \Exception('Not allowed to archive users');
    }
}