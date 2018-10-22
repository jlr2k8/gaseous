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

namespace Pages;

class Submit
{
    public $post_data       = [];
    public $errors          = [];
    public $new_uid         = false;
    public $uri_uid         = false;
    public $page_master_uid = false;


    /**
     * @param array $post_data
     * @throws \Exception
     */
    public function __construct($post_data = [])
    {
        $post_data = empty($post_data) ? $_POST : $post_data;

        if(empty($post_data))
            throw new \Exception('A post data array is required for ' . get_class($this));
        
        $this->post_data = $post_data;
    }


    /**
     *
     */
    public function upsert()
    {
        $this->validatePostData();

        if (!empty($this->errors))
            return false;

        $old_uid        = $this->post_data['uid'];
        $this->new_uid  = $this->buildIterationHash();

        $transaction = new \Db\PdoMySql();

        $transaction->beginTransaction();

        try {
            // process URI changes
            $this->processUri($transaction);

            // new iteration of same page
            if ($old_uid != $this->new_uid && !empty($old_uid) && !$this->iterationExists())  {
                $this->page_master_uid = $this->getMasterPageId($transaction);
                $this->insertIteration($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->updateCurrentIteration($transaction);

            // a previous iteration of the same page
            } elseif($old_uid != $this->new_uid && !empty($old_uid) && $this->iterationExists()) {
                $this->updateCurrentIteration($transaction);

            // new page altogether. the iteration does not exist already
            } elseif(empty($old_uid) && !$this->iterationExists()) {
                $this->insertPage($transaction);
                $this->insertIteration($transaction);
                $this->page_master_uid = $this->getMasterPageId($transaction);
                $this->insertIterationCommitInfo($transaction);
                $this->insertCurrentIteration($transaction);
            }

            // archive/re-add page roles
            if (!empty($this->post_data['page_roles']))
                $this->insertPageRoles($transaction);

        } catch(\Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getTraceAsString();

            $this->checkAndThrowErrorException();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     *
     */
    public function archive()
    {
        self::archivePageCheck();

        $transaction    = new \Db\PdoMySql();
        $this->uri_uid  = $this->post_data['original_uri_uid'];
        $transaction->beginTransaction();

        $uri = \Pages\Get::uri($this->post_data['original_uri_uid']);

        if ($uri == 'home')
        {
            throw new \Exception('Cannot delete the home page!');
        }

        try {
            $this->page_master_uid = $this->getMasterPageId($transaction);
            $this->archiveOldUri($transaction);
            $this->archivePageRoles($transaction);
            $this->archiveCurrentIteration($transaction);
            $this->archivePage($transaction);
        } catch(\Exception $e) {
            $transaction->rollBack();

            $this->errors[] = $e->getTraceAsString();

            $this->checkAndThrowErrorException();

            return false;
        }

        $transaction->commit();

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     * @throws \Exception
     */
    function archivePageRoles(\Db\PdoMySql $transaction)
    {
        self::editPageCheck();

        $sql = "
            UPDATE page_roles
            SET
              archived = '1',
              archived_datetime = NOW()
            WHERE uri_uid = ?
            AND archived = '0'
        ";

        $bind = [$this->uri_uid];

        return $transaction
            ->prepare($sql)
            ->execute($bind);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     * @throws \Exception
     */
    function insertPageRoles(\Db\PdoMySql $transaction)
    {
        self::editPageCheck();

        $roles      = new \User\Roles();
        $all_roles  = $roles->getAll();
        $page_roles = $this->post_data['page_roles']; // array

        foreach ($all_roles as $role) {

            $sql    = null;
            $bind   = [];

            if (in_array($role['role_name'], $page_roles)) {

                $sql .= "
                  INSERT INTO page_roles (page_iteration_uid, role_name)
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
     * @throws \Exception
     */
    public static function editPageCheck()
    {
        if (!\Settings::value('edit_pages'))
            throw new \Exception('Not allowed to edit pages');
    }


    /**
     * @throws \Exception
     */
    public static function archivePageCheck()
    {
        if (!\Settings::value('archive_pages'))
            throw new \Exception('Not allowed to archive pages');
    }


    /**
     * @throws \Exception
     */
    public static function addPageCheck()
    {
        if (!\Settings::value('add_pages'))
            throw new \Exception('Not allowed to add pages');
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     */
    private function insertPage(\Db\PdoMySql $transaction)
    {
        self::addPageCheck();

        $sql = "
            INSERT INTO page (uri_uid)
            VALUES (?);
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
     * @param \Db\PdoMySql $transaction
     */
    private function insertCurrentIteration(\Db\PdoMySql $transaction)
    {
        $sql = "
            INSERT INTO current_page_iteration 
            (page_iteration_uid, page_master_uid) VALUES (?, ?);
        ";

        $bind = [
            $this->new_uid,
            $this->page_master_uid,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     *
     */
    private function insertIteration(\Db\PdoMySql $transaction)
    {
        $sql        = "
            INSERT INTO page_iteration
            (uid, page_title_seo, page_title_h1, meta_desc, meta_robots, content, status, include_in_sitemap)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?);
        ";

        $bind = [
            $this->new_uid,
            $this->post_data['page_title_seo'],
            $this->post_data['page_title_h1'],
            $this->post_data['meta_desc'],
            $this->post_data['meta_robots'],
            htmlspecialchars($this->post_data['body']),
            $this->post_data['status'],
            !empty($this->post_data['include_in_sitemap']) ? '1' : '0',
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     */
    private function insertIterationCommitInfo(\Db\PdoMySql $transaction)
    {
        $account    = new \User\Account();
        $my_account = $account->getAccountFromSessionValidation();
        $sql        = "
            INSERT INTO page_iteration_commits (page_master_uid, page_iteration_uid, author, iteration_description)
            VALUES (?, ?, ?, ?);
        ";

        $bind = [
            $this->page_master_uid,
            $this->new_uid,
            $my_account['username'],
            $this->post_data['page_iteration_message'],
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     */
    public function updateCurrentIteration(\Db\PdoMySql $transaction, $page_iteration_uid = false, $page_master_uid = false)
    {
        $page_iteration_uid = $page_iteration_uid ?: $this->new_uid;
        $page_master_uid    = $page_master_uid ?: $this->page_master_uid;

        $sql = "
            UPDATE current_page_iteration
            SET page_iteration_uid = ?
            WHERE page_master_uid = ?;
        ";

        $bind = [
            $page_iteration_uid,
            $page_master_uid,
        ];
var_dump($sql, $bind);
        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     */
    private function archiveCurrentIteration(\Db\PdoMySql $transaction)
    {
        $sql = "
            UPDATE current_page_iteration
            SET archived = '1', archived_datetime = NOW()
            WHERE page_master_uid = ?;
        ";

        $bind = [
            $this->page_master_uid,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     */
    private function archivePage(\Db\PdoMySql $transaction)
    {
        $sql = "
            UPDATE page
            SET archived = '1', archived_datetime = NOW()
            WHERE page_master_uid = ?;
        ";

        $bind = [
            $this->page_master_uid,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     */
    private function updatePage(\Db\PdoMySql $transaction)
    {
        $sql = "
            UPDATE page
            SET uri_uid = ?
            WHERE uri_uid = ?;
        ";

        $bind = [
            $this->uri_uid,
            $this->post_data['original_uri_uid'],
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param array $post_data
     * @return string
     */
    private function buildIterationHash()
    {
        $serialized_post_array = serialize($this->post_data);

        return hash('sha512', $serialized_post_array);
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return null
     */
    function processUri(\Db\PdoMySql $transaction)
    {
        $this->uri_uid          = $this->post_data['original_uri_uid'];
        $old_uri                = \Pages\Get::uri($this->uri_uid);
        $old_uri_as_array       = \Pages\Utilities::uriAsArray($old_uri);
        $parent_page_uri        = \Pages\Get::uri($this->post_data['parent_page_uri']);
        $this_uri_piece         = $this->post_data['this_uri_piece'];
        $new_uri                = trim($parent_page_uri . '/' . $this_uri_piece, '/');
        $new_uri_as_array       = \Pages\Utilities::uriAsArray($new_uri);
        $all_uris               = \Pages\Get::allUris();

        // user changed URI and it matches an existing
        if ($old_uri != $new_uri && self::uriExists($new_uri)) {
            $this->errors[] = 'The URI, /' . $new_uri . '/, already exists';
            $this->checkAndThrowErrorException();

        // user changed URI and it's unique
        } elseif($old_uri != $new_uri && !self::uriExists($new_uri)) {
            $this->insertUri($transaction, $new_uri);
            $this->archiveOldUri($transaction);
            $this->uri_uid = $this->getUriUid($transaction, $new_uri);
            $this->updatePage($transaction);
        }

        // user changed URI. loop through all URIs and attempt to find a match to also update child URIs
        if (!empty($old_uri) && $old_uri != $new_uri) {
            foreach ($all_uris as $uri_result) {
                $result_uri             = $uri_result['uri'];
                $result_uri_as_array    = \Pages\Utilities::uriAsArray($result_uri);

                $matching_uri_bases = true;

                foreach($old_uri_as_array as $key => $val) {
                    if (empty($result_uri_as_array[$key]) || $old_uri_as_array[$key] != $result_uri_as_array[$key]) {
                        $matching_uri_bases = false;
                        break;
                    }
                }

                if ($matching_uri_bases === true)
                    $this->updateUri($transaction, $uri_result['uid'], $new_uri_as_array);
            }
        }

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @return bool
     */
    function archiveOldUri(\Db\PdoMySql $transaction)
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
     * @param \Db\PdoMySql $transaction
     * @param $uri
     * @return bool
     */
    function insertUri(\Db\PdoMySql $transaction, $uri)
    {
        $sql = "
            INSERT INTO uri (uri)
            VALUES(?);
        ";

        $bind = [trim($uri, '/')];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param $uri
     * @return mixed
     */
    function getUriUid(\Db\PdoMySql $transaction, $uri)
    {
        $sql = "
            SELECT uid
            FROM uri
            WHERE uri = ?
        ";

        $bind   = [trim($uri, '/')];
        $result = $transaction->prepare($sql);

        $result->execute($bind);

        return $result->fetchColumn();
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param $uri
     * @return mixed
     */
    function getMasterPageId(\Db\PdoMySql $transaction)
    {
        $sql = "
            SELECT page_master_uid
            FROM page
            WHERE uri_uid = ?
        ";

        $bind   = [$this->uri_uid];
        $result = $transaction->prepare($sql);

        $result->execute($bind);

        return $result->fetchColumn();
    }


    /**
     * @param \Db\PdoMySql $transaction
     * @param $old_uri_uid
     * @param $new_uri_as_array
     * @return bool
     */
    private function updateUri(\Db\PdoMySql $transaction, $old_uri_uid, array $new_uri_as_array)
    {
        $old_uri            = \Pages\Get::uri($old_uri_uid);                        // foo/bar/baz/lorem/child/pages/several/levels/down/with/commonly/changed/upper-uri
        $old_uri_as_array   = \Pages\Utilities::uriAsArray($old_uri);               // [0] => foo, [1] => bar, [2] => baz, [3] => lorem ......... [12] => upper-uri
        $updated_uri_array  = array_replace($old_uri_as_array, $new_uri_as_array);  // [0] => foo, [1] => bar, [2] => baz, [3] => lorem-ipsum ... [12] => upper-uri
        $updated_uri        = trim(\Pages\Utilities::arrayAsUri($updated_uri_array), '/');     // foo/bar/baz/lorem-ipsum

        $sql = "
            UPDATE uri
            SET uri = ?
            WHERE uid = ?;
        ";

        $bind = [
            trim($updated_uri, '/'),
            $old_uri_uid,
        ];

        $transaction
            ->prepare($sql)
            ->execute($bind);

        return true;
    }


    /**
     * @param array $post_data
     * @return bool
     */
    function validatePostData()
    {
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
            FROM page_iteration 
            WHERE uid = ?
            AND archived = '0';
        ";

        $db     = new \Db\Query($sql, [$this->new_uid]);
        $count  = $db->fetch();

        return ($count > 0);
    }


    /**
     * @param string $uri
     * @return bool
     */
    private function uriExists($uri)
    {
        $sql = "
            SELECT COUNT(*)
            FROM uri 
            WHERE uri = ?
            AND archived = '0';
        ";

        $db     = new \Db\Query($sql, [$uri]);
        $count  = $db->fetch();

        return ($count > 0);
    }


    /**
     * @throws \Exception
     */
    private function checkAndThrowErrorException()
    {
        if (!empty($this->errors)) {
            $errors = implode('; ', $this->errors);

            throw new \ErrorException($errors);
        }
    }
}