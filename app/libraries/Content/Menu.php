<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 3/13/20
 *
 * Menu.php
 *
 * Menu administration and rendering class
 *
 **/

namespace Content;

use Content\Templator;
use Db\PdoMySql;
use Db\Query;
use Exception;
use Log;
use Settings;
use Utilities\Sanitize;

class Menu
{
    public $admin   = false;
    public $errors  = [];

    public function __construct()
    {
    }


    /**
     * @throws Exception
     */
    public static function editMenuCheck()
    {
        if (!Settings::value('manage_menu'))
            throw new Exception('Editing menu items is not allowed');
    }


    /**
     * @return bool|string
     * @throws \SmartyException
     */
    public function renderMenu()
    {
        $templator  = new Templator();
        $menu       = $this->getMenu();

        $templator->assign('menu', $menu);
        $templator->assign('admin', $this->admin);

        // Recursive template
        $output = $templator->fetch('common/menu.tpl');

        return $output;
    }


    /**
     * @param $form_data_serialized
     * @return bool
     * @throws Exception
     */
    public function processMenu($form_data_serialized)
    {
        self::editMenuCheck();

        parse_str($form_data_serialized, $form_data);

        $i              = (int)0;
        $transaction    = new PdoMySql();

        $transaction->beginTransaction();

        try {
            foreach ($form_data['menu-item'] as $uid => $parent_uid) {
                $i++;
                $menu_item[$i]  = $this->getMenuItem($uid);

                if ($this->archiveMenuItem($uid, $transaction)) {
                    $label      = Sanitize::string($menu_item[$i]['label']);
                    $uri_uid    = Sanitize::string($menu_item[$i]['uri_uid']);
                    $nofollow   = Sanitize::string($menu_item[$i]['nofollow']);
                    $target     = Sanitize::string($menu_item[$i]['target']);
                    $sort_order = filter_var($i, FILTER_SANITIZE_NUMBER_INT);
                    $class      = Sanitize::string($menu_item[$i]['class']);
                    $uid        = Sanitize::string($uid);
                    $parent_uid = ($parent_uid == 'null')
                        ? null
                        : Sanitize::string($parent_uid);

                    $this->insertMenuItem($label, $uri_uid, $nofollow, $target, $class, $sort_order, $uid, $parent_uid, $transaction);
                }
            }
        } catch (Exception $e) {
            Log::app('Failed to insert menu items!', $e->getMessage(), $e->getTraceAsString());

            $transaction->rollBack();

            throw $e;
        }

        return $transaction->commit();
    }


    /**
     * @param null $parent_uid
     * @return array
     */
    public function getMenu($parent_uid = null)
    {
        $sql = "
            SELECT
                menu.uid,
                parent_uid,
                sort_order,
                nofollow,
                target,
                class,
                label,
                uri_uid,
                uri,
                menu.archived,
                menu.archived_datetime
            FROM menu
            LEFT JOIN uri
                ON uri.uid = menu.uri_uid
            WHERE menu.archived = '0'
            AND (uri.archived = '0' OR uri.archived IS NULL)
        ";

        $bind = [];

        if (!empty($parent_uid)) {
            $sql .= "
                AND parent_uid = ?
            ";

            $bind[] = $parent_uid;
        } else {
            $sql .= "
                AND parent_uid IS NULL
            ";
        }

        $sql .= "
            ORDER BY sort_order;
        ";

        $db     = new Query($sql, $bind);
        $menu   = $db->fetchAllAssoc();

        foreach ($menu as $key => $item) {
            $menu[$key]['uid']      = str_replace('-', '', $item['uid']);
            $menu[$key]['children'] = $this->getMenu($item['uid']);
            $menu[$key]['uri']      = \Settings::value('full_web_url') . $item['uri'] . '/';
        }

        return $menu;
    }


