<?php

class Webex_Model_Site_LstTimeZoneResponse extends Webex_Model_Entity
{
    const CLASS_NAME = __CLASS__;

    /**
     * @var Webex_Collection_Collection<Webex_Model_Site_TimeZone>
     */
    protected $_timeZone;

    /**
     * @return Webex_Collection_Collection<Webex_Model_Site_TimeZone>
     */
    public function getTimeZone()
    {
        if (!$this->_timeZone instanceof Webex_Collection_Collection) {
            $this->_timeZone = new Webex_Collection_Collection(Webex_Model_Site_TimeZone::CLASS_NAME);
        }
        return $this->_timeZone;
    }

    /**
     * @param Webex_Model_Site_TimeZone $timeZone
     * @return Webex_Model_Site_LstTimeZoneResponse
     */
    public function addTimeZone($timeZone)
    {
        if (!$timeZone instanceof Webex_Model_Site_TimeZone) {
            $timeZone = new Webex_Model_Site_TimeZone($timeZone);
        }
        $this->getTimeZone()->add($timeZone);
        return $this;
    }
}
