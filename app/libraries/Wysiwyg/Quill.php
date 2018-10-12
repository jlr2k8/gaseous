<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/1/18
 *
 * Quill.php
 *
 * Quill JS editor
 *
 **/

namespace Wysiwyg;

class Quill
{
    public $cdn;

    public function __construct()
    {
        $this->init();
    }


    function init()
    {
        $this->cdn = '
            <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
            <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        ';

        return true;
    }


    function editor($textarea_id)
    {
        $editor = '
            <script>
              var quill = new Quill("#' . $textarea_id . '", {
                theme: "snow"
              });
            </script>
        ';

        return $editor;
    }
}