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

//Set redirect URL
$session = new Vst\Controller\Session;
$session->setRedirectUrl(public_base_url() . '/tests.php?test=Google_calendar');

//init classes
$user = new Vst\Controller\User;
$sync = new Vst\Model\Sync;

//Set up the connection
echo('<button id="test-google-connect">Authorize with Google</button>');br();

//Instantiate factory
$provider = $user->getCalendarProvider();
if($provider == PROVIDER_GOOGLE) {
    $credentials = $user->getCalendarCredentials();
    $cal = Vst\Controller\CalendarFactory::getProvider($provider,$credentials);
    echo('Calendar class created via static provider');br();
} else {
    echo('Calendar credentials not set in database');br();
}


//Test the connection
if ($cal->testConnection()) {
    //Get agenda & selected target agenda
    $agds = [];
    foreach($cal->getAgendas() as $agd) {
        $agds[] = $agd['name'];
    }
    echo_pre($agds,'agendas');

    

    echo('<h2>List appointments</h2>');
    //Build the table with appointments in the selected time range
    echo('<table><tr><th>Date</th><th>Start time</th><th>End time</th><th>Summary</th></tr>');
    foreach($cal->getEvents() as $event) {
        $dtStart = new DateTime($event['start']);
        $dtEnd = new DateTime($event['end']);
        echo('<tr><td>' . $dtStart->format('d-m-Y') . '</td><td>');
        echo(($event['all_day'] ? '-' : $dtStart->format('H:i')) . '</td><td>');
        echo(($event['all_day'] ? '-' : $dtEnd->format('H:i')) . '</td><td>');
        echo($event['summary'] . '</td></tr>');
    }
    echo('</table>');

    echo('<h2>Push stored activities to calendar</h2>');
    $sync->storedActToCal();

} else {
    echo('Unable to connect to calendar provider');
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