<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/29/20
 *
 * Install.php
 *
 * Installation setup methods
 *
 **/

namespace Setup;

use PDO;
use PDOException;

class Install
{
    public $environment, $mysql_server, $mysql_database, $mysql_port, $mysql_user, $mysql_password;

    public function __construct()
    {
    }


    /**
     * @return string
     */
    public static function outputStyles()
    {
        $output = '
            <style>
                html {
                    background: radial-gradient(circle, lightsteelblue 0%, steelblue 150%);
                    font-family: "Consolas", monospace;
                }
                body {
                    padding: 1% 1% 400px 1%;
                    margin: 1% auto;
                    width: 80%;
                    min-height: 100%;
                    background-color: #eeeeee66;
                }
                textarea,input[type="text"] {
                    width: 500px;
                    border: 0;
                    height: 2em;
                    max-width: 90%;
                }
                input[type="text"]#web_url {
                    width: 400px;
                }
                textarea {
                    height: 300px;
                }
            </style>
        ';

        return $output;
    }


    /**
     * @return string
     */
    public static function pdoConnectionForm()
    {
        $default_environment_value = self::checkIniSectionExists(ENVIRONMENT) === false ? ENVIRONMENT : 'some-other-environment';

        $form = '
            <form  method="post" action="">
                <h1>Database Connection</h1>
                <p>
                    In order to begin, you must provide connection details to a MySql database server.
                </p>
                <div>
                    <label>
                        Environment:
                    </label><br />
                    <input type="text" name="environment" id="environment" placeholder="$_SERVER[\'ENVIRONMENT\'] global" value="' . $default_environment_value . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <label>
                        MySql Server (host):
                    </label><br />
                    <input type="text" name="mysql_server" id="mysql_server" placeholder="127.0.0.1" value="' . ($_POST['mysql_server'] ?? null) . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <label>
                        MySql Database:
                    </label><br />
                    <input type="text" name="mysql_database" id="mysql_database" placeholder="gaseous_db" value="' . ($_POST['mysql_database'] ?? null) . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <label>
                        MySql Port:
                    </label><br />
                    <input type="text" name="mysql_port" id="mysql_port" placeholder="Default is 3306" value="' . ($_POST['mysql_port'] ?? '3306') . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <label>
                        MySql User:
                    </label><br />
                    <input type="text" name="mysql_user" id="mysql_user" placeholder="administrator" value="' . ($_POST['mysql_user'] ?? null) . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <label>
                        MySql Password:
                    </label><br />
                    <input type="text" name="mysql_password" id="mysql_password" placeholder="mypassword" value="' . ($_POST['mysql_password'] ?? null) . '" />
                </div>
                <p>&#160;</p>
                <div>
                    <input type="hidden" name="setup_mode" value="' . $_SESSION['setup_mode'] . '" />
                    <input type="submit" value="Test Connection &#187;" />
                </div>
            </form>
        ';

        return $form;
    }


    /**
     * @param $submission_result
     * @return string
     */
    public static function formResults($submission_result)
    {
        if ($submission_result === true) {
            $output = '<h2>The environments file was generated successfully with the database information provided. See ' . ENVIRONMENT_INI . ' for more details...</h2>';
        } elseif (is_string($submission_result)) {
            $output = '<h2>The connection to the database was successful, however, the environments file could not be automatically created.</h2><p> Please put the following in ' . ENVIRONMENT_INI . ': <br /><br />';
            $output .= '<pre>' . $submission_result . '</pre>';
        } else {
            $output = '<h2 class="red">Please try again....</h2>';
        }

        return $output;
    }


    /**
     * @param array $data
     * @return bool|string
     */
    public function processDbConnectionForm(array $data)
    {
        $this->environment    = filter_var($data['environment'], FILTER_SANITIZE_STRING);
        $this->mysql_server   = filter_var($data['mysql_server'], FILTER_SANITIZE_STRING);
        $this->mysql_database = filter_var($data['mysql_database'], FILTER_SANITIZE_STRING);
        $this->mysql_port     = filter_var($data['mysql_port'], FILTER_SANITIZE_NUMBER_INT);
        $this->mysql_user     = filter_var($data['mysql_user'], FILTER_SANITIZE_STRING);
        $this->mysql_password = filter_var($data['mysql_password'], FILTER_SANITIZE_STRING);

        $result                 = false;
        $is_valid_connection    = $this->testPdoConnection();

        if ($is_valid_connection) {
            $result   = $this->generateIniSection();
        }

        return $result;
    }


    /**
     * @return bool
     */
    public static function checkIniSectionExists()
    {
        $result = false;

        if (is_readable(ENVIRONMENT_INI)) {
            $parsed_environment_ini_file    = parse_ini_file(ENVIRONMENT_INI, true);
            $result                         = (!empty($parsed_environment_ini_file[ENVIRONMENT]));
        }

        return $result;
    }


    /**
     * @return bool
     */
    public function testPdoConnection()
    {
        $dsn      = 'mysql:host=' . $this->mysql_server . ';port=' . $this->mysql_port . ';dbname=' . $this->mysql_database;

        try {
            new PDO(
                $dsn,
                $this->mysql_user,
                $this->mysql_password
            );
        } catch(PDOException $p) {
            return false;
        }

        return true;
    }


    /**
     * Returns true if able to generate/append to a physical environments file. Returns a string of data that must go into
     * an environments file - perhaps the app couldn't create/write to it because of permission issues.
     *
     * @return bool|string
     */
    private function generateIniSection()
    {
        $data[] = '[' . $this->environment . ']';
        $data[] = 'mysql_server    = ' . $this->mysql_server;
        $data[] = 'mysql_database  = ' . $this->mysql_database;
        $data[] = 'mysql_port      = ' . $this->mysql_port;
        $data[] = 'mysql_user      = ' . $this->mysql_user;
        $data[] = 'mysql_password  = ' . $this->mysql_password;

        $data = implode("\r\n", $data);

        if (!file_exists(ENVIRONMENT_INI) && touch(ENVIRONMENT_INI)) {
            file_put_contents(ENVIRONMENT_INI, '; Generated by installation ' . date('Y-m-d H:i:s'));
        }

        if (touch(ENVIRONMENT_INI)) {
            file_put_contents(ENVIRONMENT_INI, "\r\n" . $data, FILE_APPEND);
        } else {
            return $data;
        }

        return true;
    }
}