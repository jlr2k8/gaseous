<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/16/18
 *
 * Diff.php
 *
 * Get page iteration diffs
 *
 **/

namespace Content\Pages;

class Diff extends \Utilities\GetDiff
{
    public function __construct()
    {
    }


    /**
     * @param $page_master_uid
     * @return array
     */
    function getPageIterations($page_master_uid)
    {
        $sql = "
            SELECT
              page_master_uid,
              page_iteration_uid,
              author,
              iteration_description,
              created_datetime
            FROM page_iteration_commits
            WHERE page_master_uid = ?
            AND archived = '0'
            ORDER BY created_datetime DESC;
        ";

        $bind = [
            $page_master_uid,
        ];

        $db         = new \Db\Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        if (count($results)) {
            foreach ($results as $key => $result) {
                $results[$key]['formatted_created'] = \Utilities\DateTime::formatDateTime($result['created_datetime']);
            }

            return $results;
        } else {
            return [];
        }
    }


    /**
     * @param $page_iteration_uid
     * @return array
     */
    function getPageIteration($page_iteration_uid)
    {
        $sql = "
            SELECT
                page_iteration_uid,
                author,
                iteration_description,
                created_datetime
            FROM page_iteration_commits
            WHERE page_iteration_uid = ?
            AND archived = '0';
        ";

        $bind = [
            $page_iteration_uid,
        ];

        $db     = new \Db\Query($sql, $bind);
        $result = $db->fetchAssoc();

        $result['formatted_created'] = \Utilities\DateTime::formatDateTime($result['created_datetime']);

        return $result;
    }
}