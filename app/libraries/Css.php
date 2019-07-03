<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/5/18
 *
 * Css.php
 *
 * Site custom CSS management
 *
 **/

class Css
{
    public function __construct()
    {
    }


    /**
     * @return array
     */
    public function getAllIterations()
    {
        $sql = "
            SELECT uid, css, author, description, is_selected, preview_only, modified_datetime
            FROM css_iteration
            WHERE archived = '0'
            ORDER BY modified_datetime DESC;
        ";

        $db         = new \Db\Query($sql);
        $results    = $db->fetchAllAssoc();

        foreach ($results as $key => $result)
        {
            $results[$key]['formatted_modified'] = \Utilities\DateTime::formatDateTime($result['modified_datetime'], 'm/d/Y g:i A e');

            if ($result['preview_only'] == '1' && $result['author'] != \User\Account::getUsername())
            {
                unset($results[$key]);
            }
        }

        return $results;
    }


    /**
     * @return array
     */
    public function getCurrentCssIteration()
    {
        if (!empty($_SESSION['css_preview'])) {
            $result = $this->getCssIteration($_SESSION['css_preview']);
        } else {
            $sql = "
                SELECT uid, css, author, description, is_selected, preview_only, modified_datetime
                FROM css_iteration
                WHERE archived = '0'
                AND is_selected = '1';
            ";

            $db     = new \Db\Query($sql);
            $result = $db->fetchAssoc();

            if ($result['preview_only'] == '1' && $result['author'] != \User\Account::getUsername())
            {
                $result = [];
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getCssIteration($uid)
    {
        $sql = "
            SELECT uid, css, author, description, is_selected, preview_only, modified_datetime
            FROM css_iteration
            WHERE archived = '0'
            AND uid = ?;
        ";

        $db     = new \Db\Query($sql, [$uid]);
        $result = $db->fetchAssoc();

        if ($result['preview_only'] == '1' && $result['author'] != \User\Account::getUsername())
        {
            $result = [];
        }

        return $result;
    }


    /**
     * @param $uid
     * @return bool
     */
    public function setCssPreview($uid)
    {
        return $_SESSION['css_preview'] = $uid;
    }


    /**
     * @param $uid
     * @return bool
     */
    public function saveCssIteration(array $data, $preview_only = false)
    {
        // uid is generated in database. it's simply a hash of the CSS itself
        $css_iteration_uid  = hash('md5', $data['css_iteration']);
        $css_iteration      = $this->getCssIteration($css_iteration_uid);

        // don't change is_selected in db. just put the uid in $_SESSION['css_preview']
        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {
            if (empty($css_iteration)) {
                $this->insertIteration($transaction, $data, $preview_only);
            }

            if ($preview_only === false) {
                $this->markAllIterationsAsUnselected($transaction);
                $this->markIterationAsSelected($transaction, $css_iteration_uid);
            } else {
                $this->setCssPreview($css_iteration_uid);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage(), $e->getTraceAsString());

            $transaction->rollBack();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     */
    private function markAllIterationsAsUnselected(\Db\PdoMySql $transaction)
    {
        $sql = "
            UPDATE css_iteration
            SET is_selected = '0'
            WHERE archived = '0';
        ";

        $transaction
            ->prepare($sql)
            ->execute();

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param $uid
     * @return bool
     */
    private function markIterationAsSelected(\Db\PdoMySql $transaction, $uid)
    {
        $sql = "
            UPDATE css_iteration
            SET is_selected = '1'
            WHERE archived = '0'
            AND uid = ?;
        ";

        $transaction
            ->prepare($sql)
            ->execute([$uid]);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param array $data
     * @param bool $preview_only
     * @return bool
     */
    private function insertIteration(\Db\PdoMySql $transaction, array $data, $preview_only = false)
    {
        $preview_only   = $preview_only !== false ? '1' : '0';
        $is_selected    = $preview_only ? '0' : '1';

        $sql = "
            INSERT INTO css_iteration (css, author, description, is_selected, preview_only)
            VALUES (?, ?, ?, ?, ?)
        ";

        $bind = [
            $data['css_iteration'],
            \User\Account::getUsername(),
            $data['description'],
            $is_selected,
            $preview_only,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }

}