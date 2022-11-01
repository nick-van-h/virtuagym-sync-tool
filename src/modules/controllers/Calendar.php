<?php

namespace Vst\Controller;

class Calendar {
    private $calendar;
    private $initOk;

    public function __construct($provider, $credentials) {

        //Init calendar controller based on user setting
        switch ($provider) {
            case 'Google':
                $this->calendar = new Calendar\Google($credentials);
                break;
            default:
                $this->calendar = NULL;
        }

        //Test the connection
        if(!empty($this->calendar)) {
            $this->initOk = $this->calendar->testConnection();
        } else {
            $this->initOk = false;
        }
    }
}