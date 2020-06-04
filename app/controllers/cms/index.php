<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/12/2018
 *
 * index.php
 *
 * Landing page for general CMS content
 *
 **/

use Content\Get;

$get = new Get();

echo $get->byUri();