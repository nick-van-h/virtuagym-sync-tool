<?php

//Start the session if it is not yet started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * App-wide constants
 */
const BASE_PATH = __DIR__ . '/..';

//Changelog
const CHANGELOG_FILE = BASE_PATH . '/config/changelog.json';
const CHANGELOG_VERSION_UPCOMING = 'Upcoming';

//Providers must match Vst\ControllerCalendar\ class name
const PROVIDER_GOOGLE = 'Google';

const ORDER_ASC = 'ASC';
const ORDER_DESC = 'DESC';
