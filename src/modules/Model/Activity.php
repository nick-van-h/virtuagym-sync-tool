<?php

namespace Vst\Interface;

class Activity {
    private $act_inst_id;
    private $done;
    private $deleted;
    private $act_id;
    private $event_id;
    private $timestamp;
    private $activity_id;
    private $name;
    private $actdef_deleted;
    private $club_id;
    private $duration;
    private $event_start;
    private $event_end;
    private $attendees;
    private $max_attendees;
    private $joined;
    private $evtdef_deleted;
    private $cancelled;
    private $bookable_from;
    private $appointment_id;
    private $club_name;
    private $full_address;
    private $street;
    private $zip_code;
    private $city;
    private $club_description;

    private const REQUIRED_PARAMETERS = array('summary','location','start','end','id','appointment_id');

    public function __construct() {
        $arguments = func_get_args();
        $this->setParameters($arguments);
    }

    public function setParameters() {
        $arguments = func_get_args();

        if(!empty($arguments)) {
            foreach($arguments[0] as $key => $value) {
                if(property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function getParameters() {
        $arguments = func_get_args();

        $ret = [];

        if(!empty($arguments)) {
            foreach($arguments[0] as $key => $value) {
                if(property_exists($this, $key)) {
                    $ret[$key] = $value;
                }
            }
        }
        return $ret;
    }
}