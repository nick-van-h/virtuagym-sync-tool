<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Get the passed variables
    $enable = (isset($_POST['enable']) ? $_POST['enable'] : '');

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
        /**
         * Enable or disable the master switch based on the passed variable
         */
        $settings = new Vst\Model\Database\Settings;
        $sync = new Vst\Controller\Sync;
        if($enable) {
            /** 
             * Test if connection can be made to both VG and the calendar
             * If so, enable the switch, if not notify user
             */
            $settings->masterEnableAutoSync();
            if(!$sync->testVgConnection()) {
                $payload['statusmessage'] = 'Auto sync not enabled; Unable to connect to VirtuaGym';
                $payload['master_autosync_enabled'] = false;
            } elseif (!$sync->testCalendarConnection()) {
                $payload['statusmessage'] = 'Auto sync not enabled; Unable to connect to calendar provider';
                $payload['master_autosync_enabled'] = false;
            } else {
                $payload['statusmessage'] = 'Auto sync enabled';
                $payload['master_autosync_enabled'] = true;
            }
        } else {
            $settings->masterDisableAutoSync();
            $payload['statusmessage'] = 'Auto sync disabled';
            $payload['master_autosync_enabled'] = false;
        }
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
