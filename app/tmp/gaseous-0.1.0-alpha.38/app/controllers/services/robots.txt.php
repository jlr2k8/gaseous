<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/25/18
 *
 * robots.txt.php
 *
 * robots.txt file for the top level of app. configured in setting 'robots.txt_value'
 *
 **/

$robots_txt_value = Settings::value('robots_txt_value');

header('Content-type: text/plain');

echo $robots_txt_value ?: null;