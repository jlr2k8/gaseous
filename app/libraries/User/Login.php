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

class Login
{
    public $account, $reCaptcha;
    
    public function __construct()
    {
        $this->account      = new \User\Account();
        $this->reCaptcha    = new \ReCaptcha();
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
     * @return bool|int
     */
    public function validateLogin($username, $password)
    {
        $login_data = [
            'username'  => $username,
            'password'  => $password,
        ];

        $is_verified_password = \User\Password::verifyPassword($login_data);

        return ($is_verified_password === true);
    }


    /**
     * @param $token
     * @return bool
     */
    public function validateToken($token)
    {
        $sql    = "
            SELECT account.username 
            FROM account
			INNER JOIN token_email ON token_email.email=account.account_email
			WHERE token= ?
			AND token_email.created_datetime >= NOW() - INTERVAL 12 HOUR;
        ";

        $db     = new \Db\Query($sql, [$token]);
        $result = $db->fetchAssoc();

        return isset($result['account_id']) ? (int)$result['account_id'] : false;
    }


    /**
     * @return array|bool
     */
    public function checkLogin()
    {
        if (!empty($_SESSION[LOGIN_COOKIE]) && $this->validateSession()) {
            return true;
        } elseif (!empty($_COOKIE[LOGIN_COOKIE]) && $this->validateCookie()) {
            $account    = $this->account->getAccountFromCookieValidation();
            $username   = $account['username'];

            if ($this->clearLoginSession($username))
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
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $this->logout();

            $username   = !empty($_POST['username'])
                ? (string)filter_var($_POST['username'], FILTER_SANITIZE_STRING)
                : false;

            $password   = !empty($_POST['password'])
                ? (string)filter_var($_POST['password'], FILTER_SANITIZE_STRING)
                : false;

            if ($this->clearLoginSession($username) && $this->validateLogin($username, $password)) {
                return $this->createSession($username);
            } else {
                $this->logout();
                return false;
            }
        }

        return false;
    }


    /**
     * @return array|bool
     */
    public function checkRegistrationLogin()
    {
        $username           = !empty($_POST['username']) ? (string)filter_var($_POST['username'], FILTER_SANITIZE_STRING) : false;
        $password           = !empty($_POST['password']) ? (string)filter_var($_POST['password'], FILTER_SANITIZE_STRING) : false;
        $validated_username = $this->validateLogin($username, $password);

        if ($validated_username !== false) {
            $this->createSession($validated_username);

            return true;
        } else {
            $this->logout();

            return false;
        }
    }


    /**
     * @param $token
     * @return bool
     */
    public function checkTokenLogin($token)
    {
        $username = $this->validateToken($token);

        if ($username !== false) {
            $this->createSession($username);

            return true;
        } else {
            $this->logout();

            return false;
        }
    }


    /**
     * @param $token
     * @param $email
     * @return bool
     */
    public function addTokenEmail($token, $email)
    {
        $token = !empty($token) ? (string)filter_var($token, FILTER_SANITIZE_STRING) : false;
        $email = !empty($email) ? (string)filter_var($email, FILTER_SANITIZE_EMAIL) : false;

        if ($token && $email) {

            $sql = "
                INSERT INTO token_email (token,email)
				VALUES( ? , ?)
			";

            $db = new \Db\Query($sql, [$token, $email]);
            return $db->run();
        }

        return false;
    }


    /**
     * @param $token
     * @return bool
     */
    public function deleteToken($token)
    {
        $sql    = "DELETE FROM token_email WHERE token = ?;";
        $db     = new \Db\Query($sql, [$token]);

        return $db->run();
    }


    /**
     * @param $account_id
     * @return bool
     */
    public function createSession($username)
    {
        $expire = date('Y-m-d', strtotime('+' . \Settings::value('login_cookie_expire_days') . ' day'));
        $uuid   = \Db\Query::getUuid();

        $sql    = "
            INSERT INTO login_session (account_username, uid, expiration)
            VALUES(?, ?, ?);
        ";

        $db = new \Db\Query($sql, [$username, $uuid, $expire]);

        return $db->run() ? ($this->storeAccountInSession($username) && $this->setLoginCookie($username, $uuid)) : false;
    }


    /**
     * @param $username
     * @return bool
     */
    private function storeAccountInSession($username)
    {
        $account                = $this->account->get($username);
        $_SESSION['account']    = !empty($account) ? $account : false;

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
    public static function clearLoginCookie()
    {
        return (setcookie(LOGIN_COOKIE, null, time() - 3600, '/', \Settings::value('cookie_domain')));
    }


    /**
     * @return bool
     */
    public function logout()
    {
        self::clearLoginCookie();
        self::clearSession();

        return true;
    }


    /**
     * @param $username
     * @return bool
     */
    private function clearLoginSession($username)
    {
        $sql = "
          UPDATE login_session
          SET
            archived = '1',
            archived_datetime = NOW()
          WHERE account_username = ?;
        ";

        $db = new \Db\Query($sql, [$username]);

        return $db->run();
    }


    /**
     * @param $account_id
     * @return bool
     */
    private function setLoginCookie($username, $uid)
    {
        $days_int   = \Settings::value('login_cookie_expire_days');
        $expire     = date('Y-m-d', strtotime('+' . $days_int . ' day'));
        $domain     = \Settings::value('cookie_domain');

        $login_cookie_value = (new \User\Login())->hashCookie($username, $uid, $expire);

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
     * @param $account_id
     * @param bool $YYYY_MM_DD_expiration_date
     * @return string
     */
    public static function hashCookie($username, $uid, $YYYY_MM_DD_expiration_date = false)
    {
        if (!$YYYY_MM_DD_expiration_date || empty($YYYY_MM_DD_expiration_date)) {
            $YYYY_MM_DD_expiration_date = date('Y-m-d', time());
        }

        return hash('sha512', $username . $uid . $YYYY_MM_DD_expiration_date);
    }
}