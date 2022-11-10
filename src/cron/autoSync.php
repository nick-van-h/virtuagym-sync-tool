<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

$session = new Vst\Controller\Session;

$session->setUserId('1');

$sync = new Vst\Model\Sync;
try{
    $sync->scheduledSyncAll();
} catch (Exception $e) {
}