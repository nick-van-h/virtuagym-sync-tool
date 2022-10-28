<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';

$auth = new Authenticator;

$auth->logoutUser();
redirectToUrl(public_base_url());