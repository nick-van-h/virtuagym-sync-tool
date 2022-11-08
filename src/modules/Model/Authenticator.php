<?php

namespace Vst\Model;

use Vst\Controller\Session;
use Vst\Controller\User;
use Vst\Controller\Log;

class Authenticator
{
    //Session login statusses
    private const LOGIN_LOGGEDIN = 1;
    private const LOGIN_INVALID_CREDENTIALS = self::LOGIN_LOGGEDIN + 1;

    private $crypt;
    private $user;
    private $session;
    private $log;
    
    function __construct() {
        $this->session = new Session;
        $this->user = new User;
        $this->crypt = new Crypt;
        $this->log = new Log;
    }

    function createNewUser($username, $password) {
        //To be implemented
    }

    function resetPassword($password) {
        $pwhash = password_hash($password, PASSWORD_DEFAULT);
        if ($this->user->setPasswordHash($pwhash)) {
            $this->session->setStatus('login-status','Success','New password has been set, you can now log in with your new password.');
        } else {
            $this->session->setStatus('login-status','Warning','Error during password reset, please try again.');
        }
    }
    /**
     * Try to login a user with a specific username & password
     */
    public function loginUser($username, $password) {
        //Set username & ID, get stored password hash for compare
        $this->session->setUsername($username);
        $this->session->setUserID($this->user->getID());
        $pwhash = $this->user->getPasswordHash();

        //Get user origin info
        if(!empty($_SERVER['HTTP_CIENT_IP'])) {
            $ip = $_SERVER['HTTP_CIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $client = $_SERVER['HTTP_USER_AGENT'];
        $os = explode(";",$client)[1];
        $exp = explode(" ",$client);
        $browser = end($exp);

        //Validate the user's password with the hash
        if(password_verify($password, $pwhash)) {
            //Store the status and role of the user
            $this->session->setLoginStatus(self::LOGIN_LOGGEDIN);
            $this->session->setUserRole($this->user->getRole());

            /**
             * Check if there is an encryption key, if not;
             * Generate a key
             * Get the encrypted key
             * Store the encrypted key in the database
             */
           
            $key_enc = $this->user->getKeyEnc();
            if(!$key_enc) {
                $this->crypt->generateAndSetInitialKey();
                $key_enc = $this->crypt->getEncryptedKey();

                $this->user->setKeyEnc($key_enc);
            } else {
                $this->crypt->decryptAndSetKey($key_enc);
            }

            //Log a succesful login
            $this->log->addEvent('Login','Login successful from ' . $browser . ' on ' . $os . ' @ ' . $ip);
        } else {
            $this->session->setLoginStatus(self::LOGIN_INVALID_CREDENTIALS);
            $this->session->unsetUser();

            //Log an unsuccesful login
            $this->log->addWarning('Login','Login attempt with invalid credentials from ' . $browser . ' on ' . $os . ' @ ' . $ip);
        }
    }

    public function userIsLoggedIn() {
        return ($this->session->getLoginStatus() == self::LOGIN_LOGGEDIN);
    }

    public function getLoginMessage() {
        if($this->session->getLoginStatus() == self::LOGIN_INVALID_CREDENTIALS) {
            $this->session->unsetLoginStatus();
            return ('Invalid username or password');
        } else {
            return $this->session->getStatus('login-status');
        }
    }

    public function userIsAdmin() {
        return $this->userIsLoggedIn() && $this->session->getUserRole() =='admin';
    }

    public function userIsDev() {
        return $this->userIsLoggedIn() && $this->session->getUserRole() =='dev';
    }

    public function validateToken($token) {
        $success = false;
        $this->session->setUsername($this->user->getUsernameFromToken($token));
        if($this->session->getUsername()) {
            $dt = new \DateTime;
            $exp = $this->user->getTokenExpiryDate();
            $dtexp = $exp ? new \DateTime($exp) : new \DateTime();
            if ($dt <= $dtexp) {
                $success = true;
            }
        }
        return $success;
    }

    public function revokeToken() {
        $dt = new \DateTime;
        $this->user->setTokenExpiryDate($dt->format('d-m-Y H:i:s'));
    }

    /**
     * Logout the current user
     */
    function logoutUser()
    {
        //Unset all login related session parameters including the login status
        $this->session->unsetLoginStatus();
        $this->session->unsetUser();
    }
}