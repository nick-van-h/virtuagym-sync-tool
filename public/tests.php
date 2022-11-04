<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Check if the user is logged in
$auth = new Vst\Model\Authenticator;
if (!$auth->userIsLoggedIn() && !$auth->userIsAdmin()) {
    redirectToUrl(public_base_url());
}

//Get the testcase from URL
$url = full_url();
$url_components = parse_url($url);
$test = false;
if (isset($url_components['query']) && !empty($url_components['query'])) {
    parse_str($url_components['query'], $params);
    if (isset($params['test']) && !empty($params['test'])) {
        $test = $params['test'];
    }
}

//Get list of test files
$path    = BASE_PATH . '/tests/';
$files = array_values(array_diff(scandir($path), array('..', '.', 'index.php')));

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Build the content
get_vw_nav();
echo ('<main class="test">');
echo ('<div class="img-container img-page-title">');
echo ('<img src="' . public_base_url() . '/resources/img/title_tests.png">');
echo ('</div>');

/**
 * If a test case was passed and the test case exists then load the test case content
 * Else load the main index content
 */
if ($test && in_array($test,$files)) {
    require_once $path . $test . '/index.php';
} else {
    //Main content
    echo('<h1>Test cases</h1>');
    echo ('<ul class="list-of-tests">');
    foreach ($files as $file) {
        if (is_dir($path . $file) && !preg_match("/(0_)/",$path . $file)) {
            echo ('<li><a href="?test=' . $file . '">' . $file . '</a></li>');
        }
    }
    echo ('</ul>');


    echo('<h2>Other</h2>');
    echo ('<ul class="list-of-tests">');
    foreach ($files as $file) {
        if (is_dir($path . $file) && preg_match("/(0_)/",$path . $file)) {
            echo ('<li><a href="?test=' . $file . '">' . $file . '</a></li>');
        }
    }
    echo ('</ul>');
}

//Foot
echo("</main>");
get_vw_foot();
