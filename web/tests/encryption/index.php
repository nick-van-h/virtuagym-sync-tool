<?php
//Default include autoload
require_once __DIR__ . '/../../private/config/autoload.php';
$start = new DateTime();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Check if the user is logged in
$auth = new Authenticator;
if (!$auth->userIsLoggedIn() && !$auth->userIsAdmin()) {
    redirectToUrl(public_base_url());
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VST Test - Encryption');
get_vw_head_resources();
get_vw_head_end();

//Site content
get_vw_nav();
echo('<main class="test box-s">');

/**
 * Start main content
 */
echo('<h1>Encrypt/decrypt</h1>');

// Storing a string into the variable which 
// needs to be Encrypted 
$simple_string = "Virtuagym_username";
$simple_password = "Password123!";

// Displaying the original string 
echo "--- input ---"; br();
echo "Original String: " . $simple_string; br();

br(); echo "--- ciphering ---"; br();

// Storing cipher method 
$ciphering = "AES-256-CFB";

// Using OpenSSl encryption method 
$options   = 0;

// Using random_bytes() function which gives 
// randomly 16 digit values 
$conf = getConfig();
$encryption_iv = $conf['encryption_iv'];
$iv_length = openssl_cipher_iv_length($ciphering);
$iv_random = random_bytes($iv_length);
echo "iv random: " . $iv_random . ' - to be appended to password before scramble'; br();

// Alternatively, any 16 digits may be used 
// characters or numeric for iv 
$encryption_key = HASH("SHA256",openssl_digest($simple_password, 'SHA256', TRUE));
$encryption_key = openssl_digest($simple_password, 'SHA256', TRUE);
echo "Encryption key: " . $encryption_key . ' - to be stored in $_SESSION[]'; br();

// Encryption of string process begins
$encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);

// Display the encrypted string 
echo "Encrypted String: " . $encryption . ' - to be stored in database'; br();

$key_encrypted = openssl_encrypt($encryption_key, $ciphering, $conf['encryption_iv'], $options, $encryption_iv);
echo "Encrypted key: " . $key_encrypted . ' - to be stored in database'; br();


// Store the decryption key 
$decryption_key = openssl_digest($simple_password, 'SHA256', TRUE);
echo "Decryption key: " . $decryption_key; br();

// Decrypting the string 
$decryption = openssl_decrypt($encryption, $ciphering, $decryption_key, $options, $encryption_iv);

// Showing the decrypted string 
br(); echo "--- output ---"; br();
echo "Decrypted String: " . $decryption; br();

// Use case: User changes password
br(); echo "--- pw change ---"; br();
$simple_password = "Password456#";
// Store the updated decryption key 
$decryption_key = openssl_digest($simple_password, 'SHA256', TRUE);
echo "New decryption key: " . $decryption_key; br();

// Decrypting the string
$decryption = openssl_decrypt($encryption, $ciphering, $decryption_key, $options, $encryption_iv);


//Show the available cipther methods
echo('<h1>Available cipher methods</h1>');
echo('AES128/SHA256 or CFB is preferred');
echo_pre(openssl_get_cipher_methods());

echo('<h1>Available md methods</h1>');
echo_pre(openssl_get_md_methods());

/**
 * Summary
 */
$end = new DateTime();
$diff = date_diff($end, $start);
echo('<h1>Summary</h1>');
echo('Total runtime: ' . $diff->format('%H:%I:%S') . ' (h:m:s)');
br();br();
echo('--- end ---');

//Foot
echo("</main>");
get_vw_foot();
