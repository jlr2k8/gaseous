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

use Db\PdoMySql;
use Db\Query;
use Exception;
use Log;
use Settings;
use stdClass;
use Utilities\Sanitize;

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
          SELECT
                account.*
          FROM
                account
          INNER JOIN
                account_password ON account.username = account_password.account_username
          LEFT JOIN
                account_roles ON account.username = account_roles.account_username
          WHERE
                account.username = ?
          AND
                account.archived = '0'
        ";

        $db     = new Query($sql, [$username]);
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

        $db         = new Query($sql);
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
     * @return array
     */
    private function getAccountRoles($username)
    {
        $sql = "
            SELECT role_name
            FROM account_roles
            WHERE account_username = ?
            AND archived = '0';
        ";

        $db = new Query($sql, [$username]);

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
            SELECT
                DISTINCT login_session.account_username AS login_session_account_username,
                login_session.uid, expiration
            FROM
                login_session
            INNER JOIN
                account ON login_session.account_username=account.username
            LEFT JOIN
                account_roles ON account.username = account_roles.account_username
            WHERE
                login_session.archived = '0'
            AND
                account.archived = '0'
            ORDER BY
                expiration DESC;
        ";

        $db     = new Query($sql);
        $result = $db->fetchAllAssoc();

        foreach ($result as $key => $row) {
            $username   = $row['login_session_account_username'];
            $uid        = $row['uid'];
            $exp        = date('Y-m-d', strtotime($row['expiration']));

            if (Login::hashCookie($username, $uid, $exp) == $value) {
                $username_hit = $row['login_session_account_username'];

                continue;
            }
        }

        return !empty($username_hit) ? $this->get($username_hit) : false;
    }


    /**
     * @param array $account_data
     * @return bool
     * @throws Exception
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

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            // archive/re-add account
            if (self::archive($transaction, $account_data)) {
               $this->createAccount($transaction, (object)$account_data);
            }

            // archive/re-add account roles
            if (self::archiveAccountRoles($transaction, $account_data['username']))
                self::insertAccountRoles($transaction, $account_data['username'], $account_data['account_roles']);
        } catch (Exception $e) {

            $transaction->rollBack();

            Log::app($e->getTraceAsString(), $e->getMessage());

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param stdClass $account_data
     * @param bool $account_update
     * @return mixed
     */
    public function validation(stdClass $account_data, $account_update = false)
    {
        // validate first name
        if (empty($account_data->firstname) || !Validation::checkValidName($account_data->firstname)) {
            $this->errors[] = 'First Name is invalid or missing';
        }

        // validate last name
        if (empty($account_data->lastname) || !Validation::checkValidName($account_data->lastname)) {
            $this->errors[] = 'Last Name is invalid or missing';
        }

        // validate email (formatting and whether or not it exists)
        $this->validateEmail($account_data->email);

        // validate username
        if (empty($account_data->username) || Validation::checkValidUsername($account_data->username) == false) {
            $this->errors[] = 'This username is not allowed. It must be at least 3 characters, contain only numbers and letters, and may not contain profanity.';
        }

        // check if passwords matched
        if ($account_data->password != $account_data->confirm_password) {
            $this->errors[] = 'Your passwords did not match';
        }

        // check valid password
        if (Validation::checkValidPassword($account_data->password) == false) {
            $this->errors[] = 'Your password must contain at least 7 characters, at least one uppercase letter, at least one lowercase letter, and at least one number';
        }

        // check against existing username
        if ($account_update !== true) {
            if (Validation::checkIfUsernameExists($account_data->username)) {
                $this->errors[] = 'This username already exists!';
            }
        }

        return $this->errors ?: true;
    }

    /**
     * Simply validates email.
     *
     * @return bool
     */
    private function validateEmail($email_address)
    {
        // validate email
        if (!isset($email_address) && !filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'This email is not valid';
        }

        // validate against existing email
        if (Validation::checkIfEmailExists($email_address)) {
            $this->errors[] = 'This email address has already been used for another account.';
        }

        return true;
    }


    /**
     * @param array $account_data
     * @return bool
     * @throws Exception
     */
    public function userUpdate(array $account_data)
    {
        $skip_password      = false;
        $required_fields    = [
            'username',
            'firstname',
            'lastname',
            'email',
            'password',
        ];

        if (empty($_SESSION['token_login']) && !empty($account_data['password'])) {
            $required_fields[] = 'current_password';
        } elseif (empty($account_data['password'])) {
            $skip_password  = true;
        }

        if (empty($account_data['password']) && empty($account_data['confirm_password'])) {
            unset($required_fields[array_search('password', $required_fields)]);
        }

        foreach ($required_fields as $required_field) {
            if (empty($account_data[$required_field]))
                $errors[] = 'Missing ' . $required_field;
        }

        if (!empty($errors)) {
            $this->errors[] = implode(',', $errors);

            return false;
        }

        $validation     = $this->validation((object)$account_data, true);

        if ($validation !== true) {
            $this->errors = $validation;
            return false;
        }

        $transaction    = new PdoMySql();

        $transaction->beginTransaction();

        try {
            // archive/re-add account
            if (self::archive($transaction, $account_data)) {
                $this->createAccount($transaction, (object)$account_data);
            }

            // archive/re-add account password (user logged in with password)
            if (!$skip_password && empty($_SESSION['token_login'])) {
                $verify['username'] = $account_data['username'];
                $verify['password'] = $account_data['current_password'];

                if (Password::verifyPassword($verify)) {
                    $this->archiveAccountPassword($transaction);
                    $this->createAccountPassword($transaction, $account_data['password']);
                } else {
                    $this->errors[] = 'Your current password is invalid...';

                    Log::app('Password change attempt failed', $account_data['username']);

                    return false;
                }
            } elseif (!$skip_password && !empty($_SESSION['token_login'])) {
                $this->archiveAccountPassword($transaction);
                $this->createAccountPassword($transaction, $account_data['password']);
            } else {

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
     * @param PdoMySql $transaction
     * @return bool
     */
    public function archiveAccountPassword(PdoMySql $transaction)
    {
        $username   = $_SESSION['account']['username'];

        $sql        = "
            UPDATE
                account_password
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                account_username = ?
            AND
                archived = '0'; 
        ";

        $bind = [
            $username,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param stdClass $account_data
     */
    public function createAccount(PdoMySql $transaction, stdClass $account_data)
    {
        $sql = "
                INSERT INTO
                    account (
                        firstname,
                        lastname,
                        username,
                        email
                    ) VALUES (
                        ?,
                        ?,
                        ?,
                        ?
                    );
            ";

        $bind = [
            $account_data->firstname,
            $account_data->lastname,
            $account_data->username,
            $account_data->email
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param $password
     * @param null $username
     * @return bool
     */
    public function createAccountPassword(PdoMySql $transaction, $password, $username = null)
    {
        $username   = $_SESSION['account']['username'] ?? $username;
        $password   = Password::hashPassword($password);

        $sql        = "
            INSERT INTO
                account_password (
                    account_username,
                    password
                ) VALUES (
                    ?,
                    ?
                );
        ";

        $bind = [
            $username,
            $password,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param string $username
     * @return bool
     * @throws Exception
     */
    private function archiveAccountRoles(PdoMySql $transaction, $username)
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
     * @param PdoMySql $transaction
     * @param string $username
     * @param array $account_roles
     * @return bool
     * @throws Exception
     */
    private function insertAccountRoles(PdoMySql $transaction, $username, $account_roles = array())
    {
        $roles      = new Roles();
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
     * @return bool
     * @throws Exception
     */
    public function archiveAccount($username)
    {
        self::archiveUsersCheck();
        
        if (self::getUsername() == $username)
            throw new Exception('The logged in account cannot archive itself...');

        $sql    = "
            UPDATE
                account
            SET
                archived='1',
                archived_datetime = NOW()
            WHERE
                username = ?;
        ";

        $db     = new Query($sql, [$username]);

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

        $db = new Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getFirstname($username = null)
    {
        $username = Sanitize::string($username) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT firstname
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getLastname($username = null)
    {
        $username = Sanitize::string($username) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT lastname
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @param null $username
     * @return bool|string
     */
    public static function getEmail($username = null)
    {
        $username = Sanitize::string($username) ?? $_SESSION['account']['username'] ?? false;

        if(empty($username))
            return false;

        $sql = "
            SELECT email
            FROM account
            WHERE username = ?
            AND archived = '0';
        ";

        $db = new Query($sql, [$username]);

        return $db->fetch();
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return (array)$this->errors;
    }


    /**
     * @param PdoMySql $transaction
     * @param array $data
     * @return bool
     */
    private static function archive(PdoMySql $transaction, array $data)
    {
        $sql = "
            UPDATE
                account
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                username = ?
            AND
                archived='0';
        ";

        $bind = [
            $data['username']
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @throws Exception
     */
    public static function editUsersCheck()
    {
        if (!Settings::value('edit_users'))
            throw new Exception('Not allowed to edit users');
    }


    /**
     * @throws Exception
     */
    public static function archiveUsersCheck()
    {
        if (!Settings::value('archive_users'))
            throw new Exception('Not allowed to archive users');
    }
}