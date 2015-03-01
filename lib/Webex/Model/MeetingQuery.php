<?php

class Webex_Model_MeetingQuery extends Webex_Model_Query
{
    // specific to meeting listing, timezone will be unified
    // during search all dates will be converted to GTM+00:00 timezone
    // <dateScope>
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
     * @param  int|string|DateTime $startDate
     */
    public function setStartDateMin($startDateMin)
    {
        $this->_startDateMin = Webex_Util_Time::toDateTime($startDateMin);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDateMax()
    {
        return $this->_startDateMax;
    }

    /**
     * @param  int|string|DateTime $startDate
     */
    public function setStartDateMax($startDateMax)
    {
        $this->_startDateMax = Webex_Util_Time::toDateTime($startDateMax);
        return $this;
    }

    /**
     * Proxy to {@link setEndDateMin()}.
     *
     * @param  int|string|DateTime $date
     */
    public function setEndDate($date)
    {
        return $this->setEndDateMin($date);
    }

    /**
     * @param  int|string|DateTime $date
     */
    public function setEndDateMin($date)
    {
        $this->_endDateMin = Webex_Util_Time::toDateTime($date);
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
     * @return DateTime|null
     */
    public function getEndDateMax()
    {
        return $this->_endDateMax;
    }

    /**
     * @param  int|string|DateTime $date
     */
    public function setEndDateMax($date)
    {
        $this->_endDateMax = Webex_Util_Time::toDateTime($date);
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
