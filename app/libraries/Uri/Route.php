<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 5/12/19
 *
 * UriRoute.php
 *
 * Map URI pattern to database-stored controllers/endpoints
 *
 **/

namespace Uri;

class Route
{
    public $routing_map;

    public function __construct()
    {
        $this->getUriRoutes();
    }


    /**
     * @param $uri
     * @return array
     */
    public function parseUri($uri)
    {
        $parsed_uri_raw = parse_url($uri);
        $path_raw       = $parsed_uri_raw['path'];
        $real_file      = $_SERVER['WEB_ROOT'] . $path_raw;

        // requests to index.php should go to an empty path
        if ($path_raw == '/index.php') {
            $path_raw = '/';
        }

        $matched_uri_pattern    = $this->matchUriRegex($path_raw);
        $path                   = null;
        $query                  = [];

        if (!empty($matched_uri_pattern)) {
            $parse_url  = parse_url($matched_uri_pattern);
            $path       = (string)filter_var($parse_url['path'], FILTER_SANITIZE_URL);

            // convert querystring to array, if present
            if (!empty($parse_url['query'])) {
                parse_str($parse_url['query'], $query);
            }
        } elseif (is_readable($real_file)) {
            /*
             * NOTE
             * if the path exists as a physical file on the server, try that next. that means, in this world, routes
             * override files/endpoints that physically exist (for better or worse, that's the current logic)
             */
            $path = $real_file;
        }

        $parsed_uri = [
            'path'  => $path,
            'query' => $query,
        ];

        return $parsed_uri;
    }


    /**
     * @param $uri
     * @return string|string[]|null
     */
    public function matchUriRegex($uri)
    {
        $match              = null;
        $uri_routing_map    = $this->routing_map;

        /*
         * instead of providing preg_replace() with the array keys/values of $uri_mapping_regex as arrays,
         * we need to loop through the array one at a time to preserve priority order
        */
        foreach ($uri_routing_map as $key => $val) {
            // wrap the key with delimiter and start/end line matching
            $wrapped_key        = '~^' . $key . '$~';
            $matches_pattern    = preg_match($wrapped_key, $uri);

            if ($matches_pattern) {
                $match  = preg_replace($wrapped_key, $val, $uri);

                break;
            }
        }

        return $match;
    }


    /**
     * @return array
     */
    private function getUriRoutes()
    {
        $sql = "
            SELECT DISTINCT
                uid,
                regex_pattern,
                destination_controller,
                description,
                priority_order
            FROM
                uri_routes
            WHERE
                archived = '0'
            ORDER BY
                priority_order ASC;
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        $this->setRoutingMap($results);

        return $results;
    }


    /**
     * @param array $uri_routes
     * @return bool
     */
    private function setRoutingMap(array $uri_routes)
    {
        foreach ($uri_routes as $route)
        {
            $this->routing_map[$route['regex_pattern']] = $route['destination_controller'];
        }

        return true;
    }


    /**
     * @return array
     */
    public function getAll()
    {
        $sql = "
            SELECT
                uid,
                regex_pattern,
                destination_controller,
                description
            FROM
                uri_routes
            WHERE
                archived = '0'
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param array $data
     * @param \Db\PdoMySql|null $transaction
     * @return bool
     */
    public function insert(array $data, \Db\PdoMySql $transaction = null)
    {
        $regex_pattern          = filter_var($data['regex_pattern'], FILTER_SANITIZE_STRING);
        $destination_controller = filter_var($data['destination_controller'], FILTER_SANITIZE_STRING);
        $description            = filter_var($data['description'], FILTER_SANITIZE_STRING);

        $sql = "
            INSERT INTO
                uri_routes (
                    regex_pattern,
                    destination_controller,
                    description
                ) VALUES (
                    ?,
                    ?,
                    ?
                );
        ";

        $bind = [
            $regex_pattern,
            $destination_controller,
            $description,
        ];

        if (empty($transaction)) {
            $db         = new \Db\Query($sql, $bind);
            $ran        = $db->run();
        } else {
            $ran = $transaction
                ->prepare($sql)
                ->execute($bind);
        }

        return $ran;
    }


    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function update(array $data)
    {
        $uid                    = filter_var($data['uid'], FILTER_SANITIZE_STRING);
        $regex_pattern          = filter_var($data['regex_pattern'], FILTER_SANITIZE_STRING);
        $destination_controller = filter_var($data['destination_controller'], FILTER_SANITIZE_STRING);
        $description            = filter_var($data['description'], FILTER_SANITIZE_STRING);

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {
            $this->archive($uid, $transaction);

            $data = [
                'regex_pattern'     => $regex_pattern,
                'destination_controller'   => $destination_controller,
                'description'       => $description,
            ];

            $this->insert($data, $transaction);
        } catch(\ErrorException $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getMessage();

            $this->generateJsonUpsertStatus('status', $e->getMessage());
            $this->checkAndThrowErrorException();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param $uid
     * @param \Db\PdoMySql|null $transaction
     * @return bool
     */
    public function archive($uid, \Db\PdoMySql $transaction = null)
    {
        $sql = "
            UPDATE
                uri_routes
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                uid = ?
            AND
                archived = '0';
        ";

        $bind = [
            $uid,
        ];

        if (empty($transaction)) {
            $db         = new \Db\Query($sql, $bind);
            $ran        = $db->run();
        } else {
            $ran    = $transaction
                ->prepare($sql)
                ->execute($bind);
        }

        return $ran;
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
     * @throws \Exception
     */
    private function checkAndThrowErrorException()
    {
        if (!empty($this->errors)) {
            $errors = implode('; ', $this->errors);

            throw new \ErrorException($errors);
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