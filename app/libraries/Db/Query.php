<?php
/**
* Created by Josh L. Rogers
* Copyright (c) 2018 All Rights Reserved.
* 4/10/2018
*
* Query.php
*
* Run basic PDO/MySQL queries
*
**/

namespace Db;

use Error;
use Log;
use PDO;
use \Exception;
use PDOException;
use PDOStatement;

class Query extends PdoMySql
{
    public $query, $con;
    protected $sql, $bind_array;


    /**
     * @param $sql
     * @param array $bind
     * @throws PDOException
     */
    public function __construct($sql, $bind = array())
    {
        parent::__construct();

        try {
            $this->sql			= $sql;
            $this->bind_array	= $bind;

            $this->query = $this->runQuery();
        } catch(Exception $e) {
            trigger_error('Queries cancelled because connection to the database could not be established.', E_WARNING);
        }
    }


    /**
     * @return PDOStatement
     */
    private function runQuery()
    {
        $query = $this->status;

        if ($this->status === true) {
            try {
                $query = $this->prepare($this->sql);

                if (!empty($query->execute($this->bind_array))) {
                    debug_backtrace();
                }
            } catch (PDOException $p) {
                self::handlePdoException($p);
            } catch (Error $e) {
                self::handleErrorAsWarning($e);
            }
        }

        return $query;
    }

    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAssoc()
    {
        $fetch_assoc = [];

        try {
            $fetch_assoc = $this->query->fetch(PDO::FETCH_ASSOC);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_assoc;
    }


    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAllAssoc()
    {
        $fetch_all_assoc = [];

        try {
            $fetch_all_assoc = $this->query->fetchAll(PDO::FETCH_ASSOC);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_all_assoc;
    }


    /**
     * @return string
     * @throws PDOException
     */
    public function fetch()
    {
        $fetch = null;

        try {
            $fetch = $this->query->fetch(PDO::FETCH_COLUMN);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch;
    }


    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAll()
    {
        $fetch_all = [];

        try {
            $fetch_all = $this->query->fetchAll(PDO::FETCH_COLUMN);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_all;
    }


    /**
     * @return bool
     */
    public function run()
    {
        return true;
    }


    /**
     * @return bool
     */
    public function runAndReturnInsertId()
    {
        return $this->lastInsertId();
    }


    /**
     * @param PDOException $e
     */
    private static function handlePdoException(PDOException $e)
    {
        Log::app($e->getMessage() . ' ' . $e->getTraceAsString());
        throw new PDOException($e->getMessage() . ' ' . $e->getTraceAsString());
    }


    /**
     * @param Error $e
     * @return bool
     */
    private static function handleErrorAsWarning(Error $e)
    {
        if (empty($_SESSION['setup_mode'])) {
            Log::app($e->getMessage() . ' ' . $e->getTraceAsString());
            trigger_error($e->getMessage() . ' ' . $e->getTraceAsString(), E_USER_WARNING);
        }

        return false;
    }


    /**
     * @return string
     */
    public static function getUuid()
    {
        $sql    = "SELECT UUID();";
        $db     = new self($sql);

        return $db->fetch();
    }
}