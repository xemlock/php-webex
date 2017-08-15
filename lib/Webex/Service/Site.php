<?php

class Webex_Service_Site extends Webex_Service_Abstract
{
    const LST_TIME_ZONE = 'site.LstTimeZone';
    const GET_SITE      = 'site.GetSite';
    const SET_SITE      = 'site.SetSite';

    /**
     * @param Webex_Model_Site_LstTimeZone|array $lstTimeZone
     * @return Webex_Model_Site_LstTimeZoneResponse
     */
    public function lstTimeZone($lstTimeZone = null)
    {
        if ($lstTimeZone && !$lstTimeZone instanceof Webex_Model_Site_LstTimeZone) {
            $lstTimeZone = new Webex_Model_Site_LstTimeZone($lstTimeZone);
        }

        return $this->sendRequest(
            self::LST_TIME_ZONE,
            $lstTimeZone,
            Webex_Model_Site_LstTimeZoneResponse::CLASS_NAME
        );
    }

    /**
     * @param Webex_Model_Site_GetSite|array $getSite
     * @return Webex_Model_Site_GetSiteResponse
     */
    public function getSite($getSite = null)
    {
        if ($getSite && !$getSite instanceof Webex_Model_Site_GetSite) {
            $getSite = new Webex_Model_Site_GetSite($getSite);
        }

        return $this->sendRequest(
            self::GET_SITE,
            $getSite,
            Webex_Model_Site_GetSiteResponse::CLASS_NAME
        );
    }

    public function toBool($value)
    {
        $value = strtoupper((string) $value);
        return ($value === 'TRUE');
    }

}
