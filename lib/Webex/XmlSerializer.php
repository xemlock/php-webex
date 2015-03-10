<?php

class Webex_XmlSerializer
{
    const DATE_FORMAT = 'm/d/Y H:i:s';

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

        /*
        $username = $person->getUsername();
        if (strlen($username)) {
            $xml .= '<webExID>' . $this->esc($username) . '</webExID>';
        }*/

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
        $xml .= '<startDate>' . ($startDate ? $startDate->format(self::DATE_FORMAT) : '') . '</startDate>';
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

        $xml .= '<attendeeOptions>';
        $xml .= '<emailInvitations>' . $this->b($meeting->getEmailInvitations()) . '</emailInvitations>';
        $xml .= '</attendeeOptions>';

        //$xml .= '<enableOptions>';
        //$xml .= '<voip>TRUE</voip>';
        //$xml .= '</enableOptions>';

        // joinHostBeforeUrl: NONE (default), OTHER, CALLIN, CALLBACK
        //$xml .= '<telephony>';
        //$xml .= '<telephonySupport></telephonySupport>';
        //$xml .= '</telephony>';

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
                case 'meet:meetingkey': // meetings.CreateMeeting
                case 'meet:meetingKey': // meetings.LstsummaryMeeting
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

    public function serializeQuery(Webex_Model_Query $query, array $orderMapping = null)
    {
        $xml = '';
        // serialize common parts of query
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
            if (isset($orderMapping[$key])) {
                $key = $orderMapping[$key];
            }
            $key = strtoupper($key);
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

        return $xml;
    }

    public function serializeMeetingQuery(Webex_Model_MeetingQuery $query)
    {
        $xml = $this->serializeQuery($query, array(
            'name' => 'CONFNAME',
            'startDate' => 'STARTTIME',
            'hostUsername' => 'HOSTWEBEXID',
        ));

        // <dateScope>
        $xml .= '<dateScope>';

        // ok, we have to unify dates for a single time zone (UTC)
        $startDateMin = $query->getStartDateMin();
        $startDateMax = $query->getStartDateMax();

        $endDateMin = $query->getEndDateMin();
        $endDateMax = $query->getEndDateMax();

        if ($startDateMin || $startDateMax || $endDateMin || $endDateMax) {
            $tz = Webex_Util_Time::toTimeZone('UTC');
            $xml .= '<timeZoneID>' . Webex_Util_Time::getTimeZoneID($tz) . '</timeZoneID>';
        }

        if ($startDateMin) {
            $dt = new DateTime('@' . $startDateMin->getTimestamp(), $tz);
            $xml .= '<startDateStart>' . $dt->format(self::DATE_FORMAT) . '</startDateStart>';
        }

        if ($startDateMax) {
            $dt = new DateTime('@' . $startDateMax->getTimestamp(), $tz);
            $xml .= '<startDateEnd>' . $dt->format(self::DATE_FORMAT) . '</startDateEnd>';
        }

        if ($endDateMin) {
            $dt = new DateTime('@' . $endDateMin->getTimestamp(), $tz);
            $xml .= '<endDateStart>' . $dt->format(self::DATE_FORMAT) . '</endDateStart>';
        }
        
        if ($endDateMax) {
            $dt = new DateTime('@' . $endDateMax->getTimestamp(), $tz);
            $xml .= '<endDateEnd>' . $dt->format(self::DATE_FORMAT) . '</endDateEnd>';
        }

        $xml .= '</dateScope>';
        // </dateScope>

        // if empty hostWebExID element is supplied currently logged user
        // is assumed. To overcome this place hostWebExID element only if
        // a non-empty host username is provided.
        $hostUsername = $query->getHostUsername();
        if (strlen($hostUsername)) {
            $xml .= '<hostWebExID>' . $this->esc($hostUsername) . '</hostWebExID>';
        }

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
                    $data['total'] = (int) $xmlReader->readString();
                    break;

                case 'serv:returned':
                    $data['returned'] = (int) $xmlReader->readString();
                    break;

                case 'serv:startFrom':
                    $data['offset'] = (int) $xmlReader->readString();
                    break;

                case 'meet:meeting':
                    $subtreeReader = $xmlReader->getSubtree();
                    $data['items'][] = $this->_unserializeMeetingXml($subtreeReader);
                    break;
            }
        }

        return $data;
    }

    // so far only basic querying support is provided, only search
    // by username and email
    public function serializeUserQuery(Webex_Model_UserQuery $query)
    {
        $xml = $this->serializeQuery($query, array(
            // 'UID' => ?, valid column values are undocumented in XML API Reference
        ));

        $username = $query->getUsername();
        if (strlen($username)) {
            // yep, not webExID but webExId
            $xml .= '<webExId>' . $this->esc($username) . '</webExId>';
        }

        $email = $query->getEmail();
        if (strlen($email)) {
            $xml .= '<email>' . $this->esc($email) . '</email>';
        }

        return $xml;
    }

    public function unserializeUserSummaries($response)
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
                    $data['total'] = (int) $xmlReader->readString();
                    break;

                case 'serv:returned':
                    $data['returned'] = (int) $xmlReader->readString();
                    break;

                case 'serv:startFrom':
                    $data['offset'] = (int) $xmlReader->readString();
                    break;

                case 'use:user':
                    $subtreeReader = $xmlReader->getSubtree();
                    $data['items'][] = $this->_unserializeUserXml($subtreeReader);
                    break;
            }
        }

        return $data;
    }

    public function _unserializeUserXml(Webex_XmlReaderInterface $xmlReader)
    {
        $data = array();
        do {
            if ($xmlReader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            switch ($xmlReader->name) {
                case 'use:webExId':
                    $data['username'] = $xmlReader->readString();
                    break;

                case 'use:email':
                    $data['email'] = $xmlReader->readString();
                    break;

                case 'use:registrationDate':
                    $data['regDate'] = $xmlReader->readString();
                    break;

                case 'use:active':
                    $data['active'] = $xmlReader->readString();
                    break;

                case 'use:firstName':
                    $data['firstName'] = $xmlReader->readString();
                    break;

                case 'use:lastName':
                    $data['lastName'] = $xmlReader->readString();
                    break;
            }
        
        } while ($xmlReader->read());
        return $data;
    }
}
