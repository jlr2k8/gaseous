<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/21/18
 *
 * update_page_iteration.php
 *
 * Point current page iteration to the one specified in $_POST (e.g. clicking button within form on preview_page_iteration.php)
 *
 **/

use Content\Pages\Get;
use Content\Pages\HTTP;
use Content\Pages\Submit;
use Db\PdoMySql;

$get_pages          = new Get();
$submit_pages       = new Submit($_POST);
$transaction        = new PdoMySql();
$page_iteration_uid = !empty($_POST['page_iteration_uid']) ? (string)filter_var($_POST['page_iteration_uid'], FILTER_SANITIZE_STRING) : false;
$page_master_uid    = !empty($_POST['page_master_uid']) ? (string)filter_var($_POST['page_master_uid'], FILTER_SANITIZE_STRING) : false;
$return_url         = !empty($_POST['return_url_encoded']) ? urldecode(filter_var($_POST['return_url_encoded'], FILTER_SANITIZE_URL)) : Settings::value('full_web_url');

$get_pages::editPageCheck();

$transaction->beginTransaction();

try {
    $submit_pages->updateCurrentIteration($transaction, $page_iteration_uid, $page_master_uid);

    $transaction->commit();

    HTTP::redirect($return_url);
} catch (Exception $e) {
    echo 'There was an error';
    echo $e->getTraceAsString();
    echo $e->getMessage();

    $transaction->rollBack();
}