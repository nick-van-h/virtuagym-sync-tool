<?php
//Default include autoload
require_once __DIR__ . '/../modules/autoload.php';

$username = $_POST['username'];
$password = $_POST['password'];
$auth = new Auth();
$auth->loginUser($username, $password);
if ($auth->userIsLoggedIn()) {
    redirectToUrl(public_base_url() . '/app.php');
} else {
    redirectToUrl(public_base_url());
}