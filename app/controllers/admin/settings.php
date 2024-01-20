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

use \Content\Breadcrumbs;
use \Content\Http;
use \Content\Templator;
use \User\Roles;
use \User\Account;
use Utilities\Sanitize;
use \Wysiwyg\Codemirror;

// check setting/role privileges
if (!Settings::value('edit_settings')) {
    Http::error(403);
}

$settings           = new Settings();
$templator          = new Templator();
$roles              = new Roles();
$account            = new Account();

$all_roles          = $roles->getAll();
$all_settings       = Settings::getAllFromDB();
$setting_categories = $settings->getSettingCategories();

$codemirror         = new Codemirror();

$my_username        = Account::getUsername();

$role_name_value    = null;
$description_value  = null;

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'settings_roles') {
            foreach($val as $account_role)
                $post[$key][] = Sanitize::string($account_role);
        } elseif (!empty($_POST['codemirror']) && $_POST['codemirror'] == 'true' && $key == 'value') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = Sanitize::string($val);
        }
    }

    $submit_setting = false;

    if (isset($post['update'])) {
        $submit_setting = $settings->update($post);
    }

    if ($submit_setting) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/settings/');
    }
}

$templator->assign('roles', $all_roles);
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