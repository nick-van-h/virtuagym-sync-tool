<?php
// TODO: Remove after validate
// try {
//     /**
//      * Config first
//      */
//     require_once __DIR__ . '/config.php';

//     /**
//      * Class modules
//      * Load classes based on the data model
//      */
//     require_once __DIR__ . '/../controllers/Session.php';
//     require_once __DIR__ . '/../controllers/Database.php';
//     require_once __DIR__ . '/../controllers/Users.php';
//     require_once __DIR__ . '/../controllers/Activities.php';
//     require_once __DIR__ . '/../controllers/VGAPI.php';
//     require_once __DIR__ . '/../controllers/Calendar/CalendarInterface.php';
//     require_once __DIR__ . '/../controllers/Calendar/Calendar_Google.php';
//     require_once __DIR__ . '/../controllers/Calendar.php';
//     require_once __DIR__ . '/../models/Crypt.php';
//     require_once __DIR__ . '/../models/Authenticator.php';
//     require_once __DIR__ . '/../models/Settings.php';
//     require_once __DIR__ . '/../models/Sync.php';

//     /**
//      * Generic functions & helpers
//      */
//     require_once __DIR__ . '/../helpers/generic.php';
//     require_once __DIR__ . '/../helpers/views.php';

//     //Start the session if it is not yet started
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }
// } catch (Exception $e) {
//     echo ("Error during autoload: " . $e->getMessage());
// }
