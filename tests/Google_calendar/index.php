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
echo('<h1>Test</h1>');

//Instantiate factory
$cal = Vst\Controller\CalendarFactory::getProvider('Google','dummy');
echo('Calendar class created via static provider');br();

//Set up the connection
echo('<button id="test-google-connect">Authorize with Google</button>');br();

echo('Access token: ' . $_SESSION['access_token']);br();
echo('refresh token: ' . $_SESSION['refresh_token']);br();

//Test the connection
if(!empty($_SESSION['refresh_token'])) {
    echo('Connection status: ' . ($cal->testConnection() ? 'OK' : 'NOK'));

}

//Get calendars if connection is OK
if ($cal->testConnection()) {
    echo_pre($cal->getCalendars(),'calendars');
}


/**
 * Summary
 */
$testEnd = new \DateTime();
$diff = date_diff($testEnd, $testStart);
echo('<h1>Summary</h1>');
echo('Total runtime: ' . $diff->format('%H:%I:%S') . ' (h:m:s)');
br();br();
echo('--- end ---');