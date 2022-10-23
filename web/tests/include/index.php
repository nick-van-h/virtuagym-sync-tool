<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';
$start = new DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VST Test - xxx');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_test_nav();
echo('<main class="box-s">');

/**
 * Start main content
 */

//Include the functions first
require_once __DIR__ . '/functions.php';

//Define variable to be re-used in the view
$foo = 'Bar';

//Including the view should echo the content of $foo = 'Bar'
get_view();

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
