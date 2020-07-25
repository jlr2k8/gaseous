<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 7/21/20
 *
 * authors.php
 *
 * Landing page for /users/ and /users/{username}/ - browse promoted user content, such as blog articles
 *
 **/

$user = !empty($_GET['account_username']) ? filter_var($_GET['account_username'], FILTER_SANITIZE_STRING) : false;

