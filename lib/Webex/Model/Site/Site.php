<?php

class Webex_Model_Site_Site extends Webex_Model_Entity
{
    /**
     * @var Webex_Model_Site_MetaData
     */
    protected $_metaData;

    /**
     * @param array $metaData
     * @return $this
     */
    public function setMetaData($metaData)
    {
        if (!$metaData instanceof Webex_Model_Site_MetaData) {
            $metaData = new Webex_Model_Site_MetaData($metaData);
        }
        $this->_metaData = $metaData;
        return $this;
    }

    /**
     * @return Webex_Model_Site_MetaData
     */
    public function getMetaData()
    {
        return $this->_metaData;
    }
}
