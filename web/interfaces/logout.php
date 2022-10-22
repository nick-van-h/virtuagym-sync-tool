<?php
//Default include autoload
require_once __DIR__ . '/../modules/autoload.php';

$auth = new Auth;

$auth->logoutUser();
redirectToUrl(public_base_url());