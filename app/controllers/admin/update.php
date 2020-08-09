<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 8/9/20
 *
 * update.php
 *
 * Download the latest stable Gaseous source code, and perform updates against the code base and database.
 *
 **/


use Content\Http;

if (!Settings::value('perform_updates')) {
    Http::error(403);
}

$templator      = new Templator();
