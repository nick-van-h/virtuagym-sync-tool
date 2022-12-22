<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $agenda = (isset($_POST['agenda']) ? $_POST['agenda'] : '');
    $action = (isset($_POST['action']) ? $_POST['action'] : '');

    $settings = new Vst\Model\Database\Settings;
    $sync = new Vst\Controller\Sync;

    //Init return values
    $payload = [];
    $resp = array(
        'success' => false,
        'payload' => 'default'
    );

    try {
        if($sync->testCalendarConnection()) {
            switch ($action) {
                case 'save':
                    $settings->setTargetAgenda($agenda);
                    $payload['statusmessage'] = 'Agenda saved succesfully';

                    //Reset status
                    $settings->setLastCalendarConnectionStatusOk();

                    //Test for VG connection
                    if ($sync->testVgConnection()) {
                        $settings->masterEnableAutoSync();
                        $payload['master_autosync_enabled'] = true;
                    } else {
                        $payload['master_autosync_enabled'] = false;
                    }
                    break;
                default:
                    $payload['statusmessage'] = 'Undefined action passed';
        }
        } else {
            $payload['statusmessage'] = 'Unable to connect to calendar';
        }
        $resp['success'] = true;
    } catch (Exception $e) {
        $payload['statusmessage'] = 'Error: ' . $e->getMessage();
    }

    //Incorporate the payload and return the result
    $resp['payload'] = $payload;
    echo (json_encode($resp));
} else {
    //Throw exception and stop execution
    throw new Exception('Script called from non-AXAX source');
    exit();
}
