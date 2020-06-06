<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 4/9/20
 *
 * Body.php
 *
 * Dynamic content body field types, values and properties
 *
 **/

namespace Content;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Seo\Url;
use SmartyException;

class Body
{
    public function __construct()
    {
    }


    /**
     * @param $content_iteration_uid
     * @return array
     */
    public function getBodyFieldValues($content_iteration_uid)
    {
        $sql = "
            SELECT
                cbf.template_token,
                cbtv.value
            FROM content_body_field_values AS cbtv
            INNER JOIN content_body_fields AS cbf
                ON cbtv.content_body_field_uid = cbf.uid
            INNER JOIN content_body_field_types AS cbft
                ON cbf.content_body_field_type_id = cbft.type_id
            WHERE
                cbtv.content_iteration_uid = ?
            AND
                cbtv.archived = '0'
            AND 
                cbf.archived = '0'
            AND 
                cbft.archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db                                 = new Query($sql, $bind);
        $results[$content_iteration_uid]    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $content_iteration_uid
     * @param Templator $templator
     * @param array $additional_fields_find_replace
     * @return string|null
     * @throws SmartyException
     * @throws Exception
     */
    public function renderTemplate($content_iteration_uid, Templator $templator, $additional_fields_find_replace = [])
    {
        $body   = $this->getBodyFieldValues($content_iteration_uid);

        if (empty($content_iteration_uid)) {
            throw new Exception('Missing content iteration uid in body array');
        }

        $content_body_type      = $this->getContentBodyTypeByIterationUid($content_iteration_uid);
        $content_body_type_id   = $content_body_type['type_id'];
        $trusted_static_methods = [
            '\Cms' => [],
        ];

        $templator->enableSecurity(null, $trusted_static_methods);

        if (!empty($additional_fields_find_replace)) {
            foreach ($additional_fields_find_replace as $find => $replace) {
                $templator->assign($find, $replace);
            }
        }

        try{
            $template = $this->getBodyTemplate($content_body_type_id);

            foreach ($body[$content_iteration_uid] as $key => $find_replace) {
                $find       = $find_replace['template_token'];
                $replace    = $find_replace['value'];

                $templator->assign($find, $replace);
            }


            foreach ($body[$content_iteration_uid] as $key => $find_replace) {
                $find       = $find_replace['template_token'];
                $replace    = $find_replace['value'];

                $field_override_template = 'content/body/fields/' . $find . '.tpl';

                if ($templator->templateExists($field_override_template)) {
                    $templator->assign($find, $replace);
                    $replace = $templator->fetch($field_override_template);
                }

                $templator->assign($find, $replace);
            }

            $return = $templator->fetch('string: ' . $template);
        } catch (Exception $e) {
            Log::app($e->getTraceAsString(), $e->getMessage());

            throw $e;
        }

        $templator->disableSecurity();

        return $return;
    }


    /**
     * @param $content_iteration_uid
     * @return array
     */
    public function getContentBodyTypeByIterationUid($content_iteration_uid)
    {
        $sql = "
            SELECT DISTINCT
                cbt.type_id,
                cbt.parent_type_id,
                cbt.label,
                cbt.description
            FROM
                content_body_field_values AS cbfv
            INNER JOIN
                content_body_fields AS cbf
                ON cbfv.content_body_field_uid = cbf.uid
            INNER JOIN
                content_body_types AS cbt
                ON cbf.content_body_type_id = cbt.type_id
            WHERE
                cbfv.content_iteration_uid = ?
            AND 
                cbfv.archived = '0'
            AND 
                cbf.archived = '0'
            AND 
                cbt.archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $result;
    }


    /**
     * @return array
     */
    public function getContentBodyTypes()
    {
        $sql = "
            SELECT
                type_id,
                parent_type_id,
                label,
                description
            FROM
                content_body_types
            WHERE
                archived = '0';
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $content_body_type_id
     * @return array
     */
    public function getContentBodyType($content_body_type_id)
    {
        $sql = "
            SELECT
                type_id,
                parent_type_id,
                label,
                description
            FROM
                content_body_types
            WHERE
                type_id = ?
            AND
                archived = '0';
        ";

        $bind = [
            $content_body_type_id,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();

        return $result;
    }


    /**
     * @param $content_body_type_id
     * @return array
     */
    public function getParentContentBodyType($content_body_type_id)
    {
        $sql = "
            SELECT
                parent_type_id
            FROM
                content_body_types
            WHERE
                type_id = ?
            AND
                archived = '0';
        ";

        $bind = [
            $content_body_type_id,
        ];

        $db             = new Query($sql, $bind);
        $parent_type_id = $db->fetch();
        $result         = $this->getContentBodyType($parent_type_id);

        return $result;
    }


    /**
     * @param $content_body_type_id
     * @return string
     */
    private function getBodyTemplate($content_body_type_id)
    {
        $sql = "
            SELECT
                template
            FROM
                content_body_templates
            WHERE
                content_body_type_id = ?
            AND 
                archived = '0';
        ";

        $bind = [
            $content_body_type_id,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetch();

        return $result;
    }


    /**
     * @param $content_body_type
     * @param null $content_iteration_uid
     * @return array
     */
    public function getCmsFields($content_body_type, $content_iteration_uid = null)
    {
        $sql = "
            SELECT
                uid,
                content_body_type_id,
                content_body_field_type_id,
                template_token,
                description AS content_body_field_description,
                label AS content_body_field_label
            FROM
                content_body_fields AS cbf
            WHERE
                content_body_type_id = ?
            AND 
                archived = '0'
            ORDER BY
                sort_order;
        ";

        $bind = [
            $content_body_type,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        foreach ($results as $key => $result) {
            if (!empty($content_iteration_uid)) {
                $results[$key]['value']         = $this->getCmsFieldValue($content_iteration_uid, $result['uid']);
            }

            $results[$key]['properties']            = $this->getCmsFieldProperties($result['uid']);
            $results[$key]['content_iteration_uid'] = $content_iteration_uid;
        }

        return $results;
    }


    /**
     * @param $content_field_uid
     * @param null $content_iteration_uid
     * @return array
     */
    public function getCmsField($content_field_uid, $content_iteration_uid = null)
    {
        $sql = "
            SELECT
                uid,
                content_body_type_id,
                content_body_field_type_id,
                template_token,
                description AS content_body_field_description,
                label AS content_body_field_label
            FROM
                content_body_fields AS cbf
            WHERE
                uid = ?
            AND 
                archived = '0';
        ";

        $bind = [
            $content_field_uid,
        ];

        $db                                 = new Query($sql, $bind);
        $result                             = $db->fetchAssoc();
        $result['properties']               = $this->getCmsFieldProperties($result['uid']);
        $result['content_iteration_uid']    = $content_iteration_uid;

        if (!empty($content_iteration_uid)) {
            $result['value']         = $this->getCmsFieldValue($content_iteration_uid, $result['uid']);
        }

        return $result;
    }


    /**
     * @param $content_body_field_uid
     * @return array
     */
    public function getCmsFieldProperties($content_body_field_uid)
    {
        $sql = "
            SELECT
                cbfp.content_body_field_uid,
                cbfp.property,
                cbfp.value,
                p.description
            FROM
                content_body_field_properties AS cbfp
            INNER JOIN
                property AS p ON cbfp.property = p.property
            WHERE
                cbfp.content_body_field_uid = ?
            AND
                cbfp.archived = '0'
            AND 
                p.archived = '0';
        ";

        $bind = [
            $content_body_field_uid,
        ];

        $db         = new Query($sql, $bind);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $content_iteration_uid
     * @param $content_body_field_uid
     * @return string
     */
    private function getCmsFieldValue($content_iteration_uid, $content_body_field_uid)
    {
        $sql = "
            SELECT
                value
            FROM
                content_body_field_values
            WHERE
                content_iteration_uid = ?
            AND 
                content_body_field_uid = ?
            AND    
                archived = '0';
        ";

        $bind = [
            $content_iteration_uid,
            $content_body_field_uid,
        ];

        $db     = new Query($sql, $bind);
        $result = $db->fetch();

        return $result;
    }


    /**
     * @param $content_body_type
     * @param Templator|null $templator
     * @param null $content_iteration_uid
     * @return string
     * @throws SmartyException
     */
    public function showCmsFieldsBlock($content_body_type, Templator $templator = null, $content_iteration_uid = null)
    {
        $templator  = $templator ?? new Templator();
        $cms_fields = $this->getCmsFields($content_body_type, $content_iteration_uid);
        $output     = [];

        foreach ($cms_fields as $row) {
            foreach ($row as $key => $val) {
                $templator->assign($key, $val);
            }

            $output[] = $templator->fetch('admin/content/field_types/' . $row['content_body_field_type_id'] . '.tpl');
        }

        return implode($output);
    }


    /**
     * @param $content_body_type_id
     * @param array $field_data
     * @return bool|string
     * @throws SmartyException
     */
    public function generateTemplatedUri($content_body_type_id, array $field_data)
    {
        $templator  = new Templator();
        $sql        = "
            SELECT DISTINCT
                uri_scheme
            FROM
                content_body_templates
            WHERE
                content_body_type_id = ?
            AND 
                archived = '0';
        ";

        $bind = [
            $content_body_type_id,
        ];

        $db         = new Query($sql, $bind);
        $template   = $db->fetch();

        foreach ($field_data as $key => $val) {
            $templator->assign($key, $val);
        }

        $uri    = $templator->fetch('string: ' . $template);
        $uri    = Url::convert($uri);

        return $uri;
    }


    /**
     * @param array $data
     * @param PdoMySql|null $transaction
     * @return bool
     */
    public function insertContentBodyFieldValue(array $data, PdoMySql $transaction = null)
    {
        $sql = "
            INSERT INTO
                content_body_field_values (
                    content_iteration_uid,
                    content_body_field_uid,
                    `value`
                ) VALUES (
                    ?,
                    ?,
                    ?
                );
        ";

        $bind = [
            $data['content_iteration_uid'],
            $data['content_body_field_uid'],
            $data['value'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }
}