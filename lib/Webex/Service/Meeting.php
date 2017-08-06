<?php

/**
 * Meeting service acts as a repository for Meetings.
 */
class Webex_Service_Meeting extends Webex_Service_Abstract
{
    const GET_JOIN_URL_MEETING = 'meeting.GetjoinurlMeeting';
    const GET_HOST_URL_MEETING = 'meeting.GethosturlMeeting';

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
     * @param  Webex_Model_Meeting|array $meeting
     * @param  bool $refresh OPTIONAL
     * @return void
     * @throws Exception
     */
    public function saveMeeting($meeting, $refresh = true)
    {
        if (!$meeting instanceof Webex_Model_Meeting) {
            $meeting = new Webex_Model_Meeting($meeting);
        }

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
     * @param  Webex_Model_MeetingQuery|array $query OPTIONAL
     * @return Webex_Collection_ResultCollection<Webex_Model_MeetingSummary>
     */
    public function getMeetingSummaries($query = null)
    {
        if ($query !== null && !$query instanceof Webex_Model_MeetingQuery) {
            $query = new Webex_Model_MeetingQuery($query);
        }

        $response = $this->_webex->transmit(
            'meeting.LstsummaryMeeting',
            $query ? $this->_serializer->serializeMeetingQuery($query) : ''
        );

        try {
            $this->_parseResponse($response);
            $data = $this->_serializer->unserializeMeetingSummaries($response);

        } catch (Webex_Exception_ResponseException $e) {
            // check for "000015: Sorry, no record found" exception, it is perfectly ok
            // if no results are found here
            if ($e->getExceptionID() !== 15) {
                throw $e;
            }
            $data = array(
                'total'  => 0,
                'offset' => 0,
                'items'  => array(),
            );
        }

        $results = new Webex_Collection_ResultCollection('Webex_Model_MeetingSummary');
        $results->setTotal($data['total']);
        $results->setOffset($data['offset']);
        foreach ($data['items'] as $item) {
            $results->add(new Webex_Model_MeetingSummary($item));
        }

        return $results;
    }

    /**
     * Get the attendees' URL for joining a meeting.
     *
     * @param  int|string $sessionKey
     * @param  array $options (Optional)
     * @return array
     */
    public function getJoinUrlMeeting($sessionKey, array $options = null) // {{{
    {
        // sessionKey is the only required value, its absence, due to
        // missing or empty <sessionKey> or <meetingKey> element, result in
        // error

        $data = array();
        $data['sessionKey'] = (string) $sessionKey;

        if (isset($options['attendeeName'])) {
            $data['attendeeName'] = (string) $options['attendeeName'];
        }

        if (isset($options['attendeeEmail'])) {
            $data['attendeeEmail'] = (string) $options['attendeeEmail'];
        }

        if (isset($options['meetingPW'])) {
            $data['meetingPW'] = (string) $options['meetingPW'];
        }

        $response = $this->_webex->transmit(self::GET_JOIN_URL_MEETING, $this->_serializer->serialize($data));
        $bodyContent = $this->_parseResponse($response)->children(self::SCHEMA_MEETING);

        $joinMeetingURL     = (string) $bodyContent->joinMeetingURL;
        $inviteMeetingURL   = (string) $bodyContent->inviteMeetingURL;
        $registerMeetingURL = (string) $bodyContent->registerMeetingURL;

        return compact('joinMeetingURL', 'inviteMeetingURL', 'registerMeetingURL');
    } // }}}

    /**
     * Get the host's URL for starting a meeting.
     *
     * @param  int|string $sessionKey
     * @return string
     */
    public function getHostUrlMeeting($sessionKey) // {{{
    {
        $data = array();
        $data['sessionKey'] = (string) $sessionKey;

        $response = $this->_webex->transmit(self::GET_HOST_URL_MEETING, $this->_serializer->serialize($data));

        $bodyContent = $this->_parseResponse($response)->children(self::SCHEMA_MEETING);
        $hostMeetingURL = (string) $bodyContent->hostMeetingURL;

        return $hostMeetingURL;
    } // }}}
}
