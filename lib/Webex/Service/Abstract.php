<?php

/**
 * A base class for WebEx XML API service.
 */
abstract class Webex_Service_Abstract
{
    const SCHEMA_COMMON  = 'http://www.webex.com/schemas/2002/06/common';
    const SCHEMA_MEETING = 'http://www.webex.com/schemas/2002/06/service/meeting';
    const SCHEMA_SERVICE = 'http://www.webex.com/schemas/2002/06/service';
    const SCHEMA_SITE    = 'http://www.webex.com/schemas/2002/06/service/site';
    const SCHEMA_USER    = 'http://www.webex.com/schemas/2002/06/service/user';

    /**
     * Security context
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
     * Parse XML response.
     *
     * @param  string $response
     * @return SimpleXMLElement
     * @throws Webex_Exception_ConnectionException
     * @throws Webex_Exception_ResponseException
     */
    protected function _parseResponse($response) // {{{
    {
        if ($response === false) {
            throw new Webex_Exception_ConnectionException('Unable to connect to WebEx XML API endpoint');
        }

        // for security purposes disable external entities
        $loadEntities         = libxml_disable_entity_loader(true);
        $useInternalXmlErrors = libxml_use_internal_errors(true);

        try {
            $xml = new SimpleXMLElement($response);
        } catch (Exception $e) {
            $xml = null;
        }

        libxml_disable_entity_loader($loadEntities);
        libxml_use_internal_errors($useInternalXmlErrors);

        // The magic behind SimpleXML causes empty($xml) to evaluate to TRUE
        if (!$xml instanceof SimpleXMLElement) {
            // according to WebEx XML API error codes, 0 indicates an unknown server error
            throw new Webex_Exception_ResponseException(0, 'Improperly formatted response from WebEx XML API endpoint');
        }

        $nodes = $xml->children(self::SCHEMA_SERVICE);
        $responseHeader = $nodes->header->response;

        if ((string) $responseHeader->result !== 'SUCCESS') {
            $exceptionID = (int) $responseHeader->exceptionID;
            $reason = (string) $responseHeader->reason;

            throw new Webex_Exception_ResponseException($exceptionID, $reason);
        }

        return $nodes->body->bodyContent;
    } // }}}

    public function sendRequest($service, $payload, $responseClass)
    {
        $start = microtime(true);
        $response = $this->_webex->transmit($service, $payload);
        $start2 = microtime(true);
        printf("Transmitted in %.2fs\n", $start2 - $start);

        $bodyContent = $this->_parseResponse($response);

        $extractor = new Webex_XmlDataExtractor();
        $data = $extractor->xmlToArray($bodyContent);

        $responseObject = new $responseClass($data);
        printf("Response instantiated in %.2fs\n", microtime(true) - $start2);

        return $responseObject;
    }
}
