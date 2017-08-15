<?php

class Webex_Model_Site_Site extends Webex_Model_Entity
{
    /**
     * @var Webex_Model_Site_MetaData
     */
    protected $_metaData;

    public function __construct(array $data = null)
    {
        $this->_metaData = new Webex_Model_Site_MetaData();
        parent::__construct($data);
    }

    /**
     * @param array $metaData
     * @return $this
     */
    public function setMetaData(array $metaData)
    {
        $this->_metaData->setFromArray($metaData);
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
