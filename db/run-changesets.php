<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 1/15/19
 *
 * run-changesets.php
 *
 * This file is run as a binary to process any necessary SQL to the database.
 * Every file in the changesets directory (that has not yet been processed) is included in a transaction.
 * If a successful transaction occurs, the filename is added to the changesets table and marked with a relevant
 * datetime.
 *
 **/

$_SERVER['WEB_ROOT'] = __DIR__ . '/../app';

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$changesets_scandir             = scandir($_SERVER['WEB_ROOT'] .'/../db/changesets');
$file_changesets                = [];
$need_to_process                = [];
$db_root                        = $_SERVER['WEB_ROOT'] . '/../db';
$changeset_starting_point       = '20190702-init.sql';

// Build array of filenames from the changesets directory
foreach ($changesets_scandir as $changeset_scandir) {
    if ($changeset_scandir == '.' || $changeset_scandir == '..') {
        continue;
    }

    $file_changesets[$changeset_scandir] = $changeset_scandir;
}

// We presume they are already sorted alphabetically, but never hurts to double-check
asort($file_changesets, SORT_ASC);

// Let's see if we even have the changesets table yet
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
    \Settings::environmentIni('mysql_database'),
    'changesets',
];

$db     = new \Db\Query($sql, $bind);
$count  = $db->fetch();

if ($count < (int)1) {
    echo '
        The changesets table does not exist. Perhaps this is the first ever run for the environment.'
        . PHP_EOL
    ;

    $need_to_process[$changeset_starting_point]['filename']   = $changeset_starting_point;
    $need_to_process[$changeset_starting_point]['sql']        = file_get_contents($db_root . '/changesets/' . $changeset_starting_point);
} else {
    // Select all the files from the changesets table, and see if they've ever been processed.
    // If not, we'll concatenate them and try to process the whole string within the transaction.

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

        $db                     = new \Db\Query($sql, $bind);
        $processed_changeset    = $db->fetch();

        if (empty($processed_changeset)) {
            $need_to_process[$file_changeset]['filename']   = $file_changeset;
            $need_to_process[$file_changeset]['sql']        = file_get_contents($db_root . '/changesets/' . $file_changeset);
        }
    }
}

$sql            = null;
$transaction    = new \Db\PdoMySql();
$bind           = [];

try {
    $transaction->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
    $transaction->beginTransaction();

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
} catch (\PDOException $e) {
    echo $e->getTraceAsString();
    echo $e->getMessage();

    $transaction->rollBack();

    echo '
        The unprocessed changesets errored out, so the transaction was rolled back.
        Please review the stack trace and message above.
        Be sure the SQL in each changesets/*.sql file is properly formatted and named in alphanumeric order.
        HINT: make sure you included the ending semi-colon in each of your SQL statements!
    ' . PHP_EOL;

    exit(1);
}

exit(0);