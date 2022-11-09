<?php

namespace Vst\Controller;

use Vst\Controller\Database;
use Vst\Controller\Session;

Class EventsDB extends Database {

    private $newEntries;
    private $dupEntries;
    private $curEntries;

    private $session;

    public function __construct() {
        parent::__construct();
        $this->clearBuffer();

        $this->session = new Session;
    }

    /**
     * =============================================
     * Generic getters
     * =============================================
     */
    public function getLatestActivityTimestamp() {
        $userid = $this->session->getUserID();
        $sql = "SELECT MAX(`timestamp`) as `timestamp`
                FROM activities
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getOne('timestamp');
    }

    function getAllJoined() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 month');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        //Query results
        $sql = "SELECT DISTINCT 
                    a.`user_id`,
                    a.`act_inst_id`,
                    a.`done`,
                    a.`deleted`,
                    a.`act_id`,
                    a.`event_id`,
                    a.`timestamp`,
                    ad.`activity_id`,
                    ad.`name`,
                    ad.`deleted` as actdef_deleted,
                    ad.`club_id`,
                    ad.`duration`,
                    ed.`event_start`,
                    ed.`event_end`,
                    ed.`attendees`,
                    ed.`max_attendees`,
                    ed.`joined`,
                    ed.`deleted` as evtdef_deleted,
                    ed.`cancelled`,
                    ed.`bookable_from`
                FROM `activities` a
                LEFT JOIN `act_def` ad ON a.act_id = activity_id
                LEFT JOIN `evt_def` ed on a.event_id = ed.event_id
                WHERE a.user_id = (?) AND ed.event_start > (?)
                ORDER BY ed.event_start DESC";
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    /**
     * Get a list of activities which do not have an activity:calendar link
     */
    function getUnsyncedActivities() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        //Query results
        $sql = "SELECT DISTINCT 
                    a.`user_id`,
                    a.`act_inst_id`,
                    a.`done`,
                    a.`deleted`,
                    a.`act_id`,
                    a.`event_id`,
                    a.`timestamp`,
                    ad.`activity_id`,
                    ad.`name`,
                    ad.`deleted` as actdef_deleted,
                    ad.`club_id`,
                    ad.`duration`,
                    ed.`event_start`,
                    ed.`event_end`,
                    ed.`attendees`,
                    ed.`max_attendees`,
                    ed.`joined`,
                    ed.`deleted` as evtdef_deleted,
                    ed.`cancelled`,
                    ed.`bookable_from`
                FROM `activities` a
                LEFT JOIN `act_def` ad ON a.act_id = activity_id
                LEFT JOIN `evt_def` ed on a.event_id = ed.event_id
                LEFT JOIN `act_to_apt` ata ON a.act_inst_id = ata.act_inst_id
                WHERE a.user_id = (?) AND ed.event_start > (?) AND ata.act_inst_id IS NULL AND a.`deleted` = '0'
                ORDER BY ed.event_start DESC"; //TODO NEXT: LEFT ANTI join where act_inst_id not in act_to_cal
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    /**
     * =============================================
     * User activities
     * =============================================
     */
    public function storeActivities($activities) {
        foreach($activities as $activity) {
            $this->bufferActivity($activity);
        }
        //Query the buffer to the database
        $this->queryActivities();
    }

    public function bufferActivity($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_act_inst_id();

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->act_inst_id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryActivities() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        $sql = "UPDATE activities
                SET done=(?), deleted=(?), act_id=(?), event_id=(?), timestamp=(?)
                WHERE user_id=(?) AND act_inst_id=(?)";
        foreach($this->dupEntries as $act) {
            parent::bufferParams($act->done, $act->deleted, $act->act_id, $act->event_id, $act->timestamp, $userid, $act->act_inst_id);
        }
        parent::query($sql);
        $success &= parent::getQueryOK();

        /**
         * Insert new activities
         */
        $sql = "INSERT INTO activities (`user_id`, `act_inst_id`, `done`, `deleted`, `act_id`, `event_id`, `timestamp`)
                VALUES (?,?,?,?,?,?,?)";
        foreach($this->newEntries as $act) {
            parent::bufferParams($userid, $act->act_inst_id, $act->done, $act->deleted, $act->act_id, $act->event_id, $act->timestamp);
        }
        parent::query($sql);
        $success &= parent::getQueryOK();

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();

        //Return the query status
        return $success;
    }

    /**
     * =============================================
     * Activity definitions
     * =============================================
     */
    public function storeActivityDefinitions($activities) {
        foreach($activities as $clubactivities) {
            foreach($clubactivities as $clubactivity) {
                $this->bufferActDef($clubactivity);
            }
        }
        //Query the buffer to the database
        $this->queryActDef();
    }

    public function bufferActDef($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_activity_id();

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryActDef() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        $sql = "UPDATE act_def
                SET `name`=(?), `deleted`=(?), `club_id`=(?), `duration`=(?)
                WHERE `user_id`=(?) AND `activity_id`=(?)";
        $stmt = $this->db->prepare($sql);
        foreach($this->dupEntries as $act) {
            parent::bufferParams($act->name, $act->deleted, $act->club_id, $act->duration, $userid, $act->id);
        }
        parent::query($sql);

        /**
         * Insert new activities
         */
        $sql = "INSERT INTO act_def (`user_id`, `activity_id`, `name`, `deleted`, `club_id`, `duration`)
                VALUES (?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        foreach($this->newEntries as $act) {
            parent::bufferParams($userid, $act->activity_id, $act->name, $act->deleted, $act->club_id, $act->duration);
        }
        parent::query($sql);

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();

        //Return the query status
        return $success;
    }


    /**
     * =============================================
     * Event definitions
     * =============================================
     */
    public function storeEventDefinitions($events) {
        foreach($events as $clubevents) {
            foreach($clubevents as $clubevent) {
                $this->bufferEvtDef($clubevent);
            }
        }
        $this->queryEvtDef();
    }

    public function bufferEvtDef($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_event_id();
        
        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->event_id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryEvtDef() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        $sql = "UPDATE evt_def
                SET activity_id=(?), event_start=(?), event_end=(?), attendees=(?), max_attendees=(?), joined=(?), deleted=(?), cancelled=(?), bookable_from=(?)
                WHERE user_id=(?) AND event_id=(?)";
        foreach($this->dupEntries as $act) {
            parent::bufferParams($act->activity_id, $act->event_start, $act->event_end, $act->attendees, $act->max_attendees, $act->joined, $act->deleted, $act->canceled, $act->bookable_from_timestamp, $userid, $act->event_id);
        }
        parent::query($sql);

        /**
         * Insert new activities
         */
        $sql = "INSERT INTO `evt_def`(`user_id`, `event_id`, `activity_id`, `event_start`, `event_end`, `attendees`, `max_attendees`, `joined`, `deleted`, `cancelled`, `bookable_from`)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        foreach($this->newEntries as $act) {
            parent::bufferParams($userid, $act->event_id, $act->activity_id, $act->event_start, $act->event_end, $act->attendees, $act->max_attendees, $act->joined, $act->deleted, $act->canceled, $act->bookable_from_timestamp);
        }
        parent::query($sql);

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();

        //Return the query status
        return $success;
    }
    

    /**
     * =============================================
     * Clubs
     * =============================================
     */
    function storeClubs($clubs) {
        //Generic variables
        $userid = $this->session->getUserID();

        //Generate arrays for new & obsolete clubs
        $curClubs = $this->getClubs();
        $obsClubs = [];
        $newClubs = [];
        if(!empty($curClubs) && $curClubs) {
            foreach($clubs as $club) {
                if(!in_array($club, $curClubs)) {
                    $newClubs[] = $club;
                }
            }
            foreach($curClubs as $club) {
                if(!in_array($club, $clubs)) {
                    $obsClubs[] = $club;
                }
            }
        } else {
            $newClubs = $clubs;
        }
        
        //Store new clubs
        $sql = "INSERT INTO clubs (`user_id`, `club_id`)
                VALUES (?,?)";
        foreach($newClubs as $club) {
            parent::bufferParams($userid, $club);
        }
        parent::query($sql);

        //Delete obsolete clubs
        $sql = "DELETE FROM `clubs` WHERE `user_id`=(?) AND `club_id`=(?)";
        foreach($obsClubs as $club) {
            parent::bufferParams($userid, $club);
        }
        parent::query($sql);
    }

    function getClubs() {
        $userid = $this->session->getUserID();
        $sql = "SELECT club_id
                FROM clubs
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getRows('club_id');
    }

    /**
     * =============================================
     * Appointments
     * =============================================
     */
    public function storeAppointments($appointments) {
        foreach($appointments as $appointment) {
            $this->bufferAppointment($clubevent);
        }
        $this->queryAppointments();
    }

    public function bufferAppointment($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_appointment_id();
        
        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->event_id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryAppointments() {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        $sql = "UPDATE appointments
                SET agenda_id=(?)
                WHERE user_id=(?) AND appointment_id=(?)";
        foreach($this->dupEntries as $act) {
            parent::bufferParams($act->agenda_id, $userid, $act->appointment_id);
        }
        parent::query($sql);

        /**
         * Insert new activities
         */
        $sql = "INSERT INTO `appointments`(`user_id`, `appointment_id`, `agenda_id`)
                VALUES (?,?,?)";
        foreach($this->newEntries as $act) {
            parent::bufferParams($userid, $act->appointment_id, $act->agenda_id);
        }
        parent::query($sql);

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();

        //Return the query status
        return $success;
    }

    /**
     * Appointment relations
     */
    function bufferRelation($actId, $evtId)
    {
        $this->newEntries[] = array(
            'act_id' => $actId,
            'evt_id' => $evtId
        );
    }
    
    function queryRelations()
    {
        //Add all new entries to the database
        $userid = $this->session->getUserID();
        $sql = "INSERT INTO `act_to_apt`(`user_id`, `act_inst_id`, `appointment_id`)
                VALUES (?,?,?)";
        foreach($this->newEntries as $act) {
            echo_pre($act);
            parent::bufferParams($userid, $act['act_id'], $act['evt_id']);
        }
        parent::query($sql);

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();
    }


    /**
     * =============================================
     * Private helpers
     * =============================================
     */
    private function retrieveAll_act_inst_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `act_inst_id`
                FROM activities
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('act_inst_id');
    }

    private function retrieveAll_activity_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `activity_id`
                FROM act_def
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('activity_id');
    }

    private function retrieveAll_event_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `event_id`
                FROM evt_def
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('event_id');
    }
    
    private function retrieveAll_appointment_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `appointment_id`
                FROM appointments
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('appointment_id');
    }

    private function clearBuffer() {
        $this->newEntries = [];
        $this->dupEntries = [];
        $this->curEntries = [];
    }
}