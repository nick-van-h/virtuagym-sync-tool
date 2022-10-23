<?php

class Crypt 
{
    private $encryption_iv;
    private const CIPHERING = "AES-256-CFB";
    private const DIGEST_ALGO = "SHA256";
    private const OPTIONS = 0;

    function __construct() {
        $this->session = new Model\Session;
        $conf = getConfig();
        $this->encryption_iv = $conf['encryption_iv'];
    }

    /**
     * Generates the key based on the openssl_digest of the user password + 16 random bytes
     * This key is to be generated only once upon account creation!
     * @param string $password the user's password
     */
    function generateInitialKey($password) {
        if(!keyIsSet()) {
            $key = openssl_digest($password . random_bytes(16), self::DIGEST_ALGO, TRUE);
            $this->session->setKey($key);
        } else {
            throw new Exception('Trying to forge a new cipher key while one is already present');
        }
    }
    
    /**
     * Generates the encrypted key to be stored in the database under key_enc
     * When the user updates/resets the password, following steps are to be taken;
     * - Decrypt key with old password
     * - Update password
     * - Encrypt key with new password
     * - Store new encrypted key in database
     * @param string $password
     * @return string $key_enc
     */
    function getEncryptedKey($password) {
        $key_enc = openssl_encrypt($this->getKey(), SELF::CIPHERING, $password, SELF::OPTIONS, $this->encryption_iv);
        return $key_enc;
    }

    /**
     * Decrypts the decryption key for further processing
     * @param string $encryptedKey expects key_enc from database
     * @param string $password expects the user password
     */
    function decryptKey($encryptedKey, $password) {
        $key = openssl_decrypt($encryptedKey, SELF::CIPHERING, $password, SELF::OPTIONS, $this->encryption_iv);
        $_SESSION['key'] = $key;
    }

    /**
     * Decrypts a string with the decryption key stored in the session
     * @param string $message_end the message to be decrypted
     * @return string $message the decrypted message
     */
    function getDecryptedMessage($message_enc) {
        $message = openssl_decrypt($message_enc, SELF::CIPHERING, $this->getKey(), SELF::OPTIONS, $this->encryption_iv);
    }

    private function getKey() {
        if(keyIsSet()) {
            return $_SESSION['key'];
        } else {
            throw new Exception('Unable to get key because it is not set in the SESSION');
            exit;
        }
    }

    private function keyIsSet() {
        return isset($_SESSION['key']) && !empty($_SESSION['key']);
    }

}