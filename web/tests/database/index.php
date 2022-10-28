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
get_vw_head_title('VST Test - Database connection / obsolete');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_nav();
echo('<main class="test box-s">');

/**
 * Start main content
 */
echo('<h1>Obsolete</h1>');
echo('This test is obsolete with the new data model');
// echo('<h1>Test 1: Plain SQL connection</h1>');
// // Server name must be localhost
// $servername = "localhost";
  
// // In my case, user name will be root
// $username = "u93257p88111_testuser";
  
// // Password is empty
// $password = "Hammertime1995";
  
// // Creating a connection
// $conn = new mysqli($servername, 
//             $username, $password);
  
// // Check connection
// if ($conn->connect_error) {
//     die("Connection failure: " 
//         . $conn->connect_error);
// } 
  
// // Creating a database named geekdata
// $sql = "SELECT * FROM `u93257p88111_vst`.`test`";
// $result = $conn->query($sql);
// echo "Results: "; br();
// if($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         echo($row['col1']); br();
//     }
// }
  
// // Closing connection
// $conn->close();

// echo('<h1>Test 2: Connect with class</h1>');
// $db = new Db(getDbConfig());
// echo('Db connection status: ' . $db->getStatus()); br();
// echo('Testsetting_str for Testuser = ' . $db->getSettingValue('Testuser', 'testsetting_str'));br();
// echo('Testsetting_int for Testuser = ' . $db->getSettingValue('Testuser', 'testsetting_int'));br();
// echo('Testuser is a(n) ' . $db->getUserRole('Testuser'));

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
