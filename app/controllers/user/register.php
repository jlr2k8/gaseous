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

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$templator          = new \Content\Pages\Templator();
$access_code        = !empty($_GET['access_code']) ? (string)filter_var($_GET['access_code'], FILTER_SANITIZE_STRING) : false;
$has_valid_access   = ($access_code && $access_code == \Settings::value('registration_access_code'));

$page_find_replace = [
    'page_title'    => 'User Registration',
    'breadcrumbs'   => (new \Content\Pages\Breadcrumbs())->crumb('Register'),
];

$body = null;

if (!empty($_POST) && $has_valid_access) {

    $registration   = new \User\Register($_POST);
    $create_account = $registration->createAccount();

    if ($create_account) {
        $registration_redir = \Settings::value('full_web_url');

        if (!empty($_SESSION['registration_redirect'])) {

            $registration_redir = $_SESSION['registration_redirect'];

            unset($_SESSION['registration_redirect']);
        }

        header('Location: ' . $registration_redir);
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

$account = new \User\Account();
$account->getAll();

echo $templator::page($page_find_replace);