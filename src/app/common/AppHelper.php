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
}
