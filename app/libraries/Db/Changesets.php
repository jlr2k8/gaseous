<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/29/20
 *
 * Changesets.php
 *
 * Database updates
 *
 **/


namespace Db;

use Exception;
use PDO;
use PDOException;
use Settings;

class Changesets
{
    public function __construct()
    {
    }


    /**
     * @param array $need_to_process
     * @return bool
     * @throws Exception
     */
    public function runChangesets(array $need_to_process)
    {
        $sql            = null;
        $transaction    = new PdoMySql();

        $transaction->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $transaction->beginTransaction();

        try {
            if (!empty($need_to_process)) {
                foreach ($need_to_process as $processed) {
                    $sql = $processed['sql'];

                    $transaction
                        ->exec($sql);

                    echo 'Running SQL: ' . PHP_EOL . $sql . PHP_EOL;

                    // We're also going to insert the name of the file processed during the transaction.
                    // If the transaction fails, the file isn't recorded and the SQL from any of the attempted files is not run.
                    $sql = "
                        INSERT INTO
                            changesets (
                                filename,
                                processed_datetime
                            ) VALUES (
                                ?,
                                ?
                            );
                    ";

                    $now    = date('Y-m-d H:i:s');
                    $bind   = [
                        $processed['filename'],
                        $now,
                    ];

                    $transaction
                        ->prepare($sql)
                        ->execute($bind);

                    echo 'Running SQL: ' . PHP_EOL . $sql . PHP_EOL;
                    echo 'Bound with... ' . print_r($bind, true);
                }
            } else {
                echo 'Nothing to process!' . PHP_EOL;
            }

            $transaction->commit();
        } catch (PDOException $e) {
            echo $e->getTraceAsString();
            echo $e->getMessage();

            $transaction->rollBack();

            echo '
                    The unprocessed changesets errored out, so the transaction was rolled back.
                    Please review the stack trace and message above. Visit https://gaseo.us for more information.
                '
                . PHP_EOL;

            return false;
        }

        return true;
    }


    /**
     * @return array
     */
    public function getFileChangesets()
    {
        $changesets_scandir = scandir(WEB_ROOT . '/../db/changesets');
        $file_changesets    = [];

        // Build array of filenames from the changesets directory
        foreach ($changesets_scandir as $changeset_scandir) {
            if ($changeset_scandir == '.' || $changeset_scandir == '..') {
                continue;
            }

            $file_changesets[$changeset_scandir] = $changeset_scandir;
        }

        return $file_changesets;
    }


    /**
     * @param $changeset_starting_point
     * @return array
     */
    public function collectChangesets($changeset_starting_point)
    {
        $file_changesets    = $this->getFileChangesets();

        // We presume they are already sorted alphabetically, but never hurts to double-check
        asort($file_changesets, SORT_ASC);

        $need_to_process    = [];

        // Select all the files from the changesets table, and see if they've ever been processed.
        // If not, we'll concatenate them and try to process the whole string within the transaction.

        // $file_changesets is arranged in order by filename. Remove any changeset prior to the current starting point.
        foreach ($file_changesets as $filename => $sql) {
            if ($file_changesets[$filename] == $changeset_starting_point) {
                break;
            }

            unset($file_changesets[$filename]);
        }

        foreach ($file_changesets as $file_changeset) {
            $sql = "
                    SELECT
                        filename
                    FROM
                        changesets
                    WHERE
                        filename = ?
                    AND
                        archived = '0'
                    ORDER BY
                        processed_datetime;
                ";

            $bind = [
                $file_changeset,
            ];

            $db                     = new Query($sql, $bind);
            $processed_changeset    = $db->fetch();

            if (empty($processed_changeset)) {
                $need_to_process[$file_changeset]['filename']   = $file_changeset;
                $need_to_process[$file_changeset]['sql']        = file_get_contents(WEB_ROOT . '/../db/changesets/' . $file_changeset);
            }
        }

        return $need_to_process;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function changesetTableExists()
    {
        $sql = "
                SELECT COUNT(*)
                FROM
                  information_schema.TABLES
                WHERE
                  table_schema = ?
                AND
                  table_name = ?;
            ";

        $bind = [
            Settings::environmentIni('mysql_database'),
            'changesets',
        ];

        $db     = new Query($sql, $bind);
        $count  = (int)$db->fetch();

        return $count > (int)0;
    }


    /**
     * @return mixed
     */
    public function getLastProcessedChangeset()
    {
        $sql = "
                SELECT
                    MAX(filename) AS latest_processed
                FROM
                    changesets
                WHERE
                    archived = '0'
                ";

        $bind = [];

        $db     = new Query($sql, $bind);
        $result = $db->fetch();

        return $result;
    }
}