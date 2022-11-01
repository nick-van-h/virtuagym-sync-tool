<?php
//Default include autoload
require_once 'vendor/autoload.php';

$auth = new Authenticator;

$auth->logoutUser();
redirectToUrl(public_base_url());