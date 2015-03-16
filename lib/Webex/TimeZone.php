<?php

class Webex_TimeZone
{
    /**
     * @var array
     */
    protected static $_data;

    /**
     * Get data about all time zones supported by WebEx.
     *
     * @return array
     */
    public static function getAll()
    {
        if (self::$_data === null) {
            self::$_data = require dirname(__FILE__) . '/TimeZone/Data.php';
        }
        return self::$_data;
    }

    /**
     * Get all available TimeZoneIDs.
     *
     * @return array
     */
    public static function getIds()
    {
        return array_keys(self::getAll());
    }

    /**
     * Retrieve time zone data by given TimeZoneID.
     *
     * @param  int $id
     * @return array
     */
    public static function get($id)
    {
        self::getAll();
        return isset(self::$_data[$id]) ? self::$_data[$id] : null;
    }
}
