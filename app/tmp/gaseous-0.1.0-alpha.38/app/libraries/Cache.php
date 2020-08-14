<?php

/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/13/20
 *
 * Cache.php
 *
 * Key/value cache management
 *
 **/

use Db\Query;

class Cache
{
    public function __construct()
    {
        $this->archiveExpiredCaches();
    }


    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $sql = "
            SELECT
                `value`
            FROM
                cache
            WHERE
                `key` = ?
            AND 
                archived = '0';
        ";

        $bind = [
            $key,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetch();

        return unserialize($result);
    }


    /**
     * @param $key
     * @param $value
     * @param int $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = 3600)
    {
        $value  = serialize($value);
        $sql    = "
            INSERT INTO
                cache (
                    `key`,
                    `value`,
                    `expires_datetime`
                ) VALUES (
                    ?,
                    ?,
                    DATE_ADD(NOW(), INTERVAL ? SECOND)
                ) ON DUPLICATE KEY UPDATE
                    `value` = ?,
                    `expires_datetime` = DATE_ADD(NOW(), INTERVAL ? SECOND);
        ";

        $bind = [
            $key,
            $value,
            $expiration,
            $value,
            $expiration,
        ];

        $db = new Query($sql, $bind);

        return $db->run();
    }


    /**
     * @param $key
     * @return bool
     */
    public function archive($key)
    {
        $sql = "
            UPDATE
                cache
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
               `key` = ?
            AND
                archived = '0';
        ";

        $bind = [
            $key,
        ];

        $db = new Query($sql, $bind);

        return $db->run();
    }


    /**
     * @param $key
     * @return bool
     */
    public function archiveLike($key, $left_wildcard = true, $right_wildcard = true)
    {
        $sql = "
            UPDATE
                cache
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
               `key` LIKE ?
            AND
                archived = '0';
        ";

        $bind = [
            ($left_wildcard === true ? '%' : null) . $key . ($right_wildcard === true ? '%' : null),
        ];

        $db = new Query($sql, $bind);

        return $db->run();
    }


    /**
     * @return bool
     */
    private function archiveExpiredCaches()
    {
        $sql = "
            UPDATE
                cache
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                expires_datetime < NOW()
            AND
                archived = '0';
        ";

        $db = new Query($sql);

        return $db->run();
    }
}