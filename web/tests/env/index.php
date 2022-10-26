<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';
$start = new DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VST Test - Env');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_test_nav();
echo('<main class="box-s">');

/**
 * Start main content
 */
echo('<h1>Test</h1>');
echo(get_include_path());br();
$ini = parse_ini_file('/tmp/php/vst.ini');
echo_pre(getConfig());
echo_pre($ini);
//echo('Value = ' . $ini['FOOBAR_ENV']);
$file=fopen('/tmp/php/vst.ini', 'r');
putenv($file);br();
echo('Custom INI: ' . getenv('FOOBAR_ENV'));br();

echo_pre(ini_get_all());
echo('--end--');

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