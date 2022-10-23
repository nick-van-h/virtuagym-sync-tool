<?php
//Default include autoload
require_once __DIR__ . '/../private/config/autoload.php';

$_SESSION['debug'] = [];
redirectToUrl(public_base_url() . '/admin.php');