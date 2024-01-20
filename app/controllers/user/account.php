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

$templator  = new Templator();
$account    = new Account();
$login      = new Login();

if (!$login->checkLogin()) {
    header('Location: ' . Settings::value('full_web_url') . '/login/');
    exit;
}

$title          = 'User Account';
$account_data   = $account->get($_SESSION['account']['username']);

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb($title),
];

$body = null;

if (!empty($_POST)) {
    foreach ($_POST as $key => $val) {
        if ($key == 'email') {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_EMAIL);
        } else {
            $post[$key] = Sanitize::string($val);
        }
    }

    $post['username']   = $_SESSION['account']['username'];
    $update_account     = $account->userUpdate($post);

    if ($update_account) {
        header('Location: ' . Settings::value('full_web_url'));
        exit;
    }
}

$templator->assign('errors', $account->getErrors());
$templator->assign('token_login', $_SESSION['token_login'] ?? false);
$templator->assign('username', Sanitize::string($_POST['username'] ?? $account_data['username'] ?? null));
$templator->assign('firstname', Sanitize::string($_POST['firstname'] ?? $account_data['firstname'] ?? null));
$templator->assign('lastname', Sanitize::string($_POST['lastname'] ?? $account_data['lastname'] ?? null));
$templator->assign('email', Sanitize::string($_POST['email'] ?? $account_data['email'] ?? null));
$templator->assign('password', $_POST['password'] ?? null);
$templator->assign('confirm_password', $_POST['confirm_password'] ?? null);

$body = $templator->fetch('user/account.tpl');

$page_find_replace['body'] = $body;

echo $templator::page($page_find_replace);