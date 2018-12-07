<?php

use service\Rabbit;

define('HTML', __DIR__);
define('ROOT', dirname(HTML));
define('RUNTIME', ROOT . '/runtime');

require_once ROOT . '/vendor/autoload.php';

Prometheus\Storage\Redis::setDefaultOptions([
    'host' => 'redis',
]);

function shutdown()
{
    Rabbit::shutdown();
}

call_user_func(function (Base $f3) {
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
            shutdown();
            echo json_encode(['error' => $f3->ERROR], JSON_UNESCAPED_UNICODE);
        };
    } else {
        if (!$f3->DEBUG) {
            $f3->ONERROR = function () {
                shutdown();
                echo Template::instance()->render('error.html');
            };
        }
    }

    $f3->LOGGER = new Log(date('Y-m-d.\l\o\g'));

    $f3->UNLOAD = 'shutdown';

    if (PHP_SAPI != 'cli') {
        $f3->run();
    }
}, Base::instance());
