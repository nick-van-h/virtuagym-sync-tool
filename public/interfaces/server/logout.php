<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

$auth = new Vst\Model\Authenticator;

$auth->logoutUser();
redirectToUrl(public_base_url());