<?php
//Default include autoload
require_once __DIR__ . '/../private/config/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $username = (isset($_POST['username']) ? $_POST['username'] : '');
    $password = (isset($_POST['password']) ? $_POST['password'] : '');
    $action = (isset($_POST['action']) ? $_POST['action'] : '');

    $settings = new Settings;
    $vg = new VirtuaGym;

    $payload = [];

    
    if($action=="test") {
        if($vg->testConnection($username, $password)) {
            $data = $vg->getData();
            $payload['statusmessage'] = 'Connection OK! Account detected for ' . $data->name;
        } else {
            $payload['statusmessage'] = 'Connection error: ' . $vg->getStatusMessage();
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
