<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/14/2017
 *
 * debug_footer.php
 *
 * Output server info below footer
 *
 */

if (\Settings::value('show_debug')) {

    echo '
        <style>
            #footer_debug {
                overflow:scroll;
                text-align: left;
                background-color: #C3C3C3;
                color: darkblue;
                font-family: "Courier", "monospace";
                word-wrap: break-word;
                padding: 2%;
                display: block;
                float: left;
                width: 96%;
            }
            
            #footer_debug hr {
                margin: 15px 0;
            }
            
            #footer_debug * {
                color: darkblue;
            }
            
        </style>
        
		<div id="footer_debug">
    
            <h2>Quick Server Info:</h2>
            <p>Server: ' . gethostname() . '</p>
            <p>IP: ' . $_SERVER['SERVER_ADDR'] . '</p>

            <hr>

            <h2>Page Load Time:</h2><p>' . (float)(microtime(true) - PAGE_LOAD_START) . ' seconds...</p>

            <hr>

            <h2>$_SERVER:</h2>
            <pre>' . print_r($_SERVER, true) . '</pre>
            
            <hr>
            
            <h2>$_COOKIE:</h2>
            <pre>' . print_r($_COOKIE, true) . '</pre>
            
            <hr>
            
            <h2>$_POST:</h2>
            <pre> ' . print_r($_POST, true) . '</pre>
            
            <hr>
            
            <h2>$_GET:</h2>
            <pre>' . print_r($_GET, true). '</pre>
            
            <hr>
            
            <h2>Session: ' . session_id() . '</h2>
            <pre>' . print_r($_SESSION, true) . '</pre>
            
            <hr>
            
            <h2>Defined Constants ("Core"):</h2>
            <pre>' . print_r(get_defined_constants(true)['Core'], true) . '</pre>
            
            <hr>
            
            <h2>Defined Constants ("user"):</h2>
            <pre>' . print_r(get_defined_constants(true)['user'], true) . '</pre>
        </div>
    ';
}