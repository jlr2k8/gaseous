<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/17/2017
 *
 * login.php
 *
 * Login page for users
 *
 */

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$templator  = new \Pages\Templator();
$login      = new \User\Login();

$page_find_replace  = [
    'page_title'        => 'Log In',
    'breadcrumbs'       => null,
];

$login_message = null;

if (!empty($_SESSION['redir']['desc'])) {
    $login_message = $_SESSION['redir']['desc'];
    unset($_SESSION['redir']['desc']);
}

$templator->assign('login_message', $login_message);
$templator->assign('recaptcha', \ReCaptcha::draw());

if (!empty($_POST)) {
    $valid_login        = $login->checkPostLogin();
    $recaptcha_required = \Settings::value('require_recaptcha');
    $valid_recaptcha    = true;

    if ($recaptcha_required) {
        $valid_recaptcha    = $login->reCaptcha->validate();
    }

    if ($valid_login && $valid_recaptcha) {
        header('Location: ' . \Settings::value('full_web_url'));
    } elseif($valid_login && !$valid_recaptcha) {
        header('HTTP/1.1 401 Not Authorized');

        $templator->assign('login_message', 'Invalid Recaptcha');
    } else {
        header('HTTP/1.1 401 Not Authorized');

        $templator->assign('login_message', 'Invalid Login');
    }
}

$page_find_replace['body'] = $templator->fetch('user/login.tpl');

echo $templator::page($page_find_replace);