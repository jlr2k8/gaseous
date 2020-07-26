<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/25/20
 *
 * Pager.php
 *
 * Static methods for $_GET global/generic pagination
 *
 **/

namespace Utilities;

use Content\Templator;

class Pager
{
    public function __construct()
    {
    }


    /**
     * @return array
     */
    public static function status()
    {
        $paginator = [
            'p'                 => $_GET['p'] ?? null,
            'per_page'          => $_GET['per_page'] ?? null,
            'sort_by'           => $_GET['sort_by'] ?? null,
            'sort_ascending'   => !empty($_GET['sort_ascending']) && $_GET['sort_ascending'] == 'true' ? true : false,
        ];

        return $paginator;
    }
}