<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/9/20
 *
 * CssIterator.php
 *
 * DB stored CSS management/output
 *
 **/

namespace Assets;

use Db\PdoMySql;
use Db\Query;
use Seo\Minify;
use User\Account;

class CssIterator
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
            SELECT uid, css, author, description, is_selected, created_datetime
            FROM css_iteration
            WHERE archived = '0'
            ORDER BY created_datetime DESC;
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        foreach ($results as $key => $result)
        {
            $results[$key]['formatted_modified'] = \Utilities\DateTime::formatDateTime($result['created_datetime'], 'm/d/Y g:i A e');
        }

        return $results;
    }


    /**
     * @param $decode
     * @return array
     */
    public function getCurrentCssIteration($decode = false)
    {
        //$result = [];

        //if (empty($_SESSION['css_preview'])) {
            $sql = "
                SELECT uid, css, author, description, is_selected, modified_datetime
                FROM css_iteration
                WHERE archived = '0'
                AND is_selected = '1';
            ";

            $db     = new Query($sql);
            $result = $db->fetchAssoc();
        //}

        if ($decode === true) {
            $result['css'] = htmlspecialchars_decode($result['css']);
        }

        return $result;
    }


    /**
     * @param $uid
     * @param $decode
     * @return array
     */
    public function getCssIteration($uid, $decode = false)
    {
        $sql = "
            SELECT uid, css, author, description, is_selected, modified_datetime
            FROM css_iteration
            WHERE archived = '0'
            AND uid = ?;
        ";

        $db     = new Query($sql, [$uid]);
        $result = $db->fetchAssoc();

        if ($decode === true) {
            $result['css'] = htmlspecialchars_decode($result['css']);
        }

        return $result;
    }


    /**
     * @param $css_content
     * @param $minify
     * @param $uid
     * @return bool
     */
    public function setCssPreview($css_content, $minify = true, $uid = null)
    {
        $_SESSION['css_preview'] = [];

        if (!empty($minify)) {
            $css_content = Minify::css($css_content);
        }

        if (!empty($uid)) {
            $_SESSION['editor_css']     = [];

            $css_iteration              = $this->getCssIteration($uid);
            $css_iteration_uid_exists   = (!empty($css_iteration));

            if ($css_iteration_uid_exists) {
                $this->setEditorCss($css_iteration['css'], $uid);
            }

            $_SESSION['css_preview']['uid'] = $uid;
        }

        return ($_SESSION['css_preview']['css'] = $css_content);
    }


    /**
     * @param $css_content
     * @param $uid
     * @return bool
     */
    public function setEditorCss($css_content, $uid = false)
    {
        $_SESSION['editor_css']['uid'] = $uid;
        $_SESSION['editor_css']['css'] = $css_content;

        return true;
    }


    /**
     * @param array $data
     * @return bool
     */
    public function saveCssIteration(array $data)
    {
        // uid is simply a hash of the CSS itself
        $css_iteration_uid  = hash('md5', $data['css_iteration']);
        $css_iteration      = $this->getCssIteration($css_iteration_uid);

        // don't change is_selected in db. just put the uid in $_SESSION['css_preview']
        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            if (empty($css_iteration)) {
                $this->insertIteration($transaction, $data);
            }

            $this->markAllIterationsAsUnselected($transaction);
            $this->markIterationAsSelected($transaction, $css_iteration_uid);
        } catch (\Exception $e) {
            var_dump($e->getMessage(), $e->getTraceAsString());

            $transaction->rollBack();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     */
    private function markAllIterationsAsUnselected(PdoMySql $transaction)
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
     * @param PdoMySql $transaction
     * @param $uid
     * @return bool
     */
    private function markIterationAsSelected(PdoMySql $transaction, $uid)
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
     * @param PdoMySql $transaction
     * @param array $data
     * @return bool
     */
    private function insertIteration(PdoMySql $transaction, array $data)
    {
        $sql = "
            INSERT INTO css_iteration (css, author, description, is_selected)
            VALUES (?, ?, ?, ?)
        ";

        $bind = [
            $data['css_iteration'],
            Account::getUsername(),
            $data['description'],
            '1',
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }
}