<?php

class Webex_Model_Site_MetaData extends Webex_Model_Entity
{
    /**
     * If TRUE, specifies a Enterprise Edition site; if FALSE, not.
     * @var bool
     */
    protected $_isEnterprise;

    /**
     * The services (Meeting Center, Event Center, Training Center, Support
     * Center, and Sales Center) that are supported for this site.
     * @var Webex_Collection_Collection<string>
     */
    protected $_serviceType;

    /**
     * @var Webex_Collection_Collection<Webex_Model_Site_MetaData_MeetingType>
     */
    protected $_meetingTypes;

    /**
     * Full name for the site.
     * @var string
     */
    protected $_siteName;

    /**
     * Domain name for the site, for example, acme.webex.com.
     * @var Webex_Collection_Collection<string>
     */
    protected $_brandName;

    protected $_region;

    protected $_currency;

    protected $_timeZoneID;

    protected $_timeZone;

    protected $_partnerID;

    protected $_webDomain;

    protected $_meetingDomain;

    protected $_telephonyDomain;

    protected $_pageVersion;

    protected $_clientVersion;

    protected $_pageLanguage;

    /**
     * If TRUE, the site is an active site.
     * @var bool
     */
    protected $_activateStatus;

    protected $_webPageType;

    /**
     * Specifies that an iCalendar file will be available for users to update calendars.
     * @var bool
     */
    protected $_iCalendar;

    protected $_myWebExDefaultPage;

    protected $_componentVersion;

    protected $_accountNumLimit;

    protected $_activeUserCount;

    protected $_auoAccountNumLimit;

    protected $_auoActiveUserCount;

    protected $_displayMeetingActualTime;

    /**
     * Determines whether or not the GMT offset is displayed in emails and web pages.
     * @var bool
     */
    protected $_displayOffset;

    /**
     * @return bool
     */
    public function getIsEnterprise()
    {
        return $this->_isEnterprise;
    }

    /**
     * @param bool $isEnterprise
     * @return Webex_Model_Site_MetaData
     */
    public function setIsEnterprise($isEnterprise)
    {
        $this->_isEnterprise = (bool) $isEnterprise;
        return $this;
    }

    /**
     * @return Webex_Collection_Collection<string>
     */
    public function getServiceType()
    {
        if (!$this->_serviceType) {
            $this->_serviceType = new Webex_Collection_Collection('string');
        }
        return $this->_serviceType;
    }

    /**
     * @param mixed $serviceType
     * @return Webex_Model_Site_MetaData
     */
    public function addServiceType($serviceType)
    {
        $this->getServiceType()->add((string) $serviceType);
        return $this;
    }

    /**
     * @return Webex_Collection_Collection<Webex_Model_Site_MetaData_MeetingType>
     */
    public function getMeetingTypes()
    {
        if (!$this->_meetingTypes) {
            $this->_meetingTypes = new Webex_Collection_Collection('Webex_Model_Site_MetaData_MeetingType');
        }
        return $this->_meetingTypes;
    }

    /**
     * @param Webex_Model_Site_MetaData_MeetingType|array $meetingType
     * @return $this
     */
    public function addMeetingTypes($meetingType)
    {
        if (!$meetingType instanceof Webex_Model_Site_MetaData_MeetingType) {
            $meetingType = new Webex_Model_Site_MetaData_MeetingType($meetingType);
        }
        $this->getMeetingTypes()->add($meetingType);
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->_siteName;
    }

    /**
     * @param string $siteName
     * @return Webex_Model_Site_MetaData
     */
    public function setSiteName($siteName)
    {
        $this->_siteName = $siteName;
        return $this;
    }

    /**
     * @return Webex_Collection_Collection<string>
     */
    public function getBrandName()
    {
        if (!$this->_brandName) {
            $this->_brandName = new Webex_Collection_Collection('string');
        }
        return $this->_brandName;
    }

    /**
     * @param string $brandName
     * @return Webex_Model_Site_MetaData
     */
    public function addBrandName($brandName)
    {
        $this->getBrandName()->add((string) $brandName);
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->_region;
    }

