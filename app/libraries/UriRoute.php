<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 5/12/19
 *
 * UriRoute.php
 *
 * Map URI pattern to database-stored controllers
 *
 **/

class UriRoute
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

        $matched_uri_pattern    = $this->matchUriRegex($path_raw);
        $path                   = null;
        $query                  = [];

        if (!empty($matched_uri_pattern)) {
            $parse_url  = parse_url($matched_uri_pattern);
            $path       = (string)filter_var($parse_url['path'], FILTER_SANITIZE_URL);

            if (!empty($parse_url['query'])) {
                parse_str($parse_url['query'], $query);
            }
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
}