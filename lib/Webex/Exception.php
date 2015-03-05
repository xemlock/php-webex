<?php

class Webex_Exception extends Exception
{
    protected $_exceptionId;

    public function __construct($exceptionId, $message)
    {
        parent::__construct($message);
        $this->_exceptionId = $exceptionId;
    }

    public function getExceptionId()
    {
        return $this->_exceptionId;
    }
}