    /**
     * @param string $region
     * @return Webex_Model_Site_MetaData
     */
    public function setRegion($region)
    {
        $this->_region = $region;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * @param mixed $currency
     * @return Webex_Model_Site_MetaData
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeZoneID()
    {
        return $this->_timeZoneID;
    }

    /**
     * @param mixed $timeZoneID
     * @return Webex_Model_Site_MetaData
     */
    public function setTimeZoneID($timeZoneID)
    {
        $this->_timeZoneID = $timeZoneID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeZone()
    {
        return $this->_timeZone;
    }

    /**
     * @param mixed $timeZone
     * @return Webex_Model_Site_MetaData
     */
    public function setTimeZone($timeZone)
    {
        $this->_timeZone = $timeZone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartnerID()
    {
        return $this->_partnerID;
    }

    /**
     * @param mixed $partnerID
     * @return Webex_Model_Site_MetaData
     */
    public function setPartnerID($partnerID)
    {
        $this->_partnerID = $partnerID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebDomain()
    {
        return $this->_webDomain;
    }

    /**
     * @param mixed $webDomain
     * @return Webex_Model_Site_MetaData
     */
    public function setWebDomain($webDomain)
    {
        $this->_webDomain = $webDomain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeetingDomain()
    {
        return $this->_meetingDomain;
    }

    /**
     * @param mixed $meetingDomain
     * @return Webex_Model_Site_MetaData
     */
    public function setMeetingDomain($meetingDomain)
    {
        $this->_meetingDomain = $meetingDomain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelephonyDomain()
    {
        return $this->_telephonyDomain;
    }

    /**
     * @param mixed $telephonyDomain
     * @return Webex_Model_Site_MetaData
     */
    public function setTelephonyDomain($telephonyDomain)
    {
        $this->_telephonyDomain = $telephonyDomain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageVersion()
    {
        return $this->_pageVersion;
    }

    /**
     * @param mixed $pageVersion
     * @return Webex_Model_Site_MetaData
     */
    public function setPageVersion($pageVersion)
    {
        $this->_pageVersion = $pageVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientVersion()
    {
        return $this->_clientVersion;
    }

    /**
     * @param mixed $clientVersion
     * @return Webex_Model_Site_MetaData
     */
    public function setClientVersion($clientVersion)
    {
        $this->_clientVersion = $clientVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageLanguage()
    {
        return $this->_pageLanguage;
    }

    /**
     * @param mixed $pageLanguage
     * @return Webex_Model_Site_MetaData
     */
    public function setPageLanguage($pageLanguage)
    {
        $this->_pageLanguage = $pageLanguage;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActivateStatus()
    {
        return $this->_activateStatus;
    }

    /**
     * @param bool $activateStatus
     * @return Webex_Model_Site_MetaData
     */
    public function setActivateStatus($activateStatus)
    {
        $this->_activateStatus = (bool) $activateStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebPageType()
    {
        return $this->_webPageType;
    }

    /**
     * @param mixed $webPageType
     * @return Webex_Model_Site_MetaData
     */
    public function setWebPageType($webPageType)
    {
        $this->_webPageType = $webPageType;
        return $this;
    }

    /**
     * @return bool
     */
    public function getICalendar()
    {
        return $this->_iCalendar;
    }

    /**
     * @param bool $iCalendar
     * @return Webex_Model_Site_MetaData
     */
    public function setICalendar($iCalendar)
    {
        $this->_iCalendar = (bool) $iCalendar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMyWebExDefaultPage()
    {
        return $this->_myWebExDefaultPage;
    }

    /**
     * @param mixed $myWebExDefaultPage
     * @return Webex_Model_Site_MetaData
     */
    public function setMyWebExDefaultPage($myWebExDefaultPage)
    {
        $this->_myWebExDefaultPage = $myWebExDefaultPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComponentVersion()
    {
        return $this->_componentVersion;
    }

    /**
     * @param mixed $componentVersion
     * @return Webex_Model_Site_MetaData
     */
    public function setComponentVersion($componentVersion)
    {
        $this->_componentVersion = $componentVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccountNumLimit()
    {
        return $this->_accountNumLimit;
    }

    /**
     * @param mixed $accountNumLimit
     * @return Webex_Model_Site_MetaData
     */
    public function setAccountNumLimit($accountNumLimit)
    {
        $this->_accountNumLimit = $accountNumLimit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActiveUserCount()
    {
        return $this->_activeUserCount;
    }

    /**
     * @param mixed $activeUserCount
     * @return Webex_Model_Site_MetaData
     */
    public function setActiveUserCount($activeUserCount)
    {
        $this->_activeUserCount = $activeUserCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuoAccountNumLimit()
    {
        return $this->_auoAccountNumLimit;
    }

    /**
     * @param mixed $auoAccountNumLimit
     * @return Webex_Model_Site_MetaData
     */
    public function setAuoAccountNumLimit($auoAccountNumLimit)
    {
        $this->_auoAccountNumLimit = $auoAccountNumLimit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuoActiveUserCount()
    {
        return $this->_auoActiveUserCount;
    }

    /**
     * @param mixed $auoActiveUserCount
     * @return Webex_Model_Site_MetaData
     */
    public function setAuoActiveUserCount($auoActiveUserCount)
    {
        $this->_auoActiveUserCount = $auoActiveUserCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplayMeetingActualTime()
    {
        return $this->_displayMeetingActualTime;
    }

    /**
     * @param mixed $displayMeetingActualTime
     * @return Webex_Model_Site_MetaData
     */
    public function setDisplayMeetingActualTime($displayMeetingActualTime)
    {
        $this->_displayMeetingActualTime = $displayMeetingActualTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplayOffset()
    {
        return $this->_displayOffset;
    }

    /**
     * @param bool $displayOffset
     * @return Webex_Model_Site_MetaData
     */
    public function setDisplayOffset($displayOffset)
    {
        $this->_displayOffset = (bool) $displayOffset;
        return $this;
    }

    /**
     * Schema has this obvious typo. This method is a proxy to {@link setPartnerID}.
     * @param $partnerID
     * @return mixed
     */
    public function setParterID($partnerID)
    {
        return $this->setPartnerID($partnerID);
    }
}
