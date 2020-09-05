<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 7/16/2016
 *
 * CKEditor.php
 *
 * Generate CK Editor output on page with textarea
 *
 */

namespace Wysiwyg;

class CkEditor
{
    const DIST = 4;
    const SKIN = 0;

    public $cdn;

    // 2016-07-16
    private $skin = [
        'moono',
        'kama',
    ];

    private $version = '4.14.1';

    private $dist = [
        'basic',
        'standard',
        'standard-all',
        'full',
        'full-all',
    ];

    private $plugin_list = [
        'stylesheetparser',
        'sourcedialog',
        'codesnippet',
    ];


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
//        $this->cdn = '
//            <script src="https://cdn.ckeditor.com/' . $this->version . '/' . $this->dist[self::DIST] . '/ckeditor.js" charset="utf-8"></script>
//        ';

        $this->cdn = '
            <script src="' . \Settings::value('full_web_url') . '/assets/js/ckeditor/ckeditor.js" charset="utf-8"></script>
        ';

        return true;
    }


    /**
     * @param $textarea_id
     * @param string $custom_config
     * @return string
     */
    public function textarea($textarea_id, $custom_config = '/assets/js/ckeditor/config.js')
    {
        $item = '
            <script>
                var ckeditor = CKEDITOR.replace(\'' . $textarea_id . '\', {
                    filebrowserUploadUrl: \'/controllers/services/ckeditor_upload_file.php\',
                    filebrowserImageUploadUrl: \'/controllers/services/ckeditor_upload_image.php\',
                    filebrowserUploadMethod: \'form\'
                });
            </script>
        ';

        return $item;
    }


    /**
     * @param $textarea_id
     * @param string $custom_config
     * @return string
     */
    public function inline($textarea_id, $custom_config = '/assets/js/ckeditor/config.js')
    {
        $item = '
            <script>
                var ckeditor = CKEDITOR.inline(\'' . $textarea_id . '\', {
                    filebrowserUploadUrl: \'/controllers/services/ckeditor_upload_file.php\',
                    filebrowserImageUploadUrl: \'/controllers/services/ckeditor_upload_image.php\',
                    filebrowserUploadMethod: \'form\'
                });
            </script>
        ';

        return $item;
    }
}