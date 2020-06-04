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

// form for db con info
// on successful connection
// --> write new environment.ini file with ENVIRONMENT as the array key (just do a try/catch on file_put_contents). on catch, provide copy/paste/instructions instead

// user clicks next. session $_SESSION['setup_mode'] = true. when page reloads, db should be able to connect - but no core tables have been setup yet. run changesets.
// once successful, begin registration and name for role (e.g. admin)
// provide fields for most important setting values to set:
// --> web url
// --> SMTP/webmaster values
// --> log directory, upload directory, relative img directory, relative file directory
// --> on submit, enable all role-based settings by default and set each role to newly added role ONLY. this inherently makes this user a super-admin

use Content\Http;
use Db\PdoMySql;
use Setup\Install;
use Setup\Reset\Assets;
use Setup\Reset\Content;
use Setup\Reset\System;

if (!empty($_SESSION['setup_mode']) && date('YmdHis') < $_SESSION['setup_mode']) {
    $install        = new Install();
    $pdo_connected  =   !$install->testPdoConnection();

    echo Install::outputStyles();

    if (Install::checkIniSectionExists()) {
        $install->mysql_server      = Settings::environmentIni('mysql_server');
        $install->mysql_database    = Settings::environmentIni('mysql_database');
        $install->mysql_port        = Settings::environmentIni('mysql_port');
        $install->mysql_user        = Settings::environmentIni('mysql_user');
        $install->mysql_password    = Settings::environmentIni('mysql_password');
    }

    // Step 1 - Establish PDO connection
    if (!$pdo_connected) {
        if (!empty($_POST)) {
            $generated_ini_file = $install->processDbConnectionForm($_POST);

            echo Install::formResults($generated_ini_file);
        }

        echo Install::pdoConnectionForm();

    // Step 2 - Basic Settings
    } elseif($pdo_connected) {
        if (!empty($_POST)) {
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
            echo System::runChangesets();
        }

        echo System::form();
    }
} else {
    Http::error(403);
}