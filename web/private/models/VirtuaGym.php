<?php

Use Controller\Users;
Use Controller\Session;
Use Controller\VGDB;

class VirtuaGym {
    private $username;
    private $password;
    private $apiKey;

    private $statuscode;
    private $statusmessage;
    private $data;
    
    private $user;
    private $crypt;
    private $session;
    private $vgdb;

    private const API_URL = 'https://api.virtuagym.com/api/v0';
    private const STATUS_OK = '200';

    public function __construct(){
        //Initialize controllers
        $this->user = new Users;
        $this->crypt = new Crypt;
        $this->session = new Session;
        $this->vgdb = new VGDB;

        //Set the config parameters
        $conf = getConfig();
        $this->apiKey = $conf['virtuagym_api_key'];
        $this->username = $this->crypt->getDecryptedMessage($this->user->getVirtuagymUsernameEnc());
        $this->password = $this->crypt->getDecryptedMessage($this->user->getVirtuagymPasswordEnc());
    }

    public function testConnection($username = '', $password = '') {
        if (!empty($username)) $this->username = $username;
        if (!empty($password)) $this->password = $password;
        $path = 'user/current';
        $this->call($path);
        return $this->statuscode == self::STATUS_OK;
    }

    /**
     * Generic interfaces
     */
    public function getStatusCode() {
        return $this->statuscode;
    }
    public function getStatusMessage() {
        return $this->statusmessage;
    }
    public function getResultCount() {
        return $this->resultcount;
    }
    public function getData() {
        return $this->data;
    }

    /**
     * Specific calls
     */
    function callActivities() {
        //Make the call to get the activity data
        $path = 'activity';
        $this->call($path);

        //Buffer all activities less than one month old
        $dt = new DateTime();
        $earliest = strtotime($dt->modify('-1 month')->modify('-1 day')->format('Y-m-d') . ' 00:00:00');
        foreach($this->data as $activity) {
            if($activity->timestamp >= $earliest) $this->vgdb->bufferActivity($activity);
        }
        //Query the buffer to the database
        $this->vgdb->queryActivities();
    }

    function callClubIds() {
        //Make the call to get the club id's
        $path = 'user/current';
        $this->call($path);
        if ($this->hasResults()) {
            $clubs = $this->data->club_ids;
            $this->vgdb->storeClubs($clubs);
        }
    }

    function callActivityDefinitions() {
        $clubs = $this->vgdb->getClubs();

        foreach($clubs as $club) {
            //Make the call to get the club activities
            $path = '/club/' . $club . '/activity/definition';
            $this->call($path);
            if ($this->hasResults()) {
                foreach($this->data as $actdef) {
                    $this->vgdb->bufferActDef($actdef);
                }
            }
        }
        $this->vgdb->queryActDef();
    }

    function callEventDefinitions() {
        $clubs = $this->vgdb->getClubs();
        $dates = $this->getDates();

        foreach($clubs as $club) {
            foreach($dates as $dt) {
                //Make the call to get the club events
                $path = '/club/' . $club . '/event/' . $dt;
                $this->call($path);
                if ($this->hasResults()) {
                    foreach($this->data as $evtdef) {
                        $this->vgdb->bufferEvtDef($evtdef);
                    }
                }
            }
        }
        $this->vgdb->queryEvtDef();
    }

    function getEnrichedActivities() {
        return ($this->vgdb->getAllJoined());
    }

    private function getDates() {
        $dt = new DateTime();
        $earliest = $dt->modify('-1 month')->modify('-1 day')->format('Y-m-d') . ' 00:00:00';
        $dtMax = new DateTime(date("Y-m-d H:i:s", $this->vgdb->getLatestActivityTimestamp()));
        $dtArr = [];
        while($dt < $dtMax) {
            $dtArr[] = $dt->format("Y/m");
            $dt->modify("+1 month");
        }
        return $dtArr;
    }

    private function hasResults() {
        return $this->statuscode == self::STATUS_OK && $this->resultcount;
    }

    /**
     * Generic caller
     */
    private function call($path) {
        $url = self::API_URL . '/' . $path . '?api_key=' . $this->apiKey;
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $reply = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo('Exit with message: ' . $e->getMessage());
        }
        $result = json_decode($reply);
        $this->statuscode = $result->statuscode;
        $this->statusmessage = $result->statusmessage;
        $this->resultcount = $result->result_count;
        if($this->resultcount) {
            $this->data = $result->result;
        }
    }


}