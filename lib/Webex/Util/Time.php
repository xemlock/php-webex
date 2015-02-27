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
    public static function toTimeZone($timezone)
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

    /**
     * GMT offset in seconds
     *
     * @param  int $offset
     * @return DateTimeZone
     * @throws Exception
     */
    public static function getTimeZoneByOffset($offset)
    {
        $offset = (int) $offset;
        foreach (DateTimeZone::listAbbreviations() as $zones) {
            foreach ($zones as $zone) {
                $zo = $zone['offset'] + ($zone['dst'] ? -3600 : 0);
                if ($zo === $offset) {
                    return new DateTimeZone($zone['timezone_id']);
                }
            }
        }
        throw new Exception('Unrecognized timezone offset');
    }

    /**
     * Get timeZoneID for the given date.
     *
     * @param  int|string|DateTimeZone|DateTime $timezone
     * @return int
     */
    public static function getTimeZoneID($timezone)
    {
        if ($timezone instanceof DateTime) {
            $timezone = $timezone->getTimezone();
        }
        $timezone = self::toTimeZone($timezone);

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

        foreach (self::getTimeZones() as $tz) {
            // remember GMT timezone spec, as it may be used later
            if ($tz['offset'] === 0 && !strcasecmp($tz['region'], 'GMT')) {
                $gmtTimeZone = $tz;
            }

            // when matching timezones compare offsets, if they are equal
            // remember first matched tz, if city name are also equal end comparison
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

        // if offset is GMT+00:00 and timezone's city differs from the WebEx
        // timezone's city, use id of GMT time zone
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
    public static function getTimeZones()
    {
        if (self::$_timeZones === null) {
            foreach (explode("\n", self::$_timeZoneData) as $line) {
                $line = trim($line);
                if (preg_match('#(?P<id>\d+) GMT(?P<h>[-+]\d{2}):(?P<m>\d{2}), (?P<region>.+?) \((?P<city>.+?)\)#i', $line, $m)) {
                    self::$_timeZones[$m['id']] = array(
                        'id'     => $m['id'],
                        'offset' => ($m['h'] * 60 + ($m['h'] < 0 ? -1 : 1) * $m['m']) * 60,
                        'region' => trim($m['region']),
                        'city'   => trim($m['city']),
                    );
                }
            }
        }
        return self::$_timeZones;
    }

    /**
     * @var array
     */
    protected static $_timeZones;

    /**
     * Time zone data copied from table A.1 of XML API 5.9.
     * @var array
     */
    protected static $_timeZoneData = '
        0 GMT-12:00, Dateline (Eniwetok)
        1 GMT-11:00, Samoa (Samoa)
        2 GMT-10:00, Hawaii (Honolulu)
        3 GMT-09:00, Alaska (Anchorage)
        4 GMT-08:00, Pacific (San Jose)
        5 GMT-07:00, Mountain (Arizona)
        6 GMT-07:00, Mountain (Denver)
        7 GMT-06:00, Central (Chicago)
        8 GMT-06:00, Mexico (Mexico City, Tegucigalpa)
        9 GMT-06:00, Central (Regina)
        10 GMT-05:00, S. America Pacific (Bogota)
        11 GMT-05:00, Eastern (New York)
        12 GMT-05:00, Eastern (Indiana)
        13 GMT-04:00, Atlantic (Halifax)
        14 GMT-04:00, S. America Western (Caracas)
        15 GMT-03:30, Newfoundland (Newfoundland)
        16 GMT-03:00, S. America Eastern (Brasilia)
        17 GMT-03:00, S. America Eastern (Buenos Aires)
        18 GMT-02:00, Mid-Atlantic (Mid-Atlantic)
        19 GMT-01:00, Azores (Azores)
        20 GMT+00:00, Greenwich (Casablanca)
        21 GMT+00:00, GMT (London)
        22 GMT+01:00, Europe (Amsterdam)
        23 GMT+01:00, Europe (Paris)
        24 Deprecated. Will change to timezone 23 instead.
        25 GMT+01:00, Europe (Berlin)
        26 GMT+02:00, Greece (Athens)
        27 Deprecated. Will change to timezone 26 instead.
        28 GMT+02:00, Egypt (Cairo)
        29 GMT+02:00, South Africa (Pretoria)
        30 GMT+02:00, Northern Europe (Helsinki)
        31 GMT+02:00, Israel (Tel Aviv)
        32 GMT+03:00, Saudi Arabia (Baghdad)
        33 GMT+03:00, Russian (Moscow)
        34 GMT+03:00, Nairobi (Nairobi)
        35 GMT+03:30, Iran (Tehran)
        36 GMT+04:00, Arabian (Abu Dhabi, Muscat)
        37 GMT+04:00, Baku (Baku)
        38 GMT+04:30, Afghanistan (Kabul)
        39 GMT+05:00, West Asia (Ekaterinburg)
        40 GMT+05:00, West Asia (Islamabad)
        41 GMT+05:30, India (Bombay)
        42 GMT+06:00, Columbo (Columbo)
        43 GMT+06:00, Central Asia (Almaty)
        44 GMT+07:00, Bangkok (Bangkok)
        45 GMT+08:00, China (Beijing)
        46 GMT+08:00, Australia Western (Perth)
        47 GMT+08:00, Singapore (Singapore)
        48 GMT+08:00, Taipei (Hong Kong)
        49 GMT+09:00, Tokyo (Tokyo)
        50 GMT+09:00, Korea (Seoul)
        51 GMT+09:00, Yakutsk (Yakutsk)
        52 GMT+09:30, Australia Central (Adelaide)
        53 GMT+09:30, Australia Central (Darwin)
        54 GMT+10:00, Australia Eastern (Brisbane)
        55 GMT+10:00, Australia Eastern (Sydney)
        56 GMT+10:00, West Pacific (Guam)
        57 GMT+10:00, Tasmania (Hobart)
        58 GMT+10:00, Vladivostok (Vladivostok)
        59 GMT+11:00, Central Pacific (Solomon Is)
        60 GMT+12:00, New Zealand (Wellington)
        61 GMT+12:00, Fiji (Fiji)
    ';
}
