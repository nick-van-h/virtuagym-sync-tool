<?php

namespace Vst\Controller\Calendar;

use Vst\Controller\Calendar\CalendarInterface;


Class Google implements CalendarInterface {

    public function __construct($credentials) {}
    public function addAppointment() {}
    public function updateAppointment() {}
    public function removeAppointment() {}
    public function getAppointment() {}
    public function getCalendars() {}
    public function testConnection() {}
}