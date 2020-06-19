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
        var_dump($uri);
        $sql = "
            INSERT INTO uri (uri)
            VALUES(?);
        ";

        $bind = [rtrim($uri, '/')];

        return $this->transaction
            ->prepare($sql)
            ->execute($bind);
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
            AND archived = '0';
        ";

        $bind   = [
            rtrim($uri, '/')
        ];

        if (!empty($this->transaction)) {
            $result = $this
                ->transaction
                ->prepare($sql);

            $result->execute($bind);

            $uid = $result->fetchColumn();
        } else {
            $db     = new Query($sql, $bind);
            $uid    = $db->fetch();
        }

        return $uid;
    }


    /**
     * @param $content_uid
     * @return string|null
     */
    public function getUri($content_uid)
    {
        $sql = "
            SELECT uri
            FROM uri
            INNER JOIN content ON content.uri_uid = uri.uid
            WHERE content.uid = ?
            AND content.archived = '0'
            AND uri.archived = '0';
        ";

        $bind = [
            $content_uid,
        ];

        $db     = new Query($sql, $bind);
        $uri    = $db->fetch();

        return $uri;
    }


    /**
     * @param $uri
     * @return int
     */
    public static function uriExists($uri)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                uri
            WHERE
                uri = ?
            AND
                archived = '0';
        ";

        $db     = new Query($sql, [$uri]);
        $count  = $db->fetch();

        return (int)($count > 0);
    }


    /**
     * @param $uri
     * @return bool
     */
    public static function uriExistsAsContent($uri)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                uri
            INNER JOIN
                content
            ON
                content.uri_uid = uri.uid
            WHERE
                uri = ?
            AND
                uri.archived = '0'
            AND
                content.archived = '0';
        ";

        $db     = new Query($sql, [$uri]);
        $count  = $db->fetch();

        return (int)($count > 0);
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

        return (int)($count > 0);
    }


    /**
     * @return array|bool
     */
    public static function all()
    {
        $sql = "
          SELECT uri.uid, uri.uri
          FROM uri
          INNER JOIN content ON content.uri_uid = uri.uid
          WHERE uri.archived = '0'
          AND content.archived = '0'
          ORDER BY uri.uri ASC
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }
}