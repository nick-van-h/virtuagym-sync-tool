<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Get the passed variables
    $var = (isset($_POST['var']) ? $_POST['var'] : '');

    //Init return values
    $payload = [];
    $payload['statusmessage'] = 'default';
    $resp = array(
        'success' => false,
        'payload' => 'default'
    );

    //process the request
    try
    {
        //Logic goes here//
        $resp['success'] = true;
    } catch (Exception $e) {
        //Handle exceptions//
    }
    //Include the payload and return the result
    $resp['payload'] = $payload;
    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
