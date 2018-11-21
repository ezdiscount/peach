<?php

namespace app\test;

class Cache
{
    function post($f3)
    {
        foreach ($_POST as $key => $value) {
            $f3->set($key, $value, 60);
            echo "set $key as $value", PHP_EOL;
        }
    }

    function get($f3)
    {
        echo $f3->CACHE, PHP_EOL;
        $name = $_GET['name'] ?? false;
        echo ($name === false) ? 'false' : $f3->get($name);
    }
}
