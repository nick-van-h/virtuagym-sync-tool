<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//init variables & environment
$testStart = new \DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Start main content
 */
echo ('<h1>Test</h1>');
//Init variables
$log = new Vst\Model\Database\Log;
$db = new Vst\Model\Database\Database;


//Get start timestamp of first sync start
$sql = "SELECT `timestamp`, `id`
        FROM `log`
        WHERE (`activity` = 'Scheduled sync' OR `activity` = 'Manual sync') AND `message` = 'Sync start'";
$db->query($sql);
$res = $db->getOne();
$start = new DateTime($res['timestamp']);

//Get end timestamp of linked sync end
$sql = "SELECT `timestamp`
        FROM `log`
        WHERE `ref_log_id` = (?) AND `message` = 'Sync end'";
$db->bufferParams($res['id']);
$db->query($sql);
$end = new DateTime($db->getOne('timestamp'));

//Query sync runs from retrieved start/end
echo ('Get first sync run summary');
echo_pre($log->getSyncRuns($start, $end));

//Query api calls from retrieved start/end
echo ('Get API calls executed during said sync run');
echo_pre($log->getApiCalls($start, $end));

/**
 * Summary
 */
$testEnd = new \DateTime();
$diff = date_diff($testEnd, $testStart);
echo ('<h1>Summary</h1>');
echo ('Total runtime: ' . $diff->format('%H:%I:%S') . ' (h:m:s)');
br();
br();
echo ('--- end ---');
