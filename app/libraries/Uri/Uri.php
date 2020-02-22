<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/22/20
 *
 * Uri.php
 *
 * Manage URIs (for redirects and CMS pages)
 *
 **/

namespace Uri;

use Db\PdoMySql;
use Db\Query;

class Uri
{
    public $transaction;

    public function __construct(PdoMySql $transaction = null)
    {
        $this->transaction  = $transaction ?? new PdoMySql();
    }


    /**
     * @param $uri
     * @return bool
     */
    public function insertUri($uri)
    {
        $sql = "
            INSERT INTO uri (uri)
            VALUES(?);
        ";

        $bind = [rtrim($uri, '/')];

        $this
            ->transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param $uri
     * @return mixed
     */
    public function getUriUid($uri)
    {
        $sql = "
            SELECT uid
            FROM uri
            WHERE uri = ?
        ";

        $bind   = [rtrim($uri, '/')];
        $result = $this
            ->transaction
            ->prepare($sql);

        $result->execute($bind);

        return $result->fetchColumn();
    }


    /**
     * @param $uri
     * @return bool
     */
    public static function uriExistsAsPage($uri)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                uri
            INNER JOIN
                page
            ON
                page.uri_uid = uri.uid
            WHERE
                uri = ?
            AND
                uri.archived = '0'
            AND
                page.archived = '0';
        ";

        $db     = new Query($sql, [$uri]);
        $count  = $db->fetch();

        return ($count > 0);
    }


    /**
     * @param $uri
     * @return bool
     */
    public static function uriExistsAsRedirect($uri)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                uri
            INNER JOIN
                uri_redirects
            ON
                uri_redirects.uri_uid = uri.uid
            WHERE
                uri = ?
            AND
                uri.archived = '0'
            AND
                uri_redirects.archived = '0';
        ";

        $db     = new Query($sql, [$uri]);
        $count  = $db->fetch();

        return ($count > 0);
    }
}