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
get_vw_head_title('VST Test - Sync class');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_nav();
echo('<main class="test box-s">');

/**
 * Start main content
 */
echo('<h1>Sync all</h1>');
echo('Perform a sync all, then get stored activities');
$sync = new Sync;
$sync->syncAll();
$activities = $sync->getAllStoredActivities();

foreach ($activities as $act) {
    echo '<div class="training-entry' . ($act['cancelled'] ? ' cancelled' : '') . (!$act['joined'] ? ' not-joined' : '') . '">';
        echo '<div class="dow">' . date("D", $act['event_start']) . '</div>';
        echo '<div class="dt">';
            echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
            echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
        echo '</div>';
        echo '<div class="title">' . $act['name'] . '</div>';
    echo '</div>';
}
echo('<h2>Raw data</h2>');
echo_pre($activities,'events');


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
