<?php

namespace Vst\Model\Database;

/**
 * Use Template Method to define basic Database behavior
 */
abstract class Database
{
    protected $db;
    private $stmt;

    //Status parameters
    private $query_ok;
    private $status;

    //Input variables
    private $paramsArr;
    private $paramTypes;

    //Output variables
    private $numrows;
    private $rows;


    function __construct()
    {
        $this->rows = [];
        $this->numrows = [];
        $this->paramsArr = [];
        $this->paramTypes = [];
        $db = getConfig();
        if ($db) {
            try {
                //Try Connect to the DB with mysqli_connect function - Params {hostname, userid, password, dbname}
                //$this->db = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
                $this->db = new \mysqli($db['host'], $db['username'], $db['password'], $db['database']);
            } catch (\Exception $e) {
                //Store the exception details as error
                $this->setError("MySQLi Error Code: " . $e->getCode() . " | Exception Msg: " . $e->getMessage());
                exit;
            }
            //Turn off excessive error reporting
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            //Set status succesful
            $this->setOk();
        } else {
            $this->setError('Unable to get config file');
        }
    }

    function __destruct()
    {
        mysqli_close($this->db);
    }

    /**
     * Retrieves the last error message if any
     * @return string error_message
     */
    function getStatus()
    {
        return $this->status;
    }

    /**
     * Retrieves the status succesful of last query
     * @return bool query_ok
     */
    function getQueryOk()
    {
        return $this->query_ok;
    }

    /**
     * Add the bindparams to this class's params array
     * Then calculate the types (i.e. 'ssii') for all bindparams in the array
     */
    function bufferParams(...$bindParams)
    {
        //If new parameters are set then the query result is not representative, give a reset
        $this->resetResult();

        /**
         * If the parameter is not an array then probably the query only expects one parameter
         * If so, cast that variable into an array
         * If not, just use the child array
         * Add that array to this class's params array
         */
        if (!is_array($bindParams)) {
            $param = array($bindParams);
        } else {
            $param = $bindParams;
        }


        /**
         * Get the paramer types for each member of the bind_param
         * Add that to this class's paramtypes array
         */
        $paramTypes = '';
        foreach ($param as $key => $val) {
            if (isset($val)) {
                if (substr($val, 0, 3) == "{i}") {
                    $paramTypes .= 'i';
                } else {
                    $paramTypes .= $this->determineType($val);
                }
            } else {
                $paramTypes .= 's';
            }
        }
        $this->paramTypes[] = $paramTypes;


        foreach ($param as $key => $val) {
            //Force integer if prefix is {i}
            $ss = '';
            if (isset($val)) {
                if (substr($val, 0, 3) == "{i}") {
                    $ss = substr($val, 3);
                } else {
                    $ss = $val;
                }
            } else {
                $ss = $val;
            }
            //Cast NULL if string 'NULL' is passed
            $param[$key] = ($ss == 'NULL' ? NULL : $ss);
        }
        $this->paramsArr[] = $param;
    }

    /**
     * Execute a given query
     */
    function query($query)
    {
        //The query is not yet executed so result is not representative, give a reset
        $this->resetResult();

        //Prepare the statement
        $query = str_replace(PHP_EOL, '', $query);
        $query = preg_replace('/\s+/', ' ', $query);

        //Prepare the statement
        //$this->stmt = $this->db->prepare($query); //original
        $this->stmt = $this->db->stmt_init();
        $this->stmt->prepare($query);

        if (!empty($this->paramsArr)) {
            //Loop through the array of params to execute the query
            foreach ($this->paramsArr as $key => $params) {
                $this->queryOne($query, $this->paramTypes[$key], $params);
            }
        } else {
            $this->queryOne($query);
        }


        /**
         * Set the resulting status to OK
         * Reset the stmt
         * Then reset the input bind param arrays
         */
        $this->setOk();
        $this->stmt->reset();
        $this->paramsArr = [];
        $this->paramTypes = [];
    }

