<?php
//Default include autoload
//require_once 'vendor/vst/autoload.php';
require_once 'vendor/vst/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Check if the user is logged in
$auth = new Vst\Model\Authenticator;
if (!$auth->userIsLoggedIn() && !$auth->userIsAdmin()) {
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_nav();
get_vw_changelog();
echo ('<div class="app_outer">');
get_vw_app_header();
get_vw_admin();
echo ('</div>');

//Foot
get_vw_foot();
