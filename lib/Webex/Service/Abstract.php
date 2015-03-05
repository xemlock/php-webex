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
     */
    protected function _parseResponse($response)
    {
        if ($response === false) {
            throw new Exception('Unable to connect to WebEx API endpoint');
        }

        $xmlReader = new Webex_XmlReader();
        $xmlReader->xml($response);

        $status = array(
            'result' => 'FAILURE',
            'reason' => 'Improperly formatted server response',
            'exceptionID' => 0, // according to WebEx XML API error codes, 0 indicates a server error
        );

        while (@$xmlReader->read()) {
            if ($xmlReader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            if ($xmlReader->name === 'serv:response') {
                $subtree = $xmlReader->getSubtree();
                while ($subtree->read()) {
                    if ($subtree->nodeType !== XMLReader::ELEMENT) {
                        continue;
                    }
                    switch ($subtree->name) {
                        case 'serv:result':
                            $status['result'] = $subtree->readString();
                            break;

                        case 'serv:reason':
                            $status['reason'] = $subtree->readString();
                            break;

                        case 'serv:exceptionID':
                            $status['exceptionID'] = (int) $subtree->readString();
                            break;
                    }
                }
                break;
            }
        }

        if ($status['result'] !== 'SUCCESS') {
            throw new Exception($status['reason'], $status['exceptionID']);
        }
    }
}
