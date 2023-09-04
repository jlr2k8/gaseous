<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 6/1/20
 *
 * Utilities.php
 *
 *
 *
 **/

namespace Setup;

use Db\Query;
use Settings;

class Utilities
{
    static $core_tables = [
        'account',
        'account_password',
        'account_roles',
        'cache',
        'changesets',
        'content',
        'content_body_fields',
        'content_body_field_properties',
        'content_body_field_types',
        'content_body_field_values',
        'content_body_templates',
        'content_body_types',
        'content_iteration',
        'content_iteration_commits',
        'content_roles',
        'css_iteration',
        'current_content_iteration',
        'js_iteration',
        'login_session',
        'menu',
        'property',
        'role',
        'settings',
        'settings_categories',
        'settings_properties',
        'settings_roles',
        'settings_values',
        'token_email',
        'uri',
        'uri_redirects',
        'uri_routes',
    ];

    public function __construct()
    {
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public static function checkCoreData()
    {
        $core_tables_clause  = "'" . implode("','", self::$core_tables) . "'";
        asort(self::$core_tables, SORT_ASC);

        $sql = "
            SELECT
                table_name
            FROM
                information_schema.TABLES
            WHERE
                table_schema = ?
            AND
                table_name IN($core_tables_clause)
            ORDER BY table_name;
        ";

        $bind = [
            Settings::environmentIni('mysql_database'),
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAll();

        $system_has_routes      = false;
        $system_has_settings    = false;

        if ($results == self::$core_tables) {
            $system_has_routes      = self::checkRoutes();
            $system_has_settings    = self::checkSettings();
        }

        return ($system_has_routes && $system_has_settings);
    }


    /**
     * @return int
     */
    private static function checkRoutes()
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                uri_routes
            WHERE
                archived = '0';
        ";

        $db                 = new Query($sql);
        $system_has_routes  = (int)($db->fetch() > 0);

        return $system_has_routes;
    }


    /**
     * @return int
     */
    private static function checkSettings()
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                settings
            WHERE
                archived = '0';
        ";

        $db                     = new Query($sql);
        $system_has_settings    = (int)($db->fetch() > 0);

        return $system_has_settings;
    }
}