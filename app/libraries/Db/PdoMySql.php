<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/10/2018
 *
 * PdoMySql.php
 *
 * Extends PDO
 *
 **/

namespace Db;

use PDO;
use Settings;

class PdoMySql extends PDO
{
    public $debug;
    protected $dsn, $username, $password, $con;
    protected $status = true;

    public function __construct()
    {
        if (is_readable(ENVIRONMENT_INI)) {
            $mysql_server   = Settings::environmentIni('mysql_server');
            $mysql_database = Settings::environmentIni('mysql_database');
            $mysql_port     = Settings::environmentIni('mysql_port');
            $mysql_user     = Settings::environmentIni('mysql_user');
            $mysql_password = Settings::environmentIni('mysql_password');

            $this->dsn      = 'mysql:host=' . $mysql_server . ';port=' . $mysql_port . ';dbname=' . $mysql_database;
            $this->username = $mysql_user;
            $this->password = $mysql_password;

            try {
                $this->con = parent::__construct (
                    $this->dsn,
                    $this->username,
                    $this->password,
                    [
                        parent::ATTR_PERSISTENT => true,
                    ]
                );
            } catch (\PDOException $p) {
                if (empty($_SESSION['setup_mode'])) {
                    throw $p;
                } else {
                    trigger_error($p, E_USER_WARNING);
                }
            }

            //$this->traceExpansionQueries();

            if ($this->debug === true) { // TODO - create binary setting (true or false) for debug mode
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            } else {
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } else {
            $this->con      = false;
            $this->status   = false;
        }
    }


    public function traceExpansionQueries()
    {
        return true; // stubbing this out for now. not sure what to do with this check yet
/*        $trace              = debug_backtrace();
        $expansion_queries  = [];

        foreach ($trace as $t) {
            if (strpos($t['file'],EXPANSION_ROOT) !== false) {
                foreach ($t['args'] as $arg) {
                    if (stripos($arg, 'insert into') !== false || stripos($arg, 'update ') !== false || stripos($arg, 'delete from') !== false) {
                        // do something about data being changed to a db table via an expansion
                    }
                }

                if (!empty($t['object']->insert) || !empty($t['object']->update) || !empty($t['object']->delete)) {
                    // do something about data being changed to a db table via an expansion, using the query builder
                }
            }
        }

        return $expansion_queries;*/
    }
}