<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/6/2017
 *
 * login.php
 *
 * Deals with login validation, password management, etc.
 *
 */

namespace User;

use Content\Utilities;
use Db\Query;
use Email;
use ReCaptcha;
use Settings;
use Utilities\Token;

class Login
{
    public $account, $reCaptcha, $cookie_domain;
    
    public function __construct()
    {
        $this->account          = new Account();
        $this->reCaptcha        = new ReCaptcha();
        $this->cookie_domain    = Settings::value('cookie_domain') ?: '.' . $_SERVER['SERVER_NAME'];
    }


    /**
     * @return array|bool
     */
    public function validateCookie()
    {
        return !empty($this->account->getAccountFromCookieValidation());
    }


    /**
     * @return array|bool
     */
    public function validateSession()
    {
        return !empty($this->account->getAccountFromSessionValidation());
    }


    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function validateLogin($username, $password)
    {
        $login_data = [
            'username'  => $username,
            'password'  => $password,
        ];

        $is_verified_password = Password::verifyPassword($login_data);

        return ($is_verified_password === true);
    }


    /**
     * @param $token
     * @return string|null
     */
    public function validateToken($token)
    {
        $sql    = "
            SELECT
                account.username 
            FROM
                account
			INNER JOIN
			    token_email ON token_email.email=account.email
			WHERE
			    token= ?
			AND
			    token_email.created_datetime >= NOW() - INTERVAL 12 HOUR
			AND
			    account.archived = '0'
			AND
			    token_email.archived = '0';
        ";

        $db     = new Query($sql, [$token]);
        $result = $db->fetch();

        return $result;
    }


    /**
     * @return bool
     */
    public function checkLogin()
    {
        if (!empty($_SESSION[LOGIN_COOKIE]) && $this->validateSession()) {
            return true;
        } elseif (!empty($_COOKIE[LOGIN_COOKIE]) && $this->validateCookie()) {
            $account    = $this->account->getAccountFromCookieValidation();
            $username   = $account['username'];

            return $this->createSession($username);
        }

        $this->logout();

        return false;
    }


    /**
     * @return bool
     */
    public function checkPostLogin()
    {
        $logged_in = false;

        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $username   = !empty($_POST['username'])
                ? (string)filter_var($_POST['username'], FILTER_SANITIZE_STRING)
                : false;

            $password   = !empty($_POST['password'])
                ? (string)filter_var($_POST['password'], FILTER_SANITIZE_STRING)
                : false;

            if ($this->validateLogin($username, $password)) {
                $logged_in = $this->createSession($username);
            }
        }

