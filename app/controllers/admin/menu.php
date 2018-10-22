<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/20/18
 *
 * menu.php
 *
 * Menu links administration
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

// check setting/role privileges
if (!\Settings::value('manage_menu')) {
    \Pages\HTTP::error(401);
}