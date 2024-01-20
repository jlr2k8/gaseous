<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/21/18
 *
 * update_content_iteration.php
 *
 * Point current page iteration to the one specified in $_POST (e.g. clicking button within form on preview_content_iteration.php)
 *
 **/

use Content\Get;
use Content\Http;
use Content\Submit;
use Db\PdoMySql;
use Utilities\Sanitize;

$get_pages              = new Get();
$submit_pages           = new Submit($_POST);
$transaction            = new PdoMySql();
$content_iteration_uid  = !empty($_POST['content_iteration_uid']) ? Sanitize::string($_POST['content_iteration_uid']) : false;
$content_uid            = !empty($_POST['content_uid']) ? Sanitize::string($_POST['content_uid']) : false;
$return_url             = !empty($_POST['return_url_encoded']) ? urldecode(filter_var($_POST['return_url_encoded'], FILTER_SANITIZE_URL)) : Settings::value('full_web_url');

$get_pages::editPageCheck();

$transaction->beginTransaction();

try {
    $submit_pages->updateCurrentIteration($transaction, $content_iteration_uid, $content_uid);

    $transaction->commit();

    Http::redirect($return_url);
} catch (Exception $e) {
    echo 'There was an error';

    Log::app($e->getTraceAsString(), $e->getMessage());

    $transaction->rollBack();
}