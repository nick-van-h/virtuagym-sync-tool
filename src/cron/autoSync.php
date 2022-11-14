<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

$start = new DateTime();
$ref = $start;
$ref->modify('-1 hour');

$session = new Vst\Controller\Session;
$user = new Vst\Controller\User;
$log = new Vst\Controller\Log;

$users = $user->getAllUserIds_orderedByLastSync();

$maxCallsPerUser = $log->getMaxApiCallsForOneUser($ref, $start);

foreach ($users as $usr) {
    //Check if there is still room for more API calls (break if max > 500)
    $curApiCalls = $log->getNrApiCalls($start);
    if (($curApiCalls + $maxCallsPerUser) > 500) break;

    //Set the next user to  be synced
    $session->setUserId($usr);

    //Sync the user
    $sync = new Vst\Model\Sync;
    try {
        $sync->scheduledSyncAll();
    } catch (Exception $e) {
    }
}
