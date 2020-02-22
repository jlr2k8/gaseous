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

class Query extends PdoMySql
{
    public $query, $con;
    protected $sql, $bind;


    /**
     * @param $sql
     * @param array $bind
     * @throws \PDOException
     */
    public function __construct($sql, $bind = array())
    {
        parent::__construct();

        $this->sql			= $sql;
        $this->bind_array	= $bind;

        $this->query = $this->runQuery();
    }


    /**
     * @return \PDOStatement
     */
    private function runQuery()
    {
        try {
            $query = $this->prepare($this->sql);

            $query->execute($this->bind_array);

        } catch(\PDOException $e) {
            self::handleError($e);
        }

        return $query;
    }


    /**
     * @return array
     * @throws \PDOException
     */
    public function fetchAssoc()
    {
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     * @throws \PDOException
     */
    public function fetchAllAssoc()
    {
        return $this->query->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @return string
     * @throws \PDOException
     */
    public function fetch()
    {
        return $this->query->fetch(\PDO::FETCH_COLUMN);
    }


    /**
     * @return array
     * @throws \PDOException
     */
    public function fetchAll()
    {
        return $this->query->fetchAll(\PDO::FETCH_COLUMN);
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
     * @param \PDOException $e
     */
    private static function handleError(\PDOException $e)
    {
        throw new \PDOException($e->getMessage() . ' ' . $e->getTraceAsString());
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