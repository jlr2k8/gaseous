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

use Db\Query;
use Utilities\DateTime;
use Utilities\GetDiff;

class Diff extends GetDiff
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param $content_uid
     * @return array
     */
    function getPageIterations($content_uid)
    {
        $sql = "
            SELECT
              content_uid,
              content_iteration_uid,
              author,
              iteration_description,
              created_datetime
            FROM content_iteration_commits
            WHERE content_uid = ?
            AND archived = '0'
            ORDER BY created_datetime DESC;
        ";

        $bind = [
            $content_uid,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        if (count($results)) {
            foreach ($results as $key => $result) {
                $results[$key]['formatted_created'] = DateTime::formatDateTime($result['created_datetime']);
            }

            return $results;
        } else {
            return [];
        }
    }


    /**
     * @param $content_iteration_uid
     * @return array
     */
    function getPageIteration($content_iteration_uid)
    {
        $sql = "
            SELECT
                content_iteration_uid,
                author,
                iteration_description,
                created_datetime
            FROM content_iteration_commits
            WHERE content_iteration_uid = ?
            AND archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        $result['formatted_created'] = DateTime::formatDateTime($result['created_datetime']);

        return $result;
    }
}