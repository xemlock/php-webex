<?php

/**
 * Meeting service acts as a repository for Meetings.
 */
class Webex_Service_Meeting extends Webex_Service_Abstract
{
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
     * @return void
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
        $this->_parseResponse($response);

        // for performance reasons one can disable refreshing meeting after
        // it is persisted
        if ($refresh) {
            if (empty($id)) {
                $responseXml = new SimpleXmlElement($response);
                $results = $responseXml->xpath('//meet:meetingkey'); // case sensitive, not meetingKey
                $id = (string) $results[0];
            }

            $data = $this->_getMeetingData($id);
            $this->_populateMeeting($meeting, $data);
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

    /**
     * @param  string $id
     * @return array
     */
    protected function _getMeetingData($id)
    {
        $response = $this->_webex->transmit(
            'meeting.GetMeeting',
            '<meetingKey>' . $this->_serializer->esc($id) . '</meetingKey>'
        );
        $this->_parseResponse($response);
        return $this->_serializer->unserializeMeeting($response);
    }

    /**
     * @param  Webex_Model_Meeting $meeting
     * @param  array $data
     * @return Webex_Model_Meeting
     */
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

    /**
     * @param  Webex_Model_MeetingQuery|null $query OPTIONAL
     * @return Webex_Collection_ResultCollection<Webex_Model_MeetingSummary>
     */
    public function getMeetingSummaries(Webex_Model_MeetingQuery $query = null)
    {
        $response = $this->_webex->transmit(
            'meeting.LstsummaryMeeting',
            $query ? $this->_serializer->serializeMeetingQuery($query) : ''
        );

        try {
            $this->_parseResponse($response);
            $data = $this->_serializer->unserializeMeetingSummaries($response);

        } catch (Exception $e) {
            // check for "000015: Sorry, no record found" exception, it is perfectly ok
            // if no results are found here
            if ($e->getCode() !== 15) {
                throw $e;
            }
            $data['total'] = $data['offset'] = 0;
        }

        $results = new Webex_Collection_ResultCollection('Webex_Model_MeetingSummary');
        $results->setTotal($data['total']);
        $results->setOffset($data['offset']);
        foreach ($data['items'] as $item) {
            $results->add(new Webex_Model_MeetingSummary($item));
        }

        return $results;
    }

    public function getjoinurlMeeting(array $options)
    {
        $xml = '<sessionKey>' . $this->_serializer->esc($options['sessionKey']) . '</sessionKey>';
        if (isset($options['attendeeName'])) {
            $xml .= '<attendeeName>' . $this->_serializer->esc($options['attendeeName']) . '</attendeeName>';
        }
        if (isset($options['attendeeEmail'])) {
            $xml .= '<attendeeEmail>' . $this->_serializer->esc($options['attendeeEmail']) . '</attendeeEmail>';
        }
        if (isset($options['meetingPW'])) {
            $xml .= '<meetingPW>' . $this->_serializer->esc($options['meetingPW']) . '</meetingPW>';
        }
        // attendeeEmail + meetingPW are required for proper URL

        // joinurl may be specific to user
        $response = $this->_webex->transmit(
            'meeting.GetjoinurlMeeting',
            $xml
        );
        $this->_parseResponse($response);

        $xmlReader = new Webex_XmlReader();
        $xmlReader->xml($response);
        $urls = array(
            'joinURL' => null,
            'inviteURL' => null,
        );
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            switch ($xmlReader->name) {
                case 'meet:joinMeetingURL':
                    $urls['joinURL'] = $xmlReader->readString();
                    break;

                case 'meet:inviteMeetingURL':
                    $urls['inviteURL'] = $xmlReader->readString();
                    break;
            }
        }
        return $urls;
    }

    public function gethosturlMeeting(array $options)
    {
        // joinurl may be specific to user
        $response = $this->_webex->transmit(
            'meeting.GethosturlMeeting',
            '<sessionKey>' . $this->_serializer->esc($options['sessionKey']) . '</sessionKey>'
        );
        $this->_parseResponse($response);

        $xmlReader = new Webex_XmlReader();
        $xmlReader->xml($response);
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            if ($xmlReader->name === 'meet:hostMeetingURL') {
                return $xmlReader->readString();
            }
        }
        // unlikely to happen
    }
}
