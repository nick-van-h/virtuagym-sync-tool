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
        $_SERVER['SERVER_NAME'] == 'localhost' ? 'localhost/vst/public' : $_SERVER['SERVER_NAME']
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
function getFile($fileLocation)
{

    $crl = curl_init($fileLocation);
    curl_setopt($crl, CURLOPT_NOBODY, true);
    curl_exec($crl);

    $ret = curl_getinfo($crl, CURLINFO_HTTP_CODE);
    curl_close($crl);

    if ($ret = 200) {
        return $fileLocation;
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
    $file = getFile(CONFIG_FILE);
    if($file) {
        return parse_ini_file($file);
    } else {
        throw new Exception('Unable to read config file', 100);
        return false;
    }
}
function getGoogleOauth()
{
    $file = getFile(OAUTH_FILE);
    if($file) {
        return json_decode(file_get_contents($file));
    } else {
        throw new Exception('Unable to read config file', 100);
        return false;
    }
}

function getChangelog()
{
    $file = getFile(CHANGELOG_FILE);
    if($file) {
        return json_decode(file_get_contents($file));
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

function echo_pre($arr, $name = null) 
{
    echo('<div class="debug">');
    if (!empty($name)) {
        echo('<b><i>$</i>' . $name . ':</b>');
    } else {
        echo('<p />');
    };
    echo('<pre>');
    print_r($arr);
    echo('</pre></div>');
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

/**
 * Generates a semi-cryptographically-secure random ID according GUIDv4 format
 * 
 * @param  int $length
 * @return string
 */
function guidv4()
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}