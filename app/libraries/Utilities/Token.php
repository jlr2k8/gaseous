<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 6/10/2017
 *
 * Token.php
 *
 * Token generating methods
 *
 */

namespace Utilities;

class Token
{
    const TOKEN_LEN = 8;

    public function __construct()
    {
    }


    /**
     * @param int $len
     * @return null|string
     */
    public static function generate($len = self::TOKEN_LEN)
    {
        // sanity check
        $len    = (int)$len <= 0 ? (int)self::TOKEN_LEN : (int)$len;

        $bits   = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z')
        );

        $token  = null;

        for ($i = 0; $i < (int)$len; $i++)
            $token .= $bits[array_rand($bits)];

        return $token;
    }
}