<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

/**
 * This page validates the initial response of a Google login & acquires access/refresh tokens
 */

//Get parameters from callback
$code = (isset($_GET['code']) ? $_GET['code'] : false);
$state = (isset($_GET['state']) ? $_GET['state'] : false);

//Init classes
$session = new Vst\Controller\Session;
$user = new Vst\Controller\User;

/**
 * Validate the response
 * Check if the code is set and the state matches the guid in the session
 * If not set an error statuts and redirect the user
 * Redirect back to the stored enpoint, or to the public base url if none is set
 */
if(!($code && $state == $session->getStatus('state_guid'))) {
    $session->setStatus('Google-login','Error','Invalid callback');
    redirectToUrl($session->getRedirectUrl() ?? public_base_url());
}

/**
 * Initiate token exchange call
 * Create the exchange URL with the parameters from the OAuth json
 * Use Curl to make the call and retrieve the values
 */
$oauth = getGoogleOauth();
$endpoint = 'https://oauth2.googleapis.com/token'; //TODO: change to $oauth->??endpoint??
$params = array(
    'client_id' => $oauth->web->client_id,
    'client_secret' => $oauth->web->client_secret,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => public_base_url() . '/interfaces/web/googleLoginCallback.php'
);
$url = $endpoint . '?' . http_build_query($params);

//Make the call and get the response in a readable format
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch));
curl_close($ch);

/**
 * Validate the response
 * If an 'error' parameter was passed then the exchange failed
 * Else the refresh/access token are to be processed
 */
if(!empty($response->error)) {
    echo('error in retrieving access/refresh token');
    //Handle error
    echo_pre($response);
    $session->setStatus('Google-login','Error','Invalid access/refresh token call: ' . $response->error_message); //TODO: Validate child = error_message?

} else {
    /**
     * Valid access & refresh token received
     * Store access token in session
     * Store refresh token encrypted in settings
     * Update the status message
     */
    
    //Todo: Validate && implement functions
    $cred = Array(
        'refresh_token' => $response->refresh_token,
        'access_token' => $response->refresh_token
        //Account name?
    )
    $user->setCalendarCredentials($cred);
    $session->setAccessToken($response->access_token);
    $session->setStatus('Google-login','Success','Login credentials validated');
    
    //TMP
    echo('curl complete, response:');
    echo_pre($response);
    // $_SESSION['access_token'] = $response->access_token;
    // $_SESSION['refresh_token'] = $response->refresh_token;
    echo('Access token: ' . $_SESSION['access_token']);
}

//Redirect the user back to the original page where they came from
redirectToUrl($session->getRedirectUrl() ?? public_base_url());