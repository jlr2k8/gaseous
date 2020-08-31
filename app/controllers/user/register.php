<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/17/2017
 *
 * register.php
 *
 * User registration
 *
 */

use Content\Breadcrumbs;
use Content\Templator;
use User\Account;
use User\Login;
use User\Register;
use User\Roles;

$templator          = new Templator();
$access_code        = !empty($_GET['access_code']) ? (string)filter_var($_GET['access_code'], FILTER_SANITIZE_STRING) : null;
$has_valid_access   = ($access_code == Settings::value('registration_access_code'));
$title              = 'User Registration';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Register'),
];

$body = null;

if (!empty($_POST) && $has_valid_access) {
    $registration   = new Register($_POST);
    $create_account = $registration->createAccount();

    if ($create_account) {
        $login  = new Login();

        $login->checkPostLogin();

        header('Location: ' . Settings::value('full_web_url'));
    } else {
        $templator->assign('errors', $registration->errors);
        $templator->assign('access_code', $access_code);
        $templator->assign('username', (string)filter_var($_POST['username'], FILTER_SANITIZE_STRING));
        $templator->assign('firstname', (string)filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
        $templator->assign('lastname', (string)filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
        $templator->assign('email', (string)filter_var($_POST['email'], FILTER_SANITIZE_STRING));
        $templator->assign('password', $_POST['password']);
        $templator->assign('confirm_password', $_POST['confirm_password']);

        $body = $templator->fetch('user/register.tpl');
    }
} elseif (empty($_POST) && $has_valid_access) {
    $templator->assign('access_code', $access_code);

    $templator->assign('errors', null);
    $templator->assign('access_code', $access_code);
    $templator->assign('username', null);
    $templator->assign('firstname', null);
    $templator->assign('lastname', null);
    $templator->assign('email', null);
    $templator->assign('password', null);
    $templator->assign('confirm_password', null);

    $body = $templator->fetch('user/register.tpl');
} elseif (!$has_valid_access) {
    $body = $templator->fetch('user/access_code_entry.tpl');
}

$page_find_replace['body'] = $body;

echo $templator::page($page_find_replace);