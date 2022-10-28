<?php
//Default include autoload
require_once __DIR__ . '/../private/config/autoload.php';

$password = $_POST['password'];
$auth = new Authenticator;
$auth->resetPassword($password);
redirectToUrl(public_base_url());