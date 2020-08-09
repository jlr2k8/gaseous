<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 3/25/20
 *
 * Debug.php
 *
 * Misc debug utilities
 *
 **/

namespace Utilities;

use Settings;

class Debug
{
    /**
     * @return false|string
     */
    public static function footer()
    {
        // Redundant check also in includes/debug_footer.php
        if (!Settings::value('show_debug')) {
            return null;
        }

        ob_start();

        require_once WEB_ROOT . '/includes/debug_footer.php';

        return ob_get_clean();
    }
}