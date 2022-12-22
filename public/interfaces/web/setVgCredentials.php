<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//Check for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    //Process the request
    $username = (isset($_POST['username']) ? $_POST['username'] : '');
    $password = (isset($_POST['password']) ? $_POST['password'] : '');
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
        $name = $sync->getVgName($username, $password);
        if ($name) {
            switch ($action) {
                case "test":
                        $payload['statusmessage'] = 'Connection OK! Account detected for ' . $name;
                default:
                    if ($settings->updateVirtuagymCredentials($username, $password)) {
                        // $this->session->setStatus('virtuagym', 'Success', 'Credentials updated succesfully');
                        $payload['statusmessage'] = 'Credentials updated succesfully';
                        $this->log->addEvent('Settings', 'Updated VirtuaGym credentials');
                    } else {
                        // $this->session->setStatus('virtuagym', 'Warning', 'Error while updating credentials: ' . $settings->getErrors());
                        $errors = $settings->getErrors();
                        if(isset($errors) && !empty($errors)) {
                            $payload['statusmessage'] = 'Error while updating credentials: ';
                            foreach($errors as $err) {
                                $payload['statusmessage'] .= $err . ';';
                            }
                        } else {
                            $payload['statusmessage'] = 'Unable to save credentials';
                            throw new Exception("Error occurred while saving database credentials but no error message was returned");
                        }
                    }
                    $settings->setLastVgConnectionStatusOk();
                    if($sync->testCalendarConnection()) {
                        $settings->masterEnableAutoSync();
                        $payload['master_autosync_enabled'] = true;
                    } else {
                        $payload['master_autosync_enabled'] = false;
                    }
                    break;
            }
        } else {
            $payload['statusmessage'] = 'Invalid credentials';
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
