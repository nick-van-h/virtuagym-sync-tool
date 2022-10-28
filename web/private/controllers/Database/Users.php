<?php

Namespace Model;

use Model\Session;
use Model\Database;

class Users extends Database {
    private $session;

    function __construct() {
        parent::__construct();
        $this->session = new Session;
    }

    /**
     * Get the password hash for the logged in user
     * 
     * @return string password_hash
     */
    function getPasswordHash() {
        $username = $this->session->getUsername();
        $sql = "SELECT password_hash 
                FROM users 
                WHERE username = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return($row['password_hash']);
        } else {
            return(false);
        }
    }

    /**
     * Update the password hash for the logged in user
     */
    function setPasswordHash($pwhash) {
        $username = $this->session->getUsername();
        $sql = "UPDATE users 
                SET password_hash=(?)
                WHERE username = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss",$pwhash,$username);
        $stmt->execute();
        echo ($stmt->affected_rows . ' rows updated');
        return $stmt->affected_rows;
    }

    /**
     * User role
     */
    function setUserRole($role) {
        $this->setSetting('user_role', $role);
    }
    function getUserRole() {
        return($this->getSettingValue('user_role'));
    }

    /**
     * ID
     */
    function getID() {
        $username = $this->session->getUsername();
        $sql = "SELECT `id`
        FROM users
        WHERE username = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->numrows = $result->num_rows;
        if ($this->numrows > 0) {
            $row = $result->fetch_assoc();
            return($row['id']);
        } else {
            return(false);
        }
    }

    /**
     * Key (encrypted)
     */
    function setKeyEnc($key_enc) {
        $this->setSetting('key_enc', $key_enc);
    }
    function getKeyEnc() {
        $result = $this->getSettingValue('key_enc');
        return($result);
    }

    /**
     * VirtuaGym username (enc)
     */
    function setVirtuagymUsernameEnc($vg_username_enc) {
        $this->setSetting('virtuagym_username_enc', $vg_username_enc);
    }
    function getVirtuagymUsernameEnc() {
        return($this->getSettingValue('virtuagym_username_enc'));
    }

    /**
     * VirtuaGym password (enc)
     */
    function setVirtuagymPasswordEnc($vg_password_enc) {
        $this->setSetting('virtuagym_password_enc', $vg_password_enc);
    }
    function getVirtuagymPasswordEnc() {
        $pw_enc = $this->getSettingValue('virtuagym_password_enc');
        return($pw_enc);
    }

    function getUsernameFromToken($token) {
        $sql = "SELECT `username`
        FROM users u
        RIGHT OUTER JOIN (
            SELECT `user_id`
            FROM settings
            WHERE `setting_name` = 'password_reset_token' AND `value_str` = (?)
        ) s ON s.user_id = u.id";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$token);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->numrows = $result->num_rows;
        if ($this->numrows > 0) {
            $row = $result->fetch_assoc();
            return($row['username']);
        } else {
            echo ('no result from query');
            return(false);
        }
    }

    function getTokenExpiryDate() {
        return $this->getSettingValue('token_expiry_date');
    }

    /**
     * Generic private functions for getting & setting settings
     */
    private function getSettingValue($setting_name) {
        $username = $this->session->getUsername();
        $sql = "SELECT `value_str`, `value_int`, `type`
        FROM settings s
        LEFT JOIN (
            SELECT id
            FROM users
            WHERE username = (?)
        ) u on u.id = s.user_id
        WHERE setting_name = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss",$username,$setting_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->numrows = $result->num_rows;
        if ($this->numrows > 0) {
            $row = $result->fetch_assoc();
            if($row['type'] == 'str') {

                return($row['value_str']);
            } else {
                return($row['value_int']);
            }
        } else {
            return(false);
        }
    }

    /**
     * Add a user setting to the table
     */
    private function setSetting($setting_name, $setting_value) {
        //Prepare variables to be insterted
        $username = $this->session->getUsername();
        $type = '';
        if(!is_int($setting_value)) {
            $value_str = $setting_value;
            $value_int = NULL;
            $type = 'str';
        } else {
            $value_str = NULL;
            $value_int = $setting_value;
            $type = 'int';
        }

        //Check if the setting already exists for the user, if so we need to update, else we need to add
        $this->getSettingValue($setting_name);
        if($this->numrows) {
            $sql = "UPDATE settings
                    SET value_str=(?), value_int=(?), type=(?)
                    WHERE setting_name=(?) AND user_id = (SELECT id FROM users WHERE username = (?))";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sisss", $value_str, $value_int, $type, $setting_name, $username);
        } else {
            $sql = "INSERT INTO settings (`user_id`, `setting_name`, `value_str`, `value_int`, `type`)
                    SELECT id as user_id,(?) as setting_name,(?) as value_str,(?) as value_int,(?) as type FROM users WHERE username = (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssiss", $setting_name, $value_str, $value_int, $type, $username);
        }
        $stattus = $stmt->execute();
        
        return $stmt->affected_rows;
    }
}