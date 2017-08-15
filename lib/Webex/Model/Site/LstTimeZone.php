<?php

class Webex_Model_Site_LstTimeZone extends Webex_Model_Entity
    implements Webex_XmlSerializable
{
    /**
     * @var Webex_Collection_Collection<int>
     */
    protected $_timeZoneID;

    /**
     * @var string
     */
    protected $_date;

    public function getTimeZoneID()
    {
        if (!$this->_timeZoneID instanceof Webex_Collection_Collection) {
            $this->_timeZoneID = new Webex_Collection_Collection('int');
        }
        return $this->_timeZoneID;
    }

    /**
     * @param int $timeZoneID
     * @return Webex_Model_Site_LstTimeZone
     */
    public function addTimeZoneID($timeZoneID)
    {
        $this->getTimeZoneID()->add((int) $timeZoneID);
        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @param string $date
     * @return Webex_Model_Site_LstTimeZone
     */
    public function setDate($date)
    {
        $this->_date = date('m/d/Y H:i:s', strtotime($date));
        return $this;
    }

    public function xmlSerialize()
    {
        $data = array();

        $timeZoneID = $this->getTimeZoneID()->getItems();
        if (count($timeZoneID)) {
            $data['timeZoneID'] = $timeZoneID;
        }

        if ($this->_date) {
            $data['date'] = $this->_date;
        }

        return $data;
    }
}
