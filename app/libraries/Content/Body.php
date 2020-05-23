<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 4/9/20
 *
 * BodyField.php
 *
 * Dynamic content body field
 *
 **/

namespace Content;

use Db\Query;

class BodyField
{
    public function __construct()
    {
    }


    /**
     * @param $content_iteration_uid
     * @return array
     */
    public function getBodyFieldValues($content_iteration_uid)
    {
        $sql = "
            SELECT
                cbf.template_token,
                cbtv.value
            FROM content_body_field_values AS cbtv
            INNER JOIN content_body_fields AS cbf
                ON cbtv.content_body_field_uid = cbf.uid
            INNER JOIN content_body_field_types AS cbft
                ON cbf.content_body_field_type_id = cbft.type_id
            WHERE
                cbtv.content_iteration_uid = ?
            AND
                cbtv.archived = '0'
            AND 
                cbf.archived = '0'
            AND 
                cbft.archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        return $results;
    }
}