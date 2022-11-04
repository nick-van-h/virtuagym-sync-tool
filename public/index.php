<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';
//Enable error logging for dev environment
set_error_reporting();
//Check if the user is logged in
$auth = new Vst\Model\Authenticator;
if ($auth->userIsLoggedIn()) {
    redirectToUrl(public_base_url() . '/app.php');
}
//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();
//Site content
get_vw_index();
//Foot
get_vw_foot();