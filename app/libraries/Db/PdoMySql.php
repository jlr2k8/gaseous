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

class PdoMySql extends \PDO
{
    public $debug;
    protected $dsn, $username, $password, $con;


    public function __construct()
    {
        $mysql_server   = \Settings::environmentIni('mysql_server');
        $mysql_database = \Settings::environmentIni('mysql_database');
        $mysql_port     = \Settings::environmentIni('mysql_port');
        $mysql_user     = \Settings::environmentIni('mysql_user');
        $mysql_password = \Settings::environmentIni('mysql_password');

        $this->dsn      = 'mysql:host=' . $mysql_server . ';port=' . $mysql_port . ';dbname=' . $mysql_database;
        $this->username = $mysql_user;
        $this->password = $mysql_password;

        $this->con = parent::__construct (
            $this->dsn,
            $this->username,
            $this->password,
            [
                parent::ATTR_PERSISTENT => true,
            ]
        );

        if ($this->debug === true) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        } else {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }
}