<?php
/**
 * This class provides easy (un)setter, getter & tester interfaces to the $_SESSION
 * Each block contains at least a setXXX and getXXX
 * Setters require one @param string $value
 * Unsetters require no paramers
 * Getters provide @return mixed $value
 * Testers validate a value to a passed argument and @return bool $result true/false
 */

Namespace Model;
use Exception;

class Session {

    public function __construct() {
        //Start the session if it is not yet started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Username
     */
    public function setUsername($username) {
        $this->set('loggedin_username', $username);
    }
    public function unsetUsername() {
        $this->unset('loggedin_username');
    }
    public function getUsername() {
        return $this->get('loggedin_username');
    }

    /**
     * LoginStatus
     */
    public function setLoginStatus($status) {
        $this->set('loginstatus', $status);
    }
    public function unsetLoginStatus() {
        $this->unset('loginstatus');
    }
    public function getLoginStatus() {
        return $this->get('loginstatus');
    }
    public function testLoginStatus($test) {
        return $this->get('loginstatus') == $test;
    }

    /**
     * User role
     */
    public function setUserRole($status) {
        $this->set('user_role', $status);
    }
    public function getUserRole() {
        return $this->get('user_role');
    }
    public function testUserRole($test) {
        return $this->get('user_role') == $test;
    }

    /**
     * Encryption key
     */
    public function setKey($key) {
        $this->set('key', $key);
    }
    public function unsetKey() {
        $this->unset('key');
    }
    public function getKey() {
        return $this->get('key');
    }

    /**
     * Private helpers
     */

    private function set($variable, $value) {
        $_SESSION[$variable] = $value;
    }

    private function unset($variable) {
        unset($_SESSION[$variable]);
    }

    private function get($variable) {
        if ($this->isSSet($variable)) {
            return $_SESSION[$variable];
        } else {
            throw new Exception ('Unable to access variable $_SESSION["' . $variable . '"]');
        }
    }

    private function isSSet($variable) {
        $result = isset($_SESSION[$variable]) && !empty($_SESSION[$variable]);
        return $result;
    }
}