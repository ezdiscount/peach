<?php

namespace app\common;

trait AppHelper
{
    function url($target = '')
    {
        $scheme = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/')));
        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT'];
        $base = rtrim(strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/'), '/');
        return $scheme . '://' . $host . $port . $base . $target;
    }

    function auth($f3)
    {
        if (!$f3->get('SESSION.AUTHENTICATION')) {
            if ($f3->VERB == 'GET') {
                setcookie('target', $f3->REALM, 0, '/');
            } else {
                setcookie('target', $this->url(), 0, '/');
            }
            return false;
        } else {
            $this->user = [
                'name' => $f3->get('SESSION.AUTHENTICATION'),
                'role' => $f3->get('SESSION.AUTHORIZATION')
            ];
            $f3->set('user', $this->user);
            return true;
        }

    }
}
