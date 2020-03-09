<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 2/9/20
 *
 * JsIterator.php
 *
 * DB stored JS management/output
 *
 **/

namespace Assets;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Seo\Minify;
use User\Account;
use Utilities\DateTime;

class JsIterator
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
            SELECT uid, js, author, description, is_selected, created_datetime
            FROM js_iteration
            WHERE archived = '0'
            ORDER BY created_datetime DESC;
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        foreach ($results as $key => $result)
        {
            $results[$key]['formatted_modified'] = DateTime::formatDateTime($result['created_datetime'], 'm/d/Y g:i A e');
        }

        return $results;
    }


    /**
     * @param $decode
     * @return array
     */
    public function getCurrentJsIteration($decode = false)
    {
        $sql = "
            SELECT uid, js, author, description, is_selected, modified_datetime
            FROM js_iteration
            WHERE archived = '0'
            AND is_selected = '1';
        ";

        $db     = new Query($sql);
        $result = $db->fetchAssoc();

        if ($decode === true) {
            $result['js'] = htmlspecialchars_decode($result['js']);
        }

        return $result;
    }


    /**
     * @param $uid
     * @param $decode
     * @return array
     */
    public function getJsIteration($uid, $decode = false)
    {
        $sql = "
            SELECT uid, js, author, description, is_selected, modified_datetime
            FROM js_iteration
            WHERE archived = '0'
            AND uid = ?;
        ";

        $db     = new Query($sql, [$uid]);
        $result = $db->fetchAssoc();

        if ($decode === true) {
            $result['js'] = htmlspecialchars_decode($result['js']);
        }

        return $result;
    }


    /**
     * @param $js_content
     * @param $minify
     * @param $uid
     * @return bool
     */
    public function setJsPreview($js_content, $minify = true, $uid = null)
    {
        $_SESSION['js_preview'] = [];

        if (!empty($minify)) {
            $js_content = Minify::js($js_content);
        }

        if (!empty($uid)) {
            $_SESSION['editor_js']     = [];

            $js_iteration               = $this->getJsIteration($uid);
            $js_iteration_uid_exists    = (!empty($js_iteration));

            if ($js_iteration_uid_exists) {
                $this->setEditorJs($js_iteration['js'], $uid);
            }

            $_SESSION['js_preview']['uid'] = $uid;
        }

        return ($_SESSION['js_preview']['js'] = $js_content);
    }


    /**
     * @param $js_content
     * @param $uid
     * @return bool
     */
    public function setEditorJs($js_content, $uid = false)
    {
        $_SESSION['editor_js']['uid'] = $uid;
        $_SESSION['editor_js']['js'] = $js_content;

        return true;
    }


    /**
     * @param array $data
     * @return bool
     */
    public function saveJsIteration(array $data)
    {
        // uid is simply a hash of the JS itself
        $js_iteration_uid   = hash('md5', $data['js_iteration']);
        $js_iteration       = $this->getJsIteration($js_iteration_uid);

        // don't change is_selected in db. just put the uid in $_SESSION['js_preview']
        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            if (empty($js_iteration)) {
                $this->insertIteration($transaction, $data);
            }

            $this->markAllIterationsAsUnselected($transaction);
            $this->markIterationAsSelected($transaction, $js_iteration_uid);
        } catch (Exception $e) {
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
            UPDATE js_iteration
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
            UPDATE js_iteration
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
            INSERT INTO js_iteration (js, author, description, is_selected)
            VALUES (?, ?, ?, ?)
        ";

        $bind = [
            $data['js_iteration'],
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