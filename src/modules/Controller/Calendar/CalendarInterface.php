<?php

Namespace Controller\Calendar;

interface CalendarInterface {
    /**
     * When initializing the class the credentials array (provider specific) should be passed
     * Initializing the class should arrange all provider specific checks & settings
     */
    public function __construct($credentials);
    
    /**
     * When setting up the calendar provider the user must be able to do two things
     * - Test the connection with the entered credentials
     *   -- If no provider is set up the test will fail
     *   -- If a provider is set up the test will check if calendars can be retrieved
     *   -- If a static provider is selected (like Google) then credentials can only be changed by setting up a new calendar provider
     *   -> Testing the connection should return true/false on success/fail & use session->setStatus to pass message
     * - Save the provider credentials information
     *   -- Whether or not a provider is set up this function should initiate the process for setting up a new provider & store the credentials in the DB
     *   -- The button on the form should indicate the course of action
     *      --- E.g. Google; "switch account", CalDAV; "save credentials"
     *      --- Defining the course of action is a users/setting action and is not to be implemented by the calendar class
     *   -> Setting up the calendar should return true/false on success/fail & use session->setStatus to pass message
     */
    public function testConnection();
    public function setupCalendarProvider();

    /**
     * After setting up the calendar provider the available calendars are to be retrieved
     * Saving the selected calendar is a users/setting action and is not to be implemented by the calendar class
     * The user should be able to test if the selected calendar still exists on the server
     * -> Getting the calendars should return an array of calendars or null if empty
     * -> Testing the calendar should return true/false on success/fail & use session->setStatus to pass message
     */
    public function getCalendars();
    public function testCalendar();

    /**
     * Altering the calendar can be done in three ways;
     * - Adding a new appointment if a new event is planned
     * - Updating the appointment if details changed (e.g. Title/description/location etc)
     * - Remove an existing appointment if an event is cancelled
     * -> Altering the calendar should return true/false on success/fail & use session->setStatus to pass message
     * Retrieving the appointments are always to be done for the selected calendar
     * - Returned appointments are in the date range [-1mo -1day -> VGDB.lastestPlannedActivity]
     * -> Getting the appointments should return an array of appointments or null if empty & use session->setStatus to pass message
     */
    public function addAppointment();
    public function updateAppointment();
    public function removeAppointment();
    public function getAppointment();
}
