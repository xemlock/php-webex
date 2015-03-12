<?php

class Webex_Util_Time
{
    /**
     * @param  int|string $datetime
     * @return DateTime
     * @throws Exception
     */
    public static function toDateTime($datetime)
    {
        if (is_int($datetime) || ctype_digit($datetime)) {
            $datetime = '@' . $datetime;
        }
        if (!$datetime instanceof DateTime) {
            $datetime = new DateTime($datetime);
        }
        return $datetime;
    }

    /**
     * @param  int|string $timezone
     * @return DateTimeZone
     * @throws Exception
     */
    public static function toDateTimeZone($timezone)
    {
        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }

        if (preg_match('/^(?P<h>[+-]?\d{2}):(?P<m>\d{2})$/', $timezone, $m)) {
            $timezone = ($m['h'] * 60 + ($m['h'] < 0 ? -1 : 0) * $m['m']) * 60;
        }

        if (is_int($timezone) || ctype_digit($timezone)) {
            return self::getTimeZoneByOffset($timezone);
        }

        return new DateTimeZone($timezone);
    }

    public static function getDateTimeZoneFromTimeZoneId($timeZoneId)
    {
        $webexTimezones = self::getWebexTimeZones();
        if (isset($webexTimezones[$timeZoneId]['timezone'])) {
            return new DateTimeZone($webexTimezones[$timeZoneId]['timezone']);
        }
        throw new InvalidArgumentException('Invalid or unsupported timeZoneID');
    }

    /**
     * Get timezone matching given offset
     *
     * @param  int $offset
     * @return DateTimeZone
     * @throws Exception
     */
    public static function getTimeZoneByOffset($offset, $date = null)
    {
        if ($date) {
            $time = self::toDateTime($date)->getTimestamp();
        } else {
            $time = time();
        }

        $offset = (int) $offset;

        foreach (DateTimeZone::listAbbreviations() as $zones) {
            //$timezone = new DateTimeZone($tz);
            //$now = new DateTime('now', $timezone);

            foreach ($zones as $zone) {
                $zo = $zone['offset'] + ($zone['dst'] ? -3600 : 0);
                if ($zo === $offset) {
                    return new DateTimeZone($zone['timezone_id']);
                }
            }
        }
        throw new Exception(sprintf('Unrecognized timezone offset %s', $offset));
    }

    /**
     * Get WebEx timeZoneID for the given timezone spec.
     *
     * @param  int|string|DateTimeZone $timezone
     * @return int
     */
    public static function getWebexTimeZoneID($timezone)
    {
        if ($timezone instanceof DateTime) {
            $timezone = $timezone->getTimezone();
        }
        $timezone = self::toDateTimeZone($timezone);

        // get offset without DST correction
        // "I" date format returns 1 if DST is applied, 0 otherwise
        $datetime = new DateTime('now', $timezone);
        $offset = $datetime->getOffset() + ($datetime->format('I') ? -3600 : 0);

        $name = $timezone->getName();
        $city = null;

        if (strpos($name, '/') !== false) {
            list(, $city) = explode('/', $name, 2);
            $city = str_replace('_', ' ', $city);
        }

        $gmtTimeZone = null;
        $matchedTimeZone = null;

        foreach (self::getWebexTimeZones() as $tz) {
            if (isset($tz['timezone']) && $tz['timezone'] === $name) { // exact match
                $matchedTimeZone = $tz;
                break;
            }

            // remember id for GMT timezone, as it may be used later
            if ($tz['offset'] === 0 && !strcasecmp($tz['region'], 'GMT')) {
                $gmtTimeZone = $tz;
            }

            // If timezones have equal offsets remember id of the first matched
            // timezone (do not overwrite it if matches are found later).
            // If city names are equal use currently matched timezone and
            // jump out of the loop
            if ($tz['offset'] === $offset) {
                if ($matchedTimeZone === null) {
                    $matchedTimeZone = $tz;
                }
                if (!strcasecmp($tz['city'], $city)) {
                    $matchedTimeZone = $tz;
                    break;
                }
            }
        }

        if ($matchedTimeZone === null) {
            throw new Exception('Unable to find matching timeZoneID: ' . $offset);
        }

        // if offset is +00:00 and timezone's city differs from the WebEx
        // timezone's city, use timeZoneID of the GMT time zone rather than
        // first matched city
        if ($offset === 0 && strcasecmp($matchedTimeZone['city'], $city)) {
            $matchedTimeZone = $gmtTimeZone;
        }

        return $matchedTimeZone['id'];
    }

    /**
     * Return list of time zones supported by WebEx.
     *
     * @return array
     */
    public static function getWebexTimeZones()
    {
        if (self::$_webexTimeZones === null) {
            self::$_webexTimeZones = require dirname(__FILE__) . '/timezone_data.php';
        }
        return self::$_webexTimeZones;
    }

    /**
     * @var array
     */
    protected static $_webexTimeZones;
}
