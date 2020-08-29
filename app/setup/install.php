<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/28/20
 *
 * install.php
 *
 * Installation controller
 *
 **/

use Content\Http;
use Db\PdoMySql;
use Setup\Install;
use Setup\Reset\Assets;
use Setup\Reset\Content;
use Setup\Reset\System;

if (!empty($_SESSION['setup_mode']) && date('YmdHis') < $_SESSION['setup_mode']) {
    $install = new Install();

    if (Install::checkIniSectionExists()) {
        $install->mysql_server      = Settings::environmentIni('mysql_server');
        $install->mysql_database    = Settings::environmentIni('mysql_database');
        $install->mysql_port        = Settings::environmentIni('mysql_port');
        $install->mysql_user        = Settings::environmentIni('mysql_user');
        $install->mysql_password    = Settings::environmentIni('mysql_password');
    }

    echo Install::outputStyles();

    $pdo_connected  = $install->testPdoConnection();

    // Step 1 - Establish PDO connection
    if (!$pdo_connected) {
        if (!empty($_POST['setup_step']) && $_POST['setup_step'] == '1') {
            $generated_ini_file = $install->processDbConnectionForm($_POST);

            echo Install::formResults($generated_ini_file);
        }

        $pdo_connected  = $install->testPdoConnection();

        if (!$pdo_connected) {
            echo Install::pdoConnectionForm();
            exit;
        }
    }

    // Step 2 - Basic Settings
    $pdo_connected  = $install->testPdoConnection();

    if ($pdo_connected) {
        if (!empty($_POST['setup_step']) && $_POST['setup_step'] == '2') {
            $system         = new System();
            $assets         = new Assets();
            $content        = new Content();
            $transaction    = new PdoMySql();

            $transaction->beginTransaction();

            $transaction    = $system->setProperties($transaction);
            $transaction    = $system->setSettingCategories($transaction);
            $transaction    = $system->setUriRoutes($transaction);
            $transaction    = $system->setSettings($transaction, $_POST);
            $transaction    = $system->setSettingsRoles($transaction);

            $transaction    = $assets->setCss($transaction);
            $transaction    = $assets->setJs($transaction);

            $transaction    = $content->setContentBodyTypes($transaction);
            $transaction    = $content->setContentBodyFieldTypes($transaction);
            $transaction    = $content->setContentBodyFields($transaction);
            $transaction    = $content->setUris($transaction);
            $transaction    = $content->setContent($transaction);

            if ($transaction->commit()) {
                header('Location: ' . Settings::value('full_web_url') . '/register/');
            }
        } else {
            echo System::runChangesets()
                ? '<p class="green_text bold">Database tables/data successfully installed</p>'
                : '<p class="red_text bold">There was an issue running database changesets - please check the application logs</p>'
            ;
        }

        echo System::form();
    }
} else {
    Http::error(403);
}