<?php
//Default include autoload
require_once 'vendor/autoload.php';

$_SESSION['debug'] = [];
redirectToUrl(public_base_url() . '/debug.php');