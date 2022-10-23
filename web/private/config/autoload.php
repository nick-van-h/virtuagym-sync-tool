<?php

try {
    /**
     * Config first
     */
    require_once __DIR__ . '/config.php';

    /**
     * Class modules
     * Load classes based on the data model
     */
    require_once __DIR__ . '/../controllers/Session/Session.php';
    require_once __DIR__ . '/../controllers/Database/Database.php';
    require_once __DIR__ . '/../controllers/Users/Users.php';
    require_once __DIR__ . '/../models/Crypt/Crypt.php';
    require_once __DIR__ . '/../models/Authenticator/Authenticator.php';

    /**
     * Generic functions & helpers
     */
    require_once __DIR__ . '/../helpers/generic.php';
    require_once __DIR__ . '/../helpers/views.php';

    //Start the session if it is not yet started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    echo ("Error during autoload: " . $e->getMessage());
}
