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

use Db\Query;
use Exception;
use Settings;
use stdClass;

class Register
{
    public $errors = [], $account_id, $post;


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
     * TODO - transactions
     * @return bool
     * @throws Exception
     */
    public function createAccount()
    {
        $account_data = $this->post;

        $this->validation();

        if (empty($this->errors)) {

            //INSERT INTO account - (2016-05-27) for now, account.account_email and profile.email will be identical.
            $sql = "INSERT INTO account (firstname, lastname, username, email) VALUES (?,?,?,?);";

            $bind = [
                $account_data->firstname,
                $account_data->lastname,
                $account_data->username,
                $account_data->email
            ];

            $db                 = new Query($sql, $bind);
            $it_ran             = $db->run();
            $create_password    = $this->createAccountPassword($account_data->username);

            return ($it_ran && $create_password) ? $this->redirectRegistrationSubmission() : false;
        }

        return false;
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
     * TODO - transaction as part of createAccount()
     * @param $account_id
     * @return bool
     * @throws Exception
     */
    private function createAccountPassword($account_id, $password = false)
    {
        $password = empty($password) ? $this->post->password : $password;

        if (!$password)
            return false;

        //INSERT INTO account_password
        $sql    = "INSERT INTO account_password (account_username, password) VALUES (?, ?);";
        $db     = new Query(
            $sql,
            [
                $account_id,
                Password::hashPassword($password)
            ]
        );

        return $db->run();
    }


    /**
     * Check if roles exist. If not, recently registered user should set that up.
     * Without roles, no administration pages are bound by roles/users yet
     *
     * Potentially, this could be a good place to kick off other post-registration workflows.
     *
     * @return bool
     */
    private function redirectRegistrationSubmission()
    {
        $_SESSION['registration_redirect'] = Roles::rolesExist() ? Settings::value('full_web_url') : '/admin/roles/';

        return true;
    }
}




