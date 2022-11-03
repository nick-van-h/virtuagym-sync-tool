<?php

Namespace Controller\Calendar;

use Controller\Calendar\CalendarInterface;


Class Google implements CalendarInterface {
    private $client;

    public function __construct($credentials) {
        $client = new Google\Client();
        
    }
    public function addAppointment() {}
    public function updateAppointment() {}
    public function removeAppointment() {}
    public function getAppointment() {}
    public function getCalendars() {}
    public function testConnection() {}
}