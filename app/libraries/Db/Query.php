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
    protected $bind_array = [];
    protected $sql, $select, $from, $where, $or_where, $join, $inner_join, $cross_join, $straight_join, $left_join,
        $right_join, $natural_join, $natural_left_join, $natural_right_join, $group_by, $having, $window, $order_by,
        $limit, $for, $into;

    protected static $select_expressions = [
        '*',
        'ALL',
        'DISTINCT',
        'DISTINCT_ROW',
        'HIGH_PRIORITY',
        'STRAIGHT_JOIN',
        'SQL_SMALL_RESULT',
        'SQL_BIG_RESULT',
        'SQL_BUFFER_RESULT',
        'SQL_NO_CACHE',
        'SQL_CACHE_FOUND_ROWS',
    ];

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
            } catch(PDOException $p) {
                self::handlePdoException($p);
            } catch (Error $e) {
                self::handleErrorAsWarning($e);
            }
        }
    }


    public function select(array $columns, $from = null)
    {
        $i              = (int)0;
        $select_clause  = [];

        foreach ($columns as $alias => $col) {
            $col = self::buildSelectExpression($col);

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
        $this->from     = !empty($from) ? ' FROM ' . self::buildSelectExpression($from) : null;

        return $this;
    }


    public function where(array $clause)
    {
        $i              = (int)0;
        $where_clause   = [];

        foreach ($clause as $key => $val) {
            if (is_int($key)) {
                $where = $val;
            } else {
                $where              = $key;
                $bind_array         = $val;
                $this->bind_array   = array_merge($this->bind_array, $bind_array);
            }

            if ($i > (int)0) {
                $where_clause[] = 'AND ' . $where;
            } else {
                if (empty($this->where)) {
                    $where_clause[] = ' WHERE (' . $where;
                } else {
                    $where_clause[] = ' AND (' . $where;
                }
            }

            $i++;
        }

        $this->where    .= implode(' ', $where_clause) . ')';

        return $this;
    }


    private static function buildSelectExpression($expression)
    {
        $expression_exploded    = explode('.', $expression);
        $e                      = [];

        foreach ($expression_exploded as $ee) {
            if (in_array($ee, self::$select_expressions)) {
                $e[] = $ee;
            } else {
                $e[] = '`' . $ee . '`';
            }
        }

        return implode('.', $e);
    }


    public function buildQuery()
    {
        if (!empty($this->select)) {
            $this->sql = $this->select . $this->from . $this->where;
        }

        return $this->sql;
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
     * @return bool
     */
    private static function handlePdoException(PDOException $e)
    {
        if (empty($_SESSION['setup_mode'])) {
            throw new PDOException($e->getMessage() . ' ' . $e->getTraceAsString());
        } else {
            trigger_error($e->getMessage() . ' ' . $e->getTraceAsString(), E_USER_WARNING);
        }

        return false;
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