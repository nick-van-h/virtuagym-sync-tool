<?php

namespace Vst\Controller;

use Exception;
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
    private $settings;

    public function __construct()
    {
        /**
         * Init database controllers & check if db connection can be made
         * If no connection can be made then there is no use to continue
         */
        $this->log = new Log;
        $this->settings = new Settings;
        $this->activities = new Activities;
        $this->settings = new Settings;
        if (!$this->log->getConnectionOk() || !$this->settings->getConnectionOk() || !$this->activities->getConnectionOk() || !$this->settings->getConnectionOk()) {
            throw new Exception("Internal server error: Unable to establish database connection(s)");
        }

        /**
         * Get api key, decrypted username and decrypted password
         * Init VG API with credentials
         */
        $conf = getConfig();
        $apikey = $conf['virtuagym_api_key'];
        $username = $this->settings->getVirtuagymUsername();
        $password = $this->settings->getVirtuagymPassword();

        $this->vgapi = new VGAPI($apikey, $username, $password);

        /**
         * Get calendar provider and credentials
         * Init calendar
         */
        $provider = $this->settings->getCalendarProvider();
        if ($provider) {
            $credentials = $this->settings->getCalendarCredentials();
            $this->cal = CalendarFactory::getProvider($provider, $credentials);
            if ($this->cal->testConnection()) throw new \Exception("Unable to establish Calendar connection");
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
            /**
             * Start by getting the latest activity data from virtuagym
             */
            $this->retrieveAndStoreActivities();

            /**
             * Get all calendar appointments and store in the database
             * At this point the calendar no longer contains the activities that were just removed
             */
            $this->retrieveAndStoreAppointments();

            /**
             * With the updated activity info we can remove obsolete appointments
             */
            $this->removeObsoleteActivitiesFromCalendar();

            /**
             * Now that we have the actual activities and appointments we need to clean up the relation table
             * Example;
             * - VG Activities: 1 (cancelled), 2, 3, 4 
             * - Relations: 1=a, 2=b, 3=c, 4=d
             * - Calendar appointments: b, c, ...xyz (a was removed by script, d was removed by user)
             * Expected outcome act2apt: 2=b, 3=c;
             * -> Remove 1=a because cancelled, remove 4=c because appointment deleted
             */
            $this->activities->cleanupRelations();

            /**
             * Now that the relations table is cleaned up we can update the calendar
             * with (new) activities that do not occur in the relation table;
             * - Add appointment to the calendar & get the ID of the appointment
             * - Add relation to the table for this new appointment ID
             */
            $this->addNewActivitiesToCalendar();

            //Store last sync date
            $dt = new \DateTime();
            $this->settings->setLastSync($dt->format('d-m-Y H:i:s'));
        } catch (\Exception $e) {
            //TODO: Handle exceptions
            echo ('Exception occurred in sync: ' . $e->getMessage());
        } catch (\Error $er) {
            //TODO: Handle errors
            echo ('Exception occurred in sync: ' . $er->getMessage());
        }
    }

    /**
     * Get latest activity info from virtuagy & store in database
     * @return bool succesful
     */
    public function retrieveAndStoreActivities()
    {
        /**
         * Get raw data from VG API 
         * Check if first call was succesful (i.e. credentials are valid)
         * and store in VG database
         * If call was unsuccesful add a log entry and abort sync
         */
        $activities = $this->vgapi->getActivities();
        if (!$this->vgapi->getLastStatusIsOk()) {
            $this->log->addError('Sync', 'Unable to retrieve data from VirtuaGym: ' . $this->vgapi->getLastStatusMessage());
            $this->settings->setLastCalendarConnectionStatusNok();
            return false;
        }
        $this->activities->storeActivities($activities);

        /**
         * Check if there are any missing activity definitions
         * If so, retrieve the current clubs from the database
         * Then retrieve the activity definitions for those clubs
         * And store it in the database
         */
        $missingDefinitions = $this->activities->getMissingActivityDefinitions();
        if (isset($missingDefinitions) && !empty($missingDefinitions)) {
            $curClubIds = $this->activities->getClubIds();
            $this->getAndStoreActivityDefinitions($curClubIds);
        }

        /**
         * Check for new clubs
         * Check again if there are missing activity definitions
         * If this is the case it means that the user added a new club (and planned at least 1 activity)
         * Retrieve new clubs and retrieve activity definitions for those clubs
         */
        $missingDefinitions = $this->activities->getMissingActivityDefinitions();
        if (isset($missingDefinitions) && !empty($missingDefinitions)) {
            //Get clubs info and store in database
            $clubs = $this->vgapi->getClubs();
            $this->activities->storeClubs($clubs);


            //Extract the ID's from the array
            $clubIds = [];
            foreach ($clubs as $club) {
                $clubIds[] = $club['club_id'];
            }

            //Extract new clubs only
            $newClubIds = array_diff($clubIds, $curClubIds);

            //Loop through new clubs and get activity definitions
            if (isset($newClubIds) && !empty($newClubIds)) {
                $this->getAndStoreActivityDefinitions($newClubIds);
            }
        }


        /**
         * Get the latest club id's from the recent activities call
         * Get the date range for user planned events from the recent activities call
         */
        $clubs = $this->vgapi->getClubs();
        $dates = $this->getDates();
        /**
         * Update the database with the user specific info & club definities
         * Check if any activity definition is missing, if so;
         * We always need to retrieve & store the activity definition
         * There is no relation from user activity to club, so this must be done for all clubs
         */
        $this->activities->storeClubs($clubs);
        $this->activities->storeActivityDefinitions($this->vgapi->getActivityDefinitions($clubs));
        $this->activities->storeEventDefinitions($this->vgapi->getEventDefinitions($clubs, $dates));
        /**
         * Check if all database queres were executed ok
         */
        if ($this->activities->getQueryOk()) {
            return true;
        } else {
            $dbErrors = $this->activities->getErrors();
            foreach ($dbErrors as $error) {
                $this->log->addError('Sync', 'Unable to store data: ' . $error);
            }
            return false;
        }
    }

    private function getAndStoreActivityDefinitions($clubIds)
    {
        foreach ($clubIds as $clubId) {
            $activities = $this->vgapi->getActivityDefinitions($clubId);
            $this->activities->storeActivityDefinitions($activities);
        }
    }

    /**
     * Get all current appointments from the calendar
     * Update the appointments table to match the calendar
     */
    public function retrieveAndStoreAppointments()
    {
        $events = $this->cal->getEvents();
        if (!empty($events)) $this->activities->storeAppointments($events);
    }

    public function removeObsoleteActivitiesFromCalendar()
    {
        $appointments = $this->activities->getObsoleteAppointments();
        if (!empty($appointments)) {
            foreach ($appointments as $apt) {
                $this->cal->removeEvent($apt);
            }
        }
    }

    /**
     * Update calendar with latest activities
     * Get an array of activities which are not synced
     * Loop through each activity
     * Add it to the calendar
     * Add a relation to the act_to_cal table
     */
    private function addNewActivitiesToCalendar()
    {
        $activities = $this->activities->getUnsyncedActivities();
        if (!empty($activities)) {
            foreach ($activities as $act) {
                $evtId = $this->cal->addEvent($act);
                $this->activities->bufferRelation($act['act_inst_id'], $evtId);
            }
            $this->activities->queryRelations();
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
