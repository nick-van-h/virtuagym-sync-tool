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
echo('<h1>Test Database class</h1>');
$db = new Vst\Controller\Database;

echo('<h2>Query without parameters</h2>');
$arr = [];
echo('Select user ID for Testuser, then getOne');br();
$sql = "SELECT `id`
        FROM `users`
        WHERE `username` = 'Testuser'";
foreach ($arr as $val) {
    echo('Value in array: ' . $val);br();
}
$db->query($sql);
$numrows = $db->getAllNumrows();
$rows = $db->getOne();
echo ('Resulting row:');
echo_pre($rows,'rows');
echo ('Expected outcome: [id] => 1');br();br();
echo('Query ran with result: ' . $db->getStatus());br();br();

echo('<h2>Query without parameters but with question in query</h2>');
echo('Select user ID for Testuser, then getOne');br();
$sql = "SELECT `id`
        FROM `users`
        WHERE `username` = (?)";
$db->query($sql);
$numrows = $db->getAllNumrows();
$rows = $db->getOne();
echo ('Resulting row:');
echo_pre($rows,'rows');
echo ('Expected outcome: null');br();br();
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
$settings = new Vst\Controller\Settings;
echo('<h2>Getters</h2>');
echo('Password hash: ' . $settings->getPasswordHash());br();
echo('User role: ' . $settings->getRole());br();
echo('Key encrypted: ' . $settings->getKeyEnc());br();
echo('Username (enc): ' . $settings->getVirtuagymUsernameEnc());br();
echo('Password (enc): ' . $settings->getVirtuagymPasswordEnc());br();
echo('Username from token (CHBS): ' . $settings->getUsernameFromToken('CorrectHorseBatteryStaple'));br();
echo('Token expiry date: ' . $settings->getTokenExpiryDate());br();

echo('<h2>Update & restore</h2>');
$org = $settings->getRole();
echo('Original role: ' . $org);br();
$settings->setRole('dork'); 
echo('Change role, result: ' . $settings->getRole());br();
$settings->setRole($org);
echo('Restore role, result: ' . $settings->getRole());br();


/**
 * Summary
 */
$testEnd = new \DateTime();
$diff = date_diff($testEnd, $testStart);
echo('<h1>Summary</h1>');
echo('Total runtime: ' . $diff->format('%H:%I:%S') . ' (h:m:s)');
br();br();
echo('--- end ---');