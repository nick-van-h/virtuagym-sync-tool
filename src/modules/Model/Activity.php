<?php

namespace Vst\Interface;

class Activity {
    private $summary;
    private $location;
    private $start;
    private $end;
    private $id;
    private $appointmentId;

    private const REQUIRED_PARAMETERS = array('summary','location','start','end','id','appointment_id');

    public function __construct(array $params[] = null) {
        //Set parameters if passed
    }

    public function setParameters(array $params[]) {

    }

    public function getRequiredParameters() {

    }
}