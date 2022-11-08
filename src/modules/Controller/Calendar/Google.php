<?php

Namespace Vst\Controller\Calendar;

use Vst\Controller\Calendar\CalendarInterface;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;
use Vst\Model\Crypt;


Class Google implements CalendarInterface {
    private $client;
    private $cal;
    private $evt;
    private $service;

    private $crypt;

    private $account;
    private $agenda;

    private $tmpId;

    public function __construct($credentials) {
        /**
         * Check if all required keys are present in the passed $credentials array
         * Get the keys of the credentials array
         * Do array diff towards required keys
         * If there are any missing keys counted then throw an exception and mention the missing keys
         */
        $required = array('calendar_account', 'target_agenda_id', 'target_agenda_name','refresh_token');
        $credentialsKeys = array_keys($credentials);
        $missing = array_diff($required, $credentialsKeys);
        if (count($missing)) {
            //At least one key is missing, throw an exception and break initiation
            throw new \Exception('Passed credentials array is missing following keys: ' . implode('; ',$missing));
        }

        //Set variables
        $oauth = OAUTH_FILE;
        $this->account = $credentials['calendar_account'];
        $this->agenda = $credentials['target_agenda_name'];
        $this->agendaId = $credentials['target_agenda_id'];

        //Init crypt
        $this->crypt = new Crypt;

        //Init client
        $this->client = new \Google\Client();
        $this->client->setAuthConfig($oauth);
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->client->refreshToken($this->crypt->getDecryptedMessage($credentials['refresh_token']));

        //Init calendar/event service
        $this->cal = new \Google\Service\Calendar($this->client);
        $this->evt = new \Google\Service\Calendar\Events($this->client);
        $this->service = new \Google\Service($this->client);
    }

    public function testConnection()
    {
        /**
         * Get the current access token from the client
         * If there is not a valid access token then the returned value will be empty
         * If an access token is returned then check if it is still valid
         */
        $accessToken = $this->client->getAccessToken();
        if(!empty($accessToken)) {
            //TODO: Test for token expired via https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=xxx
            return true;
        } else {
            //The entered refresh token is invalid
            return false;
        }
    }
    public function getAccount()
    {
        return $this->account;
    }
    
    public function getAgendas()
    {
        $cals = $this->cal->calendarList->listCalendarList();
        $ret = [];
        foreach($cals->items as $cal) {
            $ret[] = Array('id' => $cal->id,
                            'name' => $cal->summary);
        }
        sort($ret);
        return $ret;
    }
    public function testAgenda()
    {}
    
    public function addAppointment()
    {
        //Create the event array
        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Test apt',
            'location' => 'Gym X',
            'description' => 'Foobar',
            'start' => array(
              'dateTime' => '2022-11-08T18:00:00+02:00',
              'timeZone' => 'Europe/Amsterdam',
            ),
            'end' => array(
                'dateTime' => '2022-11-08T19:00:00+02:00',
                'timeZone' => 'Europe/Amsterdam',
            ),
        ));

        //Insert the event in the selected agenda
        $evt = $this->cal->events->insert($this->agendaId,$event);
        $this->tmpId = $evt['id']; //Tmp store the ID
    }
    public function updateAppointment()
    {}
    public function removeAppointment()
    {
        //Remove the TMP id
        $this->cal->events->delete($this->agendaId, $this->tmpId);
    }
    public function getAppointment()
    {
        //Retrieve the events on the calendar for the given parameters
        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => '2022-11-07T00:00:00+01:00',
            'timeMax' => '2022-11-09T00:00:00+01:00'
        );
        $result = $this->cal->events->listEvents($this->agendaId, $optParams);
        return $result->getItems();
    }

    /**
     * Speciic Google calendar class functions
     */
    
}