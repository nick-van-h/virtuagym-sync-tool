<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

//TODO: Migrate to AJAX call

$username = $_POST['username'];
$password = $_POST['password'];
$auth = new Vst\Controller\Authenticator;
$auth->loginUser($username, $password);
if ($auth->userIsLoggedIn()) {
    redirectToUrl(public_base_url() . '/app.php');
} else {
    redirectToUrl(public_base_url());
}
