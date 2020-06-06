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
use PDO;
use \Exception;
use PDOException;
use PDOStatement;

class Query extends PdoMySql
{
    public $query, $con;
    protected $sql, $bind_array, $select, $from;

    protected static $

    /**
     * @param $sql
     * @param array $bind
     * @throws PDOException
     */
    public function __construct($sql = null, $bind = array())
    {
        parent::__construct();

        if (!empty($sql)) {
            try {
                $this->sql          = $sql;
                $this->bind_array   = $bind;

                $this->query = $this->runQuery();
            } catch(Exception $e) {
                trigger_error('Queries cancelled because connection to the database could not be established.', E_WARNING);
            }
        }
    }


    public function select(array $columns, $from = null)
    {
        $i              = (int)0;
        $select_clause  = [];

        foreach ($columns as $alias => $col) {
            $col = self::buildObjectName($col);

            if ($i == $alias) {
                $select_clause[] = $col;
            } else {
                $select_clause[] = $col . ' AS ' . $alias;
            }

            if (is_int($alias)) {
                $i = (int)($alias+1);
            }
        }

        $this->select   = 'SELECT ' . implode(', ', $select_clause);
        $this->from     = !empty($from) ? ' FROM ' . self::buildObjectName($from) : null;

        return $this;
    }


    private static function buildObjectName($expression)
    {
        $expression_exploded   = explode('.', $expression);
        $expression            = '`' . implode('`.`', $expression_exploded) . '`';

        return $expression;
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
        throw new PDOException($e->getMessage() . ' ' . $e->getTraceAsString());
    }


    /**
     * @param Error $e
     * @return bool
     */
    private static function handleErrorAsWarning(Error $e)
    {
        if (empty($_SESSION['setup_mode'])) {
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