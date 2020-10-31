<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 10/17/20
 *
 * run-db-changesets.php
 *
 * Wrapper for service db/run-changesets.php
 *
 **/


require_once __DIR__ . '/../../app/setup/init.php';

if (PHP_SAPI != 'cli') {
    echo 'This service can only be run via command line...';

    exit(1);
}

require_once __DIR__ . '/../../db/run-changesets.php';