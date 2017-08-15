<?php

class Webex_Model_Site_GetSite_ReturnSettings extends Webex_Model_Entity
    implements Webex_XmlSerializable
{
    /**
     * @var bool
     */
    protected $_eventCenter;

    /**
     * @return bool
     */
    public function isEventCenter()
    {
        return $this->_eventCenter;
    }

    /**
     * @param bool $eventCenter
     * @return Webex_Model_Site_GetSite_ReturnSettings
     */
    public function setEventCenter($eventCenter)
    {
        $this->_eventCenter = $eventCenter;
        return $this;
    }

    public function xmlSerialize()
    {
        return array(
            'eventCenter' => $this->_eventCenter,
        );
    }
}
