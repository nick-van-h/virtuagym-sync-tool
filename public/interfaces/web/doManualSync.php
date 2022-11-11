<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    //$var = (isset($_POST['var']) ? $_POST['var'] : '');

    //Init return values
    $payload = [];
    $resp = array(
        'success' => false,
        'payload' => 'default'
    );

    //Logic goes here//
    $sync = new Vst\Model\Sync;
    $sync->manualSyncAll();

    //Incorporate the payload and return the result
    if(true) {
        $resp['success'] = true;
    }
    $resp['payload'] = $payload;
    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
