<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/6/18
 *
 * css_preview_check.php
 *
 * Simple endpoint to determine if $_SESSION['css_preview'] is set.
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

echo !empty($_SESSION['css_preview']) ? '1' : '0';