<?php

namespace Model;

Use Model\Database;
use Model\Session;

Class VGDB extends Database {

    private $newActivities;
    private $dupActivities;
    private $extActivities;
    private $extActivityIds;
    private $extEventIds;

    private $session;

    public function __construct() {
        parent::__construct();

        $this->newActivities = [];
        $this->dupActivities = [];
        $this->extActivities = [];
        $this->extActivityIds = [];
        $this->extEventIds = [];

        $this->session = new Session;
    }

    public function bufferActivity($activity) {
        //Get the full list of existing activities from the server if not yet set
        if(empty($this->extActivities)) $this->retrieveAll_act_inst_id();

        //Generate the child to be added to the array
        $act = Array(
            'user_id' => $this->session->getUserID(),
            'act_inst_id' => $activity->act_inst_id,
            'done' => $activity->done,
            'deleted' => $activity->deleted,
            'act_id' => $activity->act_id,
            'event_id' => $activity->event_id,
        );

        //Add the child to the designated array based on if it exists already in the database
        if(in_array($activity->act_inst_id, $this->extActivities)) {
            $this->dupActivities[] = $act;
        } else {
            $this->newActivities[] = $act;
        }
    }

    public function queryActivities() {
        /**
         * Update the existing activities with new values
         */
        $rowsUpdated = 0;
        if(!empty($this->dupActivities)) {
            $sql = "UPDATE activities
                    SET done=(?), deleted=(?), act_id=(?), event_id=(?)
                    WHERE user_id=(?) AND act_inst_id=(?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->dupActivities as $act) {
                $stmt->bind_param("iiisii", $act['done'], $act['deleted'], $act['act_id'], $act['event_id'], $act['user_id'], $act['act_inst_id']);
                $status = $stmt->execute();
                $rowsUpdated += $stmt->affected_rows;
            }
        }

        /**
         * Insert new activities
         */
        if(!empty($this->newActivities)) {
            $sql = "INSERT INTO activities (`user_id`, `act_inst_id`, `done`, `deleted`, `act_id`, `event_id`)
                    VALUES (?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            foreach($this->newActivities as $act) {
                $stmt->bind_param("iiiiis", $act['user_id'], $act['act_inst_id'], $act['done'], $act['deleted'], $act['act_id'], $act['event_id']);
                $status = $stmt->execute();
                $rowsUpdated += $stmt->affected_rows;
            }
        }

        //Clear the existing activities array because it is now obsolete
        $this->extActivities = [];
    }

    private function retrieveAll_act_inst_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT act_inst_id
                FROM activities
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $this->extActivities[] = $row['act_inst_id'];
            }
        } else {
            return(false);
        }
    }

    private function retrieveAll_act_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT act_id
                FROM activities
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $this->extActivityIds[] = $row['act_inst_id'];
            }
        } else {
            return(false);
        }
    }

    private function retrieveAll_event_id() {
        $userid = $this->session->getUserID();
        $sql = "SELECT DISTINCT event_id
                FROM activities
                WHERE user_id = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userid);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $arr = [];
            while($row = $result->fetch_assoc()) {
                $this->extEventIds[] = $row['act_inst_id'];
            }
        } else {
            return(false);
        }
    }
}