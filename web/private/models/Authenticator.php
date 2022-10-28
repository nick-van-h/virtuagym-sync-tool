<?php

class Authenticator
{
    //Session login statusses
    private const LOGIN_LOGGEDIN = 1;
    private const LOGIN_INVALID_CREDENTIALS = self::LOGIN_LOGGEDIN + 1;

    private $crypt;
    private $user;
    private $session;
    
    function __construct() {
        $this->session = new Controller\Session;
        $this->user = new Controller\Users;
        $this->crypt = new Crypt;
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
        $this->session->setUsername($username);
        $this->session->setUserID($this->user->getID());
        $pwhash = $this->user->getPasswordHash();
        if(password_verify($password, $pwhash)) {
            //Store the status and role of the user
            $this->session->setLoginStatus(self::LOGIN_LOGGEDIN);
            $this->session->setUserRole($this->user->getUserRole());

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
        } else {
            $this->session->setLoginStatus(self::LOGIN_INVALID_CREDENTIALS);
            $this->session->unsetUser();
        }
    }

    public function userIsLoggedIn() {
        if($this->loginSessionParameterIsSet()) {
            return ($_SESSION['loginstatus'] == self::LOGIN_LOGGEDIN);
        }
    }

    public function getLoginMessage() {
        if($this->loginSessionParameterIsSet()) {
            if($_SESSION['loginstatus'] == self::LOGIN_INVALID_CREDENTIALS) {
                unset($_SESSION['loginstatus']);
                return ('Invalid username or password');
            } else {
                return $this->session->getStatus('login-status');
            }
        }
    }

    public function userIsAdmin() {
        return $this->userIsLoggedIn() && $_SESSION['user_role'] =='admin';
    }

    public function userIsDev() {
        return $this->userIsLoggedIn() && $_SESSION['user_role'] =='dev';
    }

    public function validateToken($token) {
        $success = false;
        $this->session->setUsername($this->user->getUsernameFromToken($token));
        if($this->session->getUsername()) {
            $dt = new DateTime;
            $exp = $this->user->getTokenExpiryDate();
            $dtexp = $exp ? new DateTime($exp) : new DateTime();
            if ($dt <= $dtexp) {
                $success = true;
            }
        }
        return $success;
    }

    public function revokeToken() {
        $dt = new DateTime;
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

    
    private function loginSessionParameterIsSet()
    {
        //Return the status of the session variable
        return (isset($_SESSION['loginstatus']) && !empty($_SESSION['loginstatus']));
    }
}