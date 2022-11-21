<?php

namespace Vst\Exceptions;

class DatabaseConnectionException extends \Exception
{
    /**
     * Construct the exception with same parameters as parent
     */
    public function __construct($msg, $code = 0, \Exception $previous = null)
    {
        /**
         * Construct the parent
         */
        parent::__construct($msg, $code, $previous);
    }

    /**
     * Get the custom string object
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
