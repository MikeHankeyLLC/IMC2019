<?php 

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Registry {
    private static $library;

    private function __construct() {

    }

    public static function get($name = null) {
        self::init();

        if (empty($name)) {
            return null;
        }

        if (array_key_exists ($name, self::$library)) {
            return self::$library[$name];
        }
    }

    public static function set($name = null, $obj) {
        self::init();

        if (empty($name)) {
            return;
        }

        self::$library[$name] = $obj;
    }

    protected static function init() {
        if (empty(self::$library)) {
            self::$library = array();
        }
    }
}

