<?php
/*
Plugin Name: Preview URL with QR Code
Plugin URI: https://github.com/dennydai
Description: Preview URLs before you're redirected there
Version: 1.0
Author: Denny Dai
Author URI: https://dennydai.github.io
*/

// EDIT THIS

// Character to add to a short URL to trigger the preview interruption
define( 'DD_PREVIEW_CHAR', '~' );

// DO NO EDIT FURTHER

// Handle failed loader request and check if there's a ~
yourls_add_action( 'loader_failed', 'dd_preview_loader_failed' );
function dd_preview_loader_failed( $args ) {
        $request = $args[0];
        $pattern = yourls_make_regexp_pattern( yourls_get_shorturl_charset() );
        if( preg_match( "@^([$pattern]+)".DD_PREVIEW_CHAR."$@", $request, $matches ) ) {
                $keyword   = isset( $matches[1] ) ? $matches[1] : '';
                $keyword = yourls_sanitize_keyword( $keyword );
                dd_preview_show( $keyword );
                die();
        }
}

// Show the preview screen for a short URL
function dd_preview_show( $keyword ) {
        require_once( YOURLS_INC.'/functions-html.php' );

        yourls_html_head( 'preview', 'Short URL preview' );
        yourls_html_logo();

        $title = yourls_get_keyword_title( $keyword );
        $url   = yourls_get_keyword_longurl( $keyword );
        $base  = YOURLS_SITE;
        $char  = DD_PREVIEW_CHAR;
        $qrcode = 'data:image/png;base64,'.base64_encode(file_get_contents('http://chart.apis.google.com/chart?chs=200x200&cht=qr&chld=M&chl='.YOURLS_SITE.'/'.$keyword));

        echo <<<HTML
        <h2>Link Preview</h2>
        <p>You requested the short URL <strong><a href="$base/$keyword">$base/$keyword</a></strong></p>
        <p>This short URL points to:</p>
        <ul>
        <li>Long URL: <strong><a href="$base/$keyword">$url</a></strong></li>
        <li>Page title: <strong>$title</strong></li>
        <li>QR Code: <br><img src="$qrcode"></li>
        </ul>
        <p>If you still want to visit this link, please <strong><a href="$base/$keyword">click here</a></strong>.</p>

        <p>Thank you for using our shortening service.</p>
HTML;

        yourls_html_footer();
}
