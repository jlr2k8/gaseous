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

use Db\PdoMySql;
use Db\Query;
use Exception;
use Expandable;
use Utilities\Sanitize;

class Route
{
    public      $routing_map;
    protected   $expandable;

    public function __construct()
    {
        $this->expandable = new Expandable();
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
        $real_file      = WEB_ROOT . $path_raw;

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
        } elseif (is_readable($real_file) && is_file($real_file)) {
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
        $results = $this->getAll();

        $this->setRoutingMap($results);

        return $this->expandable->return($results);
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
                description,
                priority_order
            FROM
                uri_routes
            WHERE
                archived = '0'
            ORDER BY
                priority_order;
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $this->expandable->return($results);
    }


    /**
     * @return array
     */
    public function getRoute($uid)
    {
        $sql = "
            SELECT
                uid,
                regex_pattern,
                destination_controller,
                description,
                priority_order
            FROM
                uri_routes
            WHERE
                archived = '0'
            AND 
                uid = ?
            ORDER BY
                priority_order;
        ";

        $bind = [
            $uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $this->expandable->return($result);
    }


    /**
     * @param array $data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public function insert(array $data, PdoMySql $transaction = null)
    {
        $uid                    = !empty($data['uid']) ? Sanitize::string($data['uid']) : Query::getUuid();
        $regex_pattern          = Sanitize::string($data['regex_pattern']);
        $destination_controller = Sanitize::string($data['destination_controller']);
        $description            = Sanitize::string($data['description']);
        $priority_order         = isset($data['priority_order']) ? (int)filter_var($data['priority_order'], FILTER_SANITIZE_NUMBER_INT) : self::getNextAvailablePriority();

        $sql = "
            INSERT INTO
                uri_routes (
                    uid,
                    regex_pattern,
                    destination_controller,
                    description,
                    priority_order
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                );
        ";

        $bind = [
            $uid,
            $regex_pattern,
            $destination_controller,
            $description,
            $priority_order,
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
     * @return bool
     * @throws Exception
     */
    public function update(array $data)
    {
        $uid                    = Sanitize::string($data['uid']);
        $regex_pattern          = Sanitize::string($data['regex_pattern']);
        $destination_controller = Sanitize::string($data['destination_controller']);
        $description            = Sanitize::string($data['description']);
        $priority_order         = !empty($data['priority_order'])
            ? filter_var($data['priority_order'], FILTER_SANITIZE_NUMBER_INT)
            : self::getNextAvailablePriority();

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            $this->archive($uid, $transaction);

            $data = [
                'uid'                       => $uid,
                'regex_pattern'             => $regex_pattern,
                'destination_controller'    => $destination_controller,
                'description'               => $description,
                'priority_order'            => $priority_order,
            ];

            $this->insert($data, $transaction);
        } catch(Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getMessage();

            $this->generateJsonUpsertStatus('status', $e->getMessage());
            $this->checkAndThrowException();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param $uid
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public function archive($uid, PdoMySql $transaction = null)
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
            $db     = new Query($sql, $bind);
            $ran    = $db->run();
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


    /**
     * @param array $priority_to_uuids
     * @return bool
     * @throws Exception
     */
    public function sortPriorityBulk(array $priority_to_uuids)
    {
        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        foreach ($priority_to_uuids as $key => $uid) {
            $key                = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $uid                = Sanitize::string($uid);
            $current_route_data = $this->getRoute($uid);

            $data['uid']                    = $uid;
            $data['regex_pattern']          = $current_route_data['regex_pattern'];
            $data['destination_controller'] = $current_route_data['destination_controller'];
            $data['description']            = $current_route_data['description'];
            $data['priority_order']         = (int)$key;

            try {
                $this->archive($uid, $transaction);
                $this->insert($data, $transaction);
            } catch(Exception $e) {
                $transaction->rollBack();

                $this->errors[] = $e->getMessage();

                $this->generateJsonUpsertStatus('status', $e->getMessage());
                $this->checkAndThrowException();

                return false;
            }
        }

        $transaction->commit();

        return true;
    }


    /**
     * @return int
     */
    public static function getNextAvailablePriority()
    {
        $sql = "
            SELECT
                MAX(priority_order)+1
            FROM
                uri_routes
            WHERE
                archived = '0'
        ";

        $db             = new Query($sql);
        $result         = $db->fetch();
        $priority_order = (int)($result);

        return $priority_order;
    }


    /**
     * Putting Apache .htaccess files in forbidden directories with the "deny all" directive won't work since everything
     * in the app is funneled through the top-level index.php file.
     *
     * @param $path
     * @return bool
     */
    public static function isDisallowedPath($path)
    {
        $is_disallowed      = false;
        $disallowed_paths   = [
            WEB_ROOT . '/setup',
            WEB_ROOT . '/views',
            WEB_ROOT . '/includes',
        ];

        foreach ($disallowed_paths as $dp) {
            if (strpos($path, $dp) !== false) {
                $is_disallowed = true;
            }
        }

        return $is_disallowed;
    }
}