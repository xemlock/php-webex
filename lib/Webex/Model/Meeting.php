<?php

class Webex_Model_Meeting extends Webex_Model_MeetingSummary
{
    /**
     * @var string
     */
    protected $_password;

    /**
     * Determines whether or not attendees are allowed to join the
     * teleconference before the host.
     * @var bool
     */
    protected $_joinBeforeHost = false;

    /**
     * @var bool
     */
    protected $_enforcePassword = false;

    protected $_greeting;

    protected $_agenda;

    protected $_invitation;

    protected $_location;

    /**
     * The number of seconds attendees can join a session before its scheduled
     * start time. Valid values 0, 300, 600, 900. If this is 0, then attendees
     * can only join the session after the host has started it.
     * @var int
     */
    protected $_openTime = 0;

    /**
     * A WebEx-maintained estimated number of participants (excluding the
     * host) that can be in the meeting at the same time.
     * @var int
     */
    protected $_maxUsers = 4;

    /**
     * @var Webex_Collection_Collection<Webex_Model_Attendee>
     */
    protected $_attendees;

    /**
     * Whether to send invitation email messages to the meeting session
     * attendees.
     * @var bool
     */
    protected $_emailInvitations = false;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param  string $password
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function getJoinBeforeHost()
    {
        return $this->_joinBeforeHost;
    }

    /**
     * @param  bool $flag
     */
    public function setJoinBeforeHost($flag)
    {
        $this->_joinBeforeHost = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnforcePassword()
    {
        return $this->_enforcePassword;
    }

    /**
     * @param  bool $flag
     */
    public function setEnforcePassword($flag)
    {
        $this->_enforcePassword = (bool) $flag;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxUsers()
    {
        return $this->_maxUsers;
    }

    /**
     * @param  int $maxUsers
     */
    public function setMaxUsers($maxUsers)
    {
        $this->_maxUsers = (int) $maxUsers;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpenTime()
    {
        return $this->_openTime;
    }

    /**
     * @param  int $openTime
     */
    public function setOpenTime($openTime)
    {
        $this->_openTime = (int) $openTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEmailInvitations()
    {
        return $this->_emailInvitations;
    }

    /**
     * @param  bool $flag
     */
    public function setEmailInvitations($flag)
    {
        $this->_emailInvitations = (bool) $flag;
        return $this;
    }

    /**
     * @return Webex_Collection_Collection<Webex_Model_Attendee>
     */
    public function getAttendees()
    {
        if ($this->_attendees === null) {
            $this->_attendees = new Webex_Collection_Collection('Webex_Model_Attendee');
        }
        return $this->_attendees;
    }
}
