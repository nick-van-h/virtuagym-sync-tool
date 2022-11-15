<?php

namespace Vst\Controller;

use Vst\Controller\Settings;
use Vst\Controller\Session;
use Vst\Controller\EventsDB;

class VGAPI
{
    private $username;
    private $password;
    private $apiKey;

    private $statuscode;
    private $statusmessage;
    private $data;

    private $user;
    private $crypt;
    private $session;
    private $EventsDB;
    private $log;

    private const API_URL = 'https://api.virtuagym.com/api/v0';
    private const STATUS_OK = '200';
    private const EXCEED_REQUESTS = 'Too many API requests.';

    public function __construct($apikey, $username, $password)
    {
        //Set the config parameters
        $this->apiKey = $apikey;
        $this->username = $username;
        $this->password = $password;

        //Init constructors
        $this->log = new Log;
    }

    /**
     * Generic interfaces
     */
    public function getLastStatusCode()
    {
        return $this->statuscode;
    }
    public function getLastStatusMessage()
    {
        return $this->statusmessage;
    }
    public function getLastResultCount()
    {
        return $this->resultcount;
    }
    public function getLastData()
    {
        return $this->data;
    }

    /**
     * Test if the connection and credentials are working properly
     */
    public function testConnection($username = '', $password = '')
    {
        if (!empty($username)) $this->username = $username;
        if (!empty($password)) $this->password = $password;
        $this->log->addEvent('VirtuaGym', 'Testing credentials');
        $this->log->startSubLinking();
        $path = 'user/current';
        $this->call($path);
        $this->log->addEvent('VirtuaGym', 'Result: ' . $this->statusmessage);
        $this->log->stopSubLinking();
        return $this->statuscode == self::STATUS_OK;
    }

    /**
     * Specific calls
     */
    public function getActivities()
    {
        //Init data array to be returned by the function
        $data = [];

        //Make the call to get the activity data
        $path = 'activity';
        $this->call($path);

        //Store all activities less than one month old in the data array
        $dt = new \DateTime();
        $earliest = strtotime($dt->modify('-1 month')->modify('-1 day')->format('Y-m-d') . ' 00:00:00');
        if (isset($this->data) && !empty($this->data)) {
            foreach ($this->data as $activity) {
                if ($activity->timestamp >= $earliest) $data[] = $activity;
            }
        }

        //Return the final data array
        return $data;
    }

    public function getClubIds()
    {
        //Init data array to be returned by the function
        $data = [];

        //Make the call to get the club id's
        $path = 'user/current';
        $this->call($path);

        //Store the call result in the data array
        if ($this->hasResults()) {
            $data = $this->data->club_ids;
        }

        //Return the final data array
        return $data;
    }

    public function getActivityDefinitions($clubs)
    {
        //Init data array to be returned by the function
        $data = [];

        //Loop through clubs
        foreach ($clubs as $club) {
            //Make the call to get the activities for that club
            $path = 'club/' . $club . '/activity/definition';
            $this->call($path);

            //Store the call result in the data array
            if ($this->hasResults()) {
                $data[] = $this->data;
            }
        }

        //Return the final data array
        return $data;
    }

    public function getEventDefinitions($clubs, $dates)
    {
        //Init data array to be returned by the function
        $data = [];

        //Loop through clubs, then loop through the date range
        foreach ($clubs as $club) {
            foreach ($dates as $dt) {
                //Make the call to get the club events for that month
                $path = 'club/' . $club . '/event/' . $dt;
                $this->call($path);

                //Store the call result in the data array
                if ($this->hasResults()) {
                    //Append the event definition to the data array
                    $data[] = $this->data;
                }
            }
        }

        //Return the final data array
        return $data;
    }

    /**
     * Returns true if the call status is OK and there is at least one result row returned
     */
    private function hasResults()
    {
        return $this->statuscode == self::STATUS_OK && $this->resultcount;
    }

    /**
     * Generic caller
     */
    private function call($path)
    {
        $url = self::API_URL . '/' . $path . '?api_key=' . $this->apiKey;
        $this->log->addApiCall('VirtuaGym', 'Requested ' . self::API_URL . '/' . $path);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $reply = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo ('Exit with message: ' . $e->getMessage());
            $this->log->addWarning('API-call', 'Call to ' . self::API_URL . '/' . $path . ' failed with message: ' . $e->getMessage());
        }
        $result = json_decode($reply);
        $this->statuscode = $result->statuscode;
        $this->statusmessage = $result->statusmessage;
        $this->resultcount = $result->result_count;
        if ($this->resultcount) {
            $this->data = $result->result;
        }
    }
}
