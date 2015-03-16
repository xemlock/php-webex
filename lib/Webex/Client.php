<?php

class Webex_Client
{
    protected $webExID;
    protected $password;
    protected $siteID;
    protected $siteName;
    protected $partnerID;

    /**
     * @var array
     */
    protected $_services;

    /**
     * @param  $webExID   A WebEx-maintained reference to the WebEx user ID for the meeting host
     * @param  $password  The password for the user with a webExID
     * @param  $siteID    The WebEx-assigned identification number that uniquely identifies your website
     * @param  $siteName  The first string in your WebEx site URL, provided by WebEx
     * @param  $partnerID Optional. A reference to the WebEx partner, provided by WebEx
     */
    public function __construct($webExID, $password, $siteID, $siteName, $partnerID = null)
    {
        $this->webExID   = $webExID;
        $this->password  = $password;
        $this->siteID    = $siteID;
        $this->siteName  = $siteName;
        $this->partnerID = $partnerID;
    }

    public function transmit($service, $payload = null)
    {
        // Generate XML payload
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
        $xml .= '<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= '<header>';
        $xml .= '<securityContext>';
        $xml .= '<webExID>' . $this->webExID . '</webExID>';
        $xml .= '<password>' . $this->password . '</password>';
        $xml .= '<siteID>' . $this->siteID . '</siteID>';
        $xml .= '<partnerID>' . $this->partnerID . '</partnerID>';
        $xml .= '</securityContext>';
        $xml .= '</header>';
        $xml .= '<body>';
        $xml .= '<bodyContent xsi:type="java:com.webex.service.binding.' . $service  . '">';
        $xml .= $payload;
        $xml .= '</bodyContent>';
        $xml .= '</body>';
        $xml .= '</serv:message>';

        // Send request
        $url = sprintf('%s.webex.com/WBXService/XMLService', $this->siteName);
        $headers = array(
            'User-Agent: Webex_Client',
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($xml),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        return $response;
    }

    public function getService($serviceName)
    {
        $serviceName = ucfirst($serviceName);

        if (empty($this->_services[$serviceName])) {
            $className = 'Webex_Service_' . $serviceName;
            $this->_services[$serviceName] = new $className($this);
        }

        return $this->_services[$serviceName];
    }

    /**
     * Proxy to {@link getService()}.
     */
    public function __get($key)
    {
        return $this->getService($key);
    }
}
