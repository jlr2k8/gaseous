<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 5/1/2016
 *
 * Headers.php
 *
 * Various header checks from client and/or server
 *
 */

class Headers
{
    public $client_headers;
    public $filename;
    public $filetype;
    public $last_modified;


    /**
     * @param array $apache_request_headers
     * @param bool $filename
     * @param bool $filetype
     * @throws \Exception
     */
    public function __construct(array $apache_request_headers, $filename = false, $filetype = false)
    {
        if (!empty($filename) && !is_readable($filename)) {
            \Pages\HTTP::error(404);
        }

        $this->client_headers   = $apache_request_headers;
        $this->filename         = empty($this->filename) && !empty($filename) ? (string)$filename : false;
        $this->filetype         = empty($this->filetype) && !empty($filename) && empty($filetype) ? mime_content_type($filename) : $filetype; // this only works for images (css and js are text/plain)
        $this->last_modified    = empty($this->last_modified) && !empty($filename) ? filemtime($filename) : false;
    }


    /**
     * @return bool
     */
    public function css()
    {
        // always send headers
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $this->last_modified) . ' GMT');
        header('Etag: ' . hash('md5', $this->last_modified));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Content-type: text/css');
        header('Cache-control: max-age=' . (int)60 * 60 * 24 * 7 * 365);
        header('Content-Encoding: gzip');

        $if_mod_since = !empty($this->client_headers['If-Modified-Since']) ? $this->client_headers['If-Modified-Since'] : false;

        // CSS Preview mode - don't send 304 header
        $is_preview_mode = !empty($_SESSION['css_preview']);

        // exit if not modified
        if ($if_mod_since == gmdate("D, d M Y H:i:s", $this->last_modified) . ' GMT' && !$is_preview_mode) {
            header('X-304: true');

            // 304 Not Modified
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->last_modified) . ' GMT', true, 304);

            exit;
        }

        header('X-304: false');

        return true;
    }


    /**
     * @return bool
     */
    public function js()
    {
        // always send headers
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $this->last_modified) . ' GMT');
        header('Etag: ' . hash('md5', $this->last_modified));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Content-type: application/js');
        header('Cache-control: max-age=' . (int)60 * 60 * 24 * 7 * 365);
        header('Content-Encoding: gzip');

        // exit if not modified
        if (isset($this->client_headers['If-Modified-Since']) && ($this->client_headers['If-Modified-Since'] == gmdate("D, d M Y H:i:s",
                    $this->last_modified) . ' GMT')
        ) {
            header('X-304: true');

            // 304 Not Modified
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->last_modified) . ' GMT', true, 304);

            exit;
        }

        header('X-304: false');

        return true;
    }


    /**
     * @return bool
     */
    public function images()
    {
        // always send headers
        header('Cache-control: max-age=' . (int)60 * 60 * 24 * 7 * 365);
        header('Etag: ' . hash('md5', $this->last_modified));
        header('Expires: Thu, 31 Dec 2099 23:59:59 GMT');
        header('Content-type: ' . $this->filetype);
        header('X-mod-since: ' . $this->last_modified);

        // exit if not modified
        if (isset($this->client_headers['If-Modified-Since']) && ($this->client_headers['If-Modified-Since'] == gmdate("D, d M Y H:i:s",
            $this->last_modified) . ' GMT')
        ) {
            // 304 Not Modified
            header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $this->last_modified) . ' GMT', true, 304);
            header('X-304: true');

            exit;
        }

        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", (int)$this->last_modified) . ' GMT');
        header('X-304: false');

        return true;
    }
}