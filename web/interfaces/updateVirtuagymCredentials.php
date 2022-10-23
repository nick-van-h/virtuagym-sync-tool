<?php
//Default include autoload
require_once __DIR__ . '/../private/config/autoload.php';

$username = $_POST['username'];
$password = $_POST['password'];

$settings = new Settings;
$vg = new VirtuaGym;

if(isset($_POST['test']))  {
    echo('using test, not yet implemented');
    exit;
} else {
    $settings->updateVirtuagymCredentials($username, $password);
    br();
    echo_pre($_SESSION);
    redirectToUrl(public_base_url() . '/settings.php');
}