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
     * @param  string $xml
     * @return Webex_Model_Meeting
     * @throws Exception
     */
    public function unserializeMeeting($xmlstr)
    {
        $xml = new Webex_XmlReader();
        if (!$xml->xml($xmlstr)) {
            throw new InvalidArgumentException('Invalid XML supplied');
        }

        $data = array();

        $date = null;
        $timezone = null;
        $attendees = array();

        while ($xml->read()) {
            if ($xml->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            $val = $xml->expand()->nodeValue;
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
                // </meet:schedule>

                case 'meet:maxUserNumber':
                    $data['maxUsers'] = $val;
                    break;

                case 'meet:attendee':
                    $depth = $xml->depth;
                    $adata = array();
                    $subtree = $xml->getSubtree();
                    while ($next = $subtree->read()) {
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
        }

        if ($timezone) {
            $timezone = Webex_Util_Time::toTimeZone($timezone);
        }
        if ($date) {
            $data['startDate'] = new DateTime($date, $timezone);
        }

        $data['attendees'] = $attendees;
        return $data;
    }
}
