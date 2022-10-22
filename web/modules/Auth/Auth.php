<?php

class Auth 
{
    //Session login statusses
    private const LOGIN_LOGGEDIN = 1;
    private const LOGIN_INVALID_CREDENTIALS = self::LOGIN_LOGGEDIN + 1;

    private $db = '';
    
    function __construct() {
        $this->db = new Db(getDbConfig());
    }
    /**
     * Try to login a user with a specific username & password
     */
    public function loginUser($username, $password) {
        $pwhash = $this->db->getPasswordHash($username);
        if(password_verify($password, $pwhash)) {
            $_SESSION['loginstatus'] = self::LOGIN_LOGGEDIN;
            $_SESSION['loggedin_user'] = $username;
            $_SESSION['user_role'] = $this->db->getUserRole($username);
        } else {
            $_SESSION['loginstatus'] = self::LOGIN_INVALID_CREDENTIALS;
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