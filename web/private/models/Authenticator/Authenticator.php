<?php

class Authenticator
{
    //Session login statusses
    private const LOGIN_LOGGEDIN = 1;
    private const LOGIN_INVALID_CREDENTIALS = self::LOGIN_LOGGEDIN + 1;

    private $usermodel = '';
    private $crypt = '';
    private $session = '';
    
    function __construct() {
        $this->session = new Model\Session;
        $this->user = new Model\Users;
        $this->crypt = new Crypt;
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
            $_SESSION['loginstatus'] = self::LOGIN_INVALID_CREDENTIALS;
            echo 'nok';
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
            }
        }
    }

    public function userIsAdmin() {
        return $_SESSION['user_role'] =='admin';
    }

    /**
     * Logout the current user
     */
    function logoutUser()
    {
        //Unset all login related session parameters
        unset($_SESSION['loginstatus']);
        unset($_SESSION['loggedin_user']);
    }

    
    private function loginSessionParameterIsSet()
    {
        //Return the status of the session variable
        return (isset($_SESSION['loginstatus']) && !empty($_SESSION['loginstatus']));
    }
}