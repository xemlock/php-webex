<?php

abstract class Webex_Service_Abstract
{
    /**
     * @var Webex_Client
     */
    protected $_webex;

    /**
     * @var Webex_XmlSerializer
     */
    protected $_serializer;

    /**
     * @param  Webex_Client $webex
     * @return void
     */
    public function __construct(Webex_Client $webex)
    {
        $this->_webex = $webex;
        $this->_serializer = new Webex_XmlSerializer();
    }

    /**
     * @param  string $response
     * @return SimpleXMLElement
     */
    protected function _parseResponse($response)
    {
        try {
            $xmlResponse = new SimpleXMLElement($response);
        } catch (Exception $e) {
            throw new Exception('Response is not a valid XML document');
        }

        $nodes = $xmlResponse->xpath('//serv:response/serv:result');
        $result = (string) $nodes[0];

        if ($result !== 'SUCCESS') {
            $nodes = $xmlResponse->xpath('//serv:response/serv:reason');
            throw new Exception((string) $nodes[0]);
        }

        return $xmlResponse;
    }
}
