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

namespace Content\Pages;

use Db\PdoMySql;
use Db\Query;
use ErrorException;
use Exception;
use Settings;
use Uri\Redirect;
use Uri\Uri;
use User\Account;
use User\Roles;

class Submit
{
    public $post_data       = [];
    public $errors          = [];
    public $new_uid         = false;
    public $uri_uid         = false;
    public $content_uid = false;

    public $json_upsert_status;


    /**
     * @param array $post_data
     * @throws Exception
     */
    public function __construct($post_data = [])
    {
        $post_data = empty($post_data) ? $_POST : $post_data;

        if(empty($post_data))
            throw new Exception('A post data array is required for ' . get_class($this));
        
        $this->post_data = $post_data;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function upsert()
    {
        $this->validatePostData();

        if (!empty($this->errors))
            return false;

        $old_uid        = $this->post_data['uid'];
        $this->new_uid  = $this->buildIterationHash();

        $transaction = new PdoMySql();

        $transaction->beginTransaction();

        try {
            // process URI changes
            $this->processUri($transaction);

            // new iteration of same page
            if ($old_uid != $this->new_uid && !empty($old_uid) && !$this->iterationExists())  {
                $this->content_uid = $this->getContentUid($transaction);
                $this->insertIteration($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->updateCurrentIteration($transaction);

            // a previous iteration of the same page
            } elseif($old_uid != $this->new_uid && !empty($old_uid) && $this->iterationExists()) {
                $this->content_uid = $this->getContentUid($transaction);
                $this->updateCurrentIteration($transaction);

            // new page altogether. the iteration does not exist already
            } elseif(empty($old_uid) && !$this->iterationExists()) {
                $this->insertContent($transaction);
                $this->content_uid = $this->getContentUid($transaction);
                $this->insertIteration($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->insertCurrentIteration($transaction);
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

            $this->errors[] = $e->getMessage();

            $this->generateJsonUpsertStatus('status', $e->getMessage());

            return false;
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
     * @return bool
     * @throws Exception
     */
    public function archive()
    {
        self::archivePageCheck();

        $transaction    = new PdoMySql();
        $this->uri_uid  = $this->post_data['original_uri_uid'];
        $uri            = Get::uri($this->post_data['original_uri_uid']);

        $transaction->beginTransaction();

        if (trim($uri, '/') == 'home') {
            throw new Exception('Cannot delete the home page!');
        }

        try {
            $this->content_uid = $this->getContentUid($transaction);
            $this->archiveOldUri($transaction);
            $this->archivePageRoles($transaction);
            $this->archiveCurrentIteration($transaction);
            $this->archivePageIteration($transaction);
            $this->archiveContent($transaction);
        } catch(Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getTraceAsString();

            $this->checkAndThrowErrorException();

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
    function archivePageRoles(PdoMySql $transaction)
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

        $roles      = new Roles();
        $all_roles  = $roles->getAll();
        $content_roles = $this->post_data['content_roles']; // array

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
        if (!Settings::value('edit_pages'))
            throw new Exception('Not allowed to edit pages');
    }


    /**
     * @throws Exception
     */
    public static function archivePageCheck()
    {
        if (!Settings::value('archive_pages'))
            throw new Exception('Not allowed to archive pages');
    }


    /**
     * @throws Exception
     */
    public static function addPageCheck()
    {
        if (!Settings::value('add_pages'))
            throw new Exception('Not allowed to add pages');
    }


    /**
     * @param PdoMySql $transaction
     * @param $content_uid
     * @param $uri_uid
     * @return bool
     * @throws Exception
     */
    private function insertContent(PdoMySql $transaction, $content_uid = null, $uri_uid = null)
    {
        self::addPageCheck();

        if (empty($content_uid)) {
            $sql = "
                INSERT INTO content (uri_uid)
                VALUES (?);
            ";

            $bind = [
                $uri_uid ?? $this->uri_uid,
            ];
        } else {
            $sql = "
                INSERT INTO content (uid, uri_uid)
                VALUES (?, ?);
            ";

            $bind = [
                $content_uid,
                $uri_uid ?? $this->uri_uid,
            ];
        }

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
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
     *
     */
    private function insertIteration(PdoMySql $transaction)
    {
        $sql        = "
            INSERT INTO content_iteration
            (uid, page_title_seo, page_title_h1, meta_desc, meta_robots, content, status, include_in_sitemap, minify_html_output)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
        ";

        $bind = [
            $this->new_uid,
            $this->post_data['page_title_seo'],
            $this->post_data['page_title_h1'],
            $this->post_data['meta_desc'],
            $this->post_data['meta_robots'],
            htmlspecialchars($this->post_data['body'], ENT_NOQUOTES),
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

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $content_uid
     * @return bool
     */
    private function archiveContent(PdoMySql $transaction, $content_uid = null)
    {
        $sql = "
            UPDATE content
            SET archived = '1', archived_datetime = NOW()
            WHERE uid = ?;
        ";

        $bind = [
            $content_uid ?? $this->content_uid,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $old_uri_uid
     * @param $new_uri_uid
     * @return bool
     * @throws Exception
     */
    private function updateContent(PdoMySql $transaction, $old_uri_uid = null, $new_uri_uid = null)
    {
        $old_uri_uid    = $old_uri_uid ?? $this->post_data['original_uri_uid'];
        $new_uri_uid    = $new_uri_uid ?? $this->uri_uid;
        $content_uid    = $this->getContentUid($transaction, $old_uri_uid);

        if (!empty($this->post_data['original_uri_uid'])) {
            $this->archiveContent($transaction, $content_uid);
            $this->insertContent($transaction, $content_uid, $new_uri_uid);
        }

        return true;
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
    private function processUri(PdoMySql $transaction)
    {
        $this->uri_uid          = $this->post_data['original_uri_uid'];
        $old_uri                = Get::uri($this->uri_uid);
        $old_uri_as_array       = Utilities::uriAsArray($old_uri);
        $parent_page_uri        = Get::uri($this->post_data['parent_page_uri']);
        $this_uri_piece         = $this->post_data['this_uri_piece'];
        $new_uri                = rtrim($parent_page_uri . '/' . $this_uri_piece, '/');
        $new_uri_as_array       = Utilities::uriAsArray($new_uri);
        $all_uris               = Get::allUris();

        if ($old_uri == '/home' && $old_uri != $new_uri) {
            $this->errors[] = 'The home page URI cannot be edited';
            $this->checkAndThrowErrorException();
        }

        // user changed URI and it matches an existing
        if (($old_uri != $new_uri && ($this->uriExists($new_uri)) || empty($new_uri))) {
            $this->checkAndThrowErrorException();

        // user changed URI and it's unique (or it doesn't exist)
        } elseif($old_uri != $new_uri && !$this->uriExists($new_uri)) {
            /*
             * NOTE
             * instead of archiving the old URI, we simply want to add an entry in the uri_redirects table and keep it
             * active. that way, the old URI still exists, but redirects to the new one (301 Moved Permanently)
             */
            if (!empty($this->uri_uid)) {
                $this->insertRedirectUri($transaction, $this->uri_uid, $new_uri . '/');
            }

            $this->insertUri($transaction, $new_uri);
            $this->uri_uid = $this->getUriUid($transaction, $new_uri);

            $this->updateContent($transaction);
        }

        // user changed URI. loop through all URIs and attempt to find a match to also update child URIs
        if (!empty($old_uri) && $old_uri != $new_uri) {
            foreach ($all_uris as $uri_result) {
                $result_uri             = $uri_result['uri'];
                $result_uri_as_array    = Utilities::uriAsArray($result_uri);

                foreach($old_uri_as_array as $key => $val) {
                    $ignore_uid = $this->getUriUid($transaction, $old_uri);

                    if (!empty($result_uri_as_array[$key]) && ($old_uri_as_array[$key] == $result_uri_as_array[$key]) && $uri_result['uid'] != $ignore_uid) {
                        $this->updateUri($transaction, $uri_result['uid'], $new_uri_as_array);
                    }
                }
            }
        }

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri_uid
     * @param $destination_url
     * @return bool
     */
    private function insertRedirectUri(PdoMySql $transaction, $uri_uid, $destination_url)
    {
        $uri_redirect       = new Redirect();

        $data               = [
            'uri_uid'           => $uri_uid,
            'destination_url'   => $destination_url,
            'http_status_code'  => 301,
            'description'       => 'Page URI was updated via the page editor',
        ];

        $inserted_redirect  = $uri_redirect->insert(
            $data,
            $transaction
        );

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

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri
     * @return bool
     */
    private function insertUri(PdoMySql $transaction, $uri)
    {
        $uri_obj = new Uri($transaction);

        return $uri_obj->insertUri($uri);
    }


    /**
     * @param PdoMySql $transaction
     * @param $uri
     * @return mixed
     */
    private function getUriUid(PdoMySql $transaction, $uri)
    {
        $uri_obj = new Uri($transaction);

        return $uri_obj->getUriUid($uri);
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

        return $result->fetchColumn();
    }


    /**
     * @param PdoMySql $transaction
     * @param $old_uri_uid
     * @param $new_uri_as_array
     * @return bool
     * @throws Exception
     */
    private function updateUri(PdoMySql $transaction, $old_uri_uid, array $new_uri_as_array)
    {
        $old_uri            = Get::uri($old_uri_uid);                                           // foo/bar/baz/lorem/child/pages/several/levels/down/with/commonly/changed/upper-uri
        $old_uri_as_array   = Utilities::uriAsArray($old_uri);                                  // [0] => foo, [1] => bar, [2] => baz, [3] => lorem ......... [12] => upper-uri
        $updated_uri_array  = array_replace($old_uri_as_array, $new_uri_as_array);              // [0] => foo, [1] => bar, [2] => baz, [3] => lorem-ipsum ... [12] => upper-uri
        $updated_uri        = Utilities::arrayAsUri($updated_uri_array);                        // foo/bar/baz/lorem-ipsum

        $insert_new_uri = $this->insertUri($transaction, $updated_uri);
        $new_uri_uid    = $this->getUriUid($transaction, $updated_uri);
        $add_redirect   = $this->insertRedirectUri($transaction, $old_uri_uid, $updated_uri);
        $update_pages   = $this->updateContent($transaction, $old_uri_uid, $new_uri_uid);

        return ($insert_new_uri && $add_redirect && $update_pages);
    }


    /**
     * @return bool
     */
    private function validatePostData()
    {
        // TODO - validatePostData() implies that we're validating all the $this->post_data. looks like we're only validating one field though - so rename the function to validateUriPiece() or actually do some more validation!

        preg_match('~[^a-z0-9\-]~', $this->post_data['this_uri_piece'],$fail_matches);

        if (!empty($fail_matches))
            $this->errors[] = 'URI submission must be constructed with lowercase alphanumeric characters and dashes only.';

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
    private function uriExists($uri)
    {
        $exists_as_content  = Uri::uriExistsAsContent($uri);
        $exists_as_redirect = Uri::uriExistsAsRedirect($uri);

        $return             = false;

        if ($exists_as_content) {
            $this->errors[] = 'The URI, ' . $uri . ', already exists for another content item.';
            $return         = true;
        } elseif ($exists_as_redirect) {
            $this->errors[] = 'The URI, ' . $uri . ', already exists as a redirect to another URL.';
            $return         = true;
        }

        return $return;
    }


    /**
     * @throws Exception
     */
    private function checkAndThrowErrorException()
    {
        if (!empty($this->errors)) {
            $errors = implode('; ', $this->errors);

            throw new ErrorException($errors);
        }

        return true;
    }
}