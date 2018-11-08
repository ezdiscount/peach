<?php

namespace app\common;

class Minify
{
    function css($f3)
    {
        $this->minify($f3, 'css');
    }

    function js($f3)
    {
        $this->minify($f3, 'js');
    }

    private function minify($f3, $type)
    {
        $f3->UI = ROOT . "/html/$type/";
        if (empty($_SERVER['QUERY_STRING'])) {
            $f3->error(404);
        } else {
            echo \Web::instance()->minify($_SERVER['QUERY_STRING']);
        }
    }
}
