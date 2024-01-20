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
use Email;
use Exception;
use Settings;
use Setup\Reset\System;
use stdClass;
use Utilities\Sanitize;

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
                // only set it if it's not empty
                if (!empty($val)) {
                    if ($key == 'email') {
                        $this->post->$key = (string)filter_var($val, FILTER_SANITIZE_EMAIL);
                    } elseif ($key !== 'password') {
                        $this->post->$key = Sanitize::string($val);
                    }
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

        $validation = $this->account->validation($account_data);

        if ($validation !== true) {
            $this->errors = $validation;
        }

        if (empty($this->errors)) {
            $transaction    = new PdoMySql();

            $transaction->beginTransaction();

            $this->account->createAccount($transaction, $account_data);
            $this->account->createAccountPassword($transaction, $account_data->password, $account_data->username);

            if (!$accounts_exist) {
                $primary_role_data = [
                    'account_username'  => $account_data->username,
                    'role_name'         => System::ADMIN_ROLE,
                ];

                Roles::insertAccountRole($primary_role_data, $transaction);
            }

            if ($transaction->commit()) {
                $this->createdAccountNotifyEmail();
                return true;
            }
        }

        return false;
    }


    /**
     * @return bool
     * @throws Exception
     */
    private function createdAccountNotifyEmail()
    {
        $email_obj          = new Email();
        $full_web_url       = Settings::value('full_web_url');
        $webmaster_name     = Settings::value('webmaster_name');
        $webmaster_email    = Settings::value('webmaster_email');
        $site_title         = Settings::value('site_title') ?: $full_web_url;

        $account_data       = $this->post;
        $username           = $account_data->username;
        $email              = $account_data->email;

        // TODO - move subject and email body to settings (templates)
        $email_to_user      = $email_obj->sendEmail(
            $webmaster_email,
            (array)$email,
            'Welcome to ' . $site_title . '!',
            $webmaster_name,
            (array)$username,
            [],
            [],
            "Hello $username,
                <br />
                You are receiving this email because you have successfully registered an account at $full_web_url using this email address.
                <br />
                Welcome aboard!
                <br />
                <br />
                $webmaster_name
                <br />
                $webmaster_email
            "
        );

        // TODO - move subject and email body to settings (templates)
        $email_to_webmaster = $email_obj->sendEmail(
            $webmaster_email,
            (array)$webmaster_email,
            'A new user has just registered',
            $webmaster_name,
            [],
            [],
            [],
            "Hello,
                <br />
                $username ($email) has just registered with $site_title.
                <br />
                <br />
                No action is required.
            "
        );

        return ($email_to_user && $email_to_webmaster);
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
}