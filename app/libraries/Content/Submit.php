<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/2/18
 *
 * Submit.php
 *
 * Create/edit pages
 *
 **/

namespace Content;

use Db\PdoMySql;
use Db\Query;
use Exception;
use Settings;
use Uri\Redirect;
use Uri\Uri;
use User\Account;
use User\Roles;

class Submit
{
    public $post_data   = [];
    public $errors      = [];

    public $new_uid, $uri_uid, $content_uid, $parent_uid, $content_body_type_id, $json_upsert_status, $body, $content,
        $process_type, $generated_page_uri;


    /**
     * @param array $post_data
     * @throws Exception
     */
    public function __construct($post_data = [])
    {
        $post_data = empty($post_data) ? $_POST : $post_data;

        if(empty($post_data))
            throw new Exception('A post data array is required for ' . get_class($this));
        
        $this->post_data    = $post_data;
        $this->content      = new Get();
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function upsert()
    {
        $content_uid = $this->post_data['content_uid'];

        if (empty($content_uid)) {
            self::addPageCheck();
            $this->process_type = 'new';
        } else {
            self::editPageCheck();
            $this->process_type = 'update';
        }

        $this->validatePostData();

        if (!empty($this->errors))
            return false;

        $old_uid        = $this->post_data['uid'];
        $this->new_uid  = $this->buildIterationHash();
        $transaction    = new PdoMySql();

        $transaction->beginTransaction();

        try {
            // process URI
            $this->processUri($transaction);

            if (empty($content_uid)) {
                $this->insertContent($transaction, $this->parent_uid, $this->post_data['content_body_type_id'], $this->uri_uid);
                $this->content_uid = $this->getContentUid($transaction);
            } else {
                $this->content_uid = $content_uid;
                $this->updateContent($transaction);
            }

            // new iteration of same page
            if ($old_uid != $this->new_uid && !empty($old_uid) && !$this->iterationExists())  {
                $this->insertIteration($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->updateCurrentIteration($transaction);
                $this->processCmsValues($transaction);

            // a previous iteration of the same page
            } elseif ($old_uid != $this->new_uid && !empty($old_uid) && $this->iterationExists()) {
                $this->updateCurrentIteration($transaction);

            // new page altogether. the iteration does not exist already
            } elseif(empty($old_uid) && !$this->iterationExists()) {
                $this->insertIteration($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->insertCurrentIteration($transaction);
                $this->processCmsValues($transaction);
            }

            // add/remove page roles
            if ($this->archivePageRoles($transaction) && !empty($this->post_data['content_roles'])) {
                $this->insertContentRoles($transaction);
            } elseif(empty($this->post_data['content_roles'])) {
                $this->archivePageRoles($transaction);
            }

            $this->generateJsonUpsertStatus('status', 'success');
        } catch(Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getMessage() . $e->getTraceAsString();
            $this->generateJsonUpsertStatus('status', $e->getMessage() . $e->getTraceAsString());
            $this->checkAndThrowException();

            return false;
        }

        $this->clearContentCache();

        $transaction->commit();

        return true;
    }


    /**
     * @return bool
     */
    private function clearContentCache()
    {
        $cache_key = $this->content_uid . '_';
        $this->content->cache->archiveLike($cache_key, false, true);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @throws Exception
     */
    private function processCmsValues(PdoMySql $transaction)
    {
        $cms_fields = $this->content->body->getCmsFields($this->content_body_type_id);

        if (!empty($cms_fields)) {
            foreach ($cms_fields as $row => $items) {
                foreach ($this->post_data as $key => $value) {
                    if ($items['template_token'] == $key) {
                        $insert[$key] = [
                            'content_iteration_uid'     => $this->new_uid,
                            'content_body_field_uid'    => $items['uid'],
                            'value'                     => htmlentities($value, ENT_QUOTES, 'UTF-8', false),
                        ];

                        $this->content->body->insertContentBodyFieldValue($insert[$key], $transaction);
                    }
                }
            }
        } else {
            $this->errors[] = 'No CMS fields to process!';
            $this->checkAndThrowException();
        }
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
     * @return bool
     * @throws Exception
     */
    public function archive()
    {
        self::archivePageCheck();

        $transaction    = new PdoMySql();
        $this->uri_uid  = $this->post_data['uri_uid'];
        $uri            = Get::uri($this->post_data['uri_uid']);

        $transaction->beginTransaction();

        if (trim($uri, '/') == 'home') {
            throw new Exception('Cannot delete the home page!');
        }

        try {
            $this->content_uid = $this->getContentUid($transaction);
            $this->archiveOldUri($transaction);
            $this->archivePageRoles($transaction);
            $this->archiveCurrentIteration($transaction, $this->content_uid);
            $this->archivePageIteration($transaction);
            $this->archiveContent($transaction, $this->content_uid);
        } catch(Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getTraceAsString();

            $this->checkAndThrowException();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     */
    private function archivePageIteration(PdoMySql $transaction)
    {
        $sql = "
            UPDATE content_iteration
            SET archived = '1',
            archived_datetime = NOW()
            WHERE uid = ?
            AND archived = '0'
        ";

        $bind = [
            $this->post_data['uid'],
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     * @throws Exception
     */
    private function archivePageRoles(PdoMySql $transaction)
    {
        self::editPageCheck();

        $sql = "
            UPDATE content_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE content_iteration_uid = ?
            AND archived = '0'
        ";

        $bind = [$this->new_uid];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     * @throws Exception
     */
    function insertContentRoles(PdoMySql $transaction)
    {
        self::editPageCheck();

        $roles          = new Roles();
        $all_roles      = $roles->getAll();
        $content_roles  = $this->post_data['content_roles']; // array

        foreach ($all_roles as $role) {
            $sql    = null;
            $bind   = [];

            if (in_array($role['role_name'], $content_roles)) {
                $sql .= "
                  INSERT INTO content_roles (content_iteration_uid, role_name)
                  VALUES (?, ?);
                ";

                $bind[] = $this->new_uid;
                $bind[] = $role['role_name'];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        }

        return true;
    }


    /**
     * @throws Exception
     */
    public static function editPageCheck()
    {
        if (!Settings::value('edit_content'))
            throw new Exception('Not allowed to edit content');
    }


    /**
     * @throws Exception
     */
    public static function archivePageCheck()
    {
        if (!Settings::value('archive_content'))
            throw new Exception('Not allowed to archive pages');
    }


    /**
     * @throws Exception
     */
    public static function addPageCheck()
    {
        if (!Settings::value('add_content'))
            throw new Exception('Not allowed to add content');
    }


    /**
     * @param PdoMySql $transaction
     * @param $parent_uid
     * @param $content_body_type_id
     * @param $uri_uid
     * @param null $content_uid
     * @param null $created_datetime
     * @param null $modified_datetime
     * @return bool
     * @throws Exception
     */
    public static function insertContent(PdoMySql $transaction, $parent_uid, $content_body_type_id, $uri_uid, $content_uid = null, $created_datetime = null, $modified_datetime = null)
    {
        if (empty($content_uid)) {
            $sql = "
                INSERT INTO content (parent_uid, content_body_type_id, uri_uid)
                VALUES (?, ?, ?);
            ";

            $bind = [
                $parent_uid,
                $content_body_type_id,
                $uri_uid,
            ];

            $inserted = $transaction
                ->prepare($sql)
                ->execute($bind);
        } else {
            $sql = "
                INSERT INTO content (uid, parent_uid, content_body_type_id, uri_uid, created_datetime, modified_datetime)
                VALUES (?, ?, ?, ?, ?, ?);
            ";

            $bind = [
                $content_uid,
                $parent_uid,
                $content_body_type_id,
                $uri_uid,
                $created_datetime ?? date('Y-m-d H:i:s'),
                $modified_datetime ?? date('Y-m-d H:i:s'),
            ];

            $inserted = $transaction
                ->prepare($sql)
                ->execute($bind);
        }

        return $inserted;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uid|null
     * @param $content_uid|null
     * @return bool
     */
    private function insertCurrentIteration(PdoMySql $transaction, $iteration_uid = null, $content_uid = null)
    {
        $sql = "
            INSERT INTO current_content_iteration 
            (content_iteration_uid, content_uid) VALUES (?, ?);
        ";

        $bind = [
            $iteration_uid ?? $this->new_uid,
            $content_uid ?? $this->content_uid,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     */
    private function insertIteration(PdoMySql $transaction)
    {
        $sql        = "
            INSERT INTO content_iteration
            (uid, page_title_seo, page_title_h1, meta_desc, meta_robots, generated_page_uri, status, include_in_sitemap, minify_html_output)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
        ";

        $bind = [
            $this->new_uid,
            $this->post_data['page_title_seo'],
            $this->post_data['page_title_h1'],
            $this->post_data['meta_desc'],
            $this->post_data['meta_robots'],
            $this->generated_page_uri,
            $this->post_data['status'],
            !empty($this->post_data['include_in_sitemap']) ? '1' : '0',
            !empty($this->post_data['minify_html_output']) ? '1' : '0',
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     */
    private function insertIterationCommitInfo(PdoMySql $transaction)
    {
        $account    = new Account();
        $my_account = $account->getAccountFromSessionValidation();
        $sql        = "
            INSERT INTO content_iteration_commits (content_uid, content_iteration_uid, author, iteration_description)
            VALUES (?, ?, ?, ?);
        ";

        $bind = [
            $this->content_uid,
            $this->new_uid,
            $my_account['username'],
            $this->post_data['content_iteration_message'],
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $content_iteration_uid|null
     * @param $content_uid|null
     * @return bool
     */
    public function updateCurrentIteration(PdoMySql $transaction, $content_iteration_uid = null, $content_uid = null)
    {
        $updated = false;

        if ($this->archiveCurrentIteration($transaction, $content_uid)) {
            $updated = $this->insertCurrentIteration($transaction, $content_iteration_uid, $content_uid);
        }

        return $updated;
    }


    /**
     * @param PdoMySql $transaction
     * @param $content_uid|null
     * @return bool
     */
    private function archiveCurrentIteration(PdoMySql $transaction, $content_uid = null)
    {
        $sql = "
            UPDATE current_content_iteration
            SET archived = '1', archived_datetime = NOW()
            WHERE content_uid = ?
            AND archived = '0';
        ";

        $bind = [
            $content_uid ?? $this->content_uid,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param $content_uid
     * @return bool
     */
    public static function archiveContent(PdoMySql $transaction, $content_uid)
    {
        $sql = "
            UPDATE content
            SET archived = '1', archived_datetime = NOW()
            WHERE uid = ?
            AND archived = '0';
        ";

        $bind = [
            $content_uid,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param $parent_content_uid
     * @param $old_uri_uid
     * @param $new_uri_uid
     * @return bool
     * @throws Exception
     */
    private function updateContent(PdoMySql $transaction)
    {
        $content    = $this->content->contentByUid($this->content_uid, 'active', true);

        if (empty($content)) {
            $content    = $this->content->contentByUid($this->content_uid, 'inactive', true);
        }

        $current_uri_uid        = $content['uri_uid'];
        $new_uri_uid            = $this->uri_uid;

        $current_parent_uid     = $content['parent_content_uid'];
        $content_body_type_id   = $content['content_body_type_id'];
        $new_parent_uid         = $this->parent_uid;

        if ($current_uri_uid == $new_uri_uid && $current_parent_uid == $new_parent_uid) {
            $return = true;
        } else {
            $content_uid        = $this->content_uid;
            $parent_content_uid = $this->parent_uid;
            $created_datetime   = $content['created_datetime'];
            $modified_datetime  = null; // preserve natural modified datetime value

            $archive            = $this->archiveContent($transaction, $content_uid);
            $insert             = $this->insertContent($transaction, $parent_content_uid, $content_body_type_id, $new_uri_uid, $content_uid, $created_datetime);

            $return             = ($archive && $insert);
        }

        return $return;
    }


    /**
     * @return string
     */
    private function buildIterationHash()
    {
        $serialized_post_array = serialize($this->post_data);

        return hash('sha512', $serialized_post_array);
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     * @throws Exception
     */
    protected function processUri(PdoMySql $transaction)
    {
        $current_uri_uid        = $this->post_data['uri_uid'] ?? null;
        $current_uri            = Get::uri($current_uri_uid);
        $content_body_type_id   = $this->post_data['content_body_type_id'];
        $this_page_uri          = $this->content->body->generateTemplatedUri($content_body_type_id, $this->post_data);
        $parent_content_uid     = !empty($this->post_data['parent_content_uid']) ? $this->post_data['parent_content_uid'] : null;
        $parent_uri             = null;
        $real_parent_uri        = null;

        if (!empty($parent_content_uid)) {
            $parent_content     = $this->content->contentByUid($parent_content_uid, 'active', true);
            $real_parent_uri    = $this->content->contentUriAncestry($parent_content['content_uid']);

            $this->parent_uid = $parent_content['content_uid'];
        } else {
            $this->parent_uid = null;
        }

        $new_full_uri   = '/' . trim($real_parent_uri . '/' . $this_page_uri, '/');

        $this->content_body_type_id = $content_body_type_id;
        $this->generated_page_uri   = $this_page_uri;

        // Check homepage URI change (not allowed!)
        if ($current_uri == '/home') {
            $new_full_uri = '/home';
            
            $this->generated_page_uri = null;
        }

        // user changed URI to one that already exists...
        if (($current_uri != $new_full_uri && ($this->uriExistsAsContent($new_full_uri)) || empty($new_full_uri))) {
            $this->checkAndThrowException();

        // URI already exists as a redirect, but we'll repurpose it for the content instead
        } elseif($current_uri != $new_full_uri && Uri::uriExistsAsRedirect($new_full_uri)) {
            $this->uri_uid = $this->removeRedirectUri($transaction, $new_full_uri);

            $this->insertRedirectUri($transaction, $current_uri_uid, $new_full_uri);

        // user changed URI and it's unique (or created a new URI)
        } elseif ($current_uri != $new_full_uri && !$this->uriExistsAsContent($new_full_uri)) {
            $this->insertUri($transaction, $new_full_uri);

            $this->uri_uid = $this->getUriUid($transaction, $new_full_uri);

            if (!empty($current_uri_uid)) {
                /*
                 * NOTE
                 * instead of archiving the old URI, we simply want to add an entry in the uri_redirects table and keep it
                 * active. that way, the old URI still exists, but redirects to the new one (301 Moved Permanently)
                 */
                $this->insertRedirectUri($transaction, $current_uri_uid, $new_full_uri);
            }
        } else {
            $this->uri_uid = $current_uri_uid;
        }

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri_uid
     * @param $destination_url
     * @return bool
     * @throws Exception
     */
    private function insertRedirectUri(PdoMySql $transaction, $uri_uid, $destination_url)
    {
        $uri_redirect       = new Redirect();
        $exists             = !empty($uri_redirect->getByUriUid($uri_uid));

        $data               = [
            'uri_uid'           => $uri_uid,
            'destination_url'   => $destination_url,
            'http_status_code'  => 301,
            'description'       => 'Page URI was updated via the page editor',
        ];

        if ($exists) {
            $inserted_redirect  = $uri_redirect->update($data, $transaction);
        } else {
            $inserted_redirect  = $uri_redirect->insert(
                $data,
                $transaction
            );
        }

        return $inserted_redirect;
    }


    /**
     * @param PdoMySql $transaction
     * @return bool
     */
    private function archiveOldUri(PdoMySql $transaction)
    {
        $sql = "
            UPDATE uri
            SET archived = '1',
            archived_datetime = NOW()
            WHERE uid = ?
            AND archived = '0';
        ";

        $bind = [
            $this->uri_uid,
        ];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri
     * @return bool
     */
    private function insertUri(PdoMySql $transaction, $uri)
    {
        $uri_obj = new Uri($transaction);

        return (!Uri::uriExistsAsRedirect($uri) && !Uri::uriExistsAsContent($uri)) ? $uri_obj->insertUri($uri) : true;
    }


    private function removeRedirectUri(PdoMySql $transaction, $uri)
    {
        $redir      = new Redirect();
        $uri_uid    = $this->getUriUid($transaction, $uri);
        $archived   = $redir->archive($uri_uid, $transaction) ? $uri_uid : null;

        return $archived;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri
     * @return mixed
     */
    private function getUriUid(PdoMySql $transaction, $uri)
    {
        $uri_obj = new Uri($transaction);
        $uri     = $uri_obj->getUriUid($uri);

        return $uri;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri_uid
     * @return mixed
     */
    private function getContentUId(PdoMySql $transaction, $uri_uid = null)
    {
        $sql = "
            SELECT uid
            FROM content
            WHERE uri_uid = ?
        ";

        $bind   = [$uri_uid ?? $this->uri_uid];
        $result = $transaction->prepare($sql);

        $result->execute($bind);

        $uid = $result->fetchColumn();

        return $uid;
    }


    /**
     * @return bool
     */
    private function validatePostData()
    {
        // Exceptions for date fields
        if (isset($this->post_data['published_date']) && $this->process_type == 'new') {
            $this->post_data['published_date'] = time();
        }

        if (isset($this->post_data['published_date']) && $this->process_type == 'update') { // (edge case)
            $strtotime = strtotime($this->post_data['published_date']);

            $this->post_data['published_date'] = $strtotime !== false ? $strtotime : $this->post_data['published_date'];
        }

        if (isset($this->post_data['revised_date']) && $this->process_type == 'update') {
            $this->post_data['revised_date'] = time();
        }

        if (isset($this->post_data['revised_date']) && $this->process_type == 'new') {
            $this->post_data['revised_date'] = null;
        }

        return true;
    }


    /**
     * @return bool
     */
    private function iterationExists()
    {
        $sql = "
            SELECT COUNT(*)
            FROM content_iteration 
            WHERE uid = ?
            AND archived = '0';
        ";

        $db     = new Query($sql, [$this->new_uid]);
        $count  = $db->fetch();

        return ($count > 0);
    }


    /**
     * @param string $uri
     * @return bool
     */
    private function uriExistsAsContent($uri)
    {
        $exists_as_content  = Uri::uriExistsAsContent($uri);
        $return             = false;

        if ($exists_as_content) {
            $this->errors[] = 'The URI, ' . $uri . ', already exists for another content item.';
            $return         = true;
        }

        return $return;
    }


    /**
     * @throws Exception
     */
    private function checkAndThrowException()
    {
        if (!empty($this->errors)) {
            $errors = implode('; ', $this->errors);

            throw new Exception($errors);
        }

        return true;
    }
}