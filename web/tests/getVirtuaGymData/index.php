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
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
echo("<style>
table {
    border-collapse: collapse;
}
table, tr, th, td {
    border: 1px solid grey;
}
td {
    padding: 3px;
}
th {
    padding: 5px 10px;
}
</style>");
get_vw_head_end();

//Site content
get_vw_test_nav();
echo('<main class="test box-s">');

const API_URL = 'https://api.virtuagym.com/api/v0';
$username = TEST_USER;
$password = TEST_PWD;

/**
 * Get all activities for the current user
 */
echo('<h1>Activities</h1>');
$url = API_URL . '/activity?api_key=' . TEST_API_KEY;

try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($ch);
    curl_close($ch);
} catch (Exception $e) {
    echo('Exit with message: ' . $e->getMessage());
}

$acts = json_decode($result);

/**
 * Loop through the activities
 * Determine for each activity if it is in the future or in the past month
 * Add the activity to the designated array
 * If earlier than one month ignore
 */
$max = 0;
$nextact = [];
$pastact = [];
$dt = new DateTime();
$now = strtotime($dt->format('Y-m-d') . ' 00:00:00');
$prev = strtotime($dt->modify('-1 month')->modify('-1 day')->format('Y-m-d') . ' 00:00:00');
echo('Look back until ' . $dt->format('Y-m-d') . ' = timestamp ' . $prev);br();

//echo_pre($acts->result);
foreach($acts->result as $act) {
    $max = max($max, $act->timestamp);
    if ($act->timestamp >= $now) {  
        echo('New activity ' . $act->event_id . ' is planned on ' . date('Y-m-d', $act->timestamp)); br();
        $nextact[] = $act;
    } else if ($act->timestamp >= $prev) {
        echo('Past activity ' . $act->event_id . ' was planned on ' . date('Y-m-d', $act->timestamp)); br();
        $pastact[] = $act;
    }
}
$dmax = new DateTime(date("Y-m-d H:i:s",$max));
echo('Latest activity planned on: ' . $dmax->format("Y-m-d"));

/**
 * Get all clubs for the current user
 */
echo('<h1>Clubs</h1>');
$url = API_URL . '/club?api_key=' . TEST_API_KEY;

try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($ch);
    curl_close($ch);
} catch (Exception $e) {
    echo('Exit with message: ' . $e->getMessage());
}
// echo('Raw data: ');
// echo('<pre>');
// print_r(json_decode($result));
// echo('</pre>');

$clubs = json_decode($result);

/**
 * Make an array with club id's only
 */
$clubids = [];

foreach($clubs->result as $club) {
    $clubids[] = $club->id;
    echo("Club found with ID: " . $club->id);
    br();
}

/**
 * Get all relevant events from all clubs
 * Loop through date range from last month until last planned activity
 * Get all events from all clubs in this timespan
 */
echo('<h1>Club events</h1>');
$clubevts = [];
foreach($clubids as $clubid) {
    $evtlist = [];
    $tdt = new DateTime($dt->format('Y-m-d'));
    while ($tdt < $dmax) {
        $url = API_URL . '/club/' . $clubid . '/event/' . $tdt->format("Y/m") . '?api_key=' . TEST_API_KEY;
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $result = curl_exec($ch);
            curl_close($ch);
        
            $gyms = json_decode($result);
        } catch (Exception $e) {
            echo('Exit with message: ' . $e->getMessage());
        }
        // echo('Raw data: ');
        // echo('<pre>');
        // print_r(json_decode($result));
        // echo('</pre>');
        $evts = json_decode($result);
        echo('Club ' . $clubid . ' events retrieved for month ' . $tdt->format('Y/m') . ' with status: ' . $evts->statusmessage); br();
        foreach($evts->result as $evt) {
            $evtlist[] = $evt;
        }
        //Increase current datetime with one month
        $tdt->modify("+1 month");
    }
    $curevt = array(
        "club" => $clubid,
        "evts" => $evtlist
    );
    $clubevts[] = $curevt;
}

/**
 * Get all activity definitions from all clubs
 */
