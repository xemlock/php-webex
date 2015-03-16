<?php

class Webex_Model_MeetingQuery extends Webex_Model_Query
{
    // specific to meeting listing, timezone will be unified
    // during search all dates will be converted to GTM+00:00 timezone
    // <dateScope>
    protected $_timeZoneId;

    protected $_startDateMin;

    protected $_startDateMax;
    // startDate BETWEEN startDateMin AND startDateMax

    protected $_endDateMin;

    protected $_endDateMax;
    // endDate BETWEEN endDateMin AND endDateMax
    // </dateScope>

    // <hostWebExID>
    // The WebEx ID of the host user
    protected $_hostUsername;
    // </hostWebExID>

    // <meetingKey>
    // search for this meetingKey only??? TODO check what it does
    protected $_meetingKey;
    // </meetingKey>

    public function setTimeZoneId($timeZoneId)
    {
        $this->_timeZoneId = (int) $timeZoneId;
        return $this;
    }

    public function getTimeZoneId()
    {
        return $this->_timeZoneId;
    }

    /**
     * Proxy to {@link setStartDateMin()}.
     *
     * {@inheritDoc}
     */
    public function setStartDate($startDate)
    {
        return $this->setStartDateMin($startDate);
    }

    /**
     * @return DateTime|null
     */
    public function getStartDateMin()
    {
        return $this->_startDateMin;
    }

    /**
     * @param  string $startDate
     */
    public function setStartDateMin($startDateMin)
    {
        $this->_startDateMin = (string) $startDateMin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartDateMax()
    {
        return $this->_startDateMax;
    }

    /**
     * @param  int|string $startDate
     */
    public function setStartDateMax($startDateMax)
    {
        $this->_startDateMax = (string) $startDateMax;
        return $this;
    }

    /**
     * Proxy to {@link setEndDateMin()}.
     *
     * @param  int|string $date
     */
    public function setEndDate($date)
    {
        return $this->setEndDateMin($date);
    }

    /**
     * @param  int|string $date
     */
    public function setEndDateMin($date)
    {
        $this->_endDateMin = (string) $date;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDateMin()
    {
        return $this->_endDateMin;
    }

    /**
     * @return string|null
     */
    public function getEndDateMax()
    {
        return $this->_endDateMax;
    }

    /**
     * @param  int|string $date
     */
    public function setEndDateMax($date)
    {
        $this->_endDateMax = (string) $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostUsername()
    {
        return $this->_hostUsername;
    }

    /**
     * @param  string $hostUsername
     */
    public function setHostUsername($hostUsername)
    {
        $this->_hostUsername = (string) $hostUsername;
        return $this;
    }
}
