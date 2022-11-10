schedule.js
getData
$ajax(
    getContent.php
)
=
ctrl->getEventsFromCalendars {
    calModel->getEventsFromCalendars
}
controller {
    $this->cal = $this->auth->getCalendarService //is Calender($client)
    this->calModel = new CalendarModel($this->cal) //is CalendarModel(Calendar($client))
}
Authenticator {

    this->client = new Auth\GoogleEx(getOAuthCredentialsFile());
    getCalendarService {
        return $this->client->getService() //Returns Calender($client)
    }
}
GoogleEx extends Google\Client {
    getService {
        service = new Google_Service_Calendar($this);
    }
}
CalendarModel($calendar) {
    this->cal = $calendar //passed Calendar($client)
    getEventsFromCalendars {
        this->getEvents {
            results = $this->cal->events->listEvents(id,params) //Calendar($client)->events->listEvents
            results->getItems()
        }
    }
}