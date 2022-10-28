<?php
//Default include autoload
require_once __DIR__ . '/private/config/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

$auth = new Authenticator;
$auth->logoutUser();

//Get the token from the URL
$url = full_url();
$url_components = parse_url($url);
$token = false;
if (isset($url_components['query']) && !empty($url_components['query'])) {
    parse_str($url_components['query'], $params);
    if (isset($params['token']) && !empty($params['token'])) {
        $token = $params['token'];
    }
} else {
    //No token passed, redirect back to base URL (nothing to see here)
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Site content
if ($token && $res = $auth->validateToken($token)) {
    get_vw_pw_reset();
} else {
    get_vw_invalid_token();
    $auth->logoutUser();
}

//Foot
get_vw_foot();
