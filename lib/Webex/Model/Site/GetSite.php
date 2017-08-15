<?php

class Webex_Model_Site_GetSite extends Webex_Model_Entity
    implements Webex_XmlSerializable
{
    /**
     * @var Webex_Model_Site_GetSite_ReturnSettings
     */
    protected $_returnSettings;

    /**
     * @return Webex_Model_Site_GetSite_ReturnSettings
     */
    public function getReturnSettings()
    {
        return $this->_returnSettings;
    }

    /**
     * @param Webex_Model_Site_GetSite_ReturnSettings|array $returnSettings
     * @return Webex_Model_Site_GetSite
     */
    public function setReturnSettings($returnSettings)
    {
        if (!$returnSettings instanceof Webex_Model_Site_GetSite_ReturnSettings) {
            $returnSettings = new Webex_Model_Site_GetSite_ReturnSettings($returnSettings);
        }

        $this->_returnSettings = $returnSettings;
        return $this;
    }

    public function xmlSerialize()
    {
        return array(
            'returnSettings' => $this->_returnSettings,
        );
    }
}
