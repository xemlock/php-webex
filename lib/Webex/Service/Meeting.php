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
        $data = $this->_getMeetingData($id);
        if ($data) {
            $meeting = new Webex_Model_Meeting();
            return $this->_populateMeeting($meeting, $data);
        }
        return null;
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
            $xml = new SimpleXMLElement($response);

            $nodes = $xml->xpath('//serv:response/serv:result');
            $result = (string) $nodes[0];

            if ($result === 'SUCCESS') {
                $results = $xml->xpath('//meet:meetingkey'); // not meetingKey
                $id = (string) $results[0];
                $data = $this->_getMeetingData($id);
                if ($data) {
                    return $this->_populateMeeting($meeting, $data);
                }
            }

            $nodes = $xml->xpath('//serv:response/serv:reason');
            throw new Exception((string) $nodes[0]);
        }
    }

    /**
     * Delete meeting from WebEx meeting service.
     *
     * @param  Webex_Service_Meeting|int $meeting
     * @return Webex_Service_Meeting
     * @throws Exception
     */
    public function deleteMeeting($meeting)
    {
        if ($meeting instanceof Webex_Model_Meeting) {
            $id = $meeting->getId();
        } else {
            $id = (string) $meeting;
            $meeting = null;
        }

        $service = 'meeting.DelMeeting';
        $xml = '<meetingKey>' . $this->_serializer->esc($id) . '</meetingKey>';
        $response = $this->_webex->transmit($service, $xml);
        print_r($response);

        $xml = new SimpleXMLElement($response);

        $nodes = $xml->xpath('//serv:response/serv:result');
        $result = (string) $nodes[0];

        if ($result !== 'SUCCESS') {
            $nodes = $xml->xpath('//serv:response/serv:reason');
            throw new Exception((string) $nodes[0]);
        }

        if ($meeting) {
            $meeting->setId(null);
        }
    }

    protected function _getMeetingData($id)
    {
        $response = $this->_webex->transmit(
            'meeting.GetMeeting',
            '<meetingKey>' . $this->_serializer->esc($id) . '</meetingKey>'
        );
        return $this->_serializer->unserializeMeeting($response);
    }

    protected function _populateMeeting(Webex_Model_Meeting $meeting, array $data)
    {
        $meeting->setFromArray($data);
        $meeting->getAttendees()->clear();
        if (isset($data['attendees'])) {
            foreach ($data['attendees'] as $attendee) {
                $meeting->getAttendees()->add(new Webex_Model_Attendee($attendee));
            }
        }
        return $meeting;
    }
}
