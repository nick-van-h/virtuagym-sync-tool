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

    echo('<h2>Appointments before adding event</h1>');

    foreach($cal->getAppointment() as $apt) {
        echo_pre(array(
            'etag' => $apt['etag'],
            'start_time' => $apt['start']['dateTime'],
            'end_time' => $apt['end']['dateTime']
        ),$apt['summary']);
    }

    echo('<h2>Appointments after adding event</h1>');
    //Insert appointment
    $cal->addAppointment();

    //Retrieve appointments
    foreach($cal->getAppointment() as $apt) {
        echo_pre(array(
            'etag' => $apt['etag'],
            'start_time' => $apt['start']['dateTime'],
            'end_time' => $apt['end']['dateTime']
        ),$apt['summary']);
    }

    echo('<h2>Appointments after removing event</h1>');
    //Remove appointment
    $cal->removeAppointment();

    //Retrieve appointments
    foreach($cal->getAppointment() as $apt) {
        echo_pre(array(
            'etag' => $apt['etag'],
            'start_time' => $apt['start']['dateTime'],
            'end_time' => $apt['end']['dateTime']
        ),$apt['summary']);
    }
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