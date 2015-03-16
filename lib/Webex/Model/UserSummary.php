<?php

/**
 * Represents basic info about a WebEx host account.
 */
class Webex_Model_UserSummary extends Webex_Model_Entity
{
    const ACTIVE_ACTIVATED           = 'ACTIVATED';
    const ACTIVE_REQUEST_TO_ACTIVATE = 'REQUEST_TO_ACTIVATE';
    const ACTIVE_DEACTIVATED         = 'DEACTIVATED';

    /**
     * WebExID of the user.
     * @var string
     */
    protected $_username;

    /**
     * The email address of the user, maximum of 64 characters.
     * @var string
     */
    protected $_email;

    /**
     * Determines whether the user account has been staged for use.
     * @var string
     */
    protected $_active;

    /**
     * The user's first name, maximum of 64 characters.
     * @var string
     */
    protected $_firstName;

    /**
     * The user's last name, maximum of 64 characters.
     * @var string
     */
    protected $_lastName;

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

    public function getActive()
    {
        return $this->_active;
    }

    public function setActive($active)
    {
        $this->_active = $active;
        return $this;
    }

    public function getFirstName()
    {
        return $this->_firstName;
    }

    public function setFirstName($firstName)
    {
        $this->_firstName = (string) $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->_lastName;
    }

    public function setLastName($lastName)
    {
        $this->_lastName = (string) $lastName;
        return $this;
    }
}
