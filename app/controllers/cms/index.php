<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/12/2018
 *
 * index.php
 *
 * Home page
 *
 **/

use Content\Pages\GetHomePage;

$get = new GetHomePage();

if (in_array($_SERVER['REQUEST_URI'], GetHomePage::$home_pages)) {
    $get->redirectHome(); exit;
}

echo $get->byUri();