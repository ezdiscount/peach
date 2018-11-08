<?php

namespace app;

use app\common\AppBase;

class Index extends AppBase
{
    function get($f3)
    {
        echo \Template::instance()->render('index.html');
    }
}
