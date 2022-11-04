<?php

namespace Vst\Controller\Calendar;

interface CalendarInterface {
    public function __construct($credentials);
    public function addAppointment();
    public function updateAppointment();
    public function removeAppointment();
    public function getAppointment();
    public function getCalendars();
    public function testConnection();
}