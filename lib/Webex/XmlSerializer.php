<?php

class Webex_XmlSerializer
{
    public function esc($value) // {{{
    {
        return strtr((string) $value, array(
            '\'' => '&apos;',
            '"'  => '&quot;',
            '<'  => '&lt;',
            '>'  => '&gt;',
            '&'  => '&amp;',
        ));
    } // }}}

    public function b($value) // {{{
    {
        return $value ? 'true' : 'false';
    } // }}}

    public function serializeAttendee(Webex_Model_Attendee $attendee, Webex_Model_Meeting $meeting) // {{{
    {
        $xml = '<attendee>';
        $xml .= '<role>' . $this->esc($attendee->getRole()) . '</role>';
        $xml .= '<sessionKey>' . $this->esc($meeting->getId()) . '</sessionKey>';
        $xml .= $this->serializePerson($attendee);
        $xml .= '</attendee>';
        return $xml;
    } // }}}

    public function serializePerson(Webex_Model_Person $person) // {{{
    {
        $xml = '<person>';
        $xml .= '<name>' . $this->esc($person->getName()) . '</name>';
        $xml .= '<email>' . $this->esc($person->getEmail()) . '</email>';
        $xml .= '</person>';
        return $xml;
    } // }}}

    public function serializeMeeting(Webex_Model_Meeting $meeting, $wrap = true) // {{{
    {
        $xml = '';

        if ($wrap) {
            $xml .= '<meeting>';
        }

        $id = $meeting->getId();
        if ($id) {
            $xml .= '<meetingkey>' . $this->esc($id) . '</meetingkey>';
        }

        $xml .= '<accessControl>';
        $xml .= '<isPublic>' . $this->b($meeting->isPublic()) . '</isPublic>';
        $xml .= '<meetingPassword>' . $this->esc($meeting->getPassword()) . '</meetingPassword>';
        $xml .= '<enforcePassword>' . $this->b($meeting->getEnforcePassword()) . '</enforcePassword>';
        $xml .= '</accessControl>';

        $xml .= '<metaData>';
        $xml .= '<confName>' . $this->esc($meeting->getName()) . '</confName>';
        $xml .= '<meetingType>' . $this->esc($meeting->getType()) . '</meetingType>';
        $xml .= '</metaData>';

        $startDate = $meeting->getStartDate();

        $xml .= '<schedule>';
        $xml .= '<startDate>' . ($startDate ? $startDate->format('m/d/Y H:i:s') : '') . '</startDate>';
        $xml .= '<duration>' . $this->esc($meeting->getDuration()) . '</duration>';
        $xml .= '<timeZoneID>' . ($startDate ? Webex_Util_Time::getTimeZoneID($startDate) : '') . '</timeZoneID>';
        $xml .= '<openTime>' . $this->esc($meeting->getOpenTime()) . '</openTime>';
        $xml .= '<joinTeleconfBeforeHost>' . $this->b($meeting->getJoinBeforeHost()) . '</joinTeleconfBeforeHost>';
        $xml .= '</schedule>';

        $xml .= '<participants>';
        $xml .= '<maxUserNumber>' . $this->esc($meeting->getMaxUsers()) . '</maxUserNumber>';
        $xml .= '<attendees>';
        foreach ($meeting->getAttendees() as $attendee) {
            $xml .= $this->serializeAttendee($attendee, $meeting);
        }
        $xml .= '</attendees>';
        $xml .= '</participants>';

        if ($wrap) {
            $xml .= '</meeting>';
        }

        return $xml;
    } // }}}

