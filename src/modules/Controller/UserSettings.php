<?php
//TODO: Remove after validate
// namespace Vst\Model;

// use Vst\Model\Database\Settings;
// use Vst\Model\Session;
// use Vst\Model\Database\Log;

// class UserSettings {
//     private $settings;
//     private $session;
//     private $crypt;
//     private $log;

//     public function __construct() {
//         $this->settings = new Settings;
//         $this->session = new Session;
//         $this->crypt = new Crypt;
//         $this->log = new Log;
//     }

//     public function updateVirtuagymCredentials($username, $password) {
//         $success = true;
//         $status = '';

//         //Update user
//         $username_enc = $this->crypt->getEncryptedMessage($username);
//         $this->settings->setVirtuagymUsernameEnc($username_enc);
//         $success &= $this->settings->getQueryOk();
//         if(!$success) $status = $this->settings->getStatus();

//         //update password
//         $password_enc = $this->crypt->getEncryptedMessage($password);
//         $this->settings->setVirtuagymPasswordEnc($password_enc);
//         $success &= $this->settings->getQueryOk();
//         if(!$success) $status .= $this->settings->getStatus();

//         //Resolve status
//         if ($success) {
//             $this->session->setStatus('virtuagym','Success','Credentials updated succesfully');
//             $this->log->addEvent('Settings','Updated VirtuaGym credentials');
//         } else {
//             $this->session->setStatus('virtuagym','Warning','Error while updating credentials: ' . $status);
//             $this->log->addEvent('Settings','Updating VirtuaGym failed with status: ' . $status);
//         }
//     }

//     public function getVirtuagymUsername() {
//         return $this->crypt->getDecryptedMessage($this->settings->getVirtuagymUsernameEnc());
//     }

//     public function getVirtuagymPassword() {
//         return $this->crypt->getDecryptedMessage($this->settings->getVirtuagymPasswordEnc());
//     }

//     public function getCalendarProvider($provider) {

//     }

//     public function setCalendarProvider($provider, $credentials) {

//     }

//     public function getTargetAgendaName() {
//         return $this->settings->getTargetAgendaName();
//     }

//     public function getVirtuagymMessage() {
//         return $this->session->getAndClearStatus('virtuagym');
//     }

//     public function getCalendarMessage() {
//         return $this->session->getAndClearStatus('Google-login');
//     }
    
    
// }