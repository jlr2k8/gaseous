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
use Utilities\Sanitize;

$templator          = new Templator();
$access_code        = !empty($_GET['access_code']) ? Sanitize::string($_GET['access_code']) : null;
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
        $templator->assign('username', Sanitize::string($_POST['username']));
        $templator->assign('firstname', Sanitize::string($_POST['firstname']));
        $templator->assign('lastname', Sanitize::string($_POST['lastname']));
        $templator->assign('email', Sanitize::string($_POST['email']));
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