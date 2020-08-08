<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 5/19/18
 *
 * redirects.php
 *
 * Redirect URI administration. Controlled by admins and system. If a CMS page's URI is modified, the old URI redirects
 * (301 header) to the new one by default. Each of these are stored as a row in the redirects table.
 *
 **/

use \Content\Breadcrumbs;
use \Content\Http;
use \Content\Templator;
use \Uri\Redirect;
use Uri\Uri;

// check setting/role privileges
if (!Settings::value('add_redirects') && !Settings::value('edit_redirects') && !Settings::value('archive_redirects')) {
    Http::error(403);
}

$templator      = new Templator();
$uri_redirect   = new Redirect();

$title              = 'Site Redirects';
$redirects          = $uri_redirect->getAll();
$unused_redirects   = $uri_redirect->getAllNotRedirected();
$error              = null;
$http_status_codes  = Http::$status_codes;
$all_uris           = Uri::all();

$add_uri_redirects      = Settings::value('add_redirects');
$edit_redirect_uris     = Settings::value('edit_redirects');
$archive_redirect_uris  = Settings::value('archive_redirects');

if (!empty($_POST) && $edit_redirect_uris) {
    foreach ($_POST as $key => $val) {
        $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
    }

    $submit_redir = false;

    if (isset($post['update'])) {
        $transaction->beginTransaction();

        try {
            $uri_redirect->update($post, $transaction);
        } catch (Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getMessage();

            $this->generateJsonUpsertStatus('status', $e->getMessage());
            $this->checkAndThrowException();

            return false;
        }

        $transaction->commit();

        $submit_redir = $uri_redirect->update($post);
    } elseif (isset($post['new'])) {
        if (!empty($post['custom_uri'])) {
            $uri_obj    = new Uri();
            $uri        = '/' . filter_var($post['custom_uri'], FILTER_SANITIZE_URL);

            if (!Uri::uriExistsAsRedirect($uri) && !Uri::uriExistsAsContent($uri)) {
                $uri_obj->insertUri($uri);

                $post['uri_uid']    = $uri_obj->getUriUid($uri);
            }
        }

        $submit_redir = $uri_redirect->insert($post);
    }

    if ($submit_redir) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/redirects/');
    } else {
        $error = $uri_redirect->getErrors();
    }
}

if (!empty($_GET['archive']) && $archive_redirect_uris) {
    $uri_uid        = filter_var($_GET['archive'], FILTER_SANITIZE_STRING);
    $uri_redirect->archive($uri_uid);
    exit;
}

$templator->assign('full_web_url', Settings::value('full_web_url'));
$templator->assign('uri_redirects', $redirects);
$templator->assign('http_status_codes', $http_status_codes);
$templator->assign('add_uri_redirects', $add_uri_redirects);
$templator->assign('edit_uri_redirects', $edit_redirect_uris);
$templator->assign('archive_uri_redirects', $archive_redirect_uris);
$templator->assign('all_uris', $all_uris);
$templator->assign('all_unused_uris', $unused_redirects);
$templator->assign('error', $error);

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())
        ->crumb('Site Administration', '/admin/')
        ->crumb($title),
    'body'              => $templator->fetch('admin/redirects.tpl'),
];

echo Templator::page($page_find_replace);