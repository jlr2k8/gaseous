<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 11/13/18
 *
 * test.php
 *
 *
 *
 **/

use Utilities\Sanitize;

$string = '<h1>HELLO "WORLD"! Look, I\'m \'single-quoted\' && double-amped! && ````quadruple-ticked````...</h1>';

var_dump(
    filter_var($string, FILTER_SANITIZE_STRING),
    Sanitize::string($string, FILTER_FLAG_STRIP_BACKTICK | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP)
);

// , FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_ENCODE_AMP | FILTER_FLAG_STRIP_BACKTICK

// , FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK | FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP