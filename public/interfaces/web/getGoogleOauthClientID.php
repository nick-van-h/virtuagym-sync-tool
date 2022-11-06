<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $var = (isset($_POST['var']) ? $_POST['var'] : '');

    //Init return values
    $payload = [];
    $resp = array(
        'success' => false,
        'payload' => 'default'
    );

    //Get the client id from the json
    $oauth = getGoogleOauth();
    $payload['client_id'] = $oauth->web->client_id;

    //Get the redirect uri
    $payload['redirect_uri'] = public_base_url() . '/interfaces/web/googleLoginCallback.php';

    //Incorporate the payload and return the result
    $resp['payload'] = $payload;
    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
