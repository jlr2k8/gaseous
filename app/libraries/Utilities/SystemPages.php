<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/21/18
 *
 * SystemPages.php
 *
 * Mapping for system pages
 *
 **/

namespace Utilities;

class SystemPages
{
    public $system_pages = [
        'admin'     => 'admin',
        'pages'     => 'admin/pages',
        'roles'     => 'admin/roles',
        'settings'  => 'admin/settings',
        'users'     => 'admin/users',
        'css'       => 'admin/css',
        'menu'      => 'admin/menu',
    ];


    public function __construct()
    {
    }


    /**
     * @return array
     */
    public function getSystemPagesAsResultSet()
    {
        $result = [];

        foreach ($this->system_pages as $key => $val)
        {
            $result[] = [
                'uid'   => $key,
                'uri'   => $val,
            ];
        }

        return $result;
    }
}