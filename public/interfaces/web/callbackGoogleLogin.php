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
$settings = new Vst\Controller\Settings;
$crypt = new Vst\Model\Crypt;

//Init a client so that we can exchange tokens and retrieve additional info
$oauth = OAUTH_FILE;
$client = new \Google\Client;
$client->setAuthConfig($oauth);
$client->setRedirectUri(public_base_url() . '/interfaces/web/callbackGoogleLogin.php');

/**
 * Validate the response
 * Check if the code is set and the state matches the guid in the session
 * If not set an error statuts and redirect the user
 * Redirect back to the stored enpoint, or to the public base url if none is set
 */
if($code && $state == $session->getStatus('state_guid')) {
    /**
     * Exchange the code for access token & ID token
     */
    $response = $client->fetchAccessTokenWithAuthCode($code);

    /**
     * Validate the response
     * If an 'error' parameter was passed then the exchange failed
     * Else the refresh/access token are to be processed
     */
    if(!empty($response['error'])) {
        $session->setStatus('Google-login','Error','Invalid access/refresh token call: ' . $response['error_description']);
    } else {
        /**
         * Valid access & refresh token received
         */
        $client->setAccessToken($response);
        $token_data = $client->verifyIdToken();

        /**
         * Store access & refresh token encrypted in settings
         * Update the status message
         */
        $cred = Array(
            'refresh_token' => $crypt->getEncryptedMessage($response['refresh_token']),
            'access_token' => $crypt->getEncryptedMessage($response['access_token']),
            'calendar_account' => $token_data['email']
        );
        $settings->setCalendarCredentials($cred);
        $settings->setCalendarProvider(PROVIDER_GOOGLE);
        $settings->setTargetAgenda('');
        
        $session->setStatus('Google-login','Success','Login credentials validated');
    }

} else {
    $session->setStatus('Google-login','Error','Invalid callback');
}

//Redirect the user back to the original page where they came from
redirectToUrl($session->getRedirectUrl() ?? public_base_url());