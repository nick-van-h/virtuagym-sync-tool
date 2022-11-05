<?php

Namespace Controller;

Class Log extends Database {
    private $session;
    private $lastId;
    private $refId = NULL;

    public function __construct()
    {
        parent::construct();

        //Init other controllers
        $this->session = new Session;
    }

    public function addEvent($trigger, $message) {
        return $this->addEntry($trigger, 'Event', $message);
    }

    public function addWarning($trigger, $message) {
        return $this->addEntry($trigger, 'Warning', $message);
    }

    public function addError($trigger, $message) {
        return $this->addEntry($trigger, 'Error', $message);
    }

    public function startLinking() {
        $this->refId = $this->lastId;
    }

    public function stopLinking() {
        $htis->refId = NULL;
    }

    /**
     * Get API calls for current user
     * Pass the userid to the query function
     */
    public function getApiCalls($from, $to=null) {
        $userid = $this->session->getUserID();
        return $this->queryApiCalls($from, $to, $userid);
    }

    /**
     * Get API calls for all users
     * Do not pass the userid to the query function
     */
    public function getAllApiCalls($from, $to=null) {
        return $this->queryApiCalls($from, $to);
    }

    /**
     * Get API calls with the actual query
     * If the userid is passed then also filter on user_id
     * If no userid is passed then do not filter on user_id
     */
    private function queryApiCalls($from, $to=null, $userid=null) {
        //Init parameters
        if (!isset($to)) $to = new DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = strtotime($from->format('d-m-Y H:i:s'));
        $sqlEnd = strtotime($to->format('d-m-Y H:i:s'));

        //Query results
        $sql = "SELECT *
                FROM `log`
                WHERE `timestamp` > (?) AND `timestamp` <= (?) AND `category` = 'API-call'";
        if(isset($userid)) {
            //Apend the user_id to the where clause
            $sql .= " AND `user_id` = (?)";
            //Buffer the parameters including user id
            parent::bufferParams($sqlStart, $sqlEnd, $userid);
        } else {
            //Buffer the parameters without user id
            parent::bufferParams($sqlStart, $sqlEnd);
        }
        parent::query($sql);
        return parent::getAllRows();
    }

    /**
     * Get sync runs for current users
     * Do not pass the userid to the query function
     */
    public function getSyncRuns($from, $to=null) {
        $userid = $this->session->getUserID();
        return querySyncRuns($from, $to, $userid);
    }

    /**
     * Get sync runs for all users
     * Do not pass the userid to the query function
     */
    public function getAllSyncRuns($from, $to=null) {
        return querySyncRuns($from, $to);
    }

    /**
     * Get sync runs with the actual query
     * If the userid is passed then also filter on user_id
     * If no userid is passed then do not filter on user_id
     */
    public function querySyncRuns($from, $to=null, $userid=null) {
        //Init parameters
        if (!isset($to)) $to = new DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = strtotime($from->format('d-m-Y H:i:s'));
        $sqlEnd = strtotime($to->format('d-m-Y H:i:s'));

        //Query results
        $sql = "SELECT
                    s.*,
                    e.timestamp as `timestamp_end`,
                    e.message as `message_end`,
                    duration = e.timestamp - s.timestamp
                FROM `log` s
                LEFT JOIN (
                    SELECT `timestamp`
                    FROM `log`
                    WHERE `trigger` = 'Scheduled sync' AND `message` = `Sync end`
                ) e ON s.ref_id = e.id
                WHERE `timestamp` > (?) AND `timestamp` <= (?) AND `trigger` = 'Scheduled sync' AND `message` = 'Sync start'";
        if(!empty($userid)) {
            //Apend the user_id to the where clause
            $sql .= " AND `user_id` = (?)";
            //Buffer the parameters including user id
            parent::bufferParams($sqlStart, $sqlEnd, $userid);
        } else {
            //Buffer the parameters without user id
            parent::bufferParams($sqlStart, $sqlEnd, $userid);
        }
        parent::query($sql);
        return parent::getAllRows();
    }

    private function addEntry($trigger, $category, $message) {
        $userid = $this->session->getUserID();
        $sql = "INSERT INTO `log` (`user_id`, `trigger`, `category`, `message`, `ref_id`)
        VALUES (?),(?),(?),(?),(?)";
        parent::bufferParams($userid, $trigger, $category, $message, $this->refId);

        parent::query($sql);
        if (parent::getQueryOk()) {
            $sql = "SELECT MAX(id) as `id_max`
                    FROM `log`"
            parent::query($sql);
            $this->lastId = parent::getOne('id_max');
            return true;
        } else {
            return false;
        }
    }
}