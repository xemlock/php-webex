<?php

class Webex_Exception_ResponseException extends RuntimeException
{
    /**
     * WebEx exception code, see WebEx XML API Appendix E.
     * @var int
     */
    protected $_exceptionID;

    /**
     * @param  int $exceptionID
     * @param  string $message
     * @param  int $code
     * @param  Exception $previous
     */
    public function __construct($exceptionID, $message = '', $code = 0, Exception $previous = null)
    {
        $this->_exceptionID = (int) $exceptionID;

        parent::__construct($message, $code, $previous);
    }

    public function getExceptionID()
    {
        return $this->_exceptionID;
    }
}
