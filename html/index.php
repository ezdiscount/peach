<?php

use Prometheus\Storage\Redis;

define('HTML', __DIR__);
define('ROOT', dirname(HTML));
define('RUNTIME', ROOT . '/runtime');

require_once ROOT . '/vendor/autoload.php';

Redis::setDefaultOptions([
    'host' => 'redis',
]);

call_user_func(function ($f3) {
    $sysDirs = [
        'logs' => ROOT . '/runtime/logs/',
        'uploads' => ROOT . '/runtime/uploads/',
    ];
    foreach ($sysDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, Base::MODE, true);
        }
    }

    $f3->config(
        ROOT . '/cfg/system.ini,' .
        ROOT . '/cfg/map.ini,' .
        ROOT . '/cfg/route.ini,' .
        ROOT . '/cfg/debug.ini'
    );
    $f3->mset([
        'AUTOLOAD' => ROOT . '/src/',
        'LOCALES' => ROOT . '/dict/',
        'LOGS' => $sysDirs['logs'],
        'UI' => ROOT . '/tpl/',
        'UPLOADS' => $sysDirs['uploads'],
    ]);
    if ($f3->AJAX) {
        $f3->ONERROR = function ($f3) {
            echo json_encode(['error' => $f3->ERROR], JSON_UNESCAPED_UNICODE);
        };
    } else {
        if (!$f3->DEBUG) {
            $f3->ONERROR = function () {
                echo Template::instance()->render('error.html');
            };
        }
    }

    $f3->LOGGER = Log::instance(date('Y-m-d.\l\o\g'));

    if (PHP_SAPI != 'cli') {
        $f3->run();
    }
}, Base::instance());
