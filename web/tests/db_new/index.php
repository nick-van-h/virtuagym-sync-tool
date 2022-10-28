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
get_vw_head_title('VST Test - New Database');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_nav();
echo('<main class="test box-s">');

/**
 * Start main content
 */
echo('<h1>Test Database class</h1>');
$db = new Controller\Database;

echo('<h2>Query without parameters</h2>');
echo('Select user ID for Testuser, then getOne');br();
$sql = "SELECT `id`
        FROM `users`
        WHERE `username` = 'Testuser'";
$db->query($sql);
$numrows = $db->getAllNumrows();
$rows = $db->getOne();
echo ('Resulting row:');
echo_pre($rows,'rows');
echo ('Expected outcome: [id] => 1');br();br();
echo('Query ran with result: ' . $db->getStatus());br();br();

echo('<h2>Query one with single input</h2>');
echo('2x get setting for testuser');
$sql = "SELECT `value_str`
        FROM `settings` s
        LEFT JOIN (
            SELECT *
            FROM `users`
            WHERE `username` = (?)
        ) u on s.`user_id` = u.`id`
        WHERE `setting_name` = (?)";
$db->bufferParams('Testuser','user_role');
$db->query($sql);
echo_pre($db->getOne(),'rows');
echo('==> Expected outcome: [value_str] => admin');br();

echo('<h2>Query with multiple buffer</h2>');
echo('2x get setting for testuser');
$sql = "SELECT `value_str`
        FROM `settings`
        WHERE `user_id` = '1' AND `setting_name` = (?)";
$db->bufferParams('user_role');
$db->bufferParams('password_reset_token');
$db->query($sql);
echo_pre($db->getRowsArr(),'rows');

echo('<h2>Query with multiple array buffer</h2>');
echo('3x get setting for userid = ?');
$sql = "SELECT `value_str`
        FROM `settings`
        WHERE `user_id` = (?) AND `setting_name` = (?)";
$db->bufferParams('1','user_role');
$db->bufferParams('1','password_reset_token');
$db->bufferParams('2','password_reset_token');
$db->query($sql);
echo_pre($db->getRowsArr(),'rows');

echo('<h2>Query all settings</h2>');
echo('Get rows for userid = ?');
$sql = "SELECT `setting_name`, `value_str`, `value_int`
        FROM `settings`
        WHERE `user_id` = (?)";
$uid = 1;
$db->bufferParams($uid);
$db->query($sql);
echo_pre($db->getRows(),'rows');

echo('<h2>Query with cast null</h2>');
echo('Update token to FooBarBaz');br();
$sql = "UPDATE settings 
        SET value_str=(?), value_int=(?), type=(?) 
        WHERE setting_name=(?)
        AND user_id = (?)";
$db->bufferParams('FooBarBaz','{i}NULL','str','password_reset_token',1);
$db->query($sql);
echo_pre($db->getOneNumrows(),'affected_rows');
echo('Query ran with result: ' . $db->getStatus());br();
echo('Update token to FooBarBaz123');br();
$sql = "UPDATE settings 
        SET value_str=(?), value_int=(?), type=(?) 
        WHERE setting_name=(?)
        AND user_id = (?)";
$db->bufferParams('FooBarBaz123','{i}NULL','str','password_reset_token',1);
$db->query($sql);
echo_pre($db->getOneNumrows(),'affected_rows');
echo('Query ran with result: ' . $db->getStatus());


echo('<h1>Test Users class</h1>');
$user = new Controller\Users;
echo('<h2>Getters</h2>');
echo('Password hash: ' . $user->getPasswordHash());br();
echo('User role: ' . $user->getRole());br();
echo('ID: ' . $user->getID());br();
echo('Key encrypted: ' . $user->getKeyEnc());br();
echo('Username (enc): ' . $user->getVirtuagymUsernameEnc());br();
echo('Password (enc): ' . $user->getVirtuagymPasswordEnc());br();
echo('Username from token (CHBS): ' . $user->getUsernameFromToken('CorrectHorseBatteryStaple'));br();
echo('Token expiry date: ' . $user->getTokenExpiryDate());br();

echo('<h2>Update & restore</h2>');
$org = $user->getRole();
echo('Original role: ' . $org);br();
$user->setRole('dork'); 
echo('Change role, result: ' . $user->getRole());br();
$user->setRole($org);
echo('Restore role, result: ' . $user->getRole());br();


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
