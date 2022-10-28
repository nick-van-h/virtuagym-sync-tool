<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';
$start = new DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Check if the user is logged in
$auth = new Authenticator;
if (!$auth->userIsLoggedIn() && !$auth->userIsAdmin()) {
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VST Test - xxx');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_test_nav();
echo('<main class="test box-s">');

/**
 * Start main content
 */
echo('<h1>Test</h1>');
$vg = new VirtuaGym;
if ($vg->testConnection()) {
    $vg->callActivities();
    echo($vg->getResultCount() . ' activities found');br();
    $vg->callClubIds();
    $vg->callActivityDefinitions();
    $vg->callEventDefinitions();
    $activities = $vg->getEnrichedActivities();
    foreach ($activities as $act) {
        echo '<div class="training-entry' . ($act['cancelled'] ? ' cancelled' : '') . (!$act['joined'] ? ' not-joined' : '') . '">';
            echo '<div class="dt">';
                echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
                echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
            echo '</div>';
            echo '<div class="title">' . $act['name'] . '</div>';
        echo '</div>';
    }
} else {
    echo 'Invalid credentials';
}

/**
 * Summary
 */
$end = new DateTime();
$diff = date_diff($end, $start);
echo('<h1>Summary</h1>');
echo('Total runtime: ' . $diff->format('%H:%I:%S') . ' (h:m:s)');
br();br();
echo('--- end ---');

//Foot
echo("</main>");
get_vw_foot();