    /**
     * Execute one single query and append the results to this class's array
     */
    private function queryOne($query, $types = null, $params = null)
    {
        //Catch a query with paramaters (? or :) while no parameters ar bound
        if (preg_match('/[?:]/', $query) && !(isset($params) && !empty($params))) {
            $this->rows[] = NULL;
            return;
        }

        /**
         * Bind parameters
         * Only necessary if they are set
         * Queries without parameters do not need to be bound
         * Queries with parameters but without binds have been caught in previous block already
         */
        if (isset($params) && !empty($params)) {
            //Prepare the array to be used for this loop's $stmt->bind_param()
            $arr = [];
            $arr[] = $types;
            foreach ($params as $param) {
                $arr[] = $param;
            }

            //Bind the parameters array via callback
            call_user_func_array(array($this->stmt, 'bind_param'), $this->refValues($arr));
        }

        //Execute the query & get all resulting rows
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $this->numrows[] = $this->stmt->affected_rows;

        //Check for errors
        if ($this->stmt->errno != 0) {
            $this->setError($this->stmt->errno . ' - ' . $this->stmt->error);
            return;
        }
        //Check if any rows were returned (stmt->get_result returns false if no rows returned)
        if ($result) {
            $resArr = [];
            while ($row = $result->fetch_assoc()) {
                $resArr[] = $row;
            }
            $this->rows[] = $resArr;
        } else {
            /**
             * If the query returned no results then still add an entry to the array
             * If multiple SELECT FROM queries are executed then there is a 1:1 relation from the bind_param array to the rows
             */
            $this->rows[] = NULL;
        }
    }

    /**
     * Get the full array of rows for every query
     */
    function getRowsArr()
    {
        return $this->rows;
    }

    /**
     * Get all rows from the first query
     */
    function getRows($col = null)
    {
        if ($this->getOneNumrows()) {
            if (!empty($col)) {
                $rows = [];
                foreach ($this->rows[0] as $row) {
                    $rows[] = $row[$col];
                }
                return $rows;
            } else {
                return $this->rows[0];
            }
        } else {
            return false;
        }
    }

    /**
     * Get the first row from the first query
     */
    function getOne($col = null)
    {
        if ($this->getOneNumrows()) {
            if (!empty($col)) {
                return $this->rows[0][0][$col];
            } else {
                return $this->rows[0][0];
            }
        } else {
            return false;
        }
    }

    /**
     * Get the full array of affected rows per query
     */
    function getAllNumrows()
    {
        return $this->numrows;
    }

    /**
     * Get the number of rows affected by the first query
     */
    function getOneNumrows()
    {
        if (!empty($this->numrows[0])) {
            return $this->numrows[0];
        } else {
            return false;
        }
    }

    /**
     * Sets the status to false and set error message
     * @param string errmsg
     */
    private function setError($errmsg)
    {
        $this->status = "Failed to connect to MySQL: " . $errmsg;
        $this->query_ok = false;
    }

    /**
     * Sets the status to OK and clears error message
     */
    private function setOk()
    {
        $this->status = "All good";
        $this->query_ok = true;
    }

    /**
     * Set the status to false and reset output arrays
     */
    private function resetResult()
    {
        $this->numrows = [];
        $this->rows = [];
        $this->query_ok = false;
        $this->status = "Query not yet executed";
    }

    /**
     * Set the status to false and reset the input arrays
     */
    private function setStatusInputChanged()
    {
        $this->query_ok = false;
        $this->status = "Input parameters have changed";
    }

    /**
     * Determines the type of a variable
     * To be used for the bind_param types
     */
    protected function determineType($val)
    {
        switch (gettype($val)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'boolean':
            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    /**
     * Cast an array to a reference array
     * To be used for call_user_func_array on $stmt->bind_param
     */
    protected function refValues(array &$arr)
    {
        //Reference in the function arguments are required for HHVM to work
        //https://github.com/facebook/hhvm/issues/5155
        //Referenced data array is required by mysqli since PHP 5.3+
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }
}
