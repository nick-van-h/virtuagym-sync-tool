<?php

/**
 * Returns the full path to the database .ini file
 * 
 * @return string
 */
function getDbConfigFile()
{
    $db_conf = DB_CONFIG_FILE;

    if (file_exists($db_conf)) {
        return $db_conf;
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
