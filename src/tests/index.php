<?php
//Default include autoload
require_once 'vendor/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Check if the user is logged in
$auth = new Authenticator;
if (!$auth->userIsLoggedIn() && !$auth->userIsAdmin()) {
    redirectToUrl(public_base_url());
}

//Get list of test files
$path    = __DIR__;
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

//Main content
echo('<h1>Test cases</h1>');
echo ('<ul class="list-of-tests">');
foreach ($files as $file) {
    if (is_dir($file) && !preg_match("/(0_)/",$file)) {
        echo ('<li><a href="' . $file . '">' . $file . '</a></li>');
    }
}
echo ('</ul>');


echo('<h2>Other</h2>');
echo ('<ul class="list-of-tests">');
foreach ($files as $file) {
    if (is_dir($file) && preg_match("/(0_)/",$file)) {
        echo ('<li><a href="' . $file . '">' . $file . '</a></li>');
    }
}
echo ('</ul>');

//End the document
echo ('</div>');

//Foot
echo("</main>");
get_vw_foot();
