<?php

class Webex_Model_Meeting
    extends Webex_Model_Entity
    implements Webex_Model_MeetingInterface
{
    /**
     * An internal unique ID number for a Meeting Center session. Equivalent
     * to meetingKey or eventID.
     * @var string
     */
    protected $_id;

    /**
     * The session type ID for a Meeting Center session.
     * @var int
     */
    protected $_type;

    /**
     * The name of the meeting, maximum of 512 characters.
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_password;

    /**
     * The starting date and time for the first (or only) occurrence of
     * the meeting. It also stores information about the time zone for
     * the geographic location of the meeting.
     * @var DateTime
     */
    protected $_startDate;

    /**
     * The duration of the meeting in minutes.
     * @var int
     */
    protected $_duration = 60;

    /**
     * @var bool
     */
    protected $_isPublic = false;

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
     * @var Webex_Model_Collection<Webex_Model_Attendee>
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
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param  string $id
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param  string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param  int $type
     */
    public function setType($type)
    {
        $this->_type = (int) $type;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate()
    {
        return $this->_startDate;
    }

    /**
     * @param  int|string|DateTime $startDate
     * @throws Exception
     */
    public function setStartDate($startDate)
    {
        $this->_startDate = Webex_Util_Time::toDateTime($startDate);
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->_duration;
    }

    /**
     * @param  int $duration
     */
    public function setDuration($duration)
    {
        $this->_duration = (int) $duration;
        return $this;
    }

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
    public function isPublic()
    {
        return $this->_isPublic;
    }

    /**
     * @param  bool $flag
     */
    public function setPublic($flag)
    {
        $this->_isPublic = (bool) $flag;
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

    public function getOpenTime()
    {
        return $this->_openTime;
    }

    public function setOpenTime($openTime)
    {
        $this->_openTime = (int) $openTime;
        return $this;
    }

    public function getEmailInvitations()
    {
        return $this->_emailInvitations;
    }

    public function setEmailInvitations($flag)
    {
        $this->_emailInvitations = (bool) $flag;
        return $this;
    }

    /**
     * @return Webex_Model_Collection<Webex_Model_Attendee>
     */
    public function getAttendees()
    {
        if ($this->_attendees === null) {
            $this->_attendees = new Webex_Model_Collection('Webex_Model_Attendee');
        }
        return $this->_attendees;
    }
}
