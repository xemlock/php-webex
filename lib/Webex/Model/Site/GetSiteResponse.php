<?php

class Webex_Model_Site_GetSiteResponse extends Webex_Model_Entity
{
    /**
     * @var Webex_Model_Site_Site
     */
    protected $_siteInstance;

    /**
     * @return Webex_Model_Site_Site
     */
    public function getSiteInstance()
    {
        return $this->_siteInstance;
    }

    /**
     * @param Webex_Model_Site_Site|array $siteInstance
     * @return Webex_Model_Site_GetSiteResponse
     */
    public function setSiteInstance($siteInstance)
    {
        if (!$siteInstance instanceof Webex_Model_Site_Site) {
            $siteInstance = new Webex_Model_Site_Site($siteInstance);
        }
        $this->_siteInstance = $siteInstance;
        return $this;
    }
}
