<?php

namespace Controller;

Use Controller\Database;
Use Controller\Session;

Class VGDB extends Database {

    private $newEntries;
    private $dupEntries;
    private $curEntries;

    private $session;

    public function __construct() {
        parent::__construct();
        $this->clearBuffer();

        $this->session = new Session;
    }

    public function getLatestActivityTimestamp() {
        $userid = $this->session->getUserID();
        $sql = "SELECT MAX(`timestamp`) as `timestamp`
                FROM activities
                WHERE user_id = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getOne('timestamp');

        // $stmt = $this->db->prepare($sql);
        // $stmt->bind_param("i",$userid);
        // $stmt->execute();
        // $result = $stmt->get_result();
        // if ($result->num_rows > 0) {
        //     $row = $result->fetch_assoc();
        //     return($row['timestamp']);
        // } else {
        //     return(false);
        // }
        // return $timestamp;
    }

    public function bufferActivity($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_act_inst_id();

        //Generate the child to be added to the array
        $act = Array(
            'user_id' => $this->session->getUserID(),
            'act_inst_id' => $activity->act_inst_id,
            'done' => $activity->done,
            'deleted' => $activity->deleted,
            'act_id' => $activity->act_id,
            'event_id' => $activity->event_id,
            'timestamp' => $activity->timestamp,
        );

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->act_inst_id, $this->curEntries)) {
            $this->dupEntries[] = $act;
        } else {
            $this->newEntries[] = $act;
        }
    }

    public function queryActivities() {
        /**
         * Update the existing activities with new values
         */
        $rowsUpdated = 0;
        if(!empty($this->dupEntries)) {
            $sql = "UPDATE activities
                    SET done=(?), deleted=(?), act_id=(?), event_id=(?), timestamp=(?)
                    WHERE user_id=(?) AND act_inst_id=(?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->dupEntries as $act) {
                $stmt->bind_param("iiisiii", $act['done'], $act['deleted'], $act['act_id'], $act['event_id'], $act['timestamp'], $act['user_id'], $act['act_inst_id']);
                $status = $stmt->execute();
                $rowsUpdated += $stmt->affected_rows;
            }
        }

        /**
         * Insert new activities
         */
        if(!empty($this->newEntries)) {
            $sql = "INSERT INTO activities (`user_id`, `act_inst_id`, `done`, `deleted`, `act_id`, `event_id`, `timestamp`)
                    VALUES (?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->newEntries as $act) {
                $stmt->bind_param("iiiiisi", $act['user_id'], $act['act_inst_id'], $act['done'], $act['deleted'], $act['act_id'], $act['event_id'], $act['timestamp']);
                $status = $stmt->execute();
                $rowsUpdated += $stmt->affected_rows;
            }
        }

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();
    }

    //=============================================
    public function bufferActDef($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_activity_id();

        //Generate the child to be added to the array
        $act = Array(
            'user_id' => $this->session->getUserID(),
            'activity_id' => $activity->id,
            'name' => $activity->name,
            'deleted' => $activity->deleted,
            'club_id' => $activity->club_id,
            'duration' => $activity->duration,
        );

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->id, $this->curEntries)) {
            $this->dupEntries[] = $act;
        } else {
            $this->newEntries[] = $act;
        }
    }

    public function queryActDef() {
        /**
         * Update the existing activities with new values
         */
        if(!empty($this->dupEntries)) {
            $sql = "UPDATE act_def
                    SET name=(?), deleted=(?), club_id=(?), duration=(?)
                    WHERE user_id=(?) AND activity_id=(?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->dupEntries as $act) {
                $stmt->bind_param("siiiii", $act['name'], $act['deleted'], $act['club_id'], $act['duration'], $act['user_id'], $act['act_inst_id']);
                $status = $stmt->execute();
            }
        }

        /**
         * Insert new activities
         */
        if(!empty($this->newEntries)) {
            $sql = "INSERT INTO act_def (`user_id`, `activity_id`, `name`, `deleted`, `club_id`, `duration`)
                    VALUES (?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->newEntries as $act) {
                $stmt->bind_param("iisiii", $act['user_id'], $act['activity_id'], $act['name'], $act['deleted'], $act['club_id'], $act['duration']);
                $status = $stmt->execute();
            }
        }

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();
    }

    //=============================================
    public function bufferEvtDef($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->curEntries)) $this->retrieveAll_event_id();

        //Generate the child to be added to the array
        $act = Array(
            'user_id' => $this->session->getUserID(),
            'event_id' => $activity->event_id,
            'activity_id' => $activity->activity_id,
            'event_start' => $activity->event_start,
            'event_end' => $activity->event_end,
            'attendees' => $activity->attendees,
            'max_attendees' => $activity->max_attendees,
            'joined' => $activity->joined,
            'deleted' => $activity->deleted,
            'cancelled' => $activity->canceled,
            'bookable_from' => $activity->bookable_from_timestamp,
        );

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->event_id, $this->curEntries)) {
            $this->dupEntries[] = $act;
        } else {
            $this->newEntries[] = $act;
        }
    }

    public function queryEvtDef() {
        /**
         * Update the existing activities with new values
         */
        if(!empty($this->dupEntries)) {
            $sql = "UPDATE evt_def
                    SET activity_id=(?), event_start=(?), event_end=(?), attendees=(?), max_attendees=(?), joined=(?), deleted=(?), cancelled=(?), bookable_from=(?)
                    WHERE user_id=(?) AND event_id=(?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->dupEntries as $act) {
                $stmt->bind_param("iiiiiiiiiis", $act['activity_id'], $act['event_start'], $act['event_end'], $act['attendees'], $act['max_attendees'], $act['joined'], $act['deleted'], $act['cancelled'], $act['bookable_from'], $act['user_id'], $act['event_id']);
                $status = $stmt->execute();
            }
        }

        /**
         * Insert new activities
         */
        if(!empty($this->newEntries)) {
            $sql = "INSERT INTO `evt_def`(`user_id`, `event_id`, `activity_id`, `event_start`, `event_end`, `attendees`, `max_attendees`, `joined`, `deleted`, `cancelled`, `bookable_from`)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->newEntries as $act) {
                $stmt->bind_param("isiiiiiiiii", $act['user_id'], $act['event_id'], $act['activity_id'], $act['event_start'], $act['event_end'], $act['attendees'], $act['max_attendees'], $act['joined'], $act['deleted'], $act['cancelled'], $act['bookable_from']);
                $status = $stmt->execute();
            }
        }

        //Clear the existing activities array because it is now obsolete
        $this->clearBuffer();
    }

    function getAllJoined() {
        $userid = $this->session->getUserID();
        $sql = "SELECT * FROM `activities` a
                LEFT JOIN `act_def` ad ON a.act_id = activity_id
                LEFT JOIN `evt_def` ed on a.event_id = ed.event_id
                WHERE a.user_id = (?)
                ORDER BY ed.event_start DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $arr[] = $row;
            }
            return $arr;
        } else {
            return(false);
        }
    }
    

    //=============================================
    function storeClubs($clubs) {
        $userid = $this->session->getUserID();
        //Generate arrays for new & obsolete clubs
        $curClubs = $this->getClubs();
        $obsClubs = [];
        $newClubs = [];
        if(!empty($curClubs) && $curClubs) {
            foreach($clubs as $club) {
                if(!in_array($club, $curClubs)) {
                    $newClub[] = $club;
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
        if(!empty($newClubs)) {
            $sql = "INSERT INTO clubs (`user_id`, `club_id`)
                    VALUES (?,?)";
            $stmt = $this->db->prepare($sql);
            foreach($newClubs as $club) {
                $stmt->bind_param("ii", $userid, $club);
                $status = $stmt->execute();
            }
        }

        //Delete obsolete clubs
        if(!empty($obsClubs)) {
            $sql = "DELETE FROM `clubs` WHERE `user_id`=(?) AND `club_id`=(?)";
            $stmt = $this->db->prepare($sql);
            foreach($obsClubs as $club) {
                $stmt->bind_param("ii", $userid, $club);
                $status = $stmt->execute();
                echo('deleted club ' . $club);
            }
        }
    }

    //=============================================
    function getClubs() {
        $userid = $this->session->getUserID();
        $sql = "SELECT club_id
                FROM clubs
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $arr[] = $row['club_id'];
            }
            return $arr;
        } else {
            return(false);
        }
    }

    private function retrieveAll_act_inst_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT act_inst_id
                FROM activities
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $this->putQueryToCurEntries($stmt, 'act_inst_id');
    }

    private function retrieveAll_activity_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT activity_id
                FROM act_def
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $this->putQueryToCurEntries($stmt, 'activity_id');
    }

    private function retrieveAll_event_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT event_id
                FROM evt_def
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $this->putQueryToCurEntries($stmt, 'event_id');
    }

    private function putQueryToCurEntries($stmt, $col) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $this->curEntries[] = $row[$col];
            }
        } else {
            return(false);
        }
    }

    private function clearBuffer() {
        $this->newEntries = [];
        $this->dupEntries = [];
        $this->curEntries = [];
    }
}