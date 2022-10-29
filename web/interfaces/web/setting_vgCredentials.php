<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $username = (isset($_POST['username']) ? $_POST['username'] : '');
    $password = (isset($_POST['password']) ? $_POST['password'] : '');
    $action = (isset($_POST['action']) ? $_POST['action'] : '');

    $settings = new Settings;
    $sync = new Sync;

    $payload = [];

    
    if($action=="test") {
        $name = $sync->getVgName($username, $password);
        if($name) {
            $payload['statusmessage'] = 'Connection OK! Account detected for ' . $name;
        } else {
            $payload['statusmessage'] = 'Connection error: ' . $sync->getLastVgMessage();
        }
    } else {
        $settings->updateVirtuagymCredentials($username, $password);
        $payload['statusmessage'] = $settings->getVirtuagymMessage();
    }

    $resp = array(
        'success' => true,
        'payload' => $payload
    );

    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
