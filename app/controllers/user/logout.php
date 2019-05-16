<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 2/5/2017
 *
 * logout.php
 *
 * Logout landing page
 *
 */

$login      = new \User\Login();
$message    = 'You have been successfully logged out!';

if (!$login->logout())
    $message = 'There was an issue logging you out. Try logging out again, or clear your cookies.';

$_SESSION['redir']['desc'] = $message;

header('Location: /login/');

exit;