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

function full_url()
{
    return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
    );
}

/**
 * Returns the full path to the database .ini file
 * 
 * @return string
 */
function getConfigFile()
{
    $db_conf = CONFIG_FILE;

    $crl = curl_init(CONFIG_FILE);
    curl_setopt($crl, CURLOPT_NOBODY, true);
    curl_exec($crl);

    $ret = curl_getinfo($crl, CURLINFO_HTTP_CODE);
    curl_close($crl);

    if ($ret = 200) {
        return CONFIG_FILE;
    } else {
        throw new Exception('Config file not found', 100);
        return false;
    }
}

/**
 * Returns an array containing the database config parameters
 * 
 * @return array contains host, username, password, database
 */
function getConfig()
{
    $file = getConfigFile();
    if($file) {
        return parse_ini_file($file);
    } else {
        throw new Exception('Unable to read config file', 100);
        return false;
    }
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