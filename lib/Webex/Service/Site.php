<?php

class Webex_Service_Site extends Webex_Service_Abstract
{
    const LST_TIME_ZONE = 'site.LstTimeZone';

    /**
     * Get information about WebEx time zones.
     *
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
        $response = $this->_webex->transmit(self::LST_TIME_ZONE, $request);

        $bodyContent = $this->_parseResponse($response);

        $timeZones = array();
        foreach ($bodyContent->children(self::SCHEMA_SITE) as $node) {
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
