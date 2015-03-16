<?php

class Webex_Service_Site extends Webex_Service_Abstract
{
    const API_SCHEMA_MEETING = 'http://www.webex.com/schemas/2002/06/service/meeting';
    const API_SCHEMA_SERVICE = 'http://www.webex.com/schemas/2002/06/service';
    const API_SCHEMA_SITE    = 'http://www.webex.com/schemas/2002/06/service/site';

    /**
     * @param  string $date
     * @param  int|array $timeZoneID
     * @return array
     */
    public function lstTimeZone($date = null, $timeZoneID = null) // {{{
    {
        $data = array();

        // a list of 0..n <timeZoneID> elements comes first
        if ($timeZoneID !== null) {
            $timeZoneID = array_values((array) $timeZoneID);
            $timeZoneID = array_filter($timeZoneID, 'is_numeric');
            $timeZoneID = array_map('intval', $timeZoneID);

            // In XML API spec up to Version 8.0 SP9, the example code for the
            // lstTimeZone call contains an error, schema diagram is valid
            // however. Time zone ID must be wrapped in a <timeZoneID> element,
            // not in a <timezoneID>.
            $data['timeZoneID'] = $timeZoneID;
        }

        // then comes a single <date> element
        if ($date !== null) {
            $data['date'] = date('m/d/Y H:i:s', strtotime($date));
        }

        $request = $this->_serializer->serialize($data);
        $response = $this->_webex->transmit('site.LstTimeZone', $request);

        $this->_parseResponse($response);
        
        $xml = simplexml_load_string($response);

        $nodes = $xml->children(self::API_SCHEMA_SERVICE);

        // $header = $nodes[0]
        // $body = $nodes[1]

        $timeZones = array();
        foreach ($nodes[1]->bodyContent->children(self::API_SCHEMA_SITE) as $node) {
            if ($node->getName() === 'timeZone') {
                $timeZones[] = array(
                    'timeZoneID'       => intval((string) $node->timeZoneID),
                    'gmtOffset'        => intval((string) $node->gmtOffset),
                    'description'      => (string) $node->description,
                    'hideTimeZoneName' => $this->toBool($node->hideTimeZoneName),
                    'fallInDST'        => $this->toBool($node->fallInDST),
                );
            }
        }
        return $timeZones;
    } // }}}

    public function toBool($value)
    {
        $value = strtoupper((string) $value);
        return ($value === 'TRUE');
    }
}
