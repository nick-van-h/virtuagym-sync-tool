<?php

namespace Vst\Model;

use Vst\Controller\User;
use Vst\Controller\Session;
use Vst\Controller\Log;

class Settings {
    private $user;
    private $session;
    private $crypt;
    private $log;

    public function __construct() {
        $this->user = new Users;
        $this->session = new Session;
        $this->crypt = new Crypt;
        $this->log = new Log;
    }

    public function updateVirtuagymCredentials($username, $password) {
        $success = true;
        $status = '';

        //Update user
        $username_enc = $this->crypt->getEncryptedMessage($username);
        $this->user->setVirtuagymUsernameEnc($username_enc);
        $success &= $this->user->getQueryOk();
        $status = $this->user->getStatus();

        //update password
        $password_enc = $this->crypt->getEncryptedMessage($password);
        $this->user->setVirtuagymPasswordEnc($password_enc);
        $success &= $this->user->getQueryOk();
        $status &= $this->user->getStatus();

        //Resolve status
        if ($success) {
            $this->session->setStatus('virtuagym','Success','Credentials updated succesfully');
            $this->log->addEvent('Settings','Updated VirtuaGym credentials');
        } else {
            $this->session->setStatus('virtuagym','Warning','Error while updating credentials: ' . $status);
            $this->log->addEvent('Settings','Updating VirtuaGym failed with status: ' . $status);
        }
    }

    public function getVirtuagymUsername() {
        return $this->crypt->getDecryptedMessage($this->user->getVirtuagymUsernameEnc());
    }

    public function getVirtuagymPassword() {
        return $this->crypt->getDecryptedMessage($this->user->getVirtuagymPasswordEnc());
    }

    public function getCalendarProvider($provider) {

    }

    public function setCalendarProvider($provider, $credentials) {

    }

    public function getTargetAgenda() {

    }

    public function setTargetAgenda() {

    }

    public function getVirtuagymMessage() {
        return $this->session->getAndClearStatus('virtuagym');
    }
    
}