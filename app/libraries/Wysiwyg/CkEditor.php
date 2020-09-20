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

use Settings;

class CkEditor
{
    const DIST = 4;
    const SKIN = 0;

    public $cdn;

    // 2016-07-16
    private $skin = [
        'moono-lisa',
        'kama',
    ];

    private $version = '4.15.0';

    private $dist = [
        'basic',
        'standard',
        'standard-all',
        'full',
        'full-all',
    ];

    private $plugin_list = [
        'dialogui',
        'dialog',
        'about',
        'a11yhelp',
        'dialogadvtab',
        'basicstyles',
        'bidi',
        'blockquote',
        'notification',
        'button',
        'toolbar',
        'clipboard',
        'panelbutton',
        'panel',
        'floatpanel',
        'colorbutton',
        'colordialog',
        'templates',
        'menu',
        'contextmenu',
        'copyformatting',
        'div',
        'resize',
        'elementspath',
        'enterkey',
        'entities',
        'popup',
        'filetools',
        'filebrowser',
        'find',
        'fakeobjects',
        'flash',
        'floatingspace',
        'listblock',
        'richcombo',
        'font',
        'forms',
        'format',
        'horizontalrule',
        'htmlwriter',
        'iframe',
        'wysiwygarea',
        'image',
        'indent',
        'indentblock',
        'indentlist',
        'smiley',
        'justify',
        'menubutton',
        'language',
        'link',
        'list',
        'liststyle',
        'magicline',
        'maximize',
        'newpage',
        'pagebreak',
        'pastetext',
        'pastetools',
        'pastefromgdocs',
        'pastefromword',
        'preview',
        'print',
        'removeformat',
        'save',
        'selectall',
        'showblocks',
        'showborders',
        'sourcearea',
        'specialchar',
        'scayt',
        'stylescombo',
        'tab',
        'table',
        'tabletools',
        'tableselection',
        'undo',
        'lineutils',
        'widgetselection',
        'widget',
        'notificationaggregator',
        'uploadwidget',
        'uploadimage',
        'wsc',
        'showprotected',
        'sourcedialog',
        'codesnippet',
        'stylesheetparser',
    ];

    private $external_plugins = [
        'showprotected',
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
        $this->cdn = '
            <script src="https://cdn.ckeditor.com/' . $this->version . '/' . $this->dist[self::DIST] . '/ckeditor.js" charset="utf-8"></script>
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
        $item = $this->addExternalPlugins();
        $item .= '
            <script>
                CKEDITOR.replace("' . $textarea_id . '", {
                    filebrowserUploadUrl: "/controllers/services/ckeditor_upload_file.php",
                    filebrowserImageUploadUrl: "/controllers/services/ckeditor_upload_image.php",
                    filebrowserUploadMethod: "form",
                    customConfig: "' . $custom_config . '",
                    extraPlugins: "' . implode(',', $this->plugin_list) . '",
                    skin: "' . $this->skin[self::SKIN] . '",
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
        $item = $this->addExternalPlugins();
        $item .= '
            <script>
                CKEDITOR.inline("' . $textarea_id . '", {
                    filebrowserUploadUrl: "/controllers/services/ckeditor_upload_file.php",
                    filebrowserImageUploadUrl: "/controllers/services/ckeditor_upload_image.php",
                    filebrowserUploadMethod: "form",
                    customConfig: "' . $custom_config . '",
                    extraPlugins: "' . implode(',', $this->plugin_list) . '",
                    skin: "' . $this->skin[self::SKIN] . '",
                });
            </script>
        ';

        return $item;
    }


    /**
     * @return string|null
     */
    private function addExternalPlugins()
    {
        $item = null;

        if (!empty($this->external_plugins)) {
            $item .= '<script>';

            foreach ($this->external_plugins as $external_plugin) {
                $item .= '
                    CKEDITOR.plugins.addExternal("' . $external_plugin . '", "' . Settings::value('full_web_url') . '/assets/js/ckeditor/plugins/' . $external_plugin . '/plugin.js", "");
                ';
            }

            $item .= '</script>';
        }

        return $item;
    }
}