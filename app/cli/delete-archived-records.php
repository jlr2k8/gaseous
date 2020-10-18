<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 10/13/20
 *
 * delete-archived-records.php
 *
 * Service to delete archived records after x days (where x represents settings value "delete_archived_records_days")
 * CLI only!
 **/

use Db\Archive;

require_once __DIR__ . '/../../app/setup/init.php';

if (PHP_SAPI != 'cli') {
    echo 'This service can only be run via command line...';

    exit(1);
}

$archive    = new Archive();
$tables     = $archive->getTables();
$days       = (int)Settings::value('delete_archived_records_days') ?: Archive::DAYS_DEFAULT;

Log::app('Checking for records that were archived ' . $days . ' day' . ($days != (int)1 ? 's' : null) . ' ago or earlier...');

foreach ($tables as $table) {
    Log::app('Deleting archived records for ' . $table);

    $archive->deleteArchivedRecords($table, $days);
}

Log::app('Done!');