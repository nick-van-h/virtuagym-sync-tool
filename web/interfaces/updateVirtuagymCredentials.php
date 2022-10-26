<?php
//Default include autoload
require_once __DIR__ . '/../private/config/autoload.php';

use Model\Session;

$username = $_POST['username'];
$password = $_POST['password'];

$settings = new Settings;
$vg = new VirtuaGym;
$session = new Session;

exit;

if(isset($_POST['test']))  {
    if($vg->testConnection($username, $password)) {
        $data = $vg->getData();
        $session->setStatus('virtuagym','Connection OK! Account detected for ' . $data->name);
    } else {
        $session->setStatus('virtuagym','Connection error: ' . $vg->getStatusMessage());
    }
} else {
    $settings->updateVirtuagymCredentials($username, $password);
}
redirectToUrl(public_base_url() . '/settings.php');