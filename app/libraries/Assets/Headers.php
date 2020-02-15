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

namespace Assets;

use Content\Pages\HTTP;

class Headers
{
    public $client_headers, $filename, $last_modified, $last_modified_gmd, $last_modified_gmd_plus_one_year,
        $if_mod_since, $one_year;

    //public static $one_year = (int)(60 * 60 * 24 * 7 * 365);


    /**
     * @param bool $filename
     * @throws \Exception
     */
    public function __construct($filename = false)
    {
        if (!empty($filename) && !is_readable($filename)) {
            HTTP::error(404);
        }

        $this->client_headers = apache_request_headers();

        $this->filename = empty($this->filename) && !empty($filename)
            ? (string)$filename
            : false;

        $this->last_modified = empty($this->last_modified) && !empty($filename)
            ? filemtime($filename)
            : false;

        $this->last_modified_gmd = empty($this->last_modified)
            ? gmdate("D, d M Y H:i:s", $this->last_modified) . ' GMT'
            : false;

        $this->last_modified_gmd_plus_one_year = empty($this->one_year)
            ? gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT'
            : false;

        $this->if_mod_since = !empty($this->client_headers['If-Modified-Since'])
            ? $this->client_headers['If-Modified-Since']
            : false;

        $this->one_year = (int)(60 * 60 * 24 * 7 * 365);
    }


    /**
     * @return bool
     */
    public function css()
    {
        // always send headers
        header('Content-type: text/css');
        header('Cache-control: max-age=' . $this->one_year);

        // CSS Preview mode - don't send 304 header
        $is_preview_mode = !empty($_SESSION['css_preview']);

        // exit if not modified
        if ($this->if_mod_since == $this->last_modified_gmd && !$is_preview_mode) {
            header('X-304: true');

            // 304 Not Modified
            header('Last-Modified: ' . $this->last_modified_gmd, true, 304);

            return true;
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
        header('Content-type: application/js');
        header('Cache-control: max-age=' . $this->one_year);

        // JS Preview mode - don't send 304 header
        $is_preview_mode = !empty($_SESSION['js_preview']);

        // exit if not modified
        if ($this->if_mod_since == $this->last_modified_gmd && !$is_preview_mode) {
            header('X-304: true');

            // 304 Not Modified
            header('Last-Modified: ' . $this->last_modified_gmd, true, 304);

            return true;
        }

        header('X-304: false');

        return true;
    }


    /**
     * @param $filetype
     * @return bool
     */
    public function images($filetype = false)
    {
        $filetype   = !empty($this->filename) && empty($filetype) ? mime_content_type($this->filename) : $filetype;

        // always send headers
        header('Last-Modified: ' . $this->last_modified_gmd);
        header('Etag: ' . hash('md5', $this->last_modified));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Content-type: ' . $filetype);
        header('X-mod-since: ' . $this->last_modified);

        // exit if not modified
        if ($this->if_mod_since == $this->last_modified_gmd) {
            header('X-304: true');

            // 304 Not Modified
            header('Last-Modified: ' . $this->last_modified_gmd, true, 304);

            return true;
        }

        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", (int)$this->last_modified) . ' GMT');
        header('X-304: false');

        return true;
    }
}