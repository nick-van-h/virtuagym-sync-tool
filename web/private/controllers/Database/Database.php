<?php

Namespace Model;

class Database
{
    protected $db;
    private $query_ok;
    private $status;

    /**
     * Pass the database parameters array to the constructor
     * @param array contains host, username, password, database
     */
    function __construct()
    {
        $db = $this->getDbConfig();
        if($db) {
            try {
                //Try Connect to the DB with mysqli_connect function - Params {hostname, userid, password, dbname}
                $this->db = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
            } catch (mysqli_sql_exception $e) {
                //Store the exception details as error
                setError("MySQLi Error Code: " . $e->getCode() . " | Exception Msg: " . $e->getMessage());
                exit;
            }
            //Set status succesful
            $this->setOk();
        } else {
            this->setError('Unable to open config file ' . DB_CONFIG_FILE);
        }
    }

    function __destruct() {
        //Close the connection when the class is terminated
        mysqli_close($this->db);
    }

    /**
     * Retrieves the last error message if any
     * @return string error_message
     */
    function getStatus() {
        return $this->status;
    }

    /**
     * Retrieves the status succesful of last query
     * @return bool query_ok
     */
    function getQueryOk() {
        return $this->query_ok;
    }

    /**
     * Sets the error message for later processing
     * @param string errmsg
     */
    private function setError($errmsg) {
        $this->status = "Failed to connect to MySQL: " . $errmsg;
        $this->query_ok = false;
    }

    /**
     * Sets the status to OK and clears error message
     */
    private function setOk() {
        $this->status = "All good";
        $this->query_ok = true;
    }

    /**
     * Returns the full path to the database .ini file
     * 
     * @return string
     */
    private function getDbConfigFile()
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
    private function getDbConfig()
    {
        $file = $this->getDbConfigFile();
        if($file) {
            return parse_ini_file($file);
        } else {
            return false;
        }
    }
}