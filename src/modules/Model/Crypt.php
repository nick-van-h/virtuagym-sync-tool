<?php

namespace Vst\Model;

use Vst\Controller\Session;

/**
 * Crypt is actually a receiver class in the Command design pattern, but we don´t have a Command class (yet)
 * Crypt executes important business logic
 */
class Crypt
{
    private $encryption_iv;
    private $key;
    private const CIPHERING = "AES-256-CFB";
    private const DIGEST_ALGO = "SHA256";
    private const HASH_ALGO = "SHA256";
    private const OPTIONS = 0;

    function __construct()
    {
        $this->session = new Session;
        $conf = getConfig();
        $this->key = $conf['encryption_key'];
        $this->encryption_iv = $conf['encryption_iv'];
    }

    /**
     * Generates the key based on the openssl_digest of the user password + 16 random bytes
     * This key is to be generated only once upon account creation!
     * @param string $password the user's password
     */
    function generateAndSetInitialKey()
    {
        if (!$this->session->isKeySet()) {
            //$key = hash(SELF::HASH_ALGO,openssl_digest($password . random_bytes(16), self::DIGEST_ALGO, TRUE)); //Probably not necessary
            $key = openssl_digest(random_bytes(16), self::DIGEST_ALGO, TRUE);
            $this->session->setKey($key);
        } else {
            throw new \Exception('Trying to forge a new cipher key while one is already present');
            exit;
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
    function getEncryptedKey()
    {
        $key_enc = openssl_encrypt($this->session->getKey(), SELF::CIPHERING, $this->key, SELF::OPTIONS, $this->encryption_iv);
        return $key_enc;
    }

    /**
     * Decrypts the decryption key for further processing
     * @param string $encryptedKey expects key_enc from database
     * @param string $password expects the user password
     */
    function decryptAndSetKey($encryptedKey)
    {
        $key = openssl_decrypt($encryptedKey, SELF::CIPHERING, $this->key, SELF::OPTIONS, $this->encryption_iv);
        $this->session->setKey($key);
    }

    /**
     * Decrypts a string with the decryption key stored in the session
     * @param string $message_end the message to be decrypted
     * @return string $message the decrypted message
     */
    function getDecryptedMessage($message_enc)
    {
        $message = openssl_decrypt($message_enc, SELF::CIPHERING, $this->key, SELF::OPTIONS, $this->encryption_iv);
        return $message;
    }

    function getEncryptedMessage($message)
    {
        $message_enc = openssl_encrypt($message, SELF::CIPHERING, $this->key, SELF::OPTIONS, $this->encryption_iv);
        return $message_enc;
    }

    /**
     * Generates a semi-cryptographically-secure random ID according GUIDv4 format
     * 
     * @param  int $length
     * @return string
     */
    function guidv4()
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
