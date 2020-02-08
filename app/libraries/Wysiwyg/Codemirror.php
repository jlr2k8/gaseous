<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/24/18
 *
 * Codemirror.php
 *
 * Load up Codemirror for user-friendly textarea editing of code.
 * Defaults to smarty mode and uses third-party CDN.
 *
 */

namespace Wysiwyg;

class Codemirror
{
    public $cdn_root    = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror';
    public $jquery_url  = '//code.jquery.com/jquery-2.2.4.min.js';
    public $mode        = 'css';
    public $mode_src    = 'mode/css/css.js';
    private $version    = '5.39.0';

    /**
     * CKEditor constructor.
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * @return bool
     */
    protected function init()
    {
        $this->cdn = '
            <script src="' . $this->jquery_url . '">&#160;</script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/codemirror.js"></script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/mode/htmlmixed/htmlmixed.js"></script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/mode/xml/xml.js"></script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/mode/javascript/javascript.js"></script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/mode/css/css.js"></script>
            <script src="' . $this->cdn_root . '/' . $this->version . '/mode/clike/clike.js"></script>
            <link rel="stylesheet" href="' . $this->cdn_root . '/' . $this->version . '/codemirror.css">
        ';

        return true;
    }


    /**
     * @param $textarea_id
     * @return string
     */
    public function textarea($textarea_id)
    {
        $item = '
            <script>
                $(document).ready(function() {
                
                    var textarea = document.getElementById("' . $textarea_id . '");
                    
                    var editor = CodeMirror.fromTextArea(textarea, {
                        lineNumbers: true,
                        mode: "' . $this->mode . '"
                    });
                    
                    editor.on("change", function() {
                       editor.save();
                    });
                });
                
            </script>
        ';

        return $item;
    }
}