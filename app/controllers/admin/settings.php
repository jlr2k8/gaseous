<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/11/18
 *
 * settings.php
 *
 * Settings administration
 *
 */

use \Content\Pages\Breadcrumbs;
use \Content\Pages\HTTP;
use \Content\Pages\Templator;
use \User\Roles;
use \User\Account;
use \Wysiwyg\Codemirror;

// check setting/role privileges
if (!Settings::value('edit_settings')) {
    HTTP::error(401);
}

$settings           = new Settings();
$templator          = new Templator();
$roles              = new Roles();
$account            = new Account();

$all_roles          = $roles->getAll();
$all_settings       = Settings::getAllFromDB();
$all_accounts       = $account->getAll();
$setting_categories = $settings->getSettingCategories();

$codemirror         = new Codemirror();

$my_username        = Account::getUsername();

$role_name_value    = null;
$description_value  = null;
$error              = null;

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'settings_roles') {
            foreach($val as $account_role)
                $post[$key][] = (string)filter_var($account_role, FILTER_SANITIZE_STRING);
        } elseif (!empty($_POST['codemirror']) && $_POST['codemirror'] == 'true' && $key == 'value') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    $submit_setting = false;

    if (isset($post['update'])) {
        $submit_setting = $settings->update($post);
    }

    if ($submit_setting) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/settings/');
    } else {
        $error = $account->getErrors();
    }
}

$templator->assign('accounts', $all_accounts);
$templator->assign('roles', $all_roles);
$templator->assign('error', $error);
$templator->assign('settings', $all_settings);
$templator->assign('my_username', $my_username);
$templator->assign('full_web_url', Settings::value('full_web_url'));
$templator->assign('setting_categories', $setting_categories);
$templator->assign('edit_settings', Settings::value('edit_settings'));
$templator->assign('edit_roles', Settings::value('edit_roles'));
$templator->assign('archive_roles', Settings::value('archive_roles'));
$templator->assign('codemirror', $codemirror);

$title = 'Site Settings';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/settings.tpl'),
];

echo Templator::page($page_find_replace);