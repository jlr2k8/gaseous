<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/14/2017
 *
 * register.php
 *
 * Account registration
 *
 */

namespace User;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Settings;
use Setup\Reset\System;
use stdClass;

class Register
{
    public $errors = [], $post;


    /**
     * @param array $post_data
     */
    public function __construct($post_data = [])
    {
        //setup
        $this->account = new Account();

        if (!empty($post_data)) {

            $this->post = new stdClass();

            foreach ($post_data as $key => $val) {

                // only set it if its not empty
                if (!empty($val)) {

                    $this->post->$key = (string)filter_var($val, FILTER_SANITIZE_STRING);
                }
            }
        }
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function createAccount()
    {
        $account_data   = $this->post;

        // If there are no accounts yet, we can presume the original user is the site admin
        $accounts_exist = self::accountsExist();

        $this->validation();

        if (empty($this->errors)) {
            $transaction    = new PdoMySql();

            $transaction->beginTransaction();

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

            $this->createAccountPassword($transaction);

            if (!$accounts_exist) {
                $primary_role_data = [
                    'account_username'  => $account_data->username,
                    'role_name'         => System::ADMIN_ROLE,
                ];

                Roles::insertAccountRole($primary_role_data, $transaction);
            }

            return $transaction->commit();
        }

        return false;
    }


    /**
     * @return int
     */
    public static function accountsExist()
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                account
            INNER JOIN
                account_password
            ON
                account.username = account_password.account_username
            WHERE
                account.archived = '0'
            AND 
                account_password.archived = '0';
        ";

        $db             = new Query($sql);
        $accounts_exist = (int)($db->fetch() > 0);

        return $accounts_exist;
    }



    /**
     * @return bool
     */
    private function validation()
    {
        $account_data = $this->post;

        // validate first name
        if (empty($account_data->firstname) || !Validation::checkValidName($account_data->firstname)) {
            $this->errors[] = 'First Name is invalid or missing';
        }

        // validate last name
        if (empty($account_data->lastname) || !Validation::checkValidName($account_data->lastname)) {
            $this->errors[] = 'Last Name is invalid or missing';
        }

        // validate email (formatting and whether or not it exists)
        $this->validateEmail();

        // validate username
        if (empty($account_data->username) || Validation::checkValidUsername($account_data->username) == false) {
            $this->errors[] = 'This username is not allowed. It must be at least 3 characters, contain only numbers and letters, and may not contain profanity.';
        }

        // check against existing username
        if (Validation::checkIfUsernameExists($account_data->username)) {
            $this->errors[] = 'This username already exists!';
        }

        // check if passwords matched
        if ($account_data->password != $account_data->confirm_password) {
            $this->errors[] = 'Your passwords did not match';
        }

        // check valid password
        if (Validation::checkValidPassword($account_data->password) == false) {
            $this->errors[] = 'Your password must contain at least 7 characters, at least one uppercase letter, at least one lowercase letter, and at least one number';
        }

        return true;
    }


    /**
     * Simply validates email. This is the only validation needed for the simpler CreateGuestAccount()
     *
     * @return bool
     */
    private function validateEmail()
    {
        $account_data = $this->post;

        // validate email
        if (!isset($account_data->email) && !filter_var($account_data->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'This email is not valid';
        }

        // validate against existing email
        if (Validation::checkIfEmailExists($account_data->email)) {
            $this->errors[] = 'This email address has already been used for another account.';
        }

        return true;
    }


    /**
     * @param $account_username
     * @return bool
     * @throws Exception
     */
    private function createAccountPassword(PdoMySql $transaction)
    {
        $username   = $this->post->username;
        $password   = Password::hashPassword($this->post->password);
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
}




