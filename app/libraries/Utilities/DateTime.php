<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/16/18
 *
 * DateTime.php
 *
 * Date and timestamp formatting
 *
 **/

namespace Utilities;

class DateTime
{
    const STANDARD_DATE_FORMAT  = 'Y-m-d';      // 2018-01-31
    const STANDARD_TIME_FORMAT  = 'g:i:s A e';  // 9:05:01 PM PDT

    public function __construct()
    {
    }


    /**
     * @param $date_time
     * @param bool $custom_format
     * @return string
     */
    public static function formatDateTime($date_time, $custom_format = false)
    {
        $format = $custom_format ?: self::STANDARD_DATE_FORMAT . ' ' . self::STANDARD_TIME_FORMAT;

        return date($format, strtotime($date_time));
    }
}