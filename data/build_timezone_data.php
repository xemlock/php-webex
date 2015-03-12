#!/usr/bin/env php
<?php

echo 'Build with ', PHP_VERSION, "\n";
// This data must be rebuild by PHP version site will use, otherwise
// (in case of obsolete data) there will be discrepancies
/**
 * @param  string $data
 * @return array
 */
function parse_webex_timezones($data) {
    $timezones = array();
    foreach (explode("\n", $data) as $line) {
        $line = trim($line);
        if (preg_match('#(?P<id>\d+)\s+(?P<name>GMT(?P<h>[-+]\d{2}):(?P<m>\d{2}), (?P<region>.+?) \((?P<city>.+?)\))#i', $line, $m)) {
            $offset = ($m['h'] * 60 + ($m['h'] < 0 ? -1 : 1) * $m['m']) * 60;
            $name = $m['name'];

            $timezones[$m['id']] = array(
                'id'     => $m['id'],
                'offset' => $offset,
                'region' => trim($m['region']),
                'city'   => trim($m['city']),
                'name'   => $name,
            );
        }
    }
    return $timezones;
}

function err($msg) {
    static $stderr;

    if ($stderr === null) {
        $stderr = fopen('php://stderr', 'w');
    }

    fwrite($stderr, $msg . "\n");
}

// timezone data from table A.1 of XML API Version 8.0 SP9 spec
$webex_timezones = parse_webex_timezones(file_get_contents(dirname(__FILE__) . '/webex_timezones.txt'));

// now we have to match webex timezones with php timezones, so that
// conversion from TimeZoneID -> DateTimeZone will be as quick as possible

$php_timezones = array(
    // some data not everywhere available
);

$aliases = array(
    // add some timezones outside standard zoneinfo database
    'Beijing'      => 'Asia/Shanghai',
    'Islamabad'    => 'Asia/Karachi',
    'Bombay'       => 'Asia/Kolkata',
    'Ekaterinburg' => 'Asia/Yekaterinburg',

    'Mid-Atlantic' => 'Atlantic/South_Georgia', // unmappable if uses DST -> to check
    'Brasilia'     => 'America/Sao_Paulo', // BRT
    'Mountain'     => 'America/Denver',
    'Eniwetok'     => 'Pacific/Kwajalein',
    'Pretoria'     => 'Africa/Johannesburg',
    'Arizona'      => 'America/Phoenix', // Arizona is always on MST, no DST

    // official aliases
    'Tel Aviv'     => 'Asia/Jerusalem',    // Asia/Tel_Aviv
    'Newfoundland' => 'America/St_Johns',  // Canada/Newfoundland
    'Samoa'        => 'Pacific/Pago_Pago', // Pacific/Samoa
    'Wellington'   => 'Pacific/Auckland',
    // 'Abu Dhabi'    => 'Asia/Dubai',
    'Solomon Is'   => 'Pacific/Guadalcanal', // Guadalcanal, Solomon Islands

    // WebEx API timezone data is outdated
    // and contains misspelling (Columbo instead of Colombo)
    // Sri-Lanka (Colombo) (+05:30 (1996-2006) instead of +06:00 (since 2006)),
    // UTC+06:00 maps to Bangladesh Standard Time
    'Columbo'      => 'Asia/Dhaka',

    // Venezuela (Caracas) ( -04:30 (until 2007) instead of -04:00 (since 2007)),
    'Caracas'      => 'America/La_Paz', // Bolivia
);
// WebEx API has invalid time spec for


// and Kwajalein (-12:00 instead of +12:00)

/*
Eastern ........... America/New_York
Central ........... America/Chicago
Mountain .......... America/Denver
Mountain no DST ... America/Phoenix
Pacific ........... America/Los_Angeles
Alaska ............ America/Anchorage
Hawaii ............ America/Adak
Hawaii no DST ..... Pacific/Honolulu
 */

foreach (DateTimeZone::listIdentifiers() as $name) {
    $timezone = new DateTimeZone($name);
    $now = new DateTime('now', $timezone);

    // Webex offset is fixed, and does not take into account changes
    // for the DST (summer time), so when comparing offsets we need
    // to compensate for DST
    $offset = $timezone->getOffset($now) - ($now->format('I') ? 1 : 0) * 3600;

    $name = strtr($timezone->getName(), array(
        'St_' => 'St. ',
        '_'   => ' ',
    ));

    $php_timezones[$timezone->getName()] = array(
        'id'     => $timezone->getName(),
        'name'   => $name,
        'offset' => $offset,
    );
}
foreach ($aliases as $alias => $timezone) {
    if (!isset($php_timezones[$timezone])) {
        die('Invalid timezone alias: ' . $alias);
    }
    $aliased_timezone = $php_timezones[$timezone];
    $aliased_timezone['name'] = $alias;
    $php_timezones[] = $aliased_timezone;
}


$v = false;
foreach ($webex_timezones as &$webex_timezone) {
    $match = null;
    $exact = false;
    if($v)echo 'RESET MATCH',"\n";

    foreach ($php_timezones as $key => $php_timezone) {
        if ($webex_timezone['offset'] == $php_timezone['offset']) {
            if ($v)echo $webex_timezone['offset'], ' ', $php_timezone['offset'], ' ', $webex_timezone['name'], ' ', $php_timezone['name'], "\n";

            if ($match === null) {
                if($v)echo 'INITIAL_MATCH[', $key, ']', "\n";
                $match = $key; // fuzzy match
            }

            // if city name matches stop searching
            $cities = array_filter(array_map('trim', explode(',', $webex_timezone['city'])));
            foreach ($cities as $city) {
                if (stripos($php_timezone['name'], $city) !== false) {
                    $match = $key;
                    $exact = true;
                    if ($v)echo 'MATCH[', $key, '][', $php_timezone['id'], "]\n";
                    break(2);
                }
            }
            if ($v)echo "NO EXACT MATCH\n";
        }
    }

    if ($match !== null) {
        $webex_timezone['timezone'] = $php_timezones[$match]['id'];
        if (!$exact) {
            err('[WARNING] Fuzzy match for timezone: ' . $webex_timezone['name'] . ' -> ' . $php_timezones[$match]['id']);
        } else {
            err('[SUCCESS] Exact match: ' . $webex_timezone['name']. ' -> '. $php_timezones[$match]['id']);
        }
    } else {
        err('[WARNING] Unmatched timezone: ' . $webex_timezone['name']);
    }
}
unset($webex_timezone);

file_put_contents('timezone_data.php', '<?php return ' . var_export($webex_timezones, true) . ';
');

copy('timezone_data.php', dirname(__FILE__) . '/../lib/Webex/Util/timezone_data.php');
