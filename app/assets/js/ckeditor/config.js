/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 *
 * Implemented/configured by Josh Rogers
 * July 16, 2016
 */

CKEDITOR.editorConfig = function (config) {
    /* Encoding */
    config.entities                     = false;
    config.entities_latin               = false;
    config.entities_greek               = false;
    config.basicEntities                = false;
    config.entities_processNumerical    = false;

    /* HTML integrity */
    config.allowedContent = true;
    config.extraAllowedContent = '*(*)';

    /* Styles */
    //config.contentsCss = ['/assets/css/styles.css', '/assets/css/navigation/top-nav.css'];
};