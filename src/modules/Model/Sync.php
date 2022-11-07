<?php

namespace Vst\Model;

use Vst\Controller\User;
use Vst\Controller\Session;
use Vst\Controller\VGDB;
use Vst\Controller\VGAPI;
use Vst\Controller\Calendar;
use Vst\Controller\Log;


class Sync {
    private $vgapi;
    private $vgdb;
    private $calendar;
    private $log;
    private $session;

    public function __construct() {
        /**
         * Init generic controllers
         */
        $this->user = new Users;
        $this->crypt = new Crypt;
        $this->session = new Session;
        $this->vgdb = new VGDB;
        $this->log = new Log;

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
     * Test connections
     */
    public function testVgConnection($username = NULL, $password = NULL) {
        return $this->vgapi->testConnection($username = NULL, $password = NULL);
    }

    public function getVgName($username = NULL, $password = NULL) {
        $this->vgapi->testConnection($username, $password);
        $data = $this->vgapi->getLastData();
        if (!empty($data->name)) {
            return $data->name;
        } else {
            return false;
        }
    }

    public function getLastVgMessage() {
        return $this->vgapi->getLastStatusMessage();
    }
    public function getLastVgCode() {
        return $this->vgapi->getLastStatusCode();
    }

    public function manualSyncAll() {
        $this->log->addEvent('Manual sync', 'Sync start');
        $this->log->startLinking();
        $this->syncAll();
        $this->log->addEvent('Manual sync', 'Sync end');
        $this->log->stopLinking();
    }

    public function scheduledSyncAll() {
        $this->log->addEvent('Scheduled sync', 'Sync start');
        $this->log->startLinking();
        $this->syncAll();
        $this->log->addEvent('Scheduled sync', 'Sync end');
        $this->log->stopLinking();
    }
    
    /**
     * Sync all activities from the API to our database
     */
    private function syncAll() {
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

        //Store last sync date
        $dt = new \DateTime();
        $this->user->setLastSync($dt->format('d-m-Y H:i:s'));
        
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
        $dt = new \DateTime(date('Y-m-1'));
        $earliest = $dt->modify('-1 month')->format('Y-m-d') . ' 00:00:00';
        $dtMax = new \DateTime(date("Y-m-d H:i:s", $this->vgdb->getLatestActivityTimestamp()));
        $dtArr = [];
        while($dt <= $dtMax) {
            $dtArr[] = $dt->format("Y/m");
            $dt->modify("+1 month");
        }
        return $dtArr;
    }
}