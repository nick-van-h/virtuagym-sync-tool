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
        $required = array('calendar_account', 'target_agenda_id', 'target_agenda_name','refresh_token','timezone');
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
        $this->timezone = $credentials['timezone'];

    }

    public function testConnection()
    {
        /**
         * First check if an agenda is set, if no agenda is set then the setup is not complete
         * Get the current access token from the client
         * If there is not a valid access token then the returned value will be empty
         * If an access token is returned then check if it is still valid
         */
        if (!(isset($this->agenda) && !(empty($this->agenda)))) {
            return false;
        }
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
                            'name' => $cal->summary,
                            'timezone' => $cal->timezone
                        );
        }
        sort($ret);
        return $ret;
    }
    public function testAgenda()
    {}
    
    public function addEvent($evt)
    {
        //Create the event array
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $evt['name'] . ' (vgsync)',
            'location' => $evt['club_id'],
            'start' => array(
              'dateTime' => $this->tsToStr($evt['event_start']),
            ),
            'end' => array(
                'dateTime' => $this->tsToStr($evt['event_end']),
            ),
        ));
        if($this->timezone) {
            $event['start']['timezone'] = $this->timezone;
            $event['end']['timezone'] = $this->timezone;
        }

        //Insert the event in the selected agenda and return the event ID
        $evt = $this->cal->events->insert($this->agendaId,$event);
        return $evt['id'];
    }
    public function updateEvent()
    {}
    public function removeEvent($appointmentId)
    {
        //Remove the TMP id
        try{
            $this->cal->events->delete($this->agendaId, $appointmentId);
        } catch (\Google\Service\Exception $e) {
            $msg = $e->getMessage();
            //If the event is already removed from the calendar it is no problem, all other exceptions need to be addressed though
            if($msg != 'Resource has been deleted') {
                echo('TODO: Store this output & implement proper error handling in code');
                echo_pre($e,'error');
            }
        }
    }

    /**
     * Returns the events from -1month to +1month
     */
    public function getEvents()
    {
        //Retrieve the events on the calendar for the given parameters
        $dt = new \DateTime();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $this->dtToStr($dt->modify('-1 month')->modify('-1 day')),
            'timeMax' => $this->dtToStr($dt->modify('+2 months')->modify('+2 days'))
        );
        $result = $this->cal->events->listEvents($this->agendaId, $optParams);
        $events = [];

        //Loop through the events & pages
        while (true) {
            //Loop through the single events
            foreach ($result->getItems() as $event) {
                //Store the required event details in the array
                $events[] = array(
                    'all_day' =>  $event['start']['dateTime'] ? false : true, //if dateTime is passed this is not an all day event, otherwise it is
                    'agendaId' => $this->agendaId,
                    'summary' => $event['summary'],
                    'start' => $event['start']['dateTime'] ? $event['start']['dateTime'] : $event['start']['date'],
                    'end' => $event['end']['dateTime'] ? $event['end']['dateTime'] : $event['end']['date'],
                    'id' => $event['id'],
                    'colorId' => $event['colorId'],
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'recurrence' => $event['recurrence'],
                    'recurringEventId' => $event['recurringEventId'],
                    'reminders' => $event['reminders'],
                    'status' => $event['status'],
                    'visibility' => $event['visibility'],
                );
            }

            //Get the next page token
            $pageToken = $result->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $result = $this->cal->events->listEvents($this->agendaId, $optParams);
            } else {
                break;
            }
        }
        return $events;
    }

    /**
     * Speciic Google calendar class functions
     */


     private function dtToStr($dt) 
     {
        if($this->timezone) {
            $dtz = new \DateTimeZone($this->timezone);
            $dt->setTimezone($this->timezone);
        }
        return $dt->format('Y-m-d') . 'T' . $dt->format('H:i:sP');
     }

     private function strToDt($str)
     {

     }

     private function tsToStr($ts)
     {
        $dt = new \DateTime(date("Y-m-d H:i:s", $ts));
        return $this->dtToStr($dt);
     }
    
}