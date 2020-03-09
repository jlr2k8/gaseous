<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/6/2017
 *
 * password.php
 *
 * Password and salt/pepper management
 *
 */

namespace User;

use Db\Query;

class Password
{
    public function __construct()
    {
    }


    /**
     * @param $raw_password
     * @return string
     */
    public static function hashPassword($raw_password)
    {
        return password_hash($raw_password, PASSWORD_DEFAULT);
    }


    /**
     * @param $raw_password
     * @return string
     */
    public static function verifyPassword(array $login_form)
    {
        $sql = "
            SELECT password FROM account_password
            WHERE account_username = ?
        ";

        $db                 = new Query($sql, [$login_form['username']]);
        $stored_password    = $db->fetch();

        return password_verify($login_form['password'], $stored_password);
    }


    /**
     * TODO - implement "reset password" functionality
     *
     * @param array $result
     * @param $raw_password
     * @param $salt
     * @return bool
     */
    public static function resetPassword(array $result, $raw_password)
    {
        // sanity check
        if (empty($result) || !isset($result['account_username'])) {
            return false;
        }

        $new_password   = self::hashPassword($raw_password);
        $sql            = "UPDATE account_password SET password = ? WHERE account_username = ? AND password != ? AND archived='0'";
        $db             = new Query($sql, [$new_password, $result['account_id'], $new_password]);

        return $db->run();
    }


    /**
     * NOTE - unused until we change up password hashing algorithm
     *
     * @param $username
     * @return bool|false|string
     */
    public static function lastModified($username)
    {
        $sql = "
            SELECT account_password.modified_datetime FROM account_password
			INNER JOIN account ON account_password..account_username = account.username
			WHERE username= ? AND archived='0'
        ";

        $db     = new Query($sql, [$username]);
        $result = $db->fetchAssoc();

        return !empty($result['modified_datetime']) ? date('YmdHis', strtotime($result['modified_datetime'])) : false;
    }
}