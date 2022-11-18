<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $agenda = (isset($_POST['agenda']) ? $_POST['agenda'] : '');
    $action = (isset($_POST['action']) ? $_POST['action'] : '');

    $settings = new Vst\Model\Database\Settings;

    //Init return values
    $payload = [];
    $resp = array(
        'success' => true,
        'payload' => 'default'
    );

    //TODO: Remove after validate
    // $payload['get-cal'] = $agenda;
    // $payload['get-action'] = $action;
    // $payload['get'] = $_GET;
    // $payload['post'] = $_POST;
    switch ($action) {
        case 'save':
            $settings->setTargetAgenda($agenda);
            $payload['statusmessage'] = 'Agenda saved succesfully!';
            break;
        default:
            $payload['statusmessage'] = 'Undefined action passed';
    }

    //Incorporate the payload and return the result
    $resp['payload'] = $payload;
    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
