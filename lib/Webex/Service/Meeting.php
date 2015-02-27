<?php

class Webex_Service_Meeting
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
     * Retrieve meeting from WebEx meeting service.
     *
     * @param  int $id
     * @return Webex_Model_Meeting|null
     */
    public function getMeeting($id)
    {
        $response = $this->_webex->transmit(
            'meeting.GetMeeting',
            '<meetingKey>' . $this->_serializer->esc($id) . '</meetingKey>'
        );
        return $this->_serializer->unserializeMeeting($response);
    }

    /**
     * Save meeting to WebEx meeting service.
     *
     * @param  Webex_Model_Meeting $meeting
     * @return Webex_Model_Meeting
     * @throws Exception
     */
    public function saveMeeting(Webex_Model_Meeting $meeting)
    {
        if ($meeting->getId()) {
            $service = 'meeting.SetMeeting';
            throw new Exception('Not yet implemented');
        } else {
            $service = 'meeting.CreateMeeting';
            $xml = $this->_serializer->serializeMeeting($meeting, false);
            $response = $this->_webex->transmit($service, $xml);
            print_r($response);

            $xml = new SimpleXMLElement($response);
            $results = $xml->xpath('//meet:meetingkey'); // not meetingKey
            $id = (string) $results[0];
            return $this->getMeeting($id);
        }
    }

    /**
     * Delete meeting from WebEx meeting service.
     *
     * @param  Webex_Service_Meeting|int $meeting
     * @return void
     * @throws Exception
     */
    public function deleteMeeting($meeting)
    {
        if ($meeting instanceof Webex_Service_Meeting) {
            $meeting = $meeting->getId();
        }

        $service = 'meeting.DelMeeting';
        $xml = '<meetingKey>' . $this->_serializer->esc($meeting) . '</meetingKey>';
        $response = $this->_webex->transmit($service, $xml);
        print_r($response);
    }
}
