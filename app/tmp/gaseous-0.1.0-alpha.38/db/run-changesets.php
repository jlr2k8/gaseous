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

use Db\Changesets;

require_once __DIR__ . '/../app/setup/init.php';

$changeset_starting_point   = '20200202-init.sql';
$changesets_obj             = new Changesets();

// Let's see if we even have the changesets table yet
$changeset_table_exists = $changesets_obj->changesetTableExists();

if (!$changeset_table_exists) {
    echo '
        The changesets table does not exist. Perhaps this is the first ever run for the environment.'
        . PHP_EOL
    ;

    $need_to_process[$changeset_starting_point]['filename']   = $changeset_starting_point;
    $need_to_process[$changeset_starting_point]['sql']        = file_get_contents(__DIR__ . '/../db/changesets/' . $changeset_starting_point);

    $ran_initial_changesets = $changesets_obj->runChangesets($need_to_process);
    $ran_changesets         = false;

    if ($ran_initial_changesets) {
        $need_to_process    = $changesets_obj->collectChangesets($changeset_starting_point);
        $ran_changesets     = $changesets_obj->runChangesets($need_to_process);
    }
} else {
    $changeset_starting_point   = $changesets_obj->getLastProcessedChangeset() ?: $changeset_starting_point;
    $need_to_process            = $changesets_obj->collectChangesets($changeset_starting_point);
    $ran_changesets             = $changesets_obj->runChangesets($need_to_process);
}

if (PHP_SAPI == 'cli') {
    exit($ran_changesets ? 0 : 1);
}