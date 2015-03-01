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
     * @param  bool $refresh OPTIONAL
     * @return Webex_Model_Meeting
     * @throws Exception
     */
    public function saveMeeting(Webex_Model_Meeting $meeting, $refresh = true)
    {
        $id = $meeting->getId();

        if (empty($id)) {
            $service = 'meeting.CreateMeeting';
        } else {
            $service = 'meeting.SetMeeting';
        }

        $xml = $this->_serializer->serializeMeeting($meeting, false);
        $response = $this->_webex->transmit($service, $xml);
        $responseXml = $this->_parseResponse($response);

        // for performance reasons one can disable refreshing meeting after
        // it is persisted
        if ($refresh) {
            if (empty($id)) {
                $results = $responseXml->xpath('//meet:meetingkey'); // case sensitive, not meetingKey
                $id = (string) $results[0];
            }

            $data = $this->_getMeetingData($id);
            $this->_populateMeeting($meeting, $data);
        }

        return $meeting;
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

        $this->_parseResponse($response);

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
        $this->_parseResponse($response);
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

    public function getMeetingSummaries(Webex_Model_MeetingQuery $query = null)
    {
        $response = $this->_webex->transmit(
            'meeting.LstsummaryMeeting',
            $query ? $this->_serializer->serializeMeetingQuery($query) : ''
        );
        $this->_parseResponse($response);

        $data = $this->_serializer->unserializeMeetingSummaries($response);

        $results = new Webex_Collection_ResultCollection('Webex_Model_MeetingSummary');
        $results->setTotal($data['total']);
        $results->setOffset($data['offset']);
        foreach ($data['items'] as $item) {
            $results->add(new Webex_Model_MeetingSummary($item));
        }

        return $results;
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
