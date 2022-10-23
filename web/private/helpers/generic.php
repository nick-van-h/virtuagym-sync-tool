<?php

/**
 * Redirects the user to the specified url
 * 
 * @param string $url
 */
function redirectToUrl($url)
{
    header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
}

/**
 * Returns the public web URL of the base project
 * 
 * @return string
 */
function public_base_url()
{
    return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'] == 'localhost' ? 'localhost/vst/web' : $_SERVER['SERVER_NAME']
    );
}

/**
 * Echos a newline
 */
function br() {
    echo('<br>');
}

function echo_pre($arr) {
    echo('<pre>');
    print_r($arr);
    echo('</pre>');
}

/**
 * Set the error reporting based on the server
 * If the server is production then no error reporting, else enable error reporting
 */
function set_error_reporting() {
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
}