<?php

class Webex_Model_Site_TimeZone extends Webex_Model_Entity
{
    const CLASS_NAME = __CLASS__;

    /**
     * Determines the time zone for the geographic location of the meeting.
     * @var int
     */
    protected $_timeZoneID;

    /**
     * The number of minutes to add or subtract from GMT to obtain local time for a time zone.
     * @var int
     */
    protected $_gmtOffset;

    /**
     * Time zone description, formatted at GMT-HH:MM, Region (City).
     * var string
     */
    protected $_description;

    /**
     * The short name for the time zone.
     * @var string
     */
    protected $_shortName;

    /**
     * Determines if the time zone name is hidden.
     * @var bool
     */
    protected $_hideTimeZoneName;

    /**
     * Automatically set time ahead by 1 hour for Daylight Saving Time if the date falls within DST.
     * @var bool
     */
    protected $_fallInDST;

    /**
     * Defines the Standard Time label.
     * @var string
     */
    protected $_standardLabel;

    /**
     * Defines the Daylight Saving Time label.
     * @var string
     */
    protected $_daylightLabel;

    /**
     * @return int
     */
    public function getTimeZoneID()
    {
        return $this->_timeZoneID;
    }

    /**
     * @param int $timeZoneID
     * @return Webex_Model_Site_TimeZone
     */
    public function setTimeZoneID($timeZoneID)
    {
        $this->_timeZoneID = (int) $timeZoneID;
        return $this;
    }

    /**
     * @return int
     */
    public function getGmtOffset()
    {
        return $this->_gmtOffset;
    }

    /**
     * @param int $gmtOffset
     * @return Webex_Model_Site_TimeZone
     */
    public function setGmtOffset($gmtOffset)
    {
        $this->_gmtOffset = (int) $gmtOffset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param mixed $description
     * @return Webex_Model_Site_TimeZone
     */
    public function setDescription($description)
    {
        $this->_description = (string) $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->_shortName;
    }

    /**
     * @param string $shortName
     * @return Webex_Model_Site_TimeZone
     */
    public function setShortName($shortName)
    {
        $this->_shortName = (string) $shortName;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHideTimeZoneName()
    {
        return $this->_hideTimeZoneName;
    }

    /**
     * @param bool $hideTimeZoneName
     * @return Webex_Model_Site_TimeZone
     */
    public function setHideTimeZoneName($hideTimeZoneName)
    {
        $this->_hideTimeZoneName = (bool) $hideTimeZoneName;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFallInDST()
    {
        return $this->_fallInDST;
    }

    /**
     * @param bool $fallInDST
     * @return Webex_Model_Site_TimeZone
     */
    public function setFallInDST($fallInDST)
    {
        $this->_fallInDST = (bool) $fallInDST;
        return $this;
    }

    /**
     * @return string
     */
    public function getStandardLabel()
    {
        return $this->_standardLabel;
    }

    /**
     * @param string $standardLabel
     * @return Webex_Model_Site_TimeZone
     */
    public function setStandardLabel($standardLabel)
    {
        $this->_standardLabel = (string) $standardLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getDaylightLabel()
    {
        return $this->_daylightLabel;
    }

    /**
     * @param string $daylightLabel
     * @return Webex_Model_Site_TimeZone
     */
    public function setDaylightLabel($daylightLabel)
    {
        $this->_daylightLabel = (string) $daylightLabel;
        return $this;
    }
}