    /**
     * @param XMLReader $xml
     */
    protected function _unserializeMeetingXml($xml)
    {
        $data = array();

        $date = null;
        $timezone = null;
        $attendees = array();

        do {
            if ($xml->nodeType !== XMLReader::ELEMENT) {
                // if reading has not yet started nodeType === XMLReader::NONE
                continue;
            }

            $val = $xml->readString();
            switch ($xml->name) {
                case 'meet:meetingkey':
                    $data['id'] = $val;
                    break;

                // <meet:accessControl>
                case 'meet:isPublic':
                    $data['isPublic'] = $val === 'true' ? true : false;
                    break;

                case 'meet:meetingPassword':
                    $data['password'] = $val;
                    break;
                // </meet:accessControl>

                // this is used only in message.LstsummaryMeeting response
                case 'meet:listStatus':
                    $data['isPublic'] = ($val === 'PUBLIC');
                    break;

                // <meet:metaData>
                case 'meet:confName':
                    $data['name'] = $val;
                    break;

                case 'meet:meetingType':
                    $data['type'] = $val;
                    break;
                // </meet:metaData>

                // <meet:schedule>
                case 'meet:startDate':
                    $date = $val;
                    break;

                case 'meet:timeZone':
                    if (preg_match('#GMT(?P<offset>[+-]\d{2}:\d{2})#i', $val, $m)) {
                        $timezone = $m['offset'];
                    }
                    break;

                case 'meet:duration':
                    $data['duration'] = $val;
                    break;

                case 'meet:openTime':
                    $data['openTime'] = $val;
                    break;

                case 'meet:joinTeleconfBeforeHost':
                    $data['joinBeforeHost'] = $val === 'true' ? true : false;
                    break;

                case 'meet:hostWebExID':
                    // this element occures in meeting.LstsummaryMeeting response
                    // as a direct meet:meeting element
                    $data['hostUsername'] = $val;
                    break;
                // </meet:schedule>

                case 'meet:maxUserNumber':
                    $data['maxUsers'] = $val;
                    break;

                case 'meet:attendee':
                    $depth = $xml->depth;
                    $adata = array();
                    $subtree = $xml->getSubtree();
                    while ($subtree->read()) {
                        // echo $subtree->name, '@', $subtree->depth, ' t:', $subtree->nodeType, "\n";

                        if ($subtree->nodeType !== XMLReader::ELEMENT) {
                            continue;
                        }

                        switch ($subtree->name) {
                            case 'com:name':
                                $adata['name'] = $subtree->readString();
                                break;

                            case 'com:email':
                                $adata['email'] = $subtree->readString();
                                break;

                            case 'att:role':
                                $adata['role'] = $subtree->readString();
                                break;
                        }
                    }
                    $attendees[] = $adata;
                    break;
            }
        } while ($xml->read());

        if ($timezone) {
            $timezone = Webex_Util_Time::toTimeZone($timezone);
        }
        if ($date) {
            $data['startDate'] = new DateTime($date, $timezone);
        }

        $data['attendees'] = $attendees;
        return $data;
    }

    /**
     * @param  string $xml
     * @return array
     * @throws Exception
     */
    public function unserializeMeeting($xmlstr)
    {
        $xml = new Webex_XmlReader();
        if (!$xml->xml($xmlstr)) {
            throw new InvalidArgumentException('Invalid XML supplied');
        }

        return $this->_unserializeMeetingXml($xml);
    }

    public function serializeMeetingQuery(Webex_Model_MeetingQuery $query)
    {
        $xml = '';

        $xml .= '<listControl>';

        $offset = (int) $query->getOffset();
        if ($offset >= 0) {
            $xml .= '<startFrom>' . $offset . '</startFrom>';
        }

        $limit = (int) $query->getLimit();
        if ($limit > 0) {
            $xml .= '<maximumNum>' . $limit . '</maximumNum>';
        }

        $xml .= '<listMethod>AND</listMethod>';
        $xml .= '</listControl>';

        $xml .= '<order>';
        foreach ($query->getOrderBy() as $key => $value) {
            switch (strtolower($key)) {
                case 'hostusername':
                    $key = 'HOSTWEBEXID';
                    break;

                case 'name':
                    $key = 'CONFNAME';
                    break;

                case 'startdate':
                    $key = 'STARTTIME';
                    break;
            }
            switch (strtolower($value)) {
                case 'desc':
                    $value = 'DESC';
                    break;

                default:
                    $value = 'ASC';
                    break;
            }
            $xml .= '<orderBy>' . $this->esc($key) . '</orderBy>';
            $xml .= '<orderAD>' . $this->esc($value) . '</orderAD>';
        }
        $xml .= '</order>';

        // TODO dateScope

        // if empty hostWebExID element is supplied currently logged user
        // is assumed. To overcome this place hostWebExID element only if
        // a non-empty host username is provided.
        $hostUsername = $query->getHostUsername();
        if (strlen($hostUsername)) {
            $xml .= '<hostWebExID>' . $this->esc($hostUsername) . '</hostWebExID>';
        }

        echo "\n\n", __FUNCTION__, ': ', $xml, "\n\n";

        return $xml;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function unserializeMeetingSummaries($response)
    {
        $xmlReader = new Webex_XmlReader();
        $xmlReader->xml($response);

        $meta = array();
        $data = array(
            'total'  => 0,
            'offset' => 0,
            'items'  => array(),
        );
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            switch ($xmlReader->name) {
                case 'serv:total':
                    $data['total'] = $xmlReader->readString();
                    break;

                case 'serv:returned':
                    $data['returned'] = $xmlReader->readString();
                    break;

                case 'serv:startFrom':
                    $data['offset'] = $xmlReader->readString();
                    break;

                case 'meet:meeting':
                    $subtreeReader = $xmlReader->getSubtree();
                    $data['items'][] = $this->_unserializeMeetingXml($subtreeReader);
                    break;
            }
        }

        return $data;
    }
}
