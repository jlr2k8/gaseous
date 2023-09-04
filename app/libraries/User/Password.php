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
     * @param array $login_form
     * @return string
     */
    public static function verifyPassword(array $login_form)
    {
        $sql = "
            SELECT
                password
            FROM
                account_password
            INNER JOIN
                account ON account_password.account_username = account.username
            WHERE
                account_username = ?
            AND
                account_password.archived = '0'
            AND
                account.archived = '0'
        ";

        $db                 = new Query($sql, [$login_form['username']]);
        $stored_password    = $db->fetch();

        return password_verify($login_form['password'], $stored_password);
    }
}