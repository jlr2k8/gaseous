<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/11/18
 *
 * users.php
 *
 * Role administration
 *
 */

use Content\Pages\Breadcrumbs;
use Content\Pages\HTTP;
use Content\Pages\Templator;
use User\Roles;

// check setting/role privileges

if (!\Settings::value('add_roles') && !\Settings::value('edit_roles') && !\Settings::value('archive_roles')) {
    HTTP::error(401);
}

$templator  = new Templator();
$roles      = new Roles();
$all_roles  = $roles->getAll();
$error      = null;

$role_name_value    = null;
$description_value  = null;

if (!empty($_POST)) {
    foreach($_POST as $key => $val)
        $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);

    if (isset($post['insert'])) {
        $submit_role = $roles->insert($post);
    } elseif (isset($post['update'])) {
        $submit_role = $roles->update($post);
    } elseif (isset($post['archive'])) {
        $submit_role = $roles->archive($post);
    }

    if ($submit_role) {
        header('Location: ' . \Settings::value('full_web_url') . '/admin/roles/');
    } else {
        $error = $roles->getErrors();
    }
}

if (!empty($_SESSION['admin_role_data_submission'])) {
    $role_name_value    = $_SESSION['admin_role_data_submission']['role_name'];
    $description_value  = $_SESSION['admin_role_data_submission']['description'];

    unset($_SESSION['admin_role_data_submission']);
}

$templator->assign('roles', $all_roles);
$templator->assign('error', $error);
$templator->assign('full_web_url',\Settings::value('full_web_url'));
$templator->assign('add_roles', \Settings::value('add_roles'));
$templator->assign('edit_roles', \Settings::value('edit_roles'));
$templator->assign('archive_roles', \Settings::value('archive_roles'));
$templator->assign('role_name', $role_name_value);
$templator->assign('description', $description_value);

$title = 'Roles';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/roles.tpl'),
];

echo Templator::page($page_find_replace);