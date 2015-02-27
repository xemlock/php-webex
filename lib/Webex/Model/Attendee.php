<?php

class Webex_Model_Attendee extends Webex_Model_Person
{
    const ROLE_ATTENDEE  = 'ATTENDEE';
    const ROLE_PRESENTER = 'PRESENTER';
    const ROLE_HOST      = 'HOST';

    /**
     * @var string
     */
    protected $_role = self::ROLE_ATTENDEE;

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param  string $role
     */
    public function setRole($role)
    {
        $this->_role = (string) $role;
        return $this;
    }
}
