<?php

namespace Vst\Controller;

use Vst\Controller\Database;

class Log extends Database
{
    private $session;
    private $lastId;
    private $subId;

    public function __construct()
    {
        parent::__construct();

        $this->subId = NULL;

        //Init other controllers
        $this->session = new Session;
    }

    public function addEvent($activity, $message)
    {
        return $this->addEntry($activity, 'Event', $message);
    }

    public function addWarning($activity, $message)
    {
        return $this->addEntry($activity, 'Warning', $message);
    }

    public function addError($activity, $message)
    {
        return $this->addEntry($activity, 'Error', $message);
    }

    public function addApiCall($activity, $message)
    {
        return $this->addEntry($activity, 'API-call', $message);
    }

    /**
     * Linking of events to other events
     * Global linking uses the session setting parameter
     * Sub linking uses the local variable in this class
     * - Use with caution, can only be used for one log instance
     */
    public function startLinking()
    {
        $this->session->setLogRefId($this->lastId);
    }

    public function startSubLinking()
    {
        $this->subId = $this->lastId;
    }

    public function stopLinking()
    {
        $this->session->setLogRefId(NULL);
    }

    public function stopSubLinking()
    {
        $this->subId = NULL;
    }

    /**
     * Get API calls for current user
     * Pass the userid to the query function
     */
    public function getApiCalls($from, $to = null)
    {
        $userid = $this->session->getUserID();
        return $this->queryApiCalls($from, $to, $userid);
    }

    /**
     * Get API calls for all users
     * Do not pass the userid to the query function
     */
    public function getAllApiCalls($from, $to = null)
    {
        return $this->queryApiCalls($from, $to);
    }

    public function getMaxApiCallsForOneUser($from, $to = null)
    {
        //Init parameters
        if (!isset($to)) $to = new \DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = $from->format('Y-m-d H:i:s');
        $sqlEnd = $to->format('Y-m-d H:i:s');

        $sql = "SELECT COUNT(*) as `numApiCalls`, `user_id`
                FROM `log`
                WHERE `timestamp` > (?) AND `timestamp` <= (?) AND `category` = 'API-call'
                GROUP BY `user_id`
                ORDER BY `numApiCalls` DESC";
        parent::bufferParams($sqlStart, $sqlEnd);
        parent::query($sql);
        $numCalls = parent::getOne('numApiCalls');
        if (!isset($numCalls) || empty($numCalls)) $numCalls = 0;
        return $numCalls;
    }

    public function getNrApiCalls($from, $to = null)
    {
        //Init parameters
        if (!isset($to)) $to = new \DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = $from->format('Y-m-d H:i:s');
        $sqlEnd = $to->format('Y-m-d H:i:s');
        $sql = "SELECT COUNT(*) as `numApiCalls`
                FROM `log`
                WHERE `timestamp` > (?) AND `timestamp` <= (?) AND `category` = 'API-call'";
        parent::bufferParams($sqlStart, $sqlEnd);
        parent::query($sql);
        $numCalls = parent::getOne('numApiCalls');
        if (!isset($numCalls) || empty($numCalls)) $numCalls = 0;
        return $numCalls;
    }

    /**
     * Get API calls with the actual query
     * If the userid is passed then also filter on user_id
     * If no userid is passed then do not filter on user_id
     */
    private function queryApiCalls($from, $to = null, $userid = null)
    {
        //Init parameters
        if (!isset($to)) $to = new \DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = $from->format('Y-m-d H:i:s');
        $sqlEnd = $to->format('Y-m-d H:i:s');

        //Query results
        $sql = "SELECT *
                FROM `log`
                WHERE `timestamp` > (?) AND `timestamp` <= (?) AND `category` = 'API-call'";
        if (isset($userid)) {
            //Apend the user_id to the where clause
            $sql .= " AND `user_id` = (?)";
            //Buffer the parameters including user id
            parent::bufferParams($sqlStart, $sqlEnd, $userid);
        } else {
            //Buffer the parameters without user id
            parent::bufferParams($sqlStart, $sqlEnd);
        }
        parent::query($sql);
        return parent::getRows();
    }

    /**
     * Get sync runs for current users
     * Do not pass the userid to the query function
     */
    public function getSyncRuns($from, $to = null)
    {
        $userid = $this->session->getUserID();
        return $this->querySyncRuns($from, $to, $userid);
    }

    /**
     * Get sync runs for all users
     * Do not pass the userid to the query function
     */
    public function getAllSyncRuns($from, $to = null)
    {
        return $this->querySyncRuns($from, $to);
    }

    /**
     * Get sync runs with the actual query
     * If the userid is passed then also filter on user_id
     * If no userid is passed then do not filter on user_id
     */
    private function querySyncRuns($from, $to = null, $userid = null)
    {
        //Init parameters
        if (!isset($to)) $to = new \DateTime();

        //Convert dt variables to sql timestamps
        $sqlStart = $from->format('Y-m-d H:i:s');
        $sqlEnd = $to->format('Y-m-d H:i:s');

        //Query results
        $sql = "SELECT
                    s.*,
                    e.timestamp as `timestamp_end`,
                    e.message as `message_end`,
                    TIMESTAMPDIFF(SECOND, s.timestamp, e.timestamp) as `duration_sec`
                FROM `log` s
                LEFT JOIN (
                    SELECT `timestamp`,`message`,`ref_log_id`
                    FROM `log`
                    WHERE (`activity` = 'Scheduled sync' OR `activity` = 'Manual sync') AND `message` = 'Sync end'
                ) e ON s.id = e.ref_log_id
                WHERE s.`timestamp` >= (?) AND s.`timestamp` <= (?) AND (s.`activity` = 'Scheduled sync' OR s.`activity` = 'Manual sync')  AND s.`message` = 'Sync start'";
        if (!empty($userid)) {
            //Apend the user_id to the where clause
            $sql .= " AND `user_id` = (?)";
            //Buffer the parameters including user id
            parent::bufferParams($sqlStart, $sqlEnd, $userid);
        } else {
            //Buffer the parameters without user id
            parent::bufferParams($sqlStart, $sqlEnd);
        }
        parent::query($sql);
        return parent::getRows();
    }

    private function addEntry($activity, $category, $message)
    {
        $userid = $this->session->getUserID();
        $refid = (!empty($this->subId) ? $this->subId : $this->session->getLogRefId());
        $sql = "INSERT INTO `log` (`user_id`, `activity`, `category`, `message`, `ref_log_id`)
                VALUES (?,?,?,?,?)";
        parent::bufferParams($userid, $activity, $category, $message, $refid);
        parent::query($sql);
        if (parent::getQueryOk()) {
            $sql = "SELECT MAX(id) as `id_max`
                    FROM `log`";
            parent::query($sql);
            $this->lastId = parent::getOne('id_max');
            $this->session->addDebug('Executed log query, last ID = ' . $this->lastId);
            return true;
        } else {
            $this->session->addDebug('Executed log query but failed');
            return false;
        }
    }
}
