<?php

/**
 * This class provides easy (un)setter, getter, isSetters & tester interfaces to the $_SESSION
 * Each block contains at least a setXXX and getXXX
 * Setters require one @param string $value
 * Unsetters require no paramers
 * Getters provide @return mixed $value
 * isSetters evaluate whether or not the variable is set and @return bool $result true/false
 * Testers validate a value to a passed argument and @return bool $result true/false
 */

namespace Vst\Model;

class Session
{

    public function __construct()
    {
        //Start the session if it is not yet started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Generic unset (logout) user
     */
    public function unsetUser()
    {
        $this->unsetUsername();
        $this->unsetUserID();
        $this->unsetUserRole();
        $this->unsetKey();
    }

    /**
     * Username
     */
    public function setUsername($username)
    {
        $this->set('username', $username);
    }
    public function unsetUsername()
    {
        $this->unset('username');
    }
    public function getUsername()
    {
        return $this->get('username');
    }

    /**
     * User ID
     */
    public function setUserID($userid)
    {
        $this->set('loggedin_userid', $userid);
    }
    public function unsetUserID()
    {
        $this->unset('loggedin_userid');
    }
    public function getUserID()
    {
        return $this->get('loggedin_userid');
    }

    /**
     * LoginStatus
     */
    public function setLoginStatus($status)
    {
        $this->set('loginstatus', $status);
    }
    public function unsetLoginStatus()
    {
        $this->unset('loginstatus');
    }
    public function getLoginStatus()
    {
        return $this->get('loginstatus');
    }
    public function testLoginStatus($test)
    {
        return $this->get('loginstatus') == $test;
    }

    /**
     * User role
     */
    public function setUserRole($status)
    {
        $this->set('user_role', $status);
    }
    public function unsetUserRole()
    {
        $this->unset('user_role');
    }
    public function getUserRole()
    {
        return $this->get('user_role');
    }
    public function isUserRoleSet()
    {
        $this->isVarSet('user_role');
    }
    public function testUserRole($test)
    {
        return $this->get('user_role') == $test;
    }

    /**
     * Encryption key
     */
    public function setKey($key)
    {
        $this->set('key', $key);
    }
    public function unsetKey()
    {
        $this->unset('key');
    }
    public function getKey()
    {
        return $this->get('key');
    }
    public function isKeySet()
    {
        $this->isVarSet('key');
    }

    /**
     * Log linking
     */
    public function setLogRefId($id)
    {
        $this->set('log_ref_id', $id);
    }
    public function getLogRefId()
    {
        $refId = $this->get('log_ref_id');
        /**
         * If no ref ID is set (NULL) then get() returns false
         * However we want to use the actual NULL value instead of false
         */
        if ($refId) {
            return $refId;
        } else {
            return NULL;
        }
    }

    /**
     * Redirect URL
     */
    public function setRedirectUrl($url)
    {
        $this->set('redirect_url', $url);
    }
    public function getRedirectUrl()
    {
        return $this->get('redirect_url');
    }

    /**
     * Status
     */
    public function setStatus($status, $code, $value)
    {
        if (!$this->isVarSet('status')) {
            $_SESSION['status'] = [];
        }
        if (!$this->isArrVarSet('status', $status)) {
            $_SESSION['status'][$status] = [];
        }
        $_SESSION['status'][$status]['code'] = $code;
        $_SESSION['status'][$status]['message'] = $value;
    }
    public function clearStatus($status)
    {
        $this->setStatus($status, '', '');
    }
    public function getStatus($status)
    {
        if ($this->isArrVarSet('status', $status)) {
            return $_SESSION['status'][$status]['message'];
        } else {
            return false;
        }
    }
    public function getStatusCode($status)
    {
        if ($this->isArrVarSet('status', $status)) {
            return $_SESSION['status'][$status]['code'];
        } else {
            return false;
        }
    }
    public function getAndClearStatus($status)
    {
        $message = $this->getStatus($status);
        $this->clearStatus($status);
        return $message;
    }

    /**
     * Debug
     */
    public function addDebug($status)
    {
        $_SESSION['debug'][] = $status;
    }

    /**
     * Private helpers
     */
    private function set($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    private function unset($variable)
    {
        unset($_SESSION[$variable]);
    }

    private function get($variable)
    {
        if ($this->isVarSet($variable)) {
            return $_SESSION[$variable];
        } else {
            return null;
        }
    }

    private function isVarSet($variable)
    {
        $result = isset($_SESSION[$variable]) && !empty($_SESSION[$variable]);
        return $result;
    }

    private function isArrVarSet($array, $variable)
    {
        $result = isset($_SESSION[$array][$variable]) && !empty($_SESSION[$array][$variable]);
        return $result;
    }
}
