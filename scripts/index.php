<?php

define('ROOT', dirname(__DIR__));

require_once ROOT. '/vendor/autoload.php';

call_user_func(function (Base $f3) {
    $f3->mset([
        'AUTOLOAD' => ROOT . '/src/',
        'LOGS' => ROOT . '/runtime/logs/',
    ]);
    $f3->config([
        ROOT . '/cfg/system.ini',
        ROOT . '/cfg/debug.ini',
    ]);
}, Base::instance());
