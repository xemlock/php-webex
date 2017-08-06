<?php

class Webex_Client
{
    /**
     * @var Webex_Type_SecurityContext
     */
    protected $_securityContext;

    /**
     * @var Webex_XmlSerializer
     */
    protected $_serializer;

    /**
     * @var array
     */
    protected $_services;

    /**
     * @param array|Webex_Type_SecurityContext $securityContext
     */
    public function __construct($securityContext)
    {
        if (!$securityContext instanceof Webex_Type_SecurityContext) {
            $securityContext = new Webex_Type_SecurityContext($securityContext);
        }

        $this->_securityContext = $securityContext;
        $this->_serializer = new Webex_XmlSerializer();
    }

    public function transmit($service, $payload = null)
    {
        // Generate XML payload
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
        $xml .= '<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= '<header>';
        $xml .= $this->_serializer->serialize($this->_securityContext);
        $xml .= '</header>';
        $xml .= '<body>';
        $xml .= '<bodyContent xsi:type="java:com.webex.service.binding.' . $service  . '">';
        $xml .= is_string($payload) ? $payload : $this->_serializer->serialize($payload);
        $xml .= '</bodyContent>';
        $xml .= '</body>';
        $xml .= '</serv:message>';

        // Send request
        $url = sprintf('%s.webex.com/WBXService/XMLService', $this->_securityContext->getSiteName());
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

    /**
     * @return Webex_Type_SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->_securityContext;
    }

    /**
     * @return Webex_XmlSerializer
     */
    public function getSerializer()
    {
        return $this->_serializer;
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
