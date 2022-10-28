<?php
//Default include autoload
require_once __DIR__ . '/private/config/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Get the token from the URL
$auth = new Authenticator;
$url = full_url();
$url_components = parse_url($url);
$token = false;
if (isset($url_components['query']) && !empty($url_components['query'])) {
    parse_str($url_components['query'], $params);
    if (isset($params['token']) && !empty($params['token'])) {
        $token = $params['token'];
    }
} else {
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Site content
if ($token && $auth->validateToken($token)) {
    get_vw_pw_reset();
} else {
    get_vw_invalid_token();
    $auth->loutoutUser();
}

//Foot
get_vw_foot();
