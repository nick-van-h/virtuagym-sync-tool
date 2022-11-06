<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';
session_start();

echo('Welcome back, world!');br();br();
echo_pre($_GET,'get');

$code = $_GET['code'];
$oauth = getGoogleOauth();
$payload['client_id'] = 

$endpoint = 'https://oauth2.googleapis.com/token';
$params = array(
    'client_id' => $oauth->web->client_id,
    'client_secret' => $oauth->web->client_secret,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => public_base_url() . '/interfaces/web/googleLoginCallback.php'
);
$url = $endpoint . '?' . http_build_query($params);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = json_decode(curl_exec($ch));
curl_close($ch);

if(!empty($response->error)) {
    echo('error in retrieving access/refresh token');
    //Handle error
    echo_pre($response);

} else {

    echo('curl complete, response:');
    echo_pre($response);
    $_SESSION['access_token'] = $response->access_token;
    $_SESSION['refresh_token'] = $response->refresh_token;
    echo('Access token: ' . $_SESSION['access_token']);
    
    redirectToUrl(public_base_url() . '/tests.php?test=Google_calendar');

}