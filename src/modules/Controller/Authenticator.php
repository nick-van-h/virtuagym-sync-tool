<?php

namespace Vst\Controller;

use Vst\Model\Session;
use Vst\Model\Database\Settings;
use Vst\Model\Database\Log;

use Vst\View\GUI;

class Authenticator
{
    //Session login statusses
    private const LOGIN_LOGGEDIN = 1;
    private const LOGIN_INVALID_CREDENTIALS = self::LOGIN_LOGGEDIN + 1;
    private const ROLE_ADMIN = 'admin';
    private const ROLE_DEV = 'dev';

    private $settings;
    private $session;
    private $log;

    function __construct()
    {
        $this->session = new Session;
        $this->settings = new Settings;
        $this->log = new Log;
    }

    function createNewUser($username, $password)
    {
        //TODO: implement
    }

    function resetPassword($password)
    {
        $pwhash = password_hash($password, PASSWORD_DEFAULT);
        if ($this->settings->setPasswordHash($pwhash)) {
            $this->session->setStatus('login-status', 'Success', 'New password has been set, you can now log in with your new password.');
        } else {
            $this->session->setStatus('login-status', 'Warning', 'Error during password reset, please try again.');
        }
    }
    /**
     * Try to login a user with a specific username & password
     */
    public function loginUser($username, $password)
    {
        //Set username & ID, get stored password hash for compare
        $this->session->setUserID($this->settings->getUserIdFromUsername($username));
        $pwhash = $this->settings->getPasswordHash();

        //Get user ip, os & browser; to be stored in the log
        if (!empty($_SERVER['HTTP_CIENT_IP'])) {
            $ip = $_SERVER['HTTP_CIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $client = $_SERVER['HTTP_USER_AGENT'];
        $os = explode(";", $client)[1];
        $exp = explode(" ", $client);
        $browser = end($exp);

        //Validate the user's password with the hash
        if (password_verify($password, $pwhash)) {
            //Store the status and role of the user
            $this->session->setLoginStatus(self::LOGIN_LOGGEDIN);
            $this->session->setUserRole($this->settings->getRole());

            //Log a succesful login
            $this->log->addEvent('Login', 'Login successful from ' . $browser . ' on ' . $os . ' @ ' . $ip);
        } else {
            //Log an unsuccesful login
            $this->log->addWarning('Login', 'Login attempt with invalid credentials from ' . $browser . ' on ' . $os . ' @ ' . $ip);

            //Set login status failed and unset user ID
            $this->session->setLoginStatus(self::LOGIN_INVALID_CREDENTIALS);
            $this->session->unsetUser();
        }
    }

    public function userIsLoggedIn()
    {
        return ($this->session->getLoginStatus() == self::LOGIN_LOGGEDIN);
    }

    public function getLoginMessage()
    {
        if ($this->session->getLoginStatus() == self::LOGIN_INVALID_CREDENTIALS) {
            $this->session->unsetLoginStatus();
            return ('Invalid username or password');
        } else {
            return $this->session->getStatus('login-status');
        }
    }

    public function userIsAdmin()
    {
        return $this->userIsLoggedIn() && $this->session->getUserRole() == self::ROLE_ADMIN;
    }

    public function userIsDev()
    {
        return $this->userIsLoggedIn() && $this->session->getUserRole() == self::ROLE_DEV;
    }

    public function validateToken($token)
    {
        //TODO: Implement chain of command
        $success = false;

        /**
         * Get the username belonging to that token
         * Since the token will be spread via email (= username) we should not have to validate the username
         */
        $username = $this->settings->getUsernameFromToken($token);

        /**
         * Check if said username actually contains a value, if not return false
         * Set the username in the session so it can be used in a view
         * If so, check if the expiry date of the token is in the future
         * If there is no expiry date returned then assume that the token is valid anyways
         */
        if ($this->session->getUsername()) {
            $this->session->setUsername($username);
            $dt = new \DateTime;
            $exp = $this->settings->getTokenExpiryDate();
            $dtexp = $exp ? new \DateTime($exp) : new \DateTime();
            if ($dt <= $dtexp) {
                $success = true;
            }
        }
        return $success;
    }

    /**
     * Revoke a token by setting the expiry date to now
     * The next time this token will be tried to validate it will be expired already
     */
    public function revokeToken()
    {
        $dt = new \DateTime;
        $this->settings->setTokenExpiryDate($dt->format('d-m-Y H:i:s'));
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
