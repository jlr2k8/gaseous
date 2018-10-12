<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/11/18
 *
 * users.php
 *
 * User/account administration
 *
 */

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$get_page = new \Pages\Get();
$get_page->enforceSystemPageRoles();

$templator      = new \Pages\Templator();
$account        = new \User\Account();
$roles          = new \User\Roles();
$all_roles      = $roles->getAll();
$all_accounts   = $account->getAll();
$error          = null;

$my_username    = \User\Account::getUsername();

$role_name_value    = null;
$description_value  = null;

if (!empty($_POST)) {

    foreach($_POST as $key => $val) {
        if ($key == 'account_roles') {
            foreach($val as $account_role)
                $post[$key][] = (string)filter_var($account_role, FILTER_SANITIZE_STRING);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    $submit_user = false;

    if (isset($post['update'])) {
        $submit_user = $account->update($post);
    } elseif (isset($post['archive'])) {
        $submit_user = $account->archive($post);
    }

    if ($submit_user) {
        header('Location: ' . \Settings::value('full_web_url') . '/admin/users/');
    } else {
        $error = $account->getErrors();
    }
}

$templator->assign('accounts', $all_accounts);
$templator->assign('roles', $all_roles);
$templator->assign('error', $error);
$templator->assign('my_username', $my_username);
$templator->assign('full_web_url',\Settings::value('full_web_url'));
$templator->assign('access_code', \Settings::value('registration_access_code'));
$templator->assign('edit_users', \Settings::value('edit_users'));

$title = 'Users';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new \Pages\Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/users.tpl'),
];

echo \Pages\Templator::page($page_find_replace);