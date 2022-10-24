<?php

use Model\Users;
use Model\Session;
use Crypt;

class VirtuaGym {
    private $user;
    private $username;
    private $password;
    private $apiKey;
    private $data;
    
    private const API_URL = 'https://api.virtuagym.com/api/v0';

    public function __construct(){
        $this->user = new Users;
        $conf = getConfig();
        $this->apiKey = $conf['virtuagym_api_key'];
        $this->username = Crypt::getDecryptedMessage($this->user->getVirtuagymUsernameEnc());
        $this->pasword = Crypt::getDecryptedMessage($this->user->getVirtuagymPasswordEnc());
    }

    public function testConnection() {
        $url = $this->apiUrl . '/'
    }

    private function getServerData($path) {
        $url = self::API_URL . '/' . $path . '?api_key=' . $this->apiKey;
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo('Exit with message: ' . $e->getMessage());
        }
        return $result;
    }
}