<?php

use Controller\Users;
use Controller\Session;
Use Controller\VGDB;
Use Controller\VGAPI;
Use Controller\Calendar;


class Sync {
    private $vgapi;
    private $vgdb;
    private $calendar;

    public function __construct() {
        /**
         * Init generic controllers
         */
        $this->user = new Users;
        $this->crypt = new Crypt;
        $this->session = new Session;
        $this->vgdb = new VGDB;

        /**
         * Get api key, decrypted username and decrypted password
         * Init VG API with credentials
         */
        $conf = getConfig();
        $apikey = $conf['virtuagym_api_key'];
        $username = $this->crypt->getDecryptedMessage($this->user->getVirtuagymUsernameEnc());
        $password = $this->crypt->getDecryptedMessage($this->user->getVirtuagymPasswordEnc());

        $this->vgapi = new VGAPI($apikey, $username, $password);

        /**
         * Get calendar provider and credentials
         * Init calendar
         */
        $provider = $this->user->getCalendarProvider();
        $credentials = $this->user->getCalendarCredentials();

        $this->calendar = new Calendar($provider, $credentials);

    }


    /**
     * Sync all activities from the API to our database
     */
    public function syncAll() {
        /**
         * Get raw data from VG API and store in VG database
         */
        $this->vgdb->storeActivities($this->vgapi->getActivities());
        /**
         * Get the latest club id's from the recent activities call
         * Get the date range for user planned events from the recent activities call
         */
        $clubs = $this->vgapi->getClubIds();
        $dates = $this->getDates();
        /**
         * Update the database with the user specific info & club definities
         */
        $this->vgdb->storeClubs($clubs);
        $this->vgdb->storeActivityDefinitions($this->vgapi->getActivityDefinitions($clubs));
        $this->vgdb->storeEventDefinitions($this->vgapi->getEventDefinitions($clubs, $dates));

        /**
         * Update calendar with latest activities
         */
    }

    public function getAllStoredActivities() {
        return $this->vgdb->getAllJoined();
    }
    /**
     * Return the date of the last sync
     */
    function getLastSyncDate() {
        //TODO: Implement
        return 'Way too long ago';
    }

    private function getDates() {
        $dt = new DateTime(date('Y-m-1'));
        $earliest = $dt->modify('-1 month')->format('Y-m-d') . ' 00:00:00';
        $dtMax = new DateTime(date("Y-m-d H:i:s", $this->vgdb->getLatestActivityTimestamp()));
        $dtArr = [];
        while($dt <= $dtMax) {
            $dtArr[] = $dt->format("Y/m");
            $dt->modify("+1 month");
        }
        return $dtArr;
    }
}