echo('<h1>Club activities</h1>');
$clubacts = [];
foreach($clubids as $clubid) {
    $actlist = [];
    $url = API_URL . '/club/' . $clubid . '/activity/definition?api_key=' . TEST_API_KEY;
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($ch);
        curl_close($ch);
    
        $gyms = json_decode($result);
    } catch (Exception $e) {
        echo('Exit with message: ' . $e->getMessage());
    }
    // echo('Raw data: ');
    // echo('<pre>');
    // print_r(json_decode($result));
    // echo('</pre>');
    $acts = json_decode($result);
    echo('Club ' . $clubid . ' activity definition retrieved with status: ' . $acts->statusmessage); br();
    foreach($acts->result as $act) {
        $actlist[] = $act;
    }
    $curact = array(
        "club" => $clubid,
        "acts" => $actlist
    );
    $clubacts[] = $curact;
}







/**
 * Join all data to get a complete overview
 * Looptieloop!
 */
echo('<h1>Joined & formatted</h1>');
$nextact_clean = [];
$pastact_clean = [];
//Loop through clubs then events
foreach($clubevts as $ce) {
    foreach($ce['evts'] as $evt) {
        //Loop through clubs tthen activities
        foreach($clubacts as $ca) {
            foreach($ca['acts'] as $act) {
                //See if there is a match on activity ID
                if ($evt->activity_id == $act->id) {
                    //Loop through next user activities to see if the event is planned
                    foreach($nextact as $ua) {
                        //echo('Compare user event ID ' . $ua->event_id . ' with gym event ID ' . $evt->event_id); br();
                        if($ua->event_id == $evt->event_id) {
                            $tmp = array(
                                'club_id' => $ce['club'],
                                'activity_id' => $act->id,
                                'event_id' => $evt->event_id,
                                'activity_description' => $act->name,
                                'event_start' => $evt->event_start,
                                'event_start_dt' => date('Y-m-d H:i:s', $evt->event_start),
                                'event_end' => $evt->event_end,
                                'event_end_dt' => date('Y-m-d H:i:s', $evt->event_end),
                                'done' => $ua->done,
                                'deleted' => $ua->deleted
                            );
                            $nextact_clean[] = $tmp;
                        }
                    }

                    //Loop through past user activities to see if the event is planned
                    foreach($pastact as $ua) {
                        if($ua->event_id == $evt->event_id) {
                            $tmp = array(
                                'club_id' => $ce['club'],
                                'activity_id' => $act->id,
                                'event_id' => $evt->event_id,
                                'activity_description' => $act->name,
                                'event_start' => $evt->event_start,
                                'event_start_dt' => date('Y-m-d H:i:s', $evt->event_start),
                                'event_end' => $evt->event_end,
                                'event_end_dt' => date('Y-m-d H:i:s', $evt->event_end),
                                'done' => $ua->done,
                                'deleted' => $ua->deleted
                            );
                            $pastact_clean[] = $tmp;
                        }
                    }
                    break;
                }
            }

        }
    }
}
echo("<h2>Upcoming activities</h2>");
echo('<table><tr><th>club_id</th><th>activity_id</th><th>event_id</th><th>activity_description</th><th>event_start</th><th>event_start_dt</th><th>event_end</th><th>event_end_dt</th><th>done</th><th>deleted</th></tr>');
foreach($nextact_clean as $act) {
    echo('<tr>');
    echo('<td>' . $act['club_id'] . '</td><td>' . $act['activity_id'] . '</td><td>' . $act['event_id'] . '</td><td>' . $act['activity_description'] . '</td><td>' . $act['event_start'] . '</td><td>' . $act['event_start_dt'] . '</td><td>' . $act['event_end'] . '</td><td>' . $act['event_end_dt'] . '</td><td>' . $act['done'] . '</td><td>' . $act['deleted'] . '</td>');
    echo('</tr>');
}
echo('</table>');
//echo_pre($nextact_clean);

echo("<h2>Past activities</h2>");
//echo_pre($pastact_clean);
echo('<table><tr><th>club_id</th><th>activity_id</th><th>event_id</th><th>activity_description</th><th>event_start</th><th>event_start_dt</th><th>event_end</th><th>event_end_dt</th><th>done</th><th>deleted</th></tr>');
foreach($pastact_clean as $act) {
    echo('<tr>');
    echo('<td>' . $act['club_id'] . '</td><td>' . $act['activity_id'] . '</td><td>' . $act['event_id'] . '</td><td>' . $act['activity_description'] . '</td><td>' . $act['event_start'] . '</td><td>' . $act['event_start_dt'] . '</td><td>' . $act['event_end'] . '</td><td>' . $act['event_end_dt'] . '</td><td>' . $act['done'] . '</td><td>' . $act['deleted'] . '</td>');
    echo('</tr>');
}
echo('</table>');

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
