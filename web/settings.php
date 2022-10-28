<?php
//Default include autoload
require_once __DIR__ . '/private/config/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Check if the user is logged in
$auth = new Authenticator;
if (!$auth->userIsLoggedIn()) {
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_app_nav();
echo('<div class="app_outer">');
get_vw_app_header();
get_vw_settings();
echo('</div>');

//Foot
get_vw_foot();
