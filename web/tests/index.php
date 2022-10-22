<?php
//Default include autoload
require_once __DIR__ . '/../modules/autoload.php';

//Get list of test files
$path    = __DIR__;
$files = array_values(array_diff(scandir($path), array('..', '.', 'index.php')));

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
echo ('<link rel="stylesheet" type="text/css" href="style.css">');
get_vw_head_end();

//Build the content
get_vw_test_nav();
echo ('<main class="box">');

//Main content
echo ('<ul class="list-of-tests">');
foreach ($files as $file) {
    if (is_dir($file)) {
        echo ('<li><a href="' . $file . '">' . $file . '</a></li>');
    }
}
echo ('</ul>');

//End the document
echo ('</div>');

//Foot
echo("</main>");
get_vw_foot();
