<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

CONST THRESHOLD = 5;

$start = new DateTime();
$ref = $start;
$ref->modify('-1 hour');

$session = new Vst\Model\Session;
$settings = new Vst\Model\Database\Settings;
$log = new Vst\Model\Database\Log;

$users = $settings->getAllUserIds_orderedByLastSync();

$maxCallsPerUser = $log->getMaxApiCallsForOneUser($ref, $start);

foreach ($users as $usr) {
    //Check if there is still room for more API calls (break if max > 500)
    $curApiCalls = $log->getNrApiCalls($start);
    if (($curApiCalls + $maxCallsPerUser) > 500) break;

    //Set the next user to  be synced
    $session->setUserId($usr);

    if($settings->getCalendarConnectionErrorCount() < THRESHOLD && $settings->getVgConnectionErrorCount() < THRESHOLD) {
        //Sync the user
        try {
            $sync = new Vst\Controller\Sync;
            $sync->scheduledSyncAll();
        } catch (Exception $e) {
            //TODO: Make nice email handler #76
            if($settings->getCalendarConnectionErrorCount() == THRESHOLD) {
                echo("Unable to connect to calendar for " . THRESHOLD . " times in a row, disabling autosync.");
            }
            if($settings->getVgConnectionErrorCount() == THRESHOLD) {
                echo("Unable to connect to VirtuaGym for " . THRESHOLD . " times in a row, disabling autosync.");

            }
            echo ("Error during sync: " . $e->getMessage() . ' in ' . $e->getTraceAsString());
        }
    }
}
