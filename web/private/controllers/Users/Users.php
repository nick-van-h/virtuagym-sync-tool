<?php

Namespace Model;

use Model\Database;

class Users extends Database {
    /**
     * Get the password hash for a given user
     * 
     * @param string $username
     * @return string password_hash
     */
    function getPasswordHash($username) {
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

    function setPassword($username,$pwhash) {
        $sql = "UPDATE users 
                SET password_hash=(?)
                WHERE username = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss",$pwhash,$username);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    function getUserRole($username) {
        return($this->getSettingValue($username,'user_role'));
    }

    function getSettingValue($username,$setting_name) {
        $sql = "SELECT `value_str`, `value_int`, `type`
        FROM settings s
        LEFT JOIN (
            SELECT id
            FROM users
            WHERE username = (?)
        ) u on u.id = s.user_id
        WHERE variable = (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss",$username,$setting_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
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
    function setSetting($username, $setting_name, $setting_value) {
        //Set the string/integer values accoring the type
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
        if($this->getSettingValue($username, $setting_name)) {
            $sql = "UPDATE settings
                    SET value_str=(?), value_int=(?), type=(?)
                    WHERE variable=(?) AND user_id = (SELECT id FROM users WHERE username = (?))";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sisss", $value_str, $value_int, $type, $setting_name, $username);
        } else {
            $sql = "INSERT INTO settings (`user_id`, `variable`, `value_str`, `value_int`, `type`)
                    SELECT id as user_id,(?) as variable,(?) as value_str,(?) as value_int,(?) as type FROM users WHERE username = (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssiss", $setting_name, $value_str, $value_int, $type, $username);
        }
        $stattus = $stmt->execute();
        
        return $stmt->affected_rows;
    }
}