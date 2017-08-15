<?php

class Webex_Model_Site_MetaData_MeetingType extends Webex_Model_Entity
{
    /**
     * @var int
     */
    protected $_meetingTypeID;

    /**
     * @var string
     */
    protected $_meetingTypeName;

    /**
     * @return int
     */
    public function getMeetingTypeID()
    {
        return $this->_meetingTypeID;
    }

    /**
     * @param int $meetingTypeID
     * @return Webex_Model_Site_MetaData_MeetingType
     */
    public function setMeetingTypeID($meetingTypeID)
    {
        $this->_meetingTypeID = (int) $meetingTypeID;
        return $this;
    }

    /**
     * @return string
     */
    public function getMeetingTypeName()
    {
        return $this->_meetingTypeName;
    }

    /**
     * @param string $meetingTypeName
     * @return Webex_Model_Site_MetaData_MeetingType
     */
    public function setMeetingTypeName($meetingTypeName)
    {
        $this->_meetingTypeName = (string) $meetingTypeName;
        return $this;
    }
}
