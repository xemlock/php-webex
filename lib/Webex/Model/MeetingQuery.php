<?php

class Webex_Model_MeetingQuery
{
    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setFromArray($data);
        }
    }

    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . $key;
            if (is_callable(array($this, $method))) {
                $this->{$method}($value);
            }
        }
        return $this;
    }

    public function setOffset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }

    public function getOffset()
    {
        return $this->_offset;
    }

    // offset from where the retrieval start
    protected $_offset = 0;

    public function setLimit($limit)
    {
        $this->_limit = (int) $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->_limit;
    }

    // max number of results to be retrieved
    protected $_limit = 0;

    // COLUMN => ASC/DESC
    protected $_orderBy;

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

    // <meetingKey>
    // search for this meetingKey only??? TODO check what it does
    protected $_meetingKey;
    // </meetingKey>

    // <hostWebExID>
    // The WebEx ID of the host user
    protected $_hostUsername;
    // </hostWebExID>

    public function setOrderBy(array $order)
    {
        $newOrder = array();
        foreach ($order as $key => $value) {
            $newOrder[$key] = (string) $value;
        }
        $this->_orderBy = $newOrder;
        return $this;
    }

    public function getOrderBy()
    {
        return (array) $this->_orderBy;
    }

    public function setStartDate($startDate)
    {
        $startDate = Webex_Util_Time::toDateTime($startDate);
        $this->setStartDateMin($startDate);
        $this->setStartDateMax($startDate);
        return $this;
    }

    public function setStartDateMin($startDateMin)
    {
        $this->_startDateMin = Webex_Util_Time::toDateTime($startDateMin);
        return $this;
    }

    public function setStartDateMax($startDateMax)
    {
        $this->_startDateMax = Webex_Util_Time::toDateTime($startDateMax);
        return $this;
    }

    public function setEndDate($endDate)
    {
        $endDate = Webex_Util_Time::toDateTime($endDate);
        $this->setEndDateMin($endDate);
        $this->setEndDateMax($endDate);
        return $this;
    }

    public function setEndDateMin($endDateMin)
    {
        $this->_endDateMin = Webex_Util_Time::toDateTime($endDateMin);
        return $this;
    }

    public function setEndDateMax($endDateMax)
    {
        $this->_endDateMax = Webex_Util_Time::toDateTime($endDateMax);
        return $this;
    }

    public function getHostUsername()
    {
        return $this->_hostUsername;
    }

    public function setHostUsername($hostUsername)
    {
        $this->_hostUsername = (string) $hostUsername;
        return $this;
    }
}
