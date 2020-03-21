<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 9/13/19
 *
 * AdminView.php
 *
 *
 *
 **/

namespace Utilities;

use \Content\Pages\Templator;
use Settings;
use SmartyException;

class AdminView
{
    public function __construct()
    {
    }


    /**
     * @return string
     * @throws SmartyException
     */
    public static function renderAdminList()
    {
        $templator = new Templator;

        $templator->assign('full_web_url', Settings::value('full_web_url'));

        $templator->assign('edit_users', Settings::value('edit_users'));
        $templator->assign('archive_users', Settings::value('archive_users'));

        $templator->assign('add_roles', Settings::value('add_roles'));
        $templator->assign('edit_roles', Settings::value('edit_roles'));
        $templator->assign('archive_roles', Settings::value('archive_roles'));

        $templator->assign('add_pages', Settings::value('add_pages'));
        $templator->assign('edit_pages', Settings::value('edit_pages'));
        $templator->assign('archive_pages', Settings::value('archive_pages'));

        $templator->assign('manage_css', Settings::value('manage_css'));
        $templator->assign('manage_js', Settings::value('manage_js'));

        $templator->assign('edit_settings', Settings::value('edit_settings'));

        $templator->assign('add_redirects', Settings::value('add_redirects'));
        $templator->assign('edit_redirects', Settings::value('edit_redirects'));
        $templator->assign('archive_redirects', Settings::value('archive_redirects'));

        $templator->assign('add_routes', Settings::value('add_routes'));
        $templator->assign('edit_routes', Settings::value('edit_routes'));
        $templator->assign('archive_routes', Settings::value('archive_routes'));

        $templator->assign('add_menu_items', Settings::value('add_menu_items'));
        $templator->assign('manage_menu', Settings::value('manage_menu'));
        $templator->assign('archive_menu_items', Settings::value('archive_menu_items'));

        $view = $templator->fetch('admin/main.tpl');

        return $view;
    }
}