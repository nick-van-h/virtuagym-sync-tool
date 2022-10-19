<?php

/**
 * App-wide constants
 */
const BASE_PATH = __DIR__ . '/..';

/**
 * Class modules
 */
//require_once __DIR__ . '/modules/foo/foo.php';

/**
 * Generic functions & helpers
 */
require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/generic.php';
require_once __DIR__ . '/functions/views.php';
require_once __DIR__ . '/config.php';

//Start the session if it is not yet started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