        return $logged_in;
    }


    /**
     * @return bool
     */
    public function checkTokenLogin()
    {
        $logged_in = false;

        $token = !empty($_GET['token'])
            ? (string)filter_var($_GET['token'], FILTER_SANITIZE_STRING)
            : false;

        $username = $this->validateToken($token);

        if ($username) {
            $this->archiveToken($token);

            $logged_in                  = $this->createSession($username);
            $_SESSION['token_login']    = true;
        }

        return $logged_in;
    }


    /**
     * @param $email
     * @return bool
     * @throws \Exception
     */
    public function processTokenEmail($email)
    {
        $token                      = Token::generate(128);
        $email                      = !empty($email) ? (string)filter_var($email, FILTER_SANITIZE_EMAIL) : false;
        $validate_existing_email    = $this->validateExistingEmail($email);

        if ($validate_existing_email) {
            $sql = "
                INSERT INTO token_email (
                    token,
                    email
                )
				VALUES (
				    ?,
				    ?
                )
			";

            $bind = [
                $token,
                $email,
            ];

            $db = new Query($sql, $bind);

            $insert_token   = $db->run();
            $send_email     = $this->sendTokenLoginEmail($email, $token);

            return ($insert_token && $send_email);
        }

        return false;
    }


    /**
     * @param $email
     * @return bool|string|null
     */
    public function validateExistingEmail($email)
    {
        $email  = filter_var($email, FILTER_SANITIZE_EMAIL);
        $result = false;

        if (!empty($email)) {
            $sql = "
                SELECT
                    email
                FROM
                    account
                WHERE
                    email = ?
                AND
                    account.archived = '0';
            ";

            $bind = [
                $email,
            ];

            $db     = new Query($sql, $bind);
            $result = $db->fetch();
        }

        return $result;
    }


    /**
     * @param $email
     * @param $token
     * @return bool
     * @throws \Exception
     */
    private function sendTokenLoginEmail($email, $token)
    {
        $email_obj          = new Email();
        $full_web_url       = Settings::value('full_web_url');
        $webmaster_name     = Settings::value('webmaster_name');
        $webmaster_email    = Settings::value('webmaster_email');

        // TODO - move subject and email body to settings (templates)
        $email_to_user      = $email_obj->sendEmail(
            $webmaster_email,
            (array)$email,
            'Forgotten Password',
            $webmaster_name,
            [],
            [],
            [],
            "Hello,
                <br />
                You are receiving this email because you have forgotten your password.
                <br />
                Click the following link or copy/paste it into your browser:
                <br />
                <a href='$full_web_url/login/?token=$token'>$full_web_url/login/?token=$token</a>.
                <br /><br />
                Once this link has been accessed or 12 hours has passed, it will no longer be available.
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
            'Forgotten Password email was requested',
            $webmaster_name,
            [],
            [],
            [],
            "Hello,
                <br />
                A user with the email address \"$email\" has requested a forgotten password email.
                <br />
                They should be receiving an email with a token-based login URL, which bypasses the need to log in via username and password.
                <br />
                No action is required.
            "
        );

        return ($email_to_user && $email_to_webmaster);
    }


    /**
     * @param $token
     * @return bool
     */
    public function archiveToken($token)
    {
        $sql    = "UPDATE token_email SET archived = '1' WHERE token = ? AND archived = '0';";
        $db     = new Query($sql, [$token]);

        return $db->run();
    }


    /**
     * @param $username
     * @return bool
     */
    public function createSession($username)
    {
        $expire = date('Y-m-d', strtotime('+' . Settings::value('login_cookie_expire_days') . ' day'));
        $uuid   = Query::getUuid();

        $sql    = "
            INSERT INTO
                login_session (
                    account_username, uid, expiration
                ) VALUES (
                    ?,
                    ?,
                    ?
                );
        ";

        $bind = [
            $username,
            $uuid,
            $expire
        ];

        $db = new Query($sql, $bind);

        return $db->run()
            ? ($this->storeAccountInSession($username) && $this->setLoginCookie($username, $uuid))
            : false;
    }


    /**
     * @param $username
     * @return bool
     */
    private function storeAccountInSession($username)
    {
        $account                = $this->account->get($username);
        $_SESSION['account']    = !empty($account) ? $account : [];

        return true;
    }


    /**
     * @return bool
     */
    public static function clearSession()
    {
        session_unset();
        return true;
    }


    /**
     * @return bool
     */
    public function clearLoginCookie()
    {
        return (setcookie(LOGIN_COOKIE, '', time() - 3600, '/', $this->cookie_domain));
    }


    /**
     * @return bool
     */
    public function logout()
    {
        $logout = ($this->clearLoginCookie() && self::clearSession());

        return $logout;
    }


    /**
     * @param $username
     * @param $uid
     * @return bool
     */
    private function setLoginCookie($username, $uid)
    {
        $days_int   = Settings::value('login_cookie_expire_days');
        $expire     = date('Y-m-d', strtotime('+' . $days_int . ' day'));
        $domain     = $this->cookie_domain;

        $login_cookie_value = (new Login())->hashCookie($username, $uid, $expire);

        $set_cookie = setcookie (
            LOGIN_COOKIE,
            $login_cookie_value,
            time() + (3600*24*$days_int),
            '/',
            $domain
        );

        if ($set_cookie) {
            $_SESSION[LOGIN_COOKIE] = $login_cookie_value;
            return true;
        }

        return false;
    }


    /**
     * @param $username
     * @param $uid
     * @param bool $YYYY_MM_DD_expiration_date
     * @return string
     */
    public static function hashCookie($username, $uid, $YYYY_MM_DD_expiration_date = false)
    {
        if (empty($YYYY_MM_DD_expiration_date)) {
            $YYYY_MM_DD_expiration_date = date('Y-m-d', time());
        }

        return hash('sha512', $username . $uid . $YYYY_MM_DD_expiration_date);
    }
}