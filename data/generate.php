<?php

define('TZID', 'tzid');
define('REGION', 'region');

// this list has mixed ST and DST
$lines = file('timezone_dropdown.js');

// Dropdown version uses wrong offsets for offsets >= +12:00 which
// results in invalid (unexpected) times of meetings
foreach ($lines as $i => $line) {
    if (($p = strpos($line, '//')) !== false) {
        $lines[$i] = substr($line, 0, $p);
    }
}
$lines = implode('', $lines);
$data = json_decode($lines, true);
$timezones = array();
foreach ($data as $tz) {
    $id = (int) $tz['value'];
    $label = $tz['label'];

    preg_match('/(?P<region>[^\(]+)\((?P<name>[^,]+),\s*GMT(?P<offset>[+-]\d{2}:\d{2})?/', $label, $match);

    $match = array_map('trim', $match);

    $region = $match['region'];
    $name = $match['name'];

    $standardName = stripos($name, 'Daylight') === false ? $name : '';
    $daylightName = stripos($name, 'Daylight') !== false ? $name : '';

    $offset = 0;
    if (isset($match['offset'])) {
        $offset = substr($match['offset'], 0, 3) * 60 + substr($match['offset'], 4, 2);
    }

    $timezones[$id] = array(
        'timeZoneID'   => $id,
        'gmtOffset'    => $offset, // difference between time zone's standard time and UTC in minutes
        'description'  => $label,
        // extra fields, undefined in nsl namespace
        'standardName' => $standardName,
        'daylightName' => $daylightName,
        REGION         => $region,
        TZID           => null, // ID in tz database
    );
}

$aliases = array(
    // add some timezones outside standard zoneinfo database
    'Beijing'      => 'Asia/Shanghai',
    'Islamabad'    => 'Asia/Karachi',

    'Bombay'       => 'Asia/Kolkata',
    'Mumbai'       => 'Asia/Kolkata',

    'Ekaterinburg' => 'Asia/Yekaterinburg',

    'Brasilia'     => 'America/Sao_Paulo', // BRT
    'Mountain'     => 'America/Denver',
    'Eniwetok'     => 'Pacific/Kwajalein',
    'Pretoria'     => 'Africa/Johannesburg',
    'Arizona'      => 'America/Phoenix', // Arizona is always on MST, no DST

    'San Francisco' => 'America/Los_Angeles',
    'Saskatchewan'  => 'America/Regina',

    'Yangon'       => 'Asia/Rangoon',

    'Nuuk'         => 'America/Godthab',

    // US Eastern Standard Time
    'Indiana'      => 'America/Indianapolis',

    // this one is unmappable, but why should we care
    // http://cldr.unicode.org/development/development-process/design-proposals/extended-windows-olson-zid-mapping#TOC-Unmappable-Windows-Time-Zone-Mid-Atlantic-Standard-Time-
    'Mid-Atlantic' => 'Atlantic/South_Georgia',

    'West Africa'  => 'Africa/Lagos',

    // official aliases
    'Tel Aviv'     => 'Asia/Jerusalem',    // Asia/Tel_Aviv
    'Newfoundland' => 'America/St_Johns',  // Canada/Newfoundland
    'Samoa'        => 'Pacific/Pago_Pago', // Pacific/Samoa
    'Wellington'   => 'Pacific/Auckland',

    'Abu Dhabi'    => 'Asia/Dubai',

    'Solomon Is'   => 'Pacific/Guadalcanal', // Guadalcanal, Solomon Islands

    // http://www.unicode.org/cldr/charts/latest/supplemental/zone_tzid.html
    'Tonga'        => 'Pacific/Tongatapu',
    'Marshall_Islands' => 'Pacific/Majuro',
);

// match build-in PHP timezones by name only
foreach ($timezones as &$timezone) {
    $matched = false;
    $rs = array_map('trim', explode(',', $timezone[REGION]));

    foreach (DateTimeZone::listIdentifiers() as $name) {
        $pos = strrpos($name, '/');
        $location = substr($name, $pos + 1);
        $location = str_ireplace('_', ' ', $location);

        foreach ($rs as $r) {
            if (!strcasecmp($r, $location)) {
                $matched = true;
                $timezone[TZID] = $name;
                break;
            }
        }
    }
    if ($matched) continue;

    foreach ($aliases as $location => $tz) {
        $location = str_replace('_', ' ', $location);
        foreach ($rs as $r) {
            if (!strcasecmp($r, $location)) {
                $tzo = new DateTimeZone($tz); // make sure alias is supported
                $timezone[TZID] = $tzo->getName();
                $matched = true;
                break;
            }
        }
    }

    if (!$matched) {
        echo 'Unmatched ', implode(',', $rs), '(', $timezone['id'], ")\n";
    }
}
unset($timezone);

ksort($timezones);

file_put_contents('Data.php', 
    sprintf(
        "<?php\n" .
        "// WebEx Time Zone Data\n" .
        "// Generated on %s\n" .
        "// PHP version %s\n" .
        "// Timezone database version %s\n" .
        "return %s;\n",
        date('c'),
        PHP_VERSION,
        timezone_version_get(),
        export($timezones)
    )
);
echo 'Saved WebEx time zone data to ' . getcwd() . '/Data.php' . "\n";

function export($var, $indent = 0) {
    $i = str_repeat('    ', $indent);
    if (is_array($var)) {
        $php = "array(\n";
        $maxl = -1;
        foreach ($var as $k => $v) {
            if (is_string($k)) {
                $maxl = max($maxl, strlen(export($k)));
            }
        }
        foreach ($var as $k => $v) {
            $fmt = is_string($k) ? "%s%-{$maxl}s => %s,\n" : "%s%d => %s,\n";
            $php .= sprintf(
                $fmt,
                str_repeat('    ', $indent + 1),
                export($k),
                export($v, $indent + 1)
            );
        }
        $php .= $i . ")";
        return $php;
    } elseif (is_int($var)) {
        return $var;
    } elseif (is_bool($var)) {
        return $var ? 'true' : 'false';
    } elseif (is_string($var) || is_float($var)) {
        return var_export($var, true);
    } elseif (is_null($var)) {
        return 'null';
    }
    throw new InvalidArgumentException('Unsupported variable type');
}
