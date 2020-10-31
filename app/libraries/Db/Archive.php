<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 10/11/20
 *
 * Archive.php
 *
 * Processes to manage archived records
 *
 **/

namespace Db;

use Exception;
use Log;

class Archive
{
    const DAYS_DEFAULT = 180;

    public function __construct()
    {
    }


    /**
     * @return array
     */
    public function getTables()
    {
        $sql = "
            SELECT
                DISTINCT TABLES.TABLE_NAME
            FROM
                information_schema.TABLES
            INNER JOIN
                information_schema.COLUMNS ON information_schema.TABLES.TABLE_SCHEMA = information_schema.COLUMNS.TABLE_SCHEMA
            WHERE
                COLUMN_NAME = ?
        ";

        $bind = [
            'archived_datetime',
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAll();

        return $results;
    }


    /**
     * @param $table
     * @param $days
     * @return bool
     * @throws Exception
     */
    public function deleteArchivedRecords($table, $days = self::DAYS_DEFAULT)
    {
        $row_count  = (int)0;

        if ($days <= (int)0 || !is_numeric($days)) {
            $days = self::DAYS_DEFAULT;
        }

        if (in_array($table, $this->getTables())) {
            $transaction = new PdoMySql();

            $transaction->beginTransaction();

            try {
                $sql = "
                    DELETE
                        FROM `$table`
                    WHERE
                        archived = '1'
                    AND
                        archived_datetime != ?
                    AND
                        archived_datetime < (NOW() - INTERVAL ? DAY)
                ";

                $bind = [
                    '0000-00-00 00:00:00',
                    $days,
                ];

                $delete = $transaction->prepare($sql);

                if ($delete->execute($bind)) {
                    $row_count = $delete->rowCount();
                }
            } catch (Exception $e) {
                Log::app($e->getCode() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

                $transaction->rollBack();

                return false;
            }
        }

        $transaction->commit();

        Log::app($row_count . ' row' . ($row_count != (int)1 ? 's' : null) . ' deleted');

        return true;
    }
}