<?php

Use Controller\Users;
Use Controller\Session;

class Settings {
    private $user;
    private $session;
    private $crypt;

    public function __construct() {
        $this->user = new Users;
        $this->session = new Session;
        $this->crypt = new Crypt;
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
        } else {
            $this->session->setStatus('virtuagym','Warning','Error while updating credentials: ' . $status);
        }
    }

    public function getVirtuagymUsername() {
        return $this->crypt->getDecryptedMessage($this->user->getVirtuagymUsernameEnc())
    }

    public function getVirtuagymPassword() {
        return $this->crypt->getDecryptedMessage($this->user->getVirtuagymPasswordEnc())
    }

    public function getVirtuagymMessage() {
        return $this->session->getAndClearStatus('virtuagym');
    }
    
}