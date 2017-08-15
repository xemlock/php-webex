<?php

class Webex_Model_SecurityContext extends Webex_Model_Entity implements Webex_XmlSerializable
{
    /**
     * A WebEx-maintained reference to the WebEx user ID for the meeting host
     * @var string
     */
    protected $_webExID;

    /**
     * The password for the user with a webExID
     * @var string
     */
    protected $_password;

    /**
     * The WebEx-assigned identification number that uniquely identifies your website
     * @var int
     */
    protected $_siteID;

    /**
     * The first string in your WebEx site URL, provided by WebEx
     * @var string
     */
    protected $_siteName;

    /**
     * Optional. A reference to the WebEx partner, provided by WebEx
     * @var string
     */
    protected $_partnerID;

    /**
     * Optional. User must supply the email address that is stored in their
     * user profile if they use this option.
     * @var string
     */
    protected $_email;

    /**
     * A 32 alphanumeric character string associated with an authenticated user
     * for the duration of a session. Can be used in place of <password>.
     * @var string
     */
    protected $_sessionTicket;

    /**
     * @var string
     */
    protected $_clientID;

    /**
     * @var string
     */
    protected $_clientSecret;

    /**
     * @return string
     */
    public function getWebExID()
    {
        return $this->_webExID;
    }

    /**
     * @param string $webExID
     * @return Webex_Model_SecurityContext
     */
    public function setWebExID($webExID)
    {
        $this->_webExID = $webExID;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param string $password
     * @return Webex_Model_SecurityContext
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getSiteID()
    {
        return $this->_siteID;
    }

    /**
     * @param int $siteID
     * @return Webex_Model_SecurityContext
     */
    public function setSiteID($siteID)
    {
        $this->_siteID = $siteID;
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
     * @return Webex_Model_SecurityContext
     */
    public function setSiteName($siteName)
    {
        $this->_siteName = $siteName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerID()
    {
        return $this->_partnerID;
    }

    /**
     * @param string $partnerID
     * @return Webex_Model_SecurityContext
     */
    public function setPartnerID($partnerID)
    {
        $this->_partnerID = $partnerID;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param string $email
     * @return Webex_Model_SecurityContext
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionTicket()
    {
        return $this->_sessionTicket;
    }

    /**
     * @param string $sessionTicket
     * @return Webex_Model_SecurityContext
     */
    public function setSessionTicket($sessionTicket)
    {
        $this->_sessionTicket = $sessionTicket;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientID()
    {
        return $this->_clientID;
    }

    /**
     * @param string $clientID
     * @return Webex_Model_SecurityContext
     */
    public function setClientID($clientID)
    {
        $this->_clientID = $clientID;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->_clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return Webex_Model_SecurityContext
     */
    public function setClientSecret($clientSecret)
    {
        $this->_clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @return array
     */
    public function xmlSerialize()
    {
        return array(
            'securityContext' => array(
                'webExID'       => $this->_webExID,
                'password'      => $this->_password,
                'siteID'        => $this->_siteID,
                'siteName'      => $this->_siteName,
                'partnerID'     => $this->_partnerID,
                'email'         => $this->_email,
                'sessionTicket' => $this->_sessionTicket,
                'clientID'      => $this->_clientID,
			    'clientSecret'  => $this->_clientSecret,
            ),
        );
    }
}
