<?php

namespace Vst\Model\Database;

use Vst\Model\Session;
use Vst\Model\Database\Database;
use Vst\Controller\Crypt;

/**
 * The database class is responsible for storing encrypted & returning decrypted values
 */
class Settings extends Database
{
    private $session;
    private $crypt;

    function __construct()
    {
        parent::__construct();
        $this->session = new Session;
        $this->crypt = new Crypt;
    }

    /**
     * Get the password hash for the logged in user
     * 
     * @return string password_hash
     */
    function getPasswordHash()
    {
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
    function setPasswordHash($pwhash)
    {
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
    function setRole($role)
    {
        $this->setSetting('user_role', $role);
    }
    function getRole()
    {
        return ($this->getSettingValue('user_role'));
    }

    /**
     * ID
     */
    function getUserIdFromUsername($username)
    {
        $sql = "SELECT `id`
                FROM users
                WHERE `username` = (?)";
        parent::bufferParams($username);
        parent::query($sql);
        return parent::getOne('id');
    }

    /**
     * VirtuaGym credentials
     */
    function updateVirtuagymCredentials($username, $password)
    {
        $success = true;

        //Update user
        $username_enc = $this->crypt->getEncryptedMessage($username);
        $this->settings->setVirtuagymUsernameEnc($username_enc);
        $success &= $this->settings->getQueryOk();

        //update password
        $password_enc = $this->crypt->getEncryptedMessage($password);
        $this->settings->setVirtuagymPasswordEnc($password_enc);
        $success &= $this->settings->getQueryOk();

        return $success;
    }

    public function getVirtuagymUsername()
    {
        return $this->crypt->getDecryptedMessage($this->getSettingValue('virtuagym_username_enc'));
    }

    public function getVirtuagymPassword()
    {
        return $this->crypt->getDecryptedMessage($this->getSettingValue('virtuagym_password_enc'));
    }

    /**
     * VirtuaGym username (enc)
     * TODO: Remove class & migrate to functions above
     */
    function setVirtuagymUsernameEnc($vg_username_enc)
    {
        $this->setSetting('virtuagym_username_enc', $vg_username_enc);
    }
    function getVirtuagymUsernameEnc()
    {
        return ($this->getSettingValue('virtuagym_username_enc'));
    }

    /**
     * VirtuaGym password (enc)
     */
    function setVirtuagymPasswordEnc($vg_password_enc)
    {
        $this->setSetting('virtuagym_password_enc', $vg_password_enc);
    }
    function getVirtuagymPasswordEnc()
    {
        $pw_enc = $this->getSettingValue('virtuagym_password_enc');
        return ($pw_enc);
    }

    /**
     * Last Virtuagym Sync
     */
    public function setLastSync($dt)
    {
        $this->setSetting('virtuagym_last_sync', $dt);
    }
    public function getLastSync()
    {
        $dt = $this->getSettingValue('virtuagym_last_sync');
        return $dt;
    }

    /**
     * Token expiry date
     */
    function getTokenExpiryDate()
    {
        return $this->getSettingValue('token_expiry_date');
    }

    function setTokenExpiryDate($value)
    {
        $this->setSetting('token_expiry_date', $value);
    }

    /**
     * Calendar provider
     */
    function getCalendarProvider()
    {
        return $this->getSettingValue('calendar_provider');
    }

    function setCalendarProvider($value)
    {
        $this->setSetting('calendar_provider', $value);
    }

    /**
     * Calendar credentials
     */
    function getCalendarCredentials()
    {
        $cal = $this->getCalendarProvider();
        $cred = [];
        switch ($cal) {
            case PROVIDER_GOOGLE:
                $cred['access_token'] = $this->getSettingValue('google_access_token');
                $cred['refresh_token'] = $this->getSettingValue('google_refresh_token');
                $cred['calendar_account'] = $this->getSettingValue('calendar_account');
                $cred['target_agenda_name'] = $this->getSettingValue('target_agenda_name');
                $cred['target_agenda_id'] = $this->getSettingValue('target_agenda_id');
                $cred['timezone'] = $this->getSettingValue('calendar_timezone');
                break;
            default:
                break;
        }
        return $cred;
    }
    function setCalendarCredentials($cred)
    {
        $cal = $this->getCalendarProvider();
        switch ($cal) {
            case PROVIDER_GOOGLE:
                $this->setSetting('google_access_token', $cred['access_token']);
                $this->setSetting('google_refresh_token', $cred['refresh_token']);
                $this->setSetting('calendar_account', $cred['calendar_account']);
                //Target agenda, ID and timezone is set in a different function
                break;
            default:
                break;
        }
    }

    /**
     * Target agenda
     */
    function getTargetAgendaName()
    {
        return $this->getSettingValue('target_agenda_name');
    }
    function getTargetAgendaId()
    {
        return $this->getSettingValue('target_agenda_id');
    }

    function setTargetAgenda($value)
    {
        $cal = $this->getCalendarProvider();
        switch ($cal) {
            case PROVIDER_GOOGLE:
                $split = preg_split("/[|]/", $value);
                $this->setSetting('target_agenda_id', $split[0]);
                $this->setSetting('target_agenda_name', $split[1]);
                $this->setSetting('calendar_timezone', $split[2]);
                break;
            default:
                break;
        }
    }

    /**
     * Last visited version
     */
    function getLastVisitedVersion()
    {
        return $this->getSettingValue('last_visited_version');
    }
    function setLastVisitedVersion($version)
    {
        $this->setSetting('last_visited_version', $version);
    }

    /**
     * Specific: get username from password reset token
     */
    function getUsernameFromToken($token)
    {
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
    function setToken($token)
    {
        //TODO IMPORTANT: Make sure token is unique before storing in database
        $this->setSetting('password_reset_token', $token);
    }

    /**
     * Get userid of user with earliest last sync date (i.e. who had the most time between syncs)
     */
    function getAllUserIds_orderedByLastSync()
    {
        $sql = "SELECT `user_id`, `setting_name`
                FROM settings
                WHERE `setting_name` = 'virtuagym_last_sync'
                ORDER BY `value_str` ASC";
        parent::query($sql);
        return (parent::getRows('user_id'));
    }

    /**
     * Generic private functions for getting & setting settings
     */
    private function getSettingValue($setting_name)
    {
        $userid = $this->session->getUserID();
        $sql = "SELECT `value_str`, `value_int`, `type`
                FROM settings
                WHERE `user_id` = (?) AND `setting_name` = (?)";
        parent::bufferParams($userid, $setting_name);
        parent::query($sql);
        if (parent::getOneNumrows()) {
            $row = parent::getOne();
            if ($row['type'] == 'str') {
                return ($row['value_str']);
            } else {
                return ($row['value_int']);
            }
        } else {
            return NULL;
        }
    }

    /**
     * Add a user setting to the table
     */
    private function setSetting($setting_name, $setting_value)
    {
        //Prepare variables to be insterted
        $userid = $this->session->getUserID();
        $type = '';
        $sql = '';
        if (!is_int($setting_value)) {
            $value_str = $setting_value;
            $value_int = '{i}NULL';
            $type = 'str';
        } else {
            $value_str = NULL;
            $value_int = $setting_value;
            $type = 'int';
        }

        //Check if the setting already exists for the user, if so we need to update, else we need to add
        $this->getSettingValue($setting_name);
        if (parent::getOneNumrows()) {
            //if(!is_null($this->getSettingValue($setting_name))) {
            $sql = "UPDATE settings
                    SET value_str=(?), value_int=(?), type=(?)
                    WHERE setting_name=(?) AND user_id = (?)";
            parent::bufferParams($value_str, $value_int, $type, $setting_name, $userid);
        } else {
            $sql = "INSERT INTO settings (`user_id`, `setting_name`, `value_str`, `value_int`, `type`)
                    VALUES (?,?,?,?,?)";
            parent::bufferParams($userid, $setting_name, $value_str, $value_int, $type);
        }
        parent::query($sql);
        return parent::getQueryOk();
    }
}
