/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    /* Encoding */
    config.entities                     = false;
    config.entities_latin               = false;
    config.entities_greek               = false;
    config.basicEntities                = false;
    config.entities_processNumerical    = false;

    /* HTML integrity */
    config.allowedContent = true;
    config.extraAllowedContent = '*(*)';
    config.protectedSource.push(/\{foreach.+\}[\s\S]+\{\/foreach\}/g);
};