<?php

try {
    /**
     * Constants definitions first
     */
    require_once __DIR__ . '/functions/const.php';

    /**
     * Class modules
     */
    require_once __DIR__ . '/Db/Db.php';
    require_once __DIR__ . '/Auth/Auth.php';

    /**
     * Generic functions & helpers
     */
    require_once __DIR__ . '/functions/database.php';
    require_once __DIR__ . '/functions/generic.php';
    require_once __DIR__ . '/functions/views.php';
    require_once __DIR__ . '/config.php'; //Not included in GIT repo

    //Start the session if it is not yet started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    echo ("Error during autoload: " . $e->getMessage());
}
