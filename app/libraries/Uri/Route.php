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

    public static $uri_routes = [
        '/register/?'                   => 'controllers/user/register.php',
        '/login/?'                      => 'controllers/user/login.php',
        '/admin/settings/?'             => 'controllers/admin/settings.php',
        '/sitemap.xml'                  => 'controllers/services/sitemap_output.php',
        '/styles.gz.css'                => 'controllers/services/css_output.php',
        '/js.gz.js'                     => 'controllers/services/js_output.php',
        '/robots.txt'                   => 'controllers/services/robots.txt.php',
        '/css-preview-check/?'          => 'controllers/services/css_preview_check.php',
        '/js-preview-check/?'           => 'controllers/services/js_preview_check.php',
        '/logout/?'                     => 'controllers/user/logout.php',
        '/img/(.*)'                     => 'controllers/services/images.php?src=$1',
        '/register/([\\w]+)/?'          => 'controllers/user/register.php?access_code=$1',
        '/admin/?'                      => 'controllers/admin/index.php',
        '/admin/css/?'                  => 'controllers/admin/css.php',
        '/admin/roles/?'                => 'controllers/admin/roles.php',
        '/admin/menu/?'                 => 'controllers/admin/menu.php',
        '/admin/routes/?'               => 'controllers/admin/routes.php',
        '/admin/pages/?'                => 'controllers/admin/pages.php',
        '/admin/users/?'                => 'controllers/admin/users.php',
        '/admin/js/?'                   => 'controllers/admin/js.php',
        '/admin/redirects/?'            => 'controllers/admin/redirects.php',
        '/([\\w\\/\\-]+(\\.html)?)?'    => 'controllers/cms/index.php?page=$1',
    ];


    public function __construct()
    {
        $this->getUriRoutes();
    }


    /***
     * TODO (next three functions)
     * Using the arrays above, a script can be written to replace any routes that may have been removed from the database.
     ***/

    public function resetRoutes()
    {

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
                description,
                priority_order
            FROM
                uri_routes
            WHERE
                archived = '0'
            ORDER BY
                priority_order;
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
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

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $result;
    }


    /**
     * @param array $data
     * @param \Db\PdoMySql|null $transaction
     * @return bool
     */
    public function insert(array $data, \Db\PdoMySql $transaction = null)
    {
        $uid                    = !empty($data['uid']) ? filter_var($data['uid'], FILTER_SANITIZE_STRING) : \Db\Query::getUuid();
        $regex_pattern          = filter_var($data['regex_pattern'], FILTER_SANITIZE_STRING);
        $destination_controller = filter_var($data['destination_controller'], FILTER_SANITIZE_STRING);
        $description            = filter_var($data['description'], FILTER_SANITIZE_STRING);
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
            $db     = new \Db\Query($sql, $bind);
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
     * @throws \Exception
     */
    public function update(array $data)
    {
        $uid                    = filter_var($data['uid'], FILTER_SANITIZE_STRING);
        $regex_pattern          = filter_var($data['regex_pattern'], FILTER_SANITIZE_STRING);
        $destination_controller = filter_var($data['destination_controller'], FILTER_SANITIZE_STRING);
        $description            = filter_var($data['description'], FILTER_SANITIZE_STRING);
        $priority_order         = !empty($data['priority_order'])
            ? filter_var($data['priority_order'], FILTER_SANITIZE_NUMBER_INT)
            : self::getNextAvailablePriority();

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {
            $this->archive($uid, $transaction);

            $data = [
                'regex_pattern'             => $regex_pattern,
                'destination_controller'    => $destination_controller,
                'description'               => $description,
                'priority_order'            => $priority_order,
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
            $db     = new \Db\Query($sql, $bind);
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


    /**
     * @param array $priority_to_uuids
     * @return bool
     * @throws \Exception
     */
    public function sortPriorityBulk(array $priority_to_uuids)
    {
        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        foreach ($priority_to_uuids as $key => $uid) {
            $key                = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $uid                = filter_var($uid, FILTER_SANITIZE_STRING);
            $current_route_data = $this->getRoute($uid);

            $data['uid']                    = $uid;
            $data['regex_pattern']          = $current_route_data['regex_pattern'];
            $data['destination_controller'] = $current_route_data['destination_controller'];
            $data['description']            = $current_route_data['description'];
            $data['priority_order']         = (int)$key;

            try {
                $this->archive($uid, $transaction);
                $this->insert($data, $transaction);
            } catch(\ErrorException $e) {
                $transaction->rollBack();

                $this->errors[] = $e->getMessage();

                $this->generateJsonUpsertStatus('status', $e->getMessage());
                $this->checkAndThrowErrorException();

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
                priority_order
            FROM
                uri_routes
            WHERE
                archived = '0'
            ORDER BY priority_order DESC
            LIMIT 1;
        ";

        $db     = new \Db\Query($sql);
        $result = $db->fetch();

        // now increment that result by +1
        $priority_order = (int)($result+1);

        return (int)$priority_order;
    }

}