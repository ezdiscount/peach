<?php

namespace app\metrics;

class Helper
{
    private static $ip;
    private static $domain;

    static function getClientIp()
    {
        if (!self::$ip) {
            self::$ip = \Base::instance()->ip();
        }
        return self::$ip;
    }

    static function getDomain()
    {
        if (!self::$domain) {
            $domains = explode('.', $_SERVER['SERVER_NAME']);
            if (count($domains) > 2) {
                self::$domain = is_numeric($domains[0]) ? 'www' : $domains[0];
            } else {
                self::$domain = 'www';
            }
        }
        return self::$domain;
    }

    static function validate($key, $ttl)
    {
        $f3 = \Base::instance();
        $prev = $f3->get($key);
        if ($prev) {
            return false;
        } else {
            $f3->set($key, 1, $ttl);
            return true;
        }
    }
}
