<?php

Namespace Model;

class Database
{
    protected $db;
    private $query_ok;
    private $status;
    private $numrows;

    /**
     * Pass the database parameters array to the constructor
     * @param array contains host, username, password, database
     */
    function __construct()
    {
        $numrows = 0;
        $db = getConfig();
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
    }

    function __destruct() {
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
}