<?php

namespace Vst\Model\Database;

use Vst\Model\Database\Database;
use Vst\Model\Session;

class Activities extends Database
{

    private $newEntries;
    private $dupEntries;
    private $obsEntries;
    private $curEntries;

    private $session;

    public function __construct()
    {
        parent::__construct();
        $this->clearBuffer();

        $this->session = new Session;
    }

    /**
     * =============================================
     * Generic getters
     * =============================================
     */
    public function getLatestActivityTimestamp()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT MAX(`timestamp`) as `timestamp`
                FROM `activities`
                WHERE `user_id` = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getOne('timestamp');
    }

    function getAllJoined($asc = null)
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 month');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        //Determine sort, default = DESC
        $ascdesc = ORDER_DESC;
        if (isset($asc) && !empty($asc)) {
            switch ($asc) {
                case ORDER_ASC:
                    $ascdesc = ORDER_ASC;
                    break;
                case ORDER_DESC:
                    $ascdesc = ORDER_DESC;
                    break;
                default:
                    break;
            }
        }

        //Query results
        $sql = "SELECT DISTINCT 
                    act.`user_id`,
                    act.`act_inst_id`,
                    act.`done`,
                    act.`deleted`,
                    act.`act_id`,
                    act.`event_id`,
                    act.`timestamp`,
                    ad.`activity_id`,
                    ad.`name`,
                    ad.`deleted` as `actdef_deleted`,
                    ad.`club_id`,
                    ad.`duration`,
                    ed.`event_start`,
                    ed.`event_end`,
                    ed.`attendees`,
                    ed.`max_attendees`,
                    ed.`joined`,
                    ed.`deleted` as `evtdef_deleted`,
                    ed.`cancelled`,
                    ed.`bookable_from`
                FROM `activities` act
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                LEFT JOIN `evt_def` ed on act.`event_id` = ed.`event_id`
                WHERE act.`user_id` = (?) AND ed.`event_start` > (?)";
        if ($ascdesc == ORDER_ASC) {
            $sql .= "ORDER BY ed.event_start ASC";
        } else {
            $sql .= "ORDER BY ed.event_start DESC";
        }
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    /**
     * Get a list of activities which are relevant (not cancelled/deleted)
     * and which do not have an activity:appointment relation
     */
    function getUnsyncedActivities()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        /**
         * Query results
         * Start from activities, enrich with activity definition & event definition
         * Join the relation activity to appointment
         * Get all activities which are for the current user, within the current timeframe,
         *      which are still valid (i.e. not deleted/cancelled) and do not have a relation to an appointment
         */
        $sql = "SELECT DISTINCT 
                    act.`user_id`,
                    act.`act_inst_id`,
                    act.`done`,
                    act.`deleted`,
                    act.`act_id`,
                    act.`event_id`,
                    act.`timestamp`,
                    ad.`activity_id`,
                    ad.`name`,
                    ad.`deleted` as `actdef_deleted`,
                    ad.`club_id`,
                    ad.`duration`,
                    ed.`event_start`,
                    ed.`event_end`,
                    ed.`attendees`,
                    ed.`max_attendees`,
                    ed.`joined`,
                    ed.`deleted` as `evtdef_deleted`,
                    ed.`cancelled`,
                    ed.`bookable_from`
                FROM `activities` act
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                LEFT JOIN `evt_def` ed on act.`event_id` = ed.`event_id`
                LEFT JOIN `act_to_apt` ata ON act.`act_inst_id` = ata.`act_inst_id`
                WHERE act.`user_id` = (?) AND ed.`event_start` > (?) AND ata.`act_inst_id` IS NULL 
                        AND act.`deleted` = '0' AND ad.`deleted` = '0' AND ed.`deleted` = '0' AND ed.`cancelled` = '0'
                ORDER BY ed.`event_start` DESC";
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    function getMissingEvents()
    {

        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        /**
         * Query results
         * Start from activities, enrich with activity definition & event definition
         * We are interested where there is no matching event_id from the evt_def table
         * We need to know the day of the activity and the club it belongs to 
         */
        $sql = "SELECT DISTINCT 
                    act.`user_id`,
                    act.`act_inst_id`,
                    act.`act_id`,
                    act.`event_id`,
                    act.`timestamp`,
                    ad.`club_id`
                FROM `activities` act
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                LEFT JOIN `evt_def` ed on act.`event_id` = ed.`event_id`
                WHERE act.`user_id` = (?) AND act.`timestamp` > (?) AND ed.`event_id` IS NULL";
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    function getMissingActivityDefinitions()
    {

        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        /**
         * Query results
         * Start from activities, enrich with activity definition & event definition
         * We are interested where there is no matching event_id from the evt_def table
         * We need to know the day of the activity and the club it belongs to 
         */
        $sql = "SELECT DISTINCT 
                    act.`user_id`,
                    act.`act_inst_id`,
                    act.`act_id`,
                    act.`event_id`,
                    act.`timestamp`
                FROM `activities` act
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                WHERE act.`user_id` = (?) AND act.`timestamp` > (?) AND ad.`activity_id` IS NULL";
        parent::bufferParams($userid, $mintimestamp);
        parent::query($sql);
        return parent::getRows();
    }

    function getObsoleteAppointments()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        //Query results
        $sql = "SELECT DISTINCT 
                    ata.`appointment_id`
                FROM `act_to_apt` ata
                LEFT JOIN `activities` act on ata.`act_inst_id` = act.`act_inst_id`
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                LEFT JOIN `evt_def` ed on act.`event_id` = ed.`event_id`
                LEFT JOIN `appointments` apt ON ata.`appointment_id` = apt.`appointment_id`
                WHERE act.`user_id` = (?) AND apt.`appointment_id` IS NOT NULL AND
                        (act.`deleted` = '1' OR ad.`deleted` = '1' OR ed.`deleted` = '1' OR ed.`cancelled` = '1')";
        parent::bufferParams($userid);
        parent::query($sql);
        $res = parent::getRows('appointment_id');
        return $res;
    }

    /**
     * =============================================
     * User activities
     * =============================================
     */
    public function storeActivities($activities)
    {
        foreach ($activities as $activity) {
            $this->bufferActivity($activity);
        }
        //Query the buffer to the database
        $this->queryActivities();
    }

    public function bufferActivity($activity)
    {
        //Get the full list of existing activities from the server if not yet set
        if (empty($this->curEntries)) $this->retrieveAll_act_inst_id();

        //Add the child to the designated array based on if it exists already in the database
        if (in_array($activity->act_inst_id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryActivities()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        if (isset($this->dupEntries) && !empty($this->dupEntries)) {
            $sql = "UPDATE `activities`
                    SET `done`=(?), `deleted`=(?), `act_id`=(?), `event_id`=(?), `timestamp`=(?)
                    WHERE `user_id`=(?) AND `act_inst_id`=(?)";
            foreach ($this->dupEntries as $act) {
                parent::bufferParams($act->done, $act->deleted, $act->act_id, $act->event_id, $act->timestamp, $userid, $act->act_inst_id);
            }
            parent::query($sql);
            $success &= parent::getQueryOK();
        }

        /**
         * Insert new activities
         */
        if (isset($this->newEntries) && !empty($this->newEntries)) {
            $sql = "INSERT INTO `activities` (`user_id`, `act_inst_id`, `done`, `deleted`, `act_id`, `event_id`, `timestamp`)
                    VALUES (?,?,?,?,?,?,?)";
            foreach ($this->newEntries as $act) {
                parent::bufferParams($userid, $act->act_inst_id, $act->done, $act->deleted, $act->act_id, $act->event_id, $act->timestamp);
            }
            parent::query($sql);
            $success &= parent::getQueryOK();
        }

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
    public function storeActivityDefinitions($activities)
    {
        foreach ($activities as $clubactivities) {
            foreach ($clubactivities as $clubactivity) {
                $this->bufferActDef($clubactivity);
            }
        }
        //Query the buffer to the database
        $this->queryActDef();
    }

    public function bufferActDef($activity)
    {
        //Get the full list of existing activities from the server if not yet set
        if (empty($this->curEntries)) $this->retrieveAll_activity_id();

        //Add the child to the designated array based on if it exists already in the database
        if (in_array($activity->id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryActDef()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        if (isset($this->dupEntries) && !empty($this->dupEntries)) {
            $sql = "UPDATE `act_def`
                    SET `name`=(?), `deleted`=(?), `club_id`=(?), `duration`=(?)
                    WHERE `activity_id`=(?)";
            $stmt = $this->db->prepare($sql);
            foreach ($this->dupEntries as $act) {
                parent::bufferParams($act->name, $act->deleted, $act->club_id, $act->duration, $act->id);
            }
            parent::query($sql);
        }

        /**
         * Insert new activities
         */
        if (isset($this->newEntries) && !empty($this->newEntries)) {
            $sql = "INSERT INTO `act_def` (`activity_id`, `name`, `deleted`, `club_id`, `duration`)
                    VALUES (?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            foreach ($this->newEntries as $act) {
                parent::bufferParams($act->activity_id, $act->name, $act->deleted, $act->club_id, $act->duration);
            }
            parent::query($sql);
        }

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
    public function storeEventDefinitions($events)
    {
        foreach ($events as $clubevents) {
            foreach ($clubevents as $clubevent) {
                $this->bufferEvtDef($clubevent);
            }
        }
        $this->queryEvtDef();
    }

    public function bufferEvtDef($activity)
    {
        //Get the full list of existing activities from the server if not yet set
        if (empty($this->curEntries)) $this->retrieveAll_event_id();

        //Add the child to the designated array based on if it exists already in the database
        if (in_array($activity->event_id, $this->curEntries)) {
            $this->dupEntries[] = $activity;
        } else {
            $this->newEntries[] = $activity;
        }
    }

    public function queryEvtDef()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        if (isset($this->dupEntries) && !empty($this->dupEntries)) {
            $sql = "UPDATE `evt_def`
                    SET `activity_id`=(?), `event_start`=(?), `event_end`=(?), `attendees`=(?), `max_attendees`=(?), `joined`=(?), `deleted`=(?), `cancelled`=(?), `bookable_from`=(?)
                    WHERE `event_id`=(?)";
            foreach ($this->dupEntries as $act) {
                parent::bufferParams($act->activity_id, $act->event_start, $act->event_end, $act->attendees, $act->max_attendees, $act->joined, $act->deleted, $act->canceled, $act->bookable_from_timestamp, $act->event_id);
            }
            parent::query($sql);
        }

        /**
         * Insert new activities
         */
        if (isset($this->newEntries) && !empty($this->newEntries)) {
            $sql = "INSERT INTO `evt_def`(`event_id`, `activity_id`, `event_start`, `event_end`, `attendees`, `max_attendees`, `joined`, `deleted`, `cancelled`, `bookable_from`)
                    VALUES (?,?,?,?,?,?,?,?,?,?)";
            foreach ($this->newEntries as $act) {
                parent::bufferParams($act->event_id, $act->activity_id, $act->event_start, $act->event_end, $act->attendees, $act->max_attendees, $act->joined, $act->deleted, $act->canceled, $act->bookable_from_timestamp);
            }
            parent::query($sql);
        }

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
    function storeClubs($clubs)
    {
        //Generic variables
        $userid = $this->session->getUserID();

        //Get club id arrays
        $curClubIds = $this->getClubIds();
        $clubIds = [];
        foreach ($clubs as $club) {
            $clubIds[] = $club['club_id'];
        }

        //Generate arrays for new & obsolete clubs
        $obsClubs = [];
        $newClubs = [];
        $dupClubs = [];
        if (!empty($curClubIds) && $curClubIds) {
            foreach ($clubs as $club) {
                if (!in_array($club['club_id'], $curClubIds)) {
                    $newClubs[] = $club;
                } else {
                    $dupClubs[] = $club;
                }
            }
            foreach ($curClubIds as $curClubId) {
                if (!in_array($curClubId, $clubIds)) {
                    $obsClubs[] = $curClubId;
                }
            }
        } else {
            $newClubs = $clubs;
        }

        //Update duplicate clubs
        $sql = "UPDATE `clubs`
                SET `name`=(?), `address`=(?), `street`=(?), `zip_code`=(?), `city`=(?), `club_description`=(?)
                WHERE `user_id`=(?) AND `club_id`=(?)";
        foreach ($dupClubs as $club) {
            parent::bufferParams($club['name'], $club['address'], $club['street'], $club['zip_code'], $club['city'], $club['club_description'], $userid, $club['club_id']);
        }
        parent::query($sql);

        //Store new clubs
        if (isset($newClubs) && !empty($newClubs)) {
            $sql = "INSERT INTO `clubs` (`user_id`, `club_id`, `name`, `address`, `street`, `zip_code`, `city`, `club_description`)
                    VALUES (?,?,?,?,?,?,?,?)";
            foreach ($newClubs as $club) {
                parent::bufferParams($userid, $club['club_id'], $club['name'], $club['address'], $club['street'], $club['zip_code'], $club['city'], $club['club_description']);
            }
            parent::query($sql);
        }

        //Delete obsolete clubs
        if (isset($obsClubs) && !empty($obsClubs)) {
            $sql = "DELETE FROM `clubs` WHERE `user_id`=(?) AND `club_id`=(?)";
            foreach ($obsClubs as $club) {
                parent::bufferParams($userid, $club['club_id']);
            }
            parent::query($sql);
        }
    }

    function getClubIds()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT `club_id`
                FROM `clubs`
                WHERE `user_id` = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getRows('club_id');
    }

    /**
     * =============================================
     * Appointments
     * =============================================
     */
    public function storeAppointments($appointments)
    {
        foreach ($appointments as $appointment) {
            $this->bufferAppointment($appointment);
        }
        $this->bufferObsoleteAppointments($appointments);
        $this->queryAppointments();
    }

    public function bufferAppointment($activity)
    {
        //Get the full list of existing activities from the server if not yet set
        if (empty($this->curEntries)) $this->retrieveAll_appointment_id();

        //Add the child to the designated array based on if it exists already in the database
        if (!empty($this->curEntries)) {
            if (in_array($activity['id'], $this->curEntries)) {
                $this->dupEntries[] = $activity;
            } else {
                $this->newEntries[] = $activity;
            }
        } else {
            $this->newEntries[] = $activity;
        }
    }

    private function bufferObsoleteAppointments($appointments)
    {
        //Get the full list of existing activities from the server if not yet set
        if (empty($this->curEntries)) $this->retrieveAll_appointment_id();

        //Generate an array of id's of the appointments
        if (!empty($this->curEntries)) {
            $ids = [];
            foreach ($appointments as $appointment) {
                $ids[] = $appointment['id'];
            }

            //Get the id's which are in the db but no longer in the appointments
            $this->obsEntries = array_diff($this->curEntries, $ids);
        }
    }

    public function queryAppointments()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $success = true;

        /**
         * Update the existing activities with new values
         */
        if (!empty($this->dupEntries)) {
            $sql = "UPDATE `appointments`
                    SET `agenda_id`=(?)
                    WHERE `user_id`=(?) AND `appointment_id`=(?)";
            foreach ($this->dupEntries as $act) {
                parent::bufferParams($act['id'], $userid, $act['agendaId']);
            }
            parent::query($sql);
        }


        /**
         * Insert new activities
         */
        if (isset($this->newEntries) && !empty($this->newEntries)) {
            if (!empty($this->newEntries)) {
                $sql = "INSERT INTO `appointments`(`user_id`, `appointment_id`, `agenda_id`)
                        VALUES (?,?,?)";
                foreach ($this->newEntries as $act) {
                    parent::bufferParams($userid, $act['id'], $act['agendaId']);
                }
                parent::query($sql);
            }
        }

        /**
         * Remove obsolete activities
         */
        if (!empty($this->obsEntries)) {
            $sql = "DELETE FROM `appointments`
                    WHERE `user_id` = (?) AND `appointment_id` = (?)";
            foreach ($this->obsEntries as $id) {
                parent::bufferParams($userid, $id);
            }
            parent::query($sql);
        }

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();

        //Return the query status
        return $success;
    }

    /**
     * Appointment relations
     */
    function cleanupRelations()
    {
        //Prepare variables
        $userid = $this->session->getUserID();
        $dt = new \DateTime();
        $dt->modify('-1 week');
        $mintimestamp = strtotime($dt->format("Y-m-d H:i:s"));

        //Remove relations where activity is cancelled
        $sql = "DELETE ata FROM `act_to_apt` ata
                LEFT JOIN `activities` act ON ata.act_inst_id = act.act_inst_id
                LEFT JOIN `act_def` ad ON act.`act_id` = ad.`activity_id`
                LEFT JOIN `evt_def` ed on act.`event_id` = ed.`event_id`
                LEFT JOIN `appointments` apt ON ata.`appointment_id` = apt.`appointment_id`
                WHERE ata.`user_id` = (?) AND (act.`deleted` = '1' OR ad.`deleted` = '1' OR ed.`deleted` = '1' OR ed.`cancelled` = '1' OR apt.`appointment_id` IS NULL)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getRows();
    }

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
        foreach ($this->newEntries as $act) {
            parent::bufferParams($userid, $act['act_id'], $act['evt_id']);
        }
        parent::query($sql);

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();
    }

    function bufferObsoleteRelation($evtId)
    {
        $this->obsEntries[] = $evtId;
    }

    function queryRemoveRelations()
    {
        //Remove all obsolete appointments from the database
        $userid = $this->session->getUserID();
        $sql = "DELETE FROM `appointments` WHERE `user_id` = (?) AND `appointment_id` = (?)";
        foreach ($this->obsEntries as $act) {
            parent::bufferParams($userid, $act);
        }
        parent::query($sql);

        $sql = "DELETE FROM `act_to_apt` WHERE `user_id` = (?) AND `appointment_id` = (?)";
        foreach ($this->obsEntries as $act) {
            parent::bufferParams($userid, $act);
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
    private function retrieveAll_act_inst_id()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `act_inst_id`
                FROM `activities`
                WHERE `user_id` = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('act_inst_id');
    }

    private function retrieveAll_activity_id()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `activity_id`
                FROM `act_def`";
        parent::query($sql);
        $this->curEntries = parent::getRows('activity_id');
    }

    private function retrieveAll_event_id()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `event_id`
                FROM `evt_def`";
        parent::query($sql);
        $this->curEntries = parent::getRows('event_id');
    }

    private function retrieveAll_appointment_id()
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT `appointment_id`
                FROM `appointments`
                WHERE `user_id` = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        $this->curEntries = parent::getRows('appointment_id');
    }

    private function clearBuffer()
    {
        $this->newEntries = [];
        $this->dupEntries = [];
        $this->curEntries = [];
        $this->obsEntries = [];
    }
}