    /**
     * This is similar to Menu::getMenu(), except it returns a flat array
     *
     * @return array
     */
    public function getMenuItems()
    {
        $sql = "
            SELECT
                menu.uid,
                parent_uid,
                sort_order,
                nofollow,
                target,
                class,
                label,
                uri_uid,
                uri,
                menu.archived,
                menu.archived_datetime
            FROM menu
            LEFT JOIN uri
                ON uri.uid = menu.uri_uid
            WHERE menu.archived = '0'
            AND (uri.archived = '0' OR uri.archived IS NULL)
        ";

        $sql .= "
            ORDER BY sort_order;
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $uid
     * @return array
     */
    public function getMenuItem($uid)
    {
        $sql = "
            SELECT
                menu.uid,
                parent_uid,
                sort_order,
                nofollow,
                target,
                class,
                label,
                uri_uid,
                uri,
                menu.archived,
                menu.archived_datetime
            FROM menu
            LEFT JOIN uri
                ON uri.uid = menu.uri_uid
            WHERE menu.archived = '0'
            AND menu.uid = ?
            AND (uri.archived = '0' OR uri.archived IS NULL)
        ";

        $bind = [
            $uid,
        ];

        $sql .= "
            ORDER BY sort_order;
        ";

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $result;
    }


    /**
     * @param array $form_data
     * @return bool
     * @throws Exception
     */
    public function addMenuItem(array $form_data)
    {
        self::editMenuCheck();

        $label      = Sanitize::string($form_data['label']);
        $uri_uid    = Sanitize::string($form_data['menu_uri_uid']);
        $nofollow   = Sanitize::string($form_data['nofollow']);
        $target     = Sanitize::string($form_data['target']);
        $class      = !empty($form_data['class']) ? Sanitize::string($form_data['class']) : null;

        $inserted   = $this->insertMenuItem($label, $uri_uid, $nofollow, $target, $class);
        
        return $inserted;
    }


    /**
     * @param array $form_data
     * @return bool
     * @throws Exception
     */
    public function updateMenuItem(array $form_data)
    {
        self::editMenuCheck();

        $uid        = Sanitize::string($form_data['uid']);
        $parent_uid = !empty($form_data['parent_uid']) ? Sanitize::string($form_data['parent_uid']) : null;
        $sort_order = filter_var($form_data['sort_order'], FILTER_SANITIZE_NUMBER_INT);
        $label      = Sanitize::string($form_data['label']);
        $uri_uid    = Sanitize::string($form_data['menu_uri_uid']);
        $nofollow   = Sanitize::string($form_data['nofollow']);
        $target     = Sanitize::string($form_data['target']);
        $class      = !empty($form_data['class']) ? Sanitize::string($form_data['class']) : null;

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            $this->archiveMenuItem($uid, $transaction);
            $this->insertMenuItem($label, $uri_uid, $nofollow, $target, $class, $sort_order, $uid, $parent_uid, $transaction);
        } catch (Exception $e) {
            $transaction->rollBack();

            Log::app('Could not update menu item ' . $uid, $e->getTraceAsString(), $e->getMessage());

            throw $e;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @return int
     */
    private function getNewSortOrder()
    {
        $sql = "
            SELECT MAX(sort_order)
            FROM menu
            WHERE archived = '0';
        ";

        $db             = new Query($sql);
        $max_sort_order = $db->fetch();
        $new_sort_order = (int)($max_sort_order+1);

        return (int)$new_sort_order;
    }


    /**
     * @param $label
     * @param $uri_uid
     * @param bool $nofollow
     * @param string $target
     * @param null $class
     * @param null $sort_order
     * @param null $uid
     * @param null $parent_uid
     * @param PdoMySql|null $transaction
     * @param string $archived_datetime
     * @return bool
     * @throws Exception
     */
    private function insertMenuItem($label, $uri_uid, $nofollow = true, $target = '_self', $class = null, $sort_order = null, $uid = null, $parent_uid = null, PdoMySql $transaction = null, $archived_datetime = '0000-00-00 00:00:00')
    {
        self::editMenuCheck();

        $uid        = $uid ?? self::getUid();
        $sort_order = (int)($sort_order ?? $this->getNewSortOrder());
        $archived   = !empty($archived_datetime) && $archived_datetime != '0000-00-00 00:00:00' ? '1' : '0';

        $sql        = "
            INSERT INTO menu (
                uid,
                parent_uid,
                sort_order,
                nofollow,
                target,
                class,
                label,
                uri_uid,
                archived,
                archived_datetime
            ) VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            );
        ";

        $bind = [
            $uid,
            $parent_uid,
            $sort_order,
            $nofollow,
            $target,
            $class,
            $label,
            $uri_uid,
            $archived,
            $archived_datetime,
        ];
        
        if (!empty($transaction)) {
            $inserted = $transaction
                ->prepare($sql)
                ->execute($bind);
        } else {
            $db         = new Query($sql, $bind);
            $inserted   = $db->run();
        }
        
        return $inserted;
    }


    /*
     * the nestedSortable.js plugin is finicky about non-alphanumeric characters. So if we remove the dashes from a
     * normally generated UUID, we are left with just letters and numbers. This is just a workaround for the front-end
     * nestedSortable functionality.
     *
     * @param $uid
     * @return $normalized_uid;
     */
    private static function getUid($uid = null)
    {
        $uid            = $uid ?? Query::getUuid();
        $normalized_uid = str_replace('-', null, $uid);

        return $normalized_uid;
    }


    /**
     * @param $uid
     * @param PdoMySql|null $transaction
     * @param bool $archive_orphans
     * @return bool
     * @throws Exception
     */
    public function archiveMenuItem($uid, PdoMySql $transaction = null, $archive_orphans = false)
    {
        self::editMenuCheck();

        $sql = "
            UPDATE menu
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                uid = ?
            AND
                archived = '0';
        ";

        $bind = [
            $uid,
        ];

        if (!empty($transaction)) {
            $archived = $transaction
                ->prepare($sql)
                ->execute($bind);
        } else {
            $db         = new Query($sql, $bind);
            $archived   = $db->run();
        }

        return $archived;
    }


    /**
     * @return array
     */
    private function getOrphans()
    {
        $sql = "
            SELECT uid
            FROM menu
            WHERE parent_uid NOT IN (
                SELECT uid
                FROM menu
                WHERE archived = '0'
            )
            AND parent_uid IS NOT NULL
            AND archived = '0';
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAll();

        return $results;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function archiveOrphans()
    {
        self::editMenuCheck();

        $orphans = $this->getOrphans();

        foreach ($orphans as $uid) {
            $this->archiveMenuItem($uid);
        }

        if (!empty($this->getOrphans())) {
            return $this->archiveOrphans();
        }

        return true;
    }


    /**
     * @return string
     */
    public function getErrors()
    {
        return implode('; ', $this->errors);
    }
}