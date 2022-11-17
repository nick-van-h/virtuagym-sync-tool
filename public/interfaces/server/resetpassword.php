<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

$password = $_POST['password'];
$auth = new Vst\Controller\Authenticator;
$auth->resetPassword($password);
$auth->revokeToken();
redirectToUrl(public_base_url());
