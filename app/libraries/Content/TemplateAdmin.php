<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 12/26/20
 *
 * TemplateAdmin.php
 *
 * Library for managing content body types and fields
 *
 **/

namespace Content;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Log;
use Seo\Url;
use Utilities\Sanitize;

class TemplateAdmin
{
    public $content_get;

    public function __construct()
    {
        $this->content_get = new Get();
    }


    /**
     * @param array $sort_order_to_uuids
     * @return bool
     * @throws Exception
     */
    public function sortOrderBulk(array $sort_order_to_uuids)
    {
        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        foreach ($sort_order_to_uuids as $key => $uid) {
            $key                = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $uid                = Sanitize::string($uid);
            $current_field_data = $this->content_get->body->getCmsField($uid);

            $data['uid']                        = $uid;
            $data['content_body_type_id']       = $current_field_data['content_body_type_id'];
            $data['content_body_field_type_id'] = $current_field_data['content_body_field_type_id'];
            $data['label']                      = $current_field_data['content_body_field_label'];
            $data['description']                = $current_field_data['content_body_field_description'];
            $data['template_token']             = $current_field_data['template_token'];
            $data['sort_order']                 = (int)$key;

            try {
                $this->archiveContentBodyField($uid, $transaction);
                $this->insertContentBodyField($data, $transaction);
                $this->generateJsonUpsertStatus('status', 'success');
            } catch(Exception $e) {
                $transaction->rollBack();
                $this->generateJsonUpsertStatus('status', $e->getMessage());

                Log::app($e);

                throw $e;
            }
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param $status
     * @param $message
     * @return bool
     */
    private function generateJsonUpsertStatus($status, $message)
    {
        $status_message = [
            $status => $message,
        ];

        $this->json_upsert_status = json_encode($status_message);

        return true;
    }


    /**
     * @param $uid
     * @param PdoMySql $transaction
     */
    public function archiveContentBodyField($uid, PdoMySql $transaction)
    {
        $sql = "
            UPDATE
                content_body_fields
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                uid = ?
            AND
                archived = '0';
        ";

        $bind = [
            $uid,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $data
     * @param PdoMySql $transaction
     * @return bool
     */
    public function insertContentBodyField(array $data, PdoMySql $transaction)
    {
        $bind = [
            'uid'                           => !empty($data['uid']) ? Sanitize::string($data['uid']) : Query::getUuid(),
            'content_body_type_id'          => Sanitize::string($data['content_body_type_id']),
            'content_body_field_type_id'    => Sanitize::string($data['content_body_field_type_id']),
            'label'                         => Sanitize::string($data['label']),
            'description'                   => Sanitize::string($data['description']),
            'template_token'                => Sanitize::string($data['template_token']),
            'sort_order'                    => !empty($data['sort_order']) ? Sanitize::string($data['sort_order']) : (int)0,
        ];

        $sql = "
            INSERT INTO
                content_body_fields (
                    uid,
                    content_body_type_id,
                    content_body_field_type_id,
                    label,
                    description,
                    template_token,
                    sort_order
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                );
        ";

        return $transaction
            ->prepare($sql)
            ->execute(array_values($bind));

    }


    /**
     * @return array
     */
    public function getCmsFieldTypes()
    {
        $sql = "
            SELECT
                type_id,
                label,
                description
            FROM
                content_body_field_types
            WHERE
                archived = '0'
            ORDER BY
                label;
        ";

        $db         = new Query($sql);
        $results    = $db->fetchAllAssoc();

        return $results;
    }


    /**
     * @param $type_id
     * @param PdoMySql $transaction
     * @return bool
     */
    public function archiveContentBodyType($type_id, PdoMySql $transaction)
    {
        $sql = "
            UPDATE
                content_body_types
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                type_id = ?
            AND
                archived = '0';
        ";

        $bind = [
            $type_id,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param $type_id
     * @param PdoMySql $transaction
     * @return bool
     */
    public function archiveContentBodyTemplate($type_id, PdoMySql $transaction)
    {
        $sql = "
            UPDATE
                content_body_templates
            SET
                archived = '1',
                archived_datetime = NOW()
            WHERE
                content_body_type_id = ?
            AND
                archived = '0';
        ";

        $bind = [
            $type_id,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param array $data
     * @param PdoMySql $transaction
     * @return bool
     */
    public function insertContentBodyType(array $data, PdoMySql $transaction)
    {
        if (empty($data['type_id'])) {
            $data['type_id'] = self::generateTypeId($data['label']);
        }

        $bind = [
            'type_id'               => Sanitize::string($data['type_id']) ?? self::generateTypeId(Sanitize::string($data['label'])),
            'parent_type_id'        => !empty($data['parent_type_id']) ? Sanitize::string($data['parent_type_id']) : $data['type_id'],
            'label'                 => Sanitize::string($data['label']),
            'description'           => Sanitize::string($data['description']),
            'promoted_user_content' => !empty($data['promoted_user_content']) ? '1' : '0',
        ];

        $sql = "
            INSERT INTO
                content_body_types (
                    type_id,
                    parent_type_id,
                    label,
                    description,
                    promoted_user_content
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                );
        ";

        return $transaction
            ->prepare($sql)
            ->execute(array_values($bind));
    }


    /**
     * @param $label
     * @return bool|string|string[]
     */
    private static function generateTypeId($label)
    {
        $seo_transformed_label  = Url::convert($label);
        $underscored_label      = str_replace('-', '_', $seo_transformed_label);

        return $underscored_label;
    }


    /**
     * @param array $data
     * @param PdoMySql $transaction
     * @return bool
     */
    public function insertContentBodyTemplate(array $data, PdoMySql $transaction)
    {
        if (empty($data['type_id'])) {
            $data['type_id'] = self::generateTypeId($data['label']);
        }

        $bind = [
            'content_body_type_id'  => Sanitize::string($data['type_id']),
            'label'                 => Sanitize::string($data['label']) . ' Layout',
            'template'              => htmlentities($data['template'], ENT_COMPAT, 'UTF-8', false),
            'uri_scheme'            => (string)$data['uri_scheme'],
        ];

        $sql = "
            INSERT INTO
                content_body_templates (
                    content_body_type_id,
                    label,
                    template,
                    uri_scheme  
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?
                );
        ";

        return $transaction
            ->prepare($sql)
            ->execute(array_values($bind));
    }
}