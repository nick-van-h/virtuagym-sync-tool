<?php

Namespace Vst\Controller\Calendar;

use Vst\Controller\Calendar\CalendarInterface;
use Google\Service\Calendar as Google_Service_Calendar;


Class Google implements CalendarInterface {
    private $client;
    private $credentials;
    private $cal;

    public function __construct($credentials) {
        $conf = getConfig();
        $oauth = getGoogleOauth();
        $oauth = OAUTH_FILE;

        //Init client
        $this->client = new \Google\Client();
        $this->client->setAuthConfig($oauth);
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        //$this->client->setDeveloperKey($conf['google_api_key']);

        //Init calendar service
        $this->cal = new \Google\Service\Calendar($this->client);

        $this->credentials = $credentials;
        
    }

    public function testConnection()
    {
        /**
         * Get the current access token from the client
         * If there is not a valid access token then the returned value will be empty
         */
        $accessToken = $this->client->getAccessToken();
        if(empty($accessToken)) {
            //Set refresh token and get new access token
            $refreshToken = $_SESSION['refresh_token'];
            $this->client->refreshToken($refreshToken);
            $accessToken = $this->client->getAccessToken();
        } 
        
        /**
         * Check if the access token is set (again, after possible setting refresh token)
         * If it's empty then the refresh token is invalid
         * If an access token is returned then check if it is still valid
         * If not, get a new access token via refresh token and check if access token is set (again, to check if refresh token is valid)
         */
        if (!empty($accessToken)) {
            //TODO: Test for token expired via https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=xxx
            return true;
        } else {
            //The entered refresh token is invalid
            return false;
        }
    }
    public function setupCalendarProvider()
    {

    }
    
    public function getCalendars()
    {
        $cals = $this->cal->calendarList->listCalendarList();
        $ret = [];
        foreach($cals->items as $cal) {
            $ret[] = $cal->summary;
        }
        return $ret;
    }
    public function testCalendar()
    {}
    
    public function addAppointment()
    {}
    public function updateAppointment()
    {}
    public function removeAppointment()
    {}
    public function getAppointment()
    {}
}