<?php

use Model\Users;
use Model\Session;
use Model\VGDB;

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
        //echo_pre($this->data);

        $dt = new DateTime();
        foreach($this->data as $activity) {
            //$user_id, $act_inst_id, $done, $deleted, $act_id, $event_id
            $this->vgdb->bufferActivity($activity);
        }
        $this->vgdb->queryActivities();

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