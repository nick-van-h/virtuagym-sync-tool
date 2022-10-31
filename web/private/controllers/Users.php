<?php

Namespace Controller;

Use Controller\Session;
Use Controller\Database;

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
        $userid = $this->session->getUserID();
        $sql = "SELECT `password_hash` 
                FROM users 
                WHERE `id` = (?)";
        parent::bufferParams($userid);
        parent::query($sql);
        return parent::getOne('password_hash');
    }

    /**
     * Update the password hash for the logged in user
     */
    function setPasswordHash($pwhash) {
        $userid = $this->session->getUserID();
        $sql = "UPDATE users 
                SET `password_hash`=(?)
                WHERE `id` = (?)";
        parent::bufferParams($pwhash, $userid);
        parent::query($sql);
        return parent::getQueryOk();
    }

    /**
     * User role
     */
    function setRole($role) {
        $this->setSetting('user_role', $role);
    }
    function getRole() {
        return($this->getSettingValue('user_role'));
    }

    /**
     * ID
     */
    function getID() {
        $username = $this->session->getUsername();
        $sql = "SELECT `id`
                FROM users
                WHERE `username` = (?)";
        parent::bufferParams($username);
        parent::query($sql);
        return parent::getOne('id');
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

    /**
     * Last Virtuagym Sync
     */
    public function setLastSync($dt) {
        $this->setSetting('virtuagym_last_sync', $dt);
    }
    public function getLastSync($dt) {
        $$dt = $this->getSetting('virtuagym_last_sync');
        return $dt;
    }

    /**
     * Token expiry date
     */
    function getTokenExpiryDate() {
        return $this->getSettingValue('token_expiry_date');
    }

    function setTokenExpiryDate($value) {
        $this->setSetting('token_expiry_date', $value);
    }

    /**
     * Calendar provider
     */
    function getCalendarProvider() {
        return $this->getSettingValue('calendar_provider');
    }

    function setCalendarProvider($value) {
        $this->setSetting('calendar_provider', $value);
    }

    /**
     * Calendar credentials
     */
    function getCalendarCredentials() {
        //TODO: Implement
        return false;
    }

    /**
     * Target agenda
     */
    function getTargetAgenda() {
        return $this->getSettingValue('target_agenda');
    }

    function setTargetAgenda($value) {
        $this->setSetting('target_agenda', $value);
    }

    /**
     * Specific: get username from password reset token
     */
    function getUsernameFromToken($token) {
        $sql = "SELECT `username`
                FROM users u
                RIGHT OUTER JOIN (
                    SELECT `user_id`
                    FROM settings
                    WHERE `setting_name` = 'password_reset_token' AND `value_str` = (?)
                ) s ON s.user_id = u.id";
        parent::bufferParams($token);
        parent::query($sql);
        return (parent::getOne('username'));
    }

    /**
     * Generic private functions for getting & setting settings
     */
    private function getSettingValue($setting_name) {
        $userid = $this->session->getUserID();
        $sql = "SELECT `value_str`, `value_int`, `type`
                FROM settings
                WHERE `user_id` = (?) AND `setting_name` = (?)";
        parent::bufferParams($userid,$setting_name);
        parent::query($sql);
        if(parent::getOneNumrows()) {
            $row = parent::getOne();
            if($row['type'] == 'str') {
                return($row['value_str']);
            } else {
                return($row['value_int']);
            }
        } else {
            return false;
        }
    }

    /**
     * Add a user setting to the table
     */
    private function setSetting($setting_name, $setting_value) {
        //Prepare variables to be insterted
        $userid = $this->session->getUserID();
        $type = '';
        $sql = '';
        if(!is_int($setting_value)) {
            $value_str = $setting_value;
            $value_int = '{i}NULL';
            $type = 'str';
        } else {
            $value_str = NULL;
            $value_int = $setting_value;
            $type = 'int';
        }

        //Check if the setting already exists for the user, if so we need to update, else we need to add
        if($this->getSettingValue($setting_name)) {
            $sql = "UPDATE settings
                    SET value_str=(?), value_int=(?), type=(?)
                    WHERE setting_name=(?) AND user_id = (?)";
            parent::bufferParams($value_str, $value_int, $type, $setting_name, $userid);
        } else {
            $sql = "INSERT INTO settings (`user_id`, `setting_name`, `value_str`, `value_int`, `type`)
                    VALUES (?),(?),(?),(?),(?)";
            parent::bufferParams($userid, $setting_name, $value_str, $value_int, $type);
        }
        parent::query($sql);
        return parent::getQueryOk();
    }
}