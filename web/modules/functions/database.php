<?php

/**
 * Returns the full path to the database .ini file
 * 
 * @return string
 */
function getDbConfigFile()
{
    $db_conf = DB_CONFIG_FILE;

    $crl = curl_init(DB_CONFIG_FILE);
    curl_setopt($crl, CURLOPT_NOBODY, true);
    curl_exec($crl);

    $ret = curl_getinfo($crl, CURLINFO_HTTP_CODE);
    curl_close($crl);

    if ($ret = 200) {
        return DB_CONFIG_FILE;
    }

    return false;
}

/**
 * Returns an array containing the database config parameters
 * 
 * @return array contains host, username, password, database
 */
function getDbConfig()
{
    $dbConf = parse_ini_file(getDbConfigFile());
    return $dbConf;
}
