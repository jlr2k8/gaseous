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

use Content\Http;
use Content\Templator;
use User\Login;

$templator          = new Templator();
$login              = new Login();
$smtp_host_is_set   = !empty(Settings::value('smtp_host'));

$templator->assign('smtp_host_is_set', $smtp_host_is_set);

$page_find_replace  = [
    'page_title_seo'    => 'Log In',
    'breadcrumbs'       => null,
];

$login_message = null;

if (!empty($_SESSION['redir']['desc'])) {
    $login_message = $_SESSION['redir']['desc'];
    unset($_SESSION['redir']['desc']);
}

$templator->assign('login_message', $login_message);
$templator->assign('recaptcha', ReCaptcha::draw());

if (!empty($_POST) && !isset($_GET['forgot_password'])) {
    $valid_login        = $login->checkPostLogin();
    $recaptcha_required = Settings::value('require_recaptcha');
    $valid_recaptcha    = true;

    if ($recaptcha_required) {
        $valid_recaptcha    = $login->reCaptcha->validate();
    }

    if ($valid_login && $valid_recaptcha) {
        Http::redirect(Settings::value('full_web_url'));
    } elseif($valid_login && !$valid_recaptcha) {
        Http::header(403);

        $templator->assign('login_message', 'Invalid Recaptcha');
    } else {
        Http::header(403);

        $templator->assign('login_message', 'Invalid Login');
    }
} elseif (!empty($_GET['token'])) {
    $valid_token = $login->checkTokenLogin();

    if ($valid_token) {
        header('Location: ' . Settings::value('full_web_url'));
    }
} elseif (!empty($_GET['forgot_password'])) {
    $email          = filter_var($_POST['registered_email'], FILTER_SANITIZE_EMAIL);
    $valid_token    = $login->processTokenEmail($email);

    $templator->assign('login_message', 'Forgotten password request is complete. If the email address is valid, you should receive an email shortly with next steps.');
}

$page_find_replace['body'] = $templator->fetch('user/login.tpl');

echo $templator::page($page_find_replace);