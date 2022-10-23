<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';
$start = new DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VST Test - array');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_test_nav();
echo('<main class="box-s">');

/**
 * Start main content
 */
echo('<h1>Test</h1>');
echo('content of $_SESSION[status]:');
if (isset($_SESSION['status']) && !empty($_SESSION['status'])) var_dump($_SESSION['status']);
br();
$arr = [];
$arr['login'] = 'foobar';
echo('Array value = ' . $arr['login']);br();
$_SESSION['status'] = $arr;
echo('content of $_SESSION[status]:');
var_dump($_SESSION['status']);br();
$_SESSION['status']['virtuagym'] = 'baz';
echo('updated array, new content:');br();
var_dump($_SESSION['status']);br();
echo('unsetting session[status] & re-adding login');br();
unset($_SESSION['status']);
echo ('$_SESSION[status] exists: ' . (isset($_SESSION['status']) && !empty($_SESSION['status']) ? "true" : "false")); br();
$_SESSION['status']['login'] = 'qux';
echo('updated array, new content:');br();
var_dump($_SESSION['status']);br();

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
