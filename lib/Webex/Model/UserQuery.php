<?php

class Webex_Model_UserQuery extends Webex_Model_Query
{
    /**
     * @var string
     */
    protected $_username;

    protected $_email;

    public function getUsername()
    {
        return $this->_username;
    }

    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = (string) $email;
        return $this;
    }
}
