<?php

namespace Vst\Controller;

use Vst\Model\Database\Settings;
use Vst\Model\Session;
use Vst\Model\Database\Activities;
use Vst\Model\VGAPI;
use Vst\Model\Calendar\CalendarFactory;
use Vst\Model\Database\Log;


class Sync
{
    private $vgapi;
    private $activities;
    private $cal;
    private $log;

    public function __construct()
    {
        /**
         * Init generic controllers
         */
        $this->settings = new Settings;
        $this->crypt = new Crypt;
        $this->session = new Session;
        $this->activities = new Activities;
        $this->log = new Log;

        /**
         * Get api key, decrypted username and decrypted password
         * Init VG API with credentials
         */
        $conf = getConfig();
        $apikey = $conf['virtuagym_api_key'];
        $username = $this->crypt->getDecryptedMessage($this->settings->getVirtuagymUsernameEnc());
        $password = $this->crypt->getDecryptedMessage($this->settings->getVirtuagymPasswordEnc());

        $this->vgapi = new VGAPI($apikey, $username, $password);

        /**
         * Get calendar provider and credentials
         * Init calendar
         */
        $provider = $this->settings->getCalendarProvider();
        if ($provider) {
            $credentials = $this->settings->getCalendarCredentials();
            $this->cal = CalendarFactory::getProvider($provider, $credentials);
        }
    }

    /**
     * Test connections
     */
    public function testVgConnection($username = NULL, $password = NULL)
    {
        return $this->vgapi->testConnection($username, $password);
    }

    public function getVgName($username = NULL, $password = NULL)
    {
        $this->vgapi->testConnection($username, $password);
        $data = $this->vgapi->getLastData();
        if (!empty($data->name)) {
            return $data->name;
        } else {
            return false;
        }
    }

    public function testCalendarConnection()
    {
        return $this->cal->testConnection();
    }

    public function getLastVgMessage()
    {
        return $this->vgapi->getLastStatusMessage();
    }
    public function getLastVgCode()
    {
        return $this->vgapi->getLastStatusCode();
    }

    public function manualSyncAll()
    {
        $this->log->addEvent('Manual sync', 'Sync start');
        $this->log->startLinking();
        $this->syncAll();
        $this->log->addEvent('Manual sync', 'Sync end');
        $this->log->stopLinking();
    }

    public function scheduledSyncAll()
    {
        $this->log->addEvent('Scheduled sync', 'Sync start');
        $this->log->startLinking();
        $this->syncAll();
        $this->log->addEvent('Scheduled sync', 'Sync end');
        $this->log->stopLinking();
    }


    /**
     * Sync all activities from the API to our database
     */
    private function syncAll()
    {
        //TODO: Before starting sync, check if all user settings are in place to actually perform the sync
        try {
            //Get all VG activities and store in the database
            $this->retrieveAndStoreActivities();

            //Get all calendar activities and store in the database
            $this->retrieveAndStoreAppointments();

            //Sync activities to calendar
            $this->addNewActivitiesToCalendar();
            $this->removeObsoleteActivitiesFromCalendar();

            //Store last sync date
            $dt = new \DateTime();
            $this->settings->setLastSync($dt->format('d-m-Y H:i:s'));
        } catch (\Exception $e) {
            //TODO: Handle exceptions
        } catch (\Error $er) {
            //TODO: Handle errors
        }
    }

    public function retrieveAndStoreActivities()
    {
        /**
         * Get raw data from VG API and store in VG database
         */
        $this->activities->storeActivities($this->vgapi->getActivities());
        /**
         * Get the latest club id's from the recent activities call
         * Get the date range for user planned events from the recent activities call
         */
        $clubs = $this->vgapi->getClubIds();
        $dates = $this->getDates();
        /**
         * Update the database with the user specific info & club definities
         */
        $this->activities->storeClubs($clubs);
        $this->activities->storeActivityDefinitions($this->vgapi->getActivityDefinitions($clubs));
        $this->activities->storeEventDefinitions($this->vgapi->getEventDefinitions($clubs, $dates));
    }

    public function retrieveAndStoreAppointments()
    {
        $events = $this->cal->getEvents();
        if (!empty($events)) $this->activities->storeAppointments($events);
    }

    /**
     * Update calendar with latest activities
     * Get an array of activities which are not synced
     * Loop through each activity
     * Add it to the calendar
     * Add a relation to the act_to_cal table
     */
    public function addNewActivitiesToCalendar()
    { //TODO make private
        $activities = $this->activities->getUnsyncedActivities();
        if (!empty($activities)) {
            foreach ($activities as $act) {
                $evtId = $this->cal->addEvent($act);
                $this->activities->bufferRelation($act['act_inst_id'], $evtId);
            }
            $this->activities->queryRelations();
        }
    }

    public function removeObsoleteActivitiesFromCalendar()
    { //TODO make private
        $activities = $this->activities->getObsoleteActivities();
        if (!empty($activities)) {
            foreach ($activities as $activity) {
                $this->cal->removeEvent($activity);
                $this->activities->bufferObsoleteRelation($activity);
            }
            $this->activities->queryRemoveRelations();
        }
    }


    public function getAllStoredActivities($asc)
    {
        return $this->activities->getAllJoined($asc);
    }
    /**
     * Return the date of the last sync
     */
    function getLastSyncDate()
    {
        $sync = $this->settings->getLastSync();
        return $sync;
    }

    private function getDates()
    {
        $dt = new \DateTime(date('Y-m-1'));
        $earliest = $dt->modify('-1 month')->format('Y-m-d') . ' 00:00:00';
        $dtMax = new \DateTime(date("Y-m-d H:i:s", $this->activities->getLatestActivityTimestamp()));
        $dtArr = [];
        while ($dt <= $dtMax) {
            $dtArr[] = $dt->format("Y/m");
            $dt->modify("+1 month");
        }
        return $dtArr;
    }
}
