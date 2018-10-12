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
        $this->dsn      = 'mysql:host=' . MYSQL_SERVER . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB;
        $this->username = MYSQL_USER;
        $this->password = MYSQL_PASSWORD;

        $this->con = parent::__construct (
            $this->dsn,
            $this->username,
            $this->password
        );

        if ($this->debug === true) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        } else {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }
}