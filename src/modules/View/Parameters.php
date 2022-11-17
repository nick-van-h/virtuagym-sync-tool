<?php

namespace Vst\View;

use Vst\Model\Database\Settings;
use Vst\Model\Session;

class Parameters
{
    private $settings;
    private $session;

    function __construct()
    {
        $this->settings = new Settings;
        $this->session = new Session;
    }

    function getVirtuagymUsername()
    {
        return $this->settings->getVirtuagymUsername();
    }

    function getVirtuagymPassword()
    {
        return $this->settings->getVirtuagymPassword();
    }

    public function getVirtuagymMessage()
    {
        return $this->session->getAndClearStatus('virtuagym');
    }

    public function getCalendarMessage()
    {
        return $this->session->getAndClearStatus('Google-login');
    }
}
