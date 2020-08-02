<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 5/16/19
 *
 * Redirect.php
 *
 * Manage stored URI redirects
 *
 **/

namespace Uri;

use Content\Get;
use Content\Http;
use Db\PdoMySql;
use Db\Query;
use Exception;
use Settings;

class Redirect
{
    public $errors = [];
    public $json_upsert_status;

    public function __construct()
    {
    }


    /**
     * @param $uri_uid
     * @return array
     */
    public function getByUriUid($uri_uid)
    {
        $sql = "
            SELECT
                uri_uid,
                destination_url,
                http_status_code,
                description
            FROM
                uri_redirects
            WHERE
                uri_uid = ?
            AND
                archived = '0';
        ";

        $bind = [
            $uri_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $result;
    }


    /**
     * @return array
     */
    public function getAll()
    {
        $sql = "
            SELECT
                uri_uid,
                CONCAT(uri, '/') AS uri,
                destination_url,
                http_status_code,
                description
            FROM
                uri_redirects
            INNER JOIN
                uri
            ON 
                uri_redirects.uri_uid = uri.uid
            WHERE
                uri.archived = '0'
            AND
                uri_redirects.archived = '0'
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @return array
     */
    public function getAllNotRedirected()
    {
        $sql = "
            SELECT
                uid,
                uri
            FROM
                uri
            WHERE
                uri.archived = '0'
            AND uid NOT IN (
                SELECT
                    uri_uid
                FROM
                    uri_redirects
                WHERE
                    uri_redirects.archived = '0'
            );
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $uri
     * @return array
     */
    public function getByUri($uri)
    {
        $sql = "
            SELECT
                uri_uid
            FROM
                uri_redirects
            INNER JOIN
                uri
            ON
                uri.uid = uri_redirects.uri_uid
            WHERE
                uri = ?
            AND
                uri.archived = '0'
            AND 
                uri_redirects.archived = '0';
        ";

        $bind = [
            rtrim($uri, '/'),
        ];

        $db         = new Query($sql, $bind);
        $uri_uid    = $db->fetch();
        $result     = $this->getByUriUid($uri_uid);

        return $result;
    }


    /**
     * @param array $data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public function insert(array $data, PdoMySql $transaction = null)
    {
        $uri_uid            = filter_var($data['uri_uid'], FILTER_SANITIZE_STRING);
        $destination_url    = filter_var($data['destination_url'], FILTER_SANITIZE_STRING);
        $http_status_code   = filter_var($data['http_status_code'], FILTER_SANITIZE_STRING);
        $description        = filter_var($data['description'], FILTER_SANITIZE_STRING);

        $sql = "
            INSERT INTO
                uri_redirects (
                     uri_uid,
                     destination_url,
                     http_status_code,
                     description
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?
                );
        ";

        $bind = [
            $uri_uid,
            $destination_url,
            $http_status_code,
            $description,
        ];

        if (empty($transaction)) {
            $db     = new Query($sql, $bind);
            $ran    = $db->run();
        } else {
            $ran = $transaction
                ->prepare($sql)
                ->execute($bind);
        }

        return $ran;
    }


    /**
     * @param array $data
     * @param PdoMySql $transaction
     * @return bool
     * @throws Exception
     */
    public function update(array $data, PdoMySql $transaction)
    {
        $uri_uid            = filter_var($data['redirect_uri_uid'] ?? $data['uri_uid'], FILTER_SANITIZE_STRING);
        $destination_url    = filter_var($data['destination_url'], FILTER_SANITIZE_STRING);
        $http_status_code   = filter_var($data['http_status_code'], FILTER_SANITIZE_STRING);
        $description        = filter_var($data['description'], FILTER_SANITIZE_STRING);

        $this->archive($uri_uid, $transaction);

        $data = [
            'uri_uid'           => $uri_uid,
            'destination_url'   => $destination_url,
            'http_status_code'  => $http_status_code,
            'description'       => $description,
        ];

        return $this->insert($data, $transaction);
    }


    /**
     * @param $uri_uid
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public function archive($uri_uid, PdoMySql $transaction = null)
    {
        $sql = "
            UPDATE
                uri_redirects
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                uri_uid = ?
            AND
                archived = '0';
        ";

        $bind = [
            $uri_uid,
        ];

        if (empty($transaction)) {
            $db         = new Query($sql, $bind);
            $ran        = $db->run();
        } else {
            $ran    = $transaction
                ->prepare($sql)
                ->execute($bind);
        }

        return $ran;
    }


    /**
     * @param array $parsed_uri
     * @return bool
     * @throws Exception
     */
    public function properUri($uri)
    {
        $parsed_uri     = parse_url($uri);
        $current_uri    = $parsed_uri['path'];
        $querystring    = !empty($parsed_uri['query']) ? '?' . $parsed_uri['query'] : null;

        if (empty($current_uri) || in_array($current_uri, Get::$home_pages)) {
            Http::redirect(Settings::value('web_uri') . '/' . $querystring, 301);
            exit;
        }

        if (!empty($querystring) && Settings::value('relative_uri') != $current_uri . $querystring) {
            Http::redirect($current_uri . $querystring, 301);
            exit;
        }

        if (empty($querystring) && substr($current_uri, -1) != '/') {
            Http::redirect(rtrim(Settings::value('relative_uri'), '/') . '/' . $querystring, 301);
            exit;
        }

        return false;
    }


    /**
     * @param $status
     * @param $message
     * @return bool
     */
    private function generateJsonUpsertStatus($status, $message)
    {
        $status_message = [
            $status => $message,
        ];

        $this->json_upsert_status = json_encode($status_message);

        return true;
    }


    /**
     * @throws Exception
     */
    private function checkAndThrowException()
    {
        if (!empty($this->errors)) {
            $errors = implode('; ', $this->errors);

            throw new Exception($errors);
        }

        return true;
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return implode('; ', $this->errors);
    }
}