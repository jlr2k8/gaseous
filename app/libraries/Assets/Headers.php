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

use Content\Http;
use Exception;

class Headers
{
    public $client_headers, $filename, $last_modified, $last_modified_gmd, $if_mod_since, $one_year, $one_year_gmd;


    /**
     * @param bool $filename
     * @throws Exception
     */
    public function __construct($filename = false)
    {
        if (!empty($filename) && !is_readable($filename)) {
            Http::error(404);
        }

        $this->client_headers = apache_request_headers();

        $this->filename = empty($this->filename) && !empty($filename)
            ? (string)$filename
            : false;

        $this->one_year     = (int)(60 * 60 * 24 * 365);
        $this->one_year_gmd = gmdate("D, d M Y H:i:s", strtotime('+1 years')) . ' GMT';
    }


    /**
     * @return bool
     */
    public function css()
    {
        $this->getLastModified();

        // always send headers
        header('Content-type: text/css');
        header('Cache-control: max-age=' . $this->one_year);
        header('Expires: ' . $this->one_year_gmd);

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
        $this->getLastModified();

        // always send headers
        header('Content-type: application/javascript');
        header('Cache-control: max-age=' . $this->one_year);
        header('Expires: ' . $this->one_year_gmd);

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
        $this->getLastModified();

        $filetype   = !empty($this->filename) && empty($filetype) ? mime_content_type($this->filename) : $filetype;

        // always send headers
        header('Content-type: ' . $filetype);
        header('Cache-control: max-age=' . $this->one_year);
        header('Expires: ' . $this->one_year_gmd);

        // exit if not modified
        if ($this->if_mod_since == $this->last_modified_gmd) {
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
    public function file()
    {
        $this->getLastModified();

        $filetype   = mime_content_type($this->filename);

        // always send headers
        header('Content-type: ' . $filetype);
        header('Cache-control: max-age=' . $this->one_year);
        header('Expires: ' . $this->one_year_gmd);

        if ($filetype == 'application/pdf') {
            header('Content-Disposition: inline; filename=' . basename($this->filename));
        } else {
            header('Content-Disposition: attachment; filename=' . basename($this->filename));
        }

        // exit if not modified
        if ($this->if_mod_since == $this->last_modified_gmd) {
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
    private function getLastModified()
    {
        $this->last_modified = empty($this->last_modified) && !empty($this->filename)
            ? filemtime($this->filename)
            : $this->last_modified;

        $this->last_modified_gmd = !empty($this->last_modified)
            ? gmdate("D, d M Y H:i:s", $this->last_modified)
            : false;

        $this->if_mod_since = !empty($this->client_headers['If-Modified-Since'])
            ? $this->client_headers['If-Modified-Since']
            : false;

        header('Last-Modified: ' . $this->last_modified_gmd);

        return true;
    }


    /**
     * @param $path
     */
    public function setContentType($path)
    {
        // TODO - make this function more inclusive for other content types
        $pathinfo = pathinfo($path);

        if (isset($pathinfo['extension']) && $pathinfo['extension'] == 'js') {
            header('Content-Type: application/javascript');
        } elseif ($pathinfo['extension'] && $pathinfo['extension'] == 'css') {
            header('Content-Type: text/css');
        }
    }
}