<?php
//Default include autoload
require_once 'vendor/vst/autoload.php';

$_SESSION['debug'] = [];
redirectToUrl(public_base_url() . '/debug.